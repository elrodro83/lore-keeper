<?php
class Era {
	private $pageTitle = "";
	
	private $categories = array();
	private $name = "";
	private $from = "";
	private $to = "";
	
	private $thumb = "";
	
	function __construct($args) {
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
		$this->extractOptions( $opts );
	}
	
	/**
	 * Converts an array of values in form [0] => "name=value" into a real
	 * associative array in form [name] => value
	 *
	 * @param array string $options
	 * @return array $results
	 */
	public function extractOptions( array $options ) {
		foreach ( $options as $option ) {
			$pair = explode( '=', $option, 2 );
			if ( count( $pair ) == 2 ) {
				$name = trim( $pair[0] );
				$value = trim( $pair[1] );
				if("from" == $name) {
					$this->from = new LKDate($value);
				} else if("to" == $name) {
					$this->to = new LKDate($value);
				} else if("name" == $name) {
					$this->name = $value;
				}
			}
		}
		//Now you've got an array that looks like this:
		//	[foo] => bar
		//	[apple] => orange
	
		if(empty($this->from)) {
			throw new Exception("Missing mandatory 'from' data: " . json_encode($options));
		} else if(empty($this->to)) {
			throw new Exception("Missing mandatory 'to' data: " . json_encode($options));
		} else if(empty($this->name)) {
			throw new Exception("Missing mandatory 'name' data: " . json_encode($options));
		}
	}

	public static function renderEra($parsedEra) {
		return "";
	}

	public function getName() {
		return $this->name;
	}
	
	public function getFrom() {
		return $this->from;
	}
	
	public function getTo() {
		return $this->to;
	}
	
	public function setThumb($thumb) {
		$this->thumb = $thumb;
	}
	
	public function getThumb() {
		return $this->thumb;
	}
}
