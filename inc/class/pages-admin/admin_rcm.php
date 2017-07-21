<?php
define('_CURRENT_OPT','Administration &raquo; Fertilizer Application Reference');

class admin_rcm
{    
    public function getAll($ftype)
    {
        $cls = new fertilizer_application;
        return $cls->getAll($ftype);
    }
    
    public function formatNPK($rec,$stage)
    {
        if ($stage === 1)
        {
            $n = intval($rec->n1);
            $p = intval($rec->p1);
            $k = intval($rec->k1);
            $dat = intval($rec->n1day);
        }
        if ($stage === 2)
        {
            $n = intval($rec->n2);
            $p = intval($rec->p2);
            $k = intval($rec->k2);
            $dat = intval($rec->n2day);
        }
        if ($stage === 3)
        {
            $n = intval($rec->n3);
            $p = intval($rec->p3);
            $k = intval($rec->k3);
            $dat = intval($rec->n3day);
        }        
        return $n . '-' . $p . '-' . $k . ' on dat ' . $dat;
    }
}