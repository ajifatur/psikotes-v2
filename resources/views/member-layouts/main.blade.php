<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/gh/hung1001/font-awesome-pro@4cac1a6/css/all.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://ajifatur.github.io/assets/spandiv.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset ('assets/css/style.css')}}">
    <link rel="icon" type="image/x-icon" href="{{ asset ('assets/images/icon/icon-spandiv.png')}}">
    @yield('css')
    <title>{{ config('app.name') }}</title>	
</head>
<body class="bg-white">
    <nav class="navbar navbar-expand navbar-dark bg-theme-1 fixed-top shadow-sm" id="navbar">
    <div class="container">
        <ul class="nav navbar-nav">
            <li class="nav-item" style="{{ Request::url() == route('member.dashboard') ? 'visibility:hidden' : '' }}">
                <a class="nav-link fw-bold" href="{{ route('member.dashboard') }}"><i class="fad fa-long-arrow-left"></i> <span class="d-none d-md-inline">Kembali</span></a>
            </li>
        </ul>
        <a class="navbar-brand mx-auto" href="{{ route('member.dashboard') }}">
            <img src="https://sgp1.digitaloceanspaces.com/spandiv/images/spandiv/2023/03/djuTZH6a-spandiv-tes-psikologi-logo-white.svg" alt="logo spandiv" id="navbar-logo">
        </a>
        <ul class="nav navbar-nav">
            <li class="nav-item">
                <a class="nav-link fw-bold btn-logout" href="#"><span class="d-none d-md-inline">Keluar</span> <i class="fad fa-sign-out"></i></a>
                <form id="form-logout" class="d-none" method="post" action="{{ route('auth.logout') }}">@csrf</form>
            </li>
        </ul>
    </div>
</nav>  
@yield('content')
<script>
var className = "sticky";
var scrollTrigger = 60;

window.onscroll = function() {
  // We add pageYOffset for compatibility with IE.
  if (window.scrollY >= scrollTrigger || window.pageYOffset >= scrollTrigger) {
    document.getElementsByTagName("nav")[0].classList.add(className);
    document.getElementById("navbar-logo").src="https://sgp1.digitaloceanspaces.com/spandiv/images/spandiv/2023/03/QwDjvjyK-spandiv-tes-psikologi-logo-blue.svg";
  } else {
    document.getElementsByTagName("nav")[0].classList.remove(className);
    document.getElementById("navbar-logo").src="https://sgp1.digitaloceanspaces.com/spandiv/images/spandiv/2023/03/djuTZH6a-spandiv-tes-psikologi-logo-white.svg";
  }
};
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
<script src="https://ajifatur.github.io/assets/spandiv.min.js"></script>
<script type="text/javascript">
	function j(e){
	    e.preventDefault();
	    e.returnValue = '';
	}
</script>
@if(is_int(strpos(Request::path(), 'member/test')))
<script type="text/javascript">
    // Before Unload
    window.addEventListener("beforeunload", j);
    // Unload
    window.addEventListener("unload", function(e){
        console.log("Bye bye");
    });
</script>
@endif
<script type="text/javascript">
	// Next form
	$(document).on("click", "#btn-next", function(e){
		e.preventDefault();
		var ask = confirm("Anda ingin melanjutkan ke bagian selanjutnya?");
		if(ask){
			window.removeEventListener("beforeunload", j);
			$("input[name=is_submitted]").val(0);
			$("#form")[0].submit();
		}
	});
	// Submit form
	$(document).on("click", "#btn-submit", function(e){
		e.preventDefault();
		var ask = confirm("Anda yakin ingin mengumpulkan tes yang telah dikerjakan?");
		if(ask){
			window.removeEventListener("beforeunload", j);
			$("#form")[0].submit();
		}
	});
</script>
@yield('js')
</body>
</html>