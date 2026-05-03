<x-guest-layout>
    <div class="card-header pb-0 text-left bg-transparent">
        <h3 class="font-weight-bolder text-info text-gradient">Create account</h3>
        <p class="mb-0">Register once, then reserve court time faster.</p>
    </div>

    <div class="card-body">
        @php($redirectTo = old('redirect_to', request('redirect_to')))

        <form method="POST" action="{{ route('register') }}" role="form text-left">
            @csrf

            @if($redirectTo)
                <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">
            @endif

            <div class="mb-3">
                <input
                    id="name"
                    class="form-control"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Name"
                    required
                    autofocus
                    autocomplete="name"
                >
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="mb-3">
                <input
                    id="email"
                    class="form-control"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="Email"
                    required
                    autocomplete="username"
                >
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mb-3">
                <input
                    id="password"
                    class="form-control"
                    type="password"
                    name="password"
                    placeholder="Password"
                    required
                    autocomplete="new-password"
                >
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="mb-3">
                <input
                    id="password_confirmation"
                    class="form-control"
                    type="password"
                    name="password_confirmation"
                    placeholder="Confirm Password"
                    required
                    autocomplete="new-password"
                >
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="text-center">
                <button type="submit" class="btn bg-gradient-dark w-100 my-4 mb-2">Register</button>
            </div>
        </form>
    </div>

    <div class="card-footer text-center pt-0 px-lg-2 px-1">
        <p class="text-sm mt-3 mb-0">
            Already registered?
            <a href="{{ $redirectTo ? route('login', ['redirect_to' => $redirectTo]) : route('login') }}" class="text-dark font-weight-bolder">Sign in</a>
        </p>
    </div>
</x-guest-layout>
