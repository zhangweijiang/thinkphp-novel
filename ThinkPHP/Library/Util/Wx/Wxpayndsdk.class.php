<?php
/**
 * App专属微信响应类
 */

namespace Util\Wx;

class Wxpayndsdk
{
	public $data; //接收到的数据，类型为关联数组
	var $returnParameters; //返回参数，类型为关联数组

	// 微信参数设置
	public $set;

	public function __construct($options) {
		$this->set = M('set')->find();
	}

	function trimString($value) {
		$ret = null;
		if (null != $value) {
			$ret = $value;
			if (strlen($ret) == 0) {
				$ret = null;
			}
		}
		return $ret;
	}
	/**
	 * 	作用：将xml转为array
	 */
	public function xmlToArray($xml) {
		//将XML转为array
		$array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		return $array_data;
	}

	/**
	 * 将微信的请求xml转换成关联数组，以方便数据处理
	 */
	function saveData($xml) {
		$this->data = $this->xmlToArray($xml);
	}

	/**
	 * 	作用：格式化参数，签名过程需要使用
	 */
	function formatBizQueryParaMap($paraMap, $urlencode) {
		$buff = "";
		ksort($paraMap);
		foreach ($paraMap as $k => $v) {
			if ($urlencode) {
				$v = urlencode($v);
			}
			//$buff .= strtolower($k) . "=" . $v . "&";
			$buff .= $k . "=" . $v . "&";
		}
		$reqPar;
		if (strlen($buff) > 0) {
			$reqPar = substr($buff, 0, strlen($buff) - 1);
		}
		return $reqPar;
	}

	public function getSign($Obj) {
		foreach ($Obj as $k => $v) {
			$Parameters[$k] = $v;
		}
		//签名步骤一：按字典序排序参数
		ksort($Parameters);
		$String = $this->formatBizQueryParaMap($Parameters, false);
		//echo '【string1】'.$String.'</br>';
		//签名步骤二：在string后加入KEY
		//$String = $String . "&key=13903072727139030727271390307272";
		$String = $String . "&key=" . $this->set['wxmchkey'];
		//echo "【string2】".$String."</br>";
		//签名步骤三：MD5加密
		$String = md5($String);
		//echo "【string3】 ".$String."</br>";
		//签名步骤四：所有字符转为大写
		$result_ = strtoupper($String);
		//echo "【result】 ".$result_."</br>";
		return $result_;
	}
	function checkSign() {
		$tmpData = $this->data;
		unset($tmpData['sign']);
		$sign = $this->getSign($tmpData); //本地签名
		if ($this->data['sign'] == $sign) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * 	作用：array转xml
	 */
	function arrayToXml($arr) {
		$xml = "<xml>";
		foreach ($arr as $key => $val) {
			if (is_numeric($val)) {
				$xml .= "<" . $key . ">" . $val . "</" . $key . ">";

			} else {
				$xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
			}

		}
		$xml .= "</xml>";
		return $xml;
	}

	/**
	 * 获取微信的请求数据
	 */
	function getData() {
		return $this->data;
	}

	/**
	 * 设置返回微信的xml数据
	 */
	function setReturnParameter($parameter, $parameterValue) {
		$this->returnParameters[$this->trimString($parameter)] = $this->trimString($parameterValue);
	}

	/**
	 * 生成接口参数xml
	 */
	function createXml() {
		return $this->arrayToXml($this->returnParameters);
	}

	/**
	 * 将xml数据返回微信
	 */
	function returnXml() {
		$returnXml = $this->createXml();
		return $returnXml;
	}
}
