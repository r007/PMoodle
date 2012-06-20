<?php
/*
 * Created on 21 May 2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 	
function GetBacktraceFromStack($level=0){

		$level++;
		$backtrace = debug_backtrace();
		if(count($backtrace)<=$level){
			return null;
		}
		return $backtrace[$level];

}

function utility_Factory_createObject($name, $args){

	/*
	 * the code below does not work for some reason.
	 * $signature = 'utilities_database_Field()';
	 * return new $signature;
	 * Therefore the current implementation is convoluted but works!
	 *
	 */
	if(!$name) {
		return null;
	}
	$signature = $name;


	// If no arguments are passed
	if(!$args){
		try{
			return new $signature;
		}
		catch(Exception $e){
			throw($e);
		}
	}

	// If arguments are use the eval and __constructor methods
	$object = new $signature;
	$signature ="__construct(";

	for($x=0;$x<count($args);$x++){

		if($x>0){
			$signature.=",";
		}

		if(gettype($args[$x])=="object"){

			$signature.= "\$args[".$x."]";
		}
		else {
			$signature.= '"'.$args[$x].'"';
		}

	}
	$signature .= ");";

	try{
		eval('$object->'.$signature);
		return $object;
	}
	catch(Exception $e){
		throw($e);
	}
}

    function isNumber($var){
        return ereg("^[0-9]+$", $var);
    }

function filename($file=null){
 	// Expects file object from __FILE__
 	if(empty($file)){
 		return "";
 	}
 	$info = pathinfo($file);
 	if(!empty($info["basename"])){
 		return $info["basename"];
 	}
 	
 	return $info["filename"].".".$info["extension"];
 }
 
 // Function to emulate JScript
function isNaN($var) {
     return !ereg ("^[-]?[0-9]+([\.][0-9]+)?$", $var);
}

function parseInt($var) {
     return intval($var);
}

/**
 * Print a single submit button.
 *
 * @param string $name 
 * @param string $value 
 * @param string $id 
 * @param bool $return Whether the method should return its output or print it
 */
function print_submit_button($name, $value, $id, $return=false) {
    $output = '';
    $output .= '<div class="singlebutton">';

    $output .= '<input name="'.$name.'" type="submit" value="'. s($value) .'" />';
    $output .= '</div>';
    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}
?>
