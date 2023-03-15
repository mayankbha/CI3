<ul class="sidebar-menu">

	<li <?php if(isset($activemenu) && $activemenu == 'dashboard') echo ' class="active"';?>>
		<a href='<?php echo URL_ADMIN_INDEX;?>'>
			<i class='fa fa-home'></i> <?php echo get_languageword('Dashboard');?></a>
		</li>

		<li class='treeview <?php if(isset($activemenu) && $activemenu == 'users') echo 'active';?>'>
			<a href='#'>
				<i class='fa fa-users'></i> <?php echo get_languageword('Users')?> <i class='fa fa-angle-left pull-right'></i>
			</a>
			<ul class='treeview-menu'>

				

				<li <?php if(isset($activesubmenu) && $activesubmenu == '2') echo ' class="active"';?>>
					<a href='<?php echo URL_AUTH_INDEX;?>/2'><i class='fa fa-circle-o'></i> <?php echo get_languageword('buyers');?></a>
				</li>
				
				<li <?php if(isset($activesubmenu) && $activesubmenu == '3') echo ' class="active"';?>>
					<a href='<?php echo URL_AUTH_INDEX;?>/3'><i class='fa fa-circle-o'></i> <?php echo get_languageword('sellers');?></a>
				</li>

				

			</ul>
		</li>

		<!--Catgories Start-->
		<li class='treeview <?php if(isset($activemenu) && $activemenu == 'categories') echo 'active';?>'>
			<a href='#'>
				<i class='fa fa-cog'></i> <?php echo get_languageword('categories');?> <i class='fa fa-angle-left pull-right'></i>
			</a>
			<ul class='treeview-menu'>
				<li <?php if(isset($activesubmenu) && $activesubmenu == 'categories') echo ' class="active"';?>>
					<a href='<?php echo URL_CATEGORIES_INDEX;?>'><i class='fa fa-circle-o'></i> <?php echo get_languageword('list_categories');?></a>
				</li>					
				<li <?php if(isset($activesubmenu) && $activesubmenu == 'categories-add') echo ' class="active"';?>>
					<a href='<?php echo URL_CATEGORIES_INDEX;?>/add'><i class='fa fa-circle-o'></i> <?php echo get_languageword('add_category');?></a>
				</li>
			</ul>
		</li>
		<!--Categories End-->

		<!--Seller Selling Books Start-->
		<li <?php if(isset($activemenu) && $activemenu == 'seller_selling_books') echo ' class="active"';?>>
			<a href="<?php echo URL_SELLER_LIST_SELLING_BOOKS;?>">
				<i class="fa fa-book"></i><?php echo get_languageword('All_Books');?></a>
			</li>
			<!--Seller Selling Books End-->
			<li <?php if(isset($activemenu) && $activemenu == 'purchased_books') echo ' class="active"';?>>
				<a href="<?php echo URL_ADMIN_VIEW_PURCHASED_BOOKS;?>">
					<i class="fa fa-money"></i><?php echo get_languageword('Total_Sale');?></a>
				</li>

			<!--Money Conversion From Buyer-->
			<li class='treeview <?php if(isset($activemenu) && $activemenu == 'buyer_money_reqs') echo 'active';?>'>
				<a href='#'>
					<i class='fa fa-money'></i> <?php echo get_languageword('seller_money_requests');?> <i class='fa fa-angle-left pull-right'></i>
				</a>
				<ul class='treeview-menu'>
					<li <?php if(isset($activesubmenu) && $activesubmenu == 'buyer_Pending') echo ' class="active"';?>>
					<a href='<?php echo URL_ADMIN_SELLER_MONEY_CONVERSION_REQUESTS."/Pending";?>'><i class='fa fa-circle-o'></i> <?php echo get_languageword('pending');?></a>
					</li>			
					<li <?php if(isset($activesubmenu) && $activesubmenu == 'buyer_Done') echo ' class="active"';?>>
					<a href='<?php echo URL_ADMIN_SELLER_MONEY_CONVERSION_REQUESTS."/Done";?>'><i class='fa fa-circle-o'></i> <?php echo get_languageword('completed');?></a>
					</li>
				</ul>
			</li>

			<li<?php if(isset($activesubmenu) && $activesubmenu == 'dynamic_pages') echo ' class="active"';?>>
				<a href='<?php echo URL_ADMIN_DYNAMIC_PAGES; ?>'><i class='fa fa-file-code-o'></i> <?php echo get_languageword('dynamic_pages');?></a>
			</li>

			<li <?php if(isset($activesubmenu) && $activesubmenu == 'scroll_news') echo ' class="active"';?>>
				<a href='<?php echo URL_ADMIN_SCROLL_NEWS; ?>'><i class='fa fa-newspaper-o'></i> <?php echo get_languageword('scroll_News');?></a>
			</li>

			<!--Site Testimonials Start-->
			<li <?php if(isset($activesubmenu) && $activesubmenu == 'sitetestimonials') echo ' class="active"';?>><a href='<?php echo base_url();?>sitetestimonials/index'><i class='fa fa-comments-o'></i><?php echo get_languageword('Testimonials');?></a>
			</li>
			<!--Site Testimonials End-->

			<li <?php if(isset($activemenu) && $activemenu == 'settings') echo ' class="active"';?>>
				<a href="<?php echo URL_SETTINGS_INDEX;?>">
					<i class="fa fa-wrench"></i><?php echo get_languageword('settings');?></a>
				</li>

				
			</ul>
