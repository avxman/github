<?php

namespace Avxman\Github\Controllers\Web;

use Avxman\Github\Controllers\GithubWebController;

/**
 *
 * Контроллер по работе со всеми запросами
 * не обявленных в машрутах через Web (Сайт)
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
