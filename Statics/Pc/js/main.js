(function () {
    //自定义tab组件
    $('.tab li').on('click', function () {
        var target = $(this).data('target').trim();
        if (target && target !== "") {
            $(this).addClass('active').siblings().removeClass('active');
            $('.tab-pane#' + target).addClass('active').siblings('.tab-pane').removeClass('active');
        }
    });

    // 导航栏事件
    $('#pin-nav').on('mouseenter', '.site-nav li:not(.site), li.sign-in', function () {
        $('#pin-nav').find('li').removeClass('active');
        $(this).addClass('active');
    }).on('mouseleave', 'li', function () {
        $(this).removeClass('active');
    });

    // 登录、注册页页面初始化高度,个人中心初始化页面最小高度
    $(window).on('resize', function () {
        $('.login-area-wrapper').css('height', ($(window).height() - 355 <= 430 ? 430 : ($(window).height() - 355)) + "px");
        $('.personal-content').css('min-height', ($(window).height() - 176 ) + "px");
    }).trigger('resize');

})();