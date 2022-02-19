<?php

namespace Avxman\Github\Controllers\Api;

use Avxman\Github\Facades\GithubApiFacade;
use Avxman\Github\Controllers\GithubApiController;
use Illuminate\Support\Facades\Response;

/**
 *
 * Контроллер по работе c репозиториями Гитхаба через API
 *
*/
class RepositoryGithubApiController extends GithubApiController
{

    /**
     * *Обработка всех маршрутов
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){
        GithubApiFacade::instance();
        return Response::json(['status'=>true, 'message'=>['Команда обработана :)']]);
    }

}
