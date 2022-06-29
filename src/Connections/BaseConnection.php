<?php

namespace Avxman\Github\Connections;

/**
 *
 * Подключение к удалённому сервису (Гитхаб или Сайт)
 *
 */
abstract class BaseConnection
{

    /**
     * *Статус подключения
     * @var bool $is_connect Валидация пройдена (true) или отклонена (false)
     */
    protected bool $is_connect = false;

    /**
     * *Данные после валидации
     * @var array $data Список данных после обработки валидации
     */
    protected array $data = [];

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
     * *Работа с ошибками
     * @var array $errorMessage Обработчик ошибок
     */
    protected array $errorMessage = [];

    /**
     * *Установлен git
     * @return bool
     */
    protected function hasGit() : bool{
        if (!shell_exec('git --version')) {
            $this->errorMessage[] = "Missing 'git' not install";
            return false;
        }
        return true;
    }

    /**
     * *Валидация входных данных или проверка на обходимые функции
     * @return bool Валидация пройдена (true) или отклонена (false)
     */
    protected function validation() : bool{
        return false;
    }

    /**
     * *Результат валидации
     * @return bool Подключены (true) или Отключены (false)
     */
    protected function connect() : bool{
        return $this->is_connect = $this->validation() && $this->hasGit();
    }

    /**
     * *Конструктор
     */
    public function __construct(array $server = [], array $config = []){
        $this->server = $server;
        $this->config = $config;
        $this->connect();
    }

    /**
     * *Получаем результат подключения
     * @return bool Подключены (true) или Отключены (false)
     */
    public function isConnect() : bool{
        return $this->is_connect;
    }

    /**
     * *Список выявленных ошибок при валидации
     * @return array Список сообщений об ошибках
     */
    public function errorMessage() : array{
        return $this->errorMessage;
    }

    /**
     * *Получения данных после валидации
     * @return array Список данных
     */
    public function getData() : array{
        return $this->data;
    }

}
