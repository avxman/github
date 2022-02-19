<?php

namespace Avxman\Github\Messages;

/**
 *
 * Работа с ошибками
 *
*/
class GithubMessage
{

    /**
     * Выводить/Не выводить ошибки
     * @var bool $is_debug
    */
    protected bool $is_debug = false;

    /**
     * Список ошибок
     * @var array $messages
    */
    protected array $messages = [];

    /**
     * Шаблон вывода ошибок для Handler
     * @param int $type
     * @param string $body
     * @param string $file
     * @param int $line
     * @return string
    */
    protected function themeHandler(int $type = 0, string $body = '', string $file = '', int $line = 0) : string{
        return $body;
    }

    /**
     * Шаблон вывода ошибок для Exception
     * @param string $body
     * @return string
    */
    protected function themeException(string $body = '') : string{
        return $body;
    }

    /**
     * Получение ошибок для Handler
     * @param int $errType
     * @param string $errText
     * @param string $errFile
     * @param int $errLine
     * @return void
    */
    protected function errorHandler(int $errType, string $errText, string $errFile, int $errLine) : void{
        if($errType && !error_reporting(E_ERROR | E_WARNING)){
            return;
        }

        header('Content-Type: text/html; charset=utf-8');

        echo $this->themeHandler($errType, $errText, $errFile, $errLine);

        exit;
    }

    /**
     * Получение ошибок для Exception
     * @param \ErrorException $e
     * @return void
    */
    protected function exceptionHandler($e) : void{
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: text/html; charset=utf-8');
        echo $this->themeException($e->getMessage());
        exit;
        // header('Content-Type: application/json; charset=utf-8');
        // echo json_encode(["error"=>"Server Error:<br>".$e->getMessage()]);
    }

    /**
     * Получить ошибку по индексу из списка
     * @param int $index
     * @return string
    */
    protected function getMessage(int $index = 0) : string{
        return $this->messages[$index]??"Не известная ошибка";
    }

    /**
     * Конструктор
     * @param bool $debug
    */
    public function __construct(bool $debug){

        $this->is_debug = $debug;

        $self = $this;

        set_error_handler(static function (int $errType, string $errText, string $errFile, int $errLine) use ($self){
            $self->errorHandler($errType, $errText, $errFile, $errLine);
        });

        set_exception_handler(static function($e) use ($self){
            $self->exceptionHandler($e);
        });

    }

    /**
     * Записать ошибку в общий список
     * @param string $message
     * @return self
    */
    public function setMessage(string $message = "") : self{
        $this->messages[] = $message;
        return $this;
    }

    /**
     * Записать несколько ошибок в общий список
     * @param array $messages
     * @return self
    */
    public function setMessages(array $messages = []) : self{
        $this->messages = $messages;
        return $this;
    }

    /**
     * Вывод одной ошибки
     * @throws \ErrorException
     * @return void
     */
    public function error() : void{
        throw new \ErrorException($this->getMessage());
    }

    /**
     * Вывод нескольких ошибок
     * @throws \ErrorException
     * @return void
     */
    public function errors() : void{
        $messages = "";
        $i = 1;
        foreach ($this->messages as $message){
            $messages .= "$i. $message; ".PHP_EOL;
            $i++;
        }
        throw new \ErrorException($messages);
    }

}
