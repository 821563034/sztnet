<?php	
	include_once "../mysql.php";
	
	$place ="";
	$title ="";
	$payrange ="";
	
	if($_GET['sheng'] !=  ""  && $_GET['shi'] ==  "" && $_GET['qu'] ==  ""){
		
		if($_GET['sheng'] <= 5 || $_GET['sheng']==33 || $_GET['sheng']==34 || $_GET['sheng']==35){
			
			$shi_arr = $conn->query("select linkageid from v9_linkage  where parentid = ".$_GET['sheng']);
			
			$shi_id = [];
			
			while($shi_arr2 = $shi_arr->fetch_assoc()){
				
				$shi_id[] = $shi_arr2;
			}
			
			foreach($shi_id as $k=>$v){
				
				$shi_str[]=  $shi_id[$k]['linkageid'];
				
			}
			
			$shi_str = implode(",",$shi_str);
		
				
			$place = " and place in (".$shi_str.")";

			
		}else{
			
			$shi_arr = $conn->query("select linkageid from v9_linkage  where parentid = ".$_GET['sheng']);			
			$shi_id = [];
			$qu_id = [];
			
			while($shi_arr2 = $shi_arr->fetch_assoc()){
				
				$shi_id[] = $shi_arr2;
			}
			
			foreach($shi_id as $k=>$v){
				
				$shi_str =  $conn->query("select linkageid from v9_linkage  where parentid = ".$v['linkageid']);
				while($qu_arr  = $shi_str->fetch_assoc()){
						
					$qu_id[] = $qu_arr;
				}
				
			}
			
			foreach($qu_id as $k=>$v){
				
				$qu_str[]  = $qu_id[$k]['linkageid'];
			}
			
			$qu_str = implode(",",$qu_str);
				
			$place = " and place in (".$qu_str.")";	
			
		}
		
	}
	
	if($_GET['shi'] != ""){
		
		if($_GET['sheng']>5 && $_GET['sheng']!=33 && $_GET['sheng']!=34 && $_GET['sheng']!=35){
		
			if($_GET['qu'] == ""){
				
				$qu_obj = $conn->query("select linkageid from v9_linkage  where parentid = ".$_GET['shi']);
				
				$qu_array = [];
				
				while($qu_arr2 = $qu_obj->fetch_assoc()){
					
					$qu_array[] = $qu_arr2['linkageid'];
				}
				
				$qu_string = implode(",",$qu_array);
				
				$place = " and place in (".$qu_string.")";	
				
			}else{
				
				$place = " and place = ".$_GET['shi'];
				
			}
		}else{
			
			$place = " and place = ".$_GET['shi'];
		}	
	}
	
	if($_GET['qu'] != ""){
		
		$place = " and place = ".$_GET['qu'];
		
	}
	
	if($_GET['title'] != ""){
		
		$title = " and title like '%".$_GET['title']."%'";
	
		
	}

	if(isset($_GET['payrange'])){

		foreach($_GET['payrange'] as $k=>$v){

          $payrange .=   " payrange like '%".$v."%' or";
			
		}
		$payrange = substr($payrange, 0, -3);
		$payrange = " and (".$payrange.")";
	
	}
	
	$sql = "select * from v9_job where nature =".$_GET['nature'].$place.$title.$payrange." order by id desc";	
		
	$res = $conn->query($sql);
	
	$arr = [];
	
	while($row = $res->fetch_assoc()){
		
		$arr[] = $row;
	}

	foreach($arr as $k=>$v){
		
		if($v['place'] != ""){
		
			$child3 = $conn->query("select linkageid,parentid,name from v9_linkage where linkageid = ".$v['place'])->fetch_assoc();
		
			$child2 = $conn->query("select linkageid,parentid,name from v9_linkage where linkageid = ".$child3['parentid'])->fetch_assoc();
		
			$child1 = $conn->query("select linkageid,parentid,name from v9_linkage where linkageid = ".$child2['parentid'])->fetch_assoc();
          
          	if($child1 == ""){
            	$arr[$k]['place'] =$child2['name']."-".$child3['name'];
            }else{
            	$arr[$k]['place'] = $child1['name']."-".$child2['name']."-".$child3['name'];
            }
			
			
		}
	}
?>

<!doctype html>
<html lang="cmn-Hans">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=Edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>招聘信息 - 上证通网</title>
  <meta name="keywords" content="">
  <meta name="description" content="">
  <link href="http://www.sztnet.com/statics/css/mycss/common.css" rel="stylesheet" />
  <link href="http://www.sztnet.com/statics/css/mycss/szt.css" rel="stylesheet" />
  <script src="http://www.sztnet.com/statics/js/jquery.min.js"></script>
  <script src="http://www.sztnet.com/statics/js/search_common.js"></script>
</head>

<body>
<?php include '../phpcms/templates/pub/header.html';?>
<div class="container">
<nav class="mainNav cateNum-10 clear">
<ul>
  <li ><a href="http://www.sztnet.com/newsCenter/" target="_self">资讯</a></li>
  <li ><a href="http://www.sztnet.com/regulation/" target="_self">监管</a></li>
  <li ><a href="http://www.sztnet.com/thinkTank/" target="_self">智库</a></li>
  <li><a href="http://www.sztnet.com/law/codification/" target="_self">法规</a></li>
  <li >
    <a href="javascript:void(0)" target="_self">业务</a>
    <ul>
      <li ><a href="http://www.sztnet.com/ipo/" target="_self">上市</a></li>
      <li ><a href="http://www.sztnet.com/fin/" target="_self">融资</a></li>
      <li ><a href="http://www.sztnet.com/ma/" target="_self">并购</a></li>
      <li ><a href="http://www.sztnet.com/inv/" target="_self">理财</a></li>
      <li ><a href="http://www.sztnet.com/incentive/" target="_self">激励</a></li>
      <li ><a href="http://www.sztnet.com/lc/" target="_self">公司</a></li>
      <li ><a href="http://www.sztnet.com/neeq/" target="_self">股转</a></li>
      <li ><a href="http://www.sztnet.com/i-bank/" target="_self">投行</a></li>
      <li ><a href="http://www.sztnet.com/auditor/" target="_self">会所</a></li>
      <li ><a href="http://www.sztnet.com/lawyer/" target="_self">律所</a></li>
      <li ><a href="http://www.sztnet.com/tool/" target="_self">工具</a></li>
    </ul>
  </li>
  <li ><a href="http://www.sztnet.com/edu/" target="_self">学院</a></li>
  <li ><a href="http://www.sztnet.com/event/" target="_self">活动</a></li>
  <li class="cur"><a href="http://www.sztnet.com/hr/" target="_self">招聘</a></li>
  <li ><a href="http://www.sztnet.com/service/" target="_self">服务</a></li>
  <li><a href="javascript:void(0)" target="_self">商城</a></li>
</ul>
</nav>

<div class="position">
  <div class="pos-lt"><i class="iconfont icon-locationfill"></i><a href="http://www.sztnet.com/hr/">招聘</a> > <a href="http://www.sztnet.com/hr/jobs/">搜索结果</a></div>
  <div class="pos-rt"></div>
</div>

<div class="row">
  <div class="col xs-12 md-8"> 
    <ul class="jobList lbd mb10">            
	<? if($arr){foreach($arr as $k=>$v){?>	
      <li>
        <div class="job1"><a href="<? echo $v['url']?>" target="_blank"><? echo $v['title']?></a><span><? echo $v['salary']?></span></div>
        <span class="job2"><? echo $v['place']?><span>|</span><? echo $v['exp']?><span>|</span><? switch($v['education']){			
			case 1:
			echo "硕士及以上";
			break;
			case 2:
			echo "本科及以上";
			break;
			case 3:
			echo "大专及以上";
			break;
			case 4:
			echo "高中及以上";
			break;
			case 5:
			echo "不限";
			break;			
		}?></span>
        <span class="job3"><? echo $v['coname']; echo"/"; echo date("Y.m.d",$v['inputtime'])?></span>
      </li>
          
	  <? }}else{?>
	  <li>抱歉，没找到你想要的内容。</li>			
	  <? }?>	
    </ul>
  <div id="pages" class="text-c"></div>    
</div><!--/左侧栏结束-->

  <div class="col xs-12 md-4">
  </div><!--/右侧栏结束-->
  
</div><!--/row-->

</div><!--/container-->
<?php include '../phpcms/templates/pub/footer.html';?>
<script>(function(){document.write(unescape('%3Cdiv id="bdcs"%3E%3C/div%3E'));var bdcs = document.createElement('script');bdcs.type = 'text/javascript';bdcs.async = true;bdcs.src = 'http://znsv.baidu.com/customer_search/api/js?sid=11033271430119132646' + '&plate_url=' + encodeURIComponent(window.location.href) + '&t=' + Math.ceil(new Date()/3600000);var s = document.getElementsByTagName('script')[0];s.parentNode.insertBefore(bdcs, s);})();</script></body>
</html>    