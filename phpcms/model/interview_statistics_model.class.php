<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_sys_class('model', '', 0);
class interview_statistics_model extends model {
	function __construct() {
		$this->db_config = pc_base::load_config('database');
		$this->db_setting = 'default';
		$this->table_name = 'interview_statistics';
		$this->_username = param::get_cookie('_username');
		$this->_userid = param::get_cookie('_userid');
		parent::__construct();
	}

    public function add($userid, $interview_userid)
    {
        $data = array();
        $data['userid'] = $userid;
        $data['interview_userid'] = ($interview_userid ? $interview_userid : 0);
        $data['create_time'] = SYS_TIME;

        $insert_id = $this->insert($data, true);
        if (!$insert_id) {
            return FALSE;
        } else {
            return true;
        }
    }
}
?>