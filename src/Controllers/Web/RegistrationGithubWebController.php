<?php

namespace Avxman\Github\Controllers\Web;

use Avxman\Github\Controllers\GithubWebController;
use Avxman\Github\Facades\GithubWebFacade;
use Illuminate\Support\Facades\Response;

/**
 *
 * Контроллер по работе c Активацией Гитхаба и Сайта через Web (Сайт)
 *
*/
class RegistrationGithubWebController extends GithubWebController
{

    /**
     * *Обработка всех маршрутов
     * @return  \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(){
        GithubWebFacade::registrationInstance();
        $messages = GithubWebFacade::getResult();
        $result = collect();
        foreach($messages as $message){
            foreach (explode(PHP_EOL, $message)??[] as $mess){
                $result->push($mess);
            }
        }
        return view('github.index', ['result'=>collect($result)]);
    }

}
