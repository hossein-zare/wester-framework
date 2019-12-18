@extends('layouts/app')

@section('title', 'Login')

@section('content')
	<main role="main" class="d-flex py-5 align-items-center">
		<div class="container">
			<div class="text-center mx-auto col-md-6 p-4 rounded-lg shadow">

                @include('inc/messages')

                <form method="post" action="{{ route('login') }}" class="text-left">
                    @csrf

                    <div class="form-group">
                        <label for="email">@lang('Email address')</label>
                        <input type="email" class="form-control" name="email" id="email" value="{{ old('email') }}" aria-describedby="emailHelp" placeholder="@lang('Enter email address')">
                    </div>
                    <div class="form-group">
                        <label for="password">@lang('Password')</label>
                        <input type="password" class="form-control" name="password" id="password" placeholder="@lang('Password')">
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" name="remember" id="remember">
                        <label class="form-check-label" for="remember">@lang('Remember me')</label>
                    </div>

                    <button type="submit" class="btn btn-primary">@lang('Login')</button> <a href="{{ route('resetpassword') }}" class="ml-2">@lang('Forgot your password?')</a>
                </form>
			</div>
		</div>
	</main>
@endsection