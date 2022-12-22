<?php

namespace Avxman\Github\Controllers\Web;

use Avxman\Github\Controllers\GithubWebController;
use Avxman\Github\Facades\GithubWebFacade;
use Avxman\Github\Requests\AdminPayloadRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 *
 * Контроллер по работе c репозиториями Гитхаба через Web (Сайт)
 *
 */
class AdminGithubWebController extends GithubWebController
{

    /**
     * *Обработка всех маршрутов
     * @return  \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(){
        GithubWebFacade::adminInstance();
        $result = collect(GithubWebFacade::getResult());
        return view('github.admin.pages.home', ['result'=>collect($result)]);
    }

    public function payload(AdminPayloadRequest $request) : JsonResponse
    {

        $view = 'github.admin.commands.'.$request->get('group').'.'.$request->get('view');

        return response()->json(['status'=>true, 'content'=>view()->exists($view)
            ? view($view, ['action'=>$request->get('link')])->render()
            : '']);

    }

}
