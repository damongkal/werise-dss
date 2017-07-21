<div class="width-center">
    <div id="help-btn">
        <p>Displays the users who can access the website.</p>
    </div>

    <?php if(is_null($cls->userid)) : ?>
    
    <table class="table table-bordered adm-table">
        <tr class="tr-gray">
            <th width="200">Username</th>
            <th width="200">Full Name</th>
            <th width="400">Address</th>
            <th width="200">Email</th>
            <th width="200">Phone</th>
            <th width="100">Is Enabled?</th>
            <th width="130">Date Created</th>
        </tr>

        <?php foreach (werise_users_model::getRecords() as $rec) : ?>

            <tr>
                <td><a href="admin.php?pageaction=users&userid=<?php echo $rec->userid ?>"><?php echo $rec->username ?></a></td>
                <td><?php echo $rec->fullname ?></td>
                <td><?php echo $rec->address ?></td>
                <td><?php echo $rec->email ?></td>
                <td><?php echo $rec->phone ?></td>
                <td><?php echo ($rec->is_enabled==1) ? "YES" : "NO" ?></td>
                <td><?php echo $rec->date_created ?></td>
            </tr>

        <?php endforeach; ?>
    </table>
    
    <?php else: ?>
        <?php $user = $cls->getUser(); ?>
        <?php if ($user) : ?>
    
            <table class="table table-bordered adm-table">
                <tr>
                    <td class="tr-gray" width="80">Username</td>
                    <td width="500"><?php echo $user->username ?></td>
                </tr>   
                <tr>
                    <td class="tr-gray" width="80">Full Name</td>
                    <td width="500"><?php echo $user->fullname ?></td>
                </tr>   
                <tr>
                    <td class="tr-gray" width="80">Email</td>
                    <td width="500"><?php echo $user->email ?></td>
                </tr>   
                <tr>
                    <td class="tr-gray" width="80">Contact Address</td>
                    <td width="500"><?php echo $user->address ?></td>
                </tr>   
                <tr>
                    <td class="tr-gray" width="80">Phone</td>
                    <td width="500"><?php echo $user->phone ?></td>
                </tr>   
                <tr>
                    <td class="tr-gray" width="80">Is Enabled?</td>
                    <td width="500"><?php echo $cls->formatEnabled($user->is_enabled) ?></td>
                </tr>                   
                <tr>
                    <td class="tr-gray" width="80">Date Registered</td>
                    <td width="500"><?php echo $user->date_created ?></td>
                </tr>                   
                <tr>
                    <td class="tr-gray" width="80">Extra Info</td>
                    <td width="500"><?php echo $user->reason ?></td>
                </tr>                                   
             </table>   

            <a href="admin.php?pageaction=users"><i class="icon-arrow-left"> </i> Back to Users List</a>
            
            <h3>Weather Access Log</h3>
            
            <table class="table table-bordered adm-table">
                <tr class="tr-gray">
                    <th width="120">Date</th>
                    <th width="80">Country</th>
                    <th width="80">Station</th>
                    <th width="80">Year</th>
                    <th width="80">Type</th>
                </tr>            
            <?php $weather_access_log = $cls->getWeatherAccessLog(); ?>
            <?php if ($weather_access_log) : ?>
                <?php foreach($weather_access_log as $wlog) : ?>
                    <tr>
                        <td><?php echo $wlog->create_date ?></td>
                        <td><?php echo $wlog->country_code ?></td>
                        <td><?php echo $wlog->station_id ?></td>
                        <td><?php echo $wlog->year ?></td>
                        <td><?php echo $wlog->wtype ?></td>
                    </tr>                
                <?php endforeach; ?>                    
            <?php endif; ?>
            </table>
            
            <h3>Grain Yield Access Log</h3>
            
            <table class="table table-bordered adm-table">
                <tr class="tr-gray">
                    <th width="120">Date</th>
                    <th width="80">Country</th>
                    <th width="80">Station</th>
                    <th width="80">Year</th>
                    <th width="80">Type</th>
                    <th width="150">Variety</th>
                    <th width="80">Fert</th>
                </tr>            
            <?php $oryza_access_log = $cls->getOryzaAccessLog(); ?>
            <?php if ($oryza_access_log) : ?>
                <?php foreach($oryza_access_log as $olog) : ?>
                    <tr>
                        <td><?php echo $olog->create_date ?></td>
                        <td><?php echo $olog->country_code ?></td>
                        <td><?php echo $olog->station_id ?></td>
                        <td><?php echo $olog->year ?></td>
                        <td><?php echo $olog->wtype ?></td>
                        <td><?php echo $olog->variety ?></td>
                        <td><?php echo $olog->fert ?></td>
                    </tr>                
                <?php endforeach; ?>                    
            <?php endif; ?>
            </table>  
            
        <?php endif; ?>            
            
    <?php endif; ?>
</div>