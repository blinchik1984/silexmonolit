<?php

echo "<pre>";
require_once __DIR__.'/vendor/autoload.php';

$app = new Silex\Application();

$locator = new \Symfony\Component\Config\FileLocator(__DIR__);
$loader = new \Symfony\Component\Routing\Loader\YamlFileLoader($locator);


$app['routes']->addCollection($loader->load('config/routes.yml'));
$targetRoutes = $loader->load('services/content/config/routes.yml');
$targetRoutes->addPrefix('/content');
$app['routes']->addCollection($targetRoutes);

$contentRoutes = $loader->load('services/target/config/routes.yml');
$contentRoutes->addPrefix('/target');
$app['routes']->addCollection($contentRoutes);



$file = __DIR__ .'/var/cache/container.php';

if (file_exists($file)) {
    require_once $file;
    $container = new ProjectServiceContainer();
} else {

    $container = new \Symfony\Component\DependencyInjection\ContainerBuilder();
    $containerLoader = new \Symfony\Component\DependencyInjection\Loader\YamlFileLoader($container, $locator);
    $containerLoader->load('services/content/config/services.yml');
    $containerLoader->load('services/target/config/services.yml');
    $containerLoader->load('config/services.yml');
    $container->compile();

    $dumper = new \Symfony\Component\DependencyInjection\Dumper\PhpDumper($container);
    file_put_contents($file, $dumper->dump());
}


$app['di'] = $container;

try {
    \AdServer\Engine\Components\Engine::run($app);
}catch (\Throwable $e) {
    print_r($e);
}
