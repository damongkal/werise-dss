<?php

class werise_varieties_record
{

    public $id = null;
    public $code = '';
    public $name = '';
    public $maturity_min = 0;
    public $maturity_max = 0;
    public $yield_avg = null;
    public $yield_potential = null;
    
    private $dds_depth;
    private $dds_volume;
    private $tp_depth;
    private $tp_volume;
    
    public function loadRecord($dbrecord) {
        $this->id = intval($dbrecord->variety_id);
        $this->code = $dbrecord->variety_code;
        $this->name = $dbrecord->variety_name;
        $this->maturity_min = intval($dbrecord->maturity_min);
        $this->maturity_max = intval($dbrecord->maturity_max);
        $this->yield_avg = floatval($dbrecord->yield_avg);
        $this->yield_potential = floatval($dbrecord->yield_potential);
        
        $avg_dth = ($this->maturity_min + $this->maturity_max) / 2;
        $grow_month = $avg_dth / 30;
        $this->dds_depth = intval($grow_month * 180);        
        $this->tp_depth = $this->dds_depth + 40 + 200;        
        $this->dds_volume = $this->dds_depth / 10 * 10000 * 1000 / 1000000;
        $this->tp_volume =$this->tp_depth / 10 * 10000 * 1000 / 1000000;
    }
    
    public function getDdsDepth() {
        return $this->dds_depth;
    }
    public function getDdsVolume() {
        return $this->dds_volume;
    }
    public function getTpDepth() {
        return $this->tp_depth;
    }
    public function getTpVolume() {
        return $this->tp_volume;
    }    

}
