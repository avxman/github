@extends('github.admin.layout')

@section('main')

    <div id="command" class="command">
        @if (($errors??false) && $errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @else
            Not command
        @endif
    </div>

@endsection

