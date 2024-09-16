var img_n = '/Application/Home/Static/images/radio_n.png';
var img_s = '/Application/Home/Static/images/radio_s.png';
var img_xz = '/Application/Home/Static/images/xuanz.png';
// 控制协议选中
//$('#radio1').click(function() {
$("body").on('click', "#radio1", function(){
	if ($(this).attr('src') == img_n) {
		$(this).attr('src', img_s);
		$(this).addClass('on');
	} else {
		$(this).attr('src', img_n);
        $(this).removeClass('on');
	}
})

//$('#radio2').click(function() {
$("body").on('click', "#radio2", function(){
	if ($(this).attr('src') == img_n) {
		$(this).attr('src', img_s)
        $(this).addClass('on');
	} else {
		$(this).attr('src', img_n)
        $(this).removeClass('on');
	}
})
// 选择卡类1
$(".sellCard-select-bg").on("click", function() {
	$(".sellCard-select-bg").find('.sellCard-select-bg-select').hide();
	$(this).find('.sellCard-select-bg-select').toggle();
});
// 选择卡类2
$(function() {
	$(".sellCard-kind-bg").on("click", function() {
		$(this).addClass('sellCard-kind-bg-color').siblings().removeClass('sellCard-kind-bg-color');
		$(".sellCard-kind-bg").find('.sellCard-kind-bg-discount').addClass('showImg');
		$(this).find('.sellCard-kind-bg-discount').removeClass('showImg');
	});
});
