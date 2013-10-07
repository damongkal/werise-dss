<?php
include('bootstrap.php'); 
admin_auth(); 
define('_INIT','admin');
include('layout_header.php'); 
?>

<div id ="dataselection">
    <span style="font-weight: 700">Administration » Country Stations</span>
</div>

<?php
class admin_stations
{
    private $db;    
    
    public function __construct()
    {
        $this->db = Database_MySQL::getInstance();
    }
}
?>

<div style="margin-left: 30px">
    
    <h3>Country Stations List</h3>
    
</div>


<?php include('layout_footer.php'); ?>