## Soupmix Cache API

Soupmix Cache provides framework agnostic implementation of [PSR-16 Simple Cache Interface](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-16-simple-cache.md). 

### 1. Install and Connect to Service

It's recommended that you use [Composer](https://getcomposer.org/) to install Soupmix Cache Adaptors.


#### 1.1 Redis

##### Source Code

See [Github Repository](https://github.com/soupmix/cache-redis)

##### Installation
```bash
$ composer require soupmix/cache-redis "~0.3"
```

##### Connect to Redis (single instance) service 

```php
require_once '/path/to/composer/vendor/autoload.php';

$rConfig = ['host'=> "127.0.0.1"];
$handler = new Redis();
$handler->connect(
    $rConfig['host']
);

$cache = new Soupmix\Cache\RedisCache($handler);
```


#### 1.2 Memcached

##### Source Code

See [Github Repository](https://github.com/soupmix/cache-memcached)

##### Installation
```bash
$ composer require soupmix/cache-memcached "~0.3"
```
##### Connect to Memcached service

```php
require_once '/path/to/composer/vendor/autoload.php';

$config = [
    'bucket' => 'test',
    'hosts'   => ['127.0.0.1'],
;
$handler = new Memcached($config['bucket']);
$handler->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
$handler->setOption(Memcached::OPT_BINARY_PROTOCOL, true);
if (!count($handler->getServerList())) {
    $hosts = [];
    foreach ($config['hosts'] as $host) {
        $hosts[] = [$host, 11211];
    }
    $handler->addServers($hosts);
}

$cache = new Soupmix\Cache\MemcachedCache($config);
```


#### 1.3 APCu

##### Source Code

See [Github Repository](https://github.com/soupmix/cache-apcu)

##### Installation
```bash
$ composer require soupmix/cache-apcu "~0.2"
```
##### Usage

```php
require_once '/path/to/composer/vendor/autoload.php';

$cache = new Soupmix\Cache\APCUCache();
```

### 2. Persist data in the cache, uniquely referenced by a key with an optional expiration TTL time.

```php
$cache->set($key, $value, $ttl);
```

**@param string $key**: The key of the item to store

**@param mixed $value**: The value of the item to store

**@param null|integer|DateInterval $ttl**: Optional. The TTL value of this item. If no value is sent and the driver supports TTL then the library may set a default value for it or let the driver take care of that. Predefined DataIntervals: TTL_MINUTE, TTL_HOUR, TTL_DAY.

@return bool True on success and false on failure


```php
$cache->set('my_key, 'my_value', TTL_DAY);

// returns bool(true)
```

### 3. Determine whether an item is present in the cache.

```php
$cache->has($key);
```

**@param string $key**: The unique cache key of the item to delete

@return bool True on success and false on failure

```php
$cache->has('my_key');

// returns bool(true)
```

### 4. Fetch a value from the cache.

```php
$cache->get($key, default=null);
```

**@param string $key**: The unique key of this item in the cache
@return mixed The value of the item from the cache, or null in case of cache miss

```php
$cache->get('my_key');

// returns  string(8) "my_value"
```

### 5. Delete an item from the cache by its unique key

```php
$cache->delete($key);
```

**@param string $key**: The unique cache key of the item to delete

@return bool True on success and false on failure

```php
$cache->delete('my_key');

// returns bool(true)
```

### 6. Persisting a set of key => value pairs in the cache, with an optional TTL.

```php
$cache->setMultiple(array $items);
```

**@param array|Traversable $items**: An array of key => value pairs for a multiple-set operation.

**@param null|integer|DateInterval $ttl**: Optional. The amount of seconds from the current time that the item will exist in the cache for. If this is null then the cache backend will fall back to its own default behaviour.

@return bool True on success and false on failure

```php
$items = ['my_key_1'=>'my_value_1', 'my_key_2'=>'my_value_2'];
$cache->setMultiple($items);

// returns bool(true)
```

### 7. Obtain multiple cache items by their unique keys.

```php
$cache->getMultiple($keys, $default=null);
```

**@param array|Traversable $keys**: A list of keys that can obtained in a single operation.

@return array An array of key => value pairs. Cache keys that do not exist or are stale will have a value of null.

```php
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

### 8. Delete multiple cache items in a single operation.

```php
$cache->deleteMultiple($keys);
```

**@param array|Traversable $keys**: The array of string-based keys to be deleted

@return bool True on success and false on failure

```php
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

### 9. Increment a value atomically in the cache by its step value, which defaults to 1.
```php
$cache->increment($key, $step);
```
**@param string  $key**: The cache item key

**@param integer $step**: The value to increment by, defaulting to 1

@return int|bool The new value on success and false on failure

```php
$cache->increment('counter', 1);
// returns int(1)
$cache->increment('counter', 1);
// returns int(2)
```

#### Important Note:

Memcached does not increments the keys that's not been set before. For Memcached you must set key with the default value.

```php
$cache->set('counter', 0);
// returns bool(true)
$cache->increment('counter', 1);
// returns int(1)
```

    
### 10. Decrement a value atomically in the cache by its step value, which defaults to 1

```php
$cache->decrement($key, $step);
```

**@param string  $key**:  The cache item key

**@param integer $step**: The value to decrement by, defaulting to 1
```php
$cache->decrement('counter', 1);
// returns int(1)
$cache->decrement('counter', 1);
// returns int(0)
```

#### Important Note 1:

Memcached does not decrements the keys that's not been set before. For Memcached you must set key with the default value.

```php
$cache->set('counter', 1);
// returns bool(true)
$cache->decrement('counter', 1);
// returns int(0)
```

#### Important Note 2:

Memcached does not decrements to negative values and stops at zero where Redis can decrement to negative values and goes setting -1,-2, etc...
    
### 11. Wipe clean the entire cache's keys (Flush)

@return bool True on success and false on failure

```php
$cache->clear();
// returns bool(true)
```