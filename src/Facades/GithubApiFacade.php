<?php

namespace Avxman\Github\Facades;

use Avxman\Github\Instances\GithubApiInstance;
use Illuminate\Support\Facades\Facade;

/**
 * Работа через API
 *
 * @method static void instance()
 * @method static array getResult()
 * @method static bool isGithub()
 *
 * @see GithubApiInstance
 */
class GithubApiFacade extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return GithubApiInstance::class;
    }

}
