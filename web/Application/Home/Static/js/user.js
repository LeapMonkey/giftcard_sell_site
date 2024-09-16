// 切换效果及显示内容
$(function() {
	$(".user-sub-nav .user-sub-nav-floor ").on("click", function() {
		var address = $(this).attr("data-src");
		console.log($(this))
		$(this).addClass("nav-select").siblings().removeClass("nav-select");
		$("iframe").attr("src", address);
	});
});

//关闭弹出层
$('.close').on('click',function(){
	$(this).parent().parent().hide();
	$('#fullbg').hide();

})
//关闭弹出层
$('.close_parent').on('click',function(){
    $(this).parent().parent().parent().hide();
    $('#fullbg').hide();

})


// 打开弹出层
window.showModel = function(type) {
	var height = document.documentElement.clientHeight;
	var width = document.documentElement.clientWidth;
	$('#fullbg').css({
		width: width,
		height: height,
		display: 'block'
	});

	$('.aletr-model[data-type='+type+']').css('display', 'block');
}