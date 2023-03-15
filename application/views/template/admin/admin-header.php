<!DOCTYPE html>
<html lang="en" dir="<?php echo language_type(); ?>">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">	
	<base href="<?php echo base_url();?>" />
	<link rel="shortcut icon" href="<?php if(isset($this->config->item('site_settings')->favicon) && file_exists(URL_PUBLIC_UPLOADS . 'settings/thumbs/'.$this->config->item('site_settings')->favicon)) echo URL_PUBLIC_UPLOADS2.'settings/thumbs/'.''.$this->config->item('site_settings')->favicon; else echo URL_FRONT_IMAGES.'favicon.ico';?>"/>

	<title><?php echo $this->config->item('site_settings')->site_title ;?> - Admin</title>

	<?php 
	if(isset($grocery) && $grocery == TRUE) 
	{
	?>
		<?php 
		foreach($css_files as $file): ?>
		<link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
		<?php endforeach; ?>
	<?php
	}?>
	
	<link href='<?php echo URL_ADMIN_CSS;?>adminlte.min.css' rel='stylesheet' media='screen'>
	<link href='<?php echo URL_ADMIN_CSS;?>lib.min.css' rel='stylesheet' media='screen'>
	<link href='<?php echo URL_ADMIN_CSS;?>app.min.css' rel='stylesheet' media='screen'>
	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->

	<?php if(!empty($activemenu) && $activemenu == "seller_selling_books") { ?>
		<link href='<?php echo URL_FRONT_CSS;?>magnific-popup.css' rel='stylesheet' media='screen'>
	<?php } ?>


	<link href="<?php echo URL_ADMIN_CSS;?>admin_notifications.css" rel="stylesheet">


<?php 
$methd = $this->uri->segment(2);
if ($methd=="seller-money-conversion-requests" || $methd=="inst-money-conversion-requests" || $methd=="fieldsvalues") { ?>
<style>
div.flexigrid a {
    color: blue;
    text-decoration: none !important;
}
</style>
<?php } 
$clas = $this->uri->segment(1);
if ($clas=="settings") { ?>
<style>
.flexigrid .read-icon {
    height: 20px;
}
</style>
<?php }
?>


</head>
<body class="skin-red"><div class="wrapper">

	<header class="main-header">
	<a href="<?php echo URL_ADMIN_INDEX;?>">
		<p class="logo" style="position: absolute;color: black;padding: 0px;"> <b>Eye</b> Books</p>
	</a>
	<nav class="navbar navbar-static-top" role="navigation">
		<a href="#" class="sidebar-toggle hidden-lg hidden-md" data-toggle="offcanvas" role="button">
			<span class="sr-only">Menu</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</a>
		<div class="navbar-custom-menu" style="
    /*background: #93b2ea;*/
    /*padding-top: 5px;
    padding-bottom: 10px;*/
">
			<ul class="nav navbar-nav" style="background: #93b2ea;">
				<li style="
    background: white;
">
             <a style=" background: white!important; color: black!important;padding: 10px;margin-top: 4px;margin-right: 15px;"href="<?php echo base_url(); ?>" target="_blank" class="btn btn-default"><?php echo 'Visit Home Page';?></a>
          </li>
				<li class="dropdown user user-menu">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" style="
    padding-top: 10px;
    padding-bottom: 5px;
">
						<span><?php echo $this->session->userdata('first_name').' '.$this->session->userdata('last_name');?></span>
					</a>
					<ul class="dropdown-menu">
						<!-- <li>
                            <a href="<?php echo URL_AUTH_PROFILE;?>"><i class="fa fa-user"></i> <?php echo get_languageword('profile');?></a>
						</li>
						<li>
							<a href='<?php echo URL_AUTH_CHANGE_PASSWORD;?>'><i class='fa fa-lock'></i> <?php echo get_languageword('change_password');?></a>
						</li> -->
						<li>
							<a href="<?php echo URL_AUTH_LOGOUT;?>"><i class="fa  fa-power-off"></i> <?php echo get_languageword('Sign out');?></a>
						</li>
					</ul>
				</li>
			</ul>
		</div>

	</nav>
</header>
		<aside class="main-sidebar">
		<section class="sidebar">
			<!--
			<div class="user-panel" style="height:65px">
				<div class="pull-left info" style="left:5px">
					<p>Webmaster</p>
					<a href="panel/account"><i class="fa fa-circle text-success"></i> Online</a>
				</div>
			</div>-->
			<?php 
			//neatPrint($this->session->all_userdata());
			$this->load->view('template/admin/admin-navigation');?>								</section>
	</aside>

		<div class="content-wrapper">
	
		<section class="content">
