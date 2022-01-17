# Install

Add umpy.example.config.php to config/ as umpy.php

## Extend Commands
In Laravel config/app.php add line to 'providers'
```php
'providers' => [
    
    //...other laravel providers
    \Infira\Umpy\Providers\ConsoleCommandsServiceProvider::class
],
```
