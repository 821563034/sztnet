<?php
/**
 *  extention.func.php 用户自定义函数库
 *
 * @copyright			(C) 2005-2010 PHPCMS
 * @license				http://www.phpcms.cn/license/
 * @lastmodify			2010-10-27
 */
function box($field, $value, $modelid='') {
  $fields = getcache('model_field_'.$modelid,'model');
  extract(string2array($fields[$field]['setting']));
  $options = explode("\n",$fields[$field]['options']);
  foreach($options as $_k) {
		  $v = explode("|",$_k);
		  $k = trim($v[1]);
		  $option[$k] = $v[0];
  }
  $string = '';
  switch($fields[$field]['boxtype']) {
	case 'radio':
			$string = $option[$value];
	break;
  
	case 'checkbox':
			$value_arr = explode(',',$value);
			foreach($value_arr as $_v) {
					if($_v) $string .= $option[$_v].' 、';
			}
	break;
  
	case 'select':
			$string = $option[$value];
	break;
  
	case 'multiple':
			$value_arr = explode(',',$value);
			foreach($value_arr as $_v) {
					if($_v) $string .= $option[$_v].' 、';
			}
	break;
  }
	return $string;
}

/** 
 * 分页，如去掉则分页会有问题; 开发服务商搜索功能时加的,汪洋 
 */  
function makeurlrule() {  
        if(strpos(URLRULE,'.html') === FALSE) {  
                return url_par('page={$'.'page}');  
        }  
        else {  
                $url = preg_replace('/-[0-9]+.html$/','-{$page}.html',get_url());  
                return $url;  
        }  
}  
?>