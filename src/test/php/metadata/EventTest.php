<?php
class LoreKeeperEventTest extends PHPUnit_Framework_TestCase {
	
	public function testEventRenderOneCharacter() {
		$parsedEvent = new Event([null,
				"who=Sauron",
				"when=1600",
				"where=Mount Doom",
				"what=The One Ring",
		]);
		
		$this->assertEquals("1600", $parsedEvent->getWhen());
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
				"when=3434",
				"where=Barad-dûr",
				"what=The One Ring",
		]);
		
		$this->assertEquals("3434", $parsedEvent->getWhen());
		$this->assertEquals("Sauron", $parsedEvent->getWho()[0]);
		$this->assertEquals("Last Alliance", $parsedEvent->getWho()[1]);
		$this->assertEquals("Gil-galad", $parsedEvent->getWho()[2]);
		$this->assertEquals("Elendil", $parsedEvent->getWho()[3]);
		$this->assertEquals("Isildur", $parsedEvent->getWho()[4]);
		$this->assertEquals("Barad-dûr", $parsedEvent->getWhere());
		$this->assertEquals("The One Ring", $parsedEvent->getWhat());
	}

	public function testEventRenderMissingInfo() {
		try {
			$parsedEvent = new Event([null,
				"who=Sauron",
				"who=Last Alliance",
				"who=Gil-galad",
				"who=Elendil",
				"who=Isildur",
				"when=3434",
// 				"where=Barad-dûr",
				"what=The One Ring",
		]);
			$this->fail();
		} catch(Exception $e) {
			$this->assertEquals('Missing mandatory \'where\' data: ["who=Sauron","who=Last Alliance","who=Gil-galad","who=Elendil","who=Isildur","when=3434","what=The One Ring"]', $e->getMessage());
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
				"when=3434",
				"where=Barad-dûr",
				"what=The One Ring",
		]);
			$this->fail();
		} catch(Exception $e) {
			$this->assertEquals('Missing mandatory \'who\' data: ["when=3434","where=Barad-d\u00fbr","what=The One Ring"]', $e->getMessage());
		}
	}
}