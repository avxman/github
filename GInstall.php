<?php

namespace Avxman\Github;

/**
 * Запуск клонирования репозитория из Github
 */
final class GInstall{

    /**
     * Конфигурация
     * @var array $config = []
     */
    private $config = [];

    /**
     * Список данных для вывода на экран
     * @var array $log = []
     */
    private $log = [];

    /**
     * Работа с git
     * @return bool
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
     * @param string $command
     * @return string
     */
    private function gitCommand(string $command) : string
    {
        return $this->command('git '.$command);
    }

    /**
     * Вызов команды
     * @param string $command
     * @return string
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
     * @param array $config = []
     * @return bool
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
     * @param array $config = []
     * @return bool
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
     * @return bool
     */
    public function remove() : bool
    {
        return unlink(__FILE__);
    }

    /**
     * Записываем результаты
     * @param string $text
     * @return void
     */
    public function writeLog(string $text) : void
    {
        $this->log[] = $text;
    }

    /**
     * Вывод результата
     * @return array
     */
    public function printLog() : array
    {
        return $this->log;
    }

}

/**
 * Вывод логов
 */
final class PrintLog{

    /**
     * Получены результаты
     * @var array $message = []
     */
    private $message = [];

    /**
     * Вид шаблона
     * @param string $content
     * @return string
     */
    private function template(string $content) : string
    {
        return implode('', [
            '<!doctype html>',
            '<html lang="en">',
            '<head>',
            '<meta charset="UTF-8">',
            '<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">',
            '<title>Копирование репозитория из Github</title>',
            '<style>',
            'html,body{height: 100%;}',
            'body{background: #000;color: #fff;display: flex;margin: 0;}',
            '.content{display: flex;align-items: flex-start;justify-content: flex-start;flex-flow: column;flex-direction: column;text-align: left;max-width: 50%;margin: auto;background: #a13737;padding: 30px;max-height: 50%;overflow: auto;}',
            '.content > .item{display: flex;width: 100%;max-width: 100%;margin: 8px 0;}',
            '.content > .item::before{content: "";width: 12px;height: 12px;min-width: 12px;margin: 4px 10px 0 0;background: #0b1c0f;box-shadow: inset 0 0 0px 2px coral;}',
            '</style>',
            '</head>',
            '<body>',
            '<div class="content">',
            $content,
            '</div>',
            '</body>',
            '</html>',
        ]);
    }

    /**
     * Преобразовываем текст в шаблонный HTMl вид
     * @param string $message
     * @return string
     */
    private function getLine(string $message) : string
    {
        return implode('', ['<p class="item">', $message, '</p>']);
    }

    /**
     * Вывод результата на экран
     * @return void
     */
    private function start() : void
    {
        $result = [];
        foreach ($this->message as $message){
            $result[] = $this->getLine($message);
        }
        echo $this->template(implode('', $result));
    }

    /**
     * Конструктор
     * @param array $message
     */
    public function __construct(array $message)
    {
        $this->message = $message;
    }

    /**
     * Инициализация вывода результата
     * @return void
     */
    public function run() : void
    {
        $this->start();
    }

}

// Настройка конфигурации
$config = [
    'GITHUB_ENABLED'=>true,
    'GITHUB_LINK'=>'https://personal_token@github.com/user_name/repository',
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
