<x-guest-layout>
    <div class="card-header pb-0 text-left bg-transparent">
        <h3 class="font-weight-bolder text-info text-gradient">Welcome back</h3>
        <p class="mb-0">Sign in to manage reservations and receipts.</p>
    </div>

    <div class="card-body">
        <x-auth-session-status class="alert alert-info text-white text-sm" :status="session('status')" />

        @php($redirectTo = old('redirect_to', request('redirect_to')))

        <form method="POST" action="{{ route('login') }}" role="form">
            @csrf

            @if($redirectTo)
                <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">
            @endif

            <label for="email">Email</label>
            <div class="mb-3">
                <input
                    id="email"
                    class="form-control"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="Email"
                    required
                    autofocus
                    autocomplete="username"
                >
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <label for="password">Password</label>
            <div class="mb-3">
                <input
                    id="password"
                    class="form-control"
                    type="password"
                    name="password"
                    placeholder="Password"
                    required
                    autocomplete="current-password"
                >
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="form-check form-switch">
                <input id="remember_me" class="form-check-input" type="checkbox" name="remember">
                <label class="form-check-label" for="remember_me">Remember me</label>
            </div>

            <div class="text-center">
                <button type="submit" class="btn bg-gradient-info w-100 mt-4 mb-0">Log in</button>
            </div>
        </form>
    </div>

    <div class="card-footer text-center pt-0 px-lg-2 px-1">
        @if (Route::has('password.request'))
            <p class="text-sm mb-2">
                <a class="text-info text-gradient font-weight-bold" href="{{ route('password.request') }}">
                    Forgot your password?
                </a>
            </p>
        @endif

        <p class="mb-4 text-sm mx-auto">
            Don't have an account?
            <a href="{{ $redirectTo ? route('register', ['redirect_to' => $redirectTo]) : route('register') }}" class="text-info text-gradient font-weight-bold">Create one</a>
        </p>
    </div>
</x-guest-layout>
