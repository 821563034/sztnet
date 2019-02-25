<?php
	
	include_once  "../../mysql.php";
	
	$classtype =" title != ''";
	$place ="";
	
	if($_GET['classtype'] !=""){
      
      	$classtype  = " catid = ".$_GET['classtype'];
	
	}
	
	if($_GET['sheng'] !=  ""  && $_GET['shi'] ==  ""){
		
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
			
			if($classtype == ""){
				
				$place = " place in (".$shi_str.")";
				
			}else{
				
				$place = " and place in (".$shi_str.")";
			}	
			
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
				
			if($classtype == ""){
				
				$place = " place in (".$qu_str.")";
				
			}else{
				
				$place = " and place in (".$qu_str.")";
			}
			
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
	
	$sql = "select * from v9_ptrain where ".$classtype.$place." and catid in (421,422,423,424,425,426) order by id desc";

	if($classtype == "" && $place==""){
		
		$ptrain_arr ="";
		
	}else{
		
		$ptrain = $conn->query($sql);

		$ptrain_arr =  [];
		
		while($row = $ptrain->fetch_assoc()){
			
			$ptrain_arr[]  = $row;
		}
		
	}

	foreach($ptrain_arr as $k=>$v){
		
		if($v['place'] != ""){
		
			$child3 = $conn->query("select linkageid,parentid,name from v9_linkage where linkageid = ".$v['place'])->fetch_assoc();

			$child2 = $conn->query("select linkageid,parentid,name from v9_linkage where linkageid = ".$child3['parentid'])->fetch_assoc();

			$child1 = $conn->query("select linkageid,parentid,name from v9_linkage where linkageid = ".$child2['parentid'])->fetch_assoc();
			
			if($child1 == ""){
            	$ptrain_arr[$k]['place'] =$child2['name']."-".$child3['name'];
            }else{
            	$ptrain_arr[$k]['place'] = $child1['name']."-".$child2['name']."-".$child3['name'];
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
  <title>公开课程 - 上证通网</title>
  <meta name="keywords" content="">
  <meta name="description" content=""> 
  <link href="http://www.sztnet.com/statics/css/mycss/common.css" rel="stylesheet" />
  <link href="http://www.sztnet.com/statics/css/mycss/szt.css" rel="stylesheet" />
  <script src="http://www.sztnet.com/statics/js/jquery.min.js"></script>
  <script src="http://www.sztnet.com/statics/js/search_common.js"></script>
  <script>$(document).ready(function(){$(".menu3 h1").click(function(){$(".menu3 > ul").toggle(100);});});</script>
</head>

<body>
<?php include '../../phpcms/templates/pub/header.html';?>
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
  <li class="cur"><a href="http://www.sztnet.com/edu/" target="_self">学院</a></li>
  <li><a href="http://www.sztnet.com/event/" target="_self">活动</a></li>
  <li><a href="http://www.sztnet.com/hr/" target="_self">招聘</a></li>
  <li><a href="http://www.sztnet.com/service/" target="_self">服务</a></li>
  <li><a href="javascript:void(0)" target="_self">商城</a></li>
</ul>
</nav>
  
<div class="row">
  <nav class="col xs-12 subNav">
    <ul>
       <li class="current"><a href="http://www.sztnet.com/edu/public//">公开课程</a></li>
       <li><a href="http://www.sztnet.com/edu/internal//">企业内训</a></li>
       <li><a href="http://www.sztnet.com/edu/official//">官方培训</a></li>
    </ul>
  </nav>
</div>

<div class="row">
  <div class="col xs-12 md-9">
    <h3 class="h3-1 bts">搜索结果</h3>
    <ul class="picTxt row5px">
	<?php if($ptrain_arr){  foreach($ptrain_arr as $v){?>
     <li class="col5px xs-6 md-3">
        <img src="<? echo $v['thumb']?>" />
        <div class="txt1"><span><? echo $v['holdtime'] ;?></span><span><? echo $v['place']?></span></div>
        <div class="txt2">
          <a href="<? echo $v['url']?>" target="_blank"><? echo $v['title']?></a>
          <span>费用：<? echo $v['fee']?></span><span>主办：<? echo $v['sponsor']?></span><span>发布：<? echo date("Y-m-d",$v['inputtime'])?></span>
        </div>
      </li>
      <? }}else{?>    
      <li class="col5px xs-6 md-3">抱歉，没找到相关内容。</li>
	  <?php }?>	          
    </ul>
  </div><!--/左-->

  <div class="col xs-12 md-3">
    <a class="publishBtn mb10" href="http://www.sztnet.com/thisSite/infoPublish.html" target="_blank"><i class="iconfont icon-notices"></i>&nbsp;免费发布相关培训信息</a> 
    <form class="infoSearch" action="search.php" method="get" name="myform">
    <table>
      <tr>
        <?php 

            $label_sql  = "select * from v9_category where parentid = 418";	

            $label_res = $conn->query($label_sql);

            $label_arr = [];

            while($row = $label_res->fetch_assoc()){

                    $label_arr[] = $row;
            }

          ?>
        <th valign="top">课程类型</th>
        <td>
          <label><input type="radio" name="classtype" value=""  checked/>不限</label>
          	
          <? foreach($label_arr as $v){?>	
		  <label><input type="radio" name="classtype" value="<? echo $v['catid']?>"/><? echo $v['catname']?></label>
		  <? }?>	
                	
        </td>
      </tr>
      <tr>
        <th>培训地点</th>
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
<?php include '../../phpcms/templates/pub/footer.html';?>
<script>(function(){document.write(unescape('%3Cdiv id="bdcs"%3E%3C/div%3E'));var bdcs = document.createElement('script');bdcs.type = 'text/javascript';bdcs.async = true;bdcs.src = 'http://znsv.baidu.com/customer_search/api/js?sid=11033271430119132646' + '&plate_url=' + encodeURIComponent(window.location.href) + '&t=' + Math.ceil(new Date()/3600000);var s = document.getElementsByTagName('script')[0];s.parentNode.insertBefore(bdcs, s);})();</script>
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
</body>
</html>