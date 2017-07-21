<?php

class werise_sintex_compute {

    // output variables
    public $raw;
    // computation proof
    private $phi_constant;
    private $phi;
    private $sin_phi;
    private $cos_phi;
    private $tan_phi;
    private $t1;
    private $t2;
    private $t3;
    
    private $debug;    

    public function __construct() {
        $this->debug = debug::getInstance();
    }

    /**
     * index description
     * pr : precipitation
     * ws : wind speed
     * tn : min temperature
     * tx : max temperature
     * tmin : vapor pressure
     * rad : radiation
     */
    public function execute($raw, $country) {
        $this->raw = $raw;
        $this->setSpecialValues($country);
        foreach ($this->raw as $key => $rec) {
            // computations
            $tmin = $this->computeT($rec[werise_cdfdm_file::_TYPE_TN]);
            $tmax = $this->computeT($rec[werise_cdfdm_file::_TYPE_TX]);
            list($dr, $delta) = $this->computeDelta($rec[werise_cdfdm_file::_COL_DOY]);
            $omega = $this->computeOmega($delta);
            $sro2 = $this->computeSro($dr, $omega, $delta);
            $rad = $this->computeSp($rec[werise_cdfdm_file::_TYPE_TN], $rec[werise_cdfdm_file::_TYPE_TX], $sro2);
            // save to raw
            $this->raw[$key]['tmin'] = $tmin;
            $this->raw[$key]['tmax'] = $tmax;
            $this->raw[$key]['dr'] = $dr;
            $this->raw[$key]['delta'] = $delta;
            $this->raw[$key]['omega'] = $omega;
            $this->raw[$key]['sro'] = $sro2;
            $this->raw[$key]['rad'] = $rad;
        }
    }
    
    private function setSpecialValues($country) {
        if ($country === 'ID') {
            $this->t1 = 1.249058;
            $this->t2 = 0.172107;
            $this->t3 = 0.4986832;
            $phi_const = -6.2;
        }
        if ($country === 'LA') {
            $this->t1 = 0.5123898;
            $this->t2 = 0.03178211;
            $this->t3 = 2.047023;
            $phi_const = 18.5;
        }
        if ($country === 'PH') {
            $this->t1 = 0.8305413;
            $this->t2 = 0.07356764;
            $this->t3 = 1.14796;
            $phi_const = 14;
        }
        $phi = ($phi_const * pi()) / 180;

        $this->phi_constant = $phi_const;
        $this->phi = $phi;
        $this->sin_phi = sin($phi);
        $this->cos_phi = cos($phi);
        $this->tan_phi = tan($phi);
    }    

    private function computeT($t) {
        if (is_null($t)) {
            return null;
        }

        $val = (0.6108) * exp((17.27) * ($t) / ($t + 237.3));
        return $val;
    }

    private function computeDelta($doy) {
        $tmp = 2 * pi() * $doy / 365;
        $dr = 1 + ((0.033) * cos($tmp));
        $delta = (0.409) * sin($tmp - 1.39);
        return array($dr, $delta);
    }

    private function computeOmega($delta) {
        return acos(-$this->tan_phi * tan($delta));
    }

    private function computeSro($dr, $omega, $delta) {
        $a = ($omega * $this->sin_phi * sin($delta));
        $b = ($this->cos_phi * cos($delta) * sin($omega));
        return (24 * 60 / pi()) * 0.082 * $dr * ($a + $b);
    }

    private function computeSp($tn, $tx, $sro) {
        if (is_null($tn)) {
            return null;
        }

        $pow = pow($tx - $tn, $this->t3);
        return (1000 * $sro * $this->t1 * (1 - exp(-$this->t2 * ($pow))));
    }
    
    public function getSpecialValues()
    {
        return array(
            'phi_const' => $this->phi_constant,
            'phi' => $this->phi,
            'sin_phi' => $this->sin_phi,
            'cos_phi' => $this->cos_phi,
            'tan_phi' => $this->tan_phi,
            't1' => $this->t1,
            't2' => $this->t2,
            't3' => $this->t3);
    }    

}
