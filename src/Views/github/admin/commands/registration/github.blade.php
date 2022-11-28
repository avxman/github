<div id="command-line" class="command-line">
    <form id="form" class="form" data-link="{{$action??''}}">
        {{csrf_field()}}
        <div class="input-block w-50">
            <label for="event" class="label-size">Event:</label>
            <input id="event" class="input-size input" type="text" name="payload[event]" value="registration" placeholder="Enter events name" readonly>
        </div>
        <div class="input-block w-50">
            <label for="config_ssh" class="label-size">*Path ssh configuration:</label>
            <input id="config_ssh" class="input-size input required" type="text" name="payload[config_ssh]" value="{{Config('github.GITHUB_PATH_CONFIG_SSH')}}" placeholder="Enter path">
        </div>
        <div class="input-block w-50">
            <label for="path_ssh" class="label-size">*Path ssh file:</label>
            <input id="path_ssh" class="input-size input required" type="text" name="payload[path_ssh]" value="{{Config('github.GITHUB_PATH_SSH')}}" placeholder="Enter path">
        </div>
        <div class="input-block w-50">
            <label for="name_ssh" class="label-size">*Name ssh file:</label>
            <input id="name_ssh" class="input-size input required" type="text" name="payload[name_ssh]" value="{{Config('github.GITHUB_PATH_NAME_SSH')}}" placeholder="Enter name">
        </div>
        <button class="btn-send" type="submit">Send</button>
    </form>
</div>

<div id="command-result" class="command-result">

</div>
