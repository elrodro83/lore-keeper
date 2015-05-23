<?php 

class Timeline {
	
	private $events = array();
	
	function __construct($parser) {
		foreach($this->fetchBacklinkPages($parser) as $backlinkPage) {
			$backlinkContent = $backlinkPage["revisions"][0]["*"];

			$rawEvents = [];
			$subtitileEvents = [];
			preg_match_all("/({{#event:[^}}]*}})/m", $backlinkContent, $rawEvents);
			preg_match_all("/(?:==+ (.+) ==+).*({{#event:[^}}]*}})/ms", $backlinkContent, $subtitileEvents);
			
			foreach($rawEvents[0] as $event) {
				$title = $this->resolveEventTitle($backlinkPage["title"], $event, $subtitileEvents[2], $subtitileEvents[1]);
				
				$eventBody = [];
				preg_match_all("/{{#event:([^}}]*)}}/", $event, $eventBody);
					
				$parsedEvent = new Event(array_merge([$parser], explode("|", $eventBody[1][0])));
				$parsedEvent->setTitle($title . " (" . $this->resolveEventCategories($backlinkContent) . ")");
				array_push($this->events, $parsedEvent);
			}
			
// 			http://www.mediawiki.org/wiki/Manual:Tag_extensions#Regenerating_the_page_when_another_page_is_edited
			$title = Title::newFromText( $backlinkPage["title"] );
			$rev = Revision::newFromTitle( $title );
			$id = $rev ? $rev->getPage() : 0;
			// Register dependency in templatelinks
			$parser->getOutput()->addTemplate( $title, $id, $rev ? $rev->getId() : 0 );			
		}
		
		usort($this->events, "Timeline::eventTimestampCmp");
	}
	
	function eventTimestampCmp($a, $b) {
		return $a->getWhen()->getTimestamp() - $b->getWhen()->getTimestamp();
	}
	
	private function fetchBacklinkPages($parser) {
		$backlinksApi = new ApiMain( new FauxRequest(
				array(
						'action' => 'query',
						'list' => 'backlinks',
						'format' => 'xml',
						'bltitle' => $parser->getTitle()->getBaseTitle(),
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
	
	private function resolveEventTitle($backlinkPageTitle, $rawEvent, $rawEventsWithSubtitles, $subtitles) {
		if(in_array($rawEvent, $rawEventsWithSubtitles)) {
			$subtitle = $subtitles[array_search($rawEvent, $rawEventsWithSubtitles)];
			$strippedSubtitle = str_replace("]]", "", str_replace("[[", "", $subtitle));
			return "[[" . $backlinkPageTitle . "#" . $strippedSubtitle . "|" .$strippedSubtitle. "]]";
		} else {
			return "[[" . $backlinkPageTitle . "]]";
		}
	}
	
	private function resolveEventCategories($backlinkContent) {
		$categories = [];
		preg_match_all("/\[\[[cC]ategory:([^\]\]]*)\]\]/m", $backlinkContent, $categories);
		
		$cat = "";
		foreach($categories[1] as $category) {
			$cat .= "[[:Category:" . $category . "|" . $category . "]], ";
		}
		
		return substr($cat, 0, -2);
	}

	public function getEvents() {
		return $this->events;
	}
	

}