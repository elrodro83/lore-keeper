<?php 

class LKDate {

	private static $calendarFactory = "InPageCalendar";
	
	private $dateString;
	private $timestamp;
	private $calendar;
	
	/**
	 * Parses a string with format dd-MMMM-YYYY AGE into a timestamp, based on the defined calendar(s).
	 * @param unknown $dateString
	 */
	function __construct($dateString) {
		$dateParts = [];
		preg_match_all("/([0-9]{1,2})[-|\/]([^-|\/]+)[-|\/]([0-9]+) (\w{2,})/", $dateString, $dateParts);
		$eraQualifier = $dateParts[4][0];
		
		$this->dateString = $dateString;
		$cf = LKDate::$calendarFactory;
		$this->calendar = $cf::fetchCalendar($eraQualifier);
		$this->timestamp = $this->calendar->toTimestamp($dateParts[1][0], $dateParts[2][0], $dateParts[3][0]);
	}
	
	public static function setCalendarFactory($cf) {
		LKDate::$calendarFactory = $cf;
	}
	
	public function getDateString() {
		return $this->dateString;
	}
	
	public function getTimestamp() {
		return $this->timestamp;
	}
	
	public function toCalendar($calendarQualifier) {
		$cf = LKDate::$calendarFactory;
		$toCalendar = $cf::fetchCalendar($calendarQualifier);
		return new LKDate($toCalendar->fromTimestamp($this->timestamp));
	}
}