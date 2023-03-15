    <!-- User Profile Details -->
    <?php  if(!empty($seller_details)) {
            foreach ($seller_details as $row) {
     ?>
    <div class="container">
        <div class="row-margin ">

            <?php echo $this->session->flashdata('message'); ?>

            <div class="box-border">
                <div class="row ">
                    <!-- User Profile -->
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                        <div class="user-profile-pic">
                            <img src="<?php echo get_seller_img($row->photo, $row->gender); ?>" alt="<?php echo $row->username; ?>" class="img-responsive img-circle">
                        </div>
                        <?php echo get_user_online_status($row->is_online); ?>
                    </div>
                    <div class="col-lg-5 col-md-5 col-sm-4 col-xs-12">
                        <div class="user-profile-content">
                            <ul class="user-badges">
                                <?php
                                      if(strcasecmp(get_system_settings('need_admin_for_seller'), 'yes') == 0) {

                                        $title = get_languageword('not_yet_verified');
                                        $last_verified_date = "";
                                        if(!empty($row->admin_approved_date)) {
                                            $title = get_languageword('last_verified:');
                                            $last_verified_date = date('jS F, Y', strtotime($row->admin_approved_date));
                                        }
                                ?>
                                <li>
                                    <a href="#" title="<?php echo $title; ?>" data-content="<?php echo $last_verified_date; ?>" class="red-popover" data-toggle="popover" data-placement="top" data-trigger="hover"><i class="fa fa-heart"></i></a>
                                </li>
                                <?php } ?>
                            </ul>
                            <h4 class="title"> <?php echo ucwords($row->username); ?></h4>
                            <p class="sub-title"><u><?php echo $row->gender.", ".calcAge($row->dob)." ".get_languageword('years');  ?></u></p>
                            <?php if(!empty($seller_raing)) { ?>
                            <ul class="user-info">
                                <?php if(!empty($seller_raing->avg_rating)) { ?>
                                <li>
                                    <div class="avg_rating" <?php echo 'data-score='.$seller_raing->avg_rating; ?> ></div>
                                </li>
                                <?php } ?>
                                <?php if(!empty($seller_raing->no_of_ratings)) { ?>
                                <li><?php  echo $seller_raing->no_of_ratings." ".get_languageword('Ratings'); ?></li>
                                <?php } ?>
                                <?php if(!empty($row->city) || !empty($row->country)) { ?>
                                <li><i class="fa fa-map-marker"></i> <?php echo $row->city.", ".$row->country; ?></li>
                                <?php } ?>
                            </ul>
                            <?php } ?>
                            <p> <?php echo $row->profile; ?> </p>
                            <hr>
                            
                            <h4><strong><?php echo get_languageword('experience'); ?>: </strong> <?php echo $row->teaching_experience." ".get_languageword('years'); ?></h4>
                            <h4><strong><?php echo get_languageword('qualification'); ?>:</strong>  <?php echo $row->qualification; ?></h4>
                            <h4><strong><?php echo get_languageword('language_of_teaching'); ?>:</strong>  <?php echo $row->language_of_teaching; ?></h4>
                             <?php if($row->academic_class != 'no' || $row->non_academic_class !='no'){?>
                            <h4><strong><?php echo get_languageword('Teaching_Class_Types'); ?>: </strong> 
                            <?php if($row->academic_class != 'no')
                                     echo get_languageword('Academic'); 

                                  if($row->non_academic_class !='no')
                                   echo ', '. get_languageword('Non_Academic'); ?></h4><?php } ?>                                
                        </div>
                    </div>
                    
                </div>
            </div>

            
            <!-- Gallery -->
            <?php if(!empty($row->seller_gallery)) { ?>
            <div class="row mtop7">
                <div class="col-sm-12">
                    <h2 class="heading-line"><?php echo get_languageword('gallery'); ?></h2>
                </div>
                <div class="col-sm-8">
                    <div class="tab-content tabpill-content">

                        <?php $i=1; foreach ($row->seller_gallery as $gallery) { ?>
                        <div id="vid<?php echo $i; ?>" class="tab-pane fade <?php if($i++ == 1) echo "active in"; ?> ">
                            <div class="my-images popup-gallery">
                                <a href="<?php echo URL_UPLOADS_GALLERY.'/'.$gallery->image_name; ?>" title="<?php echo $gallery->image_title; ?>">
                                    <img src="<?php echo URL_UPLOADS_GALLERY.'/'.$gallery->image_name; ?>" class="img-responsive" alt="">
                                </a>
                            </div>
                        </div>
                        <?php } ?>

                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="NavPillTabs">
                        <ul class=" video-tabs video-thumbs">
                            <?php $i=1; foreach ($row->seller_gallery as $gallery_thumbs) { ?>
                            <li class="<?php if($i == 1) echo 'active'; ?>">
                                <a data-toggle="pill" href="#vid<?php echo $i++; ?>">
                                    <img src="<?php echo URL_UPLOADS_GALLERY.'/thumb__'.$gallery_thumbs->image_name; ?>" alt="" class="img-responsive">
                                </a>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
            <?php } ?>

            <!--  More about Me -->
            <?php if(!empty($row->seller_experience)) { ?>
            <div class="row mtop7">
                <div class="col-sm-12">
                    <h2 class="heading-line"><?php echo get_languageword('my_experience'); ?></h2>
                    <ul class="user-more-details">
                        <?php foreach ($row->seller_experience as $exp) { ?>
                        <li>
                            <div class="media-left"><?php echo $exp->from_date." - ".$exp->to_date; ?>:</div>
                            <div class="media-body">
                                <h4><strong><?php echo $exp->company; ?></strong> - <?php echo $exp->role; ?></h4> 
                                <?php echo $exp->description; ?>
                            </div>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <?php } ?>


           

            <!-- My Reviews -->
         <?php if(!empty($seller_reviews)){?>
            <div class="row mtop7">
                <div class="col-sm-12">
                    <h2 class="heading-line"><?php echo get_languageword('My Reviews');?></h2>
                    <ul class="tree">
                        <li>
                        <?php foreach($seller_reviews as $review) { ?>
                            <!-- Single comment -->
                            <div class="media comments-list">
                            <?php
                                    $image = URL_PUBLIC_UPLOADS2.'profiles/default-buyer-female.png';
                                    if($review->gender == 'Male')
                                        $image = URL_PUBLIC_UPLOADS2.'profiles/default-buyer-male.png';
                                    if($review->photo != '' && file_exists('assets/uploads/profiles/thumbs/'.$review->photo))
                                    $image = URL_PUBLIC_UPLOADS2.'profiles/thumbs/'.$review->photo;
                            ?>
                                <div class="media-left">
                                    <img src="<?php echo $image;?>" alt="" class="comment-profile img-circle">
                                </div>
                                <div class="media-body">
                                    <h4><strong><?php echo $review->buyer_name;?></strong> On <?php echo date("jS F, Y", strtotime($review->posted_on));?>
                                        <span class="avg_rating" <?php echo 'data-score='.$review->rating; ?> ></span>
                                    </h4>
                                    <p class="time-stamp"><strong><?php echo get_languageword('Book')?>:</strong><?php echo $review->book;?> </p>
                                    <p><?php echo $review->comments;?></p>
                                </div>
                            </div>
                            <!-- Ends single comment -->
                            <?php } ?>
                           
                        </li>
                    </ul>
                </div>
            </div>
        <?php } ?>
        </div>
    </div>

    <script src="<?php echo URL_FRONT_JS;?>jquery.js"></script>
    <script>
        function get_seller_book_details()
        {
            book_slug     = $('#book_slug option:selected').val();
            selected_date   = $('#start_date').val();

            if(!book_slug || !selected_date) {
                $('#fee').text('');
                $('#duration').text('');
                $('#days_off').text('');
                $('#content_li').remove();
                $('#time_slot_div').text('<?php echo get_languageword("please_select_book_and_date_first"); ?>');
                return;
            }

            $.ajax({
                    type: "POST",
                    url: "<?php echo URL_HOME_AJAX_GET_SELLER_BOOK_DETAILS; ?>",
                    data: { "book_slug" : book_slug, "seller_id" : <?php echo $row->id; ?>, "selected_date" : selected_date },
                    cache: false,
                    beforeSend: function() {
                        $('#time_slot_div').html('<font color="#5bc0de" size="6"> Loading...</font>');
                    },
                    success: function(response) {

                        if(response == "") {
                            $('#fee').text('');
                            $('#duration').text('');
                            $('#days_off').text('');
                            $('#content_li').remove();
                            $('#time_slot_div').html('<?php echo get_languageword("no_slots_available."); ?> <a href="#"><?php echo get_languageword("click_here_to_send_me_your_message"); ?></a>');
                            $('#request_seller_btn').slideUp();
                        } else {
                            var fee_duration = response.split('~');
                            var fee          = fee_duration[0];
                            var duration     = fee_duration[1];
                            var content      = fee_duration[2];
                            var time_slots   = fee_duration[3];
                            var days_off     = fee_duration[4];

                            $('#fee').text(fee);
                            $('#duration').text('credits/'+duration);
                            if(days_off)
                                $('#days_off').text('Days off: '+days_off);

                            if(content) {
                                $('#content_li').remove();
                                $('#book_li').after('<li id="content_li"><?php echo get_languageword("book_content"); ?><p>'+content+'</p></li>');
                            }

                            time_slot_html = "";
                            if(time_slots != "")
                                time_slots = time_slots.split(',');

                            total_available_timeslots = time_slots.length;

                            if(total_available_timeslots > 0) {

                                for(i=0;i<total_available_timeslots;i++) {

                                    check_radio = "";
                                    if(i == 0)
                                        check_radio = 'checked = "checked"'; 
                                    time_slot_html += '<li><div><input id="radio1'+i+'" type="radio" name="time_slot" value="'+time_slots[i]+'" '+check_radio+' ><label for="radio1'+i+'"><span><span></span></span>'+time_slots[i]+'</label></div></li>';
                                }

                                $('#time_slot_div').html(time_slot_html);
                                $('#request_seller_btn').slideDown();

                            } else {

                                $('#time_slot_div').html('<?php echo get_languageword("no_slots_available."); ?> <a href="#"><?php echo get_languageword("click_here_to_send_me_your_message"); ?></a>');
                                 $('#request_seller_btn').slideUp();
                            }
                        }
                    }
            });

        }


        function toggle_location_chkbx()
        {
            $('input[name="teaching_type"]').removeAttr('checked');
            $('input[value="willing-to-travel"]').prop('checked',true);
        }


    </script>

    <script src="<?php echo URL_FRONT_JS;?>jquery.validate.min.js"></script>
    <script type="text/javascript"> 
      (function($,W,D)
       {
          var JQUERY4U = {};
       
          JQUERY4U.UTIL =
          {
              setupFormValidation: function()
              {

                  //form validation rules
                  $("#book_seller_form").validate({
                      rules: {
                            book_slug: {
                                required: true
                            },
                            location_slug: {
                                required: function(){
                                            return ($('input[name="teaching_type"]:checked').val() == "willing-to-travel");
                                          }
                            },
                            start_date: {
                                required: true
                            }
                      },

                      messages: {
                            book_slug: {
                                required: "<?php echo get_languageword('please_select_book'); ?>"
                            },
                            location_slug: {
                                required: "<?php echo get_languageword('please_select_location'); ?>"
                            },
                            start_date: {
                                required: "<?php echo get_languageword('please_select_date,on_which_you_want_to_start_the_book'); ?>"
                            }
                      },

                      submitHandler: function(form) {
                          form.submit();
                      }
                  });
              }
          }
             //when the dom has loaded setup form validation rules
         $(D).ready(function($) {
             JQUERY4U.UTIL.setupFormValidation();
         });
     })(jQuery, window, document);


     $(function() {

       $( "#start_date").datepicker({
           dateFormat: 'yy-mm-dd',
           defaultDate: "+1w",
           changeMonth: true,
           minDate: 0,
           onSelect: function() {
              get_seller_book_details();
           }
       });

     });


    </script>

    <link rel="stylesheet" href="<?php echo URL_FRONT_CSS;?>jquery.raty.css">
    <script src="<?php echo URL_FRONT_JS;?>jquery.raty.js"></script>
    <script>

        /****** Seller Avg. Rating  ******/
       $('div.avg_rating, span.avg_rating').raty({

        path: '<?php echo RESOURCES_FRONT;?>raty_images',
        score: function() {
          return $(this).attr('data-score');
        },
        readOnly: true
       });

       
    </script>

    <?php } } ?>
    <!-- User Profile Details  -->