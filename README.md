# JTL-Shop5 Router Lib

> Simple Router Lib, based on JTL-Shop5

## Installation

> Using composer:
 ```shell
 $ composer require jtl/shop5-router
 ```

## Usage

> My Crud Service 'product.service.js'
```php
use JTL\Plugin\Helper as PluginHelper;

$plugin = null;
$pluginId = PluginHelper::getIDByPluginID('your_plugin');
if ($pluginId !== null) {
    $loader = pluginHelper::getLoaderByPluginID($pluginId);
    if ($loader !== null) {
        $plugin = $loader->init($pluginId);
    }
}

$router = new Router('Plugin\your_plugin\Controller', Shop()::getInstance(), $plugin);

try {
    echo $router->send();
} catch (Exception $e) {
    header('Internal Server Error', true, 500);
    echo $e->getMessage();
}
```

## License

Copyright (c) 2020-present JTL-Software

[MIT License](http://en.wikipedia.org/wiki/MIT_License)
