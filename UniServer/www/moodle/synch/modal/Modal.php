<?PHP
/**
 * Base class for all models
 *
 * $Id: Model.php,v 1.6 2006/08/29 07:11:40 amir Exp $
 *
 * The base class provides __set() and __get()
 * as well as some other helper methods.
 *
 * @package Services_Ebay
 * @author  Stephan Schmidt <schst@php.net>
 *
 * @todo    different caches for different detail levels
 * @todo    add the possibility to disable the cache for single models
 */
class Synch_Modal implements ArrayAccess
{
   /**
    * model type
    *
    * @var  string
    */
    protected $type = null;
    
   /**
    * properties of the model
    *
    * @var  array
    */
    protected $properties = array();

   /**
    * property that stores the unique identifier (=pk) of the model
    *
    * @var string
    */
    protected $primaryKey = null;

 
    /**
    * create new model
    *
    * @param    array   properties
    */
    public function __construct($props, $DetailLevel = 0)
    {
        if (is_array($props)) {
            $this->properties = $props;
        } elseif ($this->primaryKey !== null) {
            $this->properties[$this->primaryKey] = $props;
            
        }
    }
    
   /**
    * get a property
    *
    * @param    string   property name
    * @return   mixed    property value
    */
    public function __get($prop)
    {
        if (isset($this->properties[$prop])) {
            return $this->properties[$prop];
        }
    }
    
   /**
    * set a property
    *
    * @param    string   property name
    * @param    mixed    property value
    */
    public function __set($prop, $value)
    {
        $this->properties[$prop] = $value;
    }
    
   /**
    * return all properties of the user
    *
    * @return   array
    */
    public function toArray()
    {
        return $this->properties;
    }
    
    /**
    * return all properties of the user
    *
    * @return   object
    */
    public function toObject()
    {
        return (object) $this->properties;
    }
    
    /**
    * replace all properties with the object passed in.
    *
    * @return   object
    */
    public function fromObject($object)
    {
        return $this->properties = (array) $object;
    }
    
   /**
	* check, whether a property exists
	*
	* This is needed to implement the ArrayAccess interface
	*
	* @param	string	property
	*/
	public function offsetExists($offset)
	{
	    if (isset($this->properties[$offset])) {
	    	return true;
	    }
	    return false;
	}

   /**
	* get a property
	*
	* This is needed to implement the ArrayAccess interface
	*
	* @param	string	property
	*/
	public function offsetGet($offset)
	{
		return $this->properties[$offset];
	}

   /**
	* set a property
	*
	* This is needed to implement the ArrayAccess interface
	*
	* @param	string	property
	* @param	mixed	value
	*/
	public function offsetSet($offset, $value)
	{
		$this->properties[$offset] = $value;
	}

   /**
	* unset a property
	*
	* This is needed to implement the ArrayAccess interface
	*
	* @param	string	property
	*/
	public function offsetUnset($offset)
	{
		unset($this->properties[$offset]);
	}

   /**
    * get the primary key of the model
    *
    * @return   string
    */
	public function getPrimaryKey()
	{
		if ($this->primaryKey === null) {
			return false;
		}
		if (!isset($this->properties[$this->primaryKey])) {
			return false;
		}
		return $this->properties[$this->primaryKey];
	}
}
?>
