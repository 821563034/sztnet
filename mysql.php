<?php
	
	  
	header ( "Content-type:text/html;charset=utf-8" );
	
	$conn  = new mysqli("127.0.0.1","www_sztnet_com","2RfibPdbz5h6NcJT","www_sztnet_com");
		
	$conn->query("SET NAMES 'UTF8'");
	
	//薪酬范围

	$label_sql  = "select * from v9_category where parentid = 418";	
	
	$label_res = $conn->query($label_sql);
	
	//$row = $res->fetch_array(); 
	
	$label_arr = [];
	
	while($row = $label_res->fetch_assoc()){
		
			$label_arr[] = $row;
	}

	$item_obj = $conn->query("select setting from v9_model_field where field = 'item'");

	$item_str = json_decode($item_obj->fetch_assoc()["setting"]);
	
	$item_arr = explode("\n",$item_str->options);
	
	//s$item_arr = explode("\n",);

	foreach($item_arr as $k=>$v){
    
      	$item_arr[$k] = explode("|",$v);
   		$item_arr[$k][1] = str_replace(array("\r\n", "\r", "\n"),'',$item_arr[$k][1]);
    	 
    }	
	

	$payrange_obj = $conn->query("select setting from v9_model_field where field = 'payrange'");

	$payrange_str = json_decode($payrange_obj->fetch_assoc()["setting"]);
	
	$payrange_arr = explode("\n",$payrange_str->options);
	
	//s$item_arr = explode("\n",);

	foreach($payrange_arr as $k=>$v){
    
      	$payrange_arr[$k] = explode("|",$v);
   		$payrange_arr[$k][1] = str_replace(array("\r\n", "\r", "\n"),'',$payrange_arr[$k][1]);
    	 
    }
	
	//var_dump($payrange_arr);die;
	
?>