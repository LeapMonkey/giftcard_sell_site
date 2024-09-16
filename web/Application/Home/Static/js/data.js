$(function(){
   $(".data-nav .data-nav-font ").on("click",function(){
        var address =$(this).attr("data-src");
		$(this).addClass("data-nav-border").siblings().removeClass("data-nav-border");
		$("iframe").attr("src",address);
   });
});