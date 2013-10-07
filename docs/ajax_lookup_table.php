<?php
include_once('bootstrap.php');

class lookup_table
{
    public function exec()
    {
        $action = $this->getArg('action', '');
        if ($action!='')
        {
            try
            {
                $action = 'get'.ucfirst($action);
                $ret = $this->$action();
            } catch (Exception $e)
            {
                return false;
            } catch (ErrorException $e)
            {
                return false;
            }
            return $ret;
        }

        return false;
    }

    private function getStation()
    {
        $cls = new weather_stations;
        return $cls->getStations($this->getArg('country', 'ID'),$this->getArg('ctype', 'w'));
    }

    public function getStationyear()
    {
        $cls = new weather_stations;
        return $cls->getStationYears(
            $this->getArg('country', 'ID'),
            $this->getArg('station', '0'),
            $this->getArg('dbsource', 'w'));
    }
    
    public function getCropyear()
    {
        $cls = new crop_data;
        return $cls->getCropYears();
    }    

    public function getVarieties()
    {
        $cls = new oryza_data;
        return $cls->getVarieties(
            $this->getArg('country', 'ID'),
            $this->getArg('station', '0'),
            $this->getArg('year', '0'));
    }

    private function getArg($varname, $default)
    {
        $tmp = $default;
        if (isset($_GET[$varname]))
        {
            $tmp = $_GET[$varname];
        }
        return $tmp;
    }
}
if (isset($_GET['action']))
{
    $cls = new lookup_table;
    $ret = $cls->exec();
    echo json_encode($ret);
}