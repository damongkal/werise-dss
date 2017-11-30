<?php

class ajax_lookup extends ajax_base
{

    protected function actionStation()
    {
        $cls = new weather_stations;
        return $cls->getStations($this->getArg('country', 'ID'), $this->getArg('ctype', 'w'));
    }

    protected function actionWvar()
    {
        return weather_data::getWvarList(
                $this->getArg('country', 'ID'), $this->getArg('station', '0'), $this->getArg('year', '0'), $this->getArg('wtype', 'r'));
    }

    protected function actionStationyear()
    {
        $cls = new weather_stations;
        $show_historical = _opt(sysoptions::_SHOW_HISTORICAL);
        return $cls->getStationYears(
                $this->getArg('country', 'ID'), $this->getArg('station', '0'), $this->getArg('dbsource', 'w'), $show_historical);
    }

    protected function actionCropyear()
    {
        $cls = new crop_data;
        return $cls->getCropYears();
    }

    protected function actionVarieties()
    {
        $cls = new oryza_data;
        $rs = $cls->getVarieties(
            $this->getArg('country', 'ID'), $this->getArg('station', '0'), $this->getArg('year', '0'), $this->getArg('wtype', 'r'));
        $rs2 = array();
        if ($this->getArg('data-all', '0') === '0') {
            foreach ($rs as $rec) {
                $rs2[] = array($rec->variety, strtoupper($rec->variety_name));
            }
        } else {
            foreach ($rs as $rec) {
                $rec_tmp = new werise_varieties_record;
                $rec_tmp->loadRecord($rec);
                $rec->dds_depth = $rec_tmp->getDdsDepth();
                $rec->dds_volume = $rec_tmp->getDdsVolume();
                $rec->tp_depth = $rec_tmp->getTpDepth();
                $rec->tp_volume = $rec_tmp->getTpVolume();
                $rs2[] = $rec;
            }
        }
        return $rs2;
    }

    protected function actionLangdata()
    {
        return language::getInstance()->jstranslate();
    }
}
