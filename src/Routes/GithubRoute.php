<?php

namespace Avxman\Github\Routes;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 *
 * Работа с маршрутами
 *
 */
class GithubRoute extends Route
{

    /**
     * Общие параметры ссылки
     * @var string $uri
     */
    protected static $uri = '/{version}/{secret}/';

    /**
     * Префикс для API
     * @var string $prefix_api
     */
    protected static $prefix_api = 'api/github';

    /**
     * Префикс для Web
     * @var string $prefix_wep
     */
    protected static $prefix_wep = 'web/github';

    /**
     * Префикс для as метода
     * @var string $as_name
     */
    protected static $as_name = 'github.';

    /**
     * Автозапуск вебхука для API
     * @param array $config
     * @return bool
     */
    protected static function autoloadFromWebhook(array $config) : bool{
        return (bool)$config['GITHUB_AUTO_WEBHOOK'];
    }

    /**
     * Установка максимального количества запроса на адрес (защита от досс-атак)
     * @param array $config
     * @return void
     */
    protected static function configureRateLimiting(array $config) : void
    {

    }

    /**
     * Маршруты для репозитория
     * @param array $config
     * @return void
     */
    protected static function repositoryRoutes(array $config) : void{
        $uri = self::$uri.'{repository}';
        $wheres = [
            'version'=>$config['GITHUB_API_VERSION'],
            'secret'=>$config['GITHUB_TOKEN'],
            'repository'=>Str::finish($config['GITHUB_REPO_USER'], Str::finish('@', $config['GITHUB_REPO_NAME'])),
        ];

        self::group(['prefix'=>self::$prefix_wep, 'as'=>self::$as_name.'web.'], function() use ($wheres, $uri){
            self::group(['where'=>$wheres], function () use ($uri){
                self::any($uri, 'Avxman\Github\Controllers\Web\RepositoryGithubWebController@index')
                    ->name('repository');
            });
        });

        if(self::autoloadFromWebhook($config)) {
            self::group(['prefix' => self::$prefix_api, 'as' => self::$as_name . 'api.'], function () use ($wheres, $uri) {
                self::group(['where' => $wheres], function () use ($uri) {
                    self::any($uri, 'Avxman\Github\Controllers\Api\RepositoryGithubApiController@index')
                        ->name('repository');
                });
            });
        }
        else{
            self::group(['prefix' => self::$prefix_api, 'as' => self::$as_name . 'api.'], function () use ($wheres, $uri) {
                self::group(['where' => $wheres], function () use ($uri) {
                    self::any($uri, 'Avxman\Github\Controllers\Api\FallbackGithubApiController@index')
                        ->name('repository');
                });
            });
        }

    }

    /**
     * Маршруты для Авторизации пользователя и Регистрации репозитория
     * @param array $config
     * @return void
     */
    protected static function registrationRoutes(array $config) : void{
        $uri = self::$uri.'registration';
        $wheres = [
            'version'=>$config['GITHUB_API_VERSION'],
            'secret'=>$config['GITHUB_TOKEN'],
        ];
        self::group(['prefix'=>self::$prefix_wep, 'as'=>self::$as_name.'web.'], function() use ($wheres, $uri){
            self::group(['where'=>$wheres], function () use ($uri){
                self::any($uri, 'Avxman\Github\Controllers\Web\RegistrationGithubWebController@index')
                    ->name('registration');
            });
        });
    }

    /**
     * Маршруты для БД
     * @param array $config
     * @return void
     */
    protected static function databaseRoutes(array $config) : void{
        $uri = self::$uri.'database';
        $wheres = [
            'version'=>$config['GITHUB_API_VERSION'],
            'secret'=>$config['GITHUB_TOKEN'],
        ];
        self::group(['prefix'=>self::$prefix_wep, 'as'=>self::$as_name.'web.'], function() use ($wheres, $uri){
            self::group(['where'=>$wheres], function () use ($uri){
                self::any($uri, 'Avxman\Github\Controllers\Web\DatabaseGithubWebController@index')
                    ->name('database');
            });
        });
    }

    /**
     * Маршруты для остальных запросов
     * @param array $config
     * @return void
     */
    protected static function fallbackRoutes(array $config) : void{
        self::group(['prefix'=>self::$prefix_wep, 'as'=>self::$as_name.'web.'], function(){
            self::any('/{page?}/{page2?}', 'Avxman\Github\Controllers\Web\FallbackGithubWebController@index')
                ->name('notFound');
        });
        self::group(['prefix'=>self::$prefix_api, 'as'=>self::$as_name.'api.'], function(){
            self::any('/{page?}/{page2?}', 'Avxman\Github\Controllers\Api\FallbackGithubApiController@index')
                ->name('notFound');
        });
    }

    /**
     * Вызов всех маршрутов
     * @param array $config
     * @return void
     */
    public static function allRoutes(array $config) : void{
        self::configureRateLimiting($config);
        self::registrationRoutes($config);
        self::databaseRoutes($config);
        self::repositoryRoutes($config);

        // Данный метод должен всегда находится в конце данного метода
        self::fallbackRoutes($config);
    }

}
