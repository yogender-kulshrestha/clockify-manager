@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <section>
        <div class="page-header min-vh-100">
            <div class="container">
                <div class="row">
                    <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0 mx-auto">
                        <div class="card card-plain">
                            <div class="card-header pb-0 text-start">
                                <h4 class="font-weight-bolder">Login</h4>
                                <p class="mb-0">Enter your email and password to login</p>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('signin') }}" role="form">
                                    @csrf
                                    {{--<div class="mb-3">
                                        <select name="role" id="role" class="form-control form-control-lg @error('role') is-invalid @enderror" --}}{{--autocomplete="role"--}}{{-- autofocus>
                                            <option value="">-- Select your Role -- </option>
                                            <option value="admin" @if(old('role') == 'admin') selected @endif>Super Admin</option>
                                            <option value="hr" @if(old('role') == 'hr') selected @endif>HR</option>
                                            <option value="user" @if(old('role') == 'user') selected @endif>Employee/Approver</option>
                                        </select>
                                        @error('role')
                                        <span class="text-danger text-sm">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>--}}
                                    <div class="mb-3">
                                        <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg @error('email') is-invalid @enderror" placeholder="Email"
                                               aria-label="Email" {{--autocomplete="email"--}} autofocus>
                                        @error('email')
                                        <span class="text-danger text-sm">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <input type="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" placeholder="Password"
                                               aria-label="Password" {{--autocomplete="current-password"--}}>
                                        @error('password')
                                        <span class="text-danger text-sm">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="remember" id="rememberMe" {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="rememberMe">Remember me</label>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit"
                                                class="btn btn-lg bg-gradient-primary btn-lg w-100 mt-4 mb-0">Login
                                        </button>
                                    </div>
                                </form>
                            </div>
                            {{--<div class="card-footer text-center pt-0 px-lg-2 px-1">
                                <p class="mb-4 text-sm mx-auto">
                                    Don't have an account?
                                    <a href="javascript:" class="text-primary text-gradient font-weight-bold">Sign
                                        up</a>
                                </p>
                            </div>--}}
                        </div>
                    </div>
                    <div
                        class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 end-0 text-center justify-content-center flex-column">
                        <div
                            class="position-relative bg-gradient-primary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center">
                            <img src="{{ asset('assets/img/shapes/pattern-lines.svg') }}" alt="pattern-lines"
                                 class="position-absolute opacity-4 start-0">
                            <div class="position-relative">
                                {{--<img class="max-width-500 w-100 position-relative z-index-2"
                                     src="{{ asset('assets/img/illustrations/chat.png') }}" alt="chat-img">--}}
                                <img class="max-width-200 w-100 position-relative z-index-2"
                                     src="{{ asset('assets/img/logo-ct.png')}}" alt="chat-img">
                            </div>
                            <h4 class="mt-5 text-white font-weight-bolder">"Attention is the new currency"</h4>
                            <p class="text-white">The more effortless the writing looks, the more effort the writer
                                actually put into the process.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
