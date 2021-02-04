# JTL-Shop5 Router Lib

> Simple Router Lib, based on JTL-Shop5

## Installation

> Using composer:
 ```shell
 $ composer require jtl/shop5-router-lib
 ```

## Usage

> api.php
```php
use JTL\Plugin\Helper as PluginHelper;
use Symfony\Component\HttpFoundation\Request;

$plugin = null;
$pluginId = PluginHelper::getIDByPluginID('your_plugin');
if ($pluginId !== null) {
    $loader = pluginHelper::getLoaderByPluginID($pluginId);
    if ($loader !== null) {
        $plugin = $loader->init($pluginId);
    }
}

$router = new Router('Plugin\your_plugin\Controller', Shop()::getInstance(), $plugin);

// After events (before works also)
$router->after(static function (Request $request, array $arguments, $result) {
    // Nasty after logic
});

try {
    echo $router->send();
} catch (Exception $e) {
    header('Internal Server Error', true, 500);
    echo $e->getMessage();
}
```

> FooController.php
```php
<?php declare(strict_types=1);

namespace Plugin\your_plugin\Controller;

use Plugin\your_plugin\Models\Foobar;
use Plugin\your_plugin\Services\FooService;
use Jtl\Shop5Router\Http\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class FooController extends AbstractController
{
    /**
     * Can be accessed via Http GET api.php?action=foo.bar&id=3
     * Other Http Methods are also available
     * 
     * @param Request $request
     * @param string $id
     * @return Foobar|null
    */
    public function bar(Request $request, string $id): ?Foobar
    {
        $service = new FooService();
    
        return $service->find($id);
    }
}
```

## License

Copyright (c) 2020-present JTL-Software

[MIT License](http://en.wikipedia.org/wiki/MIT_License)
