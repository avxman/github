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

    protected $allowMethods = ['version', 'pull', 'checkout', 'status', 'log', 'reset'];

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
        $reload = [];
        $reload_message = '';
        $branchTest = 'test';
        $command = $this->commandGenerate("pull");
        $low_comment = Str::lower($command);

        if(Str::contains($low_comment, 'no tracking')){
            $command .= PHP_EOL.'Удалённая ветка с текущим именним не существует. Попробуйте переключится на другую ветку и обратно вернуся на эту ветку.';
        }
        else{
            if(Str::contains(Str::lower($low_comment), 'error')){
                $comm = $this->commandGenerate("stash save --keep-index");
                if(Str::contains(Str::lower($comm), 'saved')){
                    $command = $this->commandGenerate("pull");
                    $command .= PHP_EOL.'Обновлено. Однако, в процессе обновлении найден конфликт,
                а именно, на сайте вручную внесли изменения: '.PHP_EOL.$comm;
                }
                else{
                    $command = $comm;
                }
            }
            $branch = preg_replace('/\\n/', '', $this->commandGenerate("symbolic-ref --short HEAD"));
            if($branchTest !== $branch){
                $reload[] = PHP_EOL.$this->commandGenerate("checkout -b {$branchTest}");
                $reload[] = PHP_EOL.$this->commandGenerate("branch -D {$branch}");
                $reload[] = PHP_EOL.$this->commandGenerate("checkout {$branch}");
                $reload[] = PHP_EOL.$this->commandGenerate("branch -D {$branchTest}");
            }
            $reload_message = implode('', $reload);
        }

        $this->writtingLog(
            'SiteEvent: %1, result: %2',
            ['%1', '%2'],
            ['pull', $command.$reload_message]
        );
        $this->result = [$command, $reload_message];
    }

    /**
     * *Переключение веток
     * @param array $data
     * @return void
     */
    protected function checkout(array $data) : void{
        $reset = '';
        $command = $this->commandGenerate("checkout {$data['branch']}");
        if(Str::contains(Str::lower($command), 'error')){
            $reset = $this->commandGenerate('reset --hard');
            $command = $this->commandGenerate("checkout {$data['branch']}");
        }
        $this->pull($data);
        $this->writtingLog(
            'SiteEvent: %1, result: %2',
            ['%1', '%2'],
            ['checkout', $command]
        );
        array_push($this->result, $reset, $command);
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
            'GithubEvent: %1, result: %2',
            ['%1', '%2'],
            ['Reset', $command]
        );
        $this->result = [$command];
    }


    public function events(array $data) : bool{

        $this->is_event = true;
        $event = strtolower(request()->get('payload')['event']??'version');
        if($this->allowedMethod($event)) {$this->{$event}($data);}
        else {$this->is_event = false;}

        return $this->is_event;

    }

}
