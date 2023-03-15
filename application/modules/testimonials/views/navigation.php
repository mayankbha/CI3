<!--Start Breadcrumb-->  
  <ul class="breadcrumb breadCrumb">
<li><a href="<?php echo URL_ADMIN_INDEX;?>"> <i class="fa fa-home"></i> <?php echo get_languageword('home')?></a></li>
    <li><a href="<?php echo URL_TESTIMONIALS_INDEX?>"><?php echo get_languageword('testimonials')?></a></li>
    <li class="active"><?php echo isset($pagetitle) ? $pagetitle :  get_languageword('no_title');?></li>
  </ul>
  <!--End Breadcrumb--> 
<div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <div class="dashboardMenu clearfix">
          <ul>
            <li <?php if(isset($activesubmenu) && in_array($activesubmenu, array('all'))) echo 'class="active"';?>>
              <a href="<?php echo URL_TESTIMONIALS_INDEX;?>">  <div class="cir"><i class="flaticon-view24"></i> </div>
              <h2><?php echo get_languageword('all')?> <br>
         </h2></a>
            </li>
			
			<li <?php if(isset($activesubmenu) && in_array($activesubmenu, array('seller'))) echo 'class="active"';?>>
              <a href="<?php echo URL_TESTIMONIALS_INDEX;?>/3/sellerz">  <div class="cir"><i class="flaticon-view24"></i> </div>
              <h2><?php echo get_languageword('seller')?>'s <br>
         </h2></a>
            </li>
			
			<li <?php if(isset($activesubmenu) && in_array($activesubmenu, array('buyer'))) echo 'class="active"';?>>
              <a href="<?php echo URL_TESTIMONIALS_INDEX;?>/2/Buyerz">  <div class="cir"><i class="flaticon-view24"></i> </div>
              <h2><?php echo get_languageword('buyer')?>'s <br>
         </h2></a>
            </li>  
            
          </ul>
        </div>
      </div>
    </div>
  </div>