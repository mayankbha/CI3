<?php $this->load->view('template/site/header', $grocery_output); ?>

<!-- Page Title Wrap  -->
<div class="page-title-wrap">
	<div class="container">
		<div class="row">
			<div class="col-sm-12">
			</div>
		</div>
	</div>
</div>
<!-- Page Title Wrap  -->

<!-- News Scroller  -->
<?php $this->load->view('template/site/scroller'); ?>
<!-- News Scroller  -->

<!-- Dashboard section  -->
<section class="dashboard-section">
	<div class="container">
		<div class="row offcanvas offcanvas-right row-margin">
			<div class="col-xs-8 col-sm-4 sidebar-offcanvas" id="sidebar">
				<?php $this->load->view('template/site/seller-template-leftmenu'); ?>
				<!-- /.panel-group -->
			</div>
			<div class="col-xs-12 col-sm-8 dashboard-content">

				<?php 

					echo $this->session->flashdata('message');

					if(!empty($seller_books)) { 

						$this->load->view('seller/my_batches', array('seller_books' => $seller_books));
					  }
				  ?>

				<div class="col-xs-8 col-sm-10">
					<!-- breadcrumb -->
					<ol class="breadcrumb dashcrumb">
						<li><a href="#">Home</a></li>
						<li class="active"> <?php if(!empty($pagetitle)) echo $pagetitle; ?></li>
						<?php if(!empty($activesubmenu)) { ?>
						<li class="active"> <?php echo get_languageword($activesubmenu); ?></li>
						<?php } ?>
					</ol>
				</div>

				<div class="col-xs-4 col-sm-2">
					<?php //if(isset($withdraw_credits_btn) && $withdraw_credits_btn) { ?>
						<a class="btn btn-info" href="<?php echo URL_SELLER_ADMIN_MONEY_REQUEST; ?>">Withdraw Credits</a>
					<?php //} ?>
				</div>

				<!-- breadcrumb ends -->
				<?php if(isset($grocery_output->output)) echo $grocery_output->output; ?>

			</div>
		</div>
	</div>
</section>
<!-- Dashboard section  -->
<?php $this->load->view('template/site/footer', $grocery_output); ?>