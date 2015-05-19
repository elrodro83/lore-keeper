<?php

class LoreKeeper {
	
	public static function onParserFirstCallInit(&$parser) {
		$parser->setFunctionHook ( 'event', 'LoreKeeper::wfEventRender' );
		return true;
	}
	
	public static function wfEventRender( $parser ) {
		try {
			$parsedEvent = Event::parseEvent(func_get_args());
			return array(Event::renderEvent($parsedEvent), 'noparse' => false, 'nowiki' => false );
		} catch(Exception $e) {
			return array("* '''" . htmlspecialchars($e->getMessage()) . "'''", 'noparse' => false );
		}
	}
}