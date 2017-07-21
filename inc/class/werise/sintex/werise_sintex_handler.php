<?php
class werise_sintex_handler
{
    // classes
    private $debug;
    public $cdfdm;
    public $sintex_compute;
    public $sintex_oryza;
    // computation proof
    private $csv;
    private $files_compute;    

    public function __construct()
    {
        $this->debug = debug::getInstance();
    }

    public function export($country_code, $region_id, $station_id, $year, $overwrite_file, $wtype)
    {
        // process the SINTEX files
        $this->cdfdm = new werise_cdfdm_outfile;
        $this->cdfdm->loadFiles($country_code, $region_id, $year);

        // compute for ORYZA2000 converted format
        $this->sintex_compute = new werise_sintex_compute;
        $this->sintex_compute->execute($this->cdfdm->raw,$country_code);

        // convert to ORYZA2000 files
        $this->sintex_oryza = new werise_sintex_oryza;
        $this->sintex_oryza->createFiles($country_code, $station_id, $this->sintex_compute->raw, $overwrite_file, $wtype);
        
        $this->saveComputation($country_code, $region_id, $station_id, $this->sintex_compute->raw);
    }
    
    private function saveComputation($country, $region_id, $station_id, $sintex_raw)
    {                
        $this->csv = array();
        
        // separate by year
        foreach($sintex_raw as $date => $comp)
        {
            $year = $comp['yr'];
            $date = $year."-".str_pad($comp['mn'], 2, "0", STR_PAD_LEFT) . "-" . str_pad($comp['dy'], 2, "0", STR_PAD_LEFT);
            $comp2 = $comp;
            $comp2['date'] = $date;
            unset($comp2['tmax']);
            $this->csv[$year][$comp['doy']] = $comp2;
        }
        
        // column headers
        $headers = array('date','doy','pr', 'tn', 'tx', 'ws', 'tmin', 'tmax', 'dr', 'delta', 'omega', 'sro', 'rad');
        
        // create csv per year
        foreach($this->csv as $year => $year_csv)
        {
            $dir = werise_cdfdm_folder::getFolder($country, $region_id, werise_cdfdm_folder::_SRC_OUT).DIRECTORY_SEPARATOR;
            $filename = werise_stations_country::getFile($country);
            $f = "{$dir}debug_prn_{$filename}{$station_id}_{$year}.csv";
            $this->files_compute[$year] = $f;            
            $h = werise_core_files::getHandle($f,'w');
            fputcsv($h, $headers);
            foreach($year_csv as $comp2)
            {
                fputcsv($h, $comp2);
            }
            fclose($h);
        }
    }
    
    public function getCsv()
    {
        return $this->csv;
    }    
    
    public function getFilesCompute()
    {
        return $this->files_compute;
    }    

}