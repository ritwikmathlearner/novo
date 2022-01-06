@extends('layouts.backend_login')

@section('content')
<div class="container">
    <div class="row">

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Login</div>

                <div class="card-body col-md-6">
                    <form method="POST" action="{{ url('/admin/loginverify') }}">
                        @csrf
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <div class="field-wrap">
                            <label>Admin Email</label>
                            <div class="form-input-wrap">
                                <span class="gradient-border"></span>
                                <input type="text" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Enter your email address">
                            </div>

                        </div>
                        <div class="field-wrap">
                            <label>Password</label>
                            <div class="form-input-wrap">
                                <span class="gradient-border"></span>
                                <input type="password"  name="password" required autocomplete="current-password" placeholder="Enter your password">
                            </div>
                        </div>
                        <div class="field-wrap">
                        <input type="submit"  class="gradient-btn" value="Login Now" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
