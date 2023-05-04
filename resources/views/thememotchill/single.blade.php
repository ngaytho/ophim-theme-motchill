@extends('themes::thememotchill.layout')

@php
    $watchUrl = '#';
    if (!$currentMovie->is_copyright && count($currentMovie->episodes) && $currentMovie->episodes[0]['link'] != '') {
        $watchUrl = $currentMovie->episodes
            ->sortBy([['server', 'asc']])
            ->groupBy('server')
            ->first()
            ->sortByDesc('name', SORT_NATURAL)
            ->groupBy('name')
            ->last()
            ->sortByDesc('type')
            ->first()
            ->getUrl();
    }
    if ($currentMovie->status == 'trailer') {
        $watchUrl = 'javascript:alert("Phim đang được cập nhật!")';
    }
@endphp

@section('content')
    <div class="detail-bl myui-panel col-pd clearfix" itemscope itemtype="http://schema.org/Movie">
        <div class="row">
            <div class="col-md-wide-7 col-xs-1 padding-0">
                <div class="detail-block">
                    <div class="myui-content__thumb">
                        <a
                            class="myui-vodlist__thumb img-md-220 img-sm-220 img-xs-130 picture"
                            href="{{ $watchUrl }}"
                            title="Xem phim {{ $currentMovie->name }}">
                            <img
                                itemprop="image"
                                alt="Xem phim {{ $currentMovie->name }}"
                                src="{{ $currentMovie->getThumbUrl() }}" />
                                <span class="play hidden-xs"></span>
                                <span class="btn btn-default btn-block btn-watch">XEM PHIM</span>
                            </a>
                        </div>
                    <div class="myui-content__detail">
                        <h1 class="title text-fff" itemprop="name">{{ $currentMovie->name }}</h1>
                        <h2 class="title2">{{ $currentMovie->origin_name }}</h2>
                        <div class="myui-media-info">
                            <div class="info-block">
                                <h6>Trạng thái:
                                    <span itemprop="duration" class="badge">{{ $currentMovie->episode_current }} {{ $currentMovie->language }}</span>
                                </h6>
                                <h6>Thể loại:
                                    {!! $currentMovie->categories->map(function ($category) {
                                        return '<a href="' . $category->getUrl() . '" tite="' . $category->name . '">' . $category->name . '</a>';
                                    })->implode(', ') !!}
                                </h6>
                                <h6>Đạo diễn:
                                    {!! count($currentMovie->directors)
                                        ? $currentMovie->directors->map(function ($director) {
                                                return '<a href="' .
                                                    $director->getUrl() .
                                                    '" tite="Đạo diễn ' .
                                                    $director->name .
                                                    '"><span itemprop="director">' .
                                                    $director->name .
                                                    '</span></a>';
                                            })->implode(', ')
                                        : 'N/A' !!}
                                </h6>
                                {{-- <h6>Sắp Chiếu: <span>Tập 30 VietSub</span></h6> --}}
                                <h6>Diễn viên:
                                    {!! count($currentMovie->actors)
                                        ? $currentMovie->actors->map(function ($actor) {
                                                return '<a href="' . $actor->getUrl() . '" tite="Diễn viên ' . $actor->name . '"><span itemprop="actor">' . $actor->name . '</span></a>';
                                            })->implode(', ')
                                        : 'N/A' !!}
                                </h6>
                            </div>

                            @if ($currentMovie->showtimes && $currentMovie->showtimes != '')
                                <div class="myui-player__notice">Lịch chiếu: {!! strip_tags($currentMovie->showtimes) !!}</div>
                            @endif

                            <div class="rating-block">
                                @include('themes::thememotchill.inc.rating2')
                            </div>
                        </div>
                    </div>
                </div>
                <div class="myui-movie-detail">
                    <h3 class="title">Nội dung chi tiết</h3>
                    <div class="text-collapse content">
                        <div class="sketch content" itemprop="description">
                            <h3>{{ $currentMovie->name }}</h3>
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

                @if (get_theme_option('show_fb_comment_in_single'))
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
                @endif

                <div class="myui-panel myui-panel-bg clearfix">
                    <div class="myui-panel-box clearfix">
                        <div class="myui-panel__head active bottom-line clearfix">
                            <h3 class="title">Có thể bạn sẽ thích</h3>
                        </div>

                        <ul id="type" class="myui-vodlist__bd clearfix">

                            @foreach ($movie_related as $movie)
                                <li class="col-md-4 col-sm-4 col-xs-3">
                                    <div class="myui-vodlist__box">
                                        <a class="myui-vodlist__thumb"
                                            href="{{ $movie->getUrl() }}"
                                            title="{{ $movie->name }}"
                                            style="background: url({{ $movie->getThumbUrl() }});">
                                            
                                            <span class="play hidden-xs"></span>
                                            <span class="pic-tag pic-tag-top">{{ $movie->episode_current }} {{ $movie->language }}</span>
                                            </a>
                                        <div class="myui-vodlist__detail">
                                            <h4 class="title text-overflow">
                                                <a href="{{ $movie->getUrl() }}" title="{{ $movie->name }}">
                                                    {{ $movie->name }}
                                                </a>
                                            </h4>
                                            <p class="text text-overflow text-muted hidden-xs">
                                                {{ $movie->origin_name }}
                                            </p>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-wide-3 col-xs-1 myui-sidebar hidden-sm hidden-xs">
                @include('themes::thememotchill.sidebar')
            </div>
        </div>
    </div>
@endsection

@push('scripts')   
    {!! setting('site_scripts_facebook_sdk') !!}
@endpush