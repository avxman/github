<?php

namespace Avxman\Github\Connections;

/**
 * Подключения для работы с Базай Данных
*/
class DatabaseConnection extends SiteConnection
{

    protected function validation(): bool
    {
        if (!extension_loaded('hash')) {
            $this->errorMessage[] = "Missing 'hash' php extension to check the secret code validity.";
            return false;
        }

        if(!function_exists('shell_exec')){
            $this->errorMessage[] = "Missing 'shell_exec' php global function is disabled.";
            return false;
        }

        if(!request()->has('payload') || !request()->get('payload')['event']??false){
            $this->errorMessage[] = "Missing 'payload' or 'payload[event]' variable is empty.";
            return false;
        }

        return true;
    }

}
