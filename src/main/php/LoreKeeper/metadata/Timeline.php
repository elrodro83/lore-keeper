<?php 

class Timeline {
	
	private $pages = array("_self");
	private $dateFrom = null;
	private $dateTo = null;
// 	private $categories = array();
	private $calendarQualifier = null;
	private $calendarJSFormatter = null;
	private $renderMode = "TABLE";
	
	private $events = array();
	private $eras = array();
	
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
		
		$currentPageId = CoreParserFunctions::pageid($parser, $parser->getTitle()->getBaseText());
		foreach($this->pages as $eventPageTitle) {
			if($eventPageTitle === "_self") {
				$eventPageTitle = $parser->getTitle()->getBaseText();
			}
			
			$pageids = PageFetchUtils::getBacklinkPagesIds($eventPageTitle);
			if($currentPageId != null) {
				array_push($pageids, $currentPageId);
			}
			
			foreach(PageFetchUtils::fetchPagesByIds($pageids) as $backlinkPage) {
				$backlinkTitle = $backlinkPage["title"];
				$backlinkContent = $backlinkPage["revisions"][0]["*"];
				
				$this->processBacklinkPage($parser, $eventPageTitle, $backlinkTitle, $backlinkContent);
			}
		}
		
		usort($this->events, "Timeline::eventTimestampCmp");
		
		foreach(PageFetchUtils::fetchPagesByIds(array($currentPageId)) as $currentPage) {
			$selfContent = $currentPage["revisions"][0]["*"];
			
			$rawEras = [];
			preg_match_all("/{{#era:([^}}]*)}}/m", $selfContent, $rawEras);
			
			foreach($rawEras[1] as $rawEra) {
				$parsedEra = new Era(array_merge([$parser],
						preg_split("/\|(?=name|from|to)/",
								str_replace(array("\r\n", "\n", "\r"), "", $rawEra))));

				array_push($this->eras, $parsedEra);
			}
		}
	}
	
	private function processBacklinkPage($parser, $eventPageTitle, $backlinkTitle, $backlinkContent) {
		foreach(ParserUtils::parseEvents($parser, $backlinkTitle, $backlinkContent) as $event) {
			if($event->hasLinksTo($eventPageTitle) && $this->checkDate($event->getWhen())) {
				$event->setCategories(ParserUtils::getCategories($backlinkContent));

				if($this->calendarQualifier != null) {
					$event->setWhen($event->getWhen()->toCalendar($this->calendarQualifier));
				}
				
				$this->events[$event->getWikiLink()] = $event;
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
				} else if("calendarJSFormatter" == $name) {
					$this->calendarJSFormatter = $value;
				} else if("renderMode" == $name) {
					$this->renderMode = $value;
				}
			}
		}
	}
	
	function eventTimestampCmp($eventA, $eventB) {
		return $eventA->getWhen()->getTimestamp() - $eventB->getWhen()->getTimestamp();
	}
	
	private function checkDate($date) {
		return ($this->dateFrom == null || $this->dateFrom->getTimestamp() <= $date->getTimestamp())
			&& ($this->dateTo == null || $this->dateTo->getTimestamp() >= $date->getTimestamp());
	}

	public function getEvents() {
		return $this->events;
	}
	
	public function getEras() {
		return $this->eras;
	}
	
	public function getRenderMode() {
		return $this->renderMode;
	}
	
	public function getCalendarJSFormatter() {
		return $this->calendarJSFormatter;
	}
}