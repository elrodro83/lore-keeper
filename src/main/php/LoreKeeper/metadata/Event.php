<?php
class Event {
	
	private $pageTitle = "";
	private $sectionTitle = "";

	private $categories = array();
	private $outgoingLinks = array();
	
	private $when = "";
	private $where = "";
	private $who = array();
	private $what = array();
	
	private $body = "";
	
	function __construct($args) {
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
		$this->extractOptions( $args[0], $opts );
	}
	
	/**
	 * Converts an array of values in form [0] => "name=value" into a real
	 * associative array in form [name] => value
	 *
	 * @param array string $options
	 * @return array $results
	 */
	public function extractOptions( $parser, array $options ) {
		foreach ( $options as $option ) {
			$pair = explode( '=', $option, 2 );
			if ( count( $pair ) == 2 ) {
				$name = trim( $pair[0] );
				$value = trim( $pair[1] );
				if("who" == $name) {
					array_push($this->who, $value);
				} else if("when" == $name) {
					$this->when = new LKDate($value);
				} else if("where" == $name) {
					$this->where = $value;
				} else if("what" == $name) {
					array_push($this->what, $value);
				}
				
				$outgoingLinks = array();
				preg_match_all("/\[\[([^\]\|]*)(?:|[^\]]*)\]\]/", $value, $outgoingLinks);
				
				foreach ($outgoingLinks[1] as $outgoingLink) {
					if($parser->getTitle()->getBaseText() != $outgoingLink) {
						array_push($this->outgoingLinks, $outgoingLink);
					}
				}
			}
		}

		//Now you've got an array that looks like this:
		//	[foo] => bar
		//	[apple] => orange
	
		if(empty($this->when)) {
			throw new Exception("Missing mandatory 'when' data: " . json_encode($options));
		} else if(empty($this->where)) {
			throw new Exception("Missing mandatory 'where' data: " . json_encode($options));
		}
	}
	
	public function hasLinksTo($pageTile) {
		foreach($this->who as $character) {
			if(preg_match("/\[\[$pageTile(?:\|(.*))?\]\]/", $character) > 0) {
				return true;
			}
		}
		foreach($this->what as $item) {
			if(preg_match("/\[\[$pageTile(?:\|(.*))?\]\]/", $item) > 0) {
				return true;
			}
		}

		return preg_match("/\[\[$pageTile(?:\|(.*))?\]\]/", $this->where) > 0;
	}
	
	public static function renderEvents($parsedEvents, $showTitle = false, $showWhen = true) {
		$markUp = "{| class=\"wikitable\"\n";
			
		if($showTitle) {
			$markUp .= "! \n";
			$markUp .= "! " . wfMessage("categories") . "\n";
		}
		if($showWhen) {
			$markUp .= "! " . wfMessage("when") . "\n";
		}
		$markUp .= "! " . wfMessage("where") . "\n";
		$markUp .= "! " . wfMessage("who") . "\n";
		$markUp .= "! " . wfMessage("what") . "\n";
			
		foreach($parsedEvents as $parsedEvent) {
			$markUp .= "|-\n";
			if($showTitle) {
				$markUp .= "| " . $parsedEvent->getWikiLink() . "\n";
				$markUp .= "| \n";
				foreach(PageFetchUtils::filterKnowledgeCategories($parsedEvent->categories) as $category) {
					$markUp .= "* [[:Category:$category|$category]]\n";
				}
			}
			if($showWhen) {
				$markUp .= "| " . $parsedEvent->when->getDateString() . "\n";
			}
			$markUp .= "| $parsedEvent->where\n";
			$markUp .= "|\n";
			foreach($parsedEvent->who as $who) {
				$markUp .= "* $who\n";
			}
			$markUp .= "|\n";
			foreach($parsedEvent->what as $what) {
				$markUp .= "* $what\n";
			}
			$markUp .= "\n";
		}
			
		$markUp .= "|}";

		return $markUp;
	}
	
	public static function renderEventsTimeline($parser, $parsedEvents, $eras, $calendarJSFormatter, $showTitle = false) {
		global $wgExtensionAssetsPath;
		global $wgLanguageCode;
		
		$timelineDataObject = array();
		
		$timelineDataObject["headline"] = $parser->getTitle()->getBaseText();
		$timelineDataObject["type"] = "default";
		$timelineDataObject["date"] = array();
		$timelineDataObject["era"] = array();
		
		foreach($parsedEvents as $parsedEvent) {
			$timelineEvent = array();
			
			$externalLink = $parsedEvent->getExternalLink($parser);
			
			$timelineEvent["startDate"] = date('Y,m,d', $parsedEvent->getWhen()->getTimestamp());
			$timelineEvent["headline"] = $externalLink;

			$eventProcessed =
					$parser->parse(
						Event::renderEvents(array($parsedEvent), false, false) . "\r\n" . $parsedEvent->getBody(),
						$parser->getTitle(), ParserOptions::newFromAnon(), false, false, 0 )->getText();
			
			$timelineEvent["text"] = $eventProcessed;
			$timelineEvent["tag"] = PageFetchUtils::filterKnowledgeCategories($parsedEvent->categories);
			$timelineEvent["asset"] = array(
					"caption" => $parsedEvent->pageTitle
			);
			
			$firstImageLink = $parsedEvent->getFirstImageFromOutgoingLinks($parser, $parsedEvent->getWhen()->getTimestamp());
			
			if($firstImageLink != "") {
				$timelineEvent["asset"]["thumbnail"] = ParserUtils::resolveImageLink($parser, $firstImageLink);
			}
			
			array_push($timelineDataObject["date"], $timelineEvent);
		}
		foreach($eras as $era) {
			$timelineEra = array();
			
			$timelineEra["startDate"] = date('Y,m,d', $era->getFrom()->getTimestamp());
			$timelineEra["endDate"] = date('Y,m,d', $era->getTo()->getTimestamp());
			$timelineEra["headline"] = $era->getName();
				
			array_push($timelineDataObject["era"], $timelineEra);
		}
		
		$dataObject = json_encode($timelineDataObject);
		
		$timelineHtml = 
'<div id="timeline-embed"></div>' .
'<script type="text/javascript">' .
'	var timeline_config = {' .
'		"lang":               "' . $wgLanguageCode . '",' .
'		"calendar":           "' . $calendarJSFormatter . '",' .
'		"width":              "100%",' .
'		"height":             "600",' .
'		"source":             {"timeline":' . $dataObject . '}' .
'	}' .
'</script>' .
'<script type="text/javascript" src="' . $wgExtensionAssetsPath . '/LoreKeeper/libs/timeline/js/storyjs-embed.js"></script>';
		
		return $timelineHtml;
	}
	
	private function getFirstImageFromOutgoingLinks($parser, $eventWhen) {
		foreach($this->getOutgoingLinks() as $outgoingLink) {
			$outgoingPageId = CoreParserFunctions::pageid($parser, $outgoingLink);
			$fetchedPage = PageFetchUtils::fetchPagesByIds(array($outgoingPageId));
			if($fetchedPage == null) {
				return "";
			}
			
			$files = array();
			foreach(PageFetchUtils::fetchPagesByIds(array($outgoingPageId)) as $linkedPage) {
				if(is_array($linkedPage)) {
					$linkedPageContent = $linkedPage["revisions"][0]["slots"]["main"]["content"];

					$outgoingEras = ParserUtils::getEras($linkedPageContent);
					if(empty($outgoingEras)) {
						$files = array_merge($files, ParserUtils::getFiles($linkedPageContent));
					} else {
						// Get thumbnail matching the event date to the eras
						foreach($outgoingEras as $era) {
							if($era->containsDate($eventWhen)) {
								array_push($files, $era->getThumb());
							}
						}
					}
				}
			}
			
			if(count($files) > 0) {
				return $files[0];
			}
		}
		return "";
	}
	
	public function setTitle($pageTitle, $sectionTitle) {
		$this->pageTitle = $pageTitle;
		$this->sectionTitle = $sectionTitle;
	}
	
	public function getTitle() {
		if($this->sectionTitle != null) {
			return $this->sectionTitle;
		} else {
			return $this->pageTitle;
		}
	}
	
	public function setCategories($categories) {
		$this->categories = $categories;
	}
	
	public function getOutgoingLinks() {
		return $this->outgoingLinks;
	}
	
	public function getWikiLink() {
		if($this->sectionTitle != null) {
			return "[[$this->pageTitle#$this->sectionTitle|$this->sectionTitle]]";
		} else {
			return "[[$this->pageTitle]]";
		}
	}
	
	/**
	 * This returns the placeholder for an external link
	 * @param unknown $parser
	 */
	public function getExternalLink($parser) {
		return $parser->parse($this->getWikiLink(), $parser->getTitle(), ParserOptions::newFromAnon(), false, false, 0 )->getText();
	}
	
	public function setWhen($when) {
		$this->when = $when;
	}
	
	public function getWhen() {
		return $this->when;
	}
	
	public function getWhere() {
		return $this->where;
	}
	
	public function getWho() {
		return $this->who;
	}
	
	public function getWhat() {
		return $this->what;
	}
	
	public function getBody() {
		return $this->body;
	}
	
	public function setBody($body) {
		$this->body = $body;
	}
}
