@extends('layouts.guest')

@section('content')
<div class="login100-form validate-form">
    <span class="login100-form-title p-b-49">
        Verify Your Email Address
    </span>

    @if (session('status'))
        <div class="bg-green-500/10 border border-green-500/20 text-green-500 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('status') }}
            </div>
        </div>
    @endif

    <div class="text-center mb-6">
        <div class="mb-4">
            <i class="fas fa-envelope-open-text text-4xl text-blue-500"></i>
        </div>
        <h3 class="text-lg font-semibold mb-2">Check Your Email</h3>
        <p class="text-gray-600">
            Before proceeding, please check your email for a verification link. 
            If you did not receive the email, we will gladly send you another.
        </p>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
            <div>
                <p class="text-sm text-blue-800">
                    <strong>Important:</strong> Please check your spam folder if you don't see the verification email in your inbox.
                </p>
            </div>
        </div>
    </div>

    <div class="container-login100-form-btn">
        <div class="wrap-login100-form-btn">
            <div class="login100-form-bgbtn"></div>
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button class="login100-form-btn" type="submit">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Resend Verification Email
                </button>
            </form>
        </div>
    </div>

    <div class="txt1 text-center p-t-54 p-b-20">
        <span>
            Already verified your email?
        </span>
    </div>

    <div class="flex-col-c p-t-155">
        <span class="txt1 p-b-17">
            Go to your dashboard
        </span>

        <a href="{{ route('dashboard') }}" class="txt2">
            Dashboard
        </a>
    </div>

    <div class="txt1 text-center p-t-20">
        <span>
            Or sign out and sign back in
        </span>
    </div>

    <div class="flex-col-c p-t-20">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="txt2 text-red-500 hover:text-red-700">
                Sign Out
            </button>
        </form>
    </div>
</div>
@endsection 