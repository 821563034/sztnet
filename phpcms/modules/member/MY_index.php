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
}