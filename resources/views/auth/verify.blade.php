@extends('layouts.auth')
@section('pageTitle', "Login")
@section('content')
    <div class="flex items-center justify-end bg-top bg-no-repeat bg-cover grow page-bg">
        <div class="absolute top-10 right-20">
            <img class="w-50" src="{{ asset('storage/images/logos/Logo.svg') }}" alt="Logo" />
        </div>
        <div class="flex w-full h-full">
            <!-- Left side - can be used for an image or additional content -->
            <div class="items-center justify-center hidden md:flex md:w-1/2">
            </div>
            <!-- Right side - contains the login form -->
            <div class="relative flex items-center justify-center w-full md:w-1/2">
                <!-- The login card will be positioned here -->
                <div class="bg-white z-5 border rounded-md hover:border-black/20 ring-0 hover:ring-8 ring-white/70 transition-all duration-300 backdrop-blur shadow-sm hover:shadow-2xl hover:shadow-black/5 p-7 max-w-[500px] w-full">
                    <form action="{{ route('auth-login') }}" method="POST" role="form" class="flex flex-col gap-5 p-10 card-body" id="sign_in_form">
                        @csrf
                        <div class="mb-10 text-center">
                            <h2 class="mb-3 text-2xl font-black">Enter Verification Code</h2>
                            <span class="text-sm text-gray-700">Check your authenticator app and enter <br/> the 6-digit code to verify.</span>
                        </div>
                        <div class="flex flex-col gap-1 mb-2">
                            <div data-toggle-password="true">
                                <input name="verification_code" type="text" class="text-center input input-normal" placeholder="xxx-xxx"  required />
                                @if($errors->all())
                                    <div class="flex items-center px-4 py-2 text-sm text-red-700 border rounded-none border-red-500/5 bg-red-500/10 dark:bg-gray-800 dark:text-red-400 dark:border-red-800" role="alert">
                                        <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                                        </svg>
                                        <div>
                                            <span>{{ $errors->first() }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <button type="submit" class="flex justify-center text-white main-btn text-md grow" style="background-color: #313d4f; color: #fff;">Verify</button>
                        <a href="" class="flex justify-center text-sm text-dark-600 hover:underline">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
        <div class="absolute right-0 overflow-hidden opacity-15 -bottom-40">
            <img class="w-50" src="{{ asset('/img/HC-Seal_blue.png') }}" alt="Logo" />
        </div>
    </div>
@endsection
