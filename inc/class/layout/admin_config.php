<div class="width-center">
    <div id="help-btn">
        <p>Set the options that is required for the operation of the website.</p>
        <button class="btn btn-info btn-small" onclick="javascript:launch_help('q14')"><i class="icon-question-sign"> </i> Help</button>
    </div>

    <table class="table table-bordered adm-table2">
        <tr class="tr-gray">
            <th width="350">Description</th>
            <th width="350">Value</th>
        </tr>
        <?php foreach ($cls->getSysopts() as $rec) : ?>        

            <?php if ($rec['default']==='category'): ?>
            <tr class="tr-gray">
                <th colspan="2" style="color:#0000ff"><?php echo $rec['desc'] ?></th>
            </tr>                
            <?php else: ?>
            <tr>
                <td><?php echo $rec['desc'] ?></td>
                <td><?php echo $cls->showValue($rec) ?></td>
            </tr>        
            <?php endif; ?>
            
        <?php endforeach; ?>
    </table>   
    
    <h2>Environment Variables</h2>

    <table class="table table-bordered adm-table2">
        <tr class="tr-gray">
            <th width="300">Description</th>
            <th width="500">Value</th>
        </tr>
        <?php foreach ($cls->getEnv() as $rec) : ?>        

            <tr>
                <td><?php echo $rec['desc'] ?></td>
                <td style="font-family: Courier"><?php echo $rec['value'] ?></td>
            </tr>        

        <?php endforeach; ?>
    </table>       
</div>

<script type="text/javascript">
    /**
     * page behaviours
     */
    jQuery(function() {

        jQuery('input').change(function() {
            updateValues(jQuery(this));
        });
        
        jQuery('select').change(function() {
            updateValues(jQuery(this));
        });        

    });
    
    function updateValues(item)
    {
        jQuery.ajax({
            type: "GET",
            url: "ajax.php",
            data: "pageaction=config&action=update&key="+item.attr('id')+'&val='+item.val(),
            dataType : 'json',
            success: function (data) {
                alert(data);
            },
            error: function (e, t, n) {
                alert('ajax error: updateValues');
                return;
            }
        });
    }
</script>