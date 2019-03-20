<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container-fluid ">
	<div class="row">
		<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 col-lg-push-2 col-md-push-2 col-sm-push-2 col-xs-push-2">
			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 col-lg-push-1 col-md-push-1 col-sm-push-1 col-xs-push-1 admin-login-form">
				<img src="<?php echo base_url();?>asset/img/lending-logo.png" class="french-logo-lagin-page">
			</div>
			<div class="col-lg-7 col-md-7 col-sm-7 col-xs-7 col-lg-push-1 col-md-push-1 col-sm-push-1 col-xs-push-1 admin-login-form">
				<?php echo form_open('User/login','class="form-group admin-sign-form"');?>
				<table>
					<p class="signin-head">SIGN IN</p>
					<?php echo validation_errors('<p class="error">');?>
						<p><input type="text" class="form-control admin-txt-fld" name="username" placeholder="E-mail" value="<?php echo set_value('username','');?>"></p>
						<p><input type="password" class="form-control admin-txt-fld" name="password" placeholder="Password"></p>
						<p><input type="checkbox"><span class="admin-remember">Remember me</span>
						<p class="admin-login-btn"><input type="submit" name="userLogin" class="btn btn-default sign-in-btn admin-sign-btn" value="SIGN IN"></p>
				</table>
			 <?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
