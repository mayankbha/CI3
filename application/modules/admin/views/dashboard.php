<!-- Main content -->
<section class="content">

  <div class="row">

    <div class="col-md-3 col-lg-3  col-sm-12 col-xs-12" >
      <div class="small-box bg-blue">
        <div class="inner"> 
          <h3 >
            <?php 
                $query = $this->db->query("SELECT count(*) as total FROM `pre_users` where user_belongs_group = 2 ");
                $active_listings = $query->row(); 
                if ($active_listings->total > 0 ) {
                     echo $active_listings->total; 
                 } else{
                    echo "0";
                 } 
            ?>
          </h3>
          <p>Buyers</p>
        </div>
        <div class="icon">
          <i class="fa fa-users"></i>
        </div>
        <a href="<?php echo site_url('auth/index/2'); ?>" class="small-box-footer"><?php echo 'Go' ; ?> <i class="fa fa-arrow-circle-right"></i></a>

      </div>          
    </div>


    <div class="col-md-3 col-lg-3 col-sm-12 col-xs-12">
     <div class="small-box bg-blue">
      <div class="inner"> 
        <h3 >
          <?php 
                $query = $this->db->query("SELECT count(*) as total FROM `pre_users` where user_belongs_group = 3 ");
                $active_listings = $query->row(); 
                if ($active_listings->total > 0 ) {
                     echo $active_listings->total; 
                 } else{
                    echo "0";
                 } 
            ?>
        </h3>
        <p>Sellers</p>
      </div>
      <div class="icon">
        <i class="fa fa-users"></i>
      </div>
      <a href="<?php echo site_url('auth/index/3'); ?>" class="small-box-footer"><?php echo 'Go' ; ?> <i class="fa fa-arrow-circle-right"></i></a>
    </div>  
  </div>

  <div class="col-md-3 col-lg-3 col-sm-12 col-xs-12">
   <div class="small-box bg-blue">
    <div class="inner"> 
      <h3 >
        <?php 
                $query = $this->db->query("SELECT count(*) as total FROM `pre_seller_selling_books`");
                $active_listings = $query->row(); 
                if ($active_listings->total > 0 ) {
                     echo $active_listings->total; 
                 } else{
                    echo "0";
                 } 
            ?>
      </h3>
      <p>Total Book(s)</p>
    </div>
    <div class="icon">
      <i class="fa fa-cubes"></i>
    </div>
    <a href="<?php echo site_url('seller/list-selling-books'); ?>" class="small-box-footer"><?php echo 'Go' ; ?> <i class="fa fa-arrow-circle-right"></i></a>
    </div>  
    </div>

    <div class="col-md-3 col-lg-3 col-sm-12 col-xs-12" >
     <div class="small-box bg-blue">
      <div class="inner"> 
        <h3 >
         <?php 
                $query = $this->db->query("SELECT count(*) as total FROM `pre_seller_selling_books` where admin_approved like '%yes%' ");
                $active_listings = $query->row(); 
                if ($active_listings->total > 0 ) {
                     echo $active_listings->total; 
                 } else{
                    echo "0";
                 } 
            ?>
        </h3>
        <p>Approved Book(s)</p>
      </div>
      <div class="icon">
        <i class="fa fa-cube"></i>
      </div>
      <a href="<?php echo site_url('seller/list-selling-books'); ?>" class="small-box-footer"><?php echo 'Go' ; ?> <i class="fa fa-arrow-circle-right"></i></a>
    </div>  
    </div>
</div>

<div class="row">

<div class="col-md-3 col-lg-3 col-sm-6 col-xs-6" >
 <div class="small-box bg-blue">
  <div class="inner"> 
    <h3 >
     <?php 
        $query = $this->db->query("SELECT SUM(round(total_amount,0)) as total FROM `pre_book_purchases` where payment_status like '%Completed%' ");
               $active_listings = $query->row();
                if ($active_listings->total > 0 ) {
                     echo $active_listings->total; 
                 } else{
                    echo "0";
                 } 
            ?>
    </h3>
    <p>Total Sale</p>
  </div>
  
  <a href="<?php echo site_url('admin/view-purchased-books'); ?>" class="small-box-footer"><?php echo 'Go' ; ?> <i class="fa fa-arrow-circle-right"></i></a>
</div>  
</div>

<div class="col-md-3 col-lg-3 col-sm-6 col-xs-6">
 <div class="small-box bg-blue">
  <div class="inner"> 
    <h3 >
   <?php 
        $query = $this->db->query("SELECT SUM(admin_commission_val) as total FROM `pre_book_purchases` where status_of_payment_to_seller like '%Pending%' ");
               $active_listings = $query->row();
                if ($active_listings->total > 0 ) {
                     echo $active_listings->total; 
                 } else{
                    echo "0";
                 } 
            ?>
    </h3>
    <p>Admin Share</p>
  </div>
  
  <a href="<?php echo site_url('admin/view-purchased-books'); ?>" class="small-box-footer"><?php echo 'GO' ; ?> <i class="fa fa-arrow-circle-right"></i></a>
</div>  
</div>

<div class="col-md-3 col-lg-3 col-sm-6 col-xs-6">
 <div class="small-box bg-blue">
  <div class="inner"> 
    <h3 >
   <?php 
        $query = $this->db->query("SELECT SUM(no_of_credits_to_be_converted) as total FROM `pre_admin_money_transactions` where status_of_payment like '%Pending%' ");
               $active_listings = $query->row();
                if ($active_listings->total > 0 ) {
                     echo $active_listings->total; 
                 } else{
                    echo "0";
                 } 
            ?>
    </h3>
    <p>Seller Money Withdraw (Pending)</p>
  </div>
  
   <a href='<?php echo URL_ADMIN_SELLER_MONEY_CONVERSION_REQUESTS."/Pending";?>' class="small-box-footer"><?php echo 'GO' ; ?> <i class="fa fa-arrow-circle-right"></i></a>
</div>  
</div>

<div class="col-md-3 col-lg-3 col-sm-6 col-xs-6">
 <div class="small-box bg-blue">
  <div class="inner"> 
    <h3 >
   <?php 
        $query = $this->db->query("SELECT SUM(no_of_credits_to_be_converted) as total FROM `pre_admin_money_transactions` where status_of_payment like '%Done%' ");
               $active_listings = $query->row();
                if ($active_listings->total > 0 ) {
                     echo $active_listings->total; 
                 } else{
                    echo "0";
                 } 
            ?>
    </h3>
    <p>Seller Money Withdraw (Done)</p>
  </div>
  
   <a href='<?php echo URL_ADMIN_SELLER_MONEY_CONVERSION_REQUESTS."/Done";?>' class="small-box-footer"><?php echo 'GO' ; ?> <i class="fa fa-arrow-circle-right"></i></a>
</div>  
</div>

<?php if($this->config->item('site_settings')->enable_moretrees_api == 'YES'){ ?>
  <div class="col-md-4 col-lg-4 col-sm-6 col-xs-6" >
    <div class="small-box bg-blue">
      <div class="inner"> 
        <h3 >
        <?php echo (isset($treeCredits)) ? $treeCredits : 0; ?>
        </h3>
        <p>More Trees API Credits(Nos.)</p>
      </div>
      
      <a href="<?php echo site_url('settings/fieldsvalues/1'); ?>" class="small-box-footer"><?php echo 'Go' ; ?> <i class="fa fa-arrow-circle-right"></i></a>
    </div>  
  </div>
  <div class="col-md-4 col-lg-4 col-sm-6 col-xs-6" >
    <div class="small-box bg-blue">
      <div class="inner"> 
        <h3 >
          <?php 
            
            $query = $this->db->query("SELECT SUM(api_val) AS total FROM `pre_book_purchases` WHERE moretrees_success = 0 ");
            $active_listings = $query->row();
            // print_r($active_listings);

            if ($active_listings->total > 0 ) {

                 echo number_format($active_listings->total,0); 

             } else {

                echo "0";
             }
          ?>
        </h3>
        <p>Trees Planted (Pending)</p>
      </div>
      
      <a href="<?php echo site_url('settings/fieldsvalues/1'); ?>" class="small-box-footer"><?php echo 'Go' ; ?> <i class="fa fa-arrow-circle-right"></i></a>
    </div>  
  </div>
  <div class="col-md-4 col-lg-4 col-sm-6 col-xs-6" >
    <div class="small-box bg-blue">
      <div class="inner"> 
        <h3 >
          <?php 
            
            $query = $this->db->query("SELECT SUM(api_val) AS total FROM `pre_book_purchases` WHERE moretrees_success = 1 ");
            $active_listings = $query->row();
            // print_r($active_listings);

            if ($active_listings->total > 0 ) {

                 echo number_format($active_listings->total,0); 

             } else {

                echo "0";
             }
          ?>
        </h3>
        <p>Trees Planted (Done)</p>
      </div>
      
      <a href="<?php echo site_url('settings/fieldsvalues/1'); ?>" class="small-box-footer"><?php echo 'Go' ; ?> <i class="fa fa-arrow-circle-right"></i></a>
    </div>  
  </div>
<?php } ?>


</div>


<script>
  $("#dashboard1").addClass('active');
</script>


<style type="text/css">
  .bg-gray {
    color:#000;
    background-color:#d2d6de!important
  }
  .bg-black {
    background-color:#111!important
  }
  .alert-danger,.alert-error,.bg-red,.callout.callout-danger,.label-danger,.modal-danger .modal-body {
    background-color:#dd4b39!important
  }
  .alert-warning,.bg-yellow,.callout.callout-warning,.label-warning,.modal-warning .modal-body {
    background-color:#f39c12!important
  }
  .alert-info,.bg-aqua,.callout.callout-info,.label-info,.modal-info .modal-body {
    background-color:#00c0ef!important
  }
  .bg-blue {
    background-color:#93b2ea!important
  }
  .bg-light-blue,.label-primary,.modal-primary .modal-body {
    background-color:#3c8dbc!important
  }
  .alert-success,.bg-green,.callout.callout-success,.label-success,.modal-success .modal-body {
    background-color:#00a65a!important
  }
  .bg-navy {
    background-color:#001f3f!important
  }
  .bg-teal {
    background-color:#39cccc!important
  }
  .bg-olive {
    background-color:#3d9970!important
  }
  .bg-lime {
    background-color:#01ff70!important
  }
  .bg-orange {
    background-color:#ff851b!important
  }
  .bg-fuchsia {
    background-color:#f012be!important
  }
  .bg-purple {
    background-color:#605ca8!important
  }
  .bg-maroon {
    background-color:#d81b60!important
  }
  .bg-gray-active {
    color:#000;
    background-color:#b5bbc8!important
  }
  .bg-black-active {
    background-color:#000!important
  }
  .bg-red-active,.modal-danger .modal-footer,.modal-danger .modal-header {
    background-color:#d33724!important
  }
  .bg-yellow-active,.modal-warning .modal-footer,.modal-warning .modal-header {
    background-color:#db8b0b!important
  }
  .bg-aqua-active,.modal-info .modal-footer,.modal-info .modal-header {
    background-color:#00a7d0!important
  }
  .bg-blue-active {
    background-color:#005384!important
  }
  .bg-light-blue-active,.modal-primary .modal-footer,.modal-primary .modal-header {
    background-color:#357ca5!important
  }
  .bg-green-active,.modal-success .modal-footer,.modal-success .modal-header {
    background-color:#008d4c!important
  }
  .bg-navy-active {
    background-color:#001a35!important
  }
  .bg-teal-active {
    background-color:#30bbbb!important
  }
  .bg-olive-active {
    background-color:#368763!important
  }
  .bg-lime-active {
    background-color:#00e765!important
  }
  .bg-orange-active {
    background-color:#ff7701!important
  }
  .bg-fuchsia-active {
    background-color:#db0ead!important
  }
  .bg-purple-active {
    background-color:#555299!important
  }
  .bg-maroon-active {
    background-color:#ca195a!important
  }
  [class^=bg-].disabled {
    opacity:.65;
    filter:alpha(opacity=65)
  }
  .text-red {
    color:#dd4b39!important
  }
  .text-yellow {
    color:#f39c12!important
  }
  .text-aqua {
    color:#00c0ef!important
  }
  .text-blue {
    color:#0073b7!important
  }
  .text-black {
    color:#111!important
  }
  .text-light-blue {
    color:#3c8dbc!important
  }
  .text-green {
    color:#00a65a!important
  }
  .text-gray {
    color:#d2d6de!important
  }
  .text-navy {
    color:#001f3f!important
  }
  .text-teal {
    color:#39cccc!important
  }
  .text-olive {
    color:#3d9970!important
  }
  .text-lime {
    color:#01ff70!important
  }
  .text-orange {
    color:#ff851b!important
  }
  .text-fuchsia {
    color:#f012be!important
  }
  .text-purple {
    color:#605ca8!important
  }
  .text-maroon {
    color:#d81b60!important
  }
  .hide {
    display:none!important
  }
  .no-border {
    border:0!important
  }
  .no-padding {
    padding:0!important
  }
  .no-margin {
    margin:0!important
  }
  .no-shadow {
    box-shadow:none!important
  }
  .chart-legend,.contacts-list,.list-unstyled,.mailbox-attachments,.users-list {
    list-style:none;
    margin:0;
    padding:0
  }
  .flat {
    border-radius:0!important
  }
  .text-bold,.text-bold.table td,.text-bold.table th {
    font-weight:700
  }
  .jqstooltip {
    padding:5px!important;
    width:auto!important;
    height:auto!important
  }
  .bg-teal-gradient {
    background:#39cccc!important;
    background:-webkit-gradient(linear,left bottom,left top,color-stop(0,#39cccc),color-stop(1,#7adddd))!important;
    background:-ms-linear-gradient(bottom,#39cccc,#7adddd)!important;
    background:-moz-linear-gradient(center bottom,#39cccc 0,#7adddd 100%)!important;
    background:-o-linear-gradient(#7adddd,#39cccc)!important;
    filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#7adddd', endColorstr='#39cccc', GradientType=0)!important;
    color:#fff
  }
  .bg-light-blue-gradient {
    background:#3c8dbc!important;
    background:-webkit-gradient(linear,left bottom,left top,color-stop(0,#3c8dbc),color-stop(1,#67a8ce))!important;
    background:-ms-linear-gradient(bottom,#3c8dbc,#67a8ce)!important;
    background:-moz-linear-gradient(center bottom,#3c8dbc 0,#67a8ce 100%)!important;
    background:-o-linear-gradient(#67a8ce,#3c8dbc)!important;
    filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#67a8ce', endColorstr='#3c8dbc', GradientType=0)!important;
    color:#fff
  }
  .bg-blue-gradient {
    background:#0073b7!important;
    background:-webkit-gradient(linear,left bottom,left top,color-stop(0,#0073b7),color-stop(1,#0089db))!important;
    background:-ms-linear-gradient(bottom,#0073b7,#0089db)!important;
    background:-moz-linear-gradient(center bottom,#0073b7 0,#0089db 100%)!important;
    background:-o-linear-gradient(#0089db,#0073b7)!important;
    filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#0089db', endColorstr='#0073b7', GradientType=0)!important;
    color:#fff
  }
  .bg-aqua-gradient {
    background:#00c0ef!important;
    background:-webkit-gradient(linear,left bottom,left top,color-stop(0,#00c0ef),color-stop(1,#14d1ff))!important;
    background:-ms-linear-gradient(bottom,#00c0ef,#14d1ff)!important;
    background:-moz-linear-gradient(center bottom,#00c0ef 0,#14d1ff 100%)!important;
    background:-o-linear-gradient(#14d1ff,#00c0ef)!important;
    filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#14d1ff', endColorstr='#00c0ef', GradientType=0)!important;
    color:#fff
  }
  .bg-yellow-gradient {
    background:#f39c12!important;
    background:-webkit-gradient(linear,left bottom,left top,color-stop(0,#f39c12),color-stop(1,#f7bc60))!important;
    background:-ms-linear-gradient(bottom,#f39c12,#f7bc60)!important;
    background:-moz-linear-gradient(center bottom,#f39c12 0,#f7bc60 100%)!important;
    background:-o-linear-gradient(#f7bc60,#f39c12)!important;
    filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#f7bc60', endColorstr='#f39c12', GradientType=0)!important;
    color:#fff
  }
  .bg-purple-gradient {
    background:#605ca8!important;
    background:-webkit-gradient(linear,left bottom,left top,color-stop(0,#605ca8),color-stop(1,#9491c4))!important;
    background:-ms-linear-gradient(bottom,#605ca8,#9491c4)!important;
    background:-moz-linear-gradient(center bottom,#605ca8 0,#9491c4 100%)!important;
    background:-o-linear-gradient(#9491c4,#605ca8)!important;
    filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#9491c4', endColorstr='#605ca8', GradientType=0)!important;
    color:#fff
  }
  .bg-green-gradient {
    background:#00a65a!important;
    background:-webkit-gradient(linear,left bottom,left top,color-stop(0,#00a65a),color-stop(1,#00ca6d))!important;
    background:-ms-linear-gradient(bottom,#00a65a,#00ca6d)!important;
    background:-moz-linear-gradient(center bottom,#00a65a 0,#00ca6d 100%)!important;
    background:-o-linear-gradient(#00ca6d,#00a65a)!important;
    filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#00ca6d', endColorstr='#00a65a', GradientType=0)!important;
    color:#fff
  }
  .bg-red-gradient {
    background:#dd4b39!important;
    background:-webkit-gradient(linear,left bottom,left top,color-stop(0,#dd4b39),color-stop(1,#e47365))!important;
    background:-ms-linear-gradient(bottom,#dd4b39,#e47365)!important;
    background:-moz-linear-gradient(center bottom,#dd4b39 0,#e47365 100%)!important;
    background:-o-linear-gradient(#e47365,#dd4b39)!important;
    filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#e47365', endColorstr='#dd4b39', GradientType=0)!important;
    color:#fff
  }
  .bg-black-gradient {
    background:#111!important;
    background:-webkit-gradient(linear,left bottom,left top,color-stop(0,#111),color-stop(1,#2b2b2b))!important;
    background:-ms-linear-gradient(bottom,#111,#2b2b2b)!important;
    background:-moz-linear-gradient(center bottom,#111 0,#2b2b2b 100%)!important;
    background:-o-linear-gradient(#2b2b2b,#111)!important;
    filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#2b2b2b', endColorstr='#111111', GradientType=0)!important;
    color:#fff
  }
  .bg-maroon-gradient {
    background:#d81b60!important;
    background:-webkit-gradient(linear,left bottom,left top,color-stop(0,#d81b60),color-stop(1,#e73f7c))!important;
    background:-ms-linear-gradient(bottom,#d81b60,#e73f7c)!important;
    background:-moz-linear-gradient(center bottom,#d81b60 0,#e73f7c 100%)!important;
    background:-o-linear-gradient(#e73f7c,#d81b60)!important;
    filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#e73f7c', endColorstr='#d81b60', GradientType=0)!important;
    color:#fff
  }
  .small-box {
    border-radius:2px;
    position:relative;
    display:block;
    margin-bottom:20px
  }
  .small-box>.inner {
    padding:20px
  }
  .small-box>.small-box-footer {
    position:relative;
    text-align:center;
    padding:3px 0;
    color:#fff;
    color:rgba(255,255,255,.8);
    display:block;
    z-index:10;
    background:rgba(0,0,0,.1);
    text-decoration:none
  }
  .small-box>.small-box-footer:hover {
    color:#fff;
    background:rgba(0,0,0,.15)
  }
  .small-box h3 {
    font-size:38px;
    font-weight:700;
    margin:0 0 10px;
    white-space:nowrap;
    padding:0;
  }
  .small-box p {
    font-size:15px
  }
  .small-box p>small {
    display:block;
    color:#f9f9f9;
    font-size:13px;
    margin-top:5px
  }
  .small-box h3,.small-box p {
    z-index:5px;
    color: white;
  }
  .small-box .icon {
    -webkit-transition:all .3s linear;
    -o-transition:all .3s linear;
    transition:all .3s linear;
    position:absolute;
    top:10px;
    right:10px;
    z-index:0;
    font-size:50px;
    color:rgba(0,0,0,.15)
  }
  .small-box:hover {
    text-decoration:none;
    color:#f9f9f9
  }
  .small-box:hover .icon {
    font-size:95px
  }
  @media (max-width:767px) {
    .small-box {
      text-align:center
    }
    .small-box .icon {
      display:none
    }
    .small-box p {
      font-size:12px
    }
  }
  .box {
    position:relative;
    border-radius:3px;
    background:#fff;
    border-top:3px solid #d2d6de;
    margin-bottom:20px;
    width:100%
  }
  .box.box-primary {
    border-top-color:#3c8dbc
  }
  .box.box-info {
    border-top-color:#00c0ef
  }
  .box.box-danger {
    border-top-color:#dd4b39
  }
  .box.box-warning {
    border-top-color:#f39c12
  }
  .box.box-success {
    border-top-color:#00a65a
  }
  .box.box-default {
    border-top-color:#d2d6de
  }
  .box.collapsed-box .box-body,.box.collapsed-box .box-footer {
    display:none
  }
  .box .nav-stacked>li {
    border-bottom:1px solid #f4f4f4;
    margin:0
  }
  .box .nav-stacked>li:last-of-type {
    border-bottom:none
  }
  .box.height-control .box-body {
    max-height:300px;
    overflow:auto
  }
  .box .border-right {
    border-right:1px solid #f4f4f4
  }
  .box .border-left {
    border-left:1px solid #f4f4f4
  }
  .box.box-solid {
    border-top:0
  }
  .box.box-solid>.box-header .btn.btn-default {
    background:0 0
  }
  .box.box-solid>.box-header .btn:hover,.box.box-solid>.box-header a:hover {
    background:rgba(0,0,0,.1)!important
  }
  .box.box-solid.box-default {
    border:1px solid #d2d6de
  }
  .box.box-solid.box-default>.box-header {
    color:#444;
    background:#d2d6de
  }
  .box.box-solid.box-default>.box-header .btn,.box.box-solid.box-default>.box-header a {
    color:#444
  }
  .box.box-solid.box-primary {
    border:1px solid #3c8dbc
  }
  .box.box-solid.box-primary>.box-header {
    color:#fff;
    background:#3c8dbc
  }
  .box.box-solid.box-primary>.box-header .btn,.box.box-solid.box-primary>.box-header a {
    color:#fff
  }
  .box.box-solid.box-info {
    border:1px solid #00c0ef
  }
  .box.box-solid.box-info>.box-header {
    color:#fff;
    background:#00c0ef
  }
  .box.box-solid.box-info>.box-header .btn,.box.box-solid.box-info>.box-header a {
    color:#fff
  }
  .box.box-solid.box-danger {
    border:1px solid #dd4b39
  }
  .box.box-solid.box-danger>.box-header {
    color:#fff;
    background:#dd4b39
  }
  .box.box-solid.box-danger>.box-header .btn,.box.box-solid.box-danger>.box-header a {
    color:#fff
  }
  .box.box-solid.box-warning {
    border:1px solid #f39c12
  }
  .box.box-solid.box-warning>.box-header {
    color:#fff;
    background:#f39c12
  }
  .box.box-solid.box-warning>.box-header .btn,.box.box-solid.box-warning>.box-header a {
    color:#fff
  }
  .box.box-solid.box-success {
    border:1px solid #00a65a
  }
  .box.box-solid.box-success>.box-header {
    color:#fff;
    background:#00a65a
  }
  .box.box-solid.box-success>.box-header .btn,.box.box-solid.box-success>.box-header a {
    color:#fff
  }
  .box.box-solid>.box-header>.box-tools .btn {
    border:0;
    box-shadow:none
  }
  .box.box-solid[class*=bg]>.box-header {
    color:#fff
  }
  .box .box-group>.box {
    margin-bottom:5px
  }
  .box .knob-label {
    text-align:center;
    color:#333;
    font-weight:100;
    font-size:12px;
    margin-bottom:.3em
  }
  .box>.loading-img,.box>.overlay,.overlay-wrapper>.loading-img,.overlay-wrapper>.overlay {
    position:absolute;
    top:0;
    left:0;
    width:100%;
    height:100%
  }
  .box .overlay,.overlay-wrapper .overlay {
    z-index:50;
    background:rgba(255,255,255,.7);
    border-radius:3px
  }
  .box .overlay>.fa,.overlay-wrapper .overlay>.fa {
    position:absolute;
    top:50%;
    left:50%;
    margin-left:-15px;
    margin-top:-15px;
    color:#000;
    font-size:30px
  }
  .box .overlay.dark,.overlay-wrapper .overlay.dark {
    background:rgba(0,0,0,.5)
  }
  .box-body:after,.box-body:before,.box-footer:after,.box-footer:before,.box-header:after,.box-header:before {
    content:" ";
    display:table
  }
  .box-body:after,.box-footer:after,.box-header:after {
    clear:both
  }
  .box-header {
    color:#444;
    display:block;
    padding:10px;
    position:relative
  }
  .box-header.with-border {
    border-bottom:1px solid #f4f4f4
  }
  .collapsed-box .box-header.with-border {
    border-bottom:none
  }
  .box-header .box-title,.box-header>.fa,.box-header>.glyphicon,.box-header>.ion {
    display:inline-block;
    font-size:18px;
    margin:0;
    line-height:1
  }
  .box-header>.fa,.box-header>.glyphicon,.box-header>.ion {
    margin-right:5px
  }
  .box-header>.box-tools {
    position:absolute;
    right:10px;
    top:5px
  }
  .box-header>.box-tools [data-toggle=tooltip],.timeline {
    position:relative
  }
  .box-header>.box-tools.pull-right .dropdown-menu {
    right:0;
    left:auto
  }
  .btn-box-tool {
    padding:5px;
    font-size:12px;
    background:0 0;
    box-shadow:none!important;
    color:#97a0b3
  }
  .btn-box-tool:hover,.open .btn-box-tool {
    color:#606c84
  }
  .btn-box-tool:active {
    outline:0!important
  }
  .box-body {
    padding:10px;
    border-radius:0 0 3px 3px
  }
  .no-header .box-body {
    border-top-right-radius:3px;
    border-top-left-radius:3px
  }
  .box-body>.table {
    margin-bottom:0
  }
  .box-body .fc {
    margin-top:5px
  }
  .box-body .full-width-chart {
    margin:-19px
  }
  .box-body.no-padding .full-width-chart {
    margin:-9px
  }
  .box-body .box-pane {
    border-radius:0 0 0 3px
  }
  .box-body .box-pane-right {
    border-radius:0 0 3px
  }
  .box-footer {
    border-top:1px solid #f4f4f4;
    padding:10px;
    background-color:#fff;
    border-radius:0 0 3px 3px
  }
</style>
