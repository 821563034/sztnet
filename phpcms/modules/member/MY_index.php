<?php
pc_base::load_sys_class('form', '', 0);
class MY_index extends index
{
    private $_userid;
    private $_username;
    private $concern_db;
    private $interview_db;
    private $linkage_db;
    private $times_db;

    function __construct()
    {
        parent::__construct();
        $this->concern_db = pc_base::load_model('concern_model');
        $this->interview_db = pc_base::load_model('interview_statistics_model');
        $this->linkage_db = pc_base::load_model('linkage_model');
        $this->_userid = param::get_cookie('_userid');
        $this->_username = param::get_cookie('_username');
    }

    public function init()
    {
        //初始化phpsso
        $phpsso_api_url = $this->_init_phpsso();
        $this->account_manage();
    }

    public function login() {
        $this->_session_start();
        //获取用户siteid
        $siteid = isset($_REQUEST['siteid']) && trim($_REQUEST['siteid']) ? intval($_REQUEST['siteid']) : 1;
        //定义站点id常量
        if (!defined('SITEID')) {
            define('SITEID', $siteid);
        }

        if(isset($_POST['dosubmit'])) {
            if(empty($_SESSION['connectid'])) {
                //判断验证码
                $code = isset($_POST['code']) && trim($_POST['code']) ? trim($_POST['code']) : showmessage(L('input_code'), HTTP_REFERER);
                if ($_SESSION['code'] != strtolower($code)) {
                    $_SESSION['code'] = '';
                    showmessage(L('code_error'), HTTP_REFERER);
                }
                $_SESSION['code'] = '';
            }

            $username = isset($_POST['username']) && (is_username($_POST['username']) || is_email($_POST['username']) || preg_match('/^1([0-9]{10})$/',$_POST['username'])) ? trim($_POST['username']) : showmessage('请输入用户名/手机号/邮箱', HTTP_REFERER);
            $password = isset($_POST['password']) && trim($_POST['password']) ? trim($_POST['password']) : showmessage(L('password_empty'), HTTP_REFERER);
            is_password($_POST['password']) && is_badword($_POST['password'])==false ? trim($_POST['password']) : showmessage(L('password_format_incorrect'), HTTP_REFERER);
            $cookietime = intval($_POST['cookietime']);
            $synloginstr = ''; //同步登陆js代码

            if(pc_base::load_config('system', 'phpsso')) {
                $this->_init_phpsso();
                $status = $this->client->ps_member_login($username, $password);
                $memberinfo = unserialize($status);
                if(isset($memberinfo['uid'])) {
                    //查询帐号
                    $r = $this->db->get_one(array('phpssouid'=>$memberinfo['uid']));
                    if(!$r) {
                        //插入会员详细信息，会员不存在 插入会员
                        $info = array(
                            'phpssouid'=>$memberinfo['uid'],
                            'username'=>$memberinfo['username'],
                            'password'=>$memberinfo['password'],
                            'encrypt'=>$memberinfo['random'],
                            'email'=>$memberinfo['email'],
                            'regip'=>$memberinfo['regip'],
                            'regdate'=>$memberinfo['regdate'],
                            'lastip'=>$memberinfo['lastip'],
                            'lastdate'=>$memberinfo['lastdate'],
                            'groupid'=>$this->_get_usergroup_bypoint(),	//会员默认组
                            'modelid'=>10,	//普通会员
                        );

                        //如果是connect用户
                        if(!empty($_SESSION['connectid'])) {
                            $userinfo['connectid'] = $_SESSION['connectid'];
                        }
                        if(!empty($_SESSION['from'])) {
                            $userinfo['from'] = $_SESSION['from'];
                        }
                        unset($_SESSION['connectid'], $_SESSION['from']);

                        $this->db->insert($info);
                        unset($info);
                        $r = $this->db->get_one(array('phpssouid'=>$memberinfo['uid']));
                    }
                    $password = $r['password'];
                    $synloginstr = $this->client->ps_member_synlogin($r['phpssouid']);
                } else {
                    if($status == -1) {	//用户不存在
                        showmessage(L('user_not_exist'), 'index.php?m=member&c=index&a=login');
                    } elseif($status == -2) { //密码错误
                        showmessage(L('password_error'), 'index.php?m=member&c=index&a=login');
                    } else {
                        showmessage(L('login_failure'), 'index.php?m=member&c=index&a=login');
                    }
                }

            } else {
                //密码错误剩余重试次数
                $this->times_db = pc_base::load_model('times_model');
                $rtime = $this->times_db->get_one(array('username'=>$username));
                if($rtime['times'] > 4) {
                    $minute = 60 - floor((SYS_TIME - $rtime['logintime']) / 60);
                    showmessage(L('wait_1_hour', array('minute'=>$minute)));
                }

                //查询帐号
                $r = $this->db->get_one(array('username'=>$username));

                if(!$r) showmessage(L('user_not_exist'),'index.php?m=member&c=index&a=login');

                //验证用户密码
                $password = md5(md5(trim($password)).$r['encrypt']);
                if($r['password'] != $password) {
                    $ip = ip();
                    if($rtime && $rtime['times'] < 5) {
                        $times = 5 - intval($rtime['times']);
                        $this->times_db->update(array('ip'=>$ip, 'times'=>'+=1'), array('username'=>$username));
                    } else {
                        $this->times_db->insert(array('username'=>$username, 'ip'=>$ip, 'logintime'=>SYS_TIME, 'times'=>1));
                        $times = 5;
                    }
                    showmessage(L('password_error', array('times'=>$times)), 'index.php?m=member&c=index&a=login', 3000);
                }
                $this->times_db->delete(array('username'=>$username));
            }

            //如果用户被锁定
            if($r['islock']) {
                showmessage(L('user_is_lock'));
            }

            $userid = $r['userid'];
            $groupid = $r['groupid'];
            $username = $r['username'];
            $nickname = empty($r['nickname']) ? $username : $r['nickname'];

            $updatearr = array('lastip'=>ip(), 'lastdate'=>SYS_TIME);
            //vip过期，更新vip和会员组
            if($r['overduedate'] < SYS_TIME) {
                $updatearr['vip'] = 0;
            }

            //检查用户积分，更新新用户组，除去邮箱认证、禁止访问、游客组用户、vip用户，如果该用户组不允许自助升级则不进行该操作
            if($r['point'] >= 0 && !in_array($r['groupid'], array('1', '7', '8')) && empty($r[vip])) {
                $grouplist = getcache('grouplist');
                if(!empty($grouplist[$r['groupid']]['allowupgrade'])) {
                    $check_groupid = $this->_get_usergroup_bypoint($r['point']);

                    if($check_groupid != $r['groupid']) {
                        $updatearr['groupid'] = $groupid = $check_groupid;
                    }
                }
            }

            //如果是connect用户
            if(!empty($_SESSION['connectid'])) {
                $updatearr['connectid'] = $_SESSION['connectid'];
            }
            if(!empty($_SESSION['from'])) {
                $updatearr['from'] = $_SESSION['from'];
            }
            unset($_SESSION['connectid'], $_SESSION['from']);

            $this->db->update($updatearr, array('userid'=>$userid));

            if(!isset($cookietime)) {
                $get_cookietime = param::get_cookie('cookietime');
            }
            $_cookietime = $cookietime ? intval($cookietime) : ($get_cookietime ? $get_cookietime : 0);
            $cookietime = $_cookietime ? SYS_TIME + $_cookietime : 0;

            $phpcms_auth = sys_auth($userid."\t".$password, 'ENCODE', get_auth_key('login'));

            param::set_cookie('auth', $phpcms_auth, $cookietime);
            param::set_cookie('_userid', $userid, $cookietime);
            param::set_cookie('_username', $username, $cookietime);
            param::set_cookie('_groupid', $groupid, $cookietime);
            param::set_cookie('_nickname', $nickname, $cookietime);
            //param::set_cookie('cookietime', $_cookietime, $cookietime);
            $forward = isset($_POST['forward']) && !empty($_POST['forward']) ? urldecode($_POST['forward']) : 'index.php?m=member&c=index';
            showmessage(L('login_success').$synloginstr, $forward);
        } else {
            $setting = pc_base::load_config('system');
            $forward = isset($_GET['forward']) && trim($_GET['forward']) ? urlencode($_GET['forward']) : '';

            $siteid = isset($_REQUEST['siteid']) && trim($_REQUEST['siteid']) ? intval($_REQUEST['siteid']) : 1;
            $siteinfo = siteinfo($siteid);

            include template('member', 'login');
        }
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
            $friendlist[$k]['avatar'] = $this->client->ps_getavatar($v['userid'], 1);
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
            $friendlist[$k]['avatar'] = $this->client->ps_getavatar($v['cc_userid'], 1);
            $friendlist[$k]['create_time'] = $v['create_time'];
        }
        include template('member', 'concern_me');
    }

    public function concern_delete()
    {
        $this->concern_db->delete(array('id'=>$_GET['id']));
        showmessage('删除成功');
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
            $friendlist[$k]['avatar'] = $this->client->ps_getavatar($v['interview_userid'], 1);
            $friendlist[$k]['create_time'] = $v['create_time'];
            $friendlist[$k]['id'] = $v['id'];
        }
        include template('member', 'interview_count');
    }

    public function interview_delete()
    {
        $this->interview_db->delete(array('id'=>$_GET['id']));
        showmessage('删除成功');
    }

    /**
     * 我的名片
     */
    public function card()
    {
        $sql = 'SELECT M.nickname,D.* FROM v9_member AS M LEFT JOIN v9_member_detail AS D ON M.userid = D.userid WHERE M.userid = '.$this->_userid;        $query = $this->db->query($sql);
        $userInfo = $this->db->fetch_array($query);
        $flag= true;
        foreach ($userInfo[0] as $k => $v){
            if ($k == 'media_name' || $k == 'ifshow') continue;
            if (empty($v)) {
                $flag = false;
                break;
            }
        }
        if (!$flag){
            echo json_encode(['code'=>0]);
        }else{
            echo json_encode(['code'=>1]);
        }
        /*$avatar = get_memberavatar($userInfo[0]['userid'],1);
        if (!$avatar)
            showmessage('请先上传头像', 'index.php?m=member&c=index&a=account_manage_avatar&t=1');
        elseif (empty($userInfo[0]['email']))
            showmessage('请先填写邮箱', 'index.php?m=member&c=index&a=account_change_email&t=1');
        elseif (empty($userInfo[0]['nickname']) || empty($userInfo[0]['unit_industry']) || empty($userInfo[0]['unit_name']) || empty($userInfo[0]['area']))
            showmessage('请先完善资料', 'index.php?m=member&c=index&a=account_manage_info&t=1&type=enable_card');
        else
            include template('member', 'card');*/
    }

    /**
     * 发布内容
     */
    public function publish()
    {
        $sql = 'SELECT M.userid,M.email,M.nickname,D.unit_name,D.unit_industry,D.area,D.media_name FROM v9_member AS M JOIN v9_member_detail AS D ON M.userid = D.userid WHERE M.userid = '.$this->_userid.'
                UNION
                SELECT M.userid,M.email,M.nickname,I.unit_name,I.unit_industry,I.area,I.media_name FROM v9_member AS M JOIN v9_member_inst AS I ON M.userid = I.userid WHERE M.userid = '.$this->_userid;
        $query = $this->db->query($sql);
        $userInfo = $this->db->fetch_array($query);
        //$avatar = get_memberavatar($userInfo[0]['userid'], 1);
        /*if (!$avatar)
            showmessage('请先上传头像', 'index.php?m=member&c=index&a=account_manage_avatar&t=1');
        elseif (empty($userInfo[0]['email']))
            showmessage('请先填写邮箱', 'index.php?m=member&c=index&a=account_change_email&t=1');
        elseif (empty($userInfo[0]['mobile']))
            showmessage('请先填写手机', 'index.php?m=member&c=index&a=account_change_mobile&t=1');
        elseif (empty($userInfo[0]['nickname']) || empty($userInfo[0]['unit_industry']) || empty($userInfo[0]['unit_name']) || empty($userInfo[0]['position']) || empty($userInfo[0]['area']) || empty($userInfo[0]['wechat']) || empty($userInfo[0]['wechat_img']) || empty($userInfo[0]['media_name']))
            showmessage('请先完善资料', 'index.php?m=member&c=index&a=account_manage_info&t=1&type=publish');
        else
            include template('member', 'publish');*/
        if (empty($userInfo[0]['nickname']) || empty($userInfo[0]['unit_industry']) || empty($userInfo[0]['unit_name']) || empty($userInfo[0]['area']) || empty($userInfo[0]['media_name'])){
            echo json_encode(['code'=>0]);
        }else{
            echo json_encode(['code'=>1]);
        }
    }

    /**
     * 站内信
     */
    public function message()
    {
        $t = intval($_GET['t']);
        header("location: index.php?m=message&c=index&a=inbox&t=".$t);
        exit;
    }

    /**
     * 财务管理
     */
    public function finance()
    {
        include template('member', 'finance');
    }

    public function public_get_select()
    {
        $cc_userid = $this->_userid;
        if(!$cc_userid) {
            $res['code'] = 0;
            $res['msg'] = '请先登录';
            echo json_encode($res);exit();
        }
        $where = 'parentid = 0 and style = 0 and keyid = 1';
        $province = $this->linkage_db->select($where);
        $where = 'parentid = 0 and style = 0 and keyid = 3412';
        $int = $this->linkage_db->select($where);
        $this->subscribe_db = pc_base::load_model('subscribe_model');
        $subscribe_list = $this->subscribe_db->select('userid='.$this->_userid);
        foreach ($subscribe_list as $k => $v){
            if ($v['type'] == 3){
                $subscribe_list[$k]['name'] = get_company_field($v['name'], $field = 'shortname');
            }
        }
        echo json_encode(['code'=>1,'province'=>$province,'int'=>$int,'subscribe_list'=>$subscribe_list]);
    }

    /**
     * 不验证用户昵称
     */
    public function public_checknickname_ajax()
    {
        exit('1');
    }

    public function account_change_email() {
        if(isset($_POST['dosubmit'])) {
            $updateinfo = array();
            if(!is_password($_POST['info']['password'])) {
                showmessage(L('password_format_incorrect'), HTTP_REFERER);
            }
            if($this->memberinfo['password'] != password($_POST['info']['password'], $this->memberinfo['encrypt'])) {
                showmessage(L('password_error'), HTTP_REFERER);
            }

            //修改会员邮箱
            if($this->memberinfo['email'] != $_POST['info']['email'] && is_email($_POST['info']['email'])) {
                $email = $_POST['info']['email'];
                $updateinfo['email'] = $_POST['info']['email'];
            } else {
                $email = '';
            }
            $this->db->update($updateinfo, array('userid'=>$this->memberinfo['userid']));
            if(pc_base::load_config('system', 'phpsso')) {
                //初始化phpsso
                $this->_init_phpsso();
                $res = $this->client->ps_member_edit('', $email, $_POST['info']['password'], '', $this->memberinfo['phpssouid'], '');
                $message_error = array('-1'=>L('user_not_exist'), '-2'=>L('old_password_incorrect'), '-3'=>L('email_already_exist'), '-4'=>L('email_error'), '-5'=>L('param_error'));
                if ($res < 0) showmessage($message_error[$res]);
            }

            showmessage(L('operation_success'), HTTP_REFERER);
        } else {
            $show_validator = true;
            $memberinfo = $this->memberinfo;

            include template('member', 'account_manage_email');
        }
    }

    public function save_tweets()
    {
        $model = pc_base::load_model('tweets_model');
        //$html = file_get_contents($_POST['url']);
        //preg_match('/<title>(.*)<\/title>/Ui', $html, $title);
        if (empty($_POST['title']) || empty($_POST['url'])){
            showmessage('请输入正确文章地址与标题');
        }
        $data['url'] = trim($_POST['url']);
        $data['title'] = trim($_POST['title']);
        $data['username'] = $this->_username;
        $data['catid'] = 546;
        $id = $model->insert($data,true);
        $info = $model->get_one(array('id'=>$id));
        showmessage('制作成功','index.php?m=content&c=index&a=show_tweets');
    }

    public function my_tweets()
    {
        $model = pc_base::load_model('tweets_model');
        $memberinfo = $this->memberinfo;
        $phpsso_api_url = $this->_init_phpsso();
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $lists = $model->listinfo(array('username'=>$this->_username), '', $page, 10);
        $pages = $model->pages;
        include template('member', 'my_tweets');
    }

    public function delete_tweets()
    {
        $id = intval($_GET['id']);
        $model = pc_base::load_model('tweets_model');
        $info = $model->delete(array('id'=>$id));
        showmessage('删除成功','index.php?m=member&c=index&a=my_tweets&t=2');
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