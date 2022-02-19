<!doctype html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Результат ответа от github</title>
        <style>
            html,body{
                height: 100%;
                margin: 0;
                padding: 0;
            }
            body{
                background: black;
            }
            *{
                box-sizing: border-box;
            }
            #result{
                display: flex;
                align-content: center;
                justify-content: center;
                max-width: 1140px;
                width: 100%;
                flex: 1 0 0;
                margin: 0 auto;
                height: 100%;
            }
            .result-text{
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center;
            }
            .result-text ul{
                list-style: none;
                padding: 15px;
                background: brown;
                color: blanchedalmond;
                box-shadow: 0 0 10px 2px #dc1b1b;
                text-align: left;
                word-break: break-all;
            }
            .result-text .tab{
                margin-right: 30px;
            }
            .line-space{
                margin: 15px 0;
                border: 1px dashed #f5f482;
            }
            .line-text{

            }
            .result-text .show-list{
                list-style: decimal;
            }
            .result-text .show-list li{
                padding-left: 10px;
                margin-bottom: 8px;
                display: flex;
                align-items: center;
            }
            .result-text .show-list li:last-child{
                margin-bottom: 0;
            }
            .show-list li a{
                margin-left: 15px;
            }
            .show-list li a span{
                width: 40px;
                height: 40px;
                display: inline-block;
            }
            .show-list li a span::before{
                content: '\2912';
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: rgb(21 143 41 / 75%);
                border-radius: 8px;
                filter: drop-shadow(-2px -4px 6px black);
                transform: rotateZ(180deg);
                color: darkorange;
                font-size: 26px;
                transition: all 0.2s;
            }
            .show-list li a span:hover::before{
                background: rgba(29, 196, 56, 0.75);
                filter: drop-shadow(-2px -4px 6px #751010);
                transition: all 0.2s;
            }
            .line-text::before{
                content: '◾';
                width: 8px;
                height: 8px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: darkorange;
                margin-right: 6px;
                font-size: 10px;
            }
            .title-download{
                text-align: center;
                justify-content: center;
                font-size: 22px;
                color: chartreuse;
                border-bottom: 1px dashed;
                margin-bottom: 15px;
                padding-bottom: 10px;
            }
            .delete{

            }
            .show-list li a.delete span::before{
                content: '\274C';
                background: rgb(58 40 40 / 75%);
                filter: drop-shadow(-2px -4px 6px black);
                color: darkorange;
                font-size: 24px;
            }
            .show-list li a.delete span:hover::before{
                background: rgb(196 29 29 / 75%);
                filter: drop-shadow(-2px -4px 6px #751010);
            }
        </style>
    </head>
    <body>
        <div id="result">
            <div class="result-text">
                <ul{!! ($type??false) ? ' class="show-list"' : '' !!}>
                    @if($type??false && $type === 'backups')
                        <li class="title-download">Список Баз Данных</li>
                    @endif
                    @if($result->count())
                        @foreach($result as $message)
                            @continue($loop->last && empty($message))
                            <li class="{{empty($message) ? 'line-space' : 'line-text'}}">
                                @if($type??false)
                                    @if($type === 'delete')
                                        {{$message}}
                                    @else
                                        {{$message['name']}}
                                        <a class="download" href="{{$message['url']}}" target="_blank" title="Скачать"><span></span></a>
                                        <a class="delete" href="{{$message['url']}}&payload[delete]=1" target="_blank" title="Удалить"><span></span></a>
                                    @endif
                                @else
                                    {!! \Illuminate\Support\Str::contains($message, "\t") ? "<span class=\"tab\"></span>" : "" !!}
                                    {{$message}}
                                @endif
                            </li>
                        @endforeach
                    @else
                        <li class="line-text">Результат не найден</li>
                    @endif
                </ul>
            </div>
        </div>
    </body>
</html>
