<div id="command-line" class="command-line">
    <form id="form" class="form" data-link="{{$action??''}}">
        {{csrf_field()}}
        <div class="input-block w-50">
            <label for="event" class="label-size">Event:</label>
            <input id="event" class="input-size input" type="text" name="payload[event]" value="checkout" placeholder="Enter events name" readonly>
        </div>
        <div class="input-block w-50">
            <label for="branch" class="label-size">*Branch:</label>
            <input id="branch" class="input-size input required" type="text" name="payload[config_ssh]" value="" placeholder="Enter branchs name">
        </div>
        <button class="btn-send" type="submit">Send</button>
    </form>
</div>

<div id="command-result" class="command-result">

</div>
