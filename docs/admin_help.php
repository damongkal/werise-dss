<?php
include('bootstrap.php'); 
admin_auth(); 
define('_INIT','admin');
include('layout_header.php'); 
?>    

<div id ="dataselection">
    <span style="font-weight: 700">Administration » Help Guide</span>
</div>

<div id="help-guide">
    <p class="question">Question</p>
    <p class="answer">Answer</p>
</div>

<?php include('layout_footer.php'); ?>