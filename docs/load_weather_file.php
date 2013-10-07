<?php
include_once('bootstrap.php');
admin_auth();
define('_INIT','admin');
include('layout_header.php'); 
?>

<div id ="dataselection">
    <span style="font-weight: 700">Administration » Weather Data File</span>
</div>

<?php
class load_weather_file
{
    private $cls;
    private $cls2;
    private $cls3;
    
    public function __construct()
    {
        $this->cls = new weather_data;
        $this->cls2 = new oryza_data;    
        $this->cls3 = new datafiles;
    }
    
    public function getWeatherFiles($wtype)
    {
        $files = $this->cls->getAvailableFiles($wtype);
        if ($files)
        {
            foreach ($files as $key => $file)
            {
                $arr = $this->cls3->getDatasetFromFilename($file['file']);
                $arr['subdir'] = $file['subdir'];
                $arr['is_loaded'] = $this->cls->isLoaded($arr,$wtype);
                $arr['is_oryza_loaded'] = $this->cls2->isLoaded($arr);
                $files[$key] = $arr;
            }
        }    
        return $files;
    }    
    
    public function loadFile($prnfile)
    {
        // destination of data load
        $loadto = 'w';
        if (isset($_GET['loadto']))
        {
            $loadto = $_GET['loadto'];
        }

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
        }  
        
        return $ret;
    }
}

$loader = new load_weather_file;
$files_r = false;
$files_f = false;

if (isset($_GET['prnfile']))
{
    // there is a request for data load
    // lets process it
    echo '<a href="load_weather_file.php">Back to Directory List</a><br />';
    $ret = $loader->loadFile($_GET['prnfile']);
    
    // data load results
    if($ret[0])
    {
        echo '<hr /><pre>';
        print_r($ret[1]);
        echo '</pre>';
        echo 'dataset was loaded successfully.<br /><br />';
        echo '<a href="load_weather_file.php">Back to Directory List</a>';
    } else
    {
        echo 'ERROR: '. $ret[1];
    }
    
    
} else
{
    // lets display the list of files available 
    // for data loading
    $files_r = $loader->getWeatherFiles(weather_data::_REALTIME);
    $files_f = $loader->getWeatherFiles(weather_data::_FORECAST);
}
?>
    
<div style="margin-left: 30px">
    
<?php if ($files_r): ?>
    <h3>Real-Time Data</h3>    

    <table border="1" cellspacing="0" cellpadding="3">
        <tr>
            <th width="100">File</th>
            <th width="150">Is Loaded to<br /> Weather chart data?</th>
            <th width="80">Action</th>
            <th width="150">Is Loaded to<br /> Oryza2000 chart data?</th>
            <th width="80">Action</th>
        </tr>    
    <?php foreach($files_r as $file) : ?>
        <tr>
            <td style="font-family:courier;font-weight:700"><?php echo $file['file'] ?></td>
            <td>
                <?php if($file['is_loaded']): ?>
                    <?php echo $file['is_loaded']->upload_date ?>
                <?php else: ?>    
                    <span style="color:#ff0000">NOT LOADED</span>
                <?php endif; ?>
            </td>
            <td>
                <a href="load_weather_file.php?loadto=w&amp;type=r&amp;prnfile=<?php echo $file['subdir'] . '/' . $file['file'] ?>">Load File</a>
            </td>
            <td>
                <?php if($file['is_oryza_loaded']): ?>
                    <?php echo $file['is_oryza_loaded']->upload_date ?>
                <?php else: ?>    
                    <span style="color:#ff0000">NOT LOADED</span>
                <?php endif; ?>
            </td>
            <td>
                <a href="load_weather_file.php?loadto=o&amp;prnfile=<?php echo $file['subdir'].'/'.$file['file'] ?>">Load File</a>
            </td>            
        </tr>        
    <?php endforeach; ?>
    </table>    
<?php endif; ?>


<?php if ($files_f): ?>
    <h3>Forecast Data</h3>
    
    <table border="1" cellspacing="0" cellpadding="3">
        <tr>
            <th width="100">File</th>
            <th width="150">Is Loaded to<br /> Weather chart data?</th>
            <th width="80">Action</th>
            <th width="150">Is Loaded to<br /> Oryza2000 chart data?</th>
            <th width="80">Action</th>
        </tr>    
    <?php foreach($files_f as $file) : ?>
        <tr>
            <td style="font-family:courier;font-weight:700"><?php echo $file['file'] ?></td>
            <td>
                <?php if($file['is_loaded']): ?>
                    <?php echo $file['is_loaded']->upload_date ?>
                <?php else: ?>    
                    <span style="color:#ff0000">NOT LOADED</span>
                <?php endif; ?>
            </td>
            <td>
                <a href="load_weather_file.php?loadto=w&amp;type=f&amp;prnfile=<?php echo $file['subdir'] . '/' . $file['file'] ?>">Load File</a>
            </td>
            <td>
                <?php if($file['is_oryza_loaded']): ?>
                    <?php echo $file['is_oryza_loaded']->upload_date ?>
                <?php else: ?>    
                    <span style="color:#ff0000">NOT LOADED</span>
                <?php endif; ?>
            </td>
            <td>
                <a href="load_weather_file.php?loadto=o&amp;prnfile=<?php echo $file['subdir'].'/'.$file['file'] ?>">Load File</a>
            </td>            
        </tr>        
    <?php endforeach; ?>
    </table>    
<?php endif; ?>
    
</div>    

<?php include('layout_footer.php'); ?>