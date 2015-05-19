<?php
class LoreKeeperEventTest extends PHPUnit_Framework_TestCase {
	
	public function testEventRenderOneCharacter() {
		$parsedEvent = Event::parseEvent([null,
				"who=Sauron",
				"when=1600",
				"where=Mount Doom",
				"what=The One Ring",
		]);
		
		$this->assertEquals("1600", $parsedEvent["when"]);
		$this->assertEquals("Sauron", $parsedEvent["who"][0]);
		$this->assertEquals("Mount Doom", $parsedEvent["where"]);
		$this->assertEquals("The One Ring", $parsedEvent["what"]);
	}
	
	public function testEventRenderManyCharacters() {
		$parsedEvent = Event::parseEvent([null,
				"who=Sauron",
				"who=Last Alliance",
				"who=Gil-galad",
				"who=Elendil",
				"who=Isildur",
				"when=3434",
				"where=Barad-dûr",
				"what=The One Ring",
		]);
		
		$this->assertEquals("3434", $parsedEvent["when"]);
		$this->assertEquals("Sauron", $parsedEvent["who"][0]);
		$this->assertEquals("Last Alliance", $parsedEvent["who"][1]);
		$this->assertEquals("Gil-galad", $parsedEvent["who"][2]);
		$this->assertEquals("Elendil", $parsedEvent["who"][3]);
		$this->assertEquals("Isildur", $parsedEvent["who"][4]);
		$this->assertEquals("Barad-dûr", $parsedEvent["where"]);
		$this->assertEquals("The One Ring", $parsedEvent["what"]);
	}

	public function testEventRenderMissingInfo() {
		try {
			$parsedEvent = Event::parseEvent([null,
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
			$parsedEvent = Event::parseEvent([null,
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