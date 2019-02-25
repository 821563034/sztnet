<?php defined('IN_PHPCMS') or exit('No permission resources.'); ?><?php include template('member', 'header'); ?>
<div id="memberArea">
<?php include template('member', 'left'); ?>
    <div class="col-auto">
	<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"announce\" data=\"op=announce&tag_md5=135f3f60241cbf851ab60e17d53ceb16&action=lists&num=1&siteid=%24memberinfo%5Bsiteid%5D&cache=3600\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$tag_cache_name = md5(implode('&',array('siteid'=>$memberinfo[siteid],)).'135f3f60241cbf851ab60e17d53ceb16');if(!$data = tpl_cache($tag_cache_name,3600)){$announce_tag = pc_base::load_app_class("announce_tag", "announce");if (method_exists($announce_tag, 'lists')) {$data = $announce_tag->lists(array('siteid'=>$memberinfo[siteid],'limit'=>'1',));}if(!empty($data)){setcache($tag_cache_name, $data, 'tpl_data');}}?>
		<?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
		<?php $announceid = $r[aid];?>
    	<div class="point" id='announcement' style="display:none">
        	<a href="javascript:hide_element('announcement');setcookie('announcement_<?php echo $memberinfo['userid'];?>_<?php echo $r['aid'];?>', 1);" hidefocus="true" class="close"><span><?php echo L('close');?></span></a>
            <div class="content">
					<strong class="title"><?php echo $r['title'];?></strong>
					<p><?php echo $r['content'];?></p>
            </div>
            <span class="o1"></span><span class="o2"></span><span class="o3"></span><span class="o4"></span>
        </div>
		<?php $n++;}unset($n); ?>
	<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
    	<div class="col-auto">
        	<div class="col-1 member-info">
            	<div class="content">
                    <div class="col-left himg">
					<a title="<?php echo L('modify').L('avatar');?>" href="index.php?m=member&c=index&a=account_manage_avatar&t=1"><img src="<?php echo $avatar['90'];?>" width="60" height="60" onerror="this.src='<?php echo $phpsso_api_url;?>/statics/images/member/nophoto.gif'"></a>
					</div>
                  <div class="col-auto">
                   	<h5><?php if($memberinfo['vip']) { ?><img src="<?php echo IMG_PATH;?>icon/vip.gif"><?php } elseif ($memberinfo['overduedate']) { ?><img src="<?php echo IMG_PATH;?>icon/vip-expired.gif" title="<?php echo L('overdue');?>，<?php echo L('overduedate');?>：<?php echo format::date($memberinfo['overduedate'],1);?>"><?php } ?>
					<?php if($memberinfo['from']) { ?><img src="<?php echo IMG_PATH;?>member/logo/<?php echo $memberinfo['from'];?>_16_16.png"><?php } ?>
					<font color="<?php echo $grouplist[$memberinfo['groupid']]['usernamecolor'];?>">
					<?php if($memberinfo['nickname']) { ?> <?php echo $memberinfo['nickname'];?> <?php } else { ?> <?php echo $memberinfo['username'];?><?php } ?>
					</font>
					<?php if($memberinfo['email']) { ?>（<?php echo $memberinfo['email'];?>）<?php } ?>
					</h5>
                    <p class="blue">
					  <?php echo L('member_group');?>：<?php echo $memberinfo['groupname'];?>，
                      <?php echo L('account_remain');?>：<font style="color:#F00; font-size:22px;font-family:Georgia,Arial; font-weight:700"><?php echo $memberinfo['amount'];?></font> <?php echo L('unit_yuan');?>，
					<?php echo L('point');?>：<font style="color:#F00; font-size:12px;font-family:Georgia,Arial; font-weight:700"><?php echo $memberinfo['point'];?></font> <?php echo L('unit_point');?> <?php if($memberinfo['vip']) { ?>，vip<?php echo L('overduedate');?>：<font style="color:#F00; font-size:12px;font-family:Georgia,Arial; font-weight:700"><?php echo format::date($memberinfo['overduedate']);?></font><?php } ?>
                      </p>
                    </div>
           	  </div>

            	<span class="o1"></span><span class="o2"></span><span class="o3"></span><span class="o4"></span>
            </div>
            <div class="bk10"></div>

            <div class="col-1 ">
            	<h5 class="title"><?php echo L('collect');?></h5>
            	<div class="content">
					<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"member\" data=\"op=member&tag_md5=1295f2e2b9a3034aec368254b03901a1&action=favoritelist&userid=%24memberinfo%5Buserid%5D&num=10\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$member_tag = pc_base::load_app_class("member_tag", "member");if (method_exists($member_tag, 'favoritelist')) {$data = $member_tag->favoritelist(array('userid'=>$memberinfo[userid],'limit'=>'10',));}?>	
                    <ul class="title-list">
					<?php $n=1; if(is_array($data)) foreach($data AS $k => $v) { ?>
                    	<li>·<a href="<?php echo $v['url'];?>" target="_blank"><?php echo $v['title'];?></a><span><em><?php echo format::date($v['adddate'], 1);?></em> <a href="index.php?m=member&c=index&a=favorite&id=<?php echo $v['id'];?>"><?php echo L('delete');?></a></span></li>
                    <?php $n++;}unset($n); ?>
                    </ul>
					<?php echo $pages;?>
					<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
            	</div>
            	<span class="o1"></span><span class="o2"></span><span class="o3"></span><span class="o4"></span>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>
<script type="text/javascript" src="<?php echo JS_PATH;?>cookie.js"></script>
<script language="JavaScript">
<!--
$(document).ready(function() {
	var announcement = getcookie('announcement_<?php echo $memberinfo['userid'];?>_<?php echo $announceid;?>');
	if(announcement==null || announcement=='') {
		$("#announcement").fadeIn("slow");
	}
});
//-->
</script>
<?php include template('member', 'footer'); ?>