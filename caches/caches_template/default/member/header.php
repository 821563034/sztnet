<?php defined('IN_PHPCMS') or exit('No permission resources.'); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<title>phpcmsV9 - <?php echo L('member','','member').L('manage_center');?></title>
<link href="<?php echo CSS_PATH;?>reset.css" rel="stylesheet" type="text/css" />
<link href="<?php echo CSS_PATH;?>member.css" rel="stylesheet" type="text/css" />
<link href="<?php echo CSS_PATH;?>table_form.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo JS_PATH;?>jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>member_common.js"></script>
<?php if(isset($show_validator)) { ?>
<script type="text/javascript" src="<?php echo JS_PATH;?>formvalidator.js" charset="UTF-8"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>formvalidatorregex.js" charset="UTF-8"></script>
<?php } ?>
</head>
<body>
<div id="header">
	<div class="logo"><a href="<?php echo $siteinfo['domain'];?>"><img src="<?php echo IMG_PATH;?>v9/logo.jpg" height="60" /></a><h3><?php echo L('member_center');?></h3></div>
	<?php $siteinfo = siteinfo($this->memberinfo['siteid']);?>
	<?php $this->menu_db = pc_base::load_model('member_menu_model');?>
	<?php $menu = $this->menu_db->select(array('display'=>1, 'parentid'=>0), '*', 20 , 'listorder');?>
    <div class="link"><?php echo L('hellow');?> <?php echo get_nickname();?><span> | </span>
	<a href="<?php echo APP_PATH;?>index.php?m=member&c=index&a=logout"><?php echo L('logout');?></a>
	<span> | </span><a href="<?php echo $siteinfo['domain'];?>"><?php echo L('index');?></a> </div>
	<div class="nav-bar">
    	<map>
        	<ul class="nav-site cu-span">
				<?php $n=1; if(is_array($menu)) foreach($menu AS $k => $v) { ?>
				<li>
					<?php if($v['isurl']) { ?>
						<a href="<?php echo $v['url'];?>" target="_blank"><span><?php echo L($v['name'], '', 'member_menu');?></span></a>
					<?php } else { ?>
						<a href="index.php?m=<?php echo $v['m'];?>&c=<?php echo $v['c'];?>&a=<?php echo $v['a'];?>&<?php echo $v['data'];?>" <?php if($k==$_GET['t']) { ?>class="on"<?php } ?>><span><?php echo L($v['name'], '', 'member_menu');?></span></a>
					<?php } ?>
					</li>
					<li class="line">|</li>
				<?php $n++;}unset($n); ?>
            </ul>
        </map>
    </div>
</div>