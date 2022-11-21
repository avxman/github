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
     * Обновление ветки из удалённого репозитория
     * @param string $branch_name
     * @return string
     */
    protected function update(string $branch_name) : string
    {
        $branchTest = 'test';
        $branch = $branch_name;
        //$branchCurrent = preg_replace('/\\n/', '', $this->commandGenerate("rev-parse --abbrev-ref HEAD"));
        $branchList = Str::contains(preg_replace('/\\n/', ' ', $this->commandGenerate('branch --list')), $branchTest);
        $command[] = PHP_EOL.$this->commandGenerate('reset --hard');
        $command[] = $branchList
            ? PHP_EOL.$this->commandGenerate("checkout {$branchTest}")
            : PHP_EOL.$this->commandGenerate("checkout -b {$branchTest}");
        $command[] = PHP_EOL.$this->commandGenerate("branch -D {$branch}");
        $command[] = PHP_EOL.$this->commandGenerate("checkout {$branch}");
        $command[] = PHP_EOL.$this->commandGenerate("branch -D {$branchTest}");
        $command[] = PHP_EOL.$this->commandGenerate("pull");
        return implode('', $command);
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
