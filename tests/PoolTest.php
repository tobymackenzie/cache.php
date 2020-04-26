<?php
namespace TJM\Cache\Tests;
use PHPUnit\Framework\TestCase;
use TJM\Cache\Pool;

class PoolTest extends TestCase{
	public function testItemAdded(){
		$cache = new Pool();
		$cache->get('item-added', function(){
			return 'testItemAdded';
		});
		$this->assertTrue($cache->has('item-added'));
	}
	public function testItemNotAdded(){
		$cache = new Pool();
		$this->assertFalse($cache->has('item-not-added'));
	}
	public function testCacheableFalse(){
		$cache = new Pool();
		$return = $cache->get('cacheable-false', function($item){
			$item->setCacheable(false);
			return 'testCacheableFalse';
		});
		$this->assertFalse($cache->has('cacheable-false'));
		$this->assertEquals('testCacheableFalse', $return);
	}
	public function testGetValueIsSame(){
		$cache = new Pool();
		$result = $cache->get('value-is-same', function(){
			return 'testGetValueIsSame';
		});
		$this->assertSame('testGetValueIsSame', $result);
	}
	public function testGetKeyOnly(){
		$cache = new Pool();
		$cache->get('get-key-only', function(){
			return 'testGetKeyOnly';
		});
		$result = $cache->get('get-key-only');
		$this->assertSame('testGetKeyOnly', $result);
	}
	public function set(){
		$cache = new Pool();
		$cache->set('set', 'set value');
		$result = $cache->get('set');
		$this->assertSame('set value', $result);
	}
	public function testBoolTrueValidator(){
		$cache = new Pool();
		$cache->get('bool-true-validator', function(){
			return 'testBoolTrueValidator';
		});
		$result = $cache->get('bool-true-validator', function(){
			return 'not';
		});
		$this->assertSame('testBoolTrueValidator', $result);
	}
	public function testBoolFalseValidator(){
		$cache = new Pool();
		$cache->get('value-stays-same', function(){
			return 'testBoolFalseValidator';
		});
		$result = $cache->get('value-stays-same', function(){
			return 'not';
		});
		$this->assertSame('testBoolFalseValidator', $result);
	}
	public function testTimeValidatorValid(){
		$cache = new Pool();
		$cache->get('time-validator-valid', function(){
			return 'testTimeValidatorValid';
		}, 100);
		$result = $cache->get('time-validator-valid', function(){
			return 'not';
		}, 100);
		$this->assertSame('testTimeValidatorValid', $result);
	}
	public function testTimeValidatorInvalid(){
		$cache = new Pool();
		$cache->get('time-validator-invalid', function(){
			return 'testTimeValidatorInvalid';
		}, 1);
		usleep(1001);
		$result = $cache->get('time-validator-invalid', function(){
			return 'not';
		}, 1);
		$this->assertSame('testTimeValidatorInvalid', $result);
	}
	public function testCallableValidatorTrue(){
		$cache = new Pool();
		$cache->get('callable-validator-true', function(){
			return 'testCallableValidatorTrue';
		});
		$result = $cache->get('callable-validator-true', function(){
			return 'not';
		}, function(){
			return true;
		});
		$this->assertSame('testCallableValidatorTrue', $result);
	}
	public function testCallableValidatorFalse(){
		$cache = new Pool();
		$cache->get('callable-validator-false', function(){
			return 'testCallableValidatorFalse';
		});
		$result = $cache->get('callable-validator-false', function(){
			return 'not';
		}, function(){
			return true;
		});
		$this->assertSame('testCallableValidatorFalse', $result);
	}
	public function testDelete(){
		$cache = new Pool();
		$cache->get('delete', function(){
			return 'testDelete';
		});
		$this->assertTrue($cache->has('delete'));
		$cache->delete('delete');
		$this->assertFalse($cache->has('delete'));
	}
	public function testClear(){
		$cache = new Pool();
		$cache->get('clear', function(){
			return 'testClear';
		});
		$this->assertTrue($cache->has('clear'));
		$cache->clear();
		$this->assertFalse($cache->has('clear'));
	}
}
