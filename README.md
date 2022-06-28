## PHP 7.* and Laravel 5.*
##### Открыть консольную комманду, перейти в корневую папку проекта и запустить набрав комманду
```composer
composer require avxman/github
php artisan vendor:publish --tag=avxman-github-all
composer dump-autoload
```
##### Добавить в config/app.php
```text
'providers'=>[
    ...
    Avxman\Github\Providers\GithubServiceProvider::class
],
'aliases'=>[
    ...
    'Github'=>Avxman\Github\Facades\GithubFacade::class
]
```
##### Добавить в app/Http/routes.php ближе к первым строкам
```text
\Avxman\Github\Routes\GithubRoute::allRoutes(Config()->get('github'));
```
##### Добавить настройки в .env
```dotenv
#Github
GITHUB_ENABLED=true
GITHUB_TOKEN=your_custom_token
HTTP_X_GITHUB_SECRET=your_secret_from_github_deploy
GITHUB_REPO_USER=user
GITHUB_REPO_NAME=repository
GITHUB_REPO=user/repository
GITHUB_REPO_URL=https://github.com/user/repository
```
