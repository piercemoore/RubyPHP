<?php
// Set awesome namespace
namespace PierceMoore\RubyPHP;

/**
 * 
 * HEY THERE! And welcome to RubyPHP, your new best friend. 
 * 
 * Have you ever written code in Ruby and then tried to do the same things in PHP but realized that you definitely couldn't? Yeah. Me too. That's why this class came to be.
 * 
 **/
require_once('includes/class.string.rubyphp.php');
require_once('includes/class.array.rubyphp.php');
require_once('includes/class.boolean.rubyphp.php');
require_once('includes/class.number.rubyphp.php');

// Let's write a function that you can use in your own code to decrease the size of object instantiation. 
function r( $item ) {
	switch( gettype( $item )) {
		case "array":
			return new rArray($item);
			break;
		case "integer":
		case "float":
			return new rNumber($item);
			break;
		case "string":
			return new rString($item);
			break;
		case "boolean":
			return new rBoolean($item);
			break;
		default:
			return $item;
	}

}
/*
As long as you use the above function, your code will go from this:

$f = new r("Pierce");

to

$f = r("Pierce"); 

That's about as easy as it gets.

*/

/**
 * I would call this class RubyPHP, but in an effort to make development with this class more efficient, I figured writing $foo = new RubyPHP("$string") a thousand times wasn't worth it. So I decided $foo = r("bar"); was better.
 * 
 * @package RubyPHP
 * @brief Taking all the beautiful simplicity of Ruby and implementing it in PHP!
 * @author Pierce Moore
 * @version 0.1
 * @copyright Pierce Moore 2012 , Refreshed Web Design 2012
 */
class r {

	/**
	* First we declare all our publicly accessible variables for each data type. These will be accessible by $foo->$var. 
	* 
	* ex:
	* $foo = r("$string");
	* print $foo->length;
	* print $foo->even;
	* print $foo-slashes;
	* 
	* etc..
	* */

	public $to_s;
	public $to_f;
	public $to_i;
	public $to_int;
	public $length;
	public $capitalize;
	public $count;
	public $isMoney;
	public $even;
	public $odd;
	public $reverse;
	public $md5;
	public $sha1;
	public $val;
	public $trim;
	public $slashes;
	public $flipArray;
	public $chaining = false;
	public $debug = false;
	public $chars = array();

	public $allowedMethods = array(
		"autorun" => array(
			"val",
			"flip",
			"reverse",
			"to_s",
			"to_f",
			"to_i",
			"to_int",
			"money",
			"even",
			"length",
			"md5",
			"sha1",
			"escape",
			"NaN",
			"swapcase",
			"downcase",
			"hex",
			"toHex",
			"first",
			"last",
			"srt",
			"slashes",
			"cnt",
			"tr",
			"shuf"
		),
		"all" => array(
			"_call",
			"val",
			"flip",
			"reverse",
			"to_s",
			"length",
			"dump",
			"destroy",
			"eql",
			"responds_to",
			"showObject",
			"secure",
			"md5",
			"sha1",
			"escape",
			"repeat"
		),
		"numbers" => array(
			"to_f",
			"to_i",
			"money",
			"even",
			"odd",
			"gcd",
			"rnd",
			"mult",
			"av",
			"infinite",
			"NaN",
			"zero"
		),
		"string" => array(
			"each",
			"swapcase",
			"concat",
			"prepend",
			"downcase",
			"each_char",
			"hex",
			"toHex",
			"index",
			"match",
			"replace",
			"each",
			"cap",
			"to_int",
			"to_i",
			"to_f",
			"first",
			"last",
			"index",
			"srt",
			"push",
			"sample",
			"rotate",
			"shuf",
			"slice",
			"ex",
			"im",
			"slashes",
			"cnt",
			"tr",
			"del",
			"pos",
			"repeat"
		),
		"array" => array(
			"first",
			"last",
			"index",
			"srt",
			"push",
			"rotate",
			"sample",
			"shuf",
			"slice",
			"uniq",
			"zip",
			"serial",
			"unserial",
			"toJSON",
			"fromJSON",
			"length",
			"cap",
			"av",
			"concat",
			"prepend",
			"downcase",
			"each_char",
			"match",
			"reverse",
			"each",
			"tr",
			"del",
			"ex",
			"im",
			"replace",
			"cnt",
			"slashes",
			"pos",
			"repeat",
			"flatten"
		),
		"boolean" => array(

		)
	);

	function __construct( $item , $chaining = true , $debug = false ) {

		$this->self = $item;
		$this->val = $item;
		$this->origVal = $this->val;
		$this->buildObject();

		$this->setChaining( $chaining );
		$this->setDebug( $debug );

		if( $this->debug )
			$this->showObject();

		return $this;
	}

	/**
	 * Just a plain utility function to deal with errors. 
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn exception()
	 * @param Exception $e - The function that threw the exception
	 * @param string $msg - The message that was thrown
	 * @return void
	 * */
	public function exception( Exception $e , $msg ) {

		print "####################################### \n\n<br /><br />";

		print "Exception: $msg \n";

		print "Stack Trace is as follows: \n <pre>";

		print_r( $e->getTrace() );

		print "#######################################";

	}

/******************************************************
 * 
 * 			UTILITY FUNCTIONS
 * 
 * ****************************************************/
	/**
	 * A pre-emptive security function. You are free to run all functions as-is, but if you want to ensure that nothing weird happens when trying 
	 * to run a method or access a property, use this function to determine whether or not that method or property is valid.
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn responds_to()
	 * @param string $function - The function that you are trying to test for
	 * @return boolean
	 * */
	public function responds_to( $function ) {

		return in_array( $function , $this->methods );
	}

	/**
	 * This utility function will call a user-provided function on the data object.
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn _call()
	 * @param string $function - The user-supplied function that will be run against the object value
	 * @return mixed
	 **/
	public function _call( $function ) {

		return $function( $this->val );

	}

	/**
	 * Set the global chaining switch on/off
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn setChaining()
	 * @param boolean $mode - True/False mode for global chaining. Defaults to on because it's just better that way.
	 * @return mixed
	 **/
	public function setChaining( $mode ) {

		$this->chaining = $mode;
		return $this;

	}

	/**
	 * Set the global debug switch on/off
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn setDebug()
	 * @param boolean $mode - True/False mode for global debug. Defaults to on because it's just better that way.
	 * @return mixed
	 **/
	public function setDebug( $mode ) {

		$this->debug = $mode;
		return $this;

	}

	/**
	 * An easy function that will send data depending on whether or not global chaining is enable. If enabled, returns $this. If disabled, simply returns value.
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn chain()
	 * @param mixed $data - The data that could be returned.
	 * @return mixed
	 **/
	public function chain( $data ) {

		if( $this->chaining ) {
			$this->val = $data;
			return $this;
		} else {
			return $data;
		}

	}

	/**
	 * Just a simple utility function to display the formatted object.
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn showObject()
	 * @return void
	 * */
	public function showObject( $fetch = false ) {

		if( $fetch ) {
			return $this;
		} else {
			print "<pre>";
			print_r( $this );
			print "</pre>";
		}

	}


	/**
	 * This is the function that is called upon instantiation. It builds the corresponding object for the item provided and gives it the properties and functionality of a ruby object.
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn buildObject()
	 * @return void
	 **/
	private function buildObject() {

		try {

			$s = $this->self;

			$this->type = gettype( $s );

			switch( $this->type ) {

				case "boolean":
					$this->buildBoolean();
					break;
				case "string":	
					$this->buildString();
					break;
				case "integer":
					$this->buildInteger();
					break;
				case "double":
					$this->buildDouble();
					break;
				case "array":
					$this->buildArray();
					break;
				// If it is an object, empty or null type or is otherwise not on this list, we spit it back out. OOPSIES
				case "object":
				case null:
				default: 
					throw new exception("Invalid argument or item supplied to RubyPHP. Please try again.");
					break;
			}	

			$this->runMethods();

		} catch( Exception $e ) {

			$this->exception( $e , $e->getMessage() );

		}
	}

	/**
	 * With every object instantiation, let's build the basic data that will be present for every single type of value.
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn runMethods()
	 * @return void
	 **/
	private function runMethods() {

		foreach( $this->allowedMethods['autorun'] as $k=>$v ) {
			$this->$v = $this->$v();
		}

	}

	/**
	 * Let's go down the list, doing the easy stuff first.
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn buildBoolean()
	 * @return boolean
	 **/
	private function buildBoolean() {

		$this->val = ( $this->self ) ? 1 : 0;
		$this->valString = ( $this->val ) ? "true" : "false";
		$this->methods = $this->allowedMethods['boolean'];
		return $this->val;
	}

	/**
	 * Still going. Now we're building the "string" object.
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn buildString()
	 * @return boolean
	 * */
	private function buildString() {

		$this->valString = $this->val;
		$this->chars = str_split( $this->val );
		$this->methods = $this->allowedMethods["string"];
		return $this->val;

	}

	/**
	 * Still going. Now we're building the "integer" object.
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn buildInteger()
	 * @return boolean
	 * */
	private function buildInteger() {

		$this->valString = $this->to_s($this->val);
		$this->methods = $this->allowedMethods['numbers'];
		return $this->val;
	}

	/**
	 * Still going. Now we're building the "double" object.
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn buildDouble()
	 * @return boolean
	 * */
	private function buildDouble() {

		$this->valString = $this->to_s();
		$this->methods = $this->allowedMethods['numbers'];
		return $this->val;

	}

	/**
	 * Still going. Now we're building the "array" object.
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn buildArray()
	 * @return boolean
	 * */
	private function buildArray() {

		$this->valString = null;
		$this->methods = $this->allowedMethods['array'];
		$this->runMethods();
		return $this->val;
	}

	/**
	 * Simple val() function, returns the value of the object. 
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn val()
	 * @return mixed
	 **/
	public function val() {

		return $this->val;

	}

	/**
	 * Dumps the contents of the object 
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn dump()
	 * @return mixed
	 **/
	public function dump() {

		var_dump( $this->val );

	}

	/**
	 * Destroys an object 
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn destroy()
	 * @return void
	 **/
	public function destroy() {

		foreach( $this as $k ){
			$k = "";
		}

	}

	/**
	 * Secures an object 
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn secure()
	 * @param string $mode - The hashing method to use. Accepts "md5" or "sha1"
	 * @param string $salt - The salt to use on the item prior to it being hashed. Default: null (no salt)
	 * @param string $position - The position to place the salt. Accepts "first" or "last". Default: Null (no salt position)
	 * @return string
	 **/
	public function secure( $mode = "sha1" , $salt = null , $position = null ) {

		try {
			if( is_array($this->val)) {
				throw new exception("Arrays are not allowed to be secured. Please try again.");
			}
			$string = $this->val;
			if( $salt != null ) {
				if( strcmp( $position , "first" ) == 0 ) {
					$string = $salt . $this->val;
				} else if( strcmp( $position , "last" ) == 0 ) {
					$string = $this->val . $salt;
				} else {
					throw new exception("Invalid salt location specified, please try again.");
				}
			}
			if( !in_array( $mode , hash_algos() ) ) {
				throw new exception("Invalid or unsupported hashing algorithm specified. Please try again.");
			}
			return $this->chain( $mode( $string ) );
		} catch( Exception $e ) {
			$this->exception( $e , $e->getMessage() );
		}

	}

	/**
	 * Secures an object using md5 hashing algorithm
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn md5()
	 * @return string
	 **/
	public function md5() {

		if( $this->type == "array" ) {
			return false;
		}

		return $this->chain( md5( $this->val ) );

	}

	/**
	 * Secures an object using sha1 hashing algorithm
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn sha1()
	 * @return string
	 **/
	public function sha1() {

		if( $this->type == "array" ) {
			return false;
		}
		return $this->chain( sha1( $this->val ) );

	}

	/**
	 * Secures a value for storage in a database by escaping.
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn escape()
	 * @return string
	 **/
	public function escape() {

		if( $this->type == "array" ) {
			return false;
		}
		return $this->chain( mysql_real_escape_string( $this->val ) );

	}

	/**
	 * Returns the REVERSE of the object. If it's a boolean, it will act like an on/off switch. For arrays, strings, and numbers it will just reverse the order of the characters. 
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn flip()
	 * @return mixed
	 **/
	public function flip() {

		try {

			switch( $this->type ) {

				case "boolean":
					return $this->chain( !$this->val );
					break;
				case "integer":
				case "string":
					$this->flipArray = array_reverse( $this->chars );
					return $this->chain( implode(array_reverse($this->chars)) );
					break;
				case "array":
					$this->flipArray = array_reverse( $this->val );
					return $this->chain( $this->flipArray );
					break;
				default:
					return null;
			}
		} catch( Exception $e ) {
			$this->exception( $e , $e->getMessage() );
		}

	}

	/**
	 * Returns the stringified version of the object's value. 
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn to_s()
	 * @return string
	 **/
	public function to_s() {

		return $this->chain( (string)$this->val );

	}

	/**
	 * Returns the integer version of the object's value. 
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn to_i()
	 * @return int
	 **/
	public function to_i() {

		return $this->chain( (int)$this->val );

	}

	/**
	 * Returns the integer version of the object's value. ## DIFFERENT SYNTAX ##
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn to_int()
	 * @return int
	 **/
	public function to_int() {

		return $this->chain( (int)$this->val );

	}

	/**
	 * Returns the floating point decimal version of the object's value.
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn to_f()
	 * @return float
	 **/
	public function to_f() {

		return $this->chain( (float)$this->val );

	}

	/**
	 * Simply returns the length of the value. If it's a number or string, it will return the number of characters. If an array or object, it will return the number of elements present. 
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn length()
	 * @return int
	 **/
	public function length( $mode = COUNT_NORMAL ) {

		if( !is_array( $this->val )) {

			return $this->chain( strlen( (string)$this->val ) );

		} else if (is_array( $this->val )) {

			return $this->chain( count( $this->val , $mode ) );
		}

	}

	/**
	 * Deals with casing in strings. Is used to modify the upper- and lower-case nature of the string. 
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn cap()
	 * @param string $type - The parameters that the capitalize function must work within. Accepts: "first" , "all" , "none" , "words"
	 * @param mixed $value - The value to be modified. I found that this parameter was necessary for other functions that required casing and needed to pass their own values.
	 * @return string
	 **/
	public function cap( $type , $value = null ) {

		try {

			if( !isset( $value ) || value == null ) {
				$value = $this->val;
			}

			switch( $type ) {

				case "first":
					return $this->chain( ucfirst( strtolower( $value )) );
					break;
				case "all":
					return $this->chain( strtoupper( $value ) );
					break;
				case "none":
					return $this->chain( strtolower( $value ) );
					break;
				case "words":
					return $this->chain( ucwords( strtolower( $value )) );
					break;
				default: 
					throw new exception("Invalid capitalization mode supplied. 'first', 'all', 'none', and 'words' are acceptable.");
			}

		} catch( Exception $e ) {

			$this->exception( $e , $e->getMessage() );

		}

	}

	/**
	 * Returns the first item in an array
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn first()
	 * @return mixed
	 **/
	public function first() {

		if( is_array( $this->val )) {
			return $this->chain( reset($this->val) );
		} else if( isset($this->chars) && is_array( $this->chars )) {
			return $this->chain( reset($this->chars) );
		} else {
			return false;
		}

	}

	/**
	 * Returns the last item in an array
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn last()
	 * @return mixed
	 **/
	public function last() {

		if( is_array( $this->val )) {
			return $this->chain( end($this->val) );
		} else if( isset($this->chars) && is_array( $this->chars )) {
			return $this->chain( end($this->chars) );
		} else {
			return false;
		}

	}

	/**
	 * Returns a JSON object of a value 
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn toJSON()
	 * @return string
	 **/
	public function toJSON() {

		return $this->chain( json_encode( $this->val ) );

	}

	/**
	 * Returns an array or string from a provided JSON object 
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn fromJSON()
	 * @return string
	 **/
	public function fromJSON() {

		return $this->chain( json_decode( $this->val ) );

	}

	/**
	 * Serializes a dataset 
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn serial()
	 * @return string
	 **/
	public function serial() {

		return $this->chain( serialize( $this->val ) );

	}

	/**
	 * Unserializes a dataset 
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn unserial()
	 * @return mixed
	 **/
	public function unserial() {

		return $this->chain( unserialize( $this->val ) );

	}

	/**
	 * Retrieves a specific index from an array 
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn index()
	 * @param int $key - The key to locate
	 * @return mixed
	 **/
	public function index( $key ) {
		if( $key > count( $this->val )) {
			return $this->chain( $this->val );
		}
		if(!is_array( $this->val )) {
			return $this->chain( $this->chars[$key] );
		} else {
			return $this->chain( $this->val[$key] );
		}

	}	

	/**
	 * 
	 * ################################################################
	 * 
	 * 			NUMBER FUNCTIONS, yay! (This includes float and int)
	 * 
	 * ################################################################
	 * 
	 * */

	/**
	 * Formats the value as a monetary value.
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn money()
	 * @param string $symbol - The currency denominator to use
	 * @param string $decimal - The decimal separator
	 * @return string
	 **/
	public function money( $symbol = "$" , $decimal = "." ) {

		$num = @number_format($this->val);
		if( is_int( $this->val )) {
			return $this->chain( "{$symbol}{$num}{$decimal}00" );
		} else if( is_float($this->val) ) {
			return $this->chain( "{$symbol}{$num}" );
		} else if( !is_array($this->val) && !is_bool($this->val)) {
			return $this->chain( "{$symbol}{$num}{$decimal}00" );
		} else {
			return $this->chain( $this->val );
		}

	}

	/**
	 * Determines if the number is odd or even - EVEN specific
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn even()
	 * @return boolean
	 **/
	public function even() {

		if( $this->val % 2 ) {
			$this->even = false;
		} else {
			$this->even = true;
		}
		if( $this->chaining ) {
			return $this;
		} else {
			return $this->even;
		}

	}	

	/**
	 * Determines if the number is odd or even - ODD specific
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn odd()
	 * @return boolean
	 **/
	public function odd() {

		if( $this->val % 2 ) {
			$this->odd = false;
		} else {
			$this->odd = true;
		}
		if( $this->chaining ) {
			return $this;
		} else {
			return $this->odd;
		}

	}	

	/**
	 * Multiplies the value by a given number
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn mult()
	 * @param mixed $times - The multiplier
	 * @return mixed
	 **/
	public function mult( $times ) {

		return $this->chain( $this->val * $times );

	}	

	/**
	 * Determines the absolute value of a number
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn av()
	 * @return mixed
	 **/
	public function av() {

		return $this->chain( abs($this->val) );

	}	

	/**
	 * Determines the greatest common denominator for the value and a provided number
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn gcd()
	 * @param mixed $comparison - The number to find the GCD with
	 * @return mixed
	 **/
	public function gcd( $comparison ) {

		return $this->chain( $this->val * $comparison );

	}	

	/**
	 * Rounds the object to a specified place
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn rnd()
	 * @param int $place - The precision with which you would like to round the number.
	 * @return mixed
	 **/
	public function rnd( $place ) {

		if( $this->NaN( $this->val ) ) {
			return $this->chain( $this->val );
		}
		return $this->chain( round( $this->val , $place ) );

	}	

	/**
	 * Determines if the value is infinite
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn infinite()
	 * @return boolean
	 **/
	public function infinite() {

		return $this->chain( is_infinite( $this->val ) );

	}	

	/**
	 * Determines if the value is a number at all
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn NaN()
	 * @return boolean
	 **/
	public function NaN() {

		$this->NaN = is_nan( (double)$this->val );
		if( $this->chaining ) {
			return $this;
		} else {
			return $this->NaN;
		}

	}

	/**
	 * Determines if the value is zero
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn zero()
	 * @return boolean
	 **/
	public function zero() {

		if( $this->val != 0 ) {
			$this->zero = false;
		} else {
			$this->zero = true;
		}
		if( $this->chaining ) {
			return $this;
		} else {
			return $this->zero;
		}

	}

	/**
	 * 
	 * ################################################################
	 * 
	 * 			ARRAY FUNCTIONS, yay!
	 * 
	 * ################################################################
	 * 
	 * */


	/**
	 * Sorts an array according to the provided parameters.
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn srt()
	 * @return mixed
	 **/
	public function srt( $method = 'sort') {

		if( !is_array( $this->val ) ) {
			return $this->val; 
		}
		return $method( $this->val );

	}

	/**
	 * Places an item into a pre-existing array at the specified location. Default: "end"
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn push()
	 * @param mixed $item - The item to be placed into the array
	 * @param string $location - The item to be 
	 * @return mixed
	 **/
	public function push( $item , $location = "end" ) {

		try {

			if( !is_array( $this->val ) ) {
				return $this->val; 
			} else {
				switch( $location ) {
					case "fin":
					case "end":
						return $this->chain( array_push( $this->val , item ) );
						break;
					case "start":
					case "beg":
						return $this->chain( array_unshift( $this->val , $item ) );
						break;
					default:
						throw new exception("Invalid location specified in push(). The function accepts 'beg','start','fin', and 'end'");
				}
			}
		} catch( Exception $e ) {
			$this->exception( $e , $e->getMessage() );
		}

	}

	/**
	 * Rotates an array (first element becomes last element, second element becomes first.) 
	 * 
	 * NOTE: THIS IS A VERY ROUGH CUT. Just.. Bear with me.
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn rotate()
	 * @return array
	 **/
	public function rotate() {

		if( !is_array( $this->val ) ) {
			return $this->$this->val;
		}
		return $this->chain( array_push(array_shift( $this->val )) );

	}

	/**
	 * Returns a random sample of an array of a specified number of elements.
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn sample()
	 * @param int $size - The number of random elements to return.
	 * @return mixed
	 **/
	public function sample( $size = 1 ) {

		$return = array();

		if( !is_array( $this->val ) ) {
			return $this->val; 
		}
		if( count( $this->val ) < $size ) {
			$size = count( $this->val );
		}
		for( $i = 0; $i < $size; $i++ ) {
			$key = array_rand( $this->val );
			$return[] = array( $key => $this->val[$key] );
		}
		$this->sample = $return;
		if( $this->chaining ) {
			return $this;
		} else {
			return $this->sample;
		}
		

	}

	/**
	 * Shuffles an array and returns it. Useful for random selection.
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn shuf()
	 * @return mixed
	 **/
	public function shuf() {

		if( $this->type == "array" ) {
			$arr = $this->val;
			shuffle( $arr );
			return $this->chain( $arr );
		} else if( $this->type == "string" ) {
			$arr = $this->chars;
			shuffle( $arr );
			return $this->chain( $arr );
		}
		if( $this->chaining ) {
			return $this;
		} else {
			return $this->val;
		}

	}

	/**
	 * Slices a specific part of the array and returns it. If the provided "start" offset is larger than the largest key, an exception will be thrown.
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn slice()
	 * @param int $start - The starting index of the slice
	 * @param int $count - The size of the slice. 
	 * @return mixed
	 **/
	public function slice( $start = 0 , $count = null ) {

		try {
			if( $count == null ) {
				throw new exception("The size of the desired slice is required, but was not provided. Please provide both the starting point and the size of the slice.");
			}
			if( is_array( $this->val ) ) {
				if( $start > count( $this->val ) ) {
					throw new exception("The key you provided is greater than the highest key in the array. Please select a smaller starting key.");
				}
				$return = array_slice( $this->val , $start , $count );
			} else if( is_string( $this->val )) {
				if( $start > count( $this->val ) ) {
					throw new exception("The key you provided is greater than the highest key in the character array. Please select a smaller starting key.");
				}
				$return = array_slice( $this->chars , $start , $count );
			} else {
				throw new exception("You are trying to slice an unsupported data type. Please try again.");
			}
			$this->slice = $return;
			if( $this->chaining ) {
				return $this;
			} else {
				return $this->slice;
			}
		} catch( Exception $e ) {
			$this->exception( $e , $e->getMessage() );
		}

	}

	/**
	 * "Cleans" an array by removing all duplicate elements and returning the array with no duplicates.
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn uniq()
	 * @return mixed
	 **/
	public function uniq() {

		if( is_array( $this->val )) {
			array_unique( $this->val );
		} else if( is_array( $this->chars )) {
			array_unique( $this->chars );
		}
		$return = $this->val;
		if( $this->chaining ) {
			return $this;
		} else {
			return $return;
		}

	}

	/**
	 * "Zip" takes multiple arrays and interlaces them at their matching positions
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn zip()
	 * @todo Check to see if array_map() is more effective here than the solution I found.
	 * @param mixed $args - This function accepts as many arguments as you throw at it. It will hard-typecast all arguments into arrays and go from there. 
	 * @return mixed
	 **/
	public function zip( $args ) {

		$val = (array)$this->val;
		$data = array();
		$return = array();
		$max = 0;
		foreach( func_get_args() as $k=>$v ) {
			$data[] = (array)$v;
		}
		$data[] = $val;
		foreach( $data as $k=>$v ) {
			if( count( $v ) > $max ) {
				$max = count( $v );
			}
		}

		for( $i = 0; $i < $max; $i++ ) {
			foreach( $data as $k=>$v ) {
				if( isset( $data[$k][$i]) ) {
					$return[$i][] = $data[$k][$i];
				} else {
					$return[$i][] = null;
				}
			} 
		}

		return $this->chain( $return );

	}

	/**
	 * Takes a string and reverses the case of each of the letters. All capitals become lowercase and vice-versa
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn swapcase()
	 * @return mixed
	 **/
	public function swapcase() {

		if( $this->type != "string" ) {
			return $this->val;
		}
		$return = array();
		foreach( $this->chars as $k=>$v) {
			if( ctype_upper( $v )) {
				$return[] = strtolower( $v );
			} else if( ctype_lower( $v )) {
				$return[] = strtoupper( $v );
			}
			return $this->chain( implode( $return ) );
		}

	}

	/**
	 * Appends the elements of a provided array to the r object.
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn concat()
	 * @param mixed $item - The item that you would like appended to the object value array. Preferred: Array 
	 * @param string $position - The position in the array you would like the value appended. Accepts "start" "fin" "begin" and "end"
	 * @return mixed
	 **/
	public function concat( $item , $position = "end" ) {

		$val = (array)$item;
		if( !is_array( $this->val ) && !is_array( $this->chars )) {
			return $this->val;
		}
		switch( $position ) {
			case "start":
			case "begin":
				if( is_array( $this->val )) {
					$return = array_unshift( $this->val , $val );
				} else if( is_array( $this->chars )) {
					$return = implode( array_unshift( $this->chars , $val ));
				}
				break;
			case "fin":
			case "end":
				if( is_array( $this->val )) {
					$return = array_push( $this->val , $val);
				} else if( is_array( $this->chars )) {
					$return = implode( array_push( $this->chars , $val ) );
				}
				break;
			default:
				return false;
		}
		return $this->chain( $return );

	}

	/**
	 * Prepends a value to the front of an array or a string
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn prepend()
	 * @param mixed $item - The item to be prepended to the existing array
	 * @return mixed
	 **/
	public function prepend( $item ) {

		if( !is_array( $this->val ) && !is_array( $this->chars )) {
			return $this->val;
		}
		if( is_array( $this->val )) {
			return $this->chain( array_unshift( $this->val , $item ) );
		} else if( is_array( $this->chars )) {
			return $this->chain( imploade( array_unshift( $this->chars, $item)) );
		} else {
			return $this->val;
		}

	}

	/**
	 * Adjusts the entire case of a string to lower
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn downcase()
	 * @return mixed
	 **/
	public function downcase() {

		if( $this->type == "string" ) {
			return $this->chain( strtolower( $this->val ) );
		} else {
			return $this->val;
		}

	}

	/**
	 * Returns the decimal value for the object value
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn hex()
	 * @return mixed
	 **/
	public function hex() {

		if( $this->type == "string" ) {
			return $this->chain( hexdec( $this->val ) );
		} else {
			return $this->val;
		}

	}

	/**
	 * Returns the hex value for the object value
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn toHex()
	 * @return mixed
	 **/
	public function toHex() {

		if( $this->type == "string" ) {
			return $this->chain( dechex( $this->val ) );
		} else {
			return false;
		}

	}

	/**
	 * Matches a regex against the object value and returns the occurrences
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn match()
	 * @param string $pattern - The pattern that the regex will follow.
	 * @return mixed
	 **/
	public function match( $pattern ) {

		if( $this->type == "string" ) {
			if( preg_match( $pattern , $this->val , $matches ) ) {
				return $this->chain( $matches );
			}
		}
		return false;

	}

	/**
	 * Runs a function against each of the characters in the object string ($this->chars)
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn each_char()
	 * @param string $function - The user-supplied function that will be run against the characters
	 * @return mixed
	 **/
	public function each_char( $function ) {

		if( is_array( $this->chars ) ) {
			foreach( $this->chars as $k => $v ) {
				$output[] = $function( $v );
			}
			return $this->chain( $output );
		}
		return $this->val;

	}

	/**
	 * Runs a function against each of the characters in the object string or the elements in the object array.
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn each()
	 * @param string $function - The user-supplied function that will be run against the characters or elements
	 * @return mixed
	 **/
	public function each( $function ) {

		if( $this->type == "string" ) {
			foreach( $this->chars as $k => $v ) {
				$output[] = $function( $v );
			}
		} else if ( $this->type == "array" ) {
			foreach( $this->val as $k => $v ) {
				$output[] = $function( $v );
			}
		} else if ( is_array( $this->chars )) {
			foreach( $this->chars as $k => $v ) {
				$output[] = $function( $v );
			}
		} else {
			return $this->val;
		}
		return $this->chain( $output );

	}

	/**
	 * Reverses the elements of an array or the characters in a string/double/integer/etc. This is an alias of flip()!
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn reverse()
	 * @return mixed
	 **/
	public function reverse() {

		return $this->chain( $this->flip() );

	}

	/**
	 * Determines whether a provided value is equal to the object value or not
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn eql()
	 * @param mixed $comparison - The value to be compared to.
	 * @return boolean
	 **/
	public function eql( $comparison ) {

		if( !isset( $comparison )) {
			return $this->val;
		}
		if( $this->type == "string" ) {
			return ( strcmp( $this->val , $comparison ) == 0 );
		} else {
			return $this->val == $comparison;
		}

	}

	/**
	 * Trims all whitespace from the beginning and end of the object value. 
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn tr()
	 * @return mixed
	 **/
	public function tr() {

		if( $this->type == "string" ) {
			return $this->chain( trim( $this->val ) );
		} else {
			return $this->val;
		}

	}

	/**
	 * Removes a specified key from an array.
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn del()
	 * @param mixed $key - The key to remove from the array
	 * @return mixed
	 **/
	public function del( $key ) {

		if( $this->type == "string" ) {
			if( $key > count( $this->chars )) {
				return $this->val;
			}
			unset( $this->chars[$key] );
			$return = $this->chars;
		} else if( $this->type == "array" ) {
			if( $key > count( $this->val )) {
				return $this->val;
			}
			unset( $this->val[$key] );
			$return = $this->val;
		} else {
			$return = $this->val;
		}

		return $this->chain( $return );

	}

	/**
	 * Breaks apart a string of values into separate array pieces by a provided delimiter
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn ex()
	 * @param string $delimiter - The character separator used to break the string apart
	 * @return mixed
	 **/
	public function ex( $delimiter ) {

		if( !isset($delimiter)) {
			return $this->val;
		}
		return $this->chain( explode( $delimiter , $this->val ) );

	}

	/**
	 * Flattens an array using a provided delimiter
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn im()
	 * @param string $delimiter - The character separator to include between characters.
	 * @return mixed
	 **/
	public function im( $delimiter ) {

		if( !isset($delimiter)) {
			return $this->val;
		}
		if( is_array( $this->val )) {
			return $this->chain( implode( $delimiter , $this->val ));
		} else if( is_array( $this->chars ) ) {
			return $this->chain( implode( $delimiter , $this->chars ));
		}

	}

	/**
	 * Repeats a string, number or array $num of times.
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn repeat()
	 * @param int $num - Number of times to be repeated
	 * @return mixed
	 **/
	public function repeat( $num ) {
		if( $num <= 0 || is_nan( $num )) {
			return $this->val;
		}
		if( $this->type == "array" ) {
			for( $i = 0; $i < $num; $i++ ) {
				$return[$i] = $this->val;
			}
		} else if( is_array( $this->chars ) ) {
			$return = str_repeat( $this->val , $num );
		} else {
			$return = $this->val;
		}
		return $return;

	}

	/**
	 * Shuffles an array
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn cnt()
	 * @param int $mode - The count mode: null ) Normal, top-level count. 1) Recursive count. Counts all levels. <DEFAULT
	 * @return mixed
	 **/
	public function cnt( $mode = 1 ) {

		if( $this->type == "string" ) {
			$this->count = count( $this->chars , $mode );
		} else if( $this->type == "array" ) {
			$this->count = count( $this->val , $mode );
		} else {
			return $this->val;
		}
		if( $this->chaining ){
			return $this;
		} else {
			return $this->count;
		}

	}

	/**
	 * Adds/Removes slashes based on input
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn slashes()
	 * @param mixed $mode - The method of slashes to use. DEFAULT: "add", ACCEPTS: "add","addc","strip","stripc"
	 * @return mixed
	 **/
	public function slashes( $mode = "add" ) {

		try {
			switch( $mode ) {
				case "add":
				case "addc":
				case "strip":
				case "stripc":
					$f = $mode . "slashes";
					break;
				default:
					return $this->val;
			}

			if( $this->type == "string" || is_array( @$this->chars) ) {
				return $this->chain( $f( $this->val ) );
			} else if( $this->type == "array" ) {
				return $this->chain( $this->arraySlash( $this->val , $f ) );
			}
			throw new exception("Invalid data type sent to slashes(). Only strings and arrays are accepted.");
		} catch( Exception $e ) {
			$this->exception( $e , $e->getMessage() );
		}

	}

	/**
	 * Adds/Removes slashes in an array. This is a recursive function, and will secure all levels of an n-dimensional array.
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn slashes()
	 * @param array $array - The array to be recursed through.
	 * @param mixed $mode - This is the function to use. Passed to the function initially by slashes(). 
	 * @return mixed
	 **/
	public function arraySlash( $array , $mode ) {

		foreach( $array as $k=>$v ){
			if( is_array( $v )) {
				$output[] = $this->arraySlash( $v , $mode );
			} else {
				$output[$k] = $mode( $v );
			}
		}
		return $this->chain( $output );

	}

	/**
	 * Searches for a key or item in a string, integer, double, or array.
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn pos()
	 * @param mixed $needle - The search term.
	 * @param boolean $recursive - Whether or not to recurse into the value array
	 * @return mixed
	 **/
	public function pos( $needle , $recursive = true ) {

		if( !isset( $needle )) {
			return $this->val;
		}
		$search = function( $key , $haystack ) {
			if( is_array( $haystack )) {
				return array_search( $key , $haystack );
			} else {
				return strpos( $haystack , $key );
			}
		};

		if( $this->type == "string" || is_array( @$this->chars) ) {
			return $search( $needle , @$this->chars );
		} else if( $this->type == "array" ) {
			return array_walk_recursive( $this->val , $search );
		}
		return $this->val;

	}

	/**
	 * Multipurpose replace function. Regex for strings and numbers, and a key->val replace for arrays.
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn replace()
	 * @param string $item - The pattern or key to match against (regex)
	 * @param mixed $replacer - The item that will replace the found key
	 * @param boolean $recursive - Flag whether or not the function will recurse into the provided array
	 * @return mixed
	 **/
	public function replace( $item , $replacer , $recursive = true ) {
		try {
			if( !isset( $item ) || !isset( $replacer ) ) {
				throw new exception("One or more arguments missing for replace(). Please try again.");
			}
			if( $this->type == "array" ) {
				if( in_array( $item , $this->val )) {
					$key = array_search( $item , $this->val );
					$this->val[$key] = $replacer;
				} else {
					if( $recursive ) {
						return $this->chain( $this->arrayReplace( $this->val , $item , $replacer ) );
					} else {
						return $this->val;
					}
				}	
			} else if( $this->type == "string"  || is_array( $this->chars )) {
				return $this->chain( preg_replace( $item , $replacer , $this->val ) );
			} else {
				throw new exception("Invalid data type for replace(). Please try again.");
			}
		} catch( Exception $e ){
			$this->exception( $e , $e->getMessage() );
		}

	}

	/**
	 * Recursive array replacement function.
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn arrayReplace()
	 * @param array $array - The Haystack to search in
	 * @param string $item - The pattern or key to match against (regex)
	 * @param mixed $replacer - The item that will replace the found key
	 * @return mixed
	 **/
	public function arrayReplace( $array , $item , $replacer ) {
		foreach( func_get_args() as $k=>$v ) {
			if( empty($v) ) {
				return $this->val;
			}
		}
		if( !is_array( $array )) {
			return $this->val;
		}
		// Test if $item is a regex
		$regex = strpos( $item , "/");
		foreach( $array as $k=>$v ){
			if( is_array( $v )) {
				$output[$k] = $this->arrayReplace( $v , $item , $replacer );
			} else {
				if( strpos( $v , $item ) || ( strcmp( $v , $item ) == 0 )) {
					$output[$k] = str_replace( $item , (string)$replacer, $v );
				} else if( $regex ) {
					$output[$k] = preg_replace( $item , (string)$replacer, $v );
				} else {
					$output[$k] = $v;
				}
			}
		}
		return $output;
	}

	/**
	 * Alias to succ() function in Ruby. Finds successor to any element provided. 
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn flatten()
	 * @param mixed $val - The value of the flattened keypair
	 * @param mixed $key - The key of the flattened keypair
	 * @return array
	 **/
	public function flatten( $val = null , $key = null ) {
		if( isset($val) && isset($key)) {
			$this->output[$key] = $val;
		} else {
			if( $this->type == "array" ) {
				$this->output = array();
				array_walk_recursive( $this->val , array( $this , "flatten"));
				return $this->output;
			}	
		}
	}

	/**
	 * 
	 * WARNING: This function is not completed. Don't use it. Or your stuff will break, guaranteed. I'll finish this later.
	 * Alias to succ() function in Ruby. Finds successor to any element provided. 
	 * 
	 * @package RubyPHP
	 * @author Pierce Moore
	 * @fn succ()
	 * @param mixed $item - The item that will be incremented by one
	 * @return mixed
	 **/
/*	public function succ( $item = null ) {
		( $item == null ) ? $val = $this->val : $val = $item;
		if( $this->type == "string" || is_array( $this->chars ) ) {
			$high = $this->length - 1;
			for( $i = $high ; $i >= 0; $i-- ) {
				$curr = $this->chars[$i];
				if( preg_match( "/^[a-zA-Z0-9]/" , $curr)) {
					if( preg_match( "/[0-9]/" , $curr )) {
						// Number, process accordingly
					} elseif( preg_match( "/[a-zA-Z]/", $curr )) {
						// Letter, do your thing.
						$curr++;
						$this->chars[$i] = $curr;
						return implode(	$this->chars );
					}
				}
			}
		} elseif( $this->type == "array" ) {
			foreach( $val as $k=>$v ) {
				if( is_array( $v ) ) {
					$output[$k] = $this-succ( $v );
				} else {
					$output[$k] = "";
				}
			}
		}

		return $output;

	}
*/

}

?>