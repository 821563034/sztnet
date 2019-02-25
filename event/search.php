<?php
	include_once "../mysql.php";
	
	$eventtype = "";
	$place = "";
	
	if($_GET['eventtype'] != ""){
		
		foreach($_GET['eventtype'] as $k=>$v){
			
			$eventtype .= " eventtype like '%".$v."%' or";
		}
		
		$eventtype = substr($eventtype, 0, -3);
		$eventtype = " and (".$eventtype.")";
		
	}
	if($_GET['sheng'] !=  ""  && $_GET['shi'] ==  "" && $_GET['qu'] ==  ""){
		
		if($_GET['sheng'] <= 5 || $_GET['sheng'] == 33 || $_GET['sheng'] == 34 || $_GET['sheng'] == 35){
			
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
	
	$sql  = "select * from v9_event where title != ''".$eventtype.$place." order by id desc";
	
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
  <title>活动 - 上证通网</title>
  <meta name="keywords" content="">
  <meta name="description" content="">
  <link href="http://www.sztnet.com/statics/css/mycss/common.css" rel="stylesheet" />
  <link href="http://www.sztnet.com/statics/css/mycss/szt.css" rel="stylesheet" /> 
  <script src="http://www.sztnet.com/statics/js/jquery.min.js"></script>
  <script src="http://www.sztnet.com/statics/js/search_common.js"></script> 
  <script>
    $(function(){
      $(".menu1 ul li").each(function(i){
        $(this).click(function(){
          $(this).addClass("active").siblings().removeClass("active");
          $(".listbox:eq("+i+")").show().siblings(".listbox").hide();
        })
      })
    })
    $(document).ready(function(){$(".menu1 h1").click(function(){$(".menu1 > ul").toggle(100);});});
  </script>
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
  <li class="cur"><a href="http://www.sztnet.com/event/" target="_self">活动</a></li>
  <li ><a href="http://www.sztnet.com/hr/" target="_self">招聘</a></li>
  <li ><a href="http://www.sztnet.com/service/" target="_self">服务</a></li>
  <li><a href="javascript:void(0)" target="_self">商城</a></li>
</ul>
</nav>
  
<div class="row">
<nav class="col xs-12 subNav">
    <ul>
       <li><a href="http://www.sztnet.com/event/" target="_self">主页</a></li>
       <li><a href="http://www.sztnet.com/event/forum/" target="_self">会议论坛</a></li>
       <li><a href="http://www.sztnet.com/communication/ target="_self"">交流活动</a></li>
   </ul>
</nav>
</div>
  
<div class="row mt10">
<div class="col xs-12 md-9">
    <h3 class="h3-1 bts">搜索结果</h3>
    <ul class="picTxt row5px">
      <? if($arr){ foreach($arr as $k=>$v){?>
       <li class="col5px xs-6 md-3">
        <img src="<? echo $v['thumb']?>"/>
         <div class="txt1">
           <span><? echo $v['holdtime']?></span>
           <span><? echo $v['place']?></span>
         </div>
         <div class="txt2">
           <a href="<? echo $v['url']?>" target="_blank"><? echo $v['title']?></a> 
           <span>费用：<? echo $v['fee']?></span>
           <span>主办：<? echo $v['sponsor']?></span>
           <span>发布：<? echo date("Y.m.d",$v['inputtime'])?></span>
        </div>
      </li>
       <? }}else{?>      
      <li class="col5px xs-6 md-3">抱歉，没找到相关内容。</li>
        <? }?>      
    </ul>
</div><!--/左-->
  
<div class="col xs-12 md-3">
<a class="publishBtn mb10" href="#" target="_blank"><i class="iconfont icon-notices"></i>&nbsp;免费发布相关活动信息</a>

<form class="infoSearch" action="" method="get" name="myform">
<table>
  <tr>
    <th valign="top">活动类型</th>
    <td>
      <label><input type="checkbox" name="eventtype[]" value="1">金融投资</label>
      <label><input type="checkbox" name="eventtype[]" value="2">券商策略会</label>
      <label><input type="checkbox" name="eventtype[]" value="3">上市公司</label>
      <label><input type="checkbox" name="eventtype[]" value="4">股转系统</label>
    </td>
  </tr>
    <tr>
    <th>所在城市</th>
    <td>
		<select id="sheng" name="sheng">
		<option value="">请选择</option>
			
			<option value="2">北京市</option>
			
			<option value="3">上海市</option>
			
			<option value="4">天津市</option>
			
			<option value="5">重庆市</option>
			
			<option value="6">河北省</option>
			
			<option value="7">山西省</option>
			
			<option value="8">内蒙古</option>
			
			<option value="9">辽宁省</option>
			
			<option value="10">吉林省</option>
			
			<option value="11">黑龙江</option>
			
			<option value="12">江苏省</option>
			
			<option value="13">浙江省</option>
			
			<option value="14">安徽省</option>
			
			<option value="15">福建省</option>
			
			<option value="16">江西省</option>
			
			<option value="17">山东省</option>
			
			<option value="18">河南省</option>
			
			<option value="19">湖北省</option>
			
			<option value="20">湖南省</option>
			
			<option value="21">广东省</option>
			
			<option value="22">广西</option>
			
			<option value="23">海南省</option>
			
			<option value="24">四川省</option>
			
			<option value="25">贵州省</option>
			
			<option value="26">云南省</option>
			
			<option value="27">西藏</option>
			
			<option value="28">陕西省</option>
			
			<option value="29">甘肃省</option>
			
			<option value="30">青海省</option>
			
			<option value="31">宁夏</option>
			
			<option value="32">新疆</option>
			
			<option value="33">台湾省</option>
			
			<option value="34">香港</option>
			
			<option value="35">澳门</option>
		</select>
		<select id="shi" name="shi">
		<option	value="">请选择</option>
		</select>
	</td>
  </tr>
 <tr>
    <td colspan="2"><button type="submit"  id="dosubmit">搜索</button></td>
  </tr>
</table>
</form>
</div><!--/右-->
</div><!--/row-->

</div><!--/container-->
<?php include '../phpcms/templates/pub/footer.html';?>

 <script>
	$("#sheng").change(function(){
		$("#shi").find("option").not(":first").remove();
		sheng = $("#sheng").val();	
		
		$.post("./ajax.php",{"sheng":sheng,"type":"sheng"},function(data){
			for(i=0;i<=data.length;i++){
				$("#shi").append("<option value='"+data[i].linkageid+"'>"+data[i].name+"</option>");
			}	
		},"json");		
	});
</script>
<script>(function(){document.write(unescape('%3Cdiv id="bdcs"%3E%3C/div%3E'));var bdcs = document.createElement('script');bdcs.type = 'text/javascript';bdcs.async = true;bdcs.src = 'http://znsv.baidu.com/customer_search/api/js?sid=11033271430119132646' + '&plate_url=' + encodeURIComponent(window.location.href) + '&t=' + Math.ceil(new Date()/3600000);var s = document.getElementsByTagName('script')[0];s.parentNode.insertBefore(bdcs, s);})();</script></body>
</html>      