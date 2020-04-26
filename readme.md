PHP Cache
=========

A simple filesystem cache implementation for storing PHP data.  Note that this is a very early implementation and is almost definitely going to change significantly.  I want to make it psr-6 / psr-16 compatible, but still allow the calculator / validator functionality if desired.

Use
----

### `TJM\Cache\Pool` class


#### `__construct($path = '/tmp/tjm-cache')`

#### `get(string $key, callable $calculator = null, int|bool|callable $validator = true)`

- $key: string reference of item, used to store
- $calculator: callable, returns cache value. run only if cache is invalidated, then stored in cache
- $validator: validate cache.
	- false: always invalid
	- true: invalid if already in cache
	- int: number of seconds since creation to consider stale
	- callable: return boolean of whether item is valid

Returns cached or calculated value.

### example

``` php
$cache = new TJM\Cache\Pool();
$items = $cache->get('items', function() use($db){
	return $db->getItems();
}, 1200);
```

This will get items from `$db` on first run.  If next run is less than 1200 seconds later, it will then get them from the cache.  Once 1200 seconds have elapsed, the next run will get the from `$db` again.
