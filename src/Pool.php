<?php
namespace TJM\Cache;
use Exception;

class Pool{
	protected $path = '/tmp/tjm-cache';
	public function __construct($path = null){
		if($path){
			$this->setPath($path);
		}
	}
	public function clear(){
		if($this->getPath() && $this->getPath() !== '/' && is_dir($this->getPath())){
			@shell_exec("find {$this->getPath()} -type f -name '*.dat' -delete");
		}else{
			error_log("TJM\Cache\Pool::clear: cannot clear path {$this->getPath()}");
		}
	}
	public function delete($item){
		$path = $this->getItemPath($item);
		if(file_exists($path)){
			return unlink($path);
		}
	}
	public function get($item, $calculator = null, $validator = null){
		//--create item for simplified interface
		if(!($item instanceof Cacheable)){
			$item = new Cacheable($item);
		}
		if(isset($calculator)){
			$item->setCalculator($calculator);
		}
		if(isset($validator)){
			$item->setValidator($validator);
		}

		//--if if valid, read cached file
		$this->getItemPath($item);
		if($item->isValid()){
			try{
				return $this->read($item);
			}catch(Exception $e){}
		}

		//--if not, calculate value
		$value = $item->calculate();

		//--cache if cacheable
		if($item->isCacheable()){
			$this->set($item->getKey(), $value);
		}

		return $value;
	}
	public function has($item){
		return file_exists($this->getItemPath($item));
	}
	protected function read($item){
		return $this->unserialize(file_get_contents($this->getItemPath($item)));
	}
	protected function serialize($value){
		return serialize($value);
	}
	public function set($item, $value){
		return $this->write($item, $value);
	}
	protected function write($item, $value){
		return file_put_contents($this->getItemPath($item), $this->serialize($value));
	}
	protected function unserialize($value){
		return unserialize($value);
	}
	protected function getItemPath($item){
		if($item instanceof Cacheable){
			if(!$item->getPath()){
				$item->setPath($this->getItemPath($item->getKey()));
			}
			return $item->getPath();
		}else{
			return $this->getPath() . '/' . $item . '.dat';
		}
	}
	public function getPath(){
		if(!$this->path){
			return null;
		}
		if(!file_exists($this->path)){
			//-! 2 bit doesn't seem to work, but here in case it does
			mkdir($this->path, 02775, true);
		}
		return $this->path;
	}
	public function setPath($value){
		$this->path = $value;
	}
}
