<?php
// 引入 Eloquent ORM 模块
use Illuminate\Database\Capsule\Manager;

//引入视图模块
use Illuminate\Support\Fluent;

//调用自动加载文件，添加自动加载文件函数
require __DIR__.'/../vendor/autoload.php';

//实例化服务器容器，注册事件、路由服务器提供者
$app = new Illuminate\Container\Container;
Illuminate\Container\Container::setInstance($app);
with(new Illuminate\Events\EventServiceProvider($app))->register();
with(new Illuminate\Routing\RoutingServiceProvider($app))->register();

//启动 Eloquent ORM 模块并进行相关配置
$manager = new Manager();
$manager->addConnection(require '../config/database.php');
$manager->bootEloquent();

$app->instance('config', new Fluent);
$app['config']['view.compiled'] = __DIR__.'/../storage/framework/views';//视图编译文件路径
$app['config']['view.paths'] = [__DIR__.'/../resources/views'];//视图文件储存路径
with(new Illuminate\View\ViewServiceProvider($app))->register();
with(new Illuminate\Filesystem\FilesystemServiceProvider($app))->register();


//加载路由
require __DIR__.'/../app/Http/routes.php';

//实例化请求并分发处理请求
$request = Illuminate\Http\Request::createFromGlobals();
$response = $app['router']->dispatch($request);

//返回请求响应
$response->send();