<?php

namespace Avxman\Github\Controllers\Web;

use Avxman\Github\Controllers\GithubWebController;
use Avxman\Github\Facades\GithubWebFacade;
use Illuminate\Support\Facades\Response;

/**
 *
 * Контроллер по работе c Базой Данных через Web (Сайт)
 *
*/
class DatabaseGithubWebController extends GithubWebController
{

    /**
     * *Обработка всех маршрутов
     * @return  \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    */
    public function index(){
        GithubWebFacade::databaseInstance();
        $messages = GithubWebFacade::getResult();
        $result = collect();
        if($messages['type']??false){
            if($messages['type'] === 'backups'){
                foreach($messages['links'] as $message){
                    $result->push($message);
                }
            }
            elseif($messages['type'] === 'delete'){
                $result->push($messages['delete']
                    ? 'Резервная копия базы данных удалена - '.$messages['name']
                    : 'Не удалось удалить резервную копию базы данных - '.$messages['name']
                );
            }
            elseif($messages['type'] === 'download'){
                if($messages['url']->isOk()){
                    return $messages['url'];
                }
            }
        }
        else{
            foreach($messages as $message){
                foreach (explode(PHP_EOL, $message)??[] as $mess){
                    $result->push($mess);
                }
            }
        }
        return view('github.index', ['result'=>collect($result), 'type'=>$messages['type']??NULL, 'delete'=>$messages['delete']??NULL]);
    }

}
