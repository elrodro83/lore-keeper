<?php
class Event {
	
	private $pageTitle = "";
	private $sectionTitle = "";

	private $categories = array();
	private $when = "";
	private $where = "";
	private $who = array();
	private $what = array();
	
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
		$this->extractOptions( $opts );
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
				if("who" == $name) {
					array_push($this->who, $value);
				} else if("when" == $name) {
					$this->when = new LKDate($value);
				} else if("where" == $name) {
					$this->where = $value;
				} else if("what" == $name) {
					array_push($this->what, $value);
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
	
	public static function renderEvents($parsedEvents, $showTitle = false) {
		$markUp = "{| class=\"wikitable\"\n";
			
		if($showTitle) {
			$markUp .= "! \n";
			$markUp .= "! " . wfMessage("categories") . "\n";
		}
		$markUp .= "! " . wfMessage("when") . "\n";
		$markUp .= "! " . wfMessage("where") . "\n";
		$markUp .= "! " . wfMessage("who") . "\n";
		$markUp .= "! " . wfMessage("what") . "\n";
			
		foreach($parsedEvents as $parsedEvent) {
			$markUp .= "|-\n";
			if($showTitle) {
				$markUp .= "| " . $parsedEvent->getWikiLink() . "\n";
				$markUp .= "| \n";
				foreach($parsedEvent->categories as $category) {
					$markUp .= "* [[:Category:$category|$category]]\n";
				}
			}
			$markUp .= "| " . $parsedEvent->when->getDateString() . "\n";
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
	
	public static function renderEventsTimeline($parser, $parsedEvents, $showTitle = false) {
		global $wgExtensionAssetsPath;
		$timeline = '<div id="timeline-embed"></div>
<script type="text/javascript">
	var dataObject = {
	    "timeline":
	    {
	        "headline":"' . $parser->getTitle()->getBaseTitle() . '",
	        "type":"default",
	        "date": [';
		
		foreach($parsedEvents as $parsedEvent) {
			$timeline = $timeline . '{
	                "startDate":"' . date('Y,m,d', $parsedEvent->getWhen()->getTimestamp()) . '",
	                "headline":\'' . $parsedEvent->getExternalLink($parser) . '\',
	                "text":\'' . $parsedEvent->getExternalLink($parser) . '\',
	                "tag":"' . $parsedEvent->categories[0] . '",
	                "asset": {
	                    "thumbnail":"optional-32x32px.jpg",
	                    "caption":"' . $parsedEvent->pageTitle . '"
	                }
	            },';
// 	                '"text":\'' . $parser->internalParse(Event::renderEvents([$parsedEvent]), false) . '\',' .
// 	                    "media":"' . Title::newFromText($parsedEvent->pageTitle)->getFullURL() . '",
		}		
		return $timeline . '{}
	        ]
	    }
	}		
	var timeline_config = {
		width:              \'100%\',
		height:             \'600\',
		source:             dataObject
	}
</script>
<script type="text/javascript" src="' . $wgExtensionAssetsPath . '/LoreKeeper/libs/timeline/js/storyjs-embed.js"></script>';
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
	
	public function getWikiLink() {
		if($this->sectionTitle != null) {
			return "[[$this->pageTitle#$this->sectionTitle|$this->sectionTitle]]";
		} else {
			return "[[$this->pageTitle]]";
		}
	}
	
	public function getExternalLink($parser) {
		return $parser->replaceInternalLinks($this->getWikiLink());
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
	
}