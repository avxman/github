<?php

namespace Avxman\Github\Events;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 *
 * Работа с событиями Базой Данных с помощью Web
 *
 */
class DatabaseEvent extends BaseEvent
{

    protected array $allowMethods = ['import', 'export', 'backups', 'download'];

    /**
     * *Логин к БД
     * @var string $USER
     */
    protected string $USER = '';

    /**
     * *Пароль к БД
     * @var string $PASSWORD
     */
    protected string $PASSWORD = '';

    /**
     * *Имя БД
     * @var string $DATABASE
     */
    protected string $DATABASE = '';

    /**
     * *Порт к БД
     * @var string $PORT
     */
    protected string $PORT = '';

    /**
     * *Хост к БД
     * @var string $HOST
     */
    protected string $HOST = '';

    /**
     * *Пропускать ошибки при выявлении импорте или экспорте БД
     * @var string $SKIPERROR
     */
    protected string $SKIPERROR = '';

    /**
     * *Имя файла БД
     * @var string $FILE
     */
    protected string $FILE = '';

    /**
     * *Путь к папки для импорта или экспорта БД
     * @var string $FOLDER
     */
    protected string $FOLDER = '';

    /**
     * *Команда для архивирования БД
     * @var string $ZIP
     */
    protected string $ZIP = '';

    /**
     * *Получение пути к БД для импорта или экспорта
     * @return string
     */
    protected function getFolder() : string{
        if(!Storage::exists('database')){
            Storage::makeDirectory('database');
        }
        $folder = Storage::path('database');
        $this->getGitignore();
        return $folder;
    }

    /**
     * *Добавить файл .gitignore в папку с БД
     * @return void
     */
    protected function getGitignore() : void{
        if(!Storage::exists('database/.gitignore')){
            Storage::put('database/.gitignore', '!.gitignore');
        }
    }

    /**
     * *Генерация данных для работы с командами БД
     * @param array $data
     * @return void
     */
    protected function generateDatabaseData(array $data) : void{
        $this->USER = ' -u '.env('DB_USERNAME', 'root');
        $this->PASSWORD = env('DB_PASSWORD')?' -p\''.env('DB_PASSWORD').'\'':'';
        $this->DATABASE = ' '.env('DB_DATABASE', 'database.loc');
        $this->PORT = ' -P '.env('DB_PORT', '3306');
        $this->HOST = ' -h '.env('DB_HOST', 'localhost');
        $this->SKIPERROR = isset($data['skip_error'])?' -f':'';
        $this->FOLDER = $this->getFolder();
        $this->FILE = Str::slug(Str::replace(['.', '-'], '_', env('APP_NAME')), '_')
            .Carbon::now(Config()->get('app.timezone'))->format('_Y_m_d_H_i_s')
            .'.sql';
        if(isset($data['is_zip'])){
            $this->ZIP = isset($data['is_zip']) ? ' | gzip' : '';
            $this->FILE .= '.gz';
        }
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
     * Импорт БД
     * @param array $data
     * @return void
     */
    protected function import(array $data): void{
        //$this->commandRaw("mysqldump{$this->USER}{$this->PASSWORD}{$this->HOST}{$this->PORT}{$this->DATABASE} < {$this->FOLDER}/{$this->FILE}", true);
        $command = "В процессе написания кода импорта в Базу Данных";
        $this->writtingLog(
            'DatabaseEvent: %1, result: %2',
            ['%1', '%2'],
            ['import', $command]
        );
        $this->result = [$command];
    }

    /**
     * Экспорт БД
     * @param array $data
     * @return void
     */
    protected function export(array $data): void{
        $connecntion = $this->commandRaw("mysqldump{$this->USER}{$this->PASSWORD}{$this->HOST}{$this->PORT}{$this->DATABASE} -V", false);
        if(Str::contains($connecntion, 'Ver')) {
            $this->commandRaw("mysqldump{$this->USER}{$this->PASSWORD}{$this->HOST}{$this->PORT}{$this->DATABASE}{$this->ZIP} > {$this->FOLDER}/{$this->FILE}", true);
            $command = "База данных была создана {$this->FILE}. Рекомендуем перепроверить...";
        }
        else{
            $command = "Не удалось создать бэкап Базы Данных";
        }
        $this->writtingLog(
            'DatabaseEvent: %1, result: %2',
            ['%1', '%2'],
            ['export', $command]
        );
        $this->result = [$command];
    }

    /**
     * Список всех бэкапов БД
     * @param array $data
     * @return void
     */
    protected function backups(array $data) : void{
        $command = $this->commandRaw("ls {$this->FOLDER}");
        $this->writtingLog(
            'DatabaseEvent: %1, result: %2',
            ['%1', '%2'],
            ['backups', $command]
        );
        if(!empty($command) && ($links = explode(PHP_EOL, $command))){
            $url = request()->url();
            $command = ['type'=>'backups', 'links'=>[]];
            foreach ($links as $link){
                if(!empty($link)){
                    $command['links'][] = [
                        'url'=>Str::finish($url, Str::start($link, '?payload[event]=download&payload[url]=')),
                        'name'=>$link
                    ];
                }
            }
        }
        else $command = [$command];
        $this->result = $command;
    }

    /**
     * Скачивание БД
     * @param array $data
     * @return void
     */
    protected function download(array $data) : void{
        if($data['url']??false && !empty($data['url']) && Storage::exists('database/'.$data['url'])){
            if($data['delete']??false && (bool)$data['delete'] === true){
                if(Storage::delete('database/'.$data['url'])){
                    $this->result = ['type'=>'delete', 'delete'=>true, 'name'=>$data['url']];
                }
                else{
                    $this->result = ['type'=>'delete', 'delete'=>false, 'name'=>$data['url']];
                }
            }
            else{
                $this->result = ['type'=>'download', 'url'=>Storage::download('database/'.$data['url'])];
            }
            $this->writtingLog(
                'DatabaseEvent: %1, result: %2',
                ['%1', '%2'],
                ['download', 'Скачивание базы данных - '.$data['url']]
            );
        }
    }

    public function events(array $data) : bool{

        $this->is_event = true;
        $this->generateDatabaseData($data);
        $event = strtolower(request()->get('payload')['event']??'version');
        if($this->allowedMethod($event)) {$this->{$event}($data);}
        else {$this->is_event = false;}

        return $this->is_event;

    }

}
