<div>
    <h2>{{ $subject }}</h2>
    <hr>
    <p>If you've asked to reset your password please click the following link, otherwise ignore it:</p>
    <a href="{{ host('/password/reset/' . $serial) }}">{{ host('/password/reset/' . $serial) }}</a>
</div>