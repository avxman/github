<?php

namespace Avxman\Github\Instances;

use Avxman\Github\Connections\DatabaseConnection;
use Avxman\Github\Connections\RegistrationConnection;
use Avxman\Github\Connections\SiteConnection;
use Avxman\Github\Events\DatabaseEvent;
use Avxman\Github\Events\RegistrationEvent;
use Avxman\Github\Events\SiteEvent;
use Avxman\Github\Classes\GithubClass;

/**
 *
 * Работы с гитхаб через Web
 *
 */
class GithubWebInstance extends GithubClass{

    protected bool $post_is_github = false;


    public function instance() : void{

        $instance = new SiteConnection($this->server, $this->config);

        $this->connection($instance);

        $result = $this->data($instance);

        $event = new SiteEvent($this->server, $this->config);

        $this->event($event, $result);

        $this->result = $event->getResult();

    }

    /**
     * *Работа с Авторизацией пользователя и Регистрацией репозитория
     * @return void
    */
    public function registrationInstance() : void{

        $instance = new RegistrationConnection($this->server, $this->config);

        $this->connection($instance);

        $result = $this->data($instance);

        $event = new RegistrationEvent($this->server, $this->config);

        $this->event($event, $result);

        $this->result = $event->getResult();

    }

    /**
     * Работа с Базой Данных
     * @return void
    */
    public function databaseInstance() : void{

        $instance = new DatabaseConnection($this->server, $this->config);

        $this->connection($instance);

        $result = $this->data($instance);

        $event = new DatabaseEvent($this->server, $this->config);

        $this->event($event, $result);

        $this->result = $event->getResult();

    }

}
