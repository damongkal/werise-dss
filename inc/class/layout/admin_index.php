<div class="width-center">

    <div id="dataselection" class="adm-menu adm-menu-main">
        <h2><i class="icon-cog"></i> Dataset Processing</h2>
            <p><a href="admin.php?pageaction=cdfdm">Cumulative Distribution Function based Downscale Method (CDF-DM)</a></p>
            <?php if (_ADM_ENV!=='PROD') : ?>
                <p><a href="admin.php?pageaction=weatherfile"> Weather Data (PRN format)</a></p>        
                <p><a href="admin.php?pageaction=oryza">Oryza2000 Interface</a></p>        
                <!--h3><a href="admin.php?pageaction=acknowledge"></i> Weather Files Acknowledgement</a></h3-->
                <!--h3><a href="admin.php?pageaction=export">Export to Server</a></h3-->
            <?php endif; ?>        
        <h2><i class="icon-folder-close"></i> References</h2>
        <p><a href="admin.php?pageaction=stations">Database Overview</a></p>
        <!--p><a href="admin.php?pageaction=weatherref">Weather Data</a></p-->
        <!--p><a href="admin.php?pageaction=oryzaref">Oryza2000 Data</a></p-->
        <p><a href="admin.php?pageaction=varieties">Rice Varieties</a></p>
        <?php if (_ADM_ENV!=='PROD') : ?>
        <p><a href="admin.php?pageaction=rcm">Fertilizer Application Reference</a></p>
        <?php endif; ?>        
        <h2><i class="icon-wrench"></i> System Options</h2>
        <p><a href="admin.php?pageaction=config">Preferences</a></p>
        <p><a href="admin.php?pageaction=users">Users</a></p>
        <p><a href="admin.php?pageaction=phpinfo">PHP Information</a></p>
        <!--h3><a href="admin.php?pageaction=help"><i class="icon-question-sign"></i> Admin Help Guide</a></h3-->
    </div>

</div>