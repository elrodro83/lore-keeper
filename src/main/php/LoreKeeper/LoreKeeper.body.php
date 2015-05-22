<?php

class LoreKeeper {
	
	public static function onParserFirstCallInit(&$parser) {
		$parser->setFunctionHook ( 'event', 'LoreKeeper::wfEventRender' );
		$parser->setFunctionHook ( 'timeline', 'LoreKeeper::wfTimelineRender' );
		return true;
	}
	
	public static function wfEventRender( $parser ) {
		try {
			$parsedEvent = new Event(func_get_args());
			return array(Event::renderEvents([$parsedEvent]), 'noparse' => false, 'nowiki' => false );
		} catch(Exception $e) {
			return array("* '''" . htmlspecialchars($e->getMessage()) . "'''", 'noparse' => false );
		}
	}
	
	public static function wfTimelineRender( $parser ) {
		try {
			$timeline = new Timeline($parser);
			return array(Event::renderEvents($timeline->getEvents(), true), 'noparse' => false, 'nowiki' => false );
		} catch(Exception $e) {
			return array("* '''" . htmlspecialchars($e->getMessage()) . "'''", 'noparse' => false );
		}
	}
}