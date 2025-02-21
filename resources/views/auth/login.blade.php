@extends('layouts.auth')
@section('pageTitle', "Login")
@section('content')
    <div class="flex items-center justify-center grow bg-top bg-cover bg-no-repeat page-bg">
        <div class="bg-white/70 border border-transparent hover:border-black/20 ring-0 hover:ring-8 ring-white/70 transition-all duration-300 backdrop-blur shadow-sm hover:shadow-2xl hover:shadow-black/5 !rounded-none p-7 max-w-[500px] w-full">
            <form action="{{ route('auth-login') }}" method="POST" role="form" class="card-body flex flex-col gap-5 p-10" id="sign_in_form">
                @csrf
                <div class="text-center mb-2.5">
                    <img class="w-48 mb-3" src="{{ asset('storage/images/logos/CRESCO-logo.png') }}"/>
                </div>
                <div class="flex flex-col gap-1">
                    @if(env('APP_ENV') != 'production')
                        <span class="badge badge-sm badge-pill badge-primary mb-4">{{ env('APP_ENV') }}</span>
                    @endif
                    {{-- <label class="form-label font-normal text-gray-900">Email</label> --}}
                    <input name="login" type="text" class="input input-normal" placeholder="User Name or Email" autofocus />
                </div>
                <div class="flex flex-col gap-1">
                    {{-- <div class="flex items-center justify-between gap-1"> --}}
                    {{--     <label class="form-label font-normal text-gray-900">Password</label> --}}
                    {{--      --}}{{--                    <a class="text-2sm link shrink-0" href="html/demo3/authentication/classic/reset-password/enter-email.html">Forgot Password?</a>--}}
                    {{-- </div> --}}
                    <div data-toggle-password="true">
                        <input name="password" type="password" class="input input-normal" placeholder="Enter Password"  required />
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

                {{--   Error Alert         --}}
                @if($errors->all())
                    <div class="flex items-center px-4 py-2 text-sm text-red-700 border border-red-500/5 rounded-none bg-red-500/10 dark:bg-gray-800 dark:text-red-400 dark:border-red-800" role="alert">
                        <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                        </svg>
                        <div>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    </div>
                @endif
                {{--   Success Alert         --}}
                {{--                @if(session()->has('message'))--}}
                {{--                    <div class="flex items-center p-4 mt-1 text-sm text-emerald-800 border border-emerald-300 rounded-none bg-emerald-50 dark:bg-gray-800 dark:text-emerald-400 dark:border-emerald-800" role="alert">--}}
                {{--                        <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">--}}
                {{--                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>--}}
                {{--                        </svg>--}}
                {{--                        <div>--}}
                {{--                            <span>{{ session()->get('message') }}</span>--}}
                {{--                        </div>--}}
                {{--                    </div>--}}
                {{--                @endif--}}
                <button type="submit" class="main-btn text-md flex justify-center grow">Log in</button>
            </form>
        </div>
    </div>
@endsection
