<?php
include_once('bootstrap.php');
admin_auth();
define('_INIT','admin');
include('layout_header.php'); 
?>

<div id ="dataselection">
    <span style="font-weight: 700">Administration » Weather Data File</span><br />
    <input type="checkbox" name="show_only_loaded" id="show_only_loaded" value="1" /> Show only loaded files?
</div>

<?php
class admin_weather_file
{
    private $db;    
    
    private $cls;
    private $cls2;
    private $cls3;
    
    /**
     * @todo : documentation
     */
    public function __construct()
    {
        $this->db = Database_MySQL::getInstance();
        
        $this->cls = new weather_data;
        $this->cls2 = new oryza_data;    
        $this->cls3 = new datafiles;
    }

    /**
     * display the list of files available 
     */
    public function actionList()
    {
        $files_r = $this->getWeatherFiles(weather_data::_REALTIME);
        $files_f = $this->getWeatherFiles(weather_data::_FORECAST);
        return array($files_r,$files_f);
    }
    
    /**
     * load a dataset 
     */
    public function actionLoad()
    {
        $ret = false;
        if (isset($_GET['loadto']))
        {
            $ret = $this->loadFiletoDatabase($_GET['prnfile'], $_GET['loadto']);
            // data load results
            if ($ret[0])
            {
                $tmp = print_r($ret[1],true);
                $ret = '<p class="alert alert-success" style="display:block;">dataset was loaded to database successfully.</p>';
                $ret .= '<pre style="width:400px">';
                $ret .= $tmp;
                $ret .= '</pre>';
                $ret .= '<h3><a href="admin_weather_file.php">Back to Directory List</a></h3>';
            }
            else
            {
                $ret = '<p class="alert alert-error" style="display:block"><b>ERROR</b> : ' . $ret[1] . '</p>';
            }
        }        
        return $ret;
    }
    
    /**
     * delete a dataset
     */
    public function actionDelete()
    {
        if (isset($_GET['delete']))
        {
            $this->deleteDataset($_GET['prnfile'], $_GET['delete']);
            return '<p class="alert alert-success" style="display:block">dataset was deleted successfully</p>';
        }        
    }

    public function actionOryzaError()
    {
        $ret ='<pre>';
        $ret .= oryza2000_api::getModelOutput();
        $ret .= '</pre>';
        return $ret;
    }
    
    /**
     * @todo : documentation
     * @param type $wtype
     * @return boolean 
     */
    public function getWeatherFiles($wtype)
    {
        $files = $this->cls->getAvailableFiles($wtype);
        $w_sets = $this->getAllDatasets($wtype,'w');
        $o_sets = $this->getAllDatasets($wtype,'o');
        
        if ($files)
        {
            foreach ($files as $key => $file)
            {
                $arr = $this->cls3->getDatasetFromFilename($file['file']);
                $arr['subdir'] = $file['subdir'];
                $arr['is_loaded'] = false;
                if (isset($w_sets[$arr['file']]))
                {
                    $arr['is_loaded'] = $w_sets[$arr['file']]['rec'];
                    $w_sets[$arr['file']]['file_exist'] = true;
                }
                $arr['is_oryza_loaded'] = false;
                if (isset($o_sets[$arr['file']]))
                {
                    $arr['is_oryza_loaded'] = $o_sets[$arr['file']]['rec'];
                    $o_sets[$arr['file']]['file_exist'] = true;
                }                
                $files[$key] = $arr;
            }
        }    
        return $files;
    }    
    
    /**
     * @todo : documentation
     * @param type $prnfile
     * @param type $loadto
     * @return type 
     */
    public function loadFiletoDatabase($prnfile, $loadto)
    {
        $this->deleteDataset($prnfile, $loadto);
        
        // realtime or forecast
        $wtype = 'r';
        if (isset($_GET['type']))
        {
            $wtype = $_GET['type'];
        }
        
        // lets load!!!
        if ($loadto=='w')
        {
            $ret = $this->cls->load($prnfile,$wtype);
        } else
        {
            $ret = $this->cls2->load($prnfile,$wtype);
            if (!$ret[0])
            {
                $ret[1] .= ' Click <a href="admin_weather_file.php?action=oryza_err">here</a> for details.';
            }
        }  
        
        return $ret;
    }
    
    /**
     * @todo : documentation
     * @param type $prnfile
     * @param type $dbtable 
     */
    public function deleteDataset($prnfile, $dbtable)
    {
        // realtime or forecast
        $wtype = 'r';
        if (isset($_GET['type']))
        {
            $wtype = $_GET['type'];
        }
        $setids = array(0);        
        $dataset = $this->cls3->getDatasetFromFilename($prnfile);        
        
        if ($dbtable=='o')
        {
            $dbtable2 = 'oryza_dataset';
            $dbtable3 = 'oryza_data';
            $dbtable4 = 'oryza_datares';
            
            $sql = "
                SELECT `id`
                FROM `{$dbtable2}`
                WHERE `country_code` = '{$dataset['country']}'
                    AND `station_id` = {$dataset['station']}
                    AND `year` = {$dataset['year']}
                    AND `wtype` = '{$wtype}'";
            $rs = $this->db->getRowList($sql);
            if ($rs)
            {
                foreach ($rs as $rec)
                {
                    $setids[] = $rec->id;
                }
            }
        }

        if ($dbtable=='w')
        {
            $dbtable2 = 'weather_dataset';
            $dbtable3 = 'weather_data';  
            $dbtable4 = '';
            
            // setid
            if (isset($_GET['setid']))
            {
                $setids[] = $_GET['setid'];
            }            
            
            // delete cache
            $cache_id = 'ptile-'.$dataset['country'].'-'.$dataset['station'].'-'.$wtype;        
            $sql1 = "DELETE FROM `cache` WHERE `keyid` LIKE '{$cache_id}%'";
            $this->db->query($sql1);
        }
        
        foreach($setids as $setid)
        {
            $sql2 = "DELETE FROM `{$dbtable2}` WHERE `id` = {$setid}";
            $this->db->query($sql2);

            $sql3 = "DELETE FROM `{$dbtable3}` WHERE `dataset_id` = {$setid}";
            $this->db->query($sql3);
            
            if ($dbtable4!='')
            {
                $sql4 = "DELETE FROM `{$dbtable4}` WHERE `dataset_id` = {$setid}";
                $this->db->query($sql4);                
            }
        }
    }
    
    /**
     * @todo : documentation
     * @global type $weather_files
     * @param type $wtype
     * @param type $source
     * @return boolean 
     */
    private function getAllDatasets($wtype, $source)
    {
        global $weather_files;
        
        $dbtable = 'weather_dataset';
        if ($source=='o')
        {
            $dbtable = 'oryza_dataset';
        }
                
        $sql = "SELECT * FROM `{$dbtable}` WHERE wtype='{$wtype}'";
        $rs = $this->db->getRowList($sql);
        $rs2 = array();
        foreach ($rs as $rec)
        {
            $country = $rec->country_code;
            if (isset($weather_files[$rec->country_code]['file']))
            {
                $country = $weather_files[$rec->country_code]['file'];
            }
            $year = $rec->year;
            if ($year>=2000)
            {
                $year = $year - 2000;
            } else
            {
                $year = $year - 1000;
            }
            
            $key = $country.$rec->station_id.".".str_pad($year,3,'0', STR_PAD_LEFT);
            $rs2[$key] = array('rec'=>$rec,'file_exist'=>false);
        }
        return $rs2;
    }    
    
    /**
     * @todo : documentation
     * @global type $weather_files
     * @param type $id
     * @return type 
     */
    public function getCountryName($id)
    {
        global $weather_files;
        
        if (isset($weather_files[$id]['country']))
        {
            return $weather_files[$id]['country'];
        }
        return $id;
    }
    
    /**
     * @todo : documentation
     * @param type $country
     * @param type $station
     * @return type 
     */
    public function getStationName($country,$station)
    {
        $sql = "SELECT `station_name` FROM weather_stations WHERE country_code='{$country}' AND station_id='$station'";
        $rs = $this->db->getRow($sql);
        if ($rs)
        {
            return $rs->station_name;
        }
        return $station;
    }    
}

$loader = new admin_weather_file;

// requested action
$action = 'list';
if (isset($_GET['action']))
{
    $action = $_GET['action'];
}

switch($action)
{
    case 'list':
        list($files_r,$files_f) = $loader->actionList();    
    case 'load':
        $action_ret = $loader->actionLoad();
        break;
    case 'del':
        $action_ret = $loader->actionDelete();
        break;
    case 'oryza_err':
        $action_ret = $loader->actionOryzaError();
        break;

}
?>
    
<div style="margin-left: 30px">
    
<?php if ($action!=='list' && $action_ret) : ?>
    <h3><a href="admin_weather_file.php">Back to Directory List</a><br /></h3>
    <?php echo $action_ret ?>
<?php endif; ?>    
    
<?php if ($action==='list' && $files_r): ?>
    <h3>Real-Time Data</h3>    

    <table border="1" cellspacing="0" cellpadding="3">
        <tr style="background-color:#dadada">
            <th width="100">File</th>
            <th width="150">Is displayed to<br /> weather chart?</th>
            <th width="180">Action</th>
            <th width="150">Is displayed to<br /> yield chart?</th>
            <th width="180">Action</th>
        </tr>
    <?php $last_country = '' ?>
    <?php $last_station = '' ?>    
    <?php $wtype = 'r' ?>        
    <?php foreach($files_r as $file) : ?>
        
        <?php if($last_country!=$file['country']): ?>
        <tr>
            <td colspan="5" style="font-family:courier;font-weight:700;font-size: 1.6em"><img class="icon-flag-<?php echo $file['country'] ?>" /><?php echo $loader->getCountryName($file['country']) ?></td>
        </tr>
        <?php endif; ?>

        <?php if($last_station!=$file['station']): ?>
        <tr>
            <td colspan="5" style="font-family:courier;font-weight:700;font-size: 1.4em"><img class="icon-flag-<?php echo $file['country'] ?>" /><i class="icon-globe"></i> <?php echo $loader->getStationName($file['country'],$file['station']) ?></td>
        </tr>
        <?php endif; ?>        
        
        <tr class="<?php echo ( $file['is_loaded'] || $file['is_oryza_loaded'] ) ? 'tr_loaded' : 'tr_nloaded'?>">
            <td style="font-family:courier;font-weight:700"><?php echo $file['file'] ?></td>
            <td>
                <?php if($file['is_loaded']): ?>
                    <?php echo $file['is_loaded']->upload_date . ' [ID: ' . $file['is_loaded']->id .']' ?>
                <?php else: ?>    
                    <span style="color:#ff0000"><i class="icon-eye-close"></i> NO</span>
                <?php endif; ?>
            </td>
            <td>
                <a class="btn btn-small" href="admin_weather_file.php?action=load&amp;loadto=w&amp;type=<?php echo $wtype ?>&amp;prnfile=<?php echo $file['subdir'] . '/' . $file['file'] ?>"><i class="icon-download"></i> Load</a>
                <?php if($file['is_loaded']): ?>
                    <a class="btn btn-small" href="admin_weather_file.php?action=del&amp;delete=w&amp;type=<?php echo $wtype ?>&amp;setid=<?php echo $file['is_loaded']->id ?>&amp;prnfile=<?php echo $file['subdir'] . '/' . $file['file'] ?>"><i class="icon-remove"></i> Delete</a>
                <?php endif; ?>    
            </td>
            <td>
                <?php if($file['is_oryza_loaded']): ?>
                    <?php echo $file['is_oryza_loaded']->upload_date ?>
                <?php else: ?>    
                    <span style="color:#ff0000"><i class="icon-eye-close"></i> NO</span>
                <?php endif; ?>
            </td>
            <td>
                <a class="btn btn-small" href="admin_weather_file.php?action=load&amp;loadto=o&amp;type=<?php echo $wtype ?>&amp;prnfile=<?php echo $file['subdir'] . '/' . $file['file'] ?>"><i class="icon-download"></i> Load</a>
                <?php if($file['is_oryza_loaded']): ?>
                    <a class="btn btn-small" href="admin_weather_file.php?action=del&amp;delete=o&amp;type=<?php echo $wtype ?>&amp;prnfile=<?php echo $file['subdir'] . '/' . $file['file'] ?>"><i class="icon-remove"></i> Delete</a>
                <?php endif; ?>
            </td>            
        </tr>        
        
        <?php $last_country = $file['country'] ?>
        <?php $last_station = $file['station'] ?>    
    <?php endforeach; ?>
    </table>    
<?php endif; ?>


<?php if ($action==='list' && $files_f): ?>
    <h3>Forecast Data</h3>
    
    <table border="1" cellspacing="0" cellpadding="3">
        <tr>
            <th width="100">File</th>
            <th width="150">Is displayed to<br /> weather chart?</th>
            <th width="180">Action</th>
            <th width="150">Is displayed to<br /> yield chart?</th>
            <th width="180">Action</th>
        </tr>    
        
    <?php $last_country = '' ?>
    <?php $last_station = '' ?>    
    <?php $wtype = 'f' ?>        
    <?php foreach($files_f as $file) : ?>
        <?php if($last_country!=$file['country']): ?>
        <tr>
            <td colspan="5" style="font-family:courier;font-weight:700;font-size: 1.6em"><img class="icon-flag-<?php echo $file['country'] ?>" /><?php echo $loader->getCountryName($file['country']) ?></td>
        </tr>
        <?php endif; ?>

        <?php if($last_station!=$file['station']): ?>
        <tr>
            <td colspan="5" style="font-family:courier;font-weight:700;font-size: 1.4em"><img class="icon-flag-<?php echo $file['country'] ?>" /><i class="icon-globe"></i> <?php echo $loader->getStationName($file['country'],$file['station']) ?></td>
        </tr>
        <?php endif; ?>        
        
        <tr>
            <td style="font-family:courier;font-weight:700"><?php echo $file['file'] ?></td>
            <td>
                <?php if($file['is_loaded']): ?>
                    <?php echo $file['is_loaded']->upload_date . ' [ID: ' . $file['is_loaded']->id .']' ?>
                <?php else: ?>    
                    <span style="color:#ff0000"><i class="icon-eye-close"></i> NO</span>
                <?php endif; ?>
            </td>
            <td>
                <a class="btn btn-small" href="admin_weather_file.php?action=load&amp;loadto=w&amp;type=<?php echo $wtype ?>&amp;prnfile=<?php echo $file['subdir'] . '/' . $file['file'] ?>"><i class="icon-download"></i> Load</a>
                <?php if($file['is_loaded']): ?>
                    <a class="btn btn-small" href="admin_weather_file.php?action=del&amp;delete=w&amp;type=<?php echo $wtype ?>&amp;setid=<?php echo $file['is_loaded']->id ?>&amp;prnfile=<?php echo $file['subdir'] . '/' . $file['file'] ?>"><i class="icon-remove"></i> Delete</a>
                <?php endif; ?>    
            </td>
            <td>
                <?php if($file['is_oryza_loaded']): ?>
                    <?php echo $file['is_oryza_loaded']->upload_date ?>
                <?php else: ?>    
                    <span style="color:#ff0000"><i class="icon-eye-close"></i> NO</span>
                <?php endif; ?>
            </td>
            <td>
                <a class="btn btn-small" href="admin_weather_file.php?action=load&amp;loadto=o&amp;type=<?php echo $wtype ?>&amp;prnfile=<?php echo $file['subdir'] . '/' . $file['file'] ?>"><i class="icon-download"></i> Load</a>
                <?php if($file['is_oryza_loaded']): ?>
                    <a class="btn btn-small" href="admin_weather_file.php?action=del&amp;delete=o&amp;type=<?php echo $wtype ?>&amp;prnfile=<?php echo $file['subdir'] . '/' . $file['file'] ?>"><i class="icon-remove"></i> Delete</a>
                <?php endif; ?>
            </td>            
        </tr>        
        
        <?php $last_country = $file['country'] ?>
        <?php $last_station = $file['station'] ?>    
    <?php endforeach; ?>
    </table>    
<?php endif; ?>
    
</div>    


<script type="text/javascript">
/**
 * page behaviours
 */
jQuery(function() {


    jQuery("#show_only_loaded").change(function() {
        if ( jQuery(this).is(':checked') == true )
        {
            jQuery('.tr_nloaded').hide();
        } else
        {
            jQuery('.tr_nloaded').show();
        }
    });
    
});
</script>        

<?php include('layout_footer.php'); ?>