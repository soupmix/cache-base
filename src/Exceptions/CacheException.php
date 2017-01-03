<?php

namespace Soupmix\Cache\Exceptions;

use Psr\SimpleCache\CacheException as PsrCacheException;

final class CacheException extends \RuntimeException implements PsrCacheException
{

}