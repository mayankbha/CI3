<!-- Dashboard panel -->
<?php 
		  $user_id = $this->ion_auth->get_user_id(); 

		  $inst_id = is_inst_seller($user_id);

	?>

    <div class="dashboard-panel">
        <?php //echo $message;?>
            <div class="row">

                    <div class="col-md-12 pad10">
                        <a href="<?php echo URL_SELLER_LIST_SELLING_BOOKS;?>">
                        <div class="dash-block d-block1">
                            <h2><?php echo $seller_dashboard_data['books'];?></strong></h2>
                            <p><?php echo get_languageword('Total_Books');?></p>
                        </div>
                        </a>
                    </div>

                    <div class="col-md-4 pad10">
                        <a href="<?php echo URL_SELLER_CREDITS_TRANSACTION_HISTORY; ?>">
                        <div class="dash-block d-block4">
                            <h2>
                                <?php 
                                    $query = $this->db->query("SELECT SUM(item_price) AS total FROM `pre_book_purchases` WHERE seller_id = '$user_id' ");
                                    $sale = $query->row();
                                    // print_r($sale);

                                    if ($sale->total > 0 ) {
                                         echo get_system_settings('currency_symbol').' '.$sale->total;
                                         $total_sale = $sale->total; 
                                     } else{
                                        echo get_system_settings('currency_symbol').' '."0";
                                        $total_sale = '0';
                                     } 
                                ?>
                            </h2>
                            <p>Total Sale</p>
                        </div>
                    </a>
                    </div>

                    <div class="col-md-4 pad10">
                        <a href="<?php echo URL_SELLER_PURCHASED_BOOKS; ?>">
                        <div class="dash-block d-block4">
                            <h2>
                                <?php 
                                    $query = $this->db->query("SELECT SUM(admin_commission_val) AS total FROM `pre_book_purchases` WHERE seller_id = '$user_id' ");
                                    $deduction = $query->row();
                                    // print_r($deduction);

                                    if ($deduction->total > 0 ) {
                                         echo get_system_settings('currency_symbol').' '.$deduction->total; 
                                         $deduction =$deduction->total;
                                     } else{
                                        echo $deduction = get_system_settings('currency_symbol').' '."0";
                                        $deduction = '0';
                                     } 
                                ?>
                            </h2>
                            <p>Net Admin + Transaction Fee <?// = '@ '.$this->config->item('site_settings')->admin_commission_on_book_purchase.'%';?></p>
                        </div>
                    </a>
                    </div>

                    <div class="col-md-4 pad10">
                        <a href="<?php echo URL_SELLER_CREDITS_TRANSACTION_HISTORY; ?>">
                        <div class="dash-block d-block4">
                            <h2>
                                <?php 
                                    
                                    $net_sale = $total_sale - $deduction;

                                    if ($net_sale > 0 ) {
                                         echo get_system_settings('currency_symbol').' '.$net_sale; 
                                     } else{
                                        echo get_system_settings('currency_symbol').' '."0";
                                     }
                                ?>
                            </h2>
                            <p>Net Sale</p>
                        </div>
                    </a>
                    </div>

                    <div class="col-md-6 pad10">
                        <a href="<?php echo URL_SELLER_CREDITS_TRANSACTION_HISTORY;?>">
                        <div class="dash-block d-block3">
                            <h2><strong><?php echo number_format(get_user_credits($user_id),0);?></strong></h2>
                            <p>Net Credit</p>
                        </div>
                        </a>
                    </div>
                    <div class="col-md-6 pad10">
                        <a href="<?php echo URL_SELLER_CREDITS_TRANSACTION_HISTORY;?>">
                        <div class="dash-block d-block3">
                            <h2><strong><?= get_system_settings('currency_symbol').get_user_credit_sum();?></strong></h2>
                            <p>Money In Wallet</p>
                        </div>
                        </a>
                    </div>
                    <br>
                    
                    

            </div>

    </div>
    <!-- Dashboard panel ends -->
