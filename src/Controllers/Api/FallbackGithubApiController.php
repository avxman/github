<?php

namespace Avxman\Github\Controllers\Api;

use Avxman\Github\Facades\GithubApiFacade;
use Avxman\Github\Controllers\GithubApiController;
use Illuminate\Support\Facades\Response;

/**
 *
 * Контроллер по работе со всеми запросами
 * не обявленных в машрутах через API
 *
*/
class FallbackGithubApiController extends GithubApiController
{

    /**
     * *Обработка всех маршрутов
     * @return \Illuminate\Http\JsonResponse
    */
    public function index(){
        return Response::json(['status'=>false, 'message'=>['Результат не найден или работа API выключена в настройках']]);
    }

}
