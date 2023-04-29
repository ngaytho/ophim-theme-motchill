@extends('themes::thememotchill.layout')

@php
    $tops = Cache::remember('site.movies.tops', setting('site_cache_ttl', 5 * 60), function () {
        $lists = preg_split('/[\n\r]+/', get_theme_option('hotest'));
        $data = [];
        foreach ($lists as $list) {
            if (trim($list)) {
                $list = explode('|', $list);
                [$label, $relation, $field, $val, $sortKey, $alg, $limit, $template] = array_merge($list, ['Phim hot', '', 'type', 'series', 'view_total', 'desc', 4, 'top_thumb']);
                try {
                    $data[] = [
                        'label' => $label,
                        'template' => $template,
                        'data' => \Ophim\Core\Models\Movie::when($relation, function ($query) use ($relation, $field, $val) {
                            $query->whereHas($relation, function ($rel) use ($field, $val) {
                                $rel->where($field, $val);
                            });
                        })
                            ->when(!$relation, function ($query) use ($field, $val) {
                                $query->where($field, $val);
                            })
                            ->orderBy($sortKey, $alg)
                            ->limit($limit)
                            ->get(),
                    ];
                } catch (\Exception $e) {
                    # code
                }
            }
        }

        return $data;
    });
@endphp

@section('content')
    <style>
        .video-footer {
            margin-top: 5px;
        }

        .btn-active {
            color: #fff !important;
            background: #d9534f !important;
            border-color: #d9534f !important;
        }

        .btn-sv {
            margin-right: 5px;
        }

        .btn-sv:last-child {
            margin-right: 0;
        }

        #player-loaded > div {
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            width: 100%;
            height: 100%;
        }
    </style>

    <div class="video-p-first mt-3">
        <div class="cc-warning text-center">
            - Cách tìm kiếm phim trên Google: <b>"Tên phim + {{ request()->getHost() }}"</b><br>
        </div>
        @if ($currentMovie->showtimes && $currentMovie->showtimes != '')
            <div class="myui-player__notice">Lịch chiếu: {!! $currentMovie->showtimes !!}</div>
        @endif
    </div>

    <div id="main-player">
        {{-- <div class="loader"></div> --}}
        <div id="player-loaded"></div>
    </div>
    <div class="video-footer">
        <script>
            function detectMobile() {
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
    </div>

    <div id="ploption" class="text-center">
        @foreach ($currentMovie->episodes->where('slug', $episode->slug)->where('server', $episode->server) as $server)
            <a
                onclick="chooseStreamingServer(this)"
                data-type="{{ $server->type }}"
                data-id="{{ $server->id }}"
                data-link="{{ $server->link }}"
                class="streaming-server current btn-sv btn btn-primary"
            >
                Nguồn phát #{{ $loop->index }}
            </a>
        @endforeach
    </div>

    <div itemscope itemtype="http://schema.org/Movie">
        <div class="rating-block text-center">
            @include('themes::thememotchill.inc.rating2')
        </div>
        <div class="row">
            <div class="col-md-wide-7 col-xs-1 padding-0">
                <div id="servers-container" class="myui-panel myui-panel-bg clearfix ">
                    <div class="myui-panel-box clearfix">
                        <div class="myui-panel_hd">
                            <div class="myui-panel__head active bottom-line clearfix">
                                <div class="title">Tập phim</div>
                                <ul class="nav nav-tabs active">
                                    @foreach ($currentMovie->episodes->sortBy([['server', 'asc']])->groupBy('server') as $server => $data)
                                        <li class="{{ $loop->index == 0 ? 'active' : ''}}"><a href="#tab_{{ $loop->index }}">{{ $server }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    
                        <div class="tab-content myui-panel_bd">
                            @foreach ($currentMovie->episodes->sortBy([['server', 'asc']])->groupBy('server') as $server => $data)
                                <div class="tab-pane fade in clearfix {{ $loop->index == 0 ? 'active' : ''}}" id="tab_{{ $loop->index }}">
                                    <ul class="myui-content__list sort-list clearfix" style="max-height: 300px; overflow: auto;">
                                        @foreach ($data->sortByDesc('name', SORT_NATURAL)->groupBy('name') as $name => $item)
                                            <li class="col-lg-8 col-md-7 col-sm-6 col-xs-4">
                                                <a
                                                    href="{{ $item->sortByDesc('type')->first()->getUrl() }}"
                                                    class="btn btn-default @if ($item->contains($episode)) active @endif"
                                                    title="{{ $name }}"
                                                >{{ $name }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="myui-panel myui-panel-bg clearfix">
                    <div class="myui-panel-box clearfix">
                        <div class="myui-panel_hd">
                            <div class="myui-panel__head clearfix height-auto">
                                <h1 class="title" itemprop="name">{{ $currentMovie->name }} - Tập {{ $episode->name }}</h1>
                                <h2 class="title2">{{ $currentMovie->origin_name }}</h2>
                            </div>
                        </div>
                        <div class="myui-panel_bd">
                            <div class="col-pd text-collapse content">
                                <div class="sketch content" itemprop="description">
                                    <h3>{{ $currentMovie->name }}, {{ $currentMovie->origin_name }}</h3>
                                    {!! $currentMovie->content !!}
                                </div>
                                <div id="tags"><label>Keywords:</label>
                                    <div class="tag-list">
                                        @foreach ($currentMovie->tags as $tag)
                                            <h3>
                                                <strong>
                                                    <a href="{{ $tag->getUrl() }}" title="{{ $tag->name }}" rel='tag'>
                                                        {{ $tag->name }}
                                                    </a>
                                                </strong>
                                            </h3>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="myui-panel myui-panel-bg clearfix">
                    <style>
                        @media only screen and (max-width: 767px) {
                            .fb-comments {
                                width: 100% !important
                            }

                            .fb-comments iframe[style] {
                                width: 100% !important
                            }

                            .fb-like-box {
                                width: 100% !important
                            }

                            .fb-like-box iframe[style] {
                                width: 100% !important
                            }

                            .fb-comments span {
                                width: 100% !important
                            }

                            .fb-comments iframe span[style] {
                                width: 100% !important
                            }

                            .fb-like-box span {
                                width: 100% !important
                            }

                            .fb-like-box iframe span[style] {
                                width: 100% !important
                            }
                        }

                        .fb-comments,
                        .fb-comments span {
                            background-color: #eee
                        }

                        .fb-comments {
                            margin-bottom: 20px
                        }
                    </style>
                    <div style="color:red;font-weight:bold;padding:5px">
                        Lưu ý các bạn không nên nhấp vào các đường link ở phần bình luận, kẻ gian có thể đưa virut vào thiết bị hoặc hack mất facebook của các bạn.
                    </div>
                    <div data-order-by="reverse_time" id="commit-99011102" class="fb-comments" data-href="{{ $currentMovie->getUrl() }}" data-width="" data-numposts="10"></div>
                    <script>document.getElementById("commit-99011102").dataset.width = $("#commit-99011102").parent().width();</script>
                </div>
            </div>

            <div class="col-md-wide-3 col-xs-1 myui-sidebar hidden-sm hidden-xs">
                @foreach ($tops as $top)
                    @include('themes::thememotchill.inc.sidebar.' . $top['template'])
                @endforeach
            </div>
        </div>
    </div>

    {{-- Load player --}}
    <script>
        $('.btn-sv').on('click', function () {
            var __this = $(this);

            __this.parent().find('a.btn').removeClass('btn-active');
            __this.addClass('btn-active');
        });
        function removeAds(__this) {
            __this.closest('.player_ads').hide();
        }
    </script>
@endsection

@push('scripts')
    <script src="/themes/motchill/static/player/js/p2p-media-loader-core.min.js"></script>
    <script src="/themes/motchill/static/player/js/p2p-media-loader-hlsjs.min.js"></script>

    <script src="/js/jwplayer-8.9.3.js"></script>
    <script src="/js/hls.min.js"></script>
    <script src="/js/jwplayer.hlsjs.min.js"></script>

    <script>
        var episode_id = {{ $episode->id }};
        const wrapper = document.getElementById('player-loaded');
        const vastAds = "{{ Setting::get('jwplayer_advertising_file') }}";

        function chooseStreamingServer(el) {
            const type = el.dataset.type;
            const link = el.dataset.link.replace(/^http:\/\//i, 'https://');
            const id = el.dataset.id;

            const newUrl =
                location.protocol +
                "//" +
                location.host +
                location.pathname.replace(`-${episode_id}`, `-${id}`);

            history.pushState({
                path: newUrl
            }, "", newUrl);
            episode_id = id;

            Array.from(document.getElementsByClassName('streaming-server')).forEach(server => {
                server.classList.remove('bg-red-600');
            })
            el.classList.add('bg-red-600')

            renderPlayer(type, link, id);
        }

        function renderPlayer(type, link, id) {
            if (type == 'embed') {
                if (vastAds) {
                    wrapper.innerHTML = `<div id="fake_jwplayer"></div>`;
                    const fake_player = jwplayer("fake_jwplayer");
                    const objSetupFake = {
                        key: "{{ Setting::get('jwplayer_license') }}",
                        aspectratio: "16:9",
                        width: "100%",
                        file: "/themes/ripple/player/1s_blank.mp4",
                        volume: 100,
                        mute: false,
                        autostart: true,
                        advertising: {
                            tag: "{{ Setting::get('jwplayer_advertising_file') }}",
                            client: "vast",
                            vpaidmode: "insecure",
                            skipoffset: {{ (int) Setting::get('jwplayer_advertising_skipoffset') ?: 5 }}, // Bỏ qua quảng cáo trong vòng 5 giây
                            skipmessage: "Bỏ qua sau xx giây",
                            skiptext: "Bỏ qua"
                        }
                    };
                    fake_player.setup(objSetupFake);
                    fake_player.on('complete', function(event) {
                        $("#fake_jwplayer").remove();
                        wrapper.innerHTML = `<iframe width="100%" height="100%" src="${link}" frameborder="0" scrolling="no"
                        allowfullscreen="" allow='autoplay'></iframe>`
                        fake_player.remove();
                    });
                    fake_player.on('adSkipped', function(event) {
                        $("#fake_jwplayer").remove();
                        wrapper.innerHTML = `<iframe width="100%" height="100%" src="${link}" frameborder="0" scrolling="no"
                        allowfullscreen="" allow='autoplay'></iframe>`
                        fake_player.remove();
                    });
                    fake_player.on('adComplete', function(event) {
                        $("#fake_jwplayer").remove();
                        wrapper.innerHTML = `<iframe width="100%" height="100%" src="${link}" frameborder="0" scrolling="no"
                        allowfullscreen="" allow='autoplay'></iframe>`
                        fake_player.remove();
                    });
                } else {
                    if (wrapper) {
                        wrapper.innerHTML = `<iframe width="100%" height="100%" src="${link}" frameborder="0" scrolling="no"
                        allowfullscreen="" allow='autoplay'></iframe>`
                    }
                }
                return;
            }

            if (type == 'm3u8' || type == 'mp4') {
                wrapper.innerHTML = `<div id="jwplayer"></div>`;
                const player = jwplayer("jwplayer");
                const objSetup = {
                    key: "{{ Setting::get('jwplayer_license') }}",
                    aspectratio: "16:9",
                    width: "100%",
                    file: link,
                    playbackRateControls: true,
                    playbackRates: [0.25, 0.75, 1, 1.25],
                    sharing: {
                        sites: [
                            "reddit",
                            "facebook",
                            "twitter",
                            "googleplus",
                            "email",
                            "linkedin",
                        ],
                    },
                    volume: 100,
                    mute: false,
                    logo: {
                        file: "{{ Setting::get('jwplayer_logo_file') }}",
                        link: "{{ Setting::get('jwplayer_logo_link') }}",
                        position: "{{ Setting::get('jwplayer_logo_position') }}",
                    },
                    advertising: {
                        tag: "{{ Setting::get('jwplayer_advertising_file') }}",
                        client: "vast",
                        vpaidmode: "insecure",
                        skipoffset: {{ (int) Setting::get('jwplayer_advertising_skipoffset') ?: 5 }}, // Bỏ qua quảng cáo trong vòng 5 giây
                        skipmessage: "Bỏ qua sau xx giây",
                        skiptext: "Bỏ qua"
                    }
                };

                if (type == 'm3u8') {
                    const segments_in_queue = 50;

                    var engine_config = {
                        debug: !1,
                        segments: {
                            forwardSegmentCount: 50,
                        },
                        loader: {
                            cachedSegmentExpiration: 864e5,
                            cachedSegmentsCount: 1e3,
                            requiredSegmentsPriority: segments_in_queue,
                            httpDownloadMaxPriority: 9,
                            httpDownloadProbability: 0.06,
                            httpDownloadProbabilityInterval: 1e3,
                            httpDownloadProbabilitySkipIfNoPeers: !0,
                            p2pDownloadMaxPriority: 50,
                            httpFailedSegmentTimeout: 500,
                            simultaneousP2PDownloads: 20,
                            simultaneousHttpDownloads: 2,
                            // httpDownloadInitialTimeout: 12e4,
                            // httpDownloadInitialTimeoutPerSegment: 17e3,
                            httpDownloadInitialTimeout: 0,
                            httpDownloadInitialTimeoutPerSegment: 17e3,
                            httpUseRanges: !0,
                            maxBufferLength: 300,
                            // useP2P: false,
                        },
                    };
                    if (Hls.isSupported() && p2pml.hlsjs.Engine.isSupported()) {
                        var engine = new p2pml.hlsjs.Engine(engine_config);
                        player.setup(objSetup);
                        jwplayer_hls_provider.attach();
                        p2pml.hlsjs.initJwPlayer(player, {
                            liveSyncDurationCount: segments_in_queue, // To have at least 7 segments in queue
                            maxBufferLength: 300,
                            loader: engine.createLoaderClass(),
                        });
                    } else {
                        player.setup(objSetup);
                    }
                } else {
                    player.setup(objSetup);
                }

                const resumeData = 'OPCMS-PlayerPosition-' + id;

                player.on('ready', function() {
                    if (typeof(Storage) !== 'undefined') {
                        if (localStorage[resumeData] == '' || localStorage[resumeData] == 'undefined') {
                            console.log("No cookie for position found");
                            var currentPosition = 0;
                        } else {
                            if (localStorage[resumeData] == "null") {
                                localStorage[resumeData] = 0;
                            } else {
                                var currentPosition = localStorage[resumeData];
                            }
                            console.log("Position cookie found: " + localStorage[resumeData]);
                        }
                        player.once('play', function() {
                            console.log('Checking position cookie!');
                            console.log(Math.abs(player.getDuration() - currentPosition));
                            if (currentPosition > 180 && Math.abs(player.getDuration() - currentPosition) >
                                5) {
                                player.seek(currentPosition);
                            }
                        });
                        window.onunload = function() {
                            localStorage[resumeData] = player.getPosition();
                        }
                    } else {
                        console.log('Your browser is too old!');
                    }
                });

                player.on('complete', function() {
                    if (typeof(Storage) !== 'undefined') {
                        localStorage.removeItem(resumeData);
                    } else {
                        console.log('Your browser is too old!');
                    }
                })

                function formatSeconds(seconds) {
                    var date = new Date(1970, 0, 1);
                    date.setSeconds(seconds);
                    return date.toTimeString().replace(/.*(\d{2}:\d{2}:\d{2}).*/, "$1");
                }
            }
        }
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const episode = '{{ $episode->id }}';
            let playing = document.querySelector(`[data-id="${episode}"]`);
            if (playing) {
                playing.click();
                return;
            }

            const servers = document.getElementsByClassName('streaming-server');
            if (servers[0]) {
                servers[0].click();
            }
        });
    </script>
@endpush
