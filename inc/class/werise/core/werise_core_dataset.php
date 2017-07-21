<?php
class werise_core_dataset
{
    private $station = null;
    private $year = null;
    private $wtype = null;
    public function setStation($station)
    {
        $this->station = $station;
    }
    public function setYear($year)
    {
        $this->year = intval($year);
        if($this->year < 1970 || $this->year > 2030)
        {
            throw new Exception("Year out of range: {$this->year}");
        }
    }
    public function getYear()
    {
        return intval($this->year);
    }

    public function setWType($wtype)
    {
        $this->wtype = $wtype;
        if ($this->wtype!=werise_weather_properties::_REALTIME && $this->wtype!=werise_weather_properties::_FORECAST)
        {
            throw new Exception("invalid value for wType: {$this->wtype}");
        }
    }

    public function getWType()
    {
        return $this->wtype;
    }

    public function getStation()
    {
        return $this->station;
    }
    public function getCountryCode()
    {
        return $this->station->country_code;
    }
    public function getStationId()
    {
        return intval($this->station->station_id);
    }
}