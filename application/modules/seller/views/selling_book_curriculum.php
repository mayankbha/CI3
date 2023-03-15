<!-- Dashboard panel -->
<div class="dashboard-panel">
	<?php echo $this->session->flashdata('message');?>
	<div class="dashboard-list">

		<div class="update-profile scc-info">
			<div><a><b><?php echo get_languageword('Admin_Approved');?>:</b> <?php if(!empty($record->admin_approved)) echo $record->admin_approved; ?></a></div>
			<div><a><b><?php echo get_languageword('status');?>:</b> <?php if(!empty($record->status)) echo $record->status; ?></a></div>
			<div><a><b><?php echo get_languageword('Created_At');?>:</b> <?php if(!empty($record->created_at)) echo $record->created_at; ?></a></div>
			<div><a><b><?php echo get_languageword('Updated_At');?>:</b> <?php if(!empty($record->updated_at)) echo $record->updated_at; ?></a></div>
		</div>

		<h2 class="heading-line"><?php echo get_languageword('Selling_Book_Information')?>:</h2>

		<?php if($this->ion_auth->is_admin()) { ?>
			<p><strong><?php echo get_languageword('Published_By');?>:</strong> <?php if(!empty($record->username)) echo '<a href="'.URL_HOME_SELLER_PROFILE.'/'.$record->seller_slug.'">'.$record->username.'</a>'; ?> </p>
		<?php } ?>

		<p><strong><?php echo get_languageword('Book_Name');?>:</strong> <?php if(!empty($record->book_name)) echo $record->book_name; ?> </p>
		<p><strong><?php echo get_languageword('Book_Title');?>:</strong> <?php if(!empty($record->book_title)) echo $record->book_title; ?> </p>
		<p><strong><?php echo get_languageword('price');?>:</strong> <?php if(!empty($record->book_price)) echo $record->book_price; ?> </p>
		<p><strong><?php echo get_languageword('Max_Downloads');?>:</strong> <?php if(!empty($record->max_downloads)) echo $record->max_downloads; ?> </p>
		<p><strong><?php echo get_languageword('Skill_Level');?>:</strong> <?php if(!empty($record->skill_level)) echo $record->skill_level; ?> </p>
		<p><strong><?php echo get_languageword('Languages');?>:</strong> <?php if(!empty($record->languages)) echo $record->languages; ?> </p>

		<p><strong><?php echo get_languageword('Book_Image');?>:</strong> 
			<?php if(!empty($record->image) && file_exists(URL_PUBLIC_UPLOADS.'book_curriculum_files/'.$record->image)) { 

					$ext = pathinfo($record->image, PATHINFO_EXTENSION);

					$file_src = URL_PUBLIC_UPLOADS2.'book_curriculum_files/'.$record->image;
			?>
				<a target="_blank" href="<?php echo $file_src; ?>"><?php echo $record->image; ?></a>
			<?php } ?>
		</p>

		<p><strong><?php echo get_languageword('Preview_Image');?>:</strong> 
			<?php if(!empty($record->preview_image) && file_exists(URL_PUBLIC_UPLOADS.'book_curriculum_files/'.$record->preview_image)) { 

					$ext = pathinfo($record->preview_image, PATHINFO_EXTENSION);

					$file_src = URL_PUBLIC_UPLOADS2.'book_curriculum_files/'.$record->preview_image;
			?>
				<a target="_blank" href="<?php echo $file_src; ?>"><?php echo $record->preview_image; ?></a>
			<?php } ?>
		</p>

		<p><strong><?php echo get_languageword('Preview_File');?>:</strong> 
			<?php if(!empty($record->preview_file) && file_exists(URL_PUBLIC_UPLOADS.'book_curriculum_files/'.$record->preview_file)) { 

					$ext = pathinfo($record->preview_file, PATHINFO_EXTENSION);

					$file_src = URL_PUBLIC_UPLOADS2.'book_curriculum_files/'.$record->preview_file;
			?>
				<a target="_blank" href="<?php echo $file_src; ?>"><?php echo $record->preview_file; ?></a>
			<?php } ?>
		</p>


		<p><strong><?php echo get_languageword('description');?>:</strong> <br/>
			<?php if(!empty($record->description)) echo $record->description; ?> 
		</p>

		<h2 class="heading-line"><?php echo get_languageword('Curriculum')?>:</h2>

		<table class="table">
			<thead>
				<tr>
					<th>#</th>
					<th><?php echo get_languageword('title'); ?></th>
					<th><?php echo get_languageword('File_Size'); ?></th>
					<?php if($this->ion_auth->is_seller()) { ?>
						<th><?php echo get_languageword('action'); ?></th>
					<?php } ?>
				</tr>
			</thead>
			<tbody>
				<?php
						if(!empty($record->sellingbook_curriculum)) {

							$i = 1;
							foreach ($record->sellingbook_curriculum as $key => $value) {

								$attr = 'target="_blank"';

								if($value->source_type == "file" && !empty($value->file_name) && file_exists(URL_PUBLIC_UPLOADS.'book_curriculum_files/'.$value->file_name)) {

									$file_src = URL_PUBLIC_UPLOADS2.'book_curriculum_files/'.$value->file_name;
									$file_size = formatSizeUnits($value->file_size);

								} else {

									$file_src = $value->file_name;
									$file_size = "--";

								}

								$freeTitle = ($value->is_free == '1') ? '(Free Preview)' : '';

								$title = '<a '.$attr.' href="'.$file_src.'" >'.$value->title.' '.$freeTitle.'</a>';

								$action = '<a href="'.URL_SELLER_DELETE_BOOK_CURRICULUM_RECORD.'/'.$value->file_id.'" class="delete-icon-grocery crud-action" title="'.get_languageword('delete').'"><img src="'.URL_FRONT_IMAGES.'close-grocery.png" alt="'.get_languageword('delete').'"></a>';

				 ?>
				<tr>
					<td><?php echo $i++; ?></td>
					<td><?php echo $title; ?></td>
					<td><?php echo $file_size; ?></td>
					<?php if($this->ion_auth->is_seller()) { ?>
						<td><?php echo $action; ?></td>
					<?php } ?>
				</tr>
				<?php } } else echo '<tr><td>'.get_languageword('No_Curriculum_added').'</td></tr>'; ?>
			</tbody>
		</table>

		<button type="button" class="btn btn-default" onclick="location.href='<?php echo URL_SELLER_LIST_SELLING_BOOKS; ?>'"><i class="fa fa-arrow-left"></i> <?php echo get_languageword('back'); ?></button>

	</div>

</div>
<!-- Dashboard panel ends -->