<?php 

class InPageCalendar {

	public static function fetchCalendar($eraQualifier) {
		global $wgLoreKeeperCalendarPage;
	
		$calendarContentApi = new ApiMain( new FauxRequest(
				array(
						'action' => 'query',
						'prop' => 'revisions',
						'format' => 'xml',
						'rvprop' => 'content',
						'rvslots' => '*',
						'titles' => $wgLoreKeeperCalendarPage),
				true
		) );
		$calendarContentApi->execute();
		$calendarContentData = & $calendarContentApi->getResult()->getResultData();

		foreach($calendarContentData["query"]["pages"] as $page) break;
		$calendarPageMarkUp = $page["revisions"][0]["slots"]["main"]["content"];

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
