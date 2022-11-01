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
        $command = $this->commandGenerate('pull');
        if(Str::contains(Str::lower($command), 'error')){
            $comm = $this->commandGenerate("stash save --keep-index");
            if(Str::contains(Str::lower($comm), 'saved')){
                $command = $this->commandGenerate('reset --hard');
                $command .= PHP_EOL.$this->commandGenerate("pull");
                $command .= PHP_EOL.'Обновлено. Однако, в процессе обновлении найден конфликт,
                а именно, на сайте вручную внесли изменения: '.PHP_EOL.$comm;
            }
            else{
                $command = $comm;
            }
        }
        $branchTest = 'test';
        $branch = str_replace('On branch ', '', stristr($this->commandGenerate("status"), PHP_EOL, true));
        $reload = PHP_EOL.$this->commandGenerate("checkout -b {$branchTest}");
        $reload .= PHP_EOL.$this->commandGenerate("branch -D {$branch}");
        $reload .= PHP_EOL.$this->commandGenerate("checkout {$branch}");
        $reload .= PHP_EOL.$this->commandGenerate("branch -D {$branchTest}");
        $this->writtingLog(
            'GithubEvent: %1, result: %2',
            ['%1', '%2'],
            [$this->server['HTTP_X_GITHUB_EVENT']??'Push', $command.$reload]
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
