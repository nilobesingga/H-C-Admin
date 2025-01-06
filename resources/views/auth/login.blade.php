@extends('layouts.auth')
@section('pageTitle', "Login")
@section('content')
    <div class="flex items-center justify-center grow bg-center bg-no-repeat page-bg">
        <div class="card max-w-[370px] w-full">
            {{--   Error Alert         --}}
            @if($errors->all())
                <div class="flex items-center p-4 m-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-800" role="alert">
                    <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                    </svg>
                    <div>
                        <span>{{ $errors->first() }}</span>
                    </div>
                </div>
            @endif
            {{--   Success Alert         --}}
            @if(session()->has('message'))
                <div class="flex items-center p-4 m-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 dark:border-green-800" role="alert">
                    <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                    </svg>
                    <div>
                        <span>{{ session()->get('message') }}</span>
                    </div>
                </div>
            @endif

            <form action="{{ route('auth-login') }}" method="POST" role="form" class="card-body flex flex-col gap-5 p-10" id="sign_in_form">
                @csrf
                <div class="text-center mb-2.5">
                    <h3 class="text-lg font-medium text-gray-900 leading-none mb-2.5">Sign in</h3>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="form-label font-normal text-gray-900">Email</label>
                    <input name="email" type="email" class="input" placeholder="email@email.com" required autofocus />
                </div>
                <div class="flex flex-col gap-1">
                    <div class="flex items-center justify-between gap-1">
                        <label class="form-label font-normal text-gray-900">Password</label>
                        {{--                    <a class="text-2sm link shrink-0" href="html/demo3/authentication/classic/reset-password/enter-email.html">Forgot Password?</a>--}}
                    </div>
                    <div class="input" data-toggle-password="true">
                        <input name="password" type="password" placeholder="Enter Password"  required />
{{--                        <button class="btn btn-icon" data-toggle-password-trigger="true" type="button">--}}
{{--                            <i class="ki-filled ki-eye text-gray-500 toggle-password-active:hidden"></i>--}}
{{--                            <i class="ki-filled ki-eye-slash text-gray-500 hidden toggle-password-active:block"></i>--}}
{{--                        </button>--}}
                    </div>
                </div>
                {{--            <label class="checkbox-group">--}}
                {{--                <input class="checkbox checkbox-sm" name="check" type="checkbox" value="1"/>--}}
                {{--                <span class="checkbox-label">Remember me</span>--}}
                {{--            </label>--}}
                <button type="submit" class="btn btn-primary flex justify-center grow">Sign In</button>
            </form>
        </div>
    </div>
@endsection
