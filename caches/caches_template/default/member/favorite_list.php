<?php defined('IN_PHPCMS') or exit('No permission resources.'); ?><?php include template('member', 'header'); ?>
<link href="<?php echo CSS_PATH;?>dialog.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH;?>dialog.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH;?>content_addtop.js"></script>
<div id="memberArea">
	<?php include template('member', 'left'); ?>
	<div class="col-auto">
		<div class="col-1 ">
			<h5 class="title"><?php echo L('favorite_list');?></h5>
			<div class="content">
				
			<form method="post" action="" id="myform" name="myform">
				<table width="100%" cellspacing="0" class="table_form">
					<tr>
						<td><?php echo L('title');?></td>
						<td><?php echo L('adddate');?></td>
						<td><?php echo L('operation');?></td>
					</tr>
					<?php $n=1; if(is_array($favoritelist)) foreach($favoritelist AS $k => $v) { ?>
					<tr>
						<td><a href="<?php echo $v['url'];?>" target="_blank"><?php echo $v['title'];?></a></td>
						<td><?php echo format::date($v['adddate'], 1);?></td>
						<td><a href="index.php?m=member&c=index&a=favorite&id=<?php echo $v['id'];?>"><?php echo L('delete');?></a></td>
					</tr>
					<?php $n++;}unset($n); ?>
				</table>
			</form>
			
			</div>
			<div id="pages"><?php echo $pages;?></div>
			<span class="o1"></span><span class="o2"></span><span class="o3"></span><span class="o4"></span>
		</div>
	</div>
</div>
<div class="clear"></div>
<?php include template('member', 'footer'); ?>