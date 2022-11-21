<?php

namespace Avxman\Github\Events;

use Illuminate\Support\Str;

/**
 *
 * Работа с событиями Гитхаба через API
 *
 */
class GithubEvent extends BaseEvent
{

    protected $allowMethods = ['default', 'ping', 'push'];

    /**
     * *Версия Гитхаба установлена на сайте (хостинг или сервер)
     * @param array $data
     * @return void
     */
    protected function default(array $data) : void{
        $command = $this->commandGenerate('--version');
        $this->writtingLog(
            'GithubEvent: %1, result: %2',
            ['%1', '%2'],
            [$this->server['HTTP_X_GITHUB_EVENT']??'Default', $command]
        );
    }

    /**
     * *Проверка соединения с Гитхаб репозиторием
     * @param array $data
     * @return void
     */
    protected function ping(array $data) : void{
        $this->writtingLog(
            'GithubEvent: %1',
            ['%1'],
            [$this->server['HTTP_X_GITHUB_EVENT']??'Ping']
        );
    }

    /**
     * *Обновления сайта из Гитхаба
     * @param array $data
     * @return void
     */
    protected function push(array $data) : void{
        $command = $this->update(
            preg_replace('/\\n/', '', $this->commandGenerate("rev-parse --abbrev-ref HEAD"))
        );
        $this->writtingLog(
            'GithubEvent: %1, result: %2',
            ['%1', '%2'],
            [$this->server['HTTP_X_GITHUB_EVENT']??'Push', $command]
        );
    }


    public function events(array $data) : bool{

        $this->is_event = true;
        $event = strtolower($this->server['HTTP_X_GITHUB_EVENT']??'default');
        if($this->allowedMethod($event)) {$this->{$event}($data);}
        else {$this->is_event = false;}

        return $this->is_event;

    }

}
