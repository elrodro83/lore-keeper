<?php

class LoreKeeper {
	
	public static function onParserFirstCallInit(&$parser) {
		$parser->setFunctionHook ( 'calendar', 'LoreKeeper::wfCalendarRender' );
		$parser->setFunctionHook ( 'event', 'LoreKeeper::wfEventRender' );
		$parser->setFunctionHook ( 'timeline', 'LoreKeeper::wfTimelineRender' );
		return true;
	}
	
	public static function wfCalendarRender( $parser, $months = '' , $qualifier = '' , $offset = '' ) {
		try {
			$parsedCalendar = new Calendar($months , $qualifier , $offset);
			return array($parsedCalendar->render(), 'noparse' => false, 'nowiki' => false );
		} catch(Exception $e) {
			return array("* '''" . htmlspecialchars($e->getMessage()) . "'''", 'noparse' => false );
		}
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
			$timeline = new Timeline($parser, func_get_args());
			return array(Event::renderEvents($timeline->getEvents(), true), 'noparse' => false, 'nowiki' => false );
		} catch(Exception $e) {
			return array("* '''" . htmlspecialchars($e->getMessage()) . "'''", 'noparse' => false );
		}
	}
}