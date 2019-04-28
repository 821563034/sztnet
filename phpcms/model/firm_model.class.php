<?php
// +----------------------------------------------------------------------
// | Author: 邓智康 <821563034@qq.com>
// +----------------------------------------------------------------------
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_sys_class('model', '', 0);
class firm_model extends model
{
    public function __construct() {
        $this->db_config = pc_base::load_config('database');
        $this->db_setting = 'default';
        $this->table_name = 'firm';
        parent::__construct();
    }
}