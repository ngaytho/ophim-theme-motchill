@extends('themes::thememotchill.layout_core')

@php
    $menu = \Ophim\Core\Models\Menu::getTree();
    $logo = setting('site_logo', '');
    preg_match( '@src="([^"]+)"@' , $logo, $match );

    // will return /images/image.jpg
    $logo = array_pop($match);
@endphp

@push('header') 
    {{-- @if(!(new \Jenssegers\Agent\Agent())->isDesktop())
        <link rel="stylesheet" type="text/css" href="/themes/motchill/css/ipad.css?v=1.0.5" />
    @endif --}}

    <link href="{{ url('/') }}" rel="alternate" hreflang="vi">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"
        integrity="sha512-AFwxAkWdvxRd9qhYYp1qbeRZj6/iTNmJ2GFwcxsMOzwwTaRwz2a/2TX225Ebcj3whXte1WGQb38cXE5j7ZQw3g=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
    </script>

    <link href="/themes/motchill/static/css/main.css?v=5" rel="stylesheet" media="all">
    <link href="/themes/motchill/static/css/ads.css" rel="stylesheet" media="all">

    <script>
        function detectMob() {
            const toMatch = [
                /Android/i,
                /webOS/i,
                /iPhone/i,
                /iPad/i,
                /iPod/i,
                /BlackBerry/i,
                /Windows Phone/i
            ];

            return toMatch.some((toMatchItem) => {
                return navigator.userAgent.match(toMatchItem);
            });
        }
    </script>

    <style>
        #star i {
            color: orange
        }

        .flickity-prev-next-button {
            width: 40px;
            height: 50px;
        }

        .flickity-prev-next-button svg {
            vertical-align: middle !important;
        }

        @media all and (min-width: 813px) {
            .m-nav, .m-nav-over {
                display: none !important;
            }
        }

        @media screen and (max-width: 800px) {
            .myui-header__logo .logo {
                height: 50px !important;
                width: 120px !important;
                background-position: center center !important;
            }
        }

        @if ($logo)
            .myui-header__logo .logo {
                background: url({{ $logo }}) no-repeat;
                font-size: 1.5em;
                color: #fff !important;
                font-weight: 700;
                background-size: contain;
                display: block;
                width: 229px;
                height: 60px;
                text-indent: -9999px;
            }
        @endif
    </style>
@endpush

@section('body')
    @include('themes::thememotchill.inc.header')
   
    <div class="container">
        <div id="top_ads"></div>
        
        @if (get_theme_option('ads_header'))
            {!! get_theme_option('ads_header') !!}
        @endif

        <div class="row">
            {{-- @yield('slider_recommended')
            <div class="clear"></div> --}}

            @yield('breadcrumb')
            @yield('content')            
        </div>
    </div>        
@endsection

@section('footer')
    @if (get_theme_option('ads_catfish'))
        {!! get_theme_option('ads_catfish') !!}
    @endif

    {!! get_theme_option('footer') !!}

    <script src="/themes/motchill/efc0d744/yii.js"></script>
    <script src="/themes/motchill/static/js/flickity.smart.min.js"></script>
    <script src="/themes/motchill/static/js/main.js?v=4"></script>
    {{-- <script src="/themes/motchill/js/ads_xx.js?v=7"></script> --}}

    <div id="footer_fixed_ads"></div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
    <div id="fb-root"></div>

    <script>
        window.fbAsyncInit = function () {
            FB.init({
                appId: '{{ setting('social_facebook_app_id') }}',
                xfbml: true,
                version: 'v5.0'
            });
            FB.AppEvents.logPageView();
        };

        (function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {
                return;
            }
            js = d.createElement(s);
            js.id = id;
            js.src = "https://connect.facebook.net/vi_VN/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    </script>

    <script>
        $('body').on('click', '.nav-tabs li a', function () {
            var tabactive = $(this).attr('href');
            $(this).closest('.nav-tabs').find('li').removeClass('active');
            $(this).parent().addClass('active');
            $('body').find('.myui-panel_bd .tab-pane').removeClass('active');
            $('body').find(tabactive).addClass('active');

            return false;
        });
    </script><!--script src="https://api.flygame.io/sdk/widget/chill_tv.1856.js" async></script-->

    {!! setting('site_scripts_google_analytics') !!}
@endsection
