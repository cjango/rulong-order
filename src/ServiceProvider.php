<?php

namespace RuLong\Order;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{

    /**
     * 部署时加载
     * @Author:<C.Jason>
     * @Date:2018-06-22T16:01:20+0800
     * @return [type] [description]
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../config/rulong_order.php' => config_path('rulong_order.php')]);
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/');
        }
    }

    /**
     * 注册服务提供者
     * @Author:<C.Jason>
     * @Date:2018-06-22T16:01:12+0800
     * @return [type] [description]
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/rulong_order.php', 'rulong_order');
    }

}
