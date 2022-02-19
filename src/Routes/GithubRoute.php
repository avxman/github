<?php

namespace Avxman\Github\Routes;

use Avxman\Github\Controllers\Api\FallbackGithubApiController;
use Avxman\Github\Controllers\Api\RepositoryGithubApiController;
use Avxman\Github\Controllers\Web\DatabaseGithubWebController;
use Avxman\Github\Controllers\Web\FallbackGithubWebController;
use Avxman\Github\Controllers\Web\RegistrationGithubWebController;
use Avxman\Github\Controllers\Web\RepositoryGithubWebController;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

/**
 *
 * Работа с маршрутами
 *
*/
class GithubRoute extends Route
{

    /**
     * Общяя параметры ссылки
     * @var string $uri
    */
    protected static string $uri = '/{version}/{secret}/';

    /**
     * Префикс для API
     * @var string $prefix_api
    */
    protected static string $prefix_api = 'api/github';

    /**
     * Префикс для Web
     * @var string $prefix_wep
    */
    protected static string $prefix_wep = 'web/github';

    /**
     * Префикс для as метода
     * @var string $as_name
    */
    protected static string $as_name = 'github.';

    /**
     * Автозапуск вебхука для API
     * @param array $config
     * @return bool
    */
    protected static function autoloadFromWebhook(array $config) : bool{
        return (bool)$config['GITHUB_AUTO_WEBHOOK'];
    }

    /**
     * Установка максимального количества запроса на адрес (защита от перенагрузки)
     * @param array $config
     * @return bool
     */
    protected static function configureRateLimiting(array $config) : void
    {
        RateLimiter::for('api', function (Request $request) use ($config) {
            return Limit::perMinute($config['GITHUB_MAX_RATE'])->by(optional($request->user())->id ?: $request->ip());
        });
    }

    /**
     * Маршруты для репозитория
     * @param array $config
     * @return bool
     */
    protected static function repositoryRoutes(array $config) : void{
        $uri = self::$uri.'{repository}';
        $wheres = [
            'version'=>$config['GITHUB_API_VERSION'],
            'secret'=>$config['GITHUB_TOKEN'],
            'repository'=>Str::finish($config['GITHUB_REPO_USER'], Str::start($config['GITHUB_REPO_NAME'], '@')),
        ];
        self::prefix(self::$prefix_wep)->middleware('api')->as(self::$as_name.'web.')->group(function () use ($config, $wheres, $uri){
            self::any($uri, [RepositoryGithubWebController::class, 'index'])
                ->name('repository')
                ->setWheres($wheres);
        });
        if(self::autoloadFromWebhook($config)) {
            self::prefix(self::$prefix_api)->middleware('api')->as(self::$as_name.'api.')->group(function () use ($config, $wheres, $uri) {
                self::any($uri, [RepositoryGithubApiController::class, 'index'])
                    ->name('repository')
                    ->setWheres($wheres);
            });
        }
        else{
            self::prefix(self::$prefix_api)->middleware('api')->as(self::$as_name.'api.')->group(function () use ($config, $wheres, $uri) {
                self::any($uri, [FallbackGithubApiController::class, 'index'])
                    ->name('repository')
                    ->setWheres($wheres);
            });
        }
    }

    /**
     * Маршруты для Авторизации пользователя и Регистрации репозитория
     * @param array $config
     * @return bool
     */
    protected static function registrationRoutes(array $config) : void{
        $uri = self::$uri.'registration';
        $wheres = [
            'version'=>$config['GITHUB_API_VERSION'],
            'secret'=>$config['GITHUB_TOKEN'],
        ];
        self::prefix(self::$prefix_wep)->middleware('api')->as(self::$as_name.'web.')->group(function () use ($config, $wheres, $uri){
            self::any($uri, [RegistrationGithubWebController::class, 'index'])
                ->name('registration')
                ->setWheres($wheres);
        });
    }

    /**
     * Маршруты для БД
     * @param array $config
     * @return bool
     */
    protected static function databaseRoutes(array $config) : void{
        $uri = self::$uri.'database';
        $wheres = [
            'version'=>$config['GITHUB_API_VERSION'],
            'secret'=>$config['GITHUB_TOKEN'],
        ];
        self::prefix(self::$prefix_wep)->middleware('api')->as(self::$as_name.'web.')->group(function () use ($config, $wheres, $uri){
            self::any($uri, [DatabaseGithubWebController::class, 'index'])
                ->name('database')
                ->setWheres($wheres);
        });
    }

    /**
     * Маршруты для остальных запросов
     * @param array $config
     * @return bool
     */
    protected static function fallbackRoutes(array $config) : void{
        self::prefix(self::$prefix_wep)->middleware('api')->as(self::$as_name.'web.')->group(function (){
            self::fallback([FallbackGithubWebController::class, 'index'])
                ->name('notFound');
        });
        self::prefix(self::$prefix_api)->middleware('api')->as(self::$as_name.'api.')->group(function (){
            self::fallback([FallbackGithubApiController::class, 'index'])
                ->name('notFound');
        });
    }

    /**
     * Вызов всех маршрутов
     * @param array $config
     * @return bool
     */
    public static function allRoutes(array $config) : void{
        self::configureRateLimiting($config);
        self::registrationRoutes($config);
        self::databaseRoutes($config);
        self::repositoryRoutes($config);

        // Данный метод должен всегда находится вконце данного метода
        self::fallbackRoutes($config);
    }

}
