<?php

namespace Avxman\Github\Controllers\Web;

use Avxman\Github\Controllers\GithubWebController;
use Illuminate\Support\Facades\Response;

/**
 *
 * Контроллер по работе со всеми запросами
 * не объявленных в маршрутах через Web (Сайт)
 *
*/
class FallbackGithubWebController extends GithubWebController
{

    /**
     * *Обработка всех маршрутов
     * @return  \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(){
        return view('github.index', ['result'=>collect()]);
    }

}
