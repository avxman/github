<?php

namespace Avxman\Github\Facades;

use Avxman\Github\Instances\GithubWebInstance;
use Illuminate\Support\Facades\Facade;

/**
 * Работа через Web
 *
 * @method static void instance()
 * @method static void registrationInstance()
 * @method static void databaseInstance()
 * @method static array getResult()
 * @method static bool isGithub()
 *
 * @see GithubWebInstance
 */
class GithubWebFacade extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return GithubWebInstance::class;
    }

}
