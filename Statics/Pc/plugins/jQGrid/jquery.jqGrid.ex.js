//默认行数
var c_rowNum = 20;
//Grid行数数组
var c_rowList = [20, 40, 80, 100];
//标准日期格式
var c_formatoptions = { srcformat: 'Y-m-d H:i:s', newformat: 'Y-m-d H:i:s' };
//年月日格式
var short_formatoptions = { srcformat: 'Y-m-d H:i:s', newformat: 'Y-m-d' };

//更新jqgrid分页图标
function updatePagerIcons(table) {
    var replacement =
    {
        'ui-icon-seek-first': 'icon-double-angle-left icon-2x',
        'ui-icon-seek-prev': 'icon-angle-left icon-2x',
        'ui-icon-seek-next': 'icon-angle-right icon-2x',
        'ui-icon-seek-end': 'icon-double-angle-right icon-2x',
        'ui-icon-circle-triangle-n': 'icon-angle-down icon-2x'
    };
    $('.ui-pg-table:not(.navtable) > tbody > tr > .ui-pg-button > .ui-icon').each(function () {
        var icon = $(this);
        var $class = $.trim(icon.attr('class').replace('ui-icon', ''));
        if ($class in replacement) icon.attr('class', 'ui-icon ' + replacement[$class]);
    });
    $('.HeaderButton>span').removeClass('ui-icon-circle-triangle-n').addClass('icon-chevron-down');
    $('.ui-separator').remove();
}

//分页提示
function enableTooltips(table) {
    $('.navtable .ui-pg-button').tooltip({ container: 'body' });
    $(table).find('.ui-pg-div').tooltip({ container: 'body' });
}

//Grid无数据时，提示无数据显示
function norecordTooltips(grid) {
    var rowNum = $(grid).jqGrid('getGridParam', 'records');
    if (rowNum == 0) {
        if ($("#norecords").html() == null) {
            $(grid).parent().append("<div id='norecords' style='text-align:center;font-size:16px;margin:100px;'>无数据显示</div>");
        }
        else
            $("#norecords").show();
    } else {//如果存在记录，则隐藏提示信息。
        $("#norecords").hide();
    }
}

function StrLenFormat(cellvalue, options, rowObject) {
    var oldstrlen = cellvalue.length;

    var newstr = "";

    if (oldstrlen > 10) {
        newstr = cellvalue.substring(0,30) + "...";

        newstr = "<div title='" + cellvalue + "'>" + newstr + "</div>";
    } else {
        newstr = cellvalue;
    }
    return newstr;
}

function SetPercentRate(cellvalue, options, rowObject) {
    var newstrs = (cellvalue=="0"?"0":cellvalue+"%");
    return newstrs;
}