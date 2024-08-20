<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Metas -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://ajifatur.github.io/assets/spandiv.min.css">
    <link rel="stylesheet" href="https://spandiv.xyz/wp-content/themes/spandiv/style.css?ver=2.3">
    <link rel="icon" type="image/x-icon" href="{{ asset ('assets/images/icon/icon-spandiv.png')}}">

    <title>Log in | {{ config('app.name') }}</title>
    <style>
        :root{--primary:#55005b; --primary-s:#FDE1FF}
        .rounded-1 {border-radius: .5rem !important;}
        .rounded-2 {border-radius: 1rem !important;}
        .rounded-3 {border-radius: 1.5rem !important;}
        .rounded-4 {border-radius: 2rem !important;}
        #left {transition:.5s cubic-bezier(0.4, 0, 0.2, 1)}
        .overlay{position: absolute; width:100%; height:100%; left: 0; right: 0; top: 0; bottom: 0; margin: auto; background-color:#00000070}
        .left-side{background-color: var(--primary) ;background-image:url('assets/images/background/login-bg.png'); background-size:cover; background-repeat:no-repeat; background-position:center}
        @media(min-width: 992px){
            #left {border-radius:0 2rem 2rem 0;}
        }
        @media (max-width: 991.98px) {
            .w-75{width:100%!important}
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row left-side">
            <div class="col-12 col-lg-5 vh-100 bg-white shadow-lg z-1 d-flex flex-wrap align-items-center align-content-around justify-content-center" id="left">
            <div class="w-75 text-center text-lg-start"><a href="https://spandiv.xyz/tes-psikologi"><img src="https://sgp1.digitaloceanspaces.com/spandiv/images/spandiv/2023/03/QwDjvjyK-spandiv-tes-psikologi-logo-blue.svg" width="200" alt="logo spandiv digital solutions"></a></div>
                <form class="login-box w-75" method="post" action="{{ route('auth.login') }}">
                    @csrf
                    <div class="heading text-center text-lg-start">
                        <h1 class="h3 mb-3">Selamat Datang di {{ config('app.name') }}</h1>
                        <p>🔔 Silahkan login untuk mengakses tes psikologi</p>
                    </div>
                    <hr>
                    @if($errors->has('message'))
                    <div class="alert alert-danger" role="alert">{{ $errors->first('message') }}</div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label">Email / Username</label>
                        <input type="text" name="username" class="form-control {{ $errors->has('username') ? 'border-danger' : '' }} rounded-3" value="{{ old('username') }}" placeholder="{{ config('faturhelper.auth.allow_login_by_email') === true ? 'Masukkan Email atau Username' : 'Masukkan Username' }}" autofocus>
                        @if($errors->has('username'))
                        <div class="small text-danger text-start">{{ $errors->first('username') }}</div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" class="form-control {{ $errors->has('password') ? 'border-danger' : '' }} rounded-3" placeholder="Masukkan Password">
                            <button type="button" class="btn {{ $errors->has('password') ? 'btn-outline-danger' : 'btn-outline-secondary' }} btn-toggle-password rounded-3 ms-1"><i class="bi-eye"></i></button>
                        </div>
                        @if($errors->has('password'))
                        <div class="small text-danger text-start">{{ $errors->first('password') }}</div>
                        @endif
                    </div>
                    <button class="w-100 btn btn-primary rounded-3 mb-5" type="submit">Log in</button>
                    <div class="text-center">Belum punya akun?<br><a href="{{ route('auth.login.provider', ['provider' => 'google']) }}">Daftar menggunakan Akun Googlemu yuk!</a></div>
                </form>
                <div class="w-75"></div>
            </div>
            <div class="col-12 col-lg-7 vh-100 d-none d-md-flex align-items-center justify-content-center">
                <div class="overlay"></div>
                <div class="text-center text-white w-75 z-1">
                    <div class="mb-4">
                        <h4>Platform Tes Psikologi Fitur & Assessment Terlengkap</h4>
                        <p>Yuk cari tahu minat atau passionmu serta potensi dan bakatmu yang dapat menunjang karier atau usaha yang sedang atau akan kamu jalani!</p>
                    </div>
                    <div class="rounded-3 py-5 mb-5"
                        style="background-color:#ffffff60; backdrop-filter:blur(10px); border:1px solid #ffffff70">
                        <img src="{{asset('assets/images/illustrations/login.png')}}" alt="img" class="img-fluid w-50">
                    </div>
                    <div class="row">
                        <div class="col-lg-3 col-6 d-flex align-items-stretch">
                            <div class="rounded-3 py-2 text-white w-100 h-100 d-flex align-items-center justify-content-center" style="background-color:#ffffff60; backdrop-filter:blur(10px); border:1px solid #ffffff70">
                                <p class="mb-0">Mentoring</p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6 d-flex align-items-stretch">
                            <div class="rounded-3 py-2 text-white w-100 h-100 d-flex align-items-center justify-content-center" style="background-color:#ffffff60; backdrop-filter:blur(10px); border:1px solid #ffffff70">
                                <p class="mb-0">Coaching & Counseling</p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6 d-flex align-items-stretch">
                            <div class="rounded-3 py-2 text-white w-100 h-100 d-flex align-items-center justify-content-center" style="background-color:#ffffff60; backdrop-filter:blur(10px); border:1px solid #ffffff70">
                                <p class="mb-0">Analisis Data Statistik</p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6 d-flex align-items-stretch">
                            <div class="rounded-3 py-2 text-white w-100 h-100 d-flex align-items-center justify-content-center" style="background-color:#ffffff60; backdrop-filter:blur(10px); border:1px solid #ffffff70">
                                <p class="mb-0">Tes Psikologi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://ajifatur.github.io/assets/spandiv.min.js"></script>
</body>
</html>