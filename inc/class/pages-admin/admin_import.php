<?php
echo 'under construction...';die();
include('bootstrap.php'); 
dss_utils::admin_auth(); 
define('_INIT','admin');
include('layout_header.php'); 

class admin_import
{
    public function execute()
    {
        echo '<pre>';
        print_r($_FILES);
        $tmp = file_get_contents($_FILES['export_file']['tmp_name']);
        echo $tmp;
        
    }
}

if (isset($_POST['submit']))
{
    $cls = new admin_import;
    $cls->execute();
}
?>

<div id ="dataselection">
    <span style="font-weight: 700">Administration » Import</span>
</div>

<div style="margin-left: 30px">

    <div>
        <form method="post" enctype="multipart/form-data" action="admin_import.php">
            <input name="export_file" type="file" /><br />
            <input name="submit" type="submit" value="Import" />
        </form> 
    </div>    

</div>


<?php include('layout_footer.php'); ?>