<?php
abstract class werise_sintex_base
{
    protected $generate_year = 0;
    protected $country;
    protected $station_id;
    
    public function initArgs()
    {
        $this->generate_year = 0;
        $this->country = '';
        $this->station_id = 0;
    }
    
}