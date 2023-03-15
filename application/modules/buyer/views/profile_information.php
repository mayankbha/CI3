<!-- Dashboard panel -->
<div class="dashboard-panel">
	<?php echo $message;?>
	<?php 
	$attributes = array('name' => 'profile_form', 'id' => 'profile_form', 'class' => 'comment-form dark-fields');
	echo form_open_multipart('buyer/profile-information',$attributes);?>
		<div class="row">
			<div class="col-sm-8 ">
                <div class="input-group profile-img-block ">
					<p><?php echo get_languageword('Profile Image')?></p>
					<?php			   
					$val = '';
					if( isset($profile->photo) && $profile->photo != '')
					{
						$val = $profile->photo;
					}
					$element = array(
						'type' => 'file',
						'name'	=>	'photo',
						'id'	=>	'photo',
						'value'	=>	$val,
						'class' => 'form-control',
						'placeholder' => get_languageword('Profile Image'),
						'onchange' => "readURL(this, 'site_logo')",
					);			
					echo form_input($element);
					?>
					<?php 
                     $src = "";
                     $style="display:none;";
                     if($val != '' && file_exists('assets/uploads/profiles/thumbs/'.$val)) {
						$src = base_url().'/'."assets/uploads/profiles/thumbs/".$val;
                     	$style="";
                     }
                     ?>
                  <img id="site_logo" src="<?php echo $src;?>" height="120" style="<?php echo $style;?>" />
					<?php echo form_error('photo');?>
				</div>
			</div>
		</div>		
		<div class="row">
			<div class="col-sm-12">
				<div class="input-group ">
					<label><?php echo get_languageword('your_profile_description')?>:<sup>*</sup></label>
					<?php			   
					$val = '';
					if( isset($_POST['submitbutt']) )
					{
						$val = $this->input->post( 'profile' );
					}
					elseif( isset($profile->profile) && !isset($_POST['submitbutt']))
					{
						$val = $profile->profile;
					}
					$element = array(
						'name'	=>	'profile',
						'id'	=>	'profile',
						'value'	=>	$val,
						'maxlength'=>  500,
						'placeholder' => get_languageword('profile_description'),
						'rows' => 6,
					);			
					echo form_textarea($element);
					?>
					
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="input-group ">
					<label><?php echo get_languageword('your_meta_seo_keywords')?>:</label>
					<?php			   
					$val = '';
					if( isset($_POST['submitbutt']) )
					{
						$val = $this->input->post( 'seo_keywords' );
					}
					elseif( isset($profile->seo_keywords) && !isset($_POST['submitbutt']))
					{
						$val = $profile->seo_keywords;
					}
					$element = array(
						'name'	=>	'seo_keywords',
						'id'	=>	'seo_keywords',
						'value'	=>	$val,
						'maxlength'=>  100,
						'placeholder' => get_languageword('meta_seo_keywords'),
						'rows' => 6,
					);			
					echo form_textarea($element);
					?>
					
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="input-group ">
					<label><?php echo get_languageword('meta_description')?></label>
					<?php			   
					$val = '';
					if( isset($_POST['submitbutt']) )
					{
						$val = $this->input->post( 'meta_desc' );
					}
					elseif( isset($profile->meta_desc) && !isset($_POST['submitbutt']))
					{
						$val = $profile->meta_desc;
					}
					$element = array(
						'name'	=>	'meta_desc',
						'id'	=>	'meta_desc',
						'value'	=>	$val,
						'maxlength'=>  100,
						'placeholder' => get_languageword('meta_description'),
						'rows' => 6,
					);			
					echo form_textarea($element);
					?>
					
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-sm-6 ">
				<div class="input-group ">
					<label><?php echo get_languageword('Profile Page Title')?>:<sup>*</sup></label>
					<?php			   
					$val = '';
					if( isset($_POST['submitbutt']) )
					{
						$val = $this->input->post( 'profile_page_title' );
					}
					elseif( isset($profile->profile_page_title) && !isset($_POST['submitbutt']))
					{
						$val = $profile->profile_page_title;
					}
					$element = array(
						'name'	=>	'profile_page_title',
						'id'	=>	'profile_page_title',
						'value'	=>	$val,
						'class' => 'form-control',
						'placeholder' => get_languageword('Profile Page Title'),
					);			
					echo form_input($element);
					?>
					
				</div>
			</div>
			<div class="col-sm-6 ">
				<div class="input-group ">
					<label><?php echo get_languageword('Qualification')?></label>
					<?php			   
					$val = '';
					if( isset($_POST['submitbutt']) )
					{
						$val = $this->input->post( 'qualification' );
					}
					elseif( isset($profile->qualification) && !isset($_POST['submitbutt']))
					{
						$val = $profile->qualification;
					}
					$element = array(
						'name'	=>	'qualification',
						'id'	=>	'qualification',
						'maxlength' =>  "50",
						'value'	=>	$val,
						'class' => 'form-control',
						'placeholder' => get_languageword('Profile Page Title'),
					);			
					echo form_input($element);
					?>
					<?php echo form_error('qualification');?>
				</div>
			</div>
		</div>
		
		<button class="btn-link-dark dash-btn" name="submitbutt" type="Submit"><?php echo get_languageword('SAVE');?></button>

	</form>

</div>
<!-- Dashboard panel ends -->