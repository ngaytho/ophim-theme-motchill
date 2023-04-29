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
                                <div class="myui-player__notice">Lịch chiếu: {!! $currentMovie->showtimes !!}</div>
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
                @foreach ($tops as $top)
                    @include('themes::thememotchill.inc.sidebar.' . $top['template'])
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('scripts')   
    {!! setting('site_scripts_facebook_sdk') !!}
@endpush