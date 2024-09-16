// 控制弹窗
$('.show_alert_model').on('click', function() {
	window.parent.showModel($(this).data('type'));
})
// 控制查询日期选中
$(function() {
	$('.record-date-day').on('click', function() {
		$(this).addClass('record-date-color').siblings().removeClass("record-date-color");
	})
})
