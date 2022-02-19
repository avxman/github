<?php

return [

    // Вкл./Откл. работу через Github
    'GITHUB_ENABLED'=>env('GITHUB_ENABLED', true),

    // Вывод результатов в логи
    'IS_DEBUG'=>env('GITHUB_DEBUG', true),

    // Github токен для работы с Webhooks
    'GITHUB_TOKEN'=>env('GITHUB_TOKEN', ''),

    // Github секретный ключ для работы с Webhooks
    'HTTP_X_GITHUB_SECRET'=>env('GITHUB_SECRET', ''),

    // Имя пользователя удалённого репозитория для получения и отправки данных, git remove add name/repository
    'GITHUB_REPO_USER'=>env('GITHUB_REPO_USER', ''),

    // Имя удалённого репозитория для получения и отправки данных, git remove add name/repository
    'GITHUB_REPO_NAME'=>env('GITHUB_REPO_NAME', ''),

    // Ссылка удалённого репозитория для получения и отправки данных, git remove add name/repository url-repository
    // https://github.com/user/test.git
    // или git@github.com:user/test.git
    'GITHUB_REPO_URL'=>env('GITHUB_REPO_URL', ''),

    // Полный путь к папке проекта, где инициализирован git
    // указываем если на хостинге или сервере используется несколько репозиторий
    'GITHUB_ROOT_FOLDER'=>env('GITHUB_ROOT_FOLDER', ''),

    // Версия библиотеки указана в адресной строке
    'GITHUB_API_VERSION'=>env('GITHUB_API_VERSION', 'v1'),

    // Максимальное количество запросов в минуту
    'GITHUB_MAX_RATE'=>env('GITHUB_MAX_RATE', 60),

    // Вкл./Откл. автоматическое обновление через репозиторий github с помощью Webhook
    'GITHUB_AUTO_WEBHOOK'=>env('GITHUB_AUTO_WEBHOOK', true),

    // Путь к папке с ssh ключами
    'GITHUB_PATH_SSH'=>env('GITHUB_PATH_SSH', '~/.ssh'),

    // Конфигурационный файл для ssh агента
    'GITHUB_PATH_CONFIG_SSH'=>env('GITHUB_PATH_CONFIG_SSH', '~/.ssh/config'),

    // Имя файла, куда будут записаны ключи
    'GITHUB_PATH_NAME_SSH'=>env('GITHUB_PATH_NAME_SSH', 'github_key'),

];
