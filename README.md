## PHP 7.* and Laravel 5.*

##### 1. Открыть консольную команду, перейти в корневую папку проекта и запустить набрав комманду
```composer
composer require avxman/github
```

##### 2. Добавить в config/app.php
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

##### 3. Запустить команду в командней строке в корневой папке проекта
```shell
php artisan vendor:publish --tag=avxman-github-all
```

##### 4. Добавить в app/Http/routes.php ближе к первым строкам
```text
\Avxman\Github\Routes\GithubRoute::allRoutes(Config()->get('github'));
```

##### 5. Добавить и настроить параметры в .env
```dotenv
#Github
GITHUB_ENABLED=true
#хеш (api) ключ добавляется в адресную строку, состоит из [0-9A-Za-z] не менее 64 символа
GITHUB_TOKEN=your_custom_token
#сектретный ключ в настройках репозитория в Github.com или Gitlab.com,
#набор символов состоящих из [0-9A-Za-z] не менее 32 символов
HTTP_X_GITHUB_SECRET=your_secret_from_github_deploy
#имя пользователя, который создаст репозиторий в Github.com или Gitlab.com
GITHUB_REPO_USER=user
#имя репозитория (проекта), который создаст пользователь Github.com или Gitlab.com
GITHUB_REPO_NAME=repository
#Путь репозитория (проекта) в Github.com или Gitlab.com
GITHUB_REPO=user/repository
#Url адрес репозитория (проекта)
GITHUB_REPO_URL=https://github.com/user/repository
```

##### 6. На локальной машине инициализируйте свой проект перейдя в корневую папку проекта и с помощью git запустите команду
```shell
git init
```

##### 7. Создать новый проект на Github.com или Gitlab.com у пользователя GITHUB_REPO_USER с именем репозитория GITHUB_REPO_NAME

##### 8. Создать связь между локальным проектом и созданным на Github.com или Gitlab.com

##### 9. Перенесите локальный проект на репозиторий Github.com или Gitlab.com

##### 10. Убедится в установке git на хостинге или сервере, где размещается сайт

##### 11. Разверните проект на хостинге (сервере):
* используя **git** команду *clone*
* используя копирование файлов через файлового менеджера и запуска инициализации **git init**

##### 12. Создайте ssh ключ на хостинге (сервере):
* автоматически. Настроить параметры добавив их в .env: GITHUB_PATH_SSH, GITHUB_PATH_CONFIG_SSH, GITHUB_PATH_NAME_SSH. Открыть ссылку в браузере https://your_domain/web/github/GITHUB_API_VERSION/GITHUB_TOKEN/registration . Скопировать полученный ssh-key (ssh-rsa....).
* ручной. Настроить ssh для пользователя имеющий право добавлять, редактировать, удалять файлы через файлового менеджера хостинга (сервера) и указать удаленное устройство *github.com*. Скопировать полученный ssh-key (ssh-rsa....).

##### 13. Github.com или Gitlab.com в настройках созданного репозитория (проекта) *"Deploy keys"* добавить новый ssh ключ указав: Title, Key. В поле Key вставляем скопированный ключ из 12 пункта.

##### 14. Github.com или Gitlab.com в настройках созданного репозитория (проекта) *"Webhooks"* добавляем новый webhook:
* Payload URL - https://your_domain/api/github/GITHUB_API_VERSION/GITHUB_TOKEN/GITHUB_REPO_USER@GITHUB_REPO_NAME/repository;
* Content type - application/json;
* Secret - набор символов состоящих из [0-9A-Za-z] не менее 32 символов взят из GITHUB_SECRET;
* SSL verification - Enable SSL verification;
* Which events would you like to trigger this webhook? - Let me select individual events: Branch or tag creation, Commit comments, Pushes, Releases;
* Active - Checked.

Сохраняем webhook

##### 14. Github.com или Gitlab.com в настройках созданного репозитория (проекта) *"Webhooks"* переходим во вкладу *"Recent Deliveries"* и проверяем привязку ...

##### 15. Готово. УРА :)

## Команды
```shell
# Создание ssh ключа для подключения к репозитории Github.com или Gitlab.com
https://your_domain/web/github/GITHUB_API_VERSION/GITHUB_TOKEN/registration

# Работа с БД
## Получить список базы данных
https://your_domain/web/github/GITHUB_API_VERSION/GITHUB_TOKEN/database?payload[event]=backups
## Скачать выбранную базу данных. payload[url] - имя файла
## Указать можно .sql или .gz если они существуют в данном расширении
https://your_domain/web/github/GITHUB_API_VERSION/GITHUB_TOKEN/database?payload[event]=download&payload[url]=name-db.sql
## Создать текущую базу данных
https://your_domain/web/github/GITHUB_API_VERSION/GITHUB_TOKEN/database?payload[event]=export

# Работа с репозиторием
## Проверить версию git
https://your_domain/web/github/GITHUB_API_VERSION/GITHUB_TOKEN/GITHUB_REPO_USER@GITHUB_REPO_NAME/repository?payload[event]=version
## Обновить репозиторий (проект) из Github.com или Gitlab.com
https://your_domain/web/github/GITHUB_API_VERSION/GITHUB_TOKEN/GITHUB_REPO_USER@GITHUB_REPO_NAME/repository?payload[event]=pull
## Переключить ветку для текущего сайта. Сайт будет загружен из указанной ветки. payload[branch] - имя ветки
https://your_domain/web/github/GITHUB_API_VERSION/GITHUB_TOKEN/GITHUB_REPO_USER@GITHUB_REPO_NAME/repository?payload[event]=checkout&payload[branch]=dev
## Проверить статус текущей ветки
https://your_domain/web/github/GITHUB_API_VERSION/GITHUB_TOKEN/GITHUB_REPO_USER@GITHUB_REPO_NAME/repository?payload[event]=status
## Получить логи. payload[count] - показывать количество логов
https://your_domain/web/github/GITHUB_API_VERSION/GITHUB_TOKEN/GITHUB_REPO_USER@GITHUB_REPO_NAME/repository?payload[event]=log&payload[count]=3
```
