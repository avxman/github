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
        $this->log->write(Str::replace($search, $params, $message));
        return true;
    }

    /**
     * *Добавление папки в команду git
     * @return string
     */
    protected function addGithubFolder() : string{
        return File::exists($this->config['GITHUB_ROOT_FOLDER'])
            ? Str::start(Str::finish($this->config['GITHUB_ROOT_FOLDER'], ' '), ' -C ')
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

}
