<?php
class LoreKeeperEventTest extends PHPUnit_Framework_TestCase {
	
	public function setUp() {
		LKDate::setCalendarFactory("FixedLOTRCalendar");
	}
	
	public function testEventRenderOneCharacter() {
		$parsedEvent = new Event([null,
				"who=Sauron",
				"when=1-ethuil-1600 2A",
				"where=Mount Doom",
				"what=The One Ring",
		]);
		
		$this->assertEquals("1-ethuil-1600 2A", $parsedEvent->getWhen()->getDateString());
		$this->assertEquals("Sauron", $parsedEvent->getWho()[0]);
		$this->assertEquals("Mount Doom", $parsedEvent->getWhere());
		$this->assertEquals("The One Ring", $parsedEvent->getWhat());
	}
	
	public function testEventRenderManyCharacters() {
		$parsedEvent = new Event([null,
				"who=Sauron",
				"who=Last Alliance",
				"who=Gil-galad",
				"who=Elendil",
				"who=Isildur",
				"when=1-ethuil-3434 2A",
				"where=Barad-dûr",
				"what=The One Ring",
		]);
		
		$this->assertEquals("1-ethuil-3434 2A", $parsedEvent->getWhen()->getDateString());
		$this->assertEquals("Sauron", $parsedEvent->getWho()[0]);
		$this->assertEquals("Last Alliance", $parsedEvent->getWho()[1]);
		$this->assertEquals("Gil-galad", $parsedEvent->getWho()[2]);
		$this->assertEquals("Elendil", $parsedEvent->getWho()[3]);
		$this->assertEquals("Isildur", $parsedEvent->getWho()[4]);
		$this->assertEquals("Barad-dûr", $parsedEvent->getWhere());
		$this->assertEquals("The One Ring", $parsedEvent->getWhat());
	}

	public function testBacklinks() {
		$parsedEvent = new Event([null,
				"who=[[Gandalf|Gandalf the grey]]",
				"who=The [[Balrog]]",
				"who=[[The Fellowship of the Ring]]",
				"when=1-ethuil-3019 3A",
				"where=[[Khazad-dûm]]"
		]);
		
		$this->assertTrue($parsedEvent->hasLinksTo("Gandalf"));
		$this->assertTrue($parsedEvent->hasLinksTo("Balrog"));
		$this->assertTrue($parsedEvent->hasLinksTo("The Fellowship of the Ring"));
		$this->assertTrue($parsedEvent->hasLinksTo("Khazad-dûm"));
		$this->assertFalse($parsedEvent->hasLinksTo("Sauron"));
	}
	
	public function testEventRenderMissingInfo() {
		try {
			$parsedEvent = new Event([null,
				"who=Sauron",
				"who=Last Alliance",
				"who=Gil-galad",
				"who=Elendil",
				"who=Isildur",
				"when=1-ethuil-3434 2A",
// 				"where=Barad-dûr",
				"what=The One Ring",
		]);
			$this->fail();
		} catch(Exception $e) {
			$this->assertEquals('Missing mandatory \'where\' data: ["who=Sauron","who=Last Alliance","who=Gil-galad","who=Elendil","who=Isildur","when=1-ethuil-3434 2A","what=The One Ring"]', $e->getMessage());
		}
	}

	public function testEventRenderEmptyWho() {
		try {
			$parsedEvent = new Event([null,
// 				"who=Sauron",
// 				"who=Last Alliance",
// 				"who=Gil-galad",
// 				"who=Elendil",
// 				"who=Isildur",
				"when=1-ethuil-3434 2A",
				"where=Barad-dûr",
				"what=The One Ring",
		]);
			$this->fail();
		} catch(Exception $e) {
			$this->assertEquals('Missing mandatory \'who\' data: ["when=1-ethuil-3434 2A","where=Barad-d\u00fbr","what=The One Ring"]', $e->getMessage());
		}
	}
}