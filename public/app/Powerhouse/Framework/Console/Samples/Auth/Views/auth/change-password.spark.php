@extends('layouts/app')

@section('title', 'ChangePassword')

@section('content')
	<main role="main" class="d-flex py-5 align-items-center">
		<div class="container">
			<div class="text-center mx-auto col-md-6 p-4 rounded-lg shadow">

                @include('inc/messages')

                <form method="post" action="{{ route('resetchangepassword', ['serial'=>$serial]) }}" class="text-left">
                    @csrf
                    @method('PATCH')

                    <div class="form-group">
                        <label for="email">@lang('New password')</label>
                        <input type="password" class="form-control" name="password" id="password" value="" placeholder="@lang('Enter new password')">
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">@lang('Confirmation')</label>
                        <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" value="" placeholder="@lang('Retype the password')">
                    </div>
                    <button type="submit" class="btn btn-primary">@lang('Change')</button>
                </form>
			</div>
		</div>
	</main>
@endsection