<?php
// +----------------------------------------------------------------------
// | Author: 邓智康 <821563034@qq.com>
// +----------------------------------------------------------------------

class upload
{
    protected $uid;

    protected $avatar_data;

    protected $db;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->db = pc_base::load_model('member_model');
    }
    /**
     *  上传头像处理重写
     *  传入头像压缩包，解压到指定文件夹后删除非图片文件
     */
    public function upload_avatar() {
        //根据用户id创建文件夹
        if(isset($_POST['uid']) && isset($_POST['avatar_data'])) {
            $this->uid = intval($_POST['uid']);
            $this->avatar_data = $_POST['avatar_data'];
        } else {
            exit('0');
        }

        $dir1 = ceil($this->uid / 10000);
        $dir2 = ceil($this->uid % 10000 / 1000);

        //创建图片存储文件夹
        $avatarfile = pc_base::load_config('system', 'upload_path').'avatar/';
        $dir = $avatarfile.$dir1.'/'.$dir2.'/'.$this->uid.'/';
        if(!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        //存储flashpost图片
        $filename = $dir.'180x180.jpg';

        preg_match('/^(data:\s*image\/(\w+);base64,)/', $this->avatar_data, $result);
        $base64 = str_replace($result[1], '', $this->avatar_data);
        $img = base64_decode($base64);
        file_put_contents($filename, $img);

        $avatararr = array('180x180.jpg', '30x30.jpg', '45x45.jpg', '90x90.jpg');
        $files = glob($dir."*");
        foreach($files as $_files) {
            if(is_dir($_files)) dir_delete($_files);
            if(!in_array(basename($_files), $avatararr)) @unlink($_files);
        }
        if($handle = opendir($dir)) {
            while(false !== ($file = readdir($handle))) {
                if($file !== '.' && $file !== '..') {
                    if(!in_array($file, $avatararr)) {
                        @unlink($dir.$file);
                    } else {
                        $info = @getimagesize($dir.$file);
                        if(!$info || $info[2] !=2) {
                            @unlink($dir.$file);
                        }
                    }
                }
            }
            closedir($handle);
        }

        pc_base::load_sys_class('image','','0');
        $image = new image(1,0);
        $image->thumb($filename, $dir.'30x30.jpg', 30, 30);
        $image->thumb($filename, $dir.'45x45.jpg', 45, 45);
        $image->thumb($filename, $dir.'90x90.jpg', 90, 90);

        $this->db->update(array('avatar'=>1), array('uid'=>$this->uid));
        exit('1');
    }
}