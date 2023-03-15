<!-- Dashboard panel -->
<?php 
          $user_id = $this->ion_auth->get_user_id(); 

          $inst_id = is_inst_seller($user_id);

    ?>

    <div class="dashboard-panel">
        <?php //echo $message;?>
            <div class="row">

                    <div class="col-md-6 pad10">
                        <a href="<?php echo URL_BUYER_BOOK_PURCHASES;?>">
                        <div class="dash-block d-block1">
                            <h2><?php echo $buyer_dashboard_data['books'];?></strong></h2>
                            <p>Book Purchased</p>
                        </div>
                        </a>
                    </div>
                    <div class="col-md-6 pad10">
                        <a href="<?php echo URL_BUYER_BOOK_PURCHASES; ?>">
                        <div class="dash-block d-block4">
                            <h2>
                                <?php 
                                    $query = $this->db->query("SELECT SUM(api_val) AS total FROM `pre_book_purchases` WHERE user_id = '$user_id' ");
                                    $active_listings = $query->row();
                                    // print_r($active_listings);

                                    if ($active_listings->total > 0 ) {
                                         echo number_format($active_listings->total,0); 
                                     } else{
                                        echo "0";
                                     } 
                                ?>
                            </h2>
                            <p><?php echo get_languageword('Trees_Planted_So_Far');?></p>
                        </div>
                    </a>
                    </div>

                    <div class="col-md-4 pad10">
                        <a href="<?php echo URL_BUYER_BOOK_PURCHASES; ?>">
                        <div class="dash-block d-block3">
                            <h2>
                                <?php 
                                    $query = $this->db->query("SELECT SUM(total_amount) AS total FROM `pre_book_purchases` WHERE user_id = '$user_id' ");
                                    $active_listings = $query->row();
                                    // print_r($active_listings);

                                    if ($active_listings->total > 0 ) {
                                         echo get_system_settings('currency_symbol').' '.$active_listings->total; 
                                     } else{
                                        echo get_system_settings('currency_symbol').' '."0";
                                     } 
                                ?>
                            </h2>
                            <p><?php echo get_languageword('Total_Purchasing');?></p>
                        </div>
                    </a>
                    </div>

                    <div class="col-md-4 pad10">
                        <a href="<?php echo URL_SELLER_CREDITS_TRANSACTION_HISTORY;?>">
                        <div class="dash-block d-block3">
                            <h2><strong><?php echo number_format(get_user_credits($user_id),0);?></strong></h2>
                            <p>Net Credit</p>
                        </div>
                        </a>
                    </div>
                    <div class="col-md-4 pad10">
                        <a href="<?php echo URL_SELLER_CREDITS_TRANSACTION_HISTORY;?>">
                        <div class="dash-block d-block3">
                            <h2><strong><?= get_system_settings('currency_symbol').get_user_credit_sum();?></strong></h2>
                            <p>Money In Wallet</p>
                        </div>
                        </a>
                    </div>
            </div>

    </div>
    <!-- Dashboard panel ends -->
