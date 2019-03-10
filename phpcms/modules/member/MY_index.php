<?php
pc_base::load_sys_class('form', '', 0);
class MY_index extends index
{
    function __construct()
    {
        parent::__construct();
        $this->concern_db = pc_base::load_model('concern_model');
        $this->interview_db = pc_base::load_model('interview_statistics_model');
    }

    public function init()
    {
        //初始化phpsso
        $phpsso_api_url = $this->_init_phpsso();
        $this->account_manage();
    }

    /**
     * 我的名片夹，我关注的人
     */
    public function my_concern()
    {
        $memberinfo = $this->memberinfo;
        $phpsso_api_url = $this->_init_phpsso();
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $friendids = $this->concern_db->listinfo(array('cc_userid'=>$memberinfo['userid']), '', $page, 10);
        $pages = $this->concern_db->pages;
        foreach($friendids as $k=>$v) {
            $friendlist[$k]['friendid'] = $v['userid'];
            $friendlist[$k]['avatar'] = $this->client->ps_getavatar($v['userid']);
            $friendlist[$k]['create_time'] = $v['create_time'];
        }
        include template('member', 'my_concern');
    }

    /**
     * 我的粉丝，关注我的
     */
    public function concern_me()
    {
        $memberinfo = $this->memberinfo;
        $phpsso_api_url = $this->_init_phpsso();
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $friendids = $this->concern_db->listinfo(array('userid'=>$memberinfo['userid']), '', $page, 10);
        $pages = $this->concern_db->pages;
        foreach($friendids as $k=>$v) {
            $friendlist[$k]['friendid'] = $v['cc_userid'];
            $friendlist[$k]['avatar'] = $this->client->ps_getavatar($v['cc_userid']);
            $friendlist[$k]['create_time'] = $v['create_time'];
        }
        include template('member', 'concern_me');
    }

    /**
     * 访客统计
     */
    public function interview_count()
    {
        $memberinfo = $this->memberinfo;
        $phpsso_api_url = $this->_init_phpsso();
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $friendids = $this->interview_db->listinfo(array('userid'=>$memberinfo['userid']), '', $page, 10);
        $pages = $this->interview_db->pages;
        foreach($friendids as $k=>$v) {
            $friendlist[$k]['friendid'] = $v['interview_userid'];
            $friendlist[$k]['avatar'] = $this->client->ps_getavatar($v['interview_userid']);
            $friendlist[$k]['create_time'] = $v['create_time'];
        }
        include template('member', 'interview_count');
    }

    public function register() {
        $this->_session_start();
        //获取用户siteid
        $siteid = isset($_REQUEST['siteid']) && trim($_REQUEST['siteid']) ? intval($_REQUEST['siteid']) : 1;
        //定义站点id常量
        if (!defined('SITEID')) {
            define('SITEID', $siteid);
        }

        //加载用户模块配置
        $member_setting = getcache('member_setting');
        if(!$member_setting['allowregister']) {
            showmessage(L('deny_register'), 'index.php?m=member&c=index&a=login');
        }
        //加载短信模块配置
        $sms_setting_arr = getcache('sms','sms');
        $sms_setting = $sms_setting_arr[$siteid];

        header("Cache-control: private");
        if(isset($_POST['dosubmit'])) {
            if($member_setting['enablcodecheck']=='1'){//开启验证码
                if ((empty($_SESSION['connectid']) && $_SESSION['code'] != strtolower($_POST['code']) && $_POST['code']!==NULL) || empty($_SESSION['code'])) {
                    showmessage(L('code_error'));
                } else {
                    $_SESSION['code'] = '';
                }
            }

            $userinfo = array();
            $userinfo['encrypt'] = create_randomstr(6);

            $userinfo['username'] = (isset($_POST['username']) && is_username($_POST['username'])) ? $_POST['username'] : exit('0');
            $userinfo['nickname'] = (isset($_POST['nickname']) && is_username($_POST['nickname'])) ? $_POST['nickname'] : '';

            $userinfo['email'] = (isset($_POST['email']) && is_email($_POST['email'])) ? $_POST['email'] : '';
            $userinfo['password'] = (isset($_POST['password']) && is_badword($_POST['password'])==false) ? $_POST['password'] : exit('0');

            $userinfo['email'] = (isset($_POST['email']) && is_email($_POST['email'])) ? $_POST['email'] : '';

            $userinfo['modelid'] = isset($_POST['modelid']) ? intval($_POST['modelid']) : 10;
            $userinfo['regip'] = ip();
            $userinfo['point'] = $member_setting['defualtpoint'] ? $member_setting['defualtpoint'] : 0;
            $userinfo['amount'] = $member_setting['defualtamount'] ? $member_setting['defualtamount'] : 0;
            $userinfo['regdate'] = $userinfo['lastdate'] = SYS_TIME;
            $userinfo['siteid'] = $siteid;
            $userinfo['connectid'] = isset($_SESSION['connectid']) ? $_SESSION['connectid'] : '';
            $userinfo['from'] = isset($_SESSION['from']) ? $_SESSION['from'] : '';
            //手机强制验证

            if($member_setting[mobile_checktype]=='1'){
                //取用户手机号
                $mobile_verify = $_POST['mobile_verify'] ? intval($_POST['mobile_verify']) : '';
                if($mobile_verify=='') showmessage('请提供正确的手机验证码！', HTTP_REFERER);
                $sms_report_db = pc_base::load_model('sms_report_model');
                $posttime = SYS_TIME-360;
                $where = "`id_code`='$mobile_verify' AND `posttime`>'$posttime'";
                $r = $sms_report_db->get_one($where,'*','id DESC');
                if(!empty($r)){
                    $userinfo['mobile'] = $r['mobile'];
                }else{
                    showmessage('未检测到正确的手机号码！', HTTP_REFERER);
                }
            }elseif($member_setting[mobile_checktype]=='2'){
                //获取验证码，直接通过POST，取mobile值
                $userinfo['mobile'] = isset($_POST['mobile']) ? $_POST['mobile'] : '';
            }
            if($userinfo['mobile']!=""){
                if(!preg_match('/^1([0-9]{10})$/',$userinfo['mobile'])) {
                    showmessage('请提供正确的手机号码！', HTTP_REFERER);
                }
            }
            unset($_SESSION['connectid'], $_SESSION['from']);

            if($member_setting['enablemailcheck']) {	//是否需要邮件验证
                $userinfo['groupid'] = 7;
            } elseif($member_setting['registerverify']) {	//是否需要管理员审核
                $modelinfo_str = $userinfo['modelinfo'] = isset($_POST['info']) ? array2string(array_map("safe_replace", new_html_special_chars($_POST['info']))) : '';
                $this->verify_db = pc_base::load_model('member_verify_model');
                unset($userinfo['lastdate'],$userinfo['connectid'],$userinfo['from']);
                $userinfo['modelinfo'] = $modelinfo_str;
                $this->verify_db->insert($userinfo);
                showmessage(L('operation_success'), 'index.php?m=member&c=index&a=register&t=3');
            } else {
                //查看当前模型是否开启了短信验证功能
                $model_field_cache = getcache('model_field_'.$userinfo['modelid'],'model');
                if(isset($model_field_cache['mobile']) && $model_field_cache['mobile']['disabled']==0) {
                    $mobile = $_POST['info']['mobile'];
                    if(!preg_match('/^1([0-9]{10})$/',$mobile)) showmessage(L('input_right_mobile'));
                    $sms_report_db = pc_base::load_model('sms_report_model');
                    $posttime = SYS_TIME-300;
                    $where = "`mobile`='$mobile' AND `posttime`>'$posttime'";
                    $r = $sms_report_db->get_one($where);
                    if(!$r || $r['id_code']!=$_POST['mobile_verify']) showmessage(L('error_sms_code'));
                }
                $userinfo['groupid'] = $this->_get_usergroup_bypoint($userinfo['point']);
            }
            //附表信息验证 通过模型获取会员信息
            if($member_setting['choosemodel']) {
                require_once CACHE_MODEL_PATH.'member_input.class.php';
                require_once CACHE_MODEL_PATH.'member_update.class.php';
                $member_input = new member_input($userinfo['modelid']);
                $_POST['info'] = array_map('new_html_special_chars',$_POST['info']);
                $user_model_info = $member_input->get($_POST['info']);
            }
            if(pc_base::load_config('system', 'phpsso')) {
                $this->_init_phpsso();
                $status = $this->client->ps_member_register($userinfo['username'], $userinfo['password'], $userinfo['email'], $userinfo['regip'], $userinfo['encrypt']);
                if($status > 0) {
                    $userinfo['phpssouid'] = $status;
                    //传入phpsso为明文密码，加密后存入phpcms_v9
                    $password = $userinfo['password'];
                    $userinfo['password'] = password($userinfo['password'], $userinfo['encrypt']);
                    $userid = $this->db->insert($userinfo, 1);
                    if($member_setting['choosemodel']) {	//如果开启选择模型
                        $user_model_info['userid'] = $userid;
                        //插入会员模型数据
                        $this->db->set_model($userinfo['modelid']);
                        $this->db->insert($user_model_info);
                    }

                    if($userid > 0) {
                        //执行登陆操作
                        if(!$cookietime) $get_cookietime = param::get_cookie('cookietime');
                        $_cookietime = $cookietime ? intval($cookietime) : ($get_cookietime ? $get_cookietime : 0);
                        $cookietime = $_cookietime ? TIME + $_cookietime : 0;

                        if($userinfo['groupid'] == 7) {
                            param::set_cookie('_username', $userinfo['username'], $cookietime);
                            param::set_cookie('email', $userinfo['email'], $cookietime);
                        } else {
                            $phpcms_auth = sys_auth($userid."\t".$userinfo['password'], 'ENCODE', get_auth_key('login'));

                            param::set_cookie('auth', $phpcms_auth, $cookietime);
                            param::set_cookie('_userid', $userid, $cookietime);
                            param::set_cookie('_username', $userinfo['username'], $cookietime);
                            param::set_cookie('_nickname', $userinfo['nickname'], $cookietime);
                            param::set_cookie('_groupid', $userinfo['groupid'], $cookietime);
                            param::set_cookie('cookietime', $_cookietime, $cookietime);
                        }
                    }
                    //如果需要邮箱认证
                    if($member_setting['enablemailcheck']) {
                        pc_base::load_sys_func('mail');
                        $code = sys_auth($userid.'|'.microtime(true), 'ENCODE', get_auth_key('email'));
                        $url = APP_PATH."index.php?m=member&c=index&a=register&code=$code&verify=1";
                        $message = $member_setting['registerverifymessage'];
                        $message = str_replace(array('{click}','{url}','{username}','{email}','{password}'), array('<a href="'.$url.'">'.L('please_click').'</a>',$url,$userinfo['username'],$userinfo['email'],$password), $message);
                        sendmail($userinfo['email'], L('reg_verify_email'), $message);
                        //设置当前注册账号COOKIE，为第二步重发邮件所用
                        $_SESSION['_regusername'] = $userinfo['username'];
                        $_SESSION['_reguserid'] = $userid;
                        $_SESSION['_reguseruid'] = $userinfo['phpssouid'];
                        showmessage(L('operation_success'), 'index.php?m=member&c=index&a=register&t=2');
                    } else {
                        //如果不需要邮箱认证、直接登录其他应用
                        $synloginstr = $this->client->ps_member_synlogin($userinfo['phpssouid']);
                        showmessage(L('operation_success').$synloginstr, 'index.php?m=member&c=index&a=init');
                    }

                }
            } else {
                showmessage(L('enable_register').L('enable_phpsso'), 'index.php?m=member&c=index&a=login');
            }
            showmessage(L('operation_failure'), HTTP_REFERER);
        } else {
            if(!pc_base::load_config('system', 'phpsso')) {
                showmessage(L('enable_register').L('enable_phpsso'), 'index.php?m=member&c=index&a=login');
            }

            if(!empty($_GET['verify'])) {
                $code = isset($_GET['code']) ? trim($_GET['code']) : showmessage(L('operation_failure'), 'index.php?m=member&c=index');
                $code_res = sys_auth($code, 'DECODE', get_auth_key('email'));
                $code_arr = explode('|', $code_res);
                $userid = isset($code_arr[0]) ? $code_arr[0] : '';
                $userid = is_numeric($userid) ? $userid : showmessage(L('operation_failure'), 'index.php?m=member&c=index');

                $this->db->update(array('groupid'=>$this->_get_usergroup_bypoint()), array('userid'=>$userid));
                showmessage(L('operation_success'), 'index.php?m=member&c=index');
            } elseif(!empty($_GET['protocol'])) {

                include template('member', 'protocol');
            } else {
                //过滤非当前站点会员模型
                $modellist = getcache('member_model', 'commons');
                foreach($modellist as $k=>$v) {
                    if($v['siteid']!=$siteid || $v['disabled']) {
                        unset($modellist[$k]);
                    }
                }
                if(empty($modellist)) {
                    showmessage(L('site_have_no_model').L('deny_register'), HTTP_REFERER);
                }
                //是否开启选择会员模型选项
                if($member_setting['choosemodel']) {
                    $first_model = array_pop(array_reverse($modellist));
                    $modelid = isset($_GET['modelid']) && in_array($_GET['modelid'], array_keys($modellist)) ? intval($_GET['modelid']) : $first_model['modelid'];

                    if(array_key_exists($modelid, $modellist)) {
                        //获取会员模型表单
                        require CACHE_MODEL_PATH.'member_form.class.php';
                        $member_form = new member_form($modelid);
                        $this->db->set_model($modelid);
                        $forminfos = $forminfos_arr = $member_form->get();

                        //万能字段过滤
                        foreach($forminfos as $field=>$info) {
                            if($info['isomnipotent']) {
                                unset($forminfos[$field]);
                            } else {
                                if($info['formtype']=='omnipotent') {
                                    foreach($forminfos_arr as $_fm=>$_fm_value) {
                                        if($_fm_value['isomnipotent']) {
                                            $info['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'], $info['form']);
                                        }
                                    }
                                    $forminfos[$field]['form'] = $info['form'];
                                }
                            }
                        }

                        $formValidator = $member_form->formValidator;
                    }
                }
                $description = $modellist[$modelid]['description'];

                include template('member', 'register');
            }
        }
    }

    /**
     * 初始化phpsso
     * about phpsso, include client and client configure
     * @return string phpsso_api_url phpsso地址
     */
    private function _init_phpsso() {
        pc_base::load_app_class('client', '', 0);
        define('APPID', pc_base::load_config('system', 'phpsso_appid'));
        $phpsso_api_url = pc_base::load_config('system', 'phpsso_api_url');
        $phpsso_auth_key = pc_base::load_config('system', 'phpsso_auth_key');
        $this->client = new client($phpsso_api_url, $phpsso_auth_key);
        return $phpsso_api_url;
    }

    private function _session_start() {
        $session_storage = 'session_'.pc_base::load_config('system','session_storage');
        pc_base::load_sys_class($session_storage);
    }
}