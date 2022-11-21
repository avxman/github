<?php

namespace Avxman\Github\Events;

use Illuminate\Support\Str;

/**
 *
 * Работа с событиями Гитхаба через Web
 *
 */
class SiteEvent extends BaseEvent
{

    /**
     * Список разрешенных событий для вызовов команд
     * @var array $allowMethods
     */
    protected $allowMethods = ['version', 'pull', 'checkout', 'status', 'log', 'reset', 'clear', 'branchremote', 'branchlocal'];

    /**
     * *Версия Гитхаба установлена на сайте (хостинг или сервер)
     * @param array $data
     * @return void
     */
    protected function version(array $data = []) : void{
        $command = $this->commandGenerate("--version");
        $this->writtingLog(
            'SiteEvent: %1, result: %2',
            ['%1', '%2'],
            ['default', $command]
        );
        $this->result = [$command];
    }

    /**
     * *Обновления сайта из Гитхаба
     * @param array $data
     * @return void
     */
    protected function pull(array $data) : void{

        $message = $this->update(
            preg_replace('/\\n/', '', $this->commandGenerate("rev-parse --abbrev-ref HEAD"))
        );
        $this->writtingLog(
            'SiteEvent: %1, result: %2',
            ['%1', '%2'],
            ['pull', $message]
        );
        $this->result = [$message];

    }

    /**
     * *Переключение веток
     * @param array $data
     * @return void
     */
    protected function checkout(array $data) : void{

        $message = $this->update(
            $data['branch']
            ?? preg_replace('/\\n/', '', $this->commandGenerate("rev-parse --abbrev-ref HEAD"))
        );
        $this->writtingLog(
            'SiteEvent: %1, result: %2',
            ['%1', '%2'],
            ['checkout', $message]
        );
        $this->result[] = $message;

    }

    /**
     * *Проверка статуса
     * @param array $data
     * @return void
     */
    protected function status(array $data) : void{
        $command = $this->commandGenerate("status");
        $this->writtingLog(
            'SiteEvent: %1, result: %2',
            ['%1', '%2'],
            ['status', $command]
        );
        $this->result = [$command];
    }

    /**
     * *Показать последние логи
     * @param array $data
     * @return void
     */
    protected function log(array $data) : void{
        $count = $data['count']??10;
        if($count > 30) $count = 30;
        $command = $this->commandGenerate("log -{$count}");
        $this->writtingLog(
            'SiteEvent: %1, result: %2',
            ['%1', '%2'],
            ['status', $command]
        );
        $this->result = [$command];
    }

    /**
     * *Сброс отслеживаемых файлов и папок в индексе
     * @param array $data
     * @return void
     */
    protected function reset(array $data) : void{
        $command = $this->commandGenerate('reset --hard');
        $this->writtingLog(
            'SiteEvent: %1, result: %2',
            ['%1', '%2'],
            ['Reset', $command]
        );
        $this->result = [$command];
    }

    /**
     * Очистка всех не существующих веток из локальной среды
     * которые отсутствуют в удалённом репозитории
    */
    protected function clear() : void
    {
        $branch_remote = $branch_local = [];
        preg_match_all('/refs\/heads\/(.*?)\\n+/i', $this->commandGenerate("ls-remote"), $branch_remote);
        preg_match_all('/\s(\w.*)\\n/', $this->commandGenerate('branch --list'), $branch_local);

        if($diff = implode(' ', array_diff($branch_local[1]??[], $branch_remote[1]??[]))){
            $command = $this->commandGenerate("branch -D ".$diff);
        }
        else {$command = 'Не используемые ветки не найдены';}

        $this->writtingLog(
            'SiteEvent: %1, result: %2',
            ['%1', '%2'],
            ['Clear', $command]
        );
        $this->result = [$command];
    }

    /**
     * Список веток из удалённого репозитория
     * @return void
     */
    protected function branchremote() : void
    {
        $branch_remote = [];
        preg_match_all('/refs\/heads\/(.*?)\\n+/i', $this->commandGenerate("ls-remote"), $branch_remote);
        $command = count($branch_remote[1]??[]) ? implode(PHP_EOL, $branch_remote[1]) : 'Нет ни одной ветки из удалённого репозитория';

        $this->writtingLog(
            'SiteEvent: %1, result: %2',
            ['%1', '%2'],
            ['BranchRemote', $command]
        );
        $this->result = [$command];
    }

    /**
     * Список веток из удалённого репозитория
     * @return void
     */
    protected function branchlocal() : void
    {

        $branch_local = [];
        preg_match_all('/\s(\w.*)\\n/', $this->commandGenerate('branch --list'), $branch_local);

        $command = count($branch_local[1]??[]) ? implode(PHP_EOL, $branch_local[1]) : 'Нет ни одной локальной ветки';

        $this->writtingLog(
            'SiteEvent: %1, result: %2',
            ['%1', '%2'],
            ['BranchLocal', $command]
        );
        $this->result = [$command];
    }

    /**
     * Вызов событий
     * @param array $data
     * @return bool
     */
    public function events(array $data) : bool{

        $this->is_event = true;
        $event = strtolower(request()->get('payload')['event']??'version');
        if($this->allowedMethod($event)) {$this->{$event}($data);}
        else {$this->is_event = false;}

        return $this->is_event;

    }

}
