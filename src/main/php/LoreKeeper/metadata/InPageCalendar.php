<?php 

class InPageCalendar {

	public static function fetchCalendar($eraQualifier) {
		global $wgLoreKeeperCalendarPage;
	
		$title = Title::newFromText( $wgLoreKeeperCalendarPage );
		$rev = Revision::newFromTitle( $title );
		$id = $rev ? $rev->getPage() : 0;
	
		$calendarContentApi = new ApiMain( new FauxRequest(
				array(
						'action' => 'query',
						'prop' => 'revisions',
						'format' => 'xml',
						'rvprop' => 'content',
						'pageids' => $id),
				true
		) );
		$calendarContentApi->execute();
		$calendarContentData = & $calendarContentApi->getResultData();
	
		$calendarPageMarkUp = $calendarContentData["query"]["pages"][$id]["revisions"][0]["*"];
	
		$rawCalendars = [];
		preg_match_all("/{{#calendar:([^}}]*)}}/", $calendarPageMarkUp, $rawCalendars);
		foreach($rawCalendars[1] as $rawCalendar) {
			$rawCalendarElems = explode("|", $rawCalendar);
			if($eraQualifier === $rawCalendarElems[1]) {
				return new Calendar($rawCalendarElems[0], $rawCalendarElems[1], $rawCalendarElems[2]);
			}
		}
		throw new Exception("No calendar found for qualifier $eraQualifier");
	}
	
}