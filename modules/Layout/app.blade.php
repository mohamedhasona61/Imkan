<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{$html_class ?? ''}}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php event(new \Modules\Layout\Events\LayoutBeginHead()); @endphp
    @php
    $favicon = setting_item('site_favicon');
    @endphp
    @if($favicon)
    @php
    $file = (new \Modules\Media\Models\MediaFile())->findById($favicon);
    @endphp
    @if(!empty($file))
    <link rel="icon" type="{{$file['file_type']}}" href="{{asset('uploads/'.$file['file_path'])}}" />
    @else:
    <link rel="icon" type="image/png" href="{{url('images/favicon.png')}}" />
    @endif
    @endif

    @include('Layout::parts.seo-meta')
    <link href="{{ asset('libs/bootstrap/css/bootstrap.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/brands.min.css" integrity="sha512-bSncow0ApIhONbz+pNI52n0trz5fMWbgteHsonaPk42JbunIeM9ee+zTYAUP1eLPky5wP0XZ7MSLAPxKkwnlzw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/fontawesome.min.css" integrity="sha512-8Vtie9oRR62i7vkmVUISvuwOeipGv8Jd+Sur/ORKDD5JiLgTGeBSkI3ISOhc730VGvA5VVQPwKIKlmi+zMZ71w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/regular.min.css" integrity="sha512-sWLOh8QiEOmwfP3jd0n7AneUOa6XKif5g8GU8FqdFMIbr6rYBt1PKWBp/Wv4E6PQ6N9lFtVAndujl9HbniDTog==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/solid.min.css" integrity="sha512-6/gTF62BJ06BajySRzTm7i8N2ZZ6StspU9uVWDdoBiuuNu5rs1a8VwiJ7skCz2BcvhpipLKfFerXkuzs+npeKA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/svg-with-js.min.css" integrity="sha512-T22AGZA32A7BJVwM85+3QTgGxP7lSHb88UwE3b19YtWs0mw6x27Pw5ea/60BQkcKO4vzzsXW230pxPdw6TthGQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/v4-font-face.min.css" integrity="sha512-YAIQTShdW1GyO8PvzxYSwqmoNBXWp/vntilAZvBogk0IPJYgyeQqVFwnoFBznX6maNw4emZZWyLLmClRXKDR3A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/v4-shims.min.css" integrity="sha512-pFN+FoTX/XKTsPGLqPfu1iibiJZvRUs1yQ++Xnx4GSQnRTXBisl4PEb7a1SHliCnSqD35d6ujp9i4tmWJT0Yvg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/v5-font-face.min.css" integrity="sha512-Ipf6bU2dOLSG8/16GCm8MfSEcNMAIQloGhcn+5o5FRRkmBIvShWK2IAZQX69PPd1aiZx0cZoLWTmWRSW68tPYA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="{{ asset('libs/ionicons/css/ionicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/icofont/icofont.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('dist/frontend/css/notification.css') }}" rel="newest stylesheet">
    <link href="{{ asset('dist/frontend/css/app.css?_ver='.config('app.asset_version')) }}" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="{{ asset("libs/daterange/daterangepicker.css") }}">
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel='stylesheet' id='google-font-css-css' href='https://fonts.googleapis.com/css?family=Poppins%3A300%2C400%2C500%2C600&display=swap' type='text/css' media='all' />
    {!! \App\Helpers\Assets::css() !!}
    {!! \App\Helpers\Assets::js() !!}
    @include('Layout::parts.global-script')
    <!-- Styles -->
    @stack('css')
    {{--Custom Style--}}
    <link href="{{ route('core.style.customCss') }}" rel="stylesheet">
    <link href="{{ asset('libs/carousel-2/owl.carousel.css') }}" rel="stylesheet">
    @if(setting_item_with_lang('enable_rtl') && app()->getLocale() === 'ar')
    <link href="{{ asset('dist/frontend/css/rtl.css') }}" rel="stylesheet">
    @endif

    @if(!is_demo_mode())
    {!! setting_item('head_scripts') !!}
    {!! setting_item_with_lang_raw('head_scripts') !!}
    @endif

    @if(Route::currentRouteName() != 'website')
    <style>
        .bravo_wrap .bravo_header .content .header-left .bravo-menu ul li a {
            color: #1a2b48;
        }
        .bravo_wrap .bravo_header .content .header-right .topbar-items li a {
            color: #1a2b48;
        }
        .socials a{
            color: #1a2b48;
        }
    </style>
    @endif

</head>

<body class="frontend-page {{ !empty($row->header_style) ? "header-".$row->header_style : "header-normal" }} {{$body_class ?? ''}} @if(setting_item_with_lang('enable_rtl') && app()->getLocale() === 'ar') is-rtl @endif @if(is_api()) is_api @endif">


    @if(!is_demo_mode())
    {!! setting_item('body_scripts') !!}
    {!! setting_item_with_lang_raw('body_scripts') !!}
    @endif
    <div class="bravo_wrap">
        @if(!is_api())
        @include('Layout::parts.topbar')
        @include('Layout::parts.header')
        @endif

        @yield('content')

        @include('Layout::parts.footer')
    </div>
    @if(!is_demo_mode())
    {!! setting_item('footer_scripts') !!}
    {!! setting_item_with_lang_raw('footer_scripts') !!}
    @endif

 
 

</body>

</html>