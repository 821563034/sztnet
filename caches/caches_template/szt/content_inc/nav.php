<?php defined('IN_PHPCMS') or exit('No permission resources.'); ?><?php $t=$CATEGORYS[$top_parentid][catid]?>
<nav class="mainNav cateNum-12 clear">
<ul>
  <li<?php if($t==12) { ?> class="cur"<?php } ?>><a href="<?php echo $CATEGORYS['12']['url'];?>" target="_self">资讯</a></li>
  <li<?php if($t==13) { ?> class="cur"<?php } ?>><a href="<?php echo $CATEGORYS['13']['url'];?>" target="_self">监管</a></li>
  <li<?php if($t==14) { ?> class="cur"<?php } ?>><a href="<?php echo $CATEGORYS['14']['url'];?>" target="_self">智库</a></li>
  <li><a href="http://www.sztnet.com/law/" target="_blank">法规</a></li>
  <li<?php if($t==55) { ?> class="cur"<?php } ?>><a href="<?php echo $CATEGORYS['55']['url'];?>" target="_self">产业</a></li>
  <li<?php if($t==21) { ?> class="cur"<?php } ?>>
    <a id="biz" href="javascript:void(0)" target="_self"><?php if($t==15) { ?>上市<?php } elseif ($t==16) { ?>融资<?php } elseif ($t==18) { ?>并购<?php } elseif ($t==17) { ?>理财<?php } elseif ($t==436) { ?>激励<?php } elseif ($t==19) { ?>公司<?php } elseif ($t==20) { ?>股转<?php } elseif ($t==122) { ?>投行<?php } elseif ($t==123) { ?>会所<?php } elseif ($t==124) { ?>律所<?php } elseif ($t==21) { ?>工具<?php } else { ?>业务<?php } ?></a>
    <ul>
      <li<?php if($t==15) { ?> class="cur"<?php } ?>><a href="<?php echo $CATEGORYS['15']['url'];?>" target="_self">上市</a></li>
      <li<?php if($t==16) { ?> class="cur"<?php } ?>><a href="<?php echo $CATEGORYS['16']['url'];?>" target="_self">融资</a></li>
      <li<?php if($t==18) { ?> class="cur"<?php } ?>><a href="<?php echo $CATEGORYS['18']['url'];?>" target="_self">并购</a></li>
      <li<?php if($t==17) { ?> class="cur"<?php } ?>><a href="<?php echo $CATEGORYS['17']['url'];?>" target="_self">理财</a></li>
      <li<?php if($t==436) { ?> class="cur"<?php } ?>><a href="<?php echo $CATEGORYS['436']['url'];?>" target="_self">激励</a></li>
      <li<?php if($t==19) { ?> class="cur"<?php } ?>><a href="<?php echo $CATEGORYS['19']['url'];?>" target="_self">公司</a></li>
      <li<?php if($t==20) { ?> class="cur"<?php } ?>><a href="<?php echo $CATEGORYS['20']['url'];?>" target="_self">股转</a></li>
      <li<?php if($t==122) { ?> class="cur"<?php } ?>><a href="<?php echo $CATEGORYS['122']['url'];?>" target="_self">投行</a></li>
      <li<?php if($t==123) { ?> class="cur"<?php } ?>><a href="<?php echo $CATEGORYS['123']['url'];?>" target="_self">会所</a></li>
      <li<?php if($t==124) { ?> class="cur"<?php } ?>><a href="<?php echo $CATEGORYS['124']['url'];?>" target="_self">律所</a></li>
      <li<?php if($t==21) { ?> class="cur"<?php } ?>><a href="<?php echo $CATEGORYS['21']['url'];?>" target="_self">工具</a></li>
    </ul>
  </li>
  <li<?php if($t==417) { ?> class="cur"<?php } ?>><a href="<?php echo $CATEGORYS['417']['url'];?>" target="_self">学院</a></li>
  <li<?php if($t==125) { ?> class="cur"<?php } ?>><a href="<?php echo $CATEGORYS['125']['url'];?>" target="_self">活动</a></li>
  <li<?php if($t==126) { ?> class="cur"<?php } ?>><a href="<?php echo $CATEGORYS['126']['url'];?>" target="_self">招聘</a></li>
  <li<?php if($t==499) { ?> class="cur"<?php } ?>><a href="<?php echo $CATEGORYS['499']['url'];?>" target="_self">微群</a></li>
  <li<?php if($t==117) { ?> class="cur"<?php } ?>><a href="<?php echo $CATEGORYS['117']['url'];?>" target="_self">服务</a></li>
  <li<?php if($t==56) { ?> class="cur"<?php } ?>><a href="<?php echo $CATEGORYS['56']['url'];?>" target="_self">商脉</a></li>
</ul>
</nav>
<script type="text/javascript">
  $(document).ready(function(){
    $("#biz").click(function(){$("#biz").siblings().toggle(100);});
  });
</script>