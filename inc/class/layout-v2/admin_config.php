<section id="main-content">
    <div class="container">

        <p class="lead">
            Set the options that is required for the operation of the website.
        </p>

        <div class="table-responsive">
            <table class="table table-bordered table-condensed">
                <tr class="tr-gray">
                    <th>Description</th>
                    <th width="450">Value</th>
                </tr>
                <?php foreach ($cls->getSysopts() as $rec) : ?>

                    <?php if ($rec['default'] === 'category'): ?>
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
        </div>

        <h4>Environment Variables</h4>

        <div class="table-responsive">
            <table class="table table-bordered table-condensed">
                <tr class="tr-gray">
                    <th width="250">Description</th>
                    <th>Value</th>
                </tr>
                <?php foreach ($cls->getEnv() as $rec) : ?>

                    <tr>
                        <td><?php echo $rec['desc'] ?></td>
                        <td style="font-family: Courier"><?php echo $rec['value'] ?></td>
                    </tr>

                <?php endforeach; ?>
            </table>
        </div>

    </div>
</section>

<script type="text/javascript">
    /**
     * page behaviours
     */
    jQuery(function () {

        jQuery('input').change(function () {
            updateValues(jQuery(this));
        });

        jQuery('select').change(function () {
            updateValues(jQuery(this));
        });

    });

    function updateValues(item)
    {
        jQuery.ajax({
            type: "GET",
            url: "ajax.php",
            data: "pageaction=config&action=update&key=" + item.attr('id') + '&val=' + item.val(),
            dataType: 'json',
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