<?php

namespace Avxman\Github\Providers;

use Avxman\Github\Instances\GithubApiInstance;
use Avxman\Github\Instances\GithubWebInstance;
use Avxman\Github\Routes\GithubRoute;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;

/**
 *
 * Провайдер для работы с Гитхаб
 *
*/
class GithubServiceProvider extends ServiceProvider
{

    /**
     * Запуск библиотеки
     * @param Filesystem $filesystem
    */
    public function boot(Filesystem $filesystem){
        if(App()->runningInConsole()){
            $this->publishes($this->getFilesNameAll($filesystem, 0, true), 'avxman-github-all');
            $this->publishes($this->getFilesNameAll($filesystem), 'avxman-github-config');
            $this->publishes($this->getFilesNameAll($filesystem, 1), 'avxman-github-storage');
        }
        else{
            $config = Config()->get('github');
            if($config['GITHUB_ENABLED']) {
                GithubRoute::allRoutes($config);
            }
        }
    }

    /**
     * Регистрация библиотеки
     * @param array $server
     * @param array $config
     * @return void
    */
    public function register(array $server = [], array $config = []) : void
    {
        $this->app->singleton(GithubApiInstance::class, GithubApiInstance::class);
        $this->app->singleton(GithubWebInstance::class, GithubWebInstance::class);
        config()->push('view.paths', dirname(__DIR__).'/Views');
    }

    /**
     * Create specified files in folders
     * @param Filesystem $filesystem
     * @param int $index
     * @param bool $all
     * @return array
     */
    protected function getFilesNameAll(Filesystem $filesystem, int $index = 0, bool $all = false) : array{
        $collect = collect()
            ->push([dirname(__DIR__, 2).'/config/' => base_path('config').DIRECTORY_SEPARATOR])
            ->push([dirname(__DIR__, 2).'/storage/' => storage_path().DIRECTORY_SEPARATOR]);
        return $all
            ? collect()->merge($collect->get(0))->merge($collect->get(1))->toArray()
            : $collect->get($index);
    }

}
