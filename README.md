## PHP 7.* and Laravel 5.*

### Добавляем библиотеку в проект

##### 1. Открываем консольную команду, переходим в корневую папку проекта и запускаем комманду
```composer
composer require avxman/github
```

##### 2. Добавляем код в config/app.php
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

##### 3. Запускаем команду в командней строке в корневой папке проекта
```shell
php artisan vendor:publish --tag=avxman-github-all
```

##### 4. Добавляем код в app/Http/routes.php ближе к первым строкам
```text
\Avxman\Github\Routes\GithubRoute::allRoutes(Config()->get('github'));
```

##### 5. Добавляем и настраиваем параметры в .env
```dotenv
#Github
#включить работу Github
GITHUB_ENABLED=true
#хеш (api) ключ добавляется в адресную строку, состоит из [0-9A-Za-z] не менее 64 символа
GITHUB_TOKEN=your_custom_token
#сектретный ключ в настройках репозитория в Github.com или Gitlab.com,
#набор символов состоящих из [0-9A-Za-z] не менее 32 символов
GITHUB_SECRET=your_secret_from_github_deploy
#имя пользователя, который создаст репозиторий в Github.com или Gitlab.com
GITHUB_REPO_USER=user
#имя репозитория (проекта), который создаст пользователь Github.com или Gitlab.com
GITHUB_REPO_NAME=repository
#Путь репозитория (проекта) в Github.com или Gitlab.com
GITHUB_REPO=user/repository
#Url адрес репозитория (проекта)
#Если проект приватный, тогда указываем ssh ссылку
#git@github.com:user/repository.git
GITHUB_REPO_URL=https://github.com/user/repository.git
```

##### 6. Настраиваем игнорирование папок/файлов в файле .gitignore (исключить файлы привышающие 100Мб и папки/файлы, которые создаются или перезаписываются с помощью админки или скриптами)

##### 7. Удаляем из текста папку /vendor в файле .gitignore, если она указана

### Настраиваем связь с хостингом или сервером

#### 1. Проект не инициализирован на локальной машине (если инициализирован пропускаем данный пункт)
##### 1.1. На локальной машине инициализируйте свой проект перейдя в корневую папку проекта и с помощью git запускаем команду (git должен быть установлен на локальной машине)
```git
git init
git add .
git commit -m "Инициализация проекта"
```

##### 1.2. Создаем новый проект на Github.com или Gitlab.com у пользователя GITHUB_REPO_USER с именем репозитория GITHUB_REPO_NAME

##### 1.3. Создаем связь между локальным проектом и созданным на Github.com или Gitlab.com. Переносим локальный проект на репозиторий Github.com или Gitlab.com испольуя команды:
```git
git remote add origin ссылка_на_удалённый_репозиторий
git push -u origin master
```
Ошибка при выгрузки проекта на удалённый репозиторий - читаем ниже блок "Заметки".
Ошибка на текст `master` - проблема в названии ветки. Ветка "master" не существует,
больше всего у Вас ветка имеет другое название, к примеру, `main`

#### 2. Проект инициализирован на локальной машине

##### 2.1. Создаем связь между локальным и Github.com(Gitlab.com) проектом используя команды:
```git
git remote add origin ссылка_на_удалённый_репозиторий
git push -u origin master
```
Ошибка при выгрузки проекта на удалённый репозиторий - читаем ниже блок "Заметки".
Ошибка на текст `master` - проблема в названии ветки. Ветка "master" не существует,
больше всего у Вас ветка имеет другое название, к примеру, `main`

##### 3. Убеждаемся в установке git на хостинге или сервере, где размещается код сайта

##### 4. Развернуть проект на хостинге (сервере):
* используя **git** команду *clone* ```git clone```
* используя копирование файлов через файлового менеджера и команды запуска инициализации проекта git ```git init```. Данный пункт не рекомендуется использовать

##### 5. Создаем ssh ключ на хостинге (сервере):
* Автоматическая настройка. Настраиваем параметры добавляя их в .env: GITHUB_PATH_SSH, GITHUB_PATH_CONFIG_SSH, GITHUB_PATH_NAME_SSH. Открываем ссылку в браузере https://your_domain/web/github/GITHUB_API_VERSION/GITHUB_TOKEN/registration . Копируем полученный ssh-key (ssh-rsa....). Не получается настроить, читаем ниже блок "Заметка"
* Ручная настройка. Настраиваем ssh для пользователя имеющий право добавлять, редактировать, удалять файлы через файлового менеджера хостинга (сервера) и указываем удаленное устройство *github.com*. Копируем полученный ssh-key (ssh-rsa....).

##### 6. Github.com или Gitlab.com в настройках созданного репозитория (проекта) *"Deploy keys"* добавляем новый ssh ключ указав: Title, Key. В поле Key вставляем скопированный ключ из 5 пункта.

##### 7. Github.com или Gitlab.com в настройках созданного репозитория (проекта) *"Webhooks"* добавляем новый webhook:
* Payload URL - https://your_domain/api/github/GITHUB_API_VERSION/GITHUB_TOKEN/GITHUB_REPO_USER@GITHUB_REPO_NAME/repository;
* Content type - application/json;
* Secret - набор символов состоящих из [0-9A-Za-z] не менее 32 символов взят из GITHUB_SECRET;
* SSL verification - Enable SSL verification;
* Which events would you like to trigger this webhook? - Let me select individual events: Branch or tag creation, Commit comments, Pushes, Releases;
* Active - Checked.

Сохраняем webhook

##### 8. Github.com или Gitlab.com в настройках созданного репозитория (проекта) *"Webhooks"* переходим во вкладу *"Recent Deliveries"* и проверяем привязку ...

##### 15. Готово. УРА :)

### Копирование существующего проекта с Github (Gitlab) и связать его уже с размещенным проектом на хостинге, где установлена и настроена библиотека avxman/github
1. С помощью консольной команды перейти в пустую папку. В эту папку будем копировать проект. При желании можно выбрать уже имеющую или создать новую.
2. В консольной команде набрать код **git clone ссылка_на_проект_в_github** (пример ссылки: **[https://github.com/имя_пользователя_или_организации/имя_проекта.git]()**; **[git@github.com:имя_пользователя_или_организации/имя_проекта.git]()**. Ошибка при копировании - читаем ниже блок "Заметки"
3. Подымаем базу данных. Копия базы данных может находится в папке /database или /storage/app/database. При отсутствии копии, скачиваем её из оригинала сайта
4. После копирования, переходим в папку с проектом, открываем корневой файл .gitignore и с размещенного проекта на хостинге копируем недостающие файлы (папки).
5. Выполняем свои настройки в файлах: .env; /public/.htaccess; /public/robots.txt. Файлы отсутствуют - создать и перенести все данных из файлов имеющие одинаковые название со суфиксом **-backups**
6. В .env файле обязательно заполняем параметры связаны с библиотекой avxman/github (пункт 5, "Добавляем и настраиваем параметры в .env")
7. Ключ GITHUB_SECRET в .env файле должен соответствовать ключу GITHUB_SECRET в .env файле из размещенного проекта на хостинге
8. Готово. NICE :)

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
## Создать текущую базу данных (работает только на OS Linux)
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
## Получить логи. payload[count] - показывает количество записей из лога
https://your_domain/web/github/GITHUB_API_VERSION/GITHUB_TOKEN/GITHUB_REPO_USER@GITHUB_REPO_NAME/repository?payload[event]=log&payload[count]=3
```

### Заметка
#### Переключение ветки сайта не соотвествует версии ветки из Github (Gitlab)
Причина может быть, из-за отсутствии последних изменений версии из репозитория github (gitlab).
Решает проблему команда запуска обновления репозитория (проекта) из Github (Gitlab) с помощью адресной строки
``https://your_domain/web/github/GITHUB_API_VERSION/GITHUB_TOKEN/GITHUB_REPO_USER@GITHUB_REPO_NAME/repository?payload[event]=pull``
#### Не получается скопировать или отправить изменения проекта на Github (Gitlab)
На Github (Gitlab) в настройке проекта (репозитория) установлен параметр видимости как "приватный (private)". В связи с этим,
требуется настроить доступ к проекту (репозиторию). Существует несколько способов:
- В настройках проекта во вкладке Deploy keys - добавить ssh ключ локальной машины и выбрав галочку "Allow write access"
- Использовать "Personal access tokens" из настроек аккаунта Github (Gitlab). В параметре "Select scopes" установить галочки: gist, read:org, repo, workflow. При копировании проекта указываем путь: [https://personal_access_token@github.com/name_user_or_name_organization/name_repository](). При ошибки команды `git push` или похожих команд, нужно в файле /.git/config текущего проекта заменить ключ `url = ...` на `url = https://personal_access_token@github.com/name_user_or_name_organization/name_repository`
#### Не удалось автоматически настроить ssh ключ
Появилась ошибка о неудачном копировании ssh ключа в операционную систему!
Решение: в файле /.env изменяем значения ключам:
- GITHUB_PATH_NAME_SSH=указываем_другое_имя_файлу_ssh_ключам (к примеру, repository_github)
- GITHUB_USER_NAME_SSH=указываем_другое_имя_подключаемого_пользователя (к примеру, avxman - имя пользователя на github.com)
- GITHUB_REPO_URL=указываем_другую_ссылку_на_удалённый_репозиторий (используем тип подключение "ssh" заменив слово "github.com" на значение из ключа GITHUB_USER_NAME_SSH - git@GITHUB_USER_NAME_SSH:GITHUB_REPO_USER/GITHUB_REPO_NAME.git)
