@extends('layouts.guest')

@section('content')
<form class="login100-form validate-form" method="POST" action="{{ route('login') }}">
    @csrf
    
    <span class="login100-form-title p-b-49">
        Welcome Back
    </span>

    @if (session('status'))
        <div class="alert-box mb-4 p-4 rounded-lg bg-green-500 text-white font-semibold shadow flex justify-between items-center">
            <span>{{ session('status') }}</span>
            <button onclick="dismissAlert(this.closest('.alert-box'))" class="text-white hover:text-gray-200 ml-4 text-2xl leading-none">&times;</button>
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

    <div class="wrap-input100 validate-input m-b-23" data-validate="Username is required">
        <span class="label-input100">Username or Email</span>
        <input class="input100" type="text" name="email" value="{{ old('email') }}" required autofocus placeholder="Type your username or email">
        <span class="focus-input100" data-symbol="&#xf206;">
            <i class="fas fa-user"></i>
        </span>
        @error('email')
            <p class="text-red-500 text-sm mt-2 flex items-center">
                <i class="fas fa-exclamation-circle mr-1"></i>
                {{ $message }}
            </p>
        @enderror
    </div>

    <div class="wrap-input100 validate-input" data-validate="Password is required">
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

    <div class="text-right p-t-8 p-b-31">
        <a href="{{ route('password.request') }}" class="txt2">
            Forgot password?
        </a>
    </div>

    <div class="container-login100-form-btn">
        <div class="wrap-login100-form-btn">
            <div class="login100-form-bgbtn"></div>
            <button class="login100-form-btn" type="submit">
                Sign In
            </button>
        </div>
    </div>

    <div class="txt1 text-center p-t-54 p-b-20">
        <span>
            New to Xtrabusiness?
        </span>
    </div>

    <div class="flex-col-c p-t-155">
        <span class="txt1 p-b-17">
            Don't have an account?
        </span>

        <a href="{{ route('register') }}" class="txt2">
            Sign Up
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

<script>
function dismissAlert(element) {
    if (element) {
        element.style.opacity = '0';
        setTimeout(() => {
            element.remove();
        }, 300);
    }
}
</script>
@endsection
