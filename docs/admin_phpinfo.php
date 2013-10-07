<?php
include('bootstrap.php'); 
admin_auth(); 
define('_INIT','admin');
include('layout_header.php'); 
?>

<div id ="dataselection">
    <span style="font-weight: 700">Administration » PHP Information</span>
</div>

<?php phpinfo() ?>
        
<?php include('layout_footer.php'); ?>