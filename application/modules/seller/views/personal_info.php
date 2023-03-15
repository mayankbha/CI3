<?php //print_r($profile);?>
<!-- Dashboard panel -->
<div class="dashboard-panel">
	<?php echo $message;?>
	<div class="row">

		<?php 
		$attributes = array('name' => 'profile_form', 'id' => 'profile_form', 'class' => 'comment-form dark-fields');
		echo form_open_multipart('seller/personal_info',$attributes);?>
			<div class="col-sm-12 ">
				<div class="input-group ">
					<label><?php echo get_languageword('paypal_email');?><?php echo required_symbol();?>:</label>
					<?php			   
					$val = '';
					if( isset($_POST['submitbutt']) )
					{
						$val = $this->input->post( 'paypal_email' );
					}
					elseif( isset($profile->paypal_email) && !isset($_POST['submitbutt']))
					{
						$val = $profile->paypal_email;
					}
					$element = array(
						'name'	=>	'paypal_email',
						'id'	=>	'paypal_email',
						'type'  =>	'email',
						'value'	=>	$val,
						'class' => 'form-control',
						'placeholder' => get_languageword('paypal_email_id'),
					);			
					echo form_input($element);
					?>
				</div>
			</div>
			<!-- <div class="col-sm-6 ">
				<div class="input-group ">
					<label><?php echo get_languageword('bank_account_details');?>:</label>
					<?php
					$val = '';
					if( isset($_POST['submitbutt']) )
					{
						$val = $this->input->post( 'bank_ac_details' );
					}
					elseif( isset($profile->bank_ac_details) && !isset($_POST['submitbutt']))
					{
						$val = $profile->bank_ac_details;
					}
					$element = array(
						'name'	=>	'bank_ac_details',
						'id'	=>	'bank_ac_details',
						'value'	=>	$val,
						'rows'	=> 4,
						'class' => 'form-control',
						'placeholder' => get_languageword('bank_ac_details'),
					);			
					echo form_textarea($element);
					?>
				</div>
			</div> -->
			<div class="col-sm-12 ">
				<button class="btn-link-dark dash-btn" name="submitbutt" type="Submit"><?php echo get_languageword('SAVE');?></button>
			</div>

		</form>
	</div>

</div>

<!-- Dashboard panel ends -->