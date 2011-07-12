<?php
namespace html\forms;


use handlers\Debug;
use handlers\HttpRequest;


/**
 * a mother class for all of the form fields
 * @author michel
 *
 */
abstract class FormElement {
	const STRING = 1;
	const INT = 2;
	const FLOAT = 3;
	const BOOL = 4;
	/**
	 * the type of the element
	 * @var int
	 */
	protected $type;
	/**
	 * the element name
	 * @var string
	 */
	protected $name;
	/**
	 * the value of the element
	 * @var mixed
	 */
	protected $value;


	/**
	 *
	 * @param int $type
	 */
	public function __construct($type){
	    $this->type = $type;
	}


	/**
	 * this fonction have to return a string representing the element in html format
	 */
	public abstract function __toString();


	/**
	 * return the value of a field, regarding its type
	 * @return mixed
	 */
	public function getValue(){
		switch($this->type){
			case self::STRING:
			    $this->value = HttpRequest::getInstance()->getString($this->name);
			    break;
			case self::INT:
			    $this->value = HttpRequest::getInstance()->getInt($this->name);
			    break;
			case self::FLOAT:
			    $this->value = HttpRequest::getInstance()->getFloat($this->name);
			    break;
			case self::BOOL:
			    $this->value = HttpRequest::getInstance()->getBool($this->name);
			    break;
			default:
			    throw new FormException('Unhandled parameter type');
		}
		return $this->value;
	}


	/**
	 * name getter
	 * @return string
	 */
	public function getName(){
		return $this->name;
	}

}

