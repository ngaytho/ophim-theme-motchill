<li class="col-lg-4 col-md-4 col-sm-3 col-xs-3">
    <div class="myui-vodlist__box">
        <a class="myui-vodlist__thumb"
            href="{{$movie->getUrl()}}"
            title="{{$movie->name}}"
            style="background: url({{$movie->getThumbUrl()}});"
        >
            <span class="play hidden-xs"></span>
            <span class="pic-tag pic-tag-top">
                {{$movie->episode_current}} {{$movie->language}}
            </span>
            <div class="myui-vodlist__detail">
                <h4 class="title text-overflow">{{$movie->name}}</h4>
                <p class="text text-overflow text-muted hidden-xs">
                    {{$movie->origin_name}}
                </p>
            </div>
        </a>
    </div>
</li>