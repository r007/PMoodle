<?php
/*
 *
 * @copyright &copy; 2007 The Open University
 * @author c.chambers@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package synch
 * 
 * A class providing basic methods for a modal object. Extended by subsequent
 * classes for specific needs.
 */
class bean_synch_BaseClass{

	public $data;

	function __construct(){
		$this->data = new Object();
		$this->data->id =  0;
	}

	/*
	 * Getter and Setter Pairs
	 */
	// Data
	function getData() {
		return $this->data;
	}

	function setData($newData) {
		$this->data = $newData;
	}
	
	// Synchronise fields which haven't been updated
	function updateData($newData){

			$this->updateBaseData($newData);
			$this->setId($this->getId());
		//}
	}

	function getPopulatedData() {
		return $this->getPopulatedBaseData();
	}

	/**
	 * Updates properties found in oCompanyData
	 * with those found in oData
	 */
	function updateBaseData($newData) {

		// Synchronise fields which haven't been updated
		//Copy the values from newData to this->data

		// If newData is an array
		if(gettype($newData) == "array"){
			foreach($this->data as $key => $value){
				if(gettype($newData[$key]) != "NULL"){
							$this->data->$key = $newData[$key];
				}
			}
		}

		//if the data is an object
		if(gettype($newData) == "object"){
			foreach($this->data as $key => $value){
				if(isset($newData->$key)){
					$this->data->$key = $newData->$key;
				}
			}
		}
		
		

		// Synchronise fields which haven't been updated

	//	$Out->iterate($this->data, "$this->data ", true);
	}

	/**
	 * Gets an object with the properties found in
	 * oCompanyData that are not null->
	 * (prevents overwriting of $this->database fields)
	 */
	function getPopulatedBaseData() {
		$populatedData = new Object();

		foreach($this->data as $key => $value){
			if($this->data->$key != null){
					$populatedData->$key = $this->data->$key;
			}
		}

		return $populatedData;
	}

	function output($text){
		$Out->append("bean_synch_BaseClass.output: Outputting this.data");
		$fields = $this->getData();
		foreach($fields as $key => $value){
			$Out->append($key." = " . $value);
		}

	}

	// id
	function getId() {
		return $this->data->id;
	}

	function setId($newId) {
		$this->data->id = $newId;
	}

	function __clone(){
		$this->data = clone($this->data);
	}
}

?>