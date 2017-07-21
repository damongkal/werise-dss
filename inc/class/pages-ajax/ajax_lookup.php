<?php

class ajax_lookup extends ajax_base{

    protected function actionStation() {
        $cls = new weather_stations;
        return $cls->getStations($this->getArg('country', 'ID'), $this->getArg('ctype', 'w'));
    }
    
    protected function actionWvar() {
        return weather_data::getWvarList(
                        $this->getArg('country', 'ID'), $this->getArg('station', '0'), $this->getArg('year', '0'), $this->getArg('wtype', 'r'));
    }    

    protected function actionStationyear() {
        $cls = new weather_stations;
        $show_historical = _opt(sysoptions::_SHOW_HISTORICAL);        
        return $cls->getStationYears(
                        $this->getArg('country', 'ID'), $this->getArg('station', '0'), $this->getArg('dbsource', 'w'), $show_historical);
    }        

    protected function actionCropyear() {
        $cls = new crop_data;
        return $cls->getCropYears();
    }    

    protected function actionVarieties() {
        $cls = new oryza_data;
        return $cls->getVarieties(
            $this->getArg('country', 'ID'), 
            $this->getArg('station', '0'), 
            $this->getArg('year', '0'),
            $this->getArg('wtype', 'r'));
    }

    protected function actionLangdata() {
        return language::getInstance()->jstranslate();
    }
}
