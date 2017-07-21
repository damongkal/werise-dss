<script type="text/javascript" src="js/jquery/jquery.validate.min.js"></script>

<div class="width-center">
    <header>
        <h1 class="title"><?php echo _CURRENT_OPT ?></h1>
    </header>

    <?php if ($cls->is_register_done): ?>
        <p>You are already registered. You can already login and access the data. <a href="mailto:werise.admin@irri.org" style="font-weight: 700">Contact us</a> if you need assistance in using WeRise.</p>
    <?php endif;?>
    
    <?php if ($cls->is_submit && !$cls->is_register_done): ?>
        <p>Thank you! Your account has been created. You can already login and access the data.</p>
    <?php endif; ?>    
    
    <?php if (!$cls->is_submit && !$cls->is_register_done): ?>

    <form id="register-form" action="index.php?pageaction=register" method="post">
        <div>
            <label for="username">Username <span class="label label-important">*</span></label>
            <input type="text" class="form-control" id="username" name="username" placeholder="username" value="<?php echo $cls->user_record->username ?>" tabindex="1" required>
        </div>
        <div>
            <label for="password">Password <span class="label label-important">*</span></label>
            <input type="password" class="form-control" id="password" name="password" placeholder="password" tabindex="2" required>
        </div>
        <div>
            <label for="password2">Re-type Password <span class="label label-important">*</span></label>
            <input type="password" class="form-control" id="password2" name="password2" placeholder="Re-type Password" tabindex="3" required>
        </div>
        <div>
            <label for="fullname">Full Name <span class="label label-important">*</span></label>
            <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Full Name" tabindex="4" value="<?php echo $cls->user_record->fullname ?>" style="width:400px" required>
        </div>
        <div>
            <label for="email">Email Address <span class="label label-important">*</span></label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?php echo $cls->user_record->email ?>" tabindex="5" style="width:400px" required>
        </div>
        <div>
            <label for="address">Contact Address</label>
            <textarea class="form-control" id="address" name="address" placeholder="Contact Address" tabindex="6" rows="5" cols="50" style="width:400px"><?php echo $cls->user_record->address ?></textarea>
        </div>
        <div>
            <label for="phone">Phone</label>
            <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone" value="<?php echo $cls->user_record->phone ?>" tabindex="7">
        </div>
        <div>
            <label for="reason">Share with us the reason why you want to use WeRise <span class="label label-important">*</span></label>
            <textarea class="form-control" id="reason" name="reason" placeholder="Reason" tabindex="8" rows="10" cols="100" style="width:600px" required><?php echo $cls->user_record->reason ?></textarea>
        </div>
        <div style="margin-top: 10px">
            <button class="form-control btn btn-success" name="submit-register" id="submit-register" type="submit"><i class="icon-ok icon-white"></i> <?php __('Submit') ?></button>
        </div>

    </form>

<script type="text/javascript">
jQuery(function() {
    jQuery('#register-form').validate({
        rules: {
            username: {
                minlength: 4,
                maxlength: 20
            },
            password: {
                minlength: 8,
                maxlength: 20
            },
            password2: {
                equalTo: "#password"
            },
            fullname: {
                minlength: 5,
                maxlength: 255
            },            
            reason: {
                minlength: 1
            }
        },
        messages: {
            password2: {
                equalTo: "Please enter the same password as above"
            }
        }
    });
});
</script>    
<?php endif; ?>

</div>
