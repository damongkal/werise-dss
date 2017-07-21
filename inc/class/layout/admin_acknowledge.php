<div class="width-center">
    
    <header>
        <h1 class="title"><?php echo _CURRENT_OPT ?></h1>
    </header>
    
    <?php foreach ($cls->actionList() as $fileset) : ?>
        <?php if ($fileset['files']): ?>
    
            <h3><?php echo $fileset['title'] ?> Data</h3>

            <table class="table table-bordered adm-table">
                <tr class="tr-gray">
                    <th width="120">File</th>
                    <th width="100">Country</th>
                    <th width="100">Station</th>
                    <th width="70">Year</th>
                    <th width="200">Action</th>
                </tr>
            <?php foreach($fileset['files'] as $file) : ?>
                <tr>
                    <td><?php echo $file['file'] ?></td>
                    <td><?php echo $file['country'] ?></td>
                    <td><?php echo $file['station'] ?></td>
                    <td><?php echo $file['year'] ?></td>
                    <td>
                        <?php if ($cls->action=='add') : ?>
                            <?php $cls->loadPrn($file,$fileset['wtype']) ?>
                            added
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </table>
        <?php endif; ?>
    <?php endforeach; ?>
        
</div>