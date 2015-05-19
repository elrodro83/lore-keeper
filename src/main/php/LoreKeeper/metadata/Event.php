<?php
class Event {
	public static function parseEvent($args) {
		//Suppose the user invoked the parser function like so:
		//{{#myparserfunction:foo=bar|apple=orange}}
	
		$opts = array();
		// Argument 0 is $parser, so begin iterating at 1
		for ( $i = 1; $i < count($args); $i++ ) {
			array_push($opts, $args[ $i ]);
		}
		//The $opts array now looks like this:
		//	[0] => 'foo=bar'
		//	[1] => 'apple=orange'
	
		//Now we need to transform $opts into a more useful form...
		return Event::extractOptions( $opts );
	}
	
	/**
	 * Converts an array of values in form [0] => "name=value" into a real
	 * associative array in form [name] => value
	 *
	 * @param array string $options
	 * @return array $results
	 */
	public static function extractOptions( array $options ) {
		$results = array();
	
		foreach ( $options as $option ) {
			$pair = explode( '=', $option, 2 );
			if ( count( $pair ) == 2 ) {
				$name = trim( $pair[0] );
				$value = trim( $pair[1] );
				if("who" == $name) {
					if(empty($results[$name])) {
						$results[$name] = array();
					}
					array_push($results[$name], $value);
				} else {
					$results[$name] = $value;
				}
			}
		}
		//Now you've got an array that looks like this:
		//	[foo] => bar
		//	[apple] => orange
	
		if(empty($results["who"])) {
			throw new Exception("Missing mandatory 'who' data: " . json_encode($options));
		} else if(empty($results["when"])) {
			throw new Exception("Missing mandatory 'when' data: " . json_encode($options));
		} else if(empty($results["where"])) {
			throw new Exception("Missing mandatory 'where' data: " . json_encode($options));
		}
	
		return $results;
	}
	
	public static function renderEvent($parsedEvent) {
		$markUp = "{| class=\"wikitable\"\n";
			
		$markUp .= "! When\n";
		$markUp .= "! Where\n";
		$markUp .= "! Who\n";
		if(!empty($parsedEvent["what"])) {
			$markUp .= "! What\n";
		}
			
		$markUp .= "|-\n";
		$markUp .= "| " . htmlspecialchars($parsedEvent["when"] . "\n");
		$markUp .= "| " . htmlspecialchars($parsedEvent["where"] . "\n");
		$markUp .= "|\n";
		foreach($parsedEvent["who"] as $who) {
			$markUp .= "* " . htmlspecialchars($who) . "\n";
		}
		if(!empty($parsedEvent["what"])) {
			$markUp .= "| " . htmlspecialchars($parsedEvent["what"] . "\n");
		}
			
		$markUp .= "|}";
		return $markUp;
	}
}