
<!DOCTYPE html>
<html lang="{{ str_replace("_", "-", app()->currentLocale()) }}" dir="{{ language_direction() }}">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
        <link type="image/png" href="{{ asset("img/favicon.png") }}" rel="icon" />
        <link href="{{ asset("img/favicon.png") }}" rel="apple-touch-icon" sizes="76x76" />
        <meta name="keyword" content="{{ setting("meta_keyword") }}" />
        <meta name="description" content="{{ setting("meta_description") }}" />

        <!-- Shortcut Icon -->
        <link href="{{ asset("img/favicon.png") }}" rel="shortcut icon" />
        <link type="image/ico" href="{{ asset("img/favicon.png") }}" rel="icon" />

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>@yield("title") | {{ config("app.name") }}</title>

        <script src="{{ asset("vendor/jquery/jquery-3.6.4.min.js") }}"></script>

        @vite(["resources/sass/app-backend.scss", "resources/js/app-backend.js"])

        <link href="https://fonts.googleapis.com/css?family=Ubuntu&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css?family=Noto+Sans+Bengali+UI&display=swap" rel="stylesheet" />
        <style>
            body {
                font-family: Ubuntu, 'Noto Sans Bengali UI', Arial, Helvetica, sans-serif;
            }

            <style>
                .select2-container--default .select2-selection--single {
                    background-color: #343a40;
                    color: #fff;
                    border: 1px solid #495057;
                }

                .select2-container--default .select2-selection--single .select2-selection__placeholder {
                    color: #adb5bd;
                }

                .select2-container--default .select2-selection--single .select2-selection__rendered {
                    color: #fff;
                }

                .select2-container--default .select2-selection--single .select2-selection__arrow {
                    color: #fff;
                }

                .select2-container--default .select2-results__option {
                    background-color: #343a40;
                    color: #fff;
                }

                .select2-container--default .select2-results__option--highlighted {
                    background-color: #495057;
                    color: #fff;
                }
                .select2-container--default .select2-selection--multiple .select2-selection__choice__display{
                    color: #000;
                }
            </style>
        </style>

        @stack("after-styles")

        <x-google-analytics />

        @livewireStyles
    </head>

    <body>
        <x-selected-theme />

        <!-- Sidebar -->
        @include("backend.includes.sidebar")
        <!-- /Sidebar -->

        <div class="wrapper d-flex flex-column min-vh-100">
            {{-- header --}}
            @include("backend.includes.header")

            <div class="body flex-grow-1">
                <div class="container-lg">
                    @include("flash::message")

                    <!-- Errors block -->
                    @include("backend.includes.errors")
                    <!-- / Errors block -->

                    <!-- Main content block -->
                    @yield("content")
                    <!-- / Main content block -->
                </div>
            </div>

            {{-- Footer block --}}
            <x-backend.includes.footer />
        </div>

        <!-- Scripts -->
        @livewireScripts

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        @stack("after-scripts")
        <!-- / Scripts -->
    </body>
</html>
