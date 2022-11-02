<?php

namespace Avxman\Github;

/**
 *
*/
final class GInstall{

    /**
     * Конфигурация
    */
    private $config = [];

    /**
     * Список данных для вывода на экран
    */
    private $log = [];

    /**
     * Работа с git
    */
    private function git() : bool
    {

        // Проверка на доступ к репозиторию
        $hasConnection = $this->gitCommand('ls-remote '.$this->config['GITHUB_LINK']);
        if(stripos($hasConnection, 'head') === false){
            $this->writeLog('Нет доступа к удалённому репозиторию из Github - '.$this->config['GITHUB_LINK']);
            return false;
        }
        $this->writeLog('Доступ к удалённому репозиторию Github имеется');

        // Копируем удалённый репозиторий
        $clone = $this->gitCommand('clone '.$this->config['GITHUB_LINK']);
        if(stripos($clone, 'cloning') === false){
            $this->writeLog('Не удалось скачать удалённый репозиторий из Github');
            return false;
        }
        $this->writeLog('Клонирование удалённого репозитория выполнено');

        return true;
    }

    /**
     * Вызов git команды
    */
    private function gitCommand(string $command) : string
    {
        return $this->command('git '.$command);
    }

    /**
     * Вызов команды
     */
    private function command(string $command) : string
    {
        $result = shell_exec($command.' 2>&1');
        if((is_null($result) || is_bool($result))){
            $this->writeLog('Команда не найдена или имеется пустой результат');
            return '';
        }

        $this->writeLog($result);
        return $result;
    }

    /**
     * Проверка на существующие методы
    */
    private function isValidation(array $config = []) : bool
    {
        $hasError = false;
        if(!function_exists('shell_exec')){
            $this->writeLog('На хостинге (сервере) отключены необходимые методы');
            $hasError = true;
        }

        if(!($config['GITHUB_ENABLED']??false)){
            $this->writeLog('В настройке конфигурации отключена установка');
            $hasError = true;
        }

        if(empty($config['GITHUB_LINK']??'')){
            $this->writeLog('В настройке конфигурации отсутствует ссылка на удалённый репозиторий');
            $hasError = true;
        }

        return !$hasError;
    }

    /**
     * Инициализация проекта (репозитория)
    */
    public function run(array $config = []) : bool
    {

        $hasError = false;
        $this->config = $config;

        // Проверяем валидацию
        if(!$this->isValidation($config)){
            $this->writeLog('Не пройдена проверка на валидацию');
            $hasError = true;
        }

        // Запускам команды Git
        if(!$this->git()){
            $this->writeLog('Команды git не отработаны');
            $hasError = true;
        }

        return !$hasError;
    }

    /**
     * Удаляем установочный файл
     */
    public function remove() : bool
    {
        return unlink(__FILE__);
    }

    /**
     * Записываем результаты
     */
    public function writeLog(string $text) : void
    {
        $this->log[] = $text;
    }

    /**
     * Вывод результата
     */
    public function printLog() : array
    {
        return $this->log;
    }

}

/**
 *
*/
final class PrintLog{

    /**
     * Результаты
    */
    private $message = [];

    /**
     * Преобразовываем текст в шаблонный вид
    */
    private function theme(string $message) : void
    {
        $result = [];
        echo implode('<br>', $result);
    }

    /**
     * Запускаем вывод результата на экран
    */
    private function start() : void
    {
        foreach ($this->message as $message){
            $this->theme($message);
        }
    }

    /**
     * Конструктор
    */
    public function __construct(array $message)
    {
        $this->message = $message;
    }

    /**
     * Инициализация вывода результата
    */
    public function run() : void
    {
        $this->start();
    }

}

// Настройка конфигураций
$config = [
    'GITHUB_ENABLED'=>true,
    'GITHUB_LINK'=>'https://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@github.com/Doroshenko-agency/livecleantoday',
];

// Запускаем клонирование проекта из Github
$install = new GInstall;
if($install->run($config)){
    if(!$install->remove()){
        $install->writeLog('Не удалось удалить установочный файл. Удалите его вручную');
    }
    $install->writeLog('Копирование проекта выполнено');
}

// Вывод результата
(new PrintLog($install->printLog()))->run();

exit;
