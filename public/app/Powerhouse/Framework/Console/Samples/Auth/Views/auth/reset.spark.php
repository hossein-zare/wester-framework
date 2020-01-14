@extends('layouts/app')

@section('title', 'Reset password')

@section('content')
	<main role="main" class="d-flex py-5 align-items-center">
		<div class="container">
			<div class="text-center mx-auto col-md-6 p-4 rounded-lg shadow">

                @include('inc/messages')

                <form method="post" action="{{ route('resetpassword') }}" class="text-left">
                    @csrf

                    <div class="form-group">
                        <label for="email">@lang('Email address')</label>
                        <input type="email" class="form-control" name="email" id="email" value="{{ old('email') }}" aria-describedby="emailHelp" placeholder="@lang('Enter email address')">                    </div>

                    <button type="submit" class="btn btn-primary">@lang('Reset')</button>
                </form>
			</div>
		</div>
	</main>
@endsection