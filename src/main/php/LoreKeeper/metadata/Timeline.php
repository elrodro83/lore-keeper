<?php 

class Timeline {
	
	private $pages = array("_self");
	private $dateFrom = null;
	private $dateTo = null;
// 	private $categories = array();
	private $calendarQualifier = null;
	private $renderMode = "TABLE";
	
	private $events = array();
	
	function __construct($parser, $args) {
		//Suppose the user invoked the parser function like so:
		//{{#myparserfunction:foo=bar|apple=orange}}
	
		$opts = array();
		// Argument 0 is $parser, so begin iterating at 1
		for ( $i = 1; $i < count($args); $i++ ) {
			array_push($opts, $args[ $i ]);
		}
		//The $opts array now looks like this:
		//	[0] => 'foo=bar'
		//	[1] => 'apple=orange'
	
		//Now we need to transform $opts into a more useful form...
		$this->extractOptions( $opts );
		
		foreach($this->pages as $eventPageTitle) {
			if($eventPageTitle === "_self") {
				$eventPageTitle = $parser->getTitle()->getBaseText();
			}
			
			foreach($this->fetchBacklinkPages($parser, $eventPageTitle) as $backlinkPage) {
				$backlinkTitle = $backlinkPage["title"];
				$backlinkContent = $backlinkPage["revisions"][0]["*"];
				
				$this->processBacklinkPage($parser, $eventPageTitle, $backlinkTitle, $backlinkContent);
			}
		}
		
		usort($this->events, "Timeline::eventTimestampCmp");
	}
	
	private function processBacklinkPage($parser, $eventPageTitle, $backlinkTitle, $backlinkContent) {
		$rawEvents = [];
		$subtitileEvents = [];
		preg_match_all("/({{#event:[^}}]*}})/m", $backlinkContent, $rawEvents);
		preg_match_all("/==+ ([^==+]+) ==+[^==+]*({{#event:[^}}]*}})/", $backlinkContent, $subtitileEvents);
		
		foreach($rawEvents[0] as $rawEvent) {
			$eventBody = [];
			preg_match_all("/{{#event:([^}}]*)}}/", $rawEvent, $eventBody);
		
			$parsedEvent = new Event(array_merge([$parser],
					preg_split("/\|(?=when|what|where|who)/",
							str_replace(array("\r\n", "\n", "\r"), "", $eventBody[1][0]))));
			if($parsedEvent->hasLinksTo($eventPageTitle)
					&& $this->checkDate($parsedEvent->getWhen())) {
						$this->resolveEventTitle($parsedEvent, $backlinkTitle, $rawEvent, $subtitileEvents[2], $subtitileEvents[1]);
						$parsedEvent->setCategories($this->resolveEventCategories($backlinkContent));
		
						if($this->calendarQualifier != null) {
							$parsedEvent->setWhen($parsedEvent->getWhen()->toCalendar($this->calendarQualifier));
						}
						// 						array_push($this->events, $parsedEvent);
						$this->events[$parsedEvent->getWikiLink()] = $parsedEvent;
					}
		}
		
		// 			http://www.mediawiki.org/wiki/Manual:Tag_extensions#Regenerating_the_page_when_another_page_is_edited
		$title = Title::newFromText( $backlinkTitle );
		$rev = Revision::newFromTitle( $title );
		$id = $rev ? $rev->getPage() : 0;
		// Register dependency in templatelinks
		$parser->getOutput()->addTemplate( $title, $id, $rev ? $rev->getId() : 0 );
	}
	
	/**
	 * Converts an array of values in form [0] => "name=value" into a real
	 * associative array in form [name] => value
	 *
	 * @param array string $options
	 * @return array $results
	 */
	public function extractOptions( array $options ) {
		foreach ( $options as $option ) {
			$pair = explode( '=', $option, 2 );
			if ( count( $pair ) == 2 ) {
				$name = trim( $pair[0] );
				$value = trim( $pair[1] );
				if("pages" == $name) {
					$this->pages = explode(';', $value);
				} else if ("dateFrom" == $name) {
					$this->dateFrom = new LKDate($value);
				} else if("dateTo" == $name) {
					$this->dateTo = new LKDate($value);
// 				} else if("categories" == $name) {
// 					$this->categories = explode(';', $value);
				} else if("calendarQualifier" == $name) {
					$this->calendarQualifier = $value;
				} else if("renderMode" == $name) {
					$this->renderMode = $value;
				}
			}
		}
	}
	
	function eventTimestampCmp($eventA, $eventB) {
		return $eventA->getWhen()->getTimestamp() - $eventB->getWhen()->getTimestamp();
	}
	
	private function fetchBacklinkPages($parser, $pageTitle) {
		$backlinksApi = new ApiMain( new FauxRequest(
				array(
						'action' => 'query',
						'list' => 'backlinks',
						'format' => 'xml',
						'bltitle' => $pageTitle,
						'blfilterredir' => 'all',
						'bllimit' => 500),
				true
		) );
		$backlinksApi->execute();
		$backlinksData = & $backlinksApi->getResultData();
		
		$pageids = [];
		foreach($backlinksData["query"]["backlinks"] as $backlink) {
			array_push($pageids, $backlink["pageid"]);
		}
		$currentPageId = CoreParserFunctions::pageid($parser, $pageTitle);
		if($currentPageId != null) {
			array_push($pageids, $currentPageId);
		}
		
		$blContentApi = new ApiMain( new FauxRequest(
				array(
						'action' => 'query',
						'prop' => 'revisions',
						'format' => 'xml',
						'rvprop' => 'content',
						'pageids' => implode("|", $pageids)),
				true
		) );
		$blContentApi->execute();
		$blContentData = & $blContentApi->getResultData();
		return $blContentData["query"]["pages"];
	}
	
	private function resolveEventTitle($parsedEvent, $backlinkPageTitle, $rawEvent, $rawEventsWithSubtitles, $subtitles) {
		if(in_array($rawEvent, $rawEventsWithSubtitles)) {
			$subtitle = $subtitles[array_search($rawEvent, $rawEventsWithSubtitles)];
			$strippedSubtitle = str_replace("]]", "", str_replace("[[", "", $subtitle));
			$parsedEvent->setTitle($backlinkPageTitle, $strippedSubtitle);
		} else {
			$parsedEvent->setTitle($backlinkPageTitle, null);
		}
	}
	
	private function resolveEventCategories($backlinkContent) {
		$categories = [];
		preg_match_all("/\[\[[cC]ategory:([^\]\]]*)\]\]/m", $backlinkContent, $categories);
		
		$cats = array();
		foreach($categories[1] as $category) {
			array_push($cats, $category);
		}
		
		return $cats;
	}
	
	private function checkDate($date) {
		return ($this->dateFrom == null || $this->dateFrom->getTimestamp() <= $date->getTimestamp())
			&& ($this->dateTo == null || $this->dateTo->getTimestamp() >= $date->getTimestamp());
	}

	public function getEvents() {
		return $this->events;
	}
	
	public function getRenderMode() {
		return $this->renderMode;
	}
}