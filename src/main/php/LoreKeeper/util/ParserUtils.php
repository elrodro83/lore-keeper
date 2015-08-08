<?php

class ParserUtils {

	public static function getCategories($pageMarkup) {
		$categories = array();
		preg_match_all("/\[\[[cC]ategory:([^\]\]]*)\]\]/m", $pageMarkup, $categories);
		
		$cats = array();
		foreach($categories[1] as $category) {
			array_push($cats, $category);
		}
		
		return $cats;
	}
	
	public static function getFiles($pageMarkup) {
		$linkedPageImages = array();
		preg_match_all("/\[\[([fF]ile:[^\]\]]*)\]\]/m", $pageMarkup, $linkedPageImages);
			
		$imgs = array();
		foreach($linkedPageImages[1] as $linkedPageImage) {
			array_push($imgs, explode("|", $linkedPageImage)[0]);
		}
		
		return $imgs;
	}

	public static function getEras($pageMarkup) {
		$rawEras = array();
		
		preg_match_all("/{{#era:([^}}]*)}}([^{]*)/", $pageMarkup, $rawEras);

		$parsedEras = array();
		for ($i = 0; $i < count($rawEras[1]); $i++) {
			$rawEra = $rawEras[1][$i];
			$eraBody = $rawEras[2][$i];

			$parsedEra = new Era(array_merge([$parser],
			preg_split("/\|(?=name|from|to)/",
					str_replace(array("\r\n", "\n", "\r"), "", $rawEra))));
			$parsedEra->setThumb(ParserUtils::getFiles($eraBody)[0]);
			
			array_push($parsedEras, $parsedEra);
		}
		
		return $parsedEras;
	}
	
	public static function parseEvents($parser, $pageTitle, $pageMarkup) {
		$events = array();
		preg_match_all("/={2,} ([^=]{2,}) ={2,}[^=]{2,}{{#event:([^}}]*)}}([^=]*)/", $pageMarkup, $events);

		$subtitles = $events[1];
		$eventMetas = $events[2];
		$eventBodys = $events[3];

		$parsedEvents = array();
		if(!empty($events[0])) {
			for ($i = 0; $i < count($subtitles); $i++) {
				$subtitle = $subtitles[$i];
				$eventMeta = $eventMetas[$i];
				$eventBody = $eventBodys[$i];

				try {
					$parsedEvent = new Event(array_merge([$parser],
							preg_split("/\|(?=when|what|where|who)/",
									str_replace(array("\r\n", "\n", "\r"), "", $eventMeta))));
					$parsedEvent->setTitle($pageTitle, $subtitle);
					$parsedEvent->setBody($eventBody);
					
					array_push($parsedEvents, $parsedEvent);
				} catch (Exception $e) {
					throw new Exception("$pageTitle#$subtitle: ($eventMeta)" . $e->getMessage());
				}
			}
		}
		
		return $parsedEvents;
	}
	
	/**
	 * Gets a url for an image.
	 * 
	 * @param Parser $parser the parser object
	 * @param string $fileName the name of the file to get an url for, in "File:filename.extension" format.
	 * @return string
	 */
	public static function resolveImageLink(Parser $parser, $fileName) {
		$imgUrl = array();
		preg_match('/src=\\"([^\\"]*)\\"/',
				$parser->makeImage(Title::newFromText($fileName), ""),
				$imgUrl);
		return $imgUrl[1];
	}
}