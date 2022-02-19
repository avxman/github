<?php

namespace Avxman\Github\Logs;

use Avxman\Github\Messages\GithubMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;

/**
 *
 * Логирование
 *
*/
class AllLog
{

    /**
     * *Конфигурационные данных
     * @var array $config
    */
    protected array $config = [];

    /**
     * *Путь к файлу лога
     * @var string $dir
     */
    protected string $dir = '';

    /**
     * *Имя файла лога
     * @var string $name
     */
    protected string $name = 'github.log';

    /**
     * *Полный путь к файлу лога
     * @var string $full_name
     */
    protected string $full_name = '';

    /**
     * *Максимальный размер файла
     * @var int $size
     */
    protected int $size = 1024000;

    /**
     * *Работа с ошибками
     * @var GithubMessage $message
     */
    protected GithubMessage $message;

    /**
     * Формат даты
     * @var string $date_format
     */
    protected string $date_format = 'd.m.Y H:i:s';

    /**
     * Получаем текущую дату
     * @return string
    */
    protected function getDate() : string{
        return Carbon::now(Config()->get('app.timezone'))->format($this->date_format);
    }

    /**
     * Проверка на перезапись лога
     * если файл привышает максимальный размер файла
     * @return bool
     */
    protected function rewrite() : bool{
        return !File::exists($this->full_name) || File::size($this->full_name) >= $this->size;
    }

    /**
     * Конструктор
     * @param array $config
     */
    public function __construct(array $config){

        $this->config = $config;
        $this->dir = storage_path('logs');
        $this->full_name = $this->dir.'/'.$this->name;
        $this->message = new GithubMessage($this->config['IS_DEBUG']??false);

    }

    /**
     * Записываем данные в файл лога
     * @param string $text
     * @return void
     */
    public function write(string $text) : void{
        $text = $this->getDate().': '.$text.PHP_EOL;
        $status = $this->rewrite() ? File::put($this->full_name, $text) : (bool)File::append($this->full_name, $text);
        if(!$status){
            $this->message->setMessage('Не удалось сохранить данные в лог файл')->error();
        }
    }

    /**
     * Считываем данные из файла лога
     * @return string
     */
    public function read() : string{
        if(empty($file = File::get($this->full_name))){
            $this->message->setMessage('Не удалось открыть файл логов')->error();
        }
        return $file;
    }

}
