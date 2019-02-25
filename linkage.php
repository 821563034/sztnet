<?php
	
	header ( "Content-type:text/html;charset=utf-8" );
	
	$conn  = new mysqli("127.0.0.1","root","root","sztnet");
	
	$conn->query("SET NAMES 'UTF8'");
	
	$sql  = "select * from v9_linkage where parentid = 0 and keyid = 1";
	
	$result = $conn->query($sql);
	
	//$row = $res->fetch_array(); 
	
	$arr = array();
	
	while($res = $result->fetch_assoc()){
		
		$arr[] = $res;
	
	}
	
	var_dump($arr);


?>