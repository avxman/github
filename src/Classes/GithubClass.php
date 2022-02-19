<?php

namespace Avxman\Github\Classes;

use Avxman\Github\Connections\BaseConnection;
use Avxman\Github\Connections\GithubConnection;
use Avxman\Github\Connections\SiteConnection;
use Avxman\Github\Events\BaseEvent;
use Avxman\Github\Events\GithubEvent;
use Avxman\Github\Events\SiteEvent;
use Avxman\Github\Messages\GithubMessage;

/**
 *
 * Основной класс для работы с гитхаб
 *
*/
abstract class GithubClass{

    /**
     * *Конфигурационные данных
     * @var array $config Параметры текущей библиотеки
    */
    protected array $config = [];

    /**
     * *Конфигурации сервера
     * @var array $server Аналог глобальной перемены $_SERVER
    */
    protected array $server = [];

    /**
     * *Режим работы, через гитхаб или сайт
     * @var bool $post_is_github Гитхаб (true) / Сайт (false)
    */
    protected bool $post_is_github = true;

    /**
     * *Работа с ошибками
     * @var GithubMessage $message Обработчик ошибок
     */
    protected GithubMessage $message;

    /**
     * *Результаты
     * @var array $result Конечный результат
    */
    protected array $result = [];

    /**
     * *Подключение к удаленному адресу
     * @param BaseConnection $instance тип подключения (Гидхаб или Сайт)
     * @return void без возврата данных
    */
    protected function connection(BaseConnection $instance) : void{
        if(!$instance->isConnect()){
            $messages = array_merge(['Не удалось соеденится с адресом'], $instance->errorMessage());
            $this->message->setMessages($messages)->errors();
        }
    }

    /**
     * *Обработка данных
     * @param BaseConnection $instance тип подключения (Гидхаб или Сайт)
     * @return array список данных полученых после валидации
     */
    protected function data(BaseConnection $instance) : array{
        if (!count($result = $instance->getData())){
            $messages = array_merge(['Данные не получены'], $instance->errorMessage());
            $this->message->setMessages($messages)->errors();
            return [];
        }
        return $result;
    }

    /**
     * *Вызов событий
     * @param BaseEvent $event тип события
     * @param array $result список данных после валидации
     * @return void без возврата данных
    */
    protected function event(BaseEvent $event, array $result) : void{
        if(!$event->events($result)){
            $e = $this->server['HTTP_X_GITHUB_EVENT']??'Not Found';
            header('HTTP/1.0 404 Not Found');
            echo "Event:{$e}";
            die();
        }
    }

    /**
     * *Конструктор
    */
    public function __construct(){
        $this->server = request()->server->all();
        $this->config = config()->get('github');
        $this->message = new GithubMessage((bool)$this->config['IS_DEBUG']);
        $errorMessage = [];
        if(!count($this->config)) {
            $isError = true;
            $errorMessage[] = "Не найден конфигурационный файл.";
        }
        elseif (!$this->config['GITHUB_TOKEN'] || empty($this->config['GITHUB_TOKEN'])){
            $isError = true;
            $errorMessage[] = empty($this->config['GITHUB_TOKEN'])
                ? "Токен пустой"
                : "Не найден токен";
        }
        elseif (is_null($this->config['IS_DEBUG']) || !is_bool($this->config['IS_DEBUG'])){
            $isError = true;
            $errorMessage[] = empty($this->config['IS_DEBUG'])
                ? "Дебагер пуст"
                : "Не найден ключ дебагер";
        }
        else{$isError = false;}
        if($isError){
            $this->message->setMessages($errorMessage)->errors();
        }
    }

    /**
     * *Инициализация работы с классом
     * @return void без возврата данных
     */
    public function instance() : void{
        header('HTTP/1.0 404 Not Found');
        echo "Instance is not found";
    }

    /**
     * *Получение результатов
     * @return array список результата после обработки события
     */
    public function getResult() : array{
        return $this->result;
    }

    /**
     * *Получение текущего режима
     * @return bool Гитхаб (true) или Сайт (false)
     */
    public function isGithub() : bool{
        return $this->post_is_github;
    }

}
