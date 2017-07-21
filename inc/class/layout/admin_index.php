<div class="width-center">

    <div id="dataselection" class="adm-menu adm-menu-main">
        <?php if (_ADM_ENV!=='PROD') : ?>
        <h2>Dataset Processing</h2>
        <p><a href="admin.php?pageaction=cdfdm"><i class="icon-cog"></i> Cumulative Distribution Function based Downscale Method (CDF-DM)</a></p>
        <p><a href="admin.php?pageaction=weatherfile"><i class="icon-cog"></i> Weather Data (PRN format)</a></p>        
        <p><a href="admin.php?pageaction=oryza"><i class="icon-cog"></i> Oryza2000 Interface</a></p>        
        <!--h3><a href="admin.php?pageaction=acknowledge"><i class="icon-download"></i> Weather Files Acknowledgement</a></h3-->
        <!--h3><a href="admin.php?pageaction=export"><i class="icon-download"></i> Export to Server</a></h3-->
        <?php endif; ?>        
        <h2>References</h2>
        <p><a href="admin.php?pageaction=stations"><i class="icon-folder-close"></i> Database Overview</a></p>
        <p><a href="admin.php?pageaction=weatherref"><i class="icon-folder-close"></i> Weather Data</a></p>
        <p><a href="admin.php?pageaction=oryzaref"><i class="icon-folder-close"></i> Oryza2000 Data</a></p>
        <?php if (_ADM_ENV!=='PROD') : ?>
        <p><a href="admin.php?pageaction=rcm"><i class="icon-folder-close"></i> Fertilizer Application Reference</a></p>
        <?php endif; ?>        
        <h2>System Options</h2>
        <p><a href="admin.php?pageaction=config"><i class="icon-wrench"></i> Preferences</a></p>
        <p><a href="admin.php?pageaction=users"><i class="icon-wrench"></i> Users</a></p>
        <p><a href="admin.php?pageaction=phpinfo"><i class="icon-wrench"></i> PHP Information</a></p>
        <!--h3><a href="admin.php?pageaction=help"><i class="icon-question-sign"></i> Admin Help Guide</a></h3-->
    </div>

</div>