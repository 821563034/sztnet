<?php
	include_once "../mysql.php";
	
	$item = "";
	$place  = "";
	$title  = "";

	if($_GET['item'] != ""){
	
      $item = " item like '%".$_GET['item']."%'";
      
    }else{
    
    	$item = "title != ''";
    }

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
			
			if($item != ""){
				
				$place = " and place in (".$shi_str.")";
				
			}else{
				
				$place = " place in (".$shi_str.")";
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
			
			if($item != ""){
				
				$place = " and place in (".$qu_str.")";
				
			}else{
				
				$place = " place in (".$qu_str.")";
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
			
			if($item != ""){
				
				$place = " and place in (".$qu_string.")";
				
			}else{
				
				$place = " place in (".$qu_string.")";
			}
			
		}else{
			
			
			if($item != ""){
				
				$place = " and place = ".$_GET['shi'];
				
			}else{
				
				$place = " and place = ".$_GET['shi'];
			}
			
		}
		
		}else{
			
			$place = " and place = ".$_GET['shi'];
		}
		
	}
	
	if($_GET['qu'] != ""){
		
		$place = " and place = ".$_GET['qu'];
		
	}
	
		if($_GET['title'] != ""){
			
			$title  = " and title like '%".$_GET['title']."%'";
		}
	
		if($title =="" && $item=="" && $place == ""){
			
			$results = "";
			
		}else{
			
			$sql = "select * from v9_service where ".$item.$place.$title." order by id desc";
			
			$res = $conn->query($sql);
			
			$results = [];
			
			while($row =  $res->fetch_assoc()){
				
				$results[] = $row;
			}
		}

	foreach($results as $k=>$v){
		
		if($v['place'] != ""){
		
			$child3 = $conn->query("select linkageid,parentid,name from v9_linkage where linkageid = ".$v['place'])->fetch_assoc();
		
			$child2 = $conn->query("select linkageid,parentid,name from v9_linkage where linkageid = ".$child3['parentid'])->fetch_assoc();
		
			$child1 = $conn->query("select linkageid,parentid,name from v9_linkage where linkageid = ".$child2['parentid'])->fetch_assoc();
			
			if($child1 == ""){
            	$results[$k]['place'] =$child2['name']."-".$child3['name'];
            }else{
            	$results[$k]['place'] = $child1['name']."-".$child2['name']."-".$child3['name'];
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
  <title>服务 - 上证通网</title>
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
  <li ><a href="http://www.sztnet.com/hr/" target="_self">招聘</a></li>
  <li class="cur"><a href="http://www.sztnet.com/service/" target="_self">服务</a></li>
  <li><a href="javascript:void(0)" target="_self">商城</a></li>
</ul>
</nav>
  
<div class="row mt10">
<div class="col xs-12 md-10">
<div class="servSearch mb20">
  <h3 class="h3-2 bts">服务商检索&nbsp;<i class="iconfont icon-search"></i></h3>  
  <form class="servSearch mb20" action="./search.php" method="get">
     <?
		$sql = "select  * from v9_linkage where keyid  = 3368 and parentid = 0";
	
		$res = $conn->query($sql);
		
		$arr = [];
		
		while($row = $res->fetch_assoc()){
				
			$arr[]  = $row;
		}
		
		$sql  = "select * from v9_linkage where parentid = 0 and keyid = 1";	
		
		$result = $conn->query($sql);
		
		$arr2 = [];
		
		while($res = $result->fetch_assoc()){
			
				$arr2[] = $res;
		}
	 ?>
      <table>
        <tr>
          <th valign="top">服务项目</th>
          <td class="servItems">
<div class="bbs">
  <label><input type="radio" name="item" value="" checked /><strong>不限</strong></label>
  <label><input type="radio" name="item" value="1" />投资银行</label>
  <label><input type="radio" name="item" value="2" />资产评估</label>
  <label><input type="radio" name="item" value="3" />会计事务所</label>
  <label><input type="radio" name="item" value="4" />律师事务所</label>
</div>
<div class="bbs">
  <label><input type="radio" name="item" value="11" />IPO咨询</label>
  <label><input type="radio" name="item" value="12" />IPO财务顾问</label>
  <label><input type="radio" name="item" value="13" />募投及行业可研</label>
  <label><input type="radio" name="item" value="14" />IPO财经公关</label>
  <label><input type="radio" name="item" value="21" />公司债财顾</label>
  <label><input type="radio" name="item" value="22" />资产证券化财顾</label>
  <label><input type="radio" name="item" value="23" />短期融资券财顾</label>
  <label><input type="radio" name="item" value="24" />信用评级</label>
  <label><input type="radio" name="item" value="31" />股权质押融资</label>
  <label><input type="radio" name="item" value="32" />大宗交易服务</label>
  <label><input type="radio" name="item" value="33" />税收筹划</label>
  <label><input type="radio" name="item" value="34" />并购重组财顾</label>
  <label><input type="radio" name="item" value="35" />设立并购基金</label>
  <label><input type="radio" name="item" value="41" />回购代操盘</label>
</div>
<div class="bbs">
  <label><input type="radio" name="item" value="51" />股权激励顾问</label>
  <label><input type="radio" name="item" value="52" />股权激励行权系统</label>
  <label><input type="radio" name="item" value="53" />合规与信披咨询</label>
  <label><input type="radio" name="item" value="54" />内部控制咨询</label>
  <label><input type="radio" name="item" value="55" />社会责任咨询</label>
  <label><input type="radio" name="item" value="56" />证券事务软件</label>
</div>
<div class="bbs">
  <label><input type="radio" name="item" value="57" />财经公关</label>
  <label><input type="radio" name="item" value="58" />危机公关</label>
  <label><input type="radio" name="item" value="59" />媒体监测</label>
  <label><input type="radio" name="item" value="60" />新闻传播</label>
  <label><input type="radio" name="item" value="61" />投资者关系</label>
  <label><input type="radio" name="item" value="62" />IR平台建设</label>
  <label><input type="radio" name="item" value="63" />现场路演服务</label>
  <label><input type="radio" name="item" value="64" />网上路演平台</label>
  <label><input type="radio" name="item" value="65" />市值管理</label>
</div>
<div class="bbs">
  <label><input type="radio" name="item" value="66" />高管责任险</label>
  <label><input type="radio" name="item" value="71" />金融信息软件</label>
  <label><input type="radio" name="item" value="72" />财经印刷</label>
  <label><input type="radio" name="item" value="73" />公司宣传片</label>
  <label><input type="radio" name="item" value="74" />大型酒会服务</label>
  <label><input type="radio" name="item" value="75" />工艺礼品</label>
</div>
          </td>
        </tr>
        <tr>
          <th>所在地区</th>
          <td>
			<select id="sheng" name="sheng">
			<option value="">请选择</option>
			<? foreach($arr2 as $k=>$v){ ?>	
				<option value="<? echo $v['linkageid']?>"><? echo $v['name']; ?></option>
			<? }?>	
			</select>
			<select id="shi" name="shi">
			<option	value="">请选择</option>
			</select>
			<select id="qu" name="qu">
			<option value="">请选择</option>	
			</select>
            <span class="placeNote">（可只选省或市）</span>
          </td>
        </tr>
        <tr>
          <th>单位名称</th>
          <td class="firmName"><input type="text" name="title" /></td>
        </tr>
        <tr>
          <td colspan="2"><button type="submit" id="dosubmit">搜索</button></td>
        </tr>
      </table>
  </form>
</div><!--/servSearch-->

  <h3 class="h3-1 bts">搜索结果</h3>
  <ul class="firmList row">
	<? if($results){foreach($results as $k=>$v){?>
	  <li class="col xs-12 md-6"><a href="<? echo $v['url']?>" target="_blank" ><? echo $v['title']?></a><span><? echo $v['place']?></span></li>
    <? }}else{?>
	  <li class="col xs-12 md-6">抱歉，没找到你想要的内容。<br/>如需帮助，请告知我们。</li>	
	<? }?>
  </ul>
</div><!--/左侧-->

<div class="col xs-12 md-2">
  <a class="publishBtn" href="#" target="_blank"><i class="iconfont icon-notices"></i>&nbsp;个人免费登记</a>
</div><!--/右侧-->

</div><!--/row-->
</div><!--/container-->
<?php include '../phpcms/templates/pub/footer.html';?>

<script>(function(){document.write(unescape('%3Cdiv id="bdcs"%3E%3C/div%3E'));var bdcs = document.createElement('script');bdcs.type = 'text/javascript';bdcs.async = true;bdcs.src = 'http://znsv.baidu.com/customer_search/api/js?sid=11033271430119132646' + '&plate_url=' + encodeURIComponent(window.location.href) + '&t=' + Math.ceil(new Date()/3600000);var s = document.getElementsByTagName('script')[0];s.parentNode.insertBefore(bdcs, s);})();</script>
<script>
	$("#xiangmu").change(function(){

		sheng = $("#xiangmu").val();
		if(sheng != ""){

			$.post("./ajax.php",{"sheng":sheng,"type":"xiangmu"},function(data){

				if(data.length>1){
					
					$("#xiangmu2").css("display","block");
					
					for(i=0;i<=data.length;i++){
				
						$("#xiangmu2").append("<option value='"+data[i].linkageid+"'>"+data[i].name+"</option>");
				
					}
					
				}else{
						
						$("#xiangmu2").css("display","none");
				
					}
              
			},"json");
		
		}else{
			
			$("#xiangmu2").css("display","none");
		}	
		
	});
	
	$("#sheng").change(function(){
		$("#shi").find("option").not(":first").remove();
		sheng = $("#sheng").val();	
		
		$.post("./ajax.php",{"sheng":sheng,"type":"sheng"},function(data){

			for(i=0;i<=data.length;i++){
			
				$("#shi").append("<option value='"+data[i].linkageid+"'>"+data[i].name+"</option>");
			
			}			
		
		},"json");
		
	});
	
	$("#shi").change(function(){

		$("#qu").find("option").not(":first").remove();
		
		sheng = $("#shi").val();	
		
		$.post("./ajax.php",{"sheng":sheng,"type":"shi"},function(data){

			for(i=0;i<=data.length;i++){
			
				$("#qu").append("<option value='"+data[i].linkageid+"'>"+data[i].name+"</option>");
			
			}			
		
		},"json");
		
	});
</script>
</body>
</html>