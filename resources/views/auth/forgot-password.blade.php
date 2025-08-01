@extends('layouts.guest')

@section('content')
<form class="login100-form validate-form" method="POST" action="{{ route('password.email') }}">
    @csrf
    
    <span class="login100-form-title p-b-49">
        Forgot Password
    </span>

    @if (session('status'))
        <div class="bg-green-500/10 border border-green-500/20 text-green-500 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('status') }}
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-500/10 border border-red-500/20 text-red-500 rounded-lg p-4 mb-6">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="wrap-input100 validate-input m-b-23" data-validate="Email is required">
        <span class="label-input100">Email Address</span>
        <input class="input100" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Type your email">
        <span class="focus-input100" data-symbol="&#xf206;">
            <i class="fas fa-envelope"></i>
        </span>
        @error('email')
            <p class="text-red-500 text-sm mt-2 flex items-center">
                <i class="fas fa-exclamation-circle mr-1"></i>
                {{ $message }}
            </p>
        @enderror
    </div>

    <div class="container-login100-form-btn">
        <div class="wrap-login100-form-btn">
            <div class="login100-form-bgbtn"></div>
            <button class="login100-form-btn" type="submit">
                Send Password Reset Link
            </button>
        </div>
    </div>

    <div class="txt1 text-center p-t-54 p-b-20">
        <span>
            Remember your password?
        </span>
    </div>

    <div class="flex-col-c p-t-155">
        <span class="txt1 p-b-17">
            Sign in to your account
        </span>

        <a href="{{ route('login') }}" class="txt2">
            Sign In
        </a>
    </div>
</form>
@endsection 