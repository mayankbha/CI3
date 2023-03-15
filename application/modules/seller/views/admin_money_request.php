<?php
	/*echo '<pre> $profile :: '; print_r($profile);
	echo '<pre> $this->session :: '; print_r($this->session);
	die;*/

	$user_id = $this->session->userdata('user_id');
	$user_type = ($this->ion_auth->is_seller() ? 'seller' : '');
	$user_name = $profile->username;
	$user_paypal_email = $profile->paypal_email;
	$user_per_credit_cost = get_system_settings('seller_point_value');
	$user_bank_ac_details = $profile->bank_ac_details;
?>

<!-- Dashboard panel -->
<div class="dashboard-panel">
	<?php echo $message; ?>

	<div class="row">
		<?php
			$attributes = array('name' => 'credits_form', 'id' => 'credits_form', 'class' => 'comment-form dark-fields');
			echo form_open_multipart('seller/admin_money_request', $attributes); ?>

			<div class="col-sm-12">
				<div class="input-group">
					<label><?php echo get_languageword('credits');?><?php echo required_symbol(); ?>:</label>

					<?php			   
						$val = '';
						if(isset($_POST['submitbutt']))
						{
							$val = $this->input->post('credits');
						}

						$element = array(
							'name'	=>	'credits',
							'id'	=>	'credits',
							'type'  =>	'text',
							'value'	=>	$val,
							'class' => 'form-control',
							'placeholder' => get_languageword('credits'),
						);

						echo form_input($element);
					?>

					<?php			   
						$element = array(
							'name'	=>	'user_id',
							'id'	=>	'user_id',
							'type'  =>	'hidden',
							'value'	=>	$user_id
						);

						echo form_input($element);
					?>
					<?php			   
						$element = array(
							'name'	=>	'user_type',
							'id'	=>	'user_type',
							'type'  =>	'hidden',
							'value'	=>	$user_type
						);

						echo form_input($element);
					?>
					<?php			   
						$element = array(
							'name'	=>	'user_name',
							'id'	=>	'user_name',
							'type'  =>	'hidden',
							'value'	=>	$user_name
						);

						echo form_input($element);
					?>
					<?php			   
						$element = array(
							'name'	=>	'user_paypal_email',
							'id'	=>	'user_paypal_email',
							'type'  =>	'hidden',
							'value'	=>	$user_paypal_email
						);

						echo form_input($element);
					?>
					<?php			   
						$element = array(
							'name'	=>	'user_per_credit_cost',
							'id'	=>	'user_per_credit_cost',
							'type'  =>	'hidden',
							'value'	=>	$user_per_credit_cost
						);

						echo form_input($element);
					?>
					<?php			   
						$element = array(
							'name'	=>	'user_bank_ac_details',
							'id'	=>	'user_bank_ac_details',
							'type'  =>	'hidden',
							'value'	=>	$user_bank_ac_details
						);

						echo form_input($element);
					?>
				</div>
			</div>

			<div class="col-sm-12 ">
				<button class="btn-link-dark dash-btn" name="submitbutt" type="Submit"><?php echo get_languageword('SAVE');?></button>
			</div>
		</form>
	</div>
</div>
<!-- Dashboard panel ends -->