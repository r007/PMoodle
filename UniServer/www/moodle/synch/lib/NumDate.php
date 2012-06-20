<?php
 /*
 *
 * @copyright &copy; 2007 The Open University
 * @author c.chambers@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package synch
 * 
 * This is a simple class providing standard date methods
 */
 
class NumDate{

	// ----------------------------------------------
	// Properties
	// ----------------------------------------------
	private $year;
	private $month;
	private $day;
	private $hour;
	private $minute;
	private $second;
	protected $microtime;

	static $DATE_FULL = 127;


	function __construct($date = null) {

		if(empty($date)){
			$date = date("YmdHis");
			$date.=$this->getCurrentMicrotime();

			GLOBAL $Out;
		//	$Out->append("\$date = $date");
		//	$Out->print_r($microtime, "\$microtime = ");

		}

		$this->setDate($date);

	}

	// ----------------------------------------------
	// Method bodies
	// ----------------------------------------------
	function parseDateInt($dateInt) {

		$dateInt = (string)$dateInt;

		if($dateInt{0}=="0"
				&& strlen($dateInt)!=1){
			$dateInt = $dateInt{1};
		}

		if(isNaN(parseInt($dateInt))){
			return "0";
		}

		if(strlen($dateInt)==1){
			$dateInt = "0".$dateInt;
		}

		return $dateInt;

	}

	function parseDateItem($value, $min, $max) {

		if(empty($value)){
			for($i=0;$i<$min;$i++){
				$value.="0";
			}
		}

		$value = $this->parseDateInt($value);

		if(isNaN($value)
				|| strlen($value) < $min
				|| strlen($value) > $max){
			// There has been an error
			return "0";
		}

		return $value;

	}

	// Getters and Setters
	// ----------------------------------------------
	/*
	 * @param $granularity Bitwise value representing the values to append from
	 * year, month, day, hour, minute, second, microtime.
	 * @deprecated param $full replaced by $granularity
	 */
	function getDate($granularity = 0) {

		$date = "";
		if($granularity == 1) {
			//$date .= $this->hour.$this->minute.$this->second;
			$granularity =63;
		}

		if(!$granularity){
			$granularity = 7;
		}

		if($granularity & 1){
			$date.= $this->getYear();
		}

		if($granularity & 2){
			$date.= $this->getMonth();
		}

		if($granularity & 4){
			$date.= $this->getDay();
		}

		if($granularity & 8){
			$date.= $this->getHour();
		}

		if($granularity & 16){
			$date.= $this->getMinute();
		}

		if($granularity & 32){
			$date.= $this->getSecond();
		}

		if($granularity & 64){
			$date.= $this->getMicrotime();
		}

		return $date;
	}

	function setDate($date) {

		$date = (string)$date;

		$this->setYear(substr($date,0,4));
		$this->setMonth(substr($date,4,2));
		$this->setDay(substr($date, 6,2));
		$this->setHour(substr($date,8,2));
		$this->setMinute(substr($date,10,2));
		$this->setSecond(substr($date,12,2));
		$this->setMicrotime(substr($date,14,8));

	}

	function getYear() {
		return $this->year;
	}

	function setYear($year) {
		$this->year = $this->parseDateItem($year, 4, 4);
	}

	function getMonth() {
		return $this->month;
	}

	function setMonth($month) {
		$this->month = $this->parseDateItem($month, 1, 2);
	}

	function getDay() {
		return $this->day;
	}

	function setDay($day) {
		$this->day = $this->parseDateItem($day, 1, 2);
	}

	function getHour() {
		return $this->hour;
	}

	function setHour($hour) {
		$this->hour = $this->parseDateItem($hour, 1, 2);
	}

	function getMinute() {
		return $this->minute;
	}

	function setMinute($minute) {
		$this->minute = $this->ParseDateItem($minute, 1, 2);
	}

	function getSecond() {
		return $this->second;
	}

	function setSecond($second) {
		$this->second = $this->parseDateItem($second, 1, 2);
	}

	function getMicrotime() {
		return $this->microtime;
	}

	function setMicrotime($new) {
		$this->microtime = $this->parseDateItem($new, 1, 8);
	}

	function getHumanDate($date, $full) {

		if(!isset($date)){
			$date = $this->getDate();
		}

		$date = (string)$date;
		$year = substr($date,0,4);
		$month = substr( $date, 4,2);
		$day = substr($date, 6,2);
		$humanDate = $day."/".$month."/".$year;

		if($full) {
			$hours = substr($date, 8,2);
			$minutes = substr($date, 10,2);
			$seconds = substr($date, 12,2);
			$humanDate .= " ".$hours.":".$minutes.":".$seconds;
		}
		return $humanDate;
	}

	function getMonthName() {

		switch($this->month) {

			case "01":
				return "January";
			break;

			case "02":
				return "Febuary";
			break;

			case "03":
				return "March";
			break;

			case "04":
				return "April";
			break;

			case "05":
				return "May";
			break;

			case "06":
				return "June";
			break;

			case "07":
				return "July";
			break;

			case "08":
				return "August";
			break;

			case "09":
				return "September";
			break;

			case "10":
				return "October";
			break;

			case "11":
				return "November";
			break;

			case "12":
				return "December";
			break;

		}
	}

	function getCurrentMicrotime(){
			$microtime = explode(" ", microtime());
			$microtime = explode(".", $microtime[0]);
			return $microtime[1];
	}
}
?>
