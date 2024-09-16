<?php
$config = array (	
		//应用ID,您的APPID。
		'app_id' => "2018060460263694",

		//商户私钥，您的原始格式RSA私钥
		'merchant_private_key' => "MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCwy8Xb8uRCyYEnDNXVuqx46A1/WrlRH6goMYRXP91nI5AoE0nZnDyrmpkQEMhZFH0adnB5VICU1Ama9kwjKUiQSVhSjL1gtJbuRZshefIywK1OPoNz2Ka1+zOt+Hg//Gayms79O5ingBZo7NDpl1p8zM5D3Ne2grFvFb0qnEA8f3TT0pSVoLctfXw/W7dXdEjxrDj4Z2AQllE+ieBjPldOewhkBj0lQi+NjXnV77VJqldqLRXV6awvrLTnFpp5hYxxRmXctxviuRvBsjsVxXeoB5pWOqOUguQqxoflUDbslNre6wivGLnU3F+lBOGmm/dBkRQ5mxVks2ukvDTRG2R5AgMBAAECggEAZmDqJ+9kzWQg3TsqjQT3oWBHiKtByz94wiXCjD/Yd0R50yswRcqau4jgABSn4CXkODHD+g68EvMiyNC90FK0yZEfi1YiyGBrUnmOVvlmDovTEA9VUf1wtMtN5v2OrQb9dYn+OViMktqyn3oblFloNgLnoxa899yA4WIGVCl9bUouW0jwhzcLH3ZwOY9Z0aciFNccumkoX8If08caHDgeYoA98+dYJMnRlMWsZbjLDecxD1qFEsUS4R83V7f6rFaVPy8J3nOYgeMwslCgjAr+cLuENNef/ohWalnWHfJzDv9T5+DLSdSgTulXcubwhq1/ojBDCVO4EU6FS4Q9NYmJAQKBgQDnWxSgt/0PAgSrR8PdUcvTws3RriMVTRikn+CNZ6bd5a2RqvNSvcLDWB7XoAd66ceijXtznMvPQkuXq3eW4/s1inhEOClp3AgQDEYkO+Yb9ACYgUbUUPj5NywGwRLVRGXSxkmO534Sa6GzBLtoLV1W0gxESKWOO23Z4c1JoldqoQKBgQDDoOH5nKfX9x2zGstUwByhmIzCJGsWTCHIWDfda1mq4/cWYdTxrNJpG8itv+EPHFc/22UTXvAahrYqYxt8n7zoJevaFuvGlwxWACrY6m3cejmiAk9/ARs+ehnlx5zpaimhDMmhetA/D4eEX7Pcvlo/YgWMpM24Se+6c0dA3bTC2QKBgDsAxY//nHBZaWttUAx/seX9xpP1P0lNcj158MDfcHUjAFMoMAooDyXWsXb4fy/fs2RHhPaFRI1FMRYrSsKWvhh3ihiL4jP3Q68kEjdnd1YlsokyXygCxpx4b6gBUZZGbVmF7HifnU2BWanYSRtWhnl/9FTt2hseGPP6XGqtFnJBAoGBALycVoLkyOZtuNK/2J5jgrdCbyOXHTAoQsH4XBQ8tf0PQWDw/e/sYgk44miygPiiqHRGwNLGS9QnYWbImu2yzN0+AoEIbFXRt/EKxETS8Q0LQpmyhJa8qFm6O1HQ/LYGxxAhVh08XZ8PB+dWpgED/dryVMte8YDmuB0Zm+8sUmUxAoGAZDYAwQts0zrQgelgSlpjRrOvX16Cr0QM/Mk9Z0U2uISR4i8Ycy0za6gWeEErap4JcRvpvibpHxAglO9D2oCXWkKhRbXLRwfM3BuZefSbzFg7Ir93RCSq4ciHTbe9qwROuhiGe5IIFsWEkvEMnJkPSko5/UdAbgw/ggUOkaJsyXM=",
		
		//异步通知地址
		'notify_url' => "http://www.shoukb.com/alipay.trade.wap.pay-PHP-UTF-8/notify_url.php",
		
		//同步跳转
		'return_url' => "http://www.shoukb.com/return_url.php",

		//编码格式
		'charset' => "UTF-8",

		//签名方式
		'sign_type'=>"RSA2",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

		//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAgFVhBp3xEN/lxW3sxHdsJJxoFl0KYBQX37vgKx284onuv+PH5Qn2wvQ6G3lKdjhii7N2EDr8lwM8+46l89IDXwyswuC+NWJbKg0Eo0dQ1aqEvgPjLeEBcf3QY/I/WPZ6LMOXFZxrSku07z7B5QXrnQbf0NhE1qjtfhJuagmxoq1wfu6fFcU+8kN/25ZUJqLnbzqQAiz21oBZsbJhkRYdsJYEHv9MEK3N8q7eRLcUhj1asG7t5T/0xLm8MbeIvvLF/wYtpajsThi2M1wzc4X1BS+M9BXTepo2a3EOZ8Z7pXX9rfmuAagMQarO/TG52AdDVaBB8os8O9G3nMW4+3gh4QIDAQAB",
		
	
);