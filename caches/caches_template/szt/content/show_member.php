<?php defined('IN_PHPCMS') or exit('No permission resources.'); ?><!doctype html>
<html lang="cmn-Hans">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=Edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo get_nickname($userid,'username');?>的微网</title>
  <meta name="keywords" content="<?php echo $SEO['keyword'];?>">
  <meta name="description" content="<?php echo $SEO['description'];?>">
  <link href="<?php echo CSS_PATH;?>mycss/common.css" rel="stylesheet" />
  <link href="<?php echo CSS_PATH;?>mycss/szt.css" rel="stylesheet" /> 
  <script src="<?php echo JS_PATH;?>jquery.min.js"></script>
  <script src="<?php echo JS_PATH;?>search_common.js"></script>
  
  <style>
  .hd_member{height:2em; line-height:2em; color:#aaa; font-size:0.9em; overflow:hidden;}
  .hd_member span{float:left;}
  .hd_member a{float:right; margin-left:1em;}
  .hd_member a:link, .hd_member a:visited{color:#aaa;}
  
  .mySlide img{width:100%;}
  
  .info1{overflow:hidden;}
  .info1 img{float:left; width:15%; border-radius:50%; overflow:hidden;}
  .info1 div{float:left; width:85%;}  
  .info1 div span{float:left;}
  .info1 div span:nth-child(1){width:70%;}
  .info1 div span:nth-child(2){width:30%; text-align:right; font-size:0.8em; color:#aaa;}
  .info1 div span:nth-child(3){width:100%; font-size:0.8em; color:#aaa;}
  .info1 div p{float:left; width:100%; font-size:0.8em; color:#aaa;}
  
  .info2 li a{display:block; padding:5px 0; margin:0.5em 0; border:1px solid #ddd; border-radius:0.5em; text-align:center;}
  .info2 li a:hover{background-color:#ddd;}
  
  h3{color:#DAA520; margin-bottom:10px;}
  
  .myCont ul{overflow:hidden;}
  .myCont ul li{margin-bottom:10px;}
  .myCont ul li img{display:block; width:100%; border-radius:5px 5px 0 0;}
  .myCont ul li a{display:block; width:100%; padding:3px; background-color:#f0f0f0; height:3em; line-height:1.5em; border-radius:0 0 5px 5px; overflow:hidden;}
  .myCont ul li a:link, .myCont ul li a:visited{color:#333333;}

  .ft_member{height:3em; line-height:3em; text-align:center; font-size:0.9em;}
  .ft_member a:link, .ft_member a:visited{color:#DAA520;}  
  </style>
</head>

<body>
<div class="contain hd_member">
  <span>欢迎光临<?php echo get_nickname($userid,'username');?>的微网！</span><a href="index.php?m=member">我的控制台</a>
  <a href="index.php?m=content&c=index&a=lists&catid=<?php echo $catid;?>" target="_self">返回[商脉]</a>
</div>
<div class="container">

<div class="row mb10">
  <div class="col xs-12 md-6 mySlide">
    <img src="http://www.sztnet.com/uploadfile/poster/dbzq/slide.jpg" />
  </div><!--/mySlide-->
  
  <div class="col xs-12 md-6 myInfo">
    <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"get\" data=\"op=get&tag_md5=3b027764ab641763e35256edd150e258&sql=SELECT+%2A+FROM+v9_member_detail+where+userid%3D%24userid&cache=6&num=1&return=memberinfo\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('sql'=>'SELECT * FROM v9_member_detail where userid=$userid',)).'3b027764ab641763e35256edd150e258');if(!$memberinfo = tpl_cache($tag_cache_name,6)){pc_base::load_sys_class("get_model", "model", 0);$get_db = new get_model();$r = $get_db->sql_query("SELECT * FROM v9_member_detail where userid=$userid LIMIT 1");while(($s = $get_db->fetch_next()) != false) {$a[] = $s;}$memberinfo = $a;unset($a);if(!empty($memberinfo)){setcache($tag_cache_name, $memberinfo, 'tpl_data');}}?>
    <div class="info1">
      <img src="<?php echo get_memberavatar($userid,1,90);?>" onerror="this.src='<?php echo $phpsso_api_url;?>/statics/images/member/nophoto.gif'">
      <div>
        <span><strong><?php echo get_memberinfo($userid, $field='username');?></strong>&nbsp;&nbsp;<?php echo $memberinfo['0']['position'];?></span><span><?php echo get_linkage($memberinfo[0][area],1,'-',1);?></span>
        <span><?php echo $memberinfo['0']['unit_name'];?></span>
        <p><?php echo $memberinfo['0']['self_introduction'];?></p>
      </div>
    </div>
    
    <ul class="row5px info2">
      <li class="col5px xs-4 md-3"><a href="tel://13760322795" target="_blank">一键拨号</a></li>
      <li class="col5px xs-4 md-3"><a href="<?php echo $memberinfo['0']['wechat_img'];?>" target="_blank">加我微信</a></li>
      <li class="col5px xs-4 md-3"><a href="#" target="_blank">导入通讯录</a></li>
      <li class="col5px xs-4 md-3"><a href="geopoint:116.281469,39.866035" target="_blank">一键导航</a></li>
      <li class="col5px xs-4 md-3"><a href="#" target="_blank">个人资料</a></li>
      <li class="col5px xs-4 md-3"><a href="#" target="_blank">加入名片夹</a></li>
      <li class="col5px xs-4 md-3"><a href="#" target="_blank">一键加我</a></li>
      <li class="col5px xs-4 md-3"><a href="#" target="_blank">给我留言</a></li>
      <li class="col5px xs-4 md-3"><a href="#" target="_blank">关注我</a></li>
      <li class="col5px xs-4 md-3"><a href="#" target="_blank">待定待定</a></li>
      <li class="col5px xs-4 md-3"><a href="#" target="_blank">关注我</a></li>
      <li class="col5px xs-4 md-3"><a href="#" target="_blank">待定待定</a></li>
    </ul>
    <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
  </div><!--/myInfo-->

</div><!--/row-->

<div class="row">
<div class="col xs-12 myCont">
  <h3>我的链接</h3>
  <ul class="row5px">
    <li class="col5px xs-6 md-3"><img src="http://www.sztnet.com/uploadfile/poster/dbzq/db1.jpg" /><a href="#" target="_blank">东北证券公司官网</a></li>
    <li class="col5px xs-6 md-3"><img src="http://www.sztnet.com/uploadfile/poster/dbzq/db2.jpg" /><a href="#" target="_blank">东北证券荣获2018年最佳投行将</a></li>
    <li class="col5px xs-6 md-3"><img src="http://www.sztnet.com/uploadfile/poster/dbzq/db3.jpg" /><a href="#" target="_blank">2018年债券业务排名</a></li>
    <li class="col5px xs-6 md-3"><img src="http://www.sztnet.com/uploadfile/poster/dbzq/db4.jpg" /><a href="#" target="_blank">长风破浪会有时 直挂云帆济沧海——记东北证券上市10周年</a></li>
  </ul>
</div><!--/模块-->

<div class="col xs-12 myCont">
  <h3>业务中心</h3>
  <ul class="row5px">
    <li class="col5px xs-6 md-3"><img src="http://www.sztnet.com/uploadfile/poster/dbzq/db5.jpg" /><a href="#" target="_blank">股权质押业务</a></li>
    <li class="col5px xs-6 md-3"><img src="http://www.sztnet.com/uploadfile/poster/dbzq/db6.jpg" /><a href="#" target="_blank">大宗交易业务</a></li>
    <li class="col5px xs-6 md-3"><img src="http://www.sztnet.com/uploadfile/poster/dbzq/db7.jpg" /><a href="#" target="_blank">私募股权投资业务</a></li>
    <li class="col5px xs-6 md-3"><img src="http://www.sztnet.com/uploadfile/poster/dbzq/db8.jpg" /><a href="#" target="_blank">股权激励顾问业务</a></li>
  </ul>
</div><!--/模块-->

<div class="col xs-12 myCont">
  <h3>我的展示</h3>
  <ul class="row5px">
    <li class="col5px xs-6 md-3"><img src="http://www.sztnet.com/uploadfile/poster/dbzq/db9.jpg" /><a href="#" target="_blank">参加东北证券2019年投资策略会</a></li>
    <li class="col5px xs-6 md-3"><img src="http://www.sztnet.com/uploadfile/poster/dbzq/db10.jpg" /><a href="#" target="_blank">与成龙大哥在香港合影</a></li>
    <li class="col5px xs-6 md-3"><img src="http://www.sztnet.com/uploadfile/poster/dbzq/db11.jpg" /><a href="#" target="_blank">公司2018年年会</a></li>
    <li class="col5px xs-6 md-3"><img src="http://www.sztnet.com/uploadfile/poster/dbzq/db12.jpg" /><a href="#" target="_blank">用公司销售业绩第一名奖章</a></li>
    <li class="col5px xs-6 md-3"><img src="http://www.sztnet.com/uploadfile/poster/dbzq/db13.jpg" /><a href="#" target="_blank">参加东北证券2019年投资策略会</a></li>
    <li class="col5px xs-6 md-3"><img src="http://www.sztnet.com/uploadfile/poster/dbzq/db14.jpg" /><a href="#" target="_blank">与成龙大哥在香港合影</a></li>
    <li class="col5px xs-6 md-3"><img src="http://www.sztnet.com/uploadfile/poster/dbzq/db15.jpg" /><a href="#" target="_blank">公司2018年年会</a></li>
    <li class="col5px xs-6 md-3"><img src="http://www.sztnet.com/uploadfile/poster/dbzq/db16.jpg" /><a href="#" target="_blank">用公司销售业绩第一名奖章</a></li>
  </ul>
</div><!--/模块-->
</div><!--/row-->

</div><!--/container-->
<div class="contain ft_member"><a href="#" target="_self">我也要制作</a></div>
<script src="<?php echo APP_PATH;?>api.php?op=count&id=<?php echo $id;?>&modelid=<?php echo $modelid;?>"></script>
</body>
</html>