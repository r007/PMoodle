<?php
class Out{

	private $stream;
	private $display; // Turn on if debugging: Deprecated in lieu of enabled;
	private $globalDisplay; // If set to false will not allow debugging;
	protected $recording = true;
	protected $enabled = true;

	function __constructor(){

		$stream = "";
		$display = false; // Turn on if debugging;
		$globalDisplay = false; // If set to false will not allow debugging;
	}

	function append($text = "", $level=0, $flush=false){
	//	echo "text = ".$text;
		if(!$this->isEnabled() || !$this->isRecording()){
			return false;
		}
		$level++;

		$text = $this->getBacktraceAsString($level).": ".$text;

		$this->stream .= $text."<br />\n";

        if($flush){
        	$this->flush();
        }
		return true;
	}

	function isEnabled(){
		return $this->enabled;
	}
	function enable($level = 0){
		$this->enabled = true;
		$level++;
		if($this->getDisplay()){
			echo "\n<!-- Output enabled - Called by ".$this->getBacktraceAsString($level)." -->\n";
		}
	}

	function disable($level = 0){
		$this->enabled = false;
		$level++;
		if($this->getDisplay()){
			echo "\n<!-- Output disabled - Called by ".$this->getBacktraceAsString($level)." -->\n";
		}
	}

	function isRecording(){
		return $this->recording;
	}

	function start($level = 0){
		$level++;
		$this->record($level);
	}
	function record($level = 0){
		$this->recording = true;
		$level++;
		if($this->getDisplay()){
			echo "\n<!-- begin recording Output - Called by ".$this->getBacktraceAsString($level)." -->\n";
		}
	}

	function stop($level = 0){
		$this->recording = false;
		$level++;
		if($this->getDisplay()){
			echo "\n<!-- stopped recording Output - Called by ".$this->getBacktraceAsString($level)." -->\n";
		}
	}

	function getBacktraceAsString($level){
		//$level++;
		$backtrace = GetBacktraceFromStack($level);
		$backtrace2 = GetBacktraceFromStack($level+1);
		return $backtrace["file"].": ".$backtrace2["function"]." (".$backtrace["line"].")";
	}

	function getGlobalDisplay(){
		return $this->globalDisplay;
	}

	function setGlobalDisplay($newGlobalDisplay){
		$this->globalDisplay = $newGlobalDisplay;

	}

	function getDisplay(){
		return $this->display;
	}

	function setDisplay($newDisplay){
		$this->display = $newDisplay;
	}

	function clean(){
		$this->stream="";
	}

	function flush($flushAsp = false, $level = 0){
		$level++;
		if($this->getDisplay()){
			echo "\n<!-- Flushing Output - Called by ".$this->getBacktraceAsString($level)." -->\n";
			echo $this->stream;
		}
		$this->clean();
		if($flushAsp){
		//	Response.Flush();
		}
	}


	// object o, String text, boolean full
	function iterate($o, $text = "", $full = false, $level = 0){
		$level++;

		if($o){
			$o = (object) $o;
			$this->append("Properties in " . $text.":", $level);
			//$this->append("Out.iterate: o.toString() " . $o->toString());
			$property = "";
			foreach($o as $name => $value){
				$property = $name;
				if($full){
				//$this->append("Out.iterate: gettype(\$value) = ".gettype($value));
				//$this->flush();
					if(gettype($value)=="object"){
						$property.=": ".get_class($value);
					}
					else {
						$property.=": ".$value;
					}
				}
				$this->append($property, $level);
			}
		}
		else {
			$this->append($text." is empty.", $level);
		}
;
	}

	// array a, String text
	function loop($a, $text = "", $level = 0){
		$level++;
		if($a){
			$this->append("Properties in " . $text, $level);
			$index = "";
			for($x=0;$x<count($a);$x++){
				$index = $x .": ".$a[$x];
				$this->append($index, $level);
			}
		}
		else {
			$this->append($text." is empty.", $level);
		}
	}

	// array a, String text
	function showDataArray($a, $text = "", $level = 0){
		$level++;
		if($a){
			$this->append("Out.showDataArray: Properties in " . $text, $level);
			$index = "";
			for($x=0;$x<count($a);$x++){
				$index = $x .": ".$a[$x];
				$this->append($index, $level);
				//$this->append("Out.showDataArray: Before iterate", $level);
				$this->iterate($a[$x], $x, true, $level);
				//append("Out.showDataArray: After iterate", $level);
			}
		}
		else {
			$this->append("Out.showDataArray: ".$text." is empty.", $level);
		}
	}

	function boolean($var, $text="", $level=0){
		$level++;

		if(gettype($var)=="NULL"){
			$this->append("Out.boolean: ".$text." is null.", $level);
			return;
		}

		if(gettype($var)!="boolean"){
			$this->append("Out.boolean: ".$text." is $var.", $level);
			return;
		}

		if($var){
			$this->append("Out.boolean: ".$text." is true.", $level);
			return;
		}
		$this->append("Out.boolean: ".$text." is false.", $level);
			return;
	}

	function backtrace($traceLevel=0, $text="The calling backtrace is ", $level=0){
		$traceLevel++;
		$level++;
		//$this->append($text.": ".$this->getBacktraceAsString($traceLevel), $level);
		$this->append($text.": ", $level);
		$trace = '';
		for($i=$level;$i<$traceLevel;$i++){
			$trace.="Level {$i}: ".$this->getBacktraceAsString($i)."<br />";
		}
		$this->append($trace, $level);
	}

	function type($var, $text="", $level=0){
		$level++;
		$this->append($text." is of type: ".gettype($var), $level);
	}

	function print_r($var, $text="", $level=0, $flush=false){
		$level++;
		
		if(empty($var)){
			$this->append("$text empty", $level, $flush);
			return;
		}
		$this->append("$text<pre>".print_r($var, true)."</pre>", $level, $flush);
	}

	function var_dump($var, $text="", $level=0){
		$level++;
		ob_start();
		var_dump($var, true);
		$this->append("$text<pre>".ob_get_contents()."</pre>", $level);
		ob_clean();
	}

	function var_export($var, $text="", $level=0){
		$level++;
		$this->append("$text<pre>".var_export($var, true)."</pre>", $level);
	}

}

?>