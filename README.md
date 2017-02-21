# Magento 2 Lock
## Description
A Magento 2 module which provides a multi-server lock service. 
This allows developers to apply mutual exclusion locks to shared resources so that they are 
not accessed simultaneously. 

##Prerequisites
* PHP 7.0 or newer.
* `magento/framework` module 100 or newer.
* Composer  (https://getcomposer.org/download/).

## Installation
```
composer require snowio/magento2-lock
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento cache:flush
```


## Usage
The lock service can be accessed through dependency injection. Please refer the [dependency injection](http://devdocs.magento.com/guides/v2.0/extension-dev-guide/depend-inj.html) section of the 
[Magento DevDocs](http://devdocs.magento.com) for more information on how to use dependency injection.

### `public boolean LockService::acquireLock(string $name, int $timeout)`
Attempt to obtain a lock
####Parameters
* `$lockName` : The lock identifier/name
* `$timeout` : Lock timeout. A negative timeout implies an infinite timeout.


####Return Values
A boolean indicating if the the lock was acquired.


### `public LockService::releaseLock($lockName)`
Release the lock 
####Parameters
* `$lockName` : The lock identifier/name


###Example
```php
namespace Vendor\Module\Model\Accessors;
class ResourceAccessor
{
    private $lockService;
    
    public function __construct(
        SnowIO\Lock\Api\LockService $lockService
    ) {
        $this->lockService = $lockService    
    }
    
    public function access($resource)
    {
        $lockName = //.. resource lock name
        
        //try acquire the lock
        if (!$this->lockService->acquireLock($lockName, 0)) {
            //Lock was not acquired ...
        }
        //Lock was acquired 
        
        try {
            // Process $resource 
        } finally {
            //release the lock
            $this->lockService->releaseLock($lockName);
        }
        
    }
}
```

##Applications
* [snowio/magento2-product-save-mutex](https://github.com/snowio/magento2-product-save-mutex) : Uses this module in order to make product save API calls mutually exclusive.
* [snowio/magento2-idempotent-api](https://github.com/snowio/magento2-idempotent-api) : Uses this module in order to determine request conflicts whereby 2 or more requests that have the same `X-Message-Group-ID` are dispatched.


## License
This software is licensed under the MIT License. [View the license](LICENSE)
