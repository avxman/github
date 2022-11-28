@if($menu)

    <ul class="children">

        @foreach($menu as $k=>$link)

            @if($link->get('enabled'))

                <li class="child">

                    <a class="link" data-href="{{$link->get('link')}}" data-group="{{$key}}" data-view="{{$k}}" data-description="{{$link->get('description')}}" title="{{$link->get('description')}}">
                        {{$link->get('name')}}
                    </a>

                </li>

            @endif

        @endforeach

    </ul>

@endif
