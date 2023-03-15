<!DOCTYPE html>
<html lang="en" dir="<?php echo language_type();?>">

<head>
    <title>
	<?php

    if($this->ion_auth->is_buyer())
    {
        $ctrl = 'buyer';
    }
    elseif($this->ion_auth->is_institute())
    {
        $ctrl = 'institute';
    }
    elseif($this->ion_auth->is_admin())
    {
        $ctrl = 'admin';
    }else{
        $ctrl = '';
    }
  
	if(isset($pagetitle) && $pagetitle != '')
	echo ucfirst($ctrl).' '.$pagetitle.' - '. $this->config->item('site_settings')->site_title ;
	elseif(isset($this->config->item('site_settings')->site_title) && $this->config->item('site_settings')->site_title != '')
	echo $this->config->item('site_settings')->site_title;
	else
		echo get_languageword('Sellers').' : '.get_languageword('Find Sellers Now');
	?></title>
    


    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="<?php if(isset($meta_description) && $meta_description != "") echo $meta_description; elseif(isset($this->config->item('seo_settings')->meta_description) && $this->config->item('seo_settings')->meta_description != '') echo $this->config->item('seo_settings')->meta_description; else if(isset($this->config->item('seo_settings')->site_description) && $this->config->item('seo_settings')->site_description != '') echo $this->config->item('seo_settings')->site_description;?>">

	<meta name="keywords" content="<?php if(isset($meta_keywords) && $meta_keywords != "") echo $meta_keywords; elseif(isset($this->config->item('seo_settings')->meta_keyword)) echo $this->config->item('seo_settings')->meta_keyword;?>">
    <meta name="author" content="">
    <link rel="shortcut icon" href="<?php if(isset($this->config->item('site_settings')->favicon) && file_exists(URL_PUBLIC_UPLOADS . 'settings/thumbs/'.$this->config->item('site_settings')->favicon)) echo URL_PUBLIC_UPLOADS2.'settings/thumbs/'.''.$this->config->item('site_settings')->favicon; else echo URL_FRONT_IMAGES.'favicon.ico';?>"/>

 
    <meta property="fb:app_id"  content="102143277123049" />
    <meta property="og:title"  content="<?= $pagetitle ?>" />
    <meta property="og:description"  content="<?= $meta_description ?>" />
    <meta property="og:image"  content="<?= $pageogimage ?>" />
    <meta property="og:image:type" content="image/jpeg" /> 
    <meta property="og:image:secure_url"  content="<?= $pageogimage ?>" />
    <meta property="og:image:width"  content="400" />
    <meta property="og:image:height"  content="400" />
    <meta property="og:url" content= <?= current_url() ?> />
    <meta property="og:type" content="<?= $pageogtype?$pageogtype:"website" ?>" />
	<?php
	if(isset($grocery) && $grocery == TRUE)
	{
	?>
		<?php
		foreach($css_files as $file): ?>
		<link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
		<?php endforeach; ?>
		<script src="<?php echo URL_FRONT_JS;?>jquery.js"></script>
	<?php
	}?>


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
       <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
       <!--[if lt IE 9]>
           <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
           <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
       <![endif]-->

    <link rel="stylesheet" type="text/css" href="<?php echo URL_FRONT_CSS;?>main.css">

    <script type="text/javascript" src="https://platform-api.sharethis.com/js/sharethis.js#property=6240b33a660a600012a3a19a&product=inline-share-buttons" async="async"></script>
</head>

<body>
    <!-- Preloader -->
    <!-- <div id="preloader">
        <div id="status"></div>
    </div> -->
    <!-- Ends Preloader -->

    <!-- Top bar -->
    <?php if(strip_tags($this->config->item('site_settings')->top_most_section) == "On") { ?>
    <div class="navbar-inverse top-bar">
        <div class="container">
            <style type="text/css">
                @media (max-width: 768px) {
                    #top-bar{
                       justify-content:center;
                       align-items:center;
                    }
                }
                #top-bar{
                       display:flex;
                }
            </style>
            <ul class="nav navbar-nav top-nav-left" id="top-bar" >
                <?php if (!$this->ion_auth->logged_in()) { ?>
                    <li>
                        <a style="padding: 3px!important;font-weight: 300;" href="<?php echo 'https://books.itbsh.com/auth/login';?>"> <span class="nav-btn"> <i class="fa  fa-user top-bar-icn"></i>  <?php echo get_languageword('Login');?></span>
                        </a>
                    </li>
                    <li>
                        <a style="padding: 3px!important;font-weight: 300;" href="<?php echo 'https://books.itbsh.com/admin';?>"> <span class="nav-btn"> <i class="fa  fa-user top-bar-icn"></i>  Admin Login</span>
                        </a>
                    </li>
                    <?php } else {
                        $url = base_url().'seller/index';
                        $ctrl = 'seller';
                        if($this->ion_auth->is_buyer())
                        {
                            $url = base_url().'buyer/index';
                            $ctrl = 'buyer';
                        }
                        elseif($this->ion_auth->is_institute())
                        {
                            $url = base_url().'institute/index';
                            $ctrl = 'institute';
                        }
                        elseif($this->ion_auth->is_admin())
                        {
                            $url = base_url().'admin/index';
                            $ctrl = 'admin';
                        }

                        ?>
                        <li>
                        <a style="padding: 3px!important;font-weight: 300;" href="<?php echo $url;?>"> <span class="nav-btn"> <i class="fa  fa-dashboard top-bar-icn"></i>  <?php echo ucfirst($ctrl).' Dashboard';?><?=  !($this->ion_auth->is_admin())?"(".get_system_settings('currency_symbol').get_user_credit_sum().")":"" ?></span>
                        </a>
                        </li>
                        <li>
                        <a style="padding: 3px!important;font-weight: 300;" href="<?php echo URL_AUTH_LOGOUT;?>"> <span class="nav-btn"> <i class="fa fa-sign-out top-bar-icn"></i>  <?php echo get_languageword('Logout');?></span>
                        </a>
                        </li>
                        <?php
                    } ?>
            </ul>
            <ul class="nav navbar-nav top-nav-right" id="top-bar" style="float: right;">                   
                <?php
                    if($this->ion_auth->logged_in() && $this->ion_auth->is_buyer()):
                        $userDetails = getUserRec();
                        if((float)$userDetails->admin_discount > 0):
                ?>
                    <li>
                        <a style="padding: 3px!important;font-weight: 700;color: green;font-size: 16px;" href="<?php echo base_url('/buy-books');?>"> <span class="nav-btn">  <?php echo get_languageword('Awesome! You have an exclusive minimum discount of '.$userDetails->admin_discount.'% on every purchase!');?></span></a>
                    </li>
                <?php
                        endif;
                    endif;
                ?>
            </ul>
            <?php if(isset($this->config->item('site_settings')->land_line) && $this->config->item('site_settings')->land_line != '') { ?>
			<ul class="nav navbar-nav pull-right">

            </ul>
			<?php } ?>
        </div>
    </div>
    <?php } ?>
    <!-- Ends Topbar -->

    <!-- Nagigation -->
    <nav class="navbar navbar-default yamm">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <?php if ($this->ion_auth->logged_in() && !empty($my_profile)) { ?>
                <button type="button" class="offcanvas-btn visible-xs" data-toggle="offcanvas" style="display: none;">
                    <img src="<?php echo get_seller_img($my_profile->photo, $my_profile->gender);?>" class="img-circle " alt="<?php echo $my_profile->first_name;?> <?php echo $my_profile->last_name;?>">
                </button>
                <?php } ?>
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#mega-nav-menu">
                    <span class="sr-only">Menu</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <a href="<?php echo base_url();?>"><img src="<?php  if(isset($this->config->item('site_settings')->logo) && $this->config->item('site_settings')->logo != '') echo URL_PUBLIC_UPLOADS_SETTINGS.''.$this->config->item('site_settings')->logo; else echo URL_FRONT_IMAGES.'Logo.png';?>" class="logo <?php if($this->ion_auth->logged_in() && !empty($my_profile)) echo "dahboard-logo"; ?>" alt="logo"></a>
            </div>

            <!-- Collect the nav links, mega-menu, vertical-menu and other content for toggling -->
            <div class="collapse navbar-collapse" id="mega-nav-menu">
                <ul class="nav navbar-nav navbar-right">
                    <li><a class="<?php if(isset($activemenu) && $activemenu == "buy_books") echo 'active'; ?>" href="<?php echo URL_HOME_BUY_BOOKS;?>"> <?php echo get_languageword('All_Books');?> </a></li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
    </nav>
       <!-- Ends Navigation -->
