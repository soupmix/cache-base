## Soupmix Cache API

Soupmix Cache provides framework agnostic cache interface. 

### 1. Connect to server

#### 1.1 Redis

##### Connect to Redis (single instance) service 

```
$rConfig = [];
$rConfig['host'] = "127.0.0.1";
$rConfig['dbIndex'] = 1;
$cache = new Soupmix\Cache\RedisCache($rConfig);
```



#### 1.2 Memcached

##### Connect to Memcached service

```
$config = [];
$config['bucket'] = 'test';
$config['hosts'] = ['127.0.0.1'];
$cache = new Soupmix\Cache\MemcachedCache($config);
```


### 2. Persist data in the cache, uniquely referenced by a key with an optional expiration TTL time.

```
$cache->set($key, $value, $ttl);
```

**@param string $key**: The key of the item to store

**@param mixed $value**: The value of the item to store

**@param null|integer|DateInterval $ttl**: Optional. The TTL value of this item. If no value is sent and the driver supports TTL then the library may set a default value for it or let the driver take care of that. Predefined DataIntervals: TTL_MINUTE, TTL_HOUR, TTL_DAY.

@return bool True on success and false on failure


```
$cache->set('my_key, 'my_value', TTL_DAY);

// returns bool(true)
```

### 3. Fetch a value from the cache.

```
$cache->get($key);
```

**@param string $key**: The unique key of this item in the cache
@return mixed The value of the item from the cache, or null in case of cache miss

```
$cache->get('my_key);

// returns  string(8) "my_value"
```

### 4. Delete an item from the cache by its unique key

```
$cache->delete($key);
```

**@param string $key**: The unique cache key of the item to delete

@return bool True on success and false on failure

```
$cache->delete('my_key);

// returns bool(true)
```

### 5. Persisting a set of key => value pairs in the cache, with an optional TTL.

```
$cache->setMultiple(array $items);
```

**@param array|Traversable $items**: An array of key => value pairs for a multiple-set operation.

**@param null|integer|DateInterval $ttl**: Optional. The amount of seconds from the current time that the item will exist in the cache for. If this is null then the cache backend will fall back to its own default behaviour.

@return bool True on success and false on failure
```
$items = ['my_key_1'=>'my_value_1', 'my_key_2'=>'my_value_2'];
$cache->setMultiple($items);

// returns bool(true)
```

### 6. Obtain multiple cache items by their unique keys.

```
$cache->getMultiple($keys);
```

**@param array|Traversable $keys**: A list of keys that can obtained in a single operation.

@return array An array of key => value pairs. Cache keys that do not exist or are stale will have a value of null.

```
$keys = ['my_key_1', 'my_key_2'];
$cache->getMultiple($keys);
/*
returns array(2) {
          ["my_key_1"]=>
          string(3) "my_value_1"
          ["my_key_2"]=>
          string(3) "my_value_2"
        }
*/
```

### 7. Delete multiple cache items in a single operation.

```
$cache->deleteMultiple($keys);
```

**@param array|Traversable $keys**: The array of string-based keys to be deleted

@return bool True on success and false on failure

```
$keys = ['my_key_1', 'my_key_2'];
$cache->deleteMultiple($keys);
 /*
 returns array(2) {
           ["my_key_1"]=>
            bool(true)
           ["my_key_2"]=>
            bool(true)
         }
 */
```

### 8. Increment a value atomically in the cache by its step value, which defaults to 1.
```
$cache->increment($key, $step);
```
**@param string  $key**: The cache item key

**@param integer $step**: The value to increment by, defaulting to 1

@return int|bool The new value on success and false on failure

```
$cache->increment('counter', 1);
// returns int(1)
$cache->increment('counter', 1);
// returns int(2)
```

#### Important Note:

Memcached does not increments the keys that's not been set before. For Memcached you must set key with the default value.

```
$cache->set('counter', 0);
// returns bool(true)
$cache->increment('counter', 1);
// returns int(1)
```

    
### 9. Decrement a value atomically in the cache by its step value, which defaults to 1

```
$cache->decrement($key, $step);
```

**@param string  $key**:  The cache item key

**@param integer $step**: The value to decrement by, defaulting to 1
```
$cache->decrement('counter', 1);
// returns int(1)
$cache->decrement('counter', 1);
// returns int(0)
```

#### Important Note 1:

Memcached does not decrements the keys that's not been set before. For Memcached you must set key with the default value.

```
$cache->set('counter', 1);
// returns bool(true)
$cache->decrement('counter', 1);
// returns int(0)
```

#### Important Note 2:

Memcached does not decrements to negative values and stops at zero where Redis can decrement to negative values and goes setting -1,-2, etc...
    
### 10. Wipe clean the entire cache's keys (Flush)

@return bool True on success and false on failure

```
$cache->clear();
returns bool(true)
```