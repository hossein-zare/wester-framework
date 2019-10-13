<div>
    <h2>{{ $subject }}</h2>
    <hr>
    <p>Please verify your account by the following link:</p>
    <a href="{{ host('/verify/' . $serial) }}">{{ host('/verify/' . $serial) }}</a>
</div>