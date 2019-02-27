<?php
pc_base::load_sys_class('form', '', 0);
class MY_index extends index
{
    private $db;

    function __construct()
    {
        parent::__construct();
        $this->message_db = pc_base::load_model('message_model');
        $this->member_db = pc_base::load_model('member_model');
        $this->concern_db = pc_base::load_model('concern_model');
    }

    /**
     * 会员主页
     */
    public function member_home()
    {
        $catid = intval($_GET['catid']);
        $userid = intval($_GET['userid']);

        $_userid = $this->_userid;
        $_username = $this->_username;
        $_groupid = $this->_groupid;

        $siteids = getcache('category_content','commons');
        $siteid = $siteids[$catid];
        $CATEGORYS = getcache('category_content_'.$siteid,'commons');
        if(!isset($CATEGORYS[$catid]) || $CATEGORYS[$catid]['type']!=0) showmessage(L('information_does_not_exist'),'blank');
        $this->category = $CAT = $CATEGORYS[$catid];
        $this->category_setting = $CAT['setting'] = string2array($this->category['setting']);
        $siteid = $GLOBALS['siteid'] = $CAT['siteid'];

        $MODEL = getcache('model','commons');
        $modelid = $CAT['modelid'];

        $template = $template ? $template : $CAT['setting']['show_template'];
        if(!$template) $template = 'show';
        include template('content',$template);
    }

    /**
     * 会员详细信息
     */
    public function member_info() {
        $userid = intval($_GET['userid']);

        $member_db = pc_base::load_model('member_model');
        $where = "`userid`='$userid'";
        $this->memberinfo = $memberinfo = $member_db->get_one($where);

        $grouplist = getcache('grouplist');
        $member_model = getcache('member_model', 'commons');
        //获取用户模型数据
        $member_db->set_model($memberinfo['modelid']);
		$member_modelinfo_arr = $member_db->get_one(array('userid'=>$memberinfo['userid']));
		$model_info = getcache('model_field_'.$memberinfo['modelid'], 'model');
		foreach($model_info as $k=>$v) {
            if($v['formtype'] == 'omnipotent') continue;
            if($v['formtype'] == 'image') {
                $member_modelinfo[$v['name']] = "<a href='$member_modelinfo_arr[$k]' target='_blank'><img src='$member_modelinfo_arr[$k]' height='40' widht='40' onerror=\"this.src='$phpsso_api_url/statics/images/member/nophoto.gif'\"></a>";
            } elseif($v['formtype'] == 'datetime' && $v['fieldtype'] == 'int') {	//如果为日期字段
                $member_modelinfo[$v['name']] = format::date($member_modelinfo_arr[$k], $v['format'] == 'Y-m-d H:i:s' ? 1 : 0);
            } elseif($v['formtype'] == 'images') {
                $tmp = string2array($member_modelinfo_arr[$k]);
                $member_modelinfo[$v['name']] = '';
                if(is_array($tmp)) {
                    foreach ($tmp as $tv) {
                        $member_modelinfo[$v['name']] .= " <a href='$tv[url]' target='_blank'><img src='$tv[url]' height='40' widht='40' onerror=\"this.src='$phpsso_api_url/statics/images/member/nophoto.gif'\"></a>";
                    }
                    unset($tmp);
                }
            } elseif($v['formtype'] == 'box') {	//box字段，获取字段名称和值的数组
                $tmp = explode("\n",$v['options']);
                if(is_array($tmp)) {
                    foreach($tmp as $boxv) {
                        $box_tmp_arr = explode('|', trim($boxv));
                        if(is_array($box_tmp_arr) && isset($box_tmp_arr[1]) && isset($box_tmp_arr[0])) {
                            $box_tmp[$box_tmp_arr[1]] = $box_tmp_arr[0];
                            $tmp_key = intval($member_modelinfo_arr[$k]);
                        }
                    }
                }
                if(isset($box_tmp[$tmp_key])) {
                    $member_modelinfo[$v['name']] = $box_tmp[$tmp_key];
                } else {
                    $member_modelinfo[$v['name']] = $member_modelinfo_arr[$k];
                }
                unset($tmp, $tmp_key, $box_tmp, $box_tmp_arr);
            } elseif($v['formtype'] == 'linkage') {	//如果为联动菜单
                $tmp = string2array($v['setting']);
                $tmpid = $tmp['linkageid'];
                $linkagelist = getcache($tmpid, 'linkage');
                $fullname = $this->_get_linkage_fullname($member_modelinfo_arr[$k], $linkagelist);

                $member_modelinfo[$v['name']] = substr($fullname, 0, -1);
                unset($tmp, $tmpid, $linkagelist, $fullname);
            } else {
                $member_modelinfo[$v['name']] = $member_modelinfo_arr[$k];
            }
        }

        include template('content', 'member_info');
    }

    /**
     * 发送消息
     */
    public function send() {
        $userid = intval($_GET['userid']);
        if(isset($_POST['dosubmit'])) {
            $username = $this->_username;
            if (empty($username)) $username = '匿名';
            $tousername = safe_replace($_POST['info']['send_to_id']);
            $r = $this->member_db->get_one(array('username'=>$tousername));
            if(!$r) showmessage(L('user_not_exist','','member'));
            if($tousername==$username){
                showmessage(L('not_myself','','message'));
            }
            $subject = new_html_special_chars($_POST['info']['subject']);
            $content = new_html_special_chars($_POST['info']['content']);
            $this->message_db->add_message($tousername,$username,$subject,$content,true);
            showmessage(L('operation_success'),HTTP_REFERER);
        } else {
            $show_validator = $show_scroll = $show_header = true;
            include template('content', 'message_send');
        }
    }

    /**
     * 关注
     */
    public function concern(){
        $userid = intval($_POST['userid']);
        $cc_userid = $this->_userid;
        if(!$cc_userid) {
            $res['code'] = 0;
            $res['msg'] = '请先登录';
            echo json_encode($res);exit();
        }
        $this->concern_db->add_concern($userid,$cc_userid);
        $res['code'] = 1;
        $res['msg'] = '关注成功';
        echo json_encode($res);exit();
    }

    /*
	 * 通过linkageid获取名字路径
	 */
    protected function _get_linkage_fullname($linkageid,  $linkagelist) {
        $fullname = '';
        if($linkagelist['data'][$linkageid]['parentid'] != 0) {
            $fullname = $this->_get_linkage_fullname($linkagelist['data'][$linkageid]['parentid'], $linkagelist);
        }
        //所在地区名称
        $return = $fullname.$linkagelist['data'][$linkageid]['name'].'>';
        return $return;
    }
}