<?php
class MY_index extends index
{
    private $db;

    function __construct()
    {
        parent::__construct();
    }

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
}