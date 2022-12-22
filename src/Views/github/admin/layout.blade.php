<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin github</title>
    {{-- Стили --}}
    @include('github.admin.includes.css')
</head>
<body>

<div class="layout">

    {{-- Шапка --}}
    <header id="header" class="header">

        <div class="header-container">
            <div class="h-left">
                <a href="https://github.com/avxman/github" title="Libriary">
                    Lib
                </a>
            </div>
            <div class="h-right">
                <a href="https://github.com/Doroshenko-agency">
                    Organization
                </a>
                <a href="https://github.com/avxman">
                    Developer
                </a>
            </div>
        </div>

    </header>

    {{-- Контент --}}
    <main id="main" class="main">

        {{-- Левая колонка --}}
        <aside id="left" class="left">
            @include('github.admin.menu.index')
        </aside>

        {{-- Правая колонка --}}
        <aside id="right" class="right">
            <div id="content" class="content">
                @yield('main')
            </div>
        </aside>

    </main>

    {{-- Подвал --}}
    <footer id="footer" class="footer">
        <div class="copyright">
            &copy 2022-2023. License MIT
        </div>
    </footer>

</div>

{{-- Скрипты --}}
@include('github.admin.includes.js')

</body>
</html>
