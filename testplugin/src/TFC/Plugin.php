<?php

/*
 * This class implements the ArrayAccess interface
 * This allows us to use it like PHP’s array
 * E.g $plugin = new Simplarity_Plugin();
       $plugin['version'] = '1.0.0';

 * The functions offsetSet, offsetExists, offsetUnset and offsetGet are required by ArrayAccess to be implemented.
 */

namespace TFC;
use \ArrayAccess;
use \ReflectionClass;

class Plugin implements ArrayAccess {
	protected $contents;

	public function __construct() {
		$this->contents = array();
	}

	public function offsetSet( $offset, $value ) {
		$this->contents[$offset] = $value;
	}

	public function offsetExists($offset) {
		return isset( $this->contents[$offset] );
	}

	public function offsetUnset($offset) {
    	unset( $this->contents[$offset] );
	}

	public function offsetGet($offset) {
    	if( is_callable($this->contents[$offset]) ){
      		return call_user_func( $this->contents[$offset], $this );
    	}
    	return isset( $this->contents[$offset] ) ? $this->contents[$offset] : null;
  	}


	// The run function will loop through the contents of the container and run the runnable objects
	// The ReflectionClass class reports information about a class.
	
	public function run(){
    	foreach( $this->contents as $key => $content ){
      		if( is_callable($content) ){
        		$content = $this[$key];
      		}
      		if( is_object( $content ) ){
        		$reflection = new ReflectionClass( $content );
        		if( $reflection->hasMethod( 'run' ) ){
          			$content->run(); // Call run method on object
        		}
      		}
    	}
  	}
}