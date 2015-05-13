<?php

class LoreKeeper {
	
	public static function onParserFirstCallInit(&$parser) {
		$parser->setHook ( 'event', 'LoreKeeper::wfEventRender' );
		return true;
	}
	
	public static function wfEventRender($input, array $args, Parser $parser, PPFrame $frame) {
		$dump =  array(
			'content' => $input,
			'atributes' => (object)$attribs,
		);

		// Very important to escape user data with htmlspecialchars() to prevent
		// an XSS security vulnerability.
		$html = '<pre>Dump Tag: ' . htmlspecialchars( FormatJson::encode( $dump, /*prettyPrint=*/true ) ) . '</pre>';

		return $html;
	}
}