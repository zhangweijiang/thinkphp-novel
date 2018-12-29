<?php
    if(C('LAYOUT_ON')) {
        echo '{__NOLAYOUT__}';
    }
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>跳转提示</title>
<style type="text/css">
*{ padding: 0; margin: 0; }
body{ background: #f4f4f4; font-family: '微软雅黑'; color: #333; font-size: 16px; }
.system-message{ width:100%; max-width:720px; margin:0 auto;}
.system-message h1{ display: block; width:51%; margin:190px auto 20px auto; text-align:center;}
.system-message h1 img{ width:100%; }
.system-message .jump{ padding-top: 10px; text-align:center; font-size:1.6em;}
.system-message .jump a{ color: #333;}
.system-message .success,.system-message .error{ line-height: 1.8em; font-size: 36px }
.system-message .detail{ font-size: 12px; line-height: 20px; margin-top: 12px; display:none}
</style>
</head>
<body>
<div class="system-message">
<present name="message">
<h1><img src="__PUBLIC__/Common/img/success.png"/></h1>
<p class="success" style="text-align: center"><?php echo($message); ?></p>
<else/>
<h1><img src="__PUBLIC__/Common/img/failed.png"/></h1>
<p class="error" style="text-align: center"><?php echo($error); ?></p>
</present>
<p class="detail"></p>
<p class="jump">
页面自动 <a id="href" href="<?php echo($jumpUrl); ?>">跳转</a> 等待时间： <b id="wait"><?php echo($waitSecond); ?></b>
</p>
</div>
<script type="text/javascript">
(function(){
var wait = document.getElementById('wait'),href = document.getElementById('href').href;
var interval = setInterval(function(){
	var time = --wait.innerHTML;
	if(time <= 0) {
		location.href = href;
		clearInterval(interval);
	};
}, 1000);
})();
</script>
</body>
</html>
