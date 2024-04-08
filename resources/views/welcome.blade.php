<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Store</title>

        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">        
        <link rel="stylesheet" href="{{ asset('./css/store.css') }}">
        
    </head>
    <body class="antialiased">
        <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0" >
            @include('nav')
            <div class="max-w-6 xl mx-auto sm:px-6 ">
                <div class="logo" style="text-align: center">
                    <img src="{{asset('images/welcome/logo.jpg')}}" alt="logo" srcset="" width="300" height="200">
                </div>
                @yield('content')                
            </div>
        </div>
    </body>
    <footer>
        <span>powered by &copy; <a href="https://gitlab.com/andrextor" target="_blank">Ivan andres l.G.</a></span>
    </footer>
</html>
