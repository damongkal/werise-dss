<?php
define('_CURRENT_OPT',_t('Account Registration'));
class form_register
{
    public $user_record = '';
    public $is_submit = false;
    public $is_register_done = false;
    
    public function __construct() {

        $this->user_record = new werise_users_record;        
        
        // do not allow re-submit
        if (isset($_SESSION['register-submit'])) {
            $this->is_register_done = true;
            return;
        }

        if (isset($_POST['submit-register'])) {
            $this->is_submit = true;

            if ($this->isFormValid()) {
                $this->createUser();
                $_SESSION['register-submit'] = 1;
            }
        }
    }

    private function isFormValid()
    {
        $validate = array();
        $this->user_record->username = validate::getPostValue('username');
        $validate[] = array($this->user_record->username,array('required'=>1,'min'=>4,'max'=>20));
        
        $this->user_record->password = validate::getPostValue('password');
        $validate[] = array($this->user_record->password,array('required'=>1,'min'=>8,'max'=>20));
        
        $password2 = validate::getPostValue('password2');
        if ($this->user_record->password !== $password2)
        {
            return false;
        }
        
        $this->user_record->email = validate::getPostValue('email');
        $validate[] = array($this->user_record->email,array('required'=>1));
        
        $this->user_record->fullname = validate::getPostValue('fullname');
        $validate[] = array($this->user_record->fullname,array('required'=>1,'min'=>5,'max'=>255));
        
        $this->user_record->phone = validate::getPostValue('phone');
        $this->user_record->address = validate::getPostValue('address');
        
        $this->user_record->reason = validate::getPostValue('reason');
        $validate[] = array($this->user_record->reason,array('required'=>1,'min'=>1));
        
        return validate::validateItem($validate);
    }
    
    private function createUser() {        
        $users = new werise_users_model;
        $userid = $users->create($this->user_record);
        $users->createReason($userid,$this->user_record);
    }

}