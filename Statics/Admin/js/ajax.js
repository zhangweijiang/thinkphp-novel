/**
 * Created by Administrator on 2017/11/5.
 */
jQuery.App = {
    /*
     App.Ajax方法
     */
    ajax: function (type, url, data, callok, callerr) {
        //if(type==null || url==null || data==null || callback==null){alert('App.ajax参数不齐全!\n用法：$.App.ajax(type,url,data,callback)\n{\n type:提交类型[GET/POST]\n url:提交地址[URL]\n data:提交数据[数组]\n callback:是否返回数据[TRUE/FALSE]\n}');return false}
        var callok = callok ? callok : function () {
        };
        var callerr = callerr ? callerr : function () {
        };
        $.ajax({
            type: type,
            url: url,
            data: data,
            global: false,
            dataType: "json",
            beforeSend: $.App.loading(),//执行ajax前执行loading函数.直到success
            success: function (info) {
                $.App.loading();
                if (info.status) {
                    $.App.alert('success', info.msg);
                    callok();
                } else {
                    $.App.alert('danger', info.msg);
                    callerr();
                }
            }, //成功时执行Response函数
            error: function (info) {
                alert('操作失败，请重试或检查网络连接！')
            }//失败时调用函数
        })
    },//App.ajax方法结束

    /*
     App.loding方法
     */
    loading: function () {
        $('#App-loading-wrap').toggle();
    }, //App.loding方法结束

    /*
     App.alert方法
     */
    alert: function (type, msg, callok, callerr) {
        Notify(msg, 'top-right', '5000', type, 'fa-bolt', true);
        if (callok) {
            callok();
        }
        if (callerr) {
            callerr();
        }
    }, //App.alert方法结束

    /*
     App.confirm方法
     */
    confirm: function (msg, callok, callerr) {
        var msg = msg ? msg : "确认执行此操作?";
        var callok = callok ? callok : function () {
        };
        var callerr = callerr ? callerr : function () {
        };
        bootbox.confirm(msg, function (result) {
            if (result) {
                callok();
            } else {
                callerr();
            }
        });
    }, //App.alert方法结束
}; //插件结束
