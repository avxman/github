<?php

namespace Avxman\Github\Instances;

use Avxman\Github\Connections\GithubConnection;
use Avxman\Github\Events\GithubEvent;
use Avxman\Github\Classes\GithubClass;

/**
 *
 * Работы с гитхаб через API
 *
*/
class GithubApiInstance extends GithubClass{


    public function instance() : void{

        $instance = new GithubConnection($this->server, $this->config);

        $this->connection($instance);

        $result = $this->data($instance);

        $event = new GithubEvent($this->server, $this->config);

        $this->event($event, $result);

    }

}
