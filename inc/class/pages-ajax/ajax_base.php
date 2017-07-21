<?php

class ajax_base {

    public $json_ret;
    protected $debug;
    
    public function __construct() {
        $this->debug = debug::getInstance();
        $action = 'default';
        if (isset($_GET['action'])) {
            $action = $_GET['action'];
        }
        $this->json_ret = $this->exec($action);
    }

    protected function exec($action) {
        if ($action != '') {
            try {
                $action = 'action' . ucfirst($action);
                return $this->$action();
            } catch (Exception $e) {
                return $e->getMessage();
            } catch (ErrorException $e) {
                return $e->getMessage();
            }
        }
        return false;
    }
    
    protected function getArg($varname, $default) {
        $tmp = $default;
        if (isset($_GET[$varname])) {
            $tmp = $_GET[$varname];
        }
        return $tmp;
    }    
}
