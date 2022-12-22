<div id="command-line" class="command-line">
    <form id="form" class="form" data-link="{{$action??''}}">
        {{csrf_field()}}
        <div class="input-block w-50">
            <label for="event" class="label-size">Event:</label>
            <input id="event" class="input-size input" type="text" name="payload[event]" value="log" placeholder="Enter events name" readonly>
        </div>
        <div class="input-block w-50">
            <label for="count" class="label-size">*Counts log:</label>
            <input id="count" class="input-size input" type="number" name="payload[count]" min="1" max="100" value="3" placeholder="Enter count">
        </div>
        <button class="btn-send" type="submit">Send</button>
    </form>
</div>

<div id="command-result" class="command-result">

</div>
