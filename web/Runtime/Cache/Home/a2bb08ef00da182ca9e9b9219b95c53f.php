<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    
	<title>百分百收卡网_礼品卡兑换_一家专注二手礼品卡回收的网站 - www.ohbbs.cn 欧皇源码论坛 </title>

    <meta name="keywords" content="<?php echo C('KEYWORDS');?>">
    <meta name="description" content="<?php echo C('DESCRIPTION');?>">
     
    <link rel="stylesheet" type="text/css" href="/Application/Home/Static/css/base.css"/>
    <link rel="stylesheet" href="/Application/Home/Static/css/style2.css">
    <!--<link rel="stylesheet" type="text/css" href="/Application/Home/Static/css/index.css"/>-->
    
	<link rel="stylesheet" type="text/css" href="/Application/Home/Static/css/sellcard_one.css"/>
	<link rel="stylesheet" type="text/css" href="/Application/Home/Static/css/style.css"/>
	<!--<link rel="stylesheet" type="text/css" href="/Application/Home/Static/css/base.css"/>-->
<style>
	.body{
		background-color: #fff;
		height: auto;
	}
	#ul_pics li{
	float:left;
	}
	#ul_pics li img{
		width:130px;
		height:130px;
	}
	.delete_imgBox {
		width: 130px;
		height: 30px;
		position: absolute;
		background: rgba(0,0,0,0.5);
		font-size: 14px;
		color: #fff !important;
		z-index: 100;
		text-align: center;
		line-height: 30px;
		margin-top: 100px;
	}
</style>

    <style type="text/css">
        body{
            min-width: 1200px;
        }
    </style>
    <script>
        // var _hmt = _hmt || [];
        // (function() {
        //     var hm = document.createElement("script");
        //     hm.src = "https://hm.baidu.com/hm.js?1d59f51ca1e06504857c1dbf0b8ba09c";
        //     var s = document.getElementsByTagName("script")[0];
        //     s.parentNode.insertBefore(hm, s);
        // })();
    </script>
</head>
<body>

    <header>
        <div class="head_z"></div>
        <div class="head_f">
            <div class="layout clear">
                <div class="logo fl">
                    <a href="/">
                    <img src="/Application/Home/Static/images/logo2.png" alt="">
                    </a>
                </div>
                <ul class="head_list fl">
                    <li class="active">
                    <a href="/">
                        首页
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo U('card/sellcard');?>">
                            寄售中心
                        </a>
                    </li>
                    <li><a href="<?php echo U('Card/recycle');?>">企业回收</a></li>
                </ul>
                <?php if(is_login()): ?><div class="user-right fr cf">
                    <div class="user-right-name fl cf" >
                        <a href="<?php echo U('User/index');?>"><img class="user-avatar fl" src="/Application/Home/Static/images/default.png">
                        <span class="user-font fl"><?php echo ($userInfo['username']); ?></span>
                        <img class="user-jiantou fl" src="/Application/Home/Static/images/xiangx@2x.png">
                        </a>
                        <div class="login-drop">
                            <img class="dropDown-top" src="/Application/Home/Static/images/top.png" >
                            <div class="dropDown-content">
                                <a href="<?php echo U('User/index');?>">
                                    <div class="dropDown-floor cf">
                                        <!--<img class=" fl" src="/Application/Home/Static/images/subicon1.png" >-->
                                        <span class="fl" style="margin-left: 47px; ">账户中心</span>
                                    </div>
                                </a>
                                <!--<div class="line"></div>-->
                                <!--<a href="<?php echo U('User/sellCardRecord');?>" target="_top">-->
                                    <!--<div class="dropDown-floor cf">-->
                                        <!--<img class=" fl" src="/Application/Home/Static/images/subicon3.png" >-->
                                        <!--<span class="fl">卖卡记录</span>-->
                                    <!--</div>-->
                                <!--</a>-->
                                <div class="line"></div>
                                <!--<a href="<?php echo U('Home/Public/logout');?>">-->
                                    <div class="dropDown-floor">
                                        <!--<img class=" fl" src="/Application/Home/Static/images/subicon2.png" >-->
                                        <span class="fl loginout" style="margin-left: 47px; ">退出登录</span>
                                    </div>
                                <!--</a>-->
                            </div>
                            <img class="dropDown-bto" src="/Application/Home/Static/images/bot.png" >
                        </div>
                    </div>

                    <!--<div class=" content-right-box fr cf">-->
                        <!--<img class="help-pic fl" src="/Application/Home/Static/images/bangz@2x.png">-->
                        <!--<span class="user-help-font fr">帮助中心</span>-->
                    <!--</div>-->
                    <div class="user-right-help fr cf">
                        <img class="help-pic fl" src="/Application/Home/Static/images/bangz@2x.png">
                        <span class="user-help-font fr" style="cursor: pointer">帮助中心</span>

                        <div class="dropDown-help help-login">
                            <img class="dropDown-top" src="/Application/Home/Static/images/top.png" >
                            <div class="help-content">
                                <div class="help-title cf">
                                    <a href="<?php echo U('Help/Index');?>"> <span class="fl ">帮助中心</span></a>
                                    <div class="help-line fl"></div>
                                    <a href="<?php echo U('Help/Index',array('id'=>13));?>">  <span class="fr">卖卡流程</span></a>
                                </div>
                                <div class="line"></div>
                                <div class="help-box">
                                    <div class="help-box-serve">或直接拨打客服热线</div>
                                    <div class="help-box-num">8373185</div>
                                    <div class="help-box-date">周一至周日 8:00-24:00</div>
                                </div>
                                <div class="line"></div>
                                <a target="_blank" href="http://wpa.qq.com/msgrd?v=3&amp;uin=<?php echo C('QQ');?>&amp;site=qq&amp;menu=yes"><div class="help-box-bot">联系在线客服</div></a>
                            </div>
                            <img class="dropDown-bto" src="/Application/Home/Static/images/bot.png" >
                        </div>
                    </div>

                </div>
                <?php else: ?>
                <div class="fr login_box">
                    <button class="btn_login loginbox">登录
                        <div class="login_way shadow" style="display:none" >
                            <ul>
                                <li>
                                   <a >
                                          <img  src="/Application/Home/Static/images/shoujihao.png" >
                                        <span>账号密码</span>
                                   </a>
                                </li>
                                <li>  <a  href="https://graph.qq.com/oauth2.0/authorize?client_id=101578620&amp;response_type=token&amp;scope=all&amp;redirect_uri=http://www.shoukb.com/Home/Login/qq">
                                         <img src="/Application/Home/Static/images/qq.png" >
                                        <span>qq登录</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://open.weixin.qq.com/connect/qrconnect?appid=wx6353058509cb4eda&redirect_uri=http://www.shoukb.com/Home/Index/webchatLogin&response_type=code&scope=snsapi_login&state=#wechat_redirect">
                                        <img  src="/Application/Home/Static/images/weixin.png" >
                                        <span>微信登录</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </button>
                    <a  class="singup">
                        <button class="btn_login" id="register">注册</button>
                    </a>
                </div><?php endif; ?>
            </div>
        </div>
    </header>

    
	<div class="sellCard">
		<div class="sellCard-title">
			<span class="sellCard-title-font">我要卖卡</span>
			<span class="sellCard-title-text">（您好，<?php echo ($user_info['username']); ?>您是百分百收卡网的客户）</span>
		</div>
		<div class="sellCard-select cf">
			<span class="sellCard-select-title fl">选择卡类：</span>
			<?php if(is_array($cardtype_info)): $i = 0; $__LIST__ = $cardtype_info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a href="<?php echo U('Home/Card/sellCard?typeid='.$vo['id']);?>" style="color: #fff">
					<div class="sellCard-select-bg fl">
						<img class="sellCard-select-bg-pic" src="<?php echo ($vo['photo_path']); ?>">
						<div class="sellCard-select-bg-font"><?php echo ($vo["name"]); ?></div>
						<!--<img class="sellCard-select-bg-select showImg" src="/Application/Home/Static/images/xuanz.png" >-->
						<!--<?php if(($key) == "0"): ?>-->
						<!--<img class="sellCard-select-bg-select showImg" src="/Application/Home/Static/images/xuanz.png" style="display: block">-->
						<!--<?php endif; ?>-->
						<!--<?php if(($vo['id']) == $typeid): ?>-->
						<!--<img class="sellCard-select-bg-select showImg" src="/Application/Home/Static/images/xuanz.png" style="display: block">-->
						<!--<?php endif; ?>-->

						<?php if(($vo['id'] == $typeid) || ($typeid == '')): ?><img class="sellCard-select-bg-select showImg type_active" data-id="<?php echo ($vo['id']); ?>" src="/Application/Home/Static/images/xuanz.png" style="display: block" ><?php endif; ?>

					</div>
				</a><?php endforeach; endif; else: echo "" ;endif; ?>
		</div>

		<div class="sellCard-kind cf">
			<span class="sellCard-kind-title fl">选择卡种：</span>
			<div class="sellCard-right cf" style="padding-left: 102px;">
				<?php if(is_array($cardinfo)): $i = 0; $__LIST__ = $cardinfo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(($key) == "0"): ?><div class="sellCard-kind-bg fl kind-item sellCard-kind-bg-color" data-id="<?php echo ($vo['id']); ?>">
							<img class="sellCard-kind-bg-pic" src="<?php echo ($vo["photo_path"]); ?>">
							<span class="sellCard-kind-bg-font"><?php echo ($vo["name"]); ?></span>
							<img class="sellCard-kind-bg-img showImg" src="/Application/Home/Static/images/car_xz.png">
							<span class="sellCard-kind-bg-discount showImg sellCard-kind-bg-font"><?php echo ($vo['sale_proportion']); ?>折</span>
						</div>
						<?php else: ?>
						<div class="sellCard-kind-bg fl kind-item" data-id="<?php echo ($vo['id']); ?>">
							<img class="sellCard-kind-bg-pic" src="<?php echo ($vo["photo_path"]); ?>">
							<span class="sellCard-kind-bg-font"><?php echo ($vo["name"]); ?></span>
							<!-- <img class="sellCard-kind-bg-img showImg" src="../../img/car_xz.png"> -->
							<span class="sellCard-kind-bg-discount showImg"><?php echo ($vo['sale_proportion']); ?>折</span>
						</div><?php endif; endforeach; endif; else: echo "" ;endif; ?>
			</div>
		</div>
		<div class="ajax_more">
		<?php if(($cardinfo[0]['offline'] == 0) && ($cardinfo[0]['is_entitycard'] == 0)): ?><div class="sellCard-faceValue cf">
					<div class="sellCard-faceValue-left fl" style="margin-right: 0">
						<div class="sellCard-faceValue-left-font">单张面值：</div>
						<div class="sellCard-faceValue-left-text">不得选错</div>
					</div>
					<div class="sellCard-faceValue-right  cf" style="padding-left: 167px">
						<?php if(is_array($result_list)): $i = 0; $__LIST__ = $result_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if($cardpriceid == $vo['id']): ?><div class="sellCard-faceValue-right-money fl money-item on" val="<?php echo ($vo["id"]); ?>" onclick="pselect(<?php echo ($vo['price']); ?>);">
									<?php else: ?>
								<div class="sellCard-faceValue-right-money fl money-item " val="<?php echo ($vo["id"]); ?>" onclick="pselect(<?php echo ($vo['price']); ?>);"><?php endif; ?>
							<!----判断是否是视频-->
							<?php if($typeid == 118): ?><div class="money-num " ><?php echo ($vo["price_name"]); ?></div>
								<div class="money-discount">
									<span>回收价</span><span>.</span><span class="money-discount-color">￥<?php echo ($vo["sale_price"]); ?></span>
								</div>
								<?php else: ?>
								<div class="money-num " ><?php echo ($vo["price"]); ?></div>
								<div class="money-discount">
									<span>￥<?php echo ($vo["sale_price"]); ?></span><span>.</span><span class="money-discount-color"><?php echo ($vo['card_proportion']); ?>折</span>
								</div><?php endif; ?>
							<!---判断是否是视频结束--->
					</div><?php endforeach; endif; else: echo "" ;endif; ?>
					<?php if($typeid != 118): ?><div class="sellCard-faceValue-right-money money-item fl zdy">
							<div class="money-num">自定义</div>
							<div class="money-discount">
								<span id="zdy">回收折扣</span><span>.</span><span class="money-discount-color"><?php echo ($zdy_proportion); ?>折</span>
							</div>
						</div><?php endif; ?>
				</div>
		</div>
		<div class="column cf sellCard-submit">
			<div class="name sellCard-submit-left fl">提交方式</div>
			<div class="card-way box-width cf fl">
				<div class="way-item item fl on" id="choose-batch">批量提交<img class="sel" src="/Application/Home/Static/images/sel.png" alt=""></div>
				<div class="way-item item fl" id="choose-single">单张提交</div>
			</div>

		</div>
		<div class="column cf sellCard-submit">
			<div style="padding-left: 100px;color: #D35400" id="introduce"><?php echo ($introduce); ?></div>
		</div>
		<div id="recycle-sumbit"   class="column cf ">
			<div class="column cf sellCard-submit sellCard-submit-left">
				<div class="fl name">卡号/密码</div>
				<div class="card-num cf fl" id="choose_batch" style="position: relative;">
					<textarea class="form-controls" id="cardlist" placeholder="卡号和卡密之间用“空格键”隔开，每张卡占用一行用“回车（Enter键）”隔开" onkeydown="cardnum(this,event)" onblur="cardnum(this,'blur')"></textarea>
					<div class="textarea-tips">
						<div class="mask-black"></div>
						<ul class="textarea-tips-list" id="card-tips">
							<!--<li id="c_0" class="error_war"></li>-->
							<!--<li id="c_1" class="error_war"></li>-->
							<!--<li id="c_2" class="error_war"></li>-->
							<!--<li id="c_3" class="error_war"></li>-->
							<!--<li id="c_4" class="error_war"></li>-->
							<!--<li id="c_5" class="error_war"></li>-->
							<!--<li id="c_6" class="error_war"></li>-->
							<!--<li id="c_7" class="error_war"></li>-->
							<li class="textarea-attention">
								<i class="iconfont iconfont-tips"></i>
								<?php if($onlypass == 1): ?><p class="text-red nocode" style="display: block;">

										<?php else: ?>
									<p class="text-red nocode" style="display: none;"><?php endif; ?>
								卡号与卡密之间请用<strong class="text-orange">“空格”</strong>隔开，<br>每张卡占用一行用 <strong class="text-orange">“回车(Enter键)”</strong> 隔开，例：</p>
								<?php if($onlypass == 0): ?><p class="text-red hide onlypass" style="display: block;">
										<?php else: ?>
									<p class="text-red hide onlypass" style="display: none;"><?php endif; ?>
								卡券面值不能有误，请核对后提交，<br>每张一行用<strong class="text-orange">“回车(Enter键)”</strong>隔开！</p>
								<p class="h5 text-gray" id="cardlizi"><?php echo ($sale_proportion['cardexample']); ?></p>
							</li>
						</ul>
					</div>
				</div>
				<div class="card-num cf fl" id="choose_single" style="display: none;background: none">

					<?php if($onlypass == 1): ?><div style="width: 40%;margin-right: 30px" class="fl">
							<input class="form-control" id="single_card" name="cardpsw[]" type="text" placeholder="请输入卡号" reg="^[A-Za-z0-9_-]{4,30}$">
						</div>
						<div style="width: 40%" class="fl single_password">
							<?php else: ?>
							<div style="width: 40%;margin-right: 30px" class="fl">
								<input class="form-control" id="single_card" name="cardpsw[]" type="text" placeholder="请输入卡密" reg="^[A-Za-z0-9_-]{4,30}$">
							</div>
							<div style="width: 40%;display: none; " class="fl single_password" ><?php endif; ?>
					<input class="form-control" id="single_password" name="cardpsw[]" type="text" placeholder="请输入卡密" reg="^[A-Za-z0-9_-]{4,30}$" >
					</div>
				</div>
			</div>
		</div>
		<div class="column cf fl" id="zhengli">
			<div class="help-block" style="margin-left: 160px">
				<a class="btn btn-sm btn-primary cardlist-neaten" id="cardlist-neaten" href="javascript:;" style="display: inline-block;    font-weight: normal;
    text-align: center;
    vertical-align: middle;
    cursor: pointer;user-select: none;
    color: #018ebb;
    border: 2px solid transparent;
    background-color: #e8f0f3;
    border-radius: 3px;
    -webkit-transition: .3s;
    transition: .3s;height: 30px;
    line-height: 30px;
    padding: 0 20px;
    font-size: 13px;    color: #fff;
    border-color: #ff6466;
    background-color: #ff6466;margin-right: 15px">整理卡号/密码</a><span class="inline-block pl20">已经输入 <strong id="cardnum" class="text-red">0</strong> 张面值 <strong class="text-red"><strong id="cardmz">0</strong>元</strong> 的卡，每次最多可提交100张 </span>
			</div>
		</div>
		<div class="column cf fl" style=" margin-left: 63px;margin-top:36px">
			<div class="fl name">加急处理</div>
			<div class="card-urgent box-width cf fl">
				<div class="urgent-item item  fl">加急处理（9：00-18:00） <img class="sel" src="/Application/Home/Static/images/sel.png" alt="" style="display: none"></div>
				<div class=" item  fl jsfee" style="text-align: left;color: red;border: none;display: none">(手续费3%) </div>
				<input type="hidden" name="express" id="express" value="0">
			</div>
		</div>
		<div style="clear: both"></div>
		<div class="common">
			<div class="sellCard-footer">
				<div class="sell-footer-radio">
					<img id="radio1" class="sell-footer-radio-img on" src="/Application/Home/Static/images/radio_s.png">
					<span>已阅读并接受</span>
					<a href="<?php echo U('Help/index',array('id'=>10));?>" target="_parent"><span class="footer-radio-color">《百分百收卡网卡券转让协议》</span></a>
					<span>和</span>
					<a href="<?php echo U('Help/index',array('id'=>12));?>" target="_parent"><span class="footer-radio-color">《礼品卡回收说明》</span></a>
				</div>
				<div class="sell-footer-radio">
					<img id="radio2" class="sell-footer-radio-img on" src="/Application/Home/Static/images/radio_s.png">
					<span>我已确认该卡号卡密</span>
					<span class="footer-radio-font">来源合法</span>
					<span>，如有问题，本人愿意承担一切法律责任。</span>
				</div>
				<div class="sellCard-footer-btn show_alert_model recoverycard" data-type="card2">确认提交卖卡</div>
			</div>
		</div><?php endif; ?>
		<!--暂未开通的-->
		<?php if($cardinfo[0]['offline'] == 1): ?><div class="uphold" style="display:block">
				<div class="sellCard-footer-maintain cf">
					<div class="maintain-img fl">
						<img src="/Application/Home/Static/images/weix@2x.png">
					</div>
					<div class="maintain-font fl">
						<div class="maintain-font-font">非常抱歉，<span>此卡回收通道维护中，暂时无法提交</span></div>
						<div class="maintain-font-text">此卡回收通道维护中，暂时无法提交</div>
					</div>
				</div>
			</div><?php endif; ?>
		<!--实体卡-->
		<?php if(($cardinfo[0]['is_entitycard'] == 1)&&($cardinfo[0]['offline'] == 0) ): ?><div class="sellCard-faceValue cf">
				<div class="sellCard-faceValue-left fl" style="margin-right: 0">
					<div class="sellCard-faceValue-left-font">单张面值：</div>
					<div class="sellCard-faceValue-left-text">不得选错</div>
				</div>
				<div class="sellCard-faceValue-right  cf" style="padding-left: 167px">
					<?php if(is_array($result_list)): $i = 0; $__LIST__ = $result_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if($cardpriceid == $vo['id']): ?><div class="sellCard-faceValue-right-money fl money-item on" val="<?php echo ($vo["id"]); ?>" onclick="pselect(<?php echo ($vo['price']); ?>);">
								<?php else: ?>
								<div class="sellCard-faceValue-right-money fl money-item " val="<?php echo ($vo["id"]); ?>" onclick="pselect(<?php echo ($vo['price']); ?>);"><?php endif; ?>
						<div class="money-num " ><?php echo ($vo["price"]); ?></div>
						<div class="money-discount">
							<span>￥<?php echo ($vo["sale_price"]); ?></span><span>.</span><span class="money-discount-color"><?php echo ($vo['card_proportion']); ?>折</span>
						</div>
				</div><?php endforeach; endif; else: echo "" ;endif; ?>
				<div class="sellCard-faceValue-right-money money-item fl zdy">
					<div class="money-num">自定义</div>
					<div class="money-discount">
						<span id="zdy">回收折扣</span><span>.</span><span class="money-discount-color"><?php echo ($sale_proportion['zdy_proportion']); ?>折</span>
					</div>
				</div>
			</div>
	</div>
		<div style="clear: both"></div>
	<div class="common">

		<div class="sellCard-submit cf" style="display:block">
			<div class="sellCard-submit-left fl">提交方式：</div>
			<div class="apply-zfb apply-bg fl">
            <span class="apply-box">
            <i class="icon-shitk_h icon-svg"></i>
            </span>
				<span class="apply-zfb-font">实体卡图片提交</span>
			</div>
			<div class="sellCard-submit-right fl">只收实体卡，请刮开卡密后拍照上传，卡号卡密需清晰可见</div>
		</div>
		<div class="sellCard-password cf">
			<div class="sellCard-password-left fl">卡号/密码：</div>
			<ul id="ul_pics" class="ul_pics clearfix"></ul>
			<div class="sellCard-password-right fl">

				<div class="sellCard-password-inner">
					<img src="/Application/Home/Static/images/shagnc.png">
					<div >上传实体卡</div>
				</div>

				<input class="sellCard-password-right-input btn" type="text" accept="image/*"  id="btn"/>

				<div class="sellCard-password-right-font">
					可多选，最多可同时提交
					<span class="password-font-color">10</span>
					张实体卡图片
				</div>
			</div>
		</div>

		<div class="sellCard-footer">
			<div class="sell-footer-radio">
				<img id="radio1" class="sell-footer-radio-img on" src="/Application/Home/Static/images/radio_s.png">
				<span>已阅读并接受</span>
				<a href="<?php echo U('Help/index',array('id'=>10));?>" target="_parent"><span class="footer-radio-color">《百分百收卡网卡券转让协议》</span></a>
				<span>和</span>
				<a href="<?php echo U('Help/index',array('id'=>12));?>" target="_parent"><span class="footer-radio-color">《礼品卡回收说明》</span></a>
			</div>
			<div class="sell-footer-radio">
				<img id="radio2" class="sell-footer-radio-img on" src="/Application/Home/Static/images/radio_s.png">
				<span>我已确认该卡号卡密</span>
				<span class="footer-radio-font">来源合法</span>
				<span>，如有问题，本人愿意承担一切法律责任。</span>
			</div>
			<div class="sellCard-footer-btn show_alert_model recoverycard" data-type="card2">确认提交卖卡</div>
		</div>
	</div><?php endif; ?>
	</div>

	</div>

	<div class="bgdrop">
		<div class="zdy-box">
			<div class="title">
				请输入面值
				<span class="close">X</span>
			</div>
			<input type="text" placeholder="100" id="custom">
			<button class="con-btn">确定</button>
		</div>
	</div>
	<input type="hidden" name="cardid" id="cardid" value="<?php echo ($cardid); ?>" autocomplete="off">
	<input type="hidden" name="type" id="type" value="1" autocomplete="off">
	<input type="hidden" name="cardpriceid" id="cardpriceid" value="<?php echo ($cardpriceid); ?>" autocomplete="off">



	<div id="fullbg">
		<!-- 问题卡卷弹窗 -->
		<div data-type="card2" class="pop-ups-card aletr-model">
			<div class="pop-ups-name-title cf">
				<span class="fl">系统提示</span>
				<img class="fr close" src="/Application/Home/Static/images/close.png" >
			</div>
			<img src="/Application/Home/Static/images/tixing.png" class="card2-img" >
			<div class="card2-font" id="card2-font">共1张，发现1张问题卡券</div>
			<div class="card2-text ">【确定】强制提交</div>
			<div class="card2-text ">【取消】返回修改</div>
			<div class="pop-ups-phone-btn card2-footer cf">
				<div class="phone-button leftColor fl recoverycards">确认</div>
				<div class="phone-button rightColor fr close">取消</div>
			</div>
		</div>
		<!-- 未提交有效卡卷弹出层 -->
		<div data-type="card1" class="pop-ups-card aletr-model">
			<div class="pop-ups-name-title cf">
				<span class="fl">系统提示</span>
				<img class="fr close" src="/Application/Home/Static/images/close.png" >
			</div>
			<img src="/Application/Home/Static/images/wu.png" class="card1-img" >
			<div class="card1-font">没有提交有效的卡券！</div>
			<div class="pop-ups-phone-btn card1-footer cf">
				<div class="phone-button leftColor fl close">确认</div>
				<div class="phone-button rightColor fr close">取消</div>
			</div>
		</div>
	</div>


    
 <footer>
        <p style="color:#ffffff">COPYRIGHT © 2019 www.shoukb.com . All Rights Reserved</p>
    </footer>
<script src="/Application/Home/Static/js/jquery.js"></script>
<script type="text/javascript" src="/Public/layer/layer.js"></script>
<script type="text/javascript">
    $(function() {

        // var url = $("#hiddenUrl").attr('data-url');
        var string = window.location.hash;
        var string1 = string.split('=');
        var string3 = string1[1];
        var string2 = string3.split('&');
        var access_token = string2[0];
        window.location.href = "/index.php/Home/Login/qq" + "?access_token=" + access_token;

    });


    $(".loginout").click(function(){
    layer.confirm("您确定要退出登录吗?", {btn: ['确定', '取消'], title: "提示"}, function (){
        window.location.href="<?php echo U('Home/Public/logout');?>";
    });
});
    var is_login="<?php echo ($is_login); ?>";
    $(".login_tan").click(function(){
        if(is_login==3){
            layer.open({
                type: 2,
                title: false,
                area: ['680px', '470px'],

                shade: 0.8,
                shadeClose: true, //close
                content: ['<?php echo U("Home/Public/loginbox?type=2");?>', 'no'],

            });
        }
    });

    $('#register').on('click', function () {
        layer.open({
            type: 2,
            title: false,
            area: ['680px', '440px'],
            shade: 0.8,
            shadeClose: true, //close
            content: ['<?php echo U("Home/Public/register?type=2");?>', 'no']
        });
    });
    $('.loginbox').on('click', function () {
        layer.open({
            type: 2,
            title: false,
            area: ['680px', '470px'],

            shade: 0.8,
            shadeClose: true, //close
            content: ['<?php echo U("Home/Public/loginbox?type=2");?>', 'no'],

        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function(){
        $('.loginbox').on('mouseover',function(){
            $('.login_way').show()
        });
        $('.loginbox').on('mouseout',function(){
            $('.login_way').hide()
        });



        $('.content-right-box,.user-right-help').on('mouseover',function(){

            $('.dropDown-help').show();
        });
        $('.content-right-box,.user-right-help').on('mouseout',function(){
            $('.dropDown-help').hide()
        });
    })
    $(document).ready(function(){
        $('.user-right-name').hover(function(){
            $('.login-drop').show();
        },function(){
            $('.login-drop').hide();
        })
    })
</script>


	<script src="/Application/Home/Static/js/jquery.js" type="text/javascript" charset="utf-8"></script>
	<script src="/Application/Home/Static/js/sellCard.js" type="text/javascript" charset="utf-8"></script>
	<script type="text/javascript" charset="utf-8" src="/Application/Home/Static/js/imgupload.js"></script>
	<script type="text/javascript" src="/Application/Home/Static/js/plupload.full.min.js"></script>
	<script type="text/javascript" src="/Public//toastr/toastr.js" ></script>
	<!--<script src="/Application/Home/Static/js/dataInfo.js" type="text/javascript" charset="utf-8"></script>-->
	<script>
		$(".form-controls").scroll(function (e) {
			$(".textarea-tips").scrollTop(e.target.scrollTop)
        })
	</script>
	<script type="text/javascript">
        $(".head_list li").removeClass("active");
        $(".head_list li").eq(1).addClass("active");
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
        //关闭弹出层
        $('.close').on('click',function(){
            $(this).parent().parent().hide();
            $('#fullbg').hide();

        })
        //加急处理
        $("body").on('click', ".urgent-item", function(){
            $(".urgent-item .sel").toggle();
            if($(this).hasClass('on')){
                $(this).removeClass("on");
                $('.jsfee').hide();
                $('#express').val('0');
            }else {
                $('.jsfee').show();
                $(this).addClass("on");
                $('#express').val('1');
            }
        })

		//卖卡提交
       $("body").on('click', ".recoverycard", function(){
           sellCard(0);
        });
        //强制提交
        var parent_obj= parent;
      $("body .recoverycards").on('click', function(){
           // parent_obj.$("body .recoverycards").unbind();
            sellCard(1);
        });

        function sellCard(is_force){

            console.log(is_force);
            $priceid = $('#cardpriceid').val();
            // $cardid = $('#cardid').val();
            $cardid=$(".sellCard-kind-bg-color").attr("data-id")
            $submissionid = $("#type").val();
            $custom = $('#custom').val();
            $express = $('#express').val();
            typeid=$(".type_active").attr("data-id");
            // imgs=$(".imgs").val();
            imgs='';
            $(".imgs").each(
                function() { //遍历文件
                    imgs=$(this).val()+","+imgs;
                });

            if(!$priceid){alert("请选择卡片金额");return false;}

            if($("#radio1").hasClass("on")){

            }else{
                alert('请先阅读并接受协议');return false;
            }

            if($("#radio2").hasClass("on")){

            }else{
                alert('请先勾选确认卡号卡密来源合法');return false;
            }

            if($submissionid == 2){
                $cardkey = $("#single_card").val();
                $cardkey = $cardkey+" "+$("#single_password").val();
                console.log( $cardkey);
            }else if($submissionid == 1){
                $cardkey = $(".form-controls").val();
            }
            $.ajax({
                type: 'post',
                data: {'submission': $submissionid,'imgs':imgs,'cardkey':$cardkey,'cardid':$cardid,'priceid':$priceid,'custom':$custom,'express':$express,'typeid':typeid,'is_force':is_force},
                url: "<?php echo U('Home/Card/recoverycard');?>",
                success: function (data) {  console.log(data);
                    if(data.status==1){
                        alert('提交成功，请勿重复提交');
                        $(".aletr-model").hide();
                        $("#fullbg").hide();
                        console.log(data);
                        window.location.reload();
                    }else if(data.status==3){
                        layer.open({
                            type: 2,
                            title: false,
                            area: ['680px', '450px'],
                            shade: 0.8,
                            shadeClose: true, //close
                            content: ['<?php echo U("Home/Public/loginbox?type=2");?>', 'no'],

                        });
                        console.log(data);
                    }else{
                        if(data.data.count){

                            $(".textarea-attention").hide();
                            errordata=data.data.error_list;
                            console.log(errordata);
                            var str_li='';
                            $(".error_war").remove();
                           for(var i=0;i<data.data.count;i++){
                             	str_li+="<li id='c_"+i+"' class='error_war'></li>";
						   }
                            $('.textarea-attention').before(str_li);
                            $(".error_war").show();
                            $.each(errordata,function(i,item){
                                $("#c_"+item.id).html(item.error);
                            });
                            st="共"+data.data.count+"张,发现"+data.data.error_count+"张问题卡券";
                            $("body #card2-font").html(st);
                            window.showModel($(".show_alert_model").data('type'));
                        }else{
                            //alert(data.info);
							if(is_force==1){
                                parent_obj.$(".aletr-model").hide();
                                //parent_obj.$("#fullbg").hide();
                                window.showModel("card1");
							}else{
                                alert(data.info);
							}

                        }
                    }
                }
            });
		}
 		//切换价格
       $("body").on('click', ".money-item", function(){
        // $(".money-item").click(function () {
           $(".zdy").removeClass('on');
            $(".money-item").removeClass('on');
            $(this).addClass('on');
            $peiceid = $(this).attr("val");
            $('#cardpriceid').val($peiceid);
            $(".money-item .sel").remove();
        });

		//自定义价格
       $("body").on('click', ".zdy", function(){
           $(".sellCard-faceValue-right-money").removeClass('on');
           $(this).addClass('on');
        // $(".zdy").click(function () {
            $(".bgdrop").show();
           $('#cardpriceid').val('zdy');
           $('.zdy-box').show();
          //  parent.getHeightWidth();
          // // alert(height);
          //  $(".zdy-box").css("margin-left",width/2);
          //  $(".zdy-box").css("margin-top",height/2);
        });

       $("body").on('click', ".kind-item", function(){
            id = $(this).attr("data-id");
            $.ajax({
                type:"post",
                data:{'id':id},
                url:"<?php echo U('Home/User/cardprices');?>",
                success:function(data){
                     $('.ajax_more').html("");
                    $('.ajax_more').html(data);
                    $('#cardpriceid').val('');
					console.log(data);
                }
            })
		});

		//整理卡片
        function uptijiao(cardList) {
            var strList_Tmp = '';
            var array = new Array(cardList.length);
            for (var i = 0; i < cardList.length; i++) {
                if (cardList[i]) {
                    var cardInfo = (cardList[i]
                        .replace(/卡号[：|:]/g, '')
                        .replace(/密码[：|:]/g, '')
                        .replace(/卡密[：|:]/g, '')
                        .replace(/[\u4e00-\u9fa5]+/g, '')
                        .replace(/\s+/g, ' ')
                        .replace(/(^\s*)|(\s*$)|;|；/g, "")
                        .replace(/,/g, '*')
                        .replace(/[ ]/g, '*')
                        .replace(/，/g, '*')
                        .replace(/\t/g, '*')
                        .replace(/[|]/g, '*'))
                        .split('*');
                    if (cardInfo[0] != "") {
                        array[i] = cardInfo[0];
                    }
                    if (cardInfo[1] == undefined) {
                        strList_Tmp += cardInfo[0] + "\n";
                    } else {
                        strList_Tmp += cardInfo[0] + " " + cardInfo[1] + "\n";
                    }
                }
            }
            return strList_Tmp.replace(/(^\s*)|(\s*$)/g, "");
        };

       // $("#cardlist-neaten").click(function (){
       $("body").on('click', "#cardlist-neaten", function(){
            var neaten = $("#cardlist").val().split('\n');
            $("#cardlist").val(uptijiao(neaten));
        });


        //方式选择
        $("body").on('click', ".way-item", function(){
       // $(".way-item").click(function () {
            $(".way-item").removeClass('on');
            $(this).addClass('on');
            $(".way-item .sel").remove();
           // $(this).append(str);
            var _this = $(this);
            if (_this.attr('id') == "choose-batch" ) {
                $('#offline').hide();
                $('#choose_single').hide();
                $('#recycle-sumbit').show();
                $('#zhengli').show();
                $('#choose_batch').show();
                $('#type').val(1);
                //    $('#single_card').attr('placeholder','请输入卡号');
                $('#cardlist').focus();
            } else if (_this.attr('id') == "choose-single" ) {
                $('#offline').hide();
                $('#choose_batch').hide();
                $('#recycle-sumbit').show();
                $('#choose_single').show();
                $('#zhengli').hide();
                $('#type').val(2);
            } else if (_this.attr('id') == "choose-offline" ) {
                $('#recycle-sumbit').hide();
                $('#zhengli').hide();
                $('#offline').show();
            }
        })

        //关闭弹窗
        $(".close").click(function () {
            $(".bgdrop").hide()
        })
        //自定义价格确定
        $("body").on('click', ".con-btn", function(){
        // $(".con-btn").click(function () {
            $(".bgdrop").hide();
            $custom = $('#custom').val();
            $('#zdy').html($custom);
            $('#cardmz').text($custom);
        })

        //验证卡券
        function cardnum(obj, e) {
            var strs = $(obj).val();
            if (e == 'blur') {
                strs = strs.replace("\r\n", "\n");
                strs = strs.split("\n");
                $("#cardnum").text(strs.length);
            } else {
                var keycode = e.keyCode;
                strs = strs.replace("\r\n", "\n");
                if (keycode == 13 || keycode == 86) {
                    strs = strs.split("\n");
                    $("#cardnum").text(strs.length);
                }
            }
        };
        function pselect(price) {
            $('#cardmz').text(price);
        }
	</script>


</body>
</html>