
//验证手机号码
function checkMobile(tel) {
	var reg = /(^1[3|4|5|7|8][0-9]{9}$)/;
	if (reg.test(tel)) {
		return true;
	}else{
		return false;
	};
}
var iTime = 59;
var Account;
function RemainTime(){
	document.getElementById('zphone').disabled = true;
	var iSecond,sSecond="",sTime="";
	if (iTime >= 0){
		iSecond = parseInt(iTime%60);
		iMinute = parseInt(iTime/60)
		if (iSecond >= 0){
			if(iMinute>0){
				sSecond = iMinute + "分" + iSecond + "秒";
			}else{
				sSecond = iSecond + "秒";
			}
		}
		sTime=sSecond;
		if(iTime==0){
			clearTimeout(Account);
			sTime='获取验证码';
			iTime = 59;
			document.getElementById('zphone').disabled = false;
		}else{
			Account = setTimeout("RemainTime()",1000);
			iTime=iTime-1;
		}
	}else{
		sTime='没有倒计时';
	}
	$('#zphone').text(sTime);
	$("#zphone").removeAttr('disabled');
}

var wAlert = window.alert;
window.alert = function (message) {
	try {
		var iframe = document.createElement("IFRAME");
		iframe.style.display = "none";
		iframe.style.color = "pink";
		iframe.style.fontSize="240px";
		iframe.setAttribute("src", 'data:text/plain,');
		document.documentElement.appendChild(iframe);
		var alertFrame = window.frames[0];
		var iwindow = alertFrame.window;
		if (iwindow == undefined) {
			iwindow = alertFrame.contentWindow;
		}
		iwindow.alert(message);
		iframe.parentNode.removeChild(iframe);
	}catch (exc) {
		return wAlert(message);
	}
}



