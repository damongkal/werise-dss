<?php
class ajax_index extends ajax_base
{    
    protected function actionDefault() {        
    }
    
    protected function actionLogin()
    {
        if (isset($_GET['username'])) {
            $username = $_GET['username'];
        }        
        if (isset($_GET['password'])) {
            $password = $_GET['password'];
        }                
        $rs = dss_auth::checkAccessDb($username,$password);
        if (dss_auth::getUsername()==='')
        {
            return false;
        }
        return 'success';
    }    
}