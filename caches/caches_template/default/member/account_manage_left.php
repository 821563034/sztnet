<?php defined('IN_PHPCMS') or exit('No permission resources.'); ?>	<div class="col-left col-1 left-memu">
        	<h5 class="title"><?php echo L('account_manage');?></h5>
            <ul>
                <li<?php if(ROUTE_A=="account_manage_info") { ?> class="on"<?php } ?>><a href="index.php?m=member&c=index&a=account_manage_info&t=1"><img src="<?php echo IMG_PATH;?>icon/user_edit.png" width="16" /> <?php echo L('modify').L('memberinfo');?></a></li>
                <li<?php if(ROUTE_A=="account_manage_avatar") { ?> class="on"<?php } ?>><a href="index.php?m=member&c=index&a=account_manage_avatar&t=1"><img src="<?php echo IMG_PATH;?>icon/vcard.png" width="16" /> <?php echo L('modify').L('avatar');?></a></li>
				<?php if(!empty($memberinfo['encrypt'])) { ?>
				<li<?php if(ROUTE_A=="account_manage_password") { ?> class="on"<?php } ?>><a href="index.php?m=member&c=index&a=account_manage_password&t=1"><img src="<?php echo IMG_PATH;?>icon/icon_key.gif" width="16" height="16" /> <?php echo L('modify').L('email');?>/<?php echo L('password');?></a></li>
				<?php } ?>
				<li<?php if(ROUTE_A=="account_change_mobile") { ?> class="on"<?php } ?>><a href="index.php?m=member&c=index&a=account_change_mobile&t=1"><img src="<?php echo IMG_PATH;?>icon/mobile_phone.png" /> <?php echo L('change_mobile');?></a></li>
            </ul>
            <h5 class="title">编辑微网</h5>
            <ul>
                <li<?php if(ROUTE_A=="carousel_map") { ?> class="on"<?php } ?>><a href="index.php?m=member&c=index&a=carousel_map&t=1"><img src="<?php echo IMG_PATH;?>icon/user_edit.png" width="16" /> 大轮播图</a></li>
                <li<?php if(ROUTE_A=="my_home") { ?> class="on"<?php } ?>><a href="index.php?m=member&c=index&a=my_home&t=1"><img src="<?php echo IMG_PATH;?>icon/vcard.png" width="16" /> 我的链接</a></li>
                <li<?php if(ROUTE_A=="business") { ?> class="on"<?php } ?>><a href="index.php?m=member&c=index&a=business&t=1"><img src="<?php echo IMG_PATH;?>icon/Upload.png" /> 业务中心</a></li>
                <li<?php if(ROUTE_A=="my_show") { ?> class="on"<?php } ?>><a href="index.php?m=member&c=index&a=my_show&t=1"><img src="<?php echo IMG_PATH;?>icon/mobile_phone.png" /> 我的展示</a></li>
            </ul>
            <h5 class="title">发布内容</h5>
            <ul>
                <li<?php if(ROUTE_A=="publish_article") { ?> class="on"<?php } ?>><a href="#"><img src="<?php echo IMG_PATH;?>icon/user_edit.png" width="16" /> 发表文章</a></li>
                <li<?php if(ROUTE_A=="publish_message") { ?> class="on"<?php } ?>><a href="#"><img src="<?php echo IMG_PATH;?>icon/vcard.png" width="16" /> 发布信息</a></li>
            </ul>
            <h5 class="title">发布内容</h5>
            <ul>
                <li<?php if(ROUTE_A=="message_board") { ?> class="on"<?php } ?>><a href="index.php?m=member&c=index&a=message_board&t=1"><img src="<?php echo IMG_PATH;?>icon/user_edit.png" width="16" /> 留言板</a></li>
                <li<?php if(ROUTE_A=="publish_message") { ?> class="on"<?php } ?>><a href="index.php?m=member&c=index&a=publish_message&t=1"><img src="<?php echo IMG_PATH;?>icon/vcard.png" width="16" /> 系统信息</a></li>
                <li<?php if(ROUTE_A=="publish_message") { ?> class="on"<?php } ?>><a href="index.php?m=member&c=index&a=publish_message&t=1"><img src="<?php echo IMG_PATH;?>icon/vcard.png" width="16" /> 我的关注</a></li>
                <li<?php if(ROUTE_A=="publish_message") { ?> class="on"<?php } ?>><a href="index.php?m=member&c=index&a=publish_message&t=1"><img src="<?php echo IMG_PATH;?>icon/vcard.png" width="16" /> 我的名片夹</a></li>
                <li<?php if(ROUTE_A=="publish_message") { ?> class="on"<?php } ?>><a href="index.php?m=member&c=index&a=publish_message&t=1"><img src="<?php echo IMG_PATH;?>icon/vcard.png" width="16" /> 访客统计</a></li>
                <li<?php if(ROUTE_A=="publish_message") { ?> class="on"<?php } ?>><a href="index.php?m=member&c=index&a=publish_message&t=1"><img src="<?php echo IMG_PATH;?>icon/vcard.png" width="16" /> 我的粉丝</a></li>
                <li<?php if(ROUTE_A=="publish_message") { ?> class="on"<?php } ?>><a href="index.php?m=member&c=index&a=publish_message&t=1"><img src="<?php echo IMG_PATH;?>icon/vcard.png" width="16" /> 我带来的用户</a></li>
                <li<?php if(ROUTE_A=="publish_message") { ?> class="on"<?php } ?>><a href="index.php?m=member&c=index&a=publish_message&t=1"><img src="<?php echo IMG_PATH;?>icon/vcard.png" width="16" /> 我的返利收益</a></li>
                <li<?php if(ROUTE_A=="publish_message") { ?> class="on"<?php } ?>><a href="index.php?m=member&c=index&a=publish_message&t=1"><img src="<?php echo IMG_PATH;?>icon/vcard.png" width="16" /> 帮助中心</a></li>
            </ul>
        <span class="o1"></span><span class="o2"></span><span class="o3"></span><span class="o4"></span>
    </div>