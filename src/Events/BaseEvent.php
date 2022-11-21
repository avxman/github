<?php

namespace Avxman\Github\Events;

use Avxman\Github\Logs\AllLog;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 *
 * Общий класс для работы с событиями с помощью API
 *
 */
abstract class BaseEvent
{

    /**
     * *Событие существует в виде метода
     * @var bool $is_event
     */
    public $is_event = false;

    /**
     * *Разрешающие методы
     * @var array $allowMethods
     */
    protected $allowMethods = [];

    /**
     * *Логирование действий
     * @var AllLog $log
     */
    protected $log;

    /**
     * *Конфигурационные данных
     * @var array $config Параметры текущей библиотеки
     */
    protected $config = [];

    /**
     * *Конфигурации сервера
     * @var array $server Аналог глобальной перемены $_SERVER
     */
    protected $server = [];

    /**
     * *Результат после обработки события
     * @var array $result
     */
    protected $result = [];

    /**
     * Тестовая ветка
     * Данная ветка требуется для переключения на её,
     * чтобы можно было удалить и обновить данные активной ветки из удалённого репозитория
     * @var string $tempBranch = 'test'
    */
    protected $tempBranch = 'test';

    /**
     * *Проверка на использование выбранного метода
     * @param string $name_method
     * @return bool
     */
    protected function allowedMethod(string $name_method) : bool{
        return in_array($name_method, $this->allowMethods);
    }

    /**
     * *Запись в лог
     * @param string $message
     * @param array $search
     * @param array $params
     * @return bool
     */
    protected function writtingLog(string $message, array $search = [], array $params = []) : bool{
        if(!$this->config['IS_DEBUG']) return false;
        $this->log->write(str_replace($search, $params, $message));
        return true;
    }

    /**
     * *Добавление папки в команду git
     * @return string
     */
    protected function addGithubFolder() : string{
        return File::exists($this->config['GITHUB_ROOT_FOLDER'])
            ? Str::finish(' -C ', Str::finish($this->config['GITHUB_ROOT_FOLDER'], ' '))
            : ' ';
    }

    /**
     * *Получить результаты после выполнения команды
     * в командной строке
     * @return string
     */
    protected function commandLineLog() : string{
        return $this->config['IS_DEBUG'] ? ' 2>&1' : '';
    }

    /**
     * *Генерируем команду для вставки в командную строку
     * @param string $command
     * @return string
     */
    protected function commandGenerate(string $command) : string{
        return $this->command("git{$this->addGithubFolder()}{$command}{$this->commandLineLog()}");
    }

    /**
     * *Вызов командной строки
     * @param string $command
     * @return string
     */
    protected function command(string $command = '') : string{
        $result = shell_exec($command);
        return (is_null($result) || is_bool($result)) ? "" : $result;
    }

    /**
     * *Проверка существующих методов
     * @param string $name
     * @param array $arguments
     */
    public function __call(string $name, array $arguments)
    {
        if(function_exists($name) && in_array($name, $this->allowMethods)){
            $this->{$name}($arguments);
        }
        else{
            $this->is_event = false;
        }
    }

    /**
     * *Конструктор
     * @param array $server
     * @param array $config
     */
    public function __construct(array $server, array $config){
        $this->server = $server;
        $this->config = $config;
        $this->log = new AllLog($config);
    }

    /**
     * *Обработка события
     * @param array $data
     * @return bool
     */
    public function events(array $data) : bool{
        return false;
    }

    /**
     * *Получения результатов
     * @return array
     */
    public function getResult() : array{
        return $this->result;
    }

    /**
     * Обновление ветки из удалённого репозитория
     * @param string $branch_name
     * @return string
     */
    protected function update(string $branch_name) : string
    {
        $branchTest = $this->tempBranch;
        $branch = $branch_name;

        if(!Str::contains(preg_replace('/\\n/', ' ', $this->commandGenerate('branch --list')), $branch)){
            return PHP_EOL.'The branch '.$branch.' is not found. It is absent in a remote repository';
        }

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

}
