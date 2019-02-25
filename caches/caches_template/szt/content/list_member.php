<?php defined('IN_PHPCMS') or exit('No permission resources.'); ?><!doctype html>
<html lang="cmn-Hans">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=Edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php if(isset($SEO['title']) && !empty($SEO['title'])) { ?><?php echo $SEO['title'];?><?php } ?><?php echo $SEO['site_title'];?></title>
  <meta name="keywords" content="<?php echo $SEO['keyword'];?>">
  <meta name="description" content="<?php echo $SEO['description'];?>">
  <link href="<?php echo CSS_PATH;?>mycss/common.css" rel="stylesheet" />
  <link href="<?php echo CSS_PATH;?>mycss/szt.css" rel="stylesheet" />
  <script src="<?php echo JS_PATH;?>jquery.min.js"></script>
  <script src="<?php echo JS_PATH;?>search_common.js"></script>
  <style type="text/css">
  .card{display:block; overflow:hidden;}
  .card img{float:left; width:15%; border-radius:50%; overflow:hidden;}
  .card div{float:left; width:85%; padding-left:5%; color:#aaa; overflow:hidden;}
  .card div span:nth-child(1){float:left; width:40%; color:#000;}
  .card div span:nth-child(2){float:left; width:60%; height:1.25em; line-height:1.25em; font-size:0.8em; text-align:right;}
  .card div span:nth-child(3){float:left; width:100%; margin-bottom:10px; font-size:0.8em;}
  .card div span:nth-child(4){float:left; width:100%; font-size:0.8em;}

  form{margin-bottom:10px; font-size:0.8em;}
  form span{float:left; width:25%;}
  form div{float:left; width:75%; color:#ccc;}
  form button{float:right; width:20%; background-color:#ddd;}
  </style>
</head>

<body>
<?php include PHPCMS_PATH.'/phpcms/templates/pub/header.html'; ?>

<div class="container">
<?php include template("content_inc","nav"); ?>
<?php include template("content_inc","position"); ?>

<div class="row">
  <div class="col xs-12 md-8">
	<form>
	  <span>城市</span><span>行业</span><span>家乡</span><span>情感</span>
      <div><input type="text" class="input-text" name="search" placeholder="姓名/ID/手机号/学校/关键词" /></div><buttom type="button">搜索</buttom>
	</form>
    <ul class="bts lbs">
      <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"get\" data=\"op=get&tag_md5=d52648e06abf3de04b21582c4a1686e0&sql=SELECT+A.username%2CB.%2A+FROM+v9_member+as+A+left+join+v9_member_detail+as+B+on+A.userid+%3D+B.userid+where+A.modelid+%3D+10&cache=3600&page=%24page&num=10&return=data\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}pc_base::load_sys_class("get_model", "model", 0);$get_db = new get_model();$pagesize = 10;$page = intval($page) ? intval($page) : 1;if($page<=0){$page=1;}$offset = ($page - 1) * $pagesize;$r = $get_db->sql_query("SELECT COUNT(*) as count FROM (SELECT A.username,B.* FROM v9_member as A left join v9_member_detail as B on A.userid = B.userid where A.modelid = 10) T");$s = $get_db->fetch_next();$pages=pages($s['count'], $page, $pagesize, $urlrule);$r = $get_db->sql_query("SELECT A.username,B.* FROM v9_member as A left join v9_member_detail as B on A.userid = B.userid where A.modelid = 10 LIMIT $offset,$pagesize");while(($s = $get_db->fetch_next()) != false) {$a[] = $s;}$data = $a;unset($a);?>
      <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
      <li>
        <a href="index.php?m=content&c=index&a=member_home&catid=<?php echo $catid;?>&id=<?php echo $r['userid'];?>" target="_blank" class="card">
          <img src="<?php echo get_memberavatar($r[userid],1,90);?>" onerror="this.src='<?php echo $phpsso_api_url;?>/statics/images/member/nophoto.gif'">
          <div>
            <span><?php echo $r['username'];?></span><span><?php echo get_linkage($r[area],1,' >> ',1);?></span>
            <span><?php echo $r['unit_name'];?>&nbsp;&nbsp;<?php echo $r['position'];?></span>
            <span><?php echo $r['self_introduction'];?></span>
          </div>
        </a>
      </li>
      <?php $n++;}unset($n); ?>
      <?php echo $pages;?>
      <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
    </ul>
    
    <div id="pages" class="text-c"></div>
  </div><!--/左侧栏结束-->

  <div class="col xs-12 md-4">
  </div><!--/右侧栏结束-->
</div><!--/row-->

</div><!--/container-->
<?php include PHPCMS_PATH.'/phpcms/templates/pub/footer.html';?>
</body>
</html>