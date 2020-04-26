<?php
namespace TJM\Cache;
use Exception;

class Cacheable{
	protected $cacheable = true;
	protected $calculator;
	protected $key;
	protected $path;
	protected $validator = true;
	public function __construct($set){
		if(is_array($set)){
			$this->set($set);
		}else{
			$this->key = $set;
		}
	}
	public function set($set, $value = null){
		if(is_array($set)){
			foreach($set as $key=> $value){
				$this->set($key, $value);
			}
		}else{
			$this->{"set" . ucfirst($set)}($value);
		}
	}
	public function getCacheable(){
		return $this->cacheable;
	}
	public function isCacheable(){
		return (bool) $this->getCacheable();
	}
	public function setCacheable($value){
		$this->cacheable = $value;
	}
	public function calculate(){
		if(is_callable($this->calculator)){
			return call_user_func($this->calculator, $this);
		}else{
			throw new Exception("Cannot calculate TJM\Cache\Cacheable value: $this->calculator isn't callable");
		}
	}
	public function setCalculator($value = null){
		$this->calculator = $value;
	}
	public function getKey(){
		return $this->key;
	}
	public function getPath(){
		return $this->path;
	}
	public function setPath($value = null){
		$this->path = $value;
	}
	public function isValid(){
		$path = $this->getPath();
		if(!($path && file_exists($path))){
			return false;
		}elseif(is_numeric($this->getValidator())){
			return (time() - filemtime($path) < $this->getValidator());
		}elseif(is_callable($this->getValidator())){
			return call_user_func($this->getValidator(), $this);
		}else{
			return $this->getValidator();
		}
	}
	public function getValidator(){
		return $this->validator;
	}
	public function setValidator($value = null){
		$this->validator = $value;
	}
}
