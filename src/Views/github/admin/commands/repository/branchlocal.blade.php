<div id="command-line" class="command-line">
    <form id="form" class="form" data-link="{{$action??''}}">
        {{csrf_field()}}
        <div class="input-block w-100">
            <label for="event" class="label-size">Event:</label>
            <input id="event" class="input-size input" type="text" name="payload[event]" value="branchlocal" placeholder="Enter events name" readonly>
        </div>
        <button class="btn-send" type="submit">Send</button>
    </form>
</div>

<div id="command-result" class="command-result">

</div>
