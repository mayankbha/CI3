    <!-- >> Blog-->
    <?php
        $actual_price = $record->actual_price;
        $discount_price = $record->book_price;
        if($this->ion_auth->logged_in() && $this->ion_auth->is_buyer() && $actual_price > 0) {
            $userDetails = getUserRec();
            $discount_price = getBuyerDiscountedPrice($record->sc_id, $userDetails->id);
        }
    ?>
    <section class="blog-content">
        <div class="container">
            <div class="row row-margin">

                <!-- Sidebar/Widgets bar -->
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <!-- Price Widget -->
                    <div class="get-video-book">
                        <!-- Sigle blog post -->
                        <div class="blog-card">
                            <div class="blog-card-img related-itm-img">

                                <img src="data:image/jpg;base64, <?php echo get_selling_book_img($record->image); ?>" class="img-responsive" style="max-height: unset;" alt="">

                            </div>
                            <p class="related-link"><a href="<?php echo URL_HOME_BUY_BOOK.'/'.$record->slug; ?>"> <?php if(!empty($record->book_name)) echo $record->book_name; ?></a></p>
                            <ul class="related-videos">
                                <li>
                                    <?php if(!empty($record->updated_at)) echo date('M jS, Y', strtotime($record->updated_at)); ?>
                                </li>
                                <li> <?php if(!empty($discount_price)) echo $this->config->item('site_settings')->currency_symbol.' '.$discount_price; ?></li>
                            </ul>
                        </div>
                        <!-- Sigle blog post Ends -->
                        <ul class="list">
                            <?php if(!empty($record->sellingbook_curriculum)) { ?>
                            <li class="list-item">
                                <span class="list-left"><?php echo get_languageword('lectures'); ?></span>
                                <span class="list-right"><?php echo count($record->sellingbook_curriculum); ?></span>
                            </li>
                            <?php } ?>
                            <?php if(!empty($record->skill_level)) { ?>
                            <li class="list-item">
                                <span class="list-left"><?php echo get_languageword('Skill_Level'); ?></span>
                                <span class="list-right"><?php echo $record->skill_level; ?></span>
                            </li>
                            <?php } ?>
                            <?php if(!empty($record->languages)) { ?>
                            <li class="list-item">
                                <span class="list-left"><?php echo get_languageword('languages'); ?></span>
                                <span class="list-right">
                                    <?php echo $record->languages; ?>
                                </span>
                            </li>
                            <?php } ?>
                            <?php if(!empty($record->max_downloads)) { ?>
                            <li class="list-item">
                                <span class="list-left"><?php echo get_languageword('Max_Downloads'); ?></span>
                                <span class="list-right"> <?php echo $record->max_downloads; ?> </span>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                    <!-- /Price Widget -->
                </div>

                <div class="col-lg-<?php echo ($this->config->item('site_settings')->enable_moretrees_api == 'YES') ? '5' : '8'; ?> col-md-<?php echo ($this->config->item('site_settings')->enable_moretrees_api == 'YES') ? '5' : '8'; ?> col-sm-<?php echo ($this->config->item('site_settings')->enable_moretrees_api == 'YES') ? '5' : '8'; ?> col-xs-12">

                    <?php echo $this->session->flashdata('message'); ?>

                    <!-- Select Payment-->
                    <div class="video-description">
                        <h2 class="heading-line"><?php echo get_languageword('Choose_Payment_Method'); ?></h2>
                        <?php echo form_open(URL_PAY, 'id="checkout_form"'); ?>
                            <div class="radio payment-methods-list" style="display: flex;">
                              <ul style="display: table;">

                                <?php 
                                        $system_currency = get_system_settings('Currency_Code');

                                        if(!empty($payment_gateways)) {

                                            foreach($payment_gateways as $gateway) {
                                              
                                    ?>
                                    <?php if (get_user_credit_sum() >= $discount_price) {?>
                                        <input type="radio" name="gateway_id" value="<?php echo $gateway->type_id;?>" <?php if($gateway->type_id == 47) echo 'checked'; ?> />
                                        
                                    <?php }else{?>
                                        
                                        <li >
                                            <label>
                                                <input type="radio" name="gateway_id" value="<?php echo $gateway->type_id; ?>" <?php if($gateway->is_default == 'Yes') echo 'checked'; ?> />
                                                <span class="radio-content">
                                                    <span class="item-content">
                                                        <?php echo $gateway->type_title?>
                                                    </span>
                                                    <i aria-hidden="true" class="fa uncheck fa-circle-thin"></i>
                                                    <i aria-hidden="true" class="fa check fa-dot-circle-o"></i>
                                                </span>
                                            </label>                      
                                        </li>
                                        <br>
                                    
                                <?php } ?>

                                <?php 
                                    } 
                                } 
                                ?>
                                </ul> 
                                <li style="width:50%;margin: 0 auto;">
                                    <ul class="list">
                                        <li class="list-item" style="width:100%;">
                                            <span class="list-left">Book Price:</span>
                                            <span class="list-right"><?php if(!empty($discount_price)) echo "&nbsp;".$this->config->item('site_settings')->currency_symbol.''.$discount_price; 
                                                
                                                $item_price = $discount_price;
                                                $sum_price = $item_price;
                                                if($this->config->item('site_settings')->enable_moretrees_api == 'YES') {
                                                    $sum_price = $item_price + 1;
                                                }

                                            ?></span>
                                        </li>
                                        <?php if($this->config->item('site_settings')->enable_moretrees_api == 'YES'): ?>
                                            <li class="list-item" style="width:100%;">
                                                <span class="list-left">Plant Tree:</span>
                                                <span class="list-right"><?php echo "&nbsp;".$this->config->item('site_settings')->currency_symbol.'1';
                                                ?></span>
                                            </li>
                                            <li class="list-item " style="width:100%;">
                                                <span class="list-leftsmall "><small>Plant Tree Reward points: <?php echo "&nbsp;".$this->config->item('site_settings')->point_system_refersplanttree; ?></small></span>
                                              
                                            </li>
                                            <?php if(get_user_credit_sum() > 0 && $item_price  > get_user_credit_sum()){ ?>
                                            <li class="list-item " style="width:100%;">
                                                <span class="list-leftsmall "><small>You have <?php echo get_system_settings('currency_symbol').get_user_credit_sum(); ?> can be use to buy books </small></span>
                                              
                                            </li>
                                            <?php } ?>
                                        <?php endif; ?>
                                        <li class="list-item" style="width:100%;">
                                            <span class="list-left">Total:</span>
                                            <span class="list-right"><?php echo "&nbsp;".$this->config->item('site_settings')->currency_symbol.''.$sum_price;
                                            ?></span>
                                        </li>
                                        <?php if(get_user_credit_sum() > 0 && $item_price  > get_user_credit_sum()){ ?>
                                         <li class="list-item" style="width:100%;">
                                            <span class="col-md-6 p-0">Partial Total After wallet deduction:</span>
                                            <?php $sum_price = ($sum_price - get_user_credit_sum()); ?>
                                            <span class="list-right"><?php echo "&nbsp;".$this->config->item('site_settings')->currency_symbol.''.$sum_price; ?></span>
                                        </li>
                                        <?php } ?>
                                        
                                    </ul>
                                </li>

                            </div>

                            <?php if(!empty($record->sc_id)) { ?>
                                <input type="hidden" name="sc_id" value="<?php echo $record->sc_id; ?>" />
                            <?php } ?>                     

                            <div class="mtop2 " style="display:flex;justify-content:center;align-items:center;">
                                <div class="mobile-effect"><button type="submit" class="btn btn-secondary pb-pay-amount "><?php echo get_languageword('Pay').' '.$this->config->item('site_settings')->currency_symbol.''.$sum_price; ?><?= (get_user_credit_sum() >= $item_price )?" from wallet":"" ?></button></div>
                            </div>
                            <div class="mtop2 " style="display:flex;justify-content:center;align-items:center;">
                                <p class="pb-payment-terms"><?php echo get_languageword('By placing the order You have read and agreed to our'); ?>
                                    <a href="<?php echo SITEURL2.'/terms-and-conditions'; ?>" target="_blank"><?php echo get_languageword('Terms and conditions'); ?></a>
                                        &
                                    <a href="<?php echo SITEURL2.'/privacy-policy'; ?>"  target="_blank"><?php echo get_languageword(' Privacy Policy'); ?></a>.
                                </p>
                            </div>
                        </form>
                    </div>
                    <!-- /Select Payment-->


                </div>
                
                <!-- Sidebar/Widgets bar -->
                <?php if($this->config->item('site_settings')->enable_moretrees_api == 'YES'): ?>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <!-- Price Widget -->
                        <div class="get-video-book">
                            <!-- Sigle blog post -->
                            <div class="blog-card">
                                <div class="blog-card-img related-itm-img">
                                    <a href="https://moretrees.eco/forest/eyeniversum/" target="_blank" >
                                        <img src="http://eyebooks.co/assets/front/images/mt-example.jpg" alt="" class="img-responsive">
                                    </a>
                                </div>
                                <p><b>Awesome!</b> You're going to plant a tree automatically when you buy this book, A simple way to plant trees for our planet to lower down the carbon foot print in £ 1</p>
                                
                                <h4>A Special Thanks To Our Partner <a href="https://moretrees.eco/benefits/" target="_blank" >More Trees</a></h4>
                                <!-- <img src="http://eyebooks.co/assets/front/images/mt.png" alt="" class="img-responsive" style="background: black;"> -->
                                    <!-- HTML to show moretrees stats -->
                                <div>
                                   
                                </div>
                                
                                <!-- <p class="related-link">

                                    <a href="<?php echo URL_HOME_BUY_BOOK.'/'.$record->slug; ?>"> 
                                    <?php if(!empty($record->book_name)) echo $record->book_name; ?> - <?php if(!empty($record->book_title)) echo $record->book_title; ?> - <?php if(!empty($record->username)) echo $record->username; ?> 
                                    </a>
                                </p>

                                <ul class="related-videos">
                                    <li>
                                        <?php if(!empty($record->updated_at)) echo date('M jS, Y', strtotime($record->updated_at)); ?>
                                    </li>
                                    <li> <?php if(!empty($discount_price)) echo $this->config->item('site_settings')->currency_symbol.' '.$discount_price; ?></li>
                                </ul> -->
                            </div>
                            <!-- Sigle blog post Ends -->
                        <!--  <ul class="list">
                                <li class="list-item">
                                    A simple way to plant trees for our planet
                                </li>
                                
                            </ul> -->
                        </div>
                        <!-- /Price Widget -->
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
