<?php

namespace Avxman\Github\Connections;

/**
 *
 * Подключение для активации Пользователя Гитхаба с Сайтом
 *
*/
class RegistrationConnection extends SiteConnection
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

        if(!$this->config['GITHUB_PATH_SSH']){
            $this->errorMessage[] = "Не указан путь к папке ssh, к примеру, ~/.ssh";
            return false;
        }

        if(!$this->config['GITHUB_PATH_CONFIG_SSH']){
            $this->errorMessage[] = "Не указано имя конфигурационного файла ssh/config, к примеру, ~/.ssh/config";
            return false;
        }

        if(!$this->config['GITHUB_PATH_NAME_SSH']){
            $this->errorMessage[] = "Не указано имя файла куда будут записаны ключи к примеру, github_key";
            return false;
        }

        return true;
    }

    public function getData(): array
    {

        return $this->data = [
            'path_ssh'=>$this->config['GITHUB_PATH_SSH'],
            'config_ssh'=>$this->config['GITHUB_PATH_CONFIG_SSH'],
            'name_ssh'=>$this->config['GITHUB_PATH_NAME_SSH'],
        ];

    }

}
