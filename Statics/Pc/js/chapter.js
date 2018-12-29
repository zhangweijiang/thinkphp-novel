(function () {
    // 变量定义
    var wList = ['640', '800', '900', '1280'],  // 阅读页宽度列表
        screenWidth = $(window).width(),  // 屏幕可视区域宽度
        screenHeight = $(window).height(), // 屏幕可视区域高度
        bodyDom = $('body'),    // body容器
        readMainWrap = $('#J_readMainWrapper'), // 阅读页主体部分容器
        goTop = $('#J_goTop'), // 返回顶部按钮容器
        leftBar = $('#J_leftBarList'),  // 左侧导航定参
        rightBar = $('#J_rightBarList'),  //右侧导航定参
        catalogBox = $('#J_catalogListWrapper'), // 目录容器
        leftNavCatalog = false,
        catalogAjaxBool = false,
        bookId = $('#bookFlag').data('bid'),
        SettingTemp = {
            theme: 1,
            fontFamily: 1,
            fontSize: 2,
            pageWidth: ((800 * 2 + 180) <= screenWidth) ? 800 : 640
        };

    // 初始化阅读设置
    function initSetting() {
        // 通过HTML5的localStorage存储阅读设置（localStorage可以永久存储数据,除被用户主动删除之外）
        if (localStorage.readSetting) {
            $.extend(SettingTemp, JSON.parse(localStorage.readSetting));
        } else {
            localStorage.readSetting = JSON.stringify(SettingTemp);
        }
        styleRender();
        showChapter();

    }
    // 事件绑定
    function eventBind() {
        // 左侧导航点击事件
        $('#J_leftBarList dd').on('click', function () {
            var that = $(this),
                target = that.data('target');
            if(target === undefined) {
                that.siblings().removeClass('active');
                $('.panel-wrapper').hide();
            } else {
                if (!that.hasClass('active')) {
                    that.addClass('active').siblings().removeClass('active');
                    $('.panel-wrapper').hide();
                    $('.panel-wrapper.' + target).show();
                } else {
                    that.removeClass('active');
                    $('.panel-wrapper.' + target).find('.close-panel').trigger('click');
                }
            }

        });

        // 左侧导航弹窗关闭
        $('#J_leftBarList .close-panel').on('click', function () {
            closePanel($(this));
        });

        // 滚动交互效果
        $(window).on('scroll', function () {
            var bottomTo,
                winScrollTop = $(window).scrollTop(),
                nowLeftTop = leftBarTop = 116,
                nowRightBottom = rightBarBottom = 133,
                pageHeight = $(document).height();

            //当滚动条位置大于leftBar距顶部的位置时,并且 nowLeftTop != 0 
            if (winScrollTop >= leftBarTop && nowLeftTop != 0) {
                nowLeftTop = 0;
                leftBar.css('top', nowLeftTop);
            } else if (winScrollTop < leftBarTop) {
                nowLeftTop = leftBarTop - winScrollTop;
                leftBar.css('top', nowLeftTop);
            }

            //获取滚动条距底部的距离
            bottomTo = pageHeight - screenHeight - rightBarBottom;
            //当滚动条位置大于rightBar距底部的位置时,并且 nowRightBottom != 0 
            if (winScrollTop <= bottomTo && nowRightBottom != 0) {
                nowRightBottom = 0;
                rightBar.css('bottom', nowRightBottom);
            } else if (winScrollTop > bottomTo) {
                nowRightBottom = rightBarBottom - pageHeight + screenHeight + winScrollTop;
                rightBar.css('bottom', nowRightBottom);
            }

            //回到顶部按钮是否出现
            if (winScrollTop > 0) {
                goTop.show();
            } else {
                goTop.hide();
            }
        }).trigger('scroll');

        // 评论按钮
        $('#J_rightBarList #J_comment').on('click', function () {
            layer.open({
                type: 1,
                title: ' ',
                skin: 'panel-wrapper comment',
                area: ['auto'],
                content: layui.jquery('#J_commentWrapper')
            });
        });

        // 返回顶部
        $('#J_goTop').on('click', function () {
            $('body, html').animate({scrollTop: 0}, 220);
        });

        // 目录列表展开/隐藏
        $('#J_catalogTabWrap').on('click', 'h3', function () {
            var that = $(this);
            that.hasClass('active') ? (that.removeClass('active'), that.next('.volume-list').slideUp()) : (that.addClass('active'), that.next('.volume-list').slideDown());
        });

        // 初始化目录最大高度 TODO: 屏幕尺寸变化时调用
        $('#J_catalogListWrapper').css('max-height', (screenHeight - 181) + 'px');

        // 主题及字体选择
        $('#J_themeList span, #J_fontFamily span').on('click', function () {
            var that = $(this),
                targetNum = parseInt(that.data('index')),
                parentId = that.parents('li').attr('id');

            that.addClass('active').siblings().removeClass('active');
            // 判断父节点的ID
            switch (parentId) {
                case 'J_themeList':
                    // 修改页面主题
                    bodyDom.attr('class', 'bg-theme-' + targetNum + ' w' + wList[SettingTemp.pageWidth]);
                    SettingTemp.theme = targetNum;
                    break;
                case 'J_fontFamily':
                    // 修改阅读内容字体
                    readMainWrap.attr('class', 'read-main-wrapper font-family-' + targetNum);
                    SettingTemp.fontFamily = targetNum;
                    break;
                default:
                    break;
            }
        });

        // 修改字号
        $('#J_fontSize span').on('click', function () {
            var that = $(this),
                sizeBox = that.parents('#J_fontSize'),
                sizeDom = sizeBox.find('.lang'),
                sizeNum = parseInt(sizeDom.text());

            // 字体大小修改
            if (that.hasClass('prev') && sizeNum > 12) {
                sizeNum -= 2;
            } else if (that.hasClass('next') && sizeNum < 48) {
                sizeNum += 2;
            } else {
                return false;
            }

            readMainWrap.css('font-size', sizeNum + 'px');
            sizeDom.text(sizeNum);
            SettingTemp.fontSize = (sizeNum - 12) / 2;
        });

        // 修改页宽
        $('#J_pageWidth span').on('click', function () {
            var that = $(this),
                widthDom = that.parents('#J_pageWidth').find('.lang'),
                widthNum = parseInt(widthDom.text()),
                numId;
            //获取宽度排序
            switch (widthNum) {
                case 640 :
                    numId = 0;
                    break;
                case 800 :
                    numId = 1;
                    break;
                case 900 :
                    numId = 2;
                    break;
                case 1280 :
                    numId = 3;
                    break;
            }

            if (that.hasClass('prev') && numId > 0) { // 宽度减小时，且w>640执行
                SettingTemp.pageWidth = numId - 1;
            } else if (that.hasClass('next') && numId < 3 && wList[numId + 1] <= screenWidth - 180) { // 宽度增大时，且w<1280执行，且当前屏幕宽度大于下次要增加到的宽度+180
                SettingTemp.pageWidth = numId + 1;
            } else {
                return false;
            }

            bodyDom.attr('class', 'bg-theme-' + SettingTemp.theme + ' w' + wList[SettingTemp.pageWidth]);
            widthDom.text(wList[SettingTemp.pageWidth]);
            $(window).trigger('resize');
        });

        // 保存阅读设置
        $('#J_setSave').on('click', function () {
            localStorage.readSetting = JSON.stringify($.extend(JSON.parse(localStorage.readSetting), SettingTemp));
            closePanel($(this));
        });

        // 不保存阅读设置
        $('#J_setCancel,#J_leftBarList #J_setting .close-panel').on('click', function () {
            SettingTemp = JSON.parse(localStorage.readSetting);
            styleRender();
            closePanel($(this));
        });

        wordCount('#J_commentText', '#J_commentCounter', 200);
    }

    // 样式渲染
    function styleRender() {
        bodyDom.attr('class', 'bg-theme-' + SettingTemp.theme + ' w' + wList[SettingTemp.pageWidth]);
        readMainWrap.attr('class', 'read-main-wrapper font-family-' + SettingTemp.fontFamily);
        readMainWrap.css('font-size', (SettingTemp.fontSize * 2 + 12) + 'px');

        $('#J_themeList span[data-index=' + SettingTemp.theme + ']').addClass('active').siblings().removeClass('active');
        $('#J_fontFamily span[data-index=' + SettingTemp.fontFamily + ']').addClass('active').siblings().removeClass('active');
        $('#J_fontSize .lang').text((SettingTemp.fontSize * 2 + 12));
        $('#J_pageWidth .lang').text(wList[SettingTemp.pageWidth]);
    }

    // 关闭面板
    function closePanel(target) {
        target.parents('.panel-wrapper').hide();
        $('#J_leftBarList dd').removeClass('active');
    }

    // 获取目录信息
    /*unction catalogAjax() {
        // TODO: 获取用户登陆标识

        // TODO: 获取目录列表(整理好的HTML结构+数据，请求时带参数:登陆状态及小说ID)

        // TODO: 把弹窗加入左侧导航中

        var catalogBox = $('#J_catalogListWrapper');

        if (!leftNavCatalog) {

            //判断是否已经发送ajax请求,
            if (!catalogAjaxBool) {
                //标识目录已经发送ajax
                catalogAjaxBool = true;
                $.ajax({
                    type: 'GET',
                    url: '/ajax/book/category',
                    dataType: 'json',
                    data: {
                        bookId: bookId
                    },
                    success: function (response) {
                        if (response.code === 0) {
                            // 加入目录列表容器中
                            catalogBox.html(response.data);
                            // 标识目录已经加载
                            leftNavCatalog = true;
                            // 设置目录展开区域的最大高度
                            $('.left-bar-list #J_catalogListWrapper').css('max-height', (screenHeight - 181) + 'px');
                            // 目录定位到章节
                            showChapter();
                        } else {
                            //标识目录未完成加载
                            leftNavCatalog = false;
                            catalogAjaxBool = false;
                        }
                    }
                });

            }

        }
    }*/

    // 目录定位到章节
    function showChapter() {
        // TODO:变量存储当前章节,根据章节ID定位HTML结构位置，展开卷并为章节加入定位样式

        //获取页面当前显示的章节id
        var nowChapterId = $('#chapterId').val(),
            chapterDom = $('#nav-chapter-' + nowChapterId),
            volumeList = chapterDom.parents('.volume-list');
        //移除li选中样式
        catalogBox.find('li.on').removeClass('on');
        //给新的目录章节添加选中样式
        chapterDom.addClass('on');
        //给新的选中章节做展开样式
        volumeList.prev('h3').addClass('cur').siblings('h3').removeClass('cur');
        volumeList.show().siblings('.volume-list').hide();
        //滚动到选中章节区域
        catalogBox.scrollTop(0).scrollTop(chapterDom.offset().top - catalogBox.offset().top);
    }

    eventBind();
    initSetting();
})();