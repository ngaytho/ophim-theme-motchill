(function() {
    var $;
    $ = this.jQuery || window.jQuery;
    win = $(window), body = $('body'), doc = $(document);

    $.fn.hc_accordion = function() {
        var acd = $(this);
        acd.find('ul>li').each(function(index, el) {
            var act = $(el).find('ul').is(':hidden') ? "" : 'active';
            if($(el).find('ul li').length > 0) $(el).prepend('<button type="button" class="acd-drop '+act+'"></button>');
        });
        acd.on('click','.acd-drop', function(e){
            e.preventDefault();
            var ul=$(this).nextAll("ul");
            if(ul.is(":hidden") === true){
                ul.parent('li').parent('ul').children('li').children('ul').slideUp(180);
                ul.parent('li').parent('ul').children('li').children('.acd-drop').removeClass("active");
                $(this).addClass("active");
                ul.slideDown(180);
            }
            else{
                $(this).removeClass("active");
                ul.slideUp(180);
            }
        });
    }
}).call(this);


jQuery(function($) {
    var win = $(window), body = $('body'), doc = $(document);

    var UI = {
        mMenu: function(){
            var m_nav = $('<div class="m-nav"><button class="m-nav-close">&times;</button><div class="nav-ct"></div></div>');
            body.append(m_nav);

            m_nav.find('.m-nav-close').click(function(e) {
                e.preventDefault();
                mnav_close();
            });

            m_nav.find('.nav-ct').append($('.nav__content .menu-block').children().clone());
            m_nav.find('ul').removeAttr('class')
            var mnav_open = function(){
                m_nav.addClass('active');
                body.append('<div class="m-nav-over"></div>').css('overflow', 'hidden');
            }
            var mnav_close = function(){
                m_nav.removeClass('active');
                body.children('.m-nav-over').remove();
                body.css('overflow', '');
            }

            doc.on('click', '.m-nav-open', function(e) {
                e.preventDefault();
                if(win.width() <=991) mnav_open();
            }).on('click', '.m-nav-over', function(e) {
                e.preventDefault();
                mnav_close();
            });
            m_nav.hc_accordion();
        },
        init: function(){
            UI.mMenu();
        },
    }

    UI.init();
});
function search_item() {
    var result_container = $('.search-dropdown-hot');
    var xhr = null;
    var inputTimer = null;
    var input = '';
    var search_item = function (str) {
        if (xhr) {
            xhr.abort();
        }
        xhr = $.ajax({
            type: 'GET',
            url: '/search',
            dataType: 'html',
            data: {
                t: 'nav_search',
                q: str,
                num: 3
            },
            beforeSend: function () {
                result_container.empty();
            },
            success: function (msg) {
                result_container.html(msg).show();
            },
            error: function () {
                renderError('timeout', str);
            }
        });
    };
    var renderError = function (str, keyword) {
        if (str == 'no item found' || str == 'timeout') {
            var _str = '';
            _str += '<p class="nav_search_notif">Không tìm thấy kết quả trả về cho từ khóa <b>\'' + keyword + '\'</b></p>';
            result_container.html(_str).show();
        }
    };
    $('.search-box').find('input[name=q]').on('keyup', function () {
        clearTimeout(inputTimer);
        var input = $(this).val();
        if (input.length > 2) {
            inputTimer = setTimeout(function () {
                search_item(input);
            }, 100);
        } else {
            result_container.hide();
        }
    });
}
$(function () {
    if (!detectMob()) {
        $(document).mouseup(function (e) {
            var container = $(".search-box");
            if (!container.is(e.target) && container.has(e.target).length === 0) {
                container.find('.search-dropdown-hot').hide();
            }
        });
        search_item();
    }
});
$('.flickity').flickity({
    cellAlign: 'left',
    contain: true,
    draggable: true,
    pageDots: false,
    autoPlay: true
});
$('body').on('click', '.control.menu', function () {
    var __this = $(this);
    $('.menu-block').slideToggle('fast');
    $('.myui-header__search').hide();
}).on('click', '.control.search', function () {
    var __this = $(this);
    $('.myui-header__search').toggle();
    $('.menu-block').slideUp('fast');
}).on('click', '.schedule-tab .tab', function() {
    var __this = $(this);
    __this.parent().find('.tab').removeClass('active');
    __this.addClass('active');
    var tabId = $(this).attr('data-id');
    $.ajax({
        url: '/ajax/schedule',
        type: 'post',
        data: {id: tabId},
        success: function (data) {
            $('.schedule-block .myui-vodlist').html(data);
        }
    });
});