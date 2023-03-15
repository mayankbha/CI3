<div class="panel-group dashboard-menu" id="accordion">
<div class="dashboard-profile">
	<?php 
		  $user_id = $this->ion_auth->get_user_id();

	?>
	<div class="media media-team">
		<a href="<?php echo base_url();?>buyer/index">
			<div class="media-body">
				<figure class="imghvr-zoom-in">
					<img class="media-object  img-circle" src="<?php echo get_buyer_img($my_profile->photo, $my_profile->gender); ?>" alt="<?php echo $my_profile->first_name;?> <?php echo $my_profile->last_name;?>">
					<figcaption></figcaption>
				</figure>
				
				<h4><?php echo $my_profile->username;?></h4>
				<!-- <p><?php echo get_languageword('User Login');?>: <?php echo date('d/m/Y H:i:s',$my_profile->last_login );?></p> -->
				<p><?php echo get_languageword('net_credits');?>: <strong><?php echo number_format(get_user_credits($user_id),0);?></strong>

                <span class="pull-right"><?php echo get_languageword('per_credit_value');?>: <strong><?php echo get_system_settings('currency_symbol').get_system_settings('buyer_point_value');?></strong></span></p>
			</div>
			
		</a>
	</div>
</div>
<div class="dashboard-menu-panel">
<div class="dashboard-link"><a <?php if(isset($activemenu) && $activemenu == 'dashboard') echo 'class="active"';?> href="<?php echo URL_BUYER_INDEX ?>"><i class="fa fa-tachometer"></i><?php echo get_languageword('Dashboard');?></a></div>


<div class="dashboard-link"><a <?php if(isset($activemenu) && $activemenu == 'my_book_purchases') echo 'class="active"';?> href="<?php echo URL_BUYER_BOOK_PURCHASES; ?>"><i class="fa fa-book"></i><?php echo get_languageword('My_Book_Purchases');?></a></div>

<div class="dashboard-link"><a <?php if(isset($activemenu) && $activemenu == 'user_credit_transactions') echo 'class="active"';?> href="<?php echo URL_BUYER_CREDITS_TRANSACTION_HISTORY;?>"><i class="fa fa-calendar"></i><?php echo get_languageword('credits_Transactions');?><span class="hidden-xs"> <?php echo get_languageword('History')?> </span></a></div>

<div class="dashboard-link"><a href="<?php echo URL_BUYER_PROFILE_INFO ?>"><i class="fa fa-image"></i><?php echo get_languageword('Update Profile');?></a></div>

<div class="dashboard-link"><a href="<?php echo base_url();?>auth/logout"><i class="fa fa-sign-out"></i><?php echo get_languageword('Logout');?></a></div>
<!-- /.panel -->
</div>
</div>
