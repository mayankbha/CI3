<div class="panel-group dashboard-menu" id="accordion">
<div class="dashboard-profile">
	<?php 
		  $user_id = $this->ion_auth->get_user_id(); 

		  $inst_id = is_inst_seller($user_id);

	?>
	<div class="media media-team">
		<a href="<?php echo base_url();?>seller/index">
			<div class="media-body">
            <figure class="imghvr-zoom-in">
				<img class="media-object  img-circle" src="<?php echo get_seller_img($my_profile->photo, $my_profile->gender);?>" alt="<?php echo $my_profile->first_name;?> <?php echo $my_profile->last_name;?>">
				<figcaption></figcaption>
			</figure>
			<h4><?php echo $my_profile->username;?></h4>
			<!-- <p><?php echo get_languageword('User Login');?>: <?php echo date('d/m/Y H:i:s',$my_profile->last_login );?></p> -->
			<?php if(!$inst_id){?>
			<p><?php echo get_languageword('net_credits');?>: <strong><?php echo number_format(get_user_credits($user_id),0);?></strong>

                <span class="pull-right"><?php echo get_languageword('per_credit_value');?>: <strong><?php echo get_system_settings('currency_symbol').get_system_settings('seller_point_value');?></strong></span></p>
			<?php } ?>
            </div>
            
            
		</a>
	</div>
</div>
    <div class="dashboard-menu-panel">
<div class="dashboard-link"><a <?php if(isset($activemenu) && $activemenu == 'dashboard') echo 'class="active"';?> href="<?php echo base_url();?>seller/index"><i class="fa fa-tachometer"></i><?php echo get_languageword('Dashboard');?></a></div>

<?php if(!$inst_id) { ?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">
		<a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseSellBook">
            <i class="fa fa-book"></i><?php echo get_languageword('Sell_Book_Online')?>
		</a>
	</h4>
	</div>
	<!--/.panel-heading -->
	<div id="collapseSellBook" class="panel-collapse <?php if(isset($activemenu) && $activemenu == 'sell_book_online') echo 'collapse in'; else echo 'collapse';?>">
		<div class="panel-body">
			<ul class="dashboard-links">
				<li <?php if(isset($activesubmenu) && $activesubmenu == 'publish') echo 'class="active"';?>><a href="<?php echo URL_SELLER_SELL_BOOKS_ONLINE;?>"><?php echo 'Add New Book'//get_languageword('publish');?> </a></li>
				<li <?php if(isset($activesubmenu) && $activesubmenu == 'list') echo 'class="active"';?>><a href="<?php echo URL_SELLER_LIST_SELLING_BOOKS;?>"><?php echo 'Manage All Book(s)'//get_languageword('List_Selling_Book');?> </a></li>
			</ul>
		</div>
		<!--/.panel-body -->
	</div>
	<!--/.panel-collapse -->
</div>

<div class="dashboard-link"><a <?php if(isset($activemenu) && $activemenu == 'purchased_book') echo 'class="active"';?> href="<?php echo URL_SELLER_PURCHASED_BOOKS; ?>"><i class="fa fa-money"></i><?php echo get_languageword('Total_Sales');?></a></div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">
		<a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseReqs">
            <i class="fa fa-money"></i><?php echo get_languageword('credit_Conversion')?><span class="hidden-xs"> <?php echo get_languageword('Request')?> </span>
		</a>
	</h4>
	</div>
	<!--/.panel-heading -->
	<div id="collapseReqs" class="panel-collapse <?php if(isset($activemenu) && $activemenu == 'credit_conversion_requests') echo 'collapse in'; else echo 'collapse';?>">
		<div class="panel-body">
			<ul class="dashboard-links">
				<li <?php if(isset($activesubmenu) && $activesubmenu == 'Pending') echo 'class="active"';?>><a href="<?php echo URL_SELLER_CREDIT_CONVERSION_REQUESTS;?>/Pending"><?php echo get_languageword('Pending');?> </a></li>
				<li <?php if(isset($activesubmenu) && $activesubmenu == 'Done') echo 'class="active"';?>><a href="<?php echo URL_SELLER_CREDIT_CONVERSION_REQUESTS;?>/Done"><?php echo get_languageword('Done');?> </a></li>
			</ul>
		</div>
		<!--/.panel-body -->
	</div>
	<!--/.panel-collapse -->
</div>

<div class="dashboard-link"><a <?php if(isset($activemenu) && $activemenu == 'user_credit_transactions') echo 'class="active"';?> href="<?php echo URL_SELLER_CREDITS_TRANSACTION_HISTORY;?>"><i class="fa fa-exchange"></i><?php echo get_languageword('credits_Transactions');?><span class="hidden-xs"> <?php echo get_languageword('History')?> </span></a></div>

<div class="dashboard-link"><a <?php if(isset($activemenu) && $activemenu == 'reviews') echo 'class="active"';?> href="<?php echo URL_SELLER_USER_REVIEWS; ?>"><i class="fa fa-retweet"></i><?php echo get_languageword('Reviews');?></a></div>    

<?php } ?>
<!-- /.panel -->


<div class="dashboard-link"><a href="<?php echo base_url();?>seller/personal-info"><i class="fa fa-usd"></i><?php echo get_languageword('Payment Information');?></a></div>


<div class="dashboard-link"><a href="<?php echo base_url();?>seller/profile-information"><i class="fa fa-image"></i><?php echo get_languageword('Update Profile');?></a></div>

<div class="panel panel-default">
	<div class="dashboard-link"><a href="<?php echo base_url();?>auth/logout"><i class="fa fa-sign-out"></i><?php echo get_languageword('Logout');?></a></div>
</div>
</div>
</div>
