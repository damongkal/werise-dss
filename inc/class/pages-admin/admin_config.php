<?php
define('_CURRENT_OPT','Administration &raquo; Preferences');

class admin_config
{
    public function getSysopts()
    {
        list($default,$sysopt) = sysoptions::getInstance()->getAll();
        $tmp = array();
        foreach($default as $key => $val)
        {
            // get db value
            $db = null;
            if (isset($sysopt[$key]))
            {
                $db = $sysopt[$key];
            }

            $tmp[] = array(
                'key' => $key,
                'desc' => $val['desc'],
                'type' => $val['type'],
                'db' => $db,
                'default' => $val['value']);
        }
        return $tmp;
    }

    public function getEnv()
    {
        $env[] = array(
            'desc' => 'Weather Data folder',
            'value' => _DATA_DIR . str_replace('forecast/','',werise_weather_file::getFolder('f'))
        );
        $env[] = array(
            'desc' => 'ORYZA2000 folder',
            'value' => _DATA_SUBDIR_ORYZA
        );
        $env[] = array(
            'desc' => 'CDFDM output folder',
            'value' => _CDFDM_DIR
        );        
        $env[] = array(
            'desc' => 'SINTEX-F conversion folder',
            'value' => _DATA_SUBDIR_SINTEX
        );
        return $env;
    }

    public function showValue($rec)
    {
        $val = $rec['default'];
        if (!is_null($rec['db']))
        {
            $val = $rec['db'];
        }
        $txtitem = '<input id="%s" name="%s" type="text" class="form-control form-control-sm" value="%s" />';
        $item = sprintf($txtitem,$rec['key'],$rec['key'],$val);
        if ($rec['type']==='boolean')
        {
            $item = $this->boolSelect($rec['key'], $val);
        }
        return $item;
    }

    private function boolSelect($key,$value)
    {
        $selected[0] = 'selected="selected"';
        $selected[1] = '';
        if ($value===true)
        {
            $selected[0] = '';
            $selected[1] = 'selected="selected"';
        }
        $tmp = '<select id="%s" name="%s" class="form-control form-control-sm" style="width:100px"><option value="0" %s >NO</option><option value="1" %s>YES</option></select>';
        return sprintf($tmp,$key,$key,$selected[0],$selected[1]);
    }
}