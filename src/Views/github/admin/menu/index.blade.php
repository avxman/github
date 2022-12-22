@if($result->has('menu'))

    <nav class="menu-command">

        <ul class="menu-command-list">

            @foreach($result->get('menu') as $key=>$parent)

                @if($parent->get('enabled'))

                    <li class="parent">

                        <a title="{{$parent->get('description')}}">{{$parent->get('name')}}</a>
                        @if($parent->has('children'))
                            <span class="arrow"></span>
                            @include('github.admin.menu._menu', ['menu'=>$parent->get('children')])
                        @endif

                    </li>

                @endif

            @endforeach

        </ul>

    </nav>

@endif
