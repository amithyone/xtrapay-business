@extends('layouts.guest')

@section('content')
<form class="login100-form validate-form" method="POST" action="{{ route('register') }}">
    @csrf
    
    <span class="login100-form-title p-b-49">
        Create Account
    </span>

    @if($errors->any())
        <div class="bg-red-500/10 border border-red-500/20 text-red-500 rounded-lg p-4 mb-6">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="wrap-input100 validate-input m-b-23" data-validate="Name is required">
        <span class="label-input100">Full Name</span>
        <input class="input100" type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Type your full name">
        <span class="focus-input100" data-symbol="&#xf206;">
            <i class="fas fa-user"></i>
        </span>
        @error('name')
            <p class="text-red-500 text-sm mt-2 flex items-center">
                <i class="fas fa-exclamation-circle mr-1"></i>
                {{ $message }}
            </p>
        @enderror
    </div>

    <div class="wrap-input100 validate-input m-b-23" data-validate="Email is required">
        <span class="label-input100">Email Address</span>
        <input class="input100" type="email" name="email" value="{{ old('email') }}" required placeholder="Type your email">
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

    <div class="wrap-input100 validate-input m-b-23" data-validate="Password is required">
        <span class="label-input100">Password</span>
        <input class="input100" type="password" name="password" required placeholder="Type your password">
        <span class="focus-input100" data-symbol="&#xf190;">
            <i class="fas fa-lock"></i>
        </span>
        @error('password')
            <p class="text-red-500 text-sm mt-2 flex items-center">
                <i class="fas fa-exclamation-circle mr-1"></i>
                {{ $message }}
            </p>
        @enderror
    </div>

    <div class="wrap-input100 validate-input" data-validate="Password confirmation is required">
        <span class="label-input100">Confirm Password</span>
        <input class="input100" type="password" name="password_confirmation" required placeholder="Confirm your password">
        <span class="focus-input100" data-symbol="&#xf190;">
            <i class="fas fa-check-circle"></i>
        </span>
    </div>

    <div class="container-login100-form-btn">
        <div class="wrap-login100-form-btn">
            <div class="login100-form-bgbtn"></div>
            <button class="login100-form-btn" type="submit">
                Create Account
            </button>
        </div>
    </div>

    <div class="txt1 text-center p-t-54 p-b-20">
        <span>
            Already have an account?
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

    <div class="txt1 text-center p-t-20">
        <span>
            Learn more about our service
        </span>
    </div>

    <div class="flex-col-c p-t-20">
        <a href="{{ route('information') }}" class="txt2">
            Service Information
        </a>
    </div>
</form>
@endsection
