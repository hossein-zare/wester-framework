@extends('layouts/app')

@section('title', 'Register')

@section('content')
	<main role="main" class="d-flex py-5 align-items-center">
		<div class="container">
			<div class="text-center mx-auto col-md-6 p-4 rounded-lg shadow">

                @include('inc/messages')

                <form method="post" action="{{ route('register') }}" class="text-left">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="first_name">@lang('First name')</label>
                        <input type="text" class="form-control" name="first_name" id="first_name" value="{{ old('first_name') }}" placeholder="@lang('Enter first name')">
                    </div>
                    <div class="form-group">
                        <label for="last_name">@lang('Last name')</label>
                        <input type="text" class="form-control" name="last_name" id="last_name" value="{{ old('last_name') }}" placeholder="@lang('Enter last name')">
                    </div>
                    <div class="form-group">
                        <label for="email">@lang('Email address')</label>
                        <input type="email" class="form-control" name="email" id="email" value="{{ old('email') }}" aria-describedby="emailHelp" placeholder="@lang('Enter email address')">
                        <small id="emailHelp" class="form-text text-muted">@lang('We\'ll never share your email with anyone else.')</small>
                    </div>
                    <div class="form-group">
                        <label for="password">@lang('Password')</label>
                        <input type="password" class="form-control" name="password" id="password" placeholder="@lang('Password')">
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">@lang('Confirm password')</label>
                        <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="@lang('Confirm password')">
                    </div>

                    <button type="submit" class="btn btn-primary">@lang('Register')</button>
                </form>
			</div>
		</div>
	</main>
@endsection