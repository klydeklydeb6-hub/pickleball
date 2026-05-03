<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('layouts.soft-ui-head', ['title' => ($title ?? 'Account') . ' - ' . config('app.name', 'Pickle BALLan Ni Juan')])
    </head>

    <body class="soft-auth-page">
        <div class="container position-sticky z-index-sticky top-0">
            <div class="row">
                <div class="col-12">
                    <nav class="navbar navbar-expand-lg blur blur-rounded top-0 z-index-3 shadow position-absolute my-3 py-2 start-0 end-0 mx-4">
                        <div class="container-fluid pe-0">
                            <a class="navbar-brand font-weight-bolder ms-lg-0 ms-3 d-flex align-items-center" href="{{ route('reservations.index') }}">
                                <span class="soft-brand-mark soft-brand-mark-xs me-2">PB</span>
                                Pickle BALLan Ni Juan
                            </a>

                            <button class="navbar-toggler shadow-none ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#authNavigation" aria-controls="authNavigation" aria-expanded="false" aria-label="Toggle navigation">
                                <span class="navbar-toggler-icon mt-2">
                                    <span class="navbar-toggler-bar bar1"></span>
                                    <span class="navbar-toggler-bar bar2"></span>
                                    <span class="navbar-toggler-bar bar3"></span>
                                </span>
                            </button>

                            <div class="collapse navbar-collapse" id="authNavigation">
                                <ul class="navbar-nav ms-auto">
                                    <li class="nav-item">
                                        <a class="nav-link me-2" href="{{ route('reservations.index') }}">
                                            <i class="fa fa-calendar opacity-6 text-dark me-1"></i>
                                            Booking
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link me-2" href="{{ route('login') }}">
                                            <i class="fas fa-key opacity-6 text-dark me-1"></i>
                                            Login
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link me-2" href="{{ route('register') }}">
                                            <i class="fas fa-user-circle opacity-6 text-dark me-1"></i>
                                            Register
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>

        <main class="main-content mt-0">
            <section>
                <div class="page-header min-vh-100">
                    <div class="container">
                        <div class="row">
                            <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-auto">
                                <div class="card card-plain mt-8 soft-auth-card">
                                    {{ $slot }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="oblique position-absolute top-0 h-100 d-md-block d-none me-n8">
                                    <div class="oblique-image bg-cover position-absolute fixed-top ms-auto h-100 z-index-0 ms-n6" style="background-image: url('{{ asset('soft-ui-dashboard-main/assets/img/curved-images/curved6.jpg') }}')"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        @include('layouts.soft-ui-scripts')
    </body>
</html>
