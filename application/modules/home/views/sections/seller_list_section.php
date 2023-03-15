<?php 
	  if(!empty($seller_list)): 
		foreach($seller_list as $row): 

            $seller_rating = $this->home_model->get_seller_rating($row->slug);

?>
<div class="box-border">
            <div class="row ">
                <!-- User Profile -->
                <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12 no-padright">
                    <div class="profile-listing">
                        <a href="<?php echo URL_HOME_SELLER_PROFILE.'/'.$row->slug; ?>"><img src="<?php echo get_seller_img($row->photo, $row->gender); ?>" alt="" class="img-responsive img-circle"></a>
                    </div>

                    <?php echo get_user_online_status($row->is_online); ?>

                </div>
                <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 col-lg-push-7 col-md-push-5 col-sm-push-5">
                    <div class="send-quote-block text-center">
                        <p class="teaching-experience"><b><?php echo get_languageword('experience'); ?>:</b> <?php echo $row->teaching_experience." ".$row->duration_of_experience; ?></p>
                        <p class="qualification"><b><?php echo get_languageword('qualification'); ?>:</b> <?php echo $row->qualification; ?></p>
                        <p class="qualification"><b><?php echo get_languageword('Free_Demo'); ?>:</b> <?php echo $row->free_demo; ?></p>
                        <?php if($row->availability_status==0){?>
                        <p class="seller-not-avilble"><i class="fa fa-exclamation-circle" aria-hidden="true"></i><b><strong> <?php echo get_languageword('This Seller Is Not Available Now')?></strong></b></p>
                        <?php } else{?>
                        <div class="profile-view"><a href="<?php echo URL_HOME_SELLER_PROFILE.'/'.$row->slug; ?>" class="btn-link-dark"><?php echo get_languageword('View Profile'); ?></a></div>
                        <div class="profile-view"><a href="<?php echo URL_HOME_SELLER_PROFILE.'/'.$row->slug.'#reserve'; ?>" class="btn-link-dark"><?php echo get_languageword('Book Now'); ?></a></div>
                        <?php } ?>
                    </div>
                </div>
                <div class="col-lg-7 col-md-5 col-sm-5 col-xs-12 col-lg-pull-3 col-md-pull-4 col-sm-pull-4">
                    <div class="user-profile-content">
                        <ul class="user-badges">
                            <?php
                                  if(strcasecmp(get_system_settings('need_admin_for_seller'), 'yes') == 0) {

                                    $title = get_languageword('not_yet_verified');
                                    $last_verified_date = "";
                                    if(!empty($row->admin_approved_date)) {
                                        $title = get_languageword('last_verified');
                                        $last_verified_date = date('jS F, Y', strtotime($row->admin_approved_date));
                                    }
                            ?>
                            <li>
                                <a title="<?php echo $title; ?>" data-content="<?php echo $last_verified_date; ?>" class="red-popover" data-toggle="popover" data-placement="top" data-trigger="hover"><i class="fa fa-heart"></i></a>
                            </li>
                            <?php } ?>
                        </ul>
                        <h4 class="title"><a href="<?php echo URL_HOME_SELLER_PROFILE.'/'.$row->slug; ?>"> <?php echo $row->username; ?></a></h4>
                        <?php if(!empty($seller_rating)) { ?>
                        <ul class="user-info">
                            <?php if(!empty($seller_rating->avg_rating)) { ?>
                                <li>
                                    <div class="avg_rating" <?php echo 'data-score='.$seller_rating->avg_rating; ?> ></div>
                                </li>
                                <?php } ?>
                             <?php if(!empty($seller_rating->no_of_ratings)) { ?>
                                <li><?php  echo $seller_rating->no_of_ratings." ".get_languageword('Ratings'); ?></li>
                                <?php } ?>
                        </ul>
                        <?php } ?>
                        <?php $sellering_books = $this->home_model->get_seller_books($row->slug, 'grouped'); 
                             if(!empty($sellering_books)) {
                        ?>
                        <div><?php echo "<strong>".get_languageword('teaches')."</strong> ".character_limiter($sellering_books, 100); ?></div>
                        <?php } ?>
                        <p><?php echo character_limiter($row->profile, 400); ?> </p>

                    </div>
                </div>

            </div>
        </div>
<?php endforeach; ?>

<script src="<?php echo URL_FRONT_JS;?>jquery.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo URL_FRONT_CSS;?>main.css">
<script src="<?php echo URL_FRONT_JS;?>main.js"></script>
<link rel="stylesheet" href="<?php echo URL_FRONT_CSS;?>jquery.raty.css">
<script src="<?php echo URL_FRONT_JS;?>jquery.raty.js"></script>
<script>

    /****** Seller Avg. Rating  ******/
   $('.avg_rating').text('');
   $('div.avg_rating').raty({

    path: '<?php echo RESOURCES_FRONT;?>raty_images',
    score: function() {
      return $(this).attr('data-score');
    },
    readOnly: true,
    half: true,
   });

</script>

<?php endif; ?>