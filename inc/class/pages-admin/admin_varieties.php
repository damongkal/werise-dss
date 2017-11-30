<?php
define('_CURRENT_OPT', 'Administration &raquo; Rice Varieties');

class admin_varieties
{

    public function getAll()
    {
        $cls = new werise_varieties_model;
        return $cls->getRecords();
    }
}
