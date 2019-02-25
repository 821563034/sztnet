<?php

	include_once "../mysql.php";
	

		
		$sql  = "select * from v9_linkage where parentid = ".$_POST['sheng'];
		
		$result = $conn->query($sql); 
		
		$arr = [];
		
		while($res = $result->fetch_assoc()){
			
				$arr[] = $res;
		}
		
		echo json_encode($arr);


	
	
	
	
?>