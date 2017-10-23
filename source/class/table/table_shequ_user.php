<?php
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class table_shequ_user extends discuz_table
{
    public function __construct() {

        $this->_table = 'shequ_user';
        $this->_pk    = 'id';
        parent::__construct();
    }
    public function insert($uid, $username,$ava){
        $arr = array(
                                'uid' => $uid,
                                'username' => $username,
                                'ava' => $ava,
                );
                $id = DB::insert('shequ_user', $arr);
    }
    public function fetch_friends_user($usernames) {
        $users = array();
        if(!empty($usernames)) {
            $users = DB::fetch_all('SELECT uid,username FROM %t WHERE username IN (%n)', array($this->_table, (array)$usernames));

        }
        return $users;
    }

    public function fetch_friends_ids($usernames) {
        $uids_all = array();
        if($usernames) {
            foreach($this->fetch_friends_user($usernames) as $k =>$v) {
                $uids_all[] = $v['uid'];
            }
        }
        return $uids_all;
    }
    public function update_ava($uid,$ava){
        DB::update('shequ_user', array('ava'=>$ava),array('uid'=> $uid));
    }
     public function fetch_all_friends_name($uids){
        $users = array();
        if(!empty($uids)) {
            $users = DB::fetch_all('SELECT uid,username FROM %t WHERE uid IN (%n)', array($this->_table, (array)$uids));

        }
        return $users;
    }

}