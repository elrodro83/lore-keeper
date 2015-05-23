<?php 

class LKDate {

	private $dateString;
	private $timestamp;
	private $calendar;
	
	function __construct($dateString) {
		$dateParts = [];
		preg_match_all("/([0-9]{1,2})[-|\/]([a-zA-z0-9]+)[-|\/]([0-9]+) ([a-zA-Z]{2})/", $dateString, $dateParts);
		$eraQualifier = $dateParts[4][0];
		
		$this->dateString = $dateString;
		$this->calendar = $this->fetchCalendar($eraQualifier);
		$this->timestamp = $this->calendar->toTimestamp($dateParts[1][0], $dateParts[2][0], $dateParts[3][0]);
	}
	
	private function fetchCalendar($eraQualifier) {
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
	
	public function getDateString() {
		return $this->dateString;
	}
	
	public function getTimestamp() {
		return $this->timestamp;
	}
}