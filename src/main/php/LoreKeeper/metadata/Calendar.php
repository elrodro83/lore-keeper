<?php 

class Calendar{

	private $months = array();
	private $qualifier;
	private $epochOffsetDays;
	
	function __construct($months , $qualifier , $offset) {
		foreach(explode(";", $months) as $month) {
			array_push($this->months, explode(",", $month));
		}
		
		$this->qualifier = $qualifier;
		$this->epochOffsetDays = $offset;
	}
	
	public function render() {
		$markUp = "{| class=\"wikitable\"\n";
			
		$markUp .= "! Month Name\n";
		$markUp .= "! Days\n";
		
		$markUp .= "|+ " . htmlspecialchars($this->qualifier) . "\n";
		$markUp .= "|+ Epoch Offset: " . htmlspecialchars($this->epochOffsetDays) . " days\n";
		
		foreach($this->months as $month) {
			$markUp .= "|-\n";
			$markUp .= "| " . htmlspecialchars($month[0]) . "\n";
			$markUp .= "| " . htmlspecialchars($month[2]) . "\n";
		}
			
		$markUp .= "|}";
		return $markUp;
	}

	public function toTimestamp($day, $monthName, $year) {
		$daysFromEraStart = ($year - 1) * $this->resolveYearDays() + $this->resolveUpToMonthDays($monthName) + ($day - 1);
		return ($daysFromEraStart + $this->epochOffsetDays) * 24*60*60;
	}
	
	public function fromTimestamp($timestamp) {
		$daysFromEraStart = $timestamp / (24*60*60) - $this->epochOffsetDays;
		
		$year = floor(1 + ($daysFromEraStart / $this->resolveYearDays()));
		$daysFromYearStart = $daysFromEraStart % $this->resolveYearDays();
		
		$days = 0;
		foreach ($this->months as $month) {
			$days += $month[2];
			if($days >= $daysFromYearStart) {
				$monthName = $month[0];
				$day = 1 + $days - $daysFromYearStart;
				return "$day-$monthName-$year $this->qualifier";
			}
		}
		
	}
	
	private function resolveUpToMonthDays($monthName) {
		$days = 0;
		foreach ($this->months as $month) {
			if($month[0] === $monthName || $month[1] === $monthName) {
				return $days;
			}
			$days += $month[2];
		}
		throw new Exception("No month found for name $monthName");
	}
	
	private function resolveYearDays() {
		$days = 0;
		foreach ($this->months as $month) {
			$days += $month[2];
		}
		return $days;
	}
	
	public function getQualifier() {
		return $this->qualifier;
	}
}
