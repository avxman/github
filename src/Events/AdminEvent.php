<?php

namespace Avxman\Github\Events;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 *
 * Работа с событиями Гитхаба через Web
 *
 */
class AdminEvent extends BaseEvent
{

    /**
     * Список разрешенных событий для вызовов команд
     * @var array $allowMethods
     */
    protected array $allowMethods = ['home'];

    protected function menu() : void
    {
        $this->result['menu'] = collect([
            'registration'=>collect([
                'enabled'=>true,
                'name'=>'Registration',
                'description'=>'',
                'children'=>collect([
                    'github'=>collect([
                        'enabled'=>true,
                        'name'=>'Github',
                        'description'=>'',
                        'link'=>route('github.web.registration', ['version'=>Config('github.GITHUB_API_VERSION'), 'secret'=>Config('github.GITHUB_TOKEN')])
                    ]),
                ]),
            ]),
            'database'=>collect([
                'enabled'=>true,
                'name'=>'Database',
                'description'=>'',
                'children'=>collect([
                    'backup'=>collect([
                        'enabled'=>true,
                        'name'=>'All backup',
                        'description'=>'',
                        'link'=>route('github.web.database', ['version'=>Config('github.GITHUB_API_VERSION'), 'secret'=>Config('github.GITHUB_TOKEN')]),
                    ]),
                    'export'=>collect([
                        'enabled'=>true,
                        'name'=>'Export',
                        'description'=>'',
                        'link'=>route('github.web.database', ['version'=>Config('github.GITHUB_API_VERSION'), 'secret'=>Config('github.GITHUB_TOKEN')]),
                    ]),
                ]),
            ]),
            'repository'=>collect([
                'enabled'=>true,
                'name'=>'Repository',
                'description'=>'',
                'children'=>collect([
                    'pull'=>collect([
                        'enabled'=>true,
                        'name'=>'Update files',
                        'description'=>'',
                        'link'=>route('github.web.repository', ['version'=>Config('github.GITHUB_API_VERSION'), 'secret'=>Config('github.GITHUB_TOKEN'), 'repository'=>Config('github.GITHUB_REPO_USER').'@'.Config('github.GITHUB_REPO_NAME')]),
                    ]),
                    'checkout'=>collect([
                        'enabled'=>true,
                        'name'=>'Checkout',
                        'description'=>'Switch a branch',
                        'link'=>route('github.web.repository', ['version'=>Config('github.GITHUB_API_VERSION'), 'secret'=>Config('github.GITHUB_TOKEN'), 'repository'=>Config('github.GITHUB_REPO_USER').'@'.Config('github.GITHUB_REPO_NAME')]),
                    ]),
                    'log'=>collect([
                        'enabled'=>true,
                        'name'=>'Show logs',
                        'description'=>'',
                        'link'=>route('github.web.repository', ['version'=>Config('github.GITHUB_API_VERSION'), 'secret'=>Config('github.GITHUB_TOKEN'), 'repository'=>Config('github.GITHUB_REPO_USER').'@'.Config('github.GITHUB_REPO_NAME')]),
                    ]),
                    'status'=>collect([
                        'enabled'=>true,
                        'name'=>'Show status',
                        'description'=>'',
                        'link'=>route('github.web.repository', ['version'=>Config('github.GITHUB_API_VERSION'), 'secret'=>Config('github.GITHUB_TOKEN'), 'repository'=>Config('github.GITHUB_REPO_USER').'@'.Config('github.GITHUB_REPO_NAME')]),
                    ]),
                    'version'=>collect([
                        'enabled'=>true,
                        'name'=>'Version git',
                        'description'=>'',
                        'link'=>route('github.web.repository', ['version'=>Config('github.GITHUB_API_VERSION'), 'secret'=>Config('github.GITHUB_TOKEN'), 'repository'=>Config('github.GITHUB_REPO_USER').'@'.Config('github.GITHUB_REPO_NAME')]),
                    ]),
                    'reset'=>collect([
                        'enabled'=>true,
                        'name'=>'Reset index files',
                        'description'=>'',
                        'link'=>route('github.web.repository', ['version'=>Config('github.GITHUB_API_VERSION'), 'secret'=>Config('github.GITHUB_TOKEN'), 'repository'=>Config('github.GITHUB_REPO_USER').'@'.Config('github.GITHUB_REPO_NAME')]),
                    ]),
                    'clear'=>collect([
                        'enabled'=>true,
                        'name'=>'Clear branches',
                        'description'=>'',
                        'link'=>route('github.web.repository', ['version'=>Config('github.GITHUB_API_VERSION'), 'secret'=>Config('github.GITHUB_TOKEN'), 'repository'=>Config('github.GITHUB_REPO_USER').'@'.Config('github.GITHUB_REPO_NAME')]),
                    ]),
                    'branchremote'=>collect([
                        'enabled'=>true,
                        'name'=>'Show remote branches',
                        'description'=>'',
                        'link'=>route('github.web.repository', ['version'=>Config('github.GITHUB_API_VERSION'), 'secret'=>Config('github.GITHUB_TOKEN'), 'repository'=>Config('github.GITHUB_REPO_USER').'@'.Config('github.GITHUB_REPO_NAME')]),
                    ]),
                    'branchlocal'=>collect([
                        'enabled'=>true,
                        'name'=>'Show local branches',
                        'description'=>'',
                        'link'=>route('github.web.repository', ['version'=>Config('github.GITHUB_API_VERSION'), 'secret'=>Config('github.GITHUB_TOKEN'), 'repository'=>Config('github.GITHUB_REPO_USER').'@'.Config('github.GITHUB_REPO_NAME')]),
                    ]),
                ]),
            ]),
        ]);
    }

    /**
     *
     * @param array $data
     * @return void
     */
    protected function home(array $data = []) : void
    {
        $this->menu();
    }

    /**
     * Вызов событий
     * @param array $data
     * @return bool
     */
    public function events(array $data) : bool
    {

        $this->is_event = true;
        $event = strtolower(request()->get('payload')['event']??'home');
        if($this->allowedMethod($event)) {$this->{$event}($data);}
        else {$this->is_event = false;}

        return $this->is_event;

    }

}
