<?php

namespace Avxman\Github\Events;

use Illuminate\Support\Str;

/**
 *
 * Работа с событиями Авторизации пользователя и Регистрации репозитория
 *
 */
class RegistrationEvent extends BaseEvent
{

    protected array $allowMethods = ['registration'];

    protected function commandGenerate(string $command) : string{
        return $this->command("git{$this->addGithubFolder()}{$command}{$this->commandLineLog()}");
    }

    /**
     * *Вызов команды без обработки (сырые запросы)
     * @param string $command
     * @param bool $not_log
     * @return string
     */
    protected function commandRaw(string $command, bool $not_log = false) : string{
        $line = $not_log ? '' : $this->commandLineLog();
        return $this->command("{$command}{$line}");
    }

    /**
     * Авторизируем пользователя и регистрируем репозиторий
     * @param array $data
     * @return void
     * @throws \ErrorException
     */
    protected function registration(array $data) : void{
        $message = '';
        $skip_log = false;
        // Инициализация параметров
        $email_github = 'git@github.com';
        $config_ssh = $data['config_ssh'];
        $path_private = Str::finish(Str::finish($data['path_ssh'], '/'), $data['name_ssh']);
        $path_public = Str::finish(Str::finish($data['path_ssh'], '/'), Str::finish($data['name_ssh'], '.pub'));

        // Проверка на права записи через ssh ключ
        $status = $this->commandRaw("ssh -T $email_github");
        $is_auth = Str::contains($status, Str::finish($this->config['GITHUB_REPO_USER'], Str::start($this->config['GITHUB_REPO_NAME'], '/')));

        // Отключаем параллельную индексацию файлов в слабых системах
        $this->commandGenerate('config core.preloadIndex false');

        if(!$is_auth){

            // Получаем имя пользователя системы
            $user = $this->commandRaw("ls -ld {$this->addGithubFolder()} | cut -d' ' -f3");

            // Проверяем ssh ключи на существования (private, public)
            // private
            $file_exists = $this->commandRaw("ls -d $path_private");
            if(!Str::contains(Str::lower($file_exists), 'no such file')){
                $this->commandRaw("rm $path_private");
            }
            // public
            $file_exists = $this->commandRaw("ls -d $path_public");
            if(!Str::contains(Str::lower($file_exists), 'no such file')){
                $this->commandRaw("rm $path_public");
            }

            // Генерируем ssh ключ
            $ssh_keygen = $this->commandRaw("ssh-keygen -f $path_private -C '$email_github' -t rsa -b 4096 -P '' -N ''");

            // Читаем ssh ключ и выводим текст для ввода в github
            if(Str::contains($ssh_keygen, Str::replaceFirst('~', '', $path_private))){
                // Добавляем новый ssh ключ в систему
                $ssh_add = $this->commandRaw("ssh-copy-id -i $path_private $email_github");
                if(!Str::contains(Str::lower($ssh_add), 'error')){
                    //
                    $config_ssh_next = true;
                    $file_exists = $this->commandRaw("ls -d $config_ssh");
                    if(Str::contains(Str::lower($file_exists), 'no such file')){
                        $this->commandRaw('echo -e \'Host github.com\' > '.$config_ssh);
                        $this->commandRaw('echo -e \'\tUser '.$user.'\' >> '.$config_ssh);
                        $this->commandRaw('echo -e \'\tIdentityFile '.$path_private.'\' >> '.$config_ssh);
                        $this->commandRaw("chmod 600 $config_ssh");
                        $this->commandRaw("chown $user $config_ssh");
                    }
                    else{
                        $config_ssh_file = $this->commandRaw("cat $config_ssh");
                        if(Str::contains(Str::lower($config_ssh_file), Str::lower('Host github.com'))){
                            $ssh_file = $this->commandRaw("cat $path_public");
                            $message = "В конфигурационном файле config уже найден Host github.com." .PHP_EOL
                                ."Чтобы изменить файл нужно сделать это вручную"
                                .(Str::contains($ssh_file, 'ssh-rsa')
                                    ? PHP_EOL."Скопировать нижеуказанный текст и вставить в github репозитория:".PHP_EOL.PHP_EOL.$ssh_file.PHP_EOL
                                    :''
                                );
                            $config_ssh_next = false;
                        }
                        else{
                            $this->commandRaw('echo -e \'\nHost github.com\' >> '.$config_ssh);
                            $this->commandRaw('echo -e \'\tUser '.$user.'\' >> '.$config_ssh);
                            $this->commandRaw('echo -e \'\tIdentityFile '.$path_private.'\' >> '.$config_ssh);
                            $this->commandRaw("chmod 600 $config_ssh");
                            $this->commandRaw("chown $user $config_ssh");
                        }
                    }
                    // Конфигурационный файл был изменён, выводим ключ
                    if($config_ssh_next){
                        // Читаем открытый ssh ключ и выводим на экран для возможности вставки его в github репозиторий
                        $ssh_file = $this->commandRaw("cat $path_public");
                        $message = "Скопировать нижеуказанный текст и вставить в github репозитория:".PHP_EOL.PHP_EOL.$ssh_file.PHP_EOL;
                    }
                }
                else{
                    $message = "Не удалось добавить ssh ключ в систему: ".Str::afterLast($ssh_add, 'ERROR');
                }
                $skip_log = true;
            }
            else{$message = "Не удалось создать ssh ключ";}

            // Результат
            $command = [$message];

        }
        else{
            $ssh_file = $this->commandRaw("cat $path_public");
            if(Str::contains(Str::lower($ssh_file), 'no such file')){
                $message = "Указанный файл с ключом не найден : ( $path_public ) нужно смотреть более детально через консольную панель";
            }
            else{
                $message = "Публичный ключ находится в файле: $path_public"
                    .PHP_EOL
                    .(Str::contains($ssh_file, 'ssh-rsa')
                        ? PHP_EOL."Скопировать нижеуказанный текст и вставить в github репозитория:".PHP_EOL.PHP_EOL.$ssh_file.PHP_EOL
                        :''
                    );;
            }

            // Результат
            $command = ["Пользователь github подключён и авторизирован для данного проекта на этом сервере (хостинге).".PHP_EOL.$message];
        }

        if(!$skip_log) {
            $this->writtingLog(
                'RegistrationEvent: %1, result: %2',
                ['%1', '%2'],
                ['registration', implode(', ', $command)]
            );
        }
        $this->result = $command;
    }


    public function events(array $data) : bool{

        $this->is_event = true;
        $this->registration($data);

        return $this->is_event;

    }

}
