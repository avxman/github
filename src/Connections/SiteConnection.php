<?php

namespace Avxman\Github\Connections;

/**
 *
 * Подключение для работы с Гитхабом через Сайт
 *
 */
class SiteConnection extends BaseConnection
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

        return true;
    }

    public function getData(): array
    {

        $type = $this->server['CONTENT_TYPE']??'text/html';

        $json = match ($type) {
            'application/json' => (array)file_get_contents('php://input'),
            'application/x-www-form-urlencoded',
            'text/html' => request()->get('payload'),
            default => FALSE,
        };

        if($json === FALSE || $json === NULL) {
            $this->errorMessage[] = ($json === FALSE
                    ? "Не поддерживается такой тип контента (CONTENT_TYPE): "
                    : "Не переданы объязательные данные в контенте (CONTENT_TYPE): ")
                .$type;
            $json = [];
        }

        return $this->data = $json;

    }

}
