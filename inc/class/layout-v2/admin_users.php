<section id="main-content">
    <div class="container">

        <p class="lead">
            Displays the users of the website.
        </p>

        <?php if (is_null($cls->userid)) : ?>

            <div class="table-responsive">
                <table class="table table-bordered table-condensed">
                    <tr class="tr-gray">
                        <th>Username</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th width="130">Date Created</th>
                    </tr>

                    <?php foreach (werise_users_model::getRecords() as $rec) : ?>

                        <tr>
                            <td><a href="admin.php?pageaction=users&userid=<?php echo $rec->userid ?>"><?php echo $cls->formatEnabled($rec) ?></a></td>
                            <td>
                                <?php echo $rec->fullname ?><br />
                                <i class="fas fa-envelope"></i> <?php echo $rec->email ?>
                            </td>
                            <td>
                                <i class="fas fa-address-card"></i> <?php echo $rec->address ?><br />
                                <i class="fas fa-phone"></i> <?php echo $rec->phone ?>
                            </td>
                            <td><?php echo $rec->date_created ?></td>
                        </tr>

                    <?php endforeach; ?>
                </table>
            </div>

        <?php else: ?>
            <?php $user = $cls->getUser(); ?>
            <?php if ($user) : ?>

                <div class="row">
                    <div class="col-md-8">        
                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed">
                                <tr>
                                    <td class="tr-gray" width="160">Username</td>
                                    <td><?php echo $user->username ?></td>
                                </tr>   
                                <tr>
                                    <td class="tr-gray">Full Name</td>
                                    <td><?php echo $user->fullname ?></td>
                                </tr>   
                                <tr>
                                    <td class="tr-gray">Email</td>
                                    <td><?php echo $user->email ?></td>
                                </tr>   
                                <tr>
                                    <td class="tr-gray">Contact Address</td>
                                    <td><?php echo $user->address ?></td>
                                </tr>   
                                <tr>
                                    <td class="tr-gray">Phone</td>
                                    <td><?php echo $user->phone ?></td>
                                </tr>   
                                <tr>
                                    <td class="tr-gray">Is Enabled?</td>
                                    <td><?php echo $user->is_enabled ?></td>
                                </tr>                   
                                <tr>
                                    <td class="tr-gray">Date Registered</td>
                                    <td><?php echo $user->date_created ?></td>
                                </tr>                   
                                <tr>
                                    <td class="tr-gray">Extra Info</td>
                                    <td><?php echo $user->reason ?></td>
                                </tr>                                   
                            </table>
                        </div>
                    </div>
                </div>

                <h4>Weather Advisory Access Log</h4>

                <div class="table-responsive">
                    <table class="table table-bordered table-condensed">
                        <tr class="tr-gray">
                            <th width="120">Date</th>
                            <th>Country</th>
                            <th>Station</th>
                            <th>Year</th>
                            <th>Type</th>
                        </tr>            
                        <?php $weather_access_log = $cls->getWeatherAccessLog(); ?>
                        <?php if ($weather_access_log) : ?>
                            <?php foreach ($weather_access_log as $wlog) : ?>
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
                </div>

                <h4>Crop Advisory Access Log</h4>

                <div class="table-responsive">
                    <table class="table table-bordered table-condensed">
                        <tr class="tr-gray">
                            <th width="120">Date</th>
                            <th>Country</th>
                            <th>Station</th>
                            <th>Year</th>
                            <th>Type</th>
                            <th>Variety</th>
                            <th>Fert</th>
                        </tr>            
                        <?php $oryza_access_log = $cls->getOryzaAccessLog(); ?>
                        <?php if ($oryza_access_log) : ?>
                            <?php foreach ($oryza_access_log as $olog) : ?>
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
                </div>

            <?php endif; ?>            

        <?php endif; ?>

    </div>
</section>