<?php
define('_CURRENT_OPT',_t('Crop Advisory'));
class form_oryza
{    
    public function __construct() {
        $this->postvars = json_encode(false);
        if (isset($_POST['show']))
        {
            $this->postvars = json_encode($_POST);
        }
    }
    
    public function getFertOpts()
    {
        // $opts[] = array('',_t('Fertilizer Application').' &raquo;');
        $fert = advisory_fertilizer::_FERT_NONE;
        $opts[] = array($fert,werise_oryza_fertilizer::getTypeDesc($fert));
        if (_opt(sysoptions::_ORYZACHART_SHOW_GENFERT))
        {
            $fert = advisory_fertilizer::_FERT_GEN;
            $opts[] = array($fert,werise_oryza_fertilizer::getTypeDesc($fert));
        }
        if (_opt(sysoptions::_ORYZACHART_SHOW_RCMFERT))
        {
            $fert = advisory_fertilizer::_FERT_SPC;
            $opts[] = array($fert,werise_oryza_fertilizer::getTypeDesc($fert));
        }
        return $opts;
    }    
    
    public function getMonths()
    {
        $opts[] = array('',_t('Month').' &raquo;');
        for($i=1;$i<=12;$i++)
        {
            $datec = mktime(0,0,0,$i,1,2000);
            $opts[] = array(date('n',$datec),_t(date('F',$datec)));
        }
        return $opts;
    }
    
    public function getSowdates()
    {
        $opts[] = array('',_t('Sow Date').' &raquo;');
        for($i=1;$i<=12;$i++)
        {
            $datec1 = mktime(0,0,0,$i,1,2000);
            $opts[] = array(date('m-d',$datec1),_t(date('M-d',$datec1)));
            $datec2 = mktime(0,0,0,$i,15,2000);
            $opts[] = array(date('m-d',$datec2),_t(date('M-d',$datec2)));
        }
        return $opts;
    }    
    
}