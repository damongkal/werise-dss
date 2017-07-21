<?php

class ajax_config extends ajax_base{

    protected function actionUpdate() {
        if (!isset($_GET['key']) || !isset($_GET['val']))
        {
            return 'invalid values';
        }
        sysoptions::update($_GET['key'], $_GET['val']);
        return 'success';
    }

}
