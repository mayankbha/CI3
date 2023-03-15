<style type="text/css">
    .p-2 {
        padding: unset !important;
    }
</style>
<?php
if (!empty($selling_books)) :
    foreach ($selling_books as $row) :
?>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="lession-card">
                <a href="<?php echo URL_HOME_BUY_BOOK . '/' . $row->slug; ?>">
                    <figure class="imghvr-zoom-in">
                        <div>
                            <div class="card-img all-c">
                                <img src="data:image/jpg;base64, <?php echo get_selling_book_img($row->image); ?>" class="img-responsive" alt="">
                                
                                <figcaption></figcaption>
                            </div>
                        </div>
                    </figure>

                </a>
                <div class="card-content opc">
                    <h4 class="card-title" style="text-align: center;" title="<?php echo $row->book_name; ?>"><?php echo $row->book_name; ?>
                        <br>
                        <span class="sell-price">
                            <?php

                            $actual_price = $row->actual_price;
                            $discount_price = $row->book_price;
                            
                            if ($actual_price > 0) {
                             
                                if ($this->ion_auth->logged_in() && $this->ion_auth->is_buyer()) {
                                    $userDetails = getUserRec();
                                    $discount_price = getBuyerDiscountedPrice($row->sc_id, $userDetails->id);
                                }

                                $code = $this->config->item('site_settings')->currency_symbol;

                                $discount = ($actual_price - $discount_price) / $actual_price * 100;
                                $discount = round($discount);
                            }

                            if ($discount_price < $actual_price) {

                                echo
                                '<span style="color:red;"><del>'
                                    . $code
                                    . ' '
                                    . $row->actual_price . '</del><span>'
                                    . '<span style="color:white;"> | </span>'
                                    . '<span style="color:#2cdd9b;">'
                                    . $code
                                    . ' '
                                    . $discount_price
                                    . '</span>';
                            } elseif ($discount_price > 0) {

                                echo
                                '<span style="color:#2cdd9b;">'
                                    . $code
                                    . ' '
                                    . $discount_price
                                    . '</span>';
                            } else {
                                echo
                                '<span style="color:#2cdd9b;"> FREE </span>';
                            }
                            ?>
                            <br>
                            <?php

                            if ($discount_price < $row->actual_price) {
                                echo
                                '<span style="color:#2cdd9b;">'
                                    . $discount
                                    . '% Discount </span>';
                            } else {
                            }
                            ?>
                        </span>
                    </h4>
                    <!-- <p class="card-info animated fadeIn" title="<?php echo $row->book_title; ?>"><?php if (!empty($row->book_title)) echo $row->book_title;
                                                                                                        else echo '&nbsp'; ?></p> -->
                    <?php if ($this->config->item('site_settings')->like_comment_setting == "Yes") : ?>
                        <div class="selling_book_comment-section">

                            <div class="bg-white">

                                <div class="d-flex flex-row fs-12">
                                    <?php //var_dump($row); die;
                                    ?>
                                    <div class="like p-2 cursor <?= (in_array($this->session->userdata['user_id'] , explode(',', $row->userliked)) ? 'like_color_blue' : '') ?>" onclick="<?php if($this->ion_auth->logged_in()  && !($this->ion_auth->is_admin())) { ?> likecallback('<?= $this->session->userdata['user_id'] ?>','<?= $row->sc_id ?>') <?php } else { echo '#'; } ?>"><i class="fa fa-thumbs-o-up"></i><span class="ml-1 likeml_<?= $row->sc_id ?>"><?= (in_array($this->session->userdata['user_id'] , explode(',', $row->userliked)) ? 'Unlike' : 'Like') ?></span>
                                    </div>
                                    <div class="p-2 thumbs-up-list-page-<?= $row->sc_id ?> <?= ($row->userliked ? 'like_color_blue' : '') ?>" >
                                        <div class=" pt-2 ">
                                            <i class="fa fa-thumbs-up"></i> 
                                            <span class="ml-1">(<?= ($row->likescount?$row->likescount:0) ?>) </span>
                                        </div>
                                    </div>
                                    <!-- <div class="like p-2 cursor"><a href="<?= URL_HOME_BUY_BOOK . '/' . $row->slug; ?>#selling_book_comment-section"><i class="fa fa-commenting-o"></i><span class="ml-1">Comment</span></a></div> -->
                                    <!-- <div class="like p-2 cursor"> <a href="#" data-toggle="modal" data-target="#myModal" data-heading="Reffer '<?= $row->book_title ?>' to friend"><i class="fa fa-envelope  "></i><span class="ml-1"><?= " Refer to friend" ?></span></a></div> -->
                                </div>
                                <div class="d-flex flex-row fs-12 ">
                                    <!-- <div class="p-2 thumbs-up-list-page-<?= $row->sc_id ?> <?= ($row->userliked ? 'like_color_blue' : '') ?>" >
                                        <div class=" pt-2 ">
                                            <i class="fa fa-thumbs-up"></i> 
                                            <span class="ml-1">(<?= ($row->likescount?$row->likescount:0) ?>) </span>
                                        </div>
                                    </div> -->
                                    <div class="p-2" style="/*display: inline-flex;*/vertical-align: middle;">
                                        <a href="<?= URL_HOME_BUY_BOOK . '/' . $row->slug; ?>#selling_book_comment-section">
                                        <span class="ml-1">
                                        <?php
                                            $totalratingscount = 0;
                                            if ($row->totalratingscount > 0) {
                                                $totalratingscount = $row->totalratingscount / $row->ratingscount;
                                            }
                                            $totalratingscount = number_format((float)$totalratingscount, 1, '.', '');

                                            echo $totalratingscount;
                                        ?>
                                            
                                        </span>

                                        <?php
                                            

                                            //  var_dump($totalratingscount); die; 
                                            for ($iv = 1; $iv <= 5; $iv++) : ?>
                                               <label for="star-<?= $iv ?>">
                                                   <svg width="15" height="15" viewBox="0 0 51 48">
                                                       <?php


                                                        $fillstoke = '';
                                                        if ($iv <= $totalratingscount) {

                                                           // echo "working here" . $iv;


                                                            $fillstoke = 'fill="#FFBB00" stroke="#cc9600"';
                                                        }


                                                        ?>
                                                       <path <?= $fillstoke ?> d="m25,1 6,17h18l-14,11 5,17-15-10-15,10 5-17-14-11h18z" />
                                                   </svg>
                                               </label>
                                           <?php endfor; ?>
                                           
                                           <span class="ml-1">(<?= $row->ratingscount ?>)</span>
                                        </a>

                                    </div>
                                    
                                    

                                </div>
                                <div class="d-flex flex-row fs-12 ">
                                    <div class="p-2" style="display: inline-flex;vertical-align: middle;">
                                        
                                        <div class=" p-2 ">
                                            <div class=" pt-2 total_comments">
                                                <a href="<?= URL_HOME_BUY_BOOK . '/' . $row->slug; ?>#selling_book_comment-section"><i class="fa fa-commenting-o "></i> Comments<span class="ml-1">(<?= $row->ratingscount ?>) </span></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                        <div class="sharethis-inline-share-buttons" data-url="<?= URL_HOME_BUY_BOOK . '/' . $row->slug . "/" . urlencode(base64_encode($this->session->userdata['user_id'] . "_" . $row->sc_id)); ?>"></div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    <?php endforeach;


    //else : 
    ?>

<?php endif; ?>

<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function(event) {
        // Your code to run since DOM is loaded and ready
        //alert("test 1");
        var user_id;
        var book_id;

        if (user_id && book_id) {
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url("home/do_likecomment"); ?>',
                data: {
                    'user_id': user_id,
                    'book_id': book_id,
                },
                success: function(data) {

                    if (data.response == 'success') {

                        if (data.action == 'add') {
                            $(".likeml_" + book_id).closest(".like.p-2.cursor").addClass("like_color_blue");
                           
                            $(".likeml_" + book_id).text("Unlike");
                        } else {
                            $(".likeml_" + book_id).closest(".like.p-2.cursor").removeClass("like_color_blue");
                            $(".likeml_" + book_id).text("Like");
                        }
                        
                        alert(data.message);



                    }
                    if (data.response == 'error') {
                        alert("There is some problem please contact admin.");
                    }
                }
            });
        }
        
        
        $('#myModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var recipient = button.data('heading'); // Extract info from data-* attributes
            // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
            // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
            var modal = $(this);
            modal.find('.modal-title').text(recipient);
        });
    });


    function likecallback(user_id, book_id) {
        if (user_id && book_id) {
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url("home/do_likecomment"); ?>',
                data: {
                    'user_id': user_id,
                    'book_id': book_id,
                },
                success: function(data) {

                    if (data.response == 'success') {

                        if (data.action == 'add') {
                               $(".likeml_" + book_id).closest(".like.p-2.cursor").addClass("like_color_blue");
                               $(".likeml_" + book_id).text("Liked");
                           } else {
                               // $(".likeml_" + book_id).closest(".like.p-2.cursor").removeClass("like_color_blue");
                               // $(".likeml_" + book_id).text("Like");
                           }

                        $(".thumbs-up-list-page-" + book_id+" .ml-1").text(data.totalcount);
                       
                        alert(data.message);



                    }
                    if (data.response == 'error') {
                        alert("There is some problem please contact admin.");
                    }
                }
            });
        } else {
            location.href = 'auth/login';
        }
    }
</script>

</script type="text/javascript">

<?php if ($this->session->userdata['user_id']) : ?>
    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            $('body').on('click', '.st-btn', function() {

                var datanetwork = $(this).attr("data-network");

                var sharethisurlcustom = $(this).closest('.sharethis-inline-share-buttons').attr('data-url');

                alert("You can earn point onetime by sharing each network!");
                $.ajax({
                    url: "https://count-server.sharethis.com/v2.0/get_counts?url=" + sharethisurlcustom,
                    type: 'GET',
                    dataType: 'json', // added data type
                    success: function(shares) {
                        var totalsharercount = shares.total;
                        setInterval(
                            function() {
                                $.ajax({
                                    url: "https://count-server.sharethis.com/v2.0/get_counts?url=" + sharethisurlcustom,
                                    type: 'GET',
                                    dataType: 'json', // added data type
                                    success: function(shares) {
                                        if (shares.total > totalsharercount) {
                                            $.ajax({
                                                type: 'POST',
                                                url: '<?php echo base_url("home/do_sharerpointcredit"); ?>',
                                                data: {
                                                    'user_id': "<?= $this->session->userdata['user_id'] ?>",
                                                    'book_id': "<?= $record->sc_id ?>",
                                                    'datanetwork': datanetwork
                                                },
                                                success: function(data) {
                                                    if (data.success == 1) {
                                                        clearInterval();
                                                    }
                                                }
                                            });
                                        }

                                        totalsharercount = shares.total;
                                    }
                                });
                            }, 10000);
                    }
                });

            });

        });
    </script>

<?php else : ?>
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function(event) {

            $('body').on('click', '.st-btn', function() {

                alert("You can earn point onetime by sharing each network but you must be first logged in!");

            });
        });
    </script>
<?php endif; ?>
