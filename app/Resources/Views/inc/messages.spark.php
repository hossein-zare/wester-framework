@if($errors->any())
    <div class="alert alert-danger text-left">
        <h5 class="alert-heading">Warning!</h5>
    @foreach($errors->all() as $error)
        <div>{{ $error }}</div>
    @endforeach
    </div>
@endif

@if(ray('success'))
    <div class="alert alert-success text-left">
    @foreach(ray('success') as $message)
        <div>{{ $message }}</div>
    @endforeach
    </div>
@endif