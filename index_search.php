<?php	
	include_once "mysql.php";	
	if($_GET['title'] != ""){	
		if($_GET['type'] == "title"){				
          $arr1 = [];
          $res2 = $conn->query("select * from v9_news where title like '%".$_GET['title']."%'");
          while($row2 = $res2->fetch_assoc()){
              $arr[] = $row2;
          }
        }else{        	
          $arr1 = [];
          $res2 = $conn->query("select * from v9_news_data where content like '%".$_GET['title']."%'");
          while($row2 = $res2->fetch_assoc()){
              $arr[] = $row2;
          }             
          foreach($arr as $k=>$v){      	
            $arr[$k]['title'] = $conn->query("select title from v9_news where id = ".$v['id'])->fetch_assoc()['title'];
          	$arr[$k]['url'] = $conn->query("select url from v9_news where id = ".$v['id'])->fetch_assoc()['url']; 
            $arr[$k]['inputtime'] = $conn->query("select inputtime from v9_news where id = ".$v['id'])->fetch_assoc()['inputtime'];    
          }      		
        }
	}else{		
		$arr = "";
	}
?>
<!doctype html>
<html lang="cmn-Hans">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=Edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>上证通网</title>
  <meta name="keywords" content="">
  <meta name="description" content="">
  <link href="http://www.sztnet.com/statics/css/mycss/common.css" rel="stylesheet" />
  <link href="http://www.sztnet.com/statics/css/mycss/szt.css" rel="stylesheet" />
  <script src="http://www.sztnet.com/statics/js/jquery.min.js"></script>
  <script src="http://www.sztnet.com/statics/js/search_common.js"></script>
</head>

<body>
<?php include './phpcms/templates/pub/header.html';?>
<div class="container">
<nav class="mainNav cateNum-10 mb10 clear">
<ul>
  <li><a href="http://www.sztnet.com/newsCenter/" target="_self">资讯</a></li>
  <li><a href="http://www.sztnet.com/regulation/" target="_self">监管</a></li>
  <li><a href="http://www.sztnet.com/thinkTank/" target="_self">智库</a></li>
  <li><a href="http://www.sztnet.com/law/codification/" target="_self">法规</a></li>
  <li>
    <a href="javascript:void(0)" target="_self">业务</a>
    <ul>
      <li><a href="http://www.sztnet.com/ipo/" target="_self">上市</a></li>
      <li><a href="http://www.sztnet.com/fin/" target="_self">融资</a></li>
      <li><a href="http://www.sztnet.com/ma/" target="_self">并购</a></li>
      <li><a href="http://www.sztnet.com/inv/" target="_self">理财</a></li>
      <li><a href="http://www.sztnet.com/incentive/" target="_self">激励</a></li>
      <li><a href="http://www.sztnet.com/lc/" target="_self">公司</a></li>
      <li><a href="http://www.sztnet.com/neeq/" target="_self">股转</a></li>
      <li><a href="http://www.sztnet.com/i-bank/" target="_self">投行</a></li>
      <li><a href="http://www.sztnet.com/auditor/" target="_self">会所</a></li>
      <li><a href="http://www.sztnet.com/lawyer/" target="_self">律所</a></li>
      <li><a href="http://www.sztnet.com/tool/" target="_self">工具</a></li>
    </ul>
  </li>
  <li><a href="http://www.sztnet.com/edu/" target="_self">学院</a></li>
  <li><a href="http://www.sztnet.com/event/" target="_self">活动</a></li>
  <li><a href="http://www.sztnet.com/hr/" target="_self">招聘</a></li>
  <li><a href="http://www.sztnet.com/service/" target="_self">服务</a></li>
  <li><a href="javascript:void(0)" target="_self">商城</a></li>
</ul>
</nav>

<div class="row">
  <div class="col xs-12 md-8">
    <h4 class="bbs">搜索结果</h4>
    <ul class="ul2-l-ymd lbd">            
	<? if($arr){foreach($arr as $k=>$v){?>
      <li><a href="<? echo $v['url']?>" target="_blank"><? echo $v['title']?></a><span><? echo date('Y-m-d',$v['inputtime']) ?></span></li>
    <? }}else{?>
	  <li>抱歉，没找到你想要的内容。</li>	
	<? }?>
    </ul>
    <div id="pages" class="text-c"></div>    
  </div><!--/左侧栏-->

  <div class="col xs-12 md-4">
  </div><!--/右侧栏-->

</div><!--/row-->
</div><!--/container-->

<?php include './phpcms/templates/pub/footer.html';?>
<script>(function(){document.write(unescape('%3Cdiv id="bdcs"%3E%3C/div%3E'));var bdcs = document.createElement('script');bdcs.type = 'text/javascript';bdcs.async = true;bdcs.src = 'http://znsv.baidu.com/customer_search/api/js?sid=11033271430119132646' + '&plate_url=' + encodeURIComponent(window.location.href) + '&t=' + Math.ceil(new Date()/3600000);var s = document.getElementsByTagName('script')[0];s.parentNode.insertBefore(bdcs, s);})();</script>
</body>
</html>