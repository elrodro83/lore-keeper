<?php

class LoreKeeper {
	
	public static function onParserFirstCallInit(&$parser) {
		$parser->setFunctionHook ( 'calendar', 'LoreKeeper::wfCalendarRender' );
		$parser->setFunctionHook ( 'event', 'LoreKeeper::wfEventRender' );
		$parser->setFunctionHook ( 'timeline', 'LoreKeeper::wfTimelineRender' );
		$parser->setFunctionHook ( 'date', 'LoreKeeper::wfDateRender' );
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
			if("TABLE" === $timeline->getRenderMode()) {
				return array(Event::renderEvents($timeline->getEvents(), true), 'noparse' => false, 'nowiki' => false );
			} else if("TIMELINE" === $timeline->getRenderMode()) {
				return array(Event::renderEventsTimeline($parser, $timeline->getEvents(),
						($timeline->getCalendarJSFormatter() != null ? $timeline->getCalendarJSFormatter() : "/default.js"),
						true), 'noparse' => true, 'isHTML' => true, "markerType" => 'nowiki' );
			} else {
				return array("* '''Invalid renderMode: " . htmlspecialchars($timeline->getRenderMode()) . "'''", 'noparse' => false );
			}
		} catch(Exception $e) {
			return array("* '''" . htmlspecialchars($e->getMessage()) . "'''", 'noparse' => false );
		}
	}
	
	public static function wfDateRender($parser, $dateString, $targetEra) {
// 		return array($dateString . ' -> ' . $targetEra, 'noparse' => false);
		
		try {
			$date = new LKDate($dateString);
			return array($date->toCalendar($targetEra)->getDateString(), 'noparse' => false);
		} catch(Exception $e) {
			return array("* '''" . htmlspecialchars($e->getMessage()) . "'''", 'noparse' => false );
		}
	}
}