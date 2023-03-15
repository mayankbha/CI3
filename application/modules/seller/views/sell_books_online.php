<div class="dashboard-panel">
	<?php 
		echo $message;
		$admin_commission = ($profile_info->admin_commission > 0) ? $profile_info->admin_commission : $this->config->item('site_settings')->admin_commission_on_book_purchase;
	?>

	<p class="text-danger">
		<small>
		<strong><?php echo get_languageword('Note1'); ?>:</strong> <?php echo get_languageword('Please upload files only with allowed formats'); ?>
		<br />
		<strong><?php echo get_languageword('Note2'); ?>:</strong> <?php echo get_languageword('Admin_Commission_On_Each_Purchase').': '.$admin_commission.'%'; ?>
		</small>
	</p>

	<div class="row">

		<?php
		$attributes = array('name' => 'sell_books_form', 'id' => 'sell_books_form', 'class' => 'comment-form dark-fields');
		echo form_open_multipart('',$attributes);?>

		
			<div class="col-sm-12 ">
				<div class="input-group ">
					<label><?php echo get_languageword('Category');?><?php echo required_symbol();?></label>
					<div class="dark-picker dark-picker-bright">
					<?php

						$val = set_value('category_id', (!empty($record->category_id)) ? $record->category_id : '');

						echo form_dropdown('category_id', $cat_opts, $val, 'id="category_id" class="select-picker"');
					?>
					</div>
				</div>
			</div>


			<div class="col-sm-6 ">
				<div class="input-group ">
					<label><?php echo get_languageword('Book_Name');?><?php echo required_symbol();?></label>
					<?php

						$val = set_value('book_name', (!empty($record->book_name)) ? $record->book_name : '');

						$element = array(
							'name'	=>	'book_name',
							'id'	=>	'book_name',
							'value'	=>	$val,
							'class' => 'form-control',
							'placeholder' => get_languageword('book_name'),
						);
						echo form_input($element);
					?>
				</div>
			</div>
			<div class="col-sm-6 ">
				<div class="input-group ">
					<label><?php echo get_languageword('Book_Title');?><?php echo required_symbol();?></label>
					<?php

						$val = set_value('book_title', (!empty($record->book_title)) ? $record->book_title : '');

						$element = array(
							'name'	=>	'book_title',
							'id'	=>	'book_title',
							'value'	=>	$val,
							'class' => 'form-control',
							'placeholder' => get_languageword('book_title'),
						);
						echo form_input($element);
					?>
				</div>
			</div>
			<div class="col-sm-12 ">
				<div class="input-group ">
					<label><?php echo get_languageword('Description');?><?php echo required_symbol();?></label>
					<textarea name="description" id="description" class="form-control texteditor"><?php echo set_value('description', (!empty($record->description)) ? $record->description : ''); ?></textarea>
				</div>
			</div>
			<div class="col-sm-6 ">
				<div class="input-group ">
					<label><?php echo get_languageword('Skill_Level');?></label>
					<?php

						$val = set_value('skill_level', (!empty($record->skill_level)) ? $record->skill_level : '');

						$element = array(
							'name'	=>	'skill_level',
							'id'	=>	'skill_level',
							'value'	=>	$val,
							'class' => 'form-control',
							'placeholder' => get_languageword('skill_level'),
						);
						echo form_input($element);
					?>
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 ">
				<div class="input-group ">
					<label><?php echo get_languageword('Languages');?></label>
					<?php

						$val = set_value('languages', (!empty($record->languages)) ? explode(',', $record->languages) : '');

						echo form_multiselect('languages[]',$language_options,$val,'id="languages" class="form-control multiple-tags" multiple="multiple" ');
					?>
				</div>
			</div>


			<div class="col-sm-12 ">
				<div class="input-group ">
					<label><?php echo get_languageword('Book_Image');?> <code><small><?php echo "('.jpg, .jpeg, .png, .svg, .bmp'".get_languageword('_are_allowed_formats_for_book_image').")"; ?></small></code></label>
					<?php
						$val = (!empty($record->image)) ? $record->image : '';

						$element = array(
							'type'	=>	'file',
							'name'	=>	'book_image',
							'id'	=>	'book_image',
							'class' => 	'form-control',
							'placeholder' => get_languageword('Book_Image'),
						);

						echo form_input($element);
						//if(!empty($val) && file_exists(URL_PUBLIC_UPLOADS.'book_curriculum_files/'.$val)) echo '&nbsp;<a target="_blank"  href="'.URL_PUBLIC_UPLOADS2.'book_curriculum_files/'.$val.'">'.$val.'</a>';
						if(!empty($val)) echo '&nbsp;<a onclick="return openBookImage()" href="javascript: void(0)">'.$val.'</a>';
					?>

					<input type="hidden" id="book_imagecanvas" name="book_imagecanvas">
					<input type="hidden" id="book_image_s3" name="book_image_s3" value="<?php echo $record->book_image_arr['image']; ?>" />
				</div>
			</div>


			<div class="col-sm-12 ">
				<div class="input-group ">
					<label><?php echo get_languageword('Preview_Image');?> <code><small><?php echo "('.jpg, .jpeg, .png, .svg, .bmp'".get_languageword('_are_allowed_formats_for_preview_image').")"; ?></small></code></label>
					<?php

						$val = (!empty($record->preview_image)) ? $record->preview_image : '';

						$element = array(
							'type'	=>	'file',
							'name'	=>	'preview_image',
							'id'	=>	'preview_image',
							'class' => 	'form-control',
							'placeholder' => get_languageword('Preview_Image'),
						);
						echo form_input($element);
						//if(!empty($val) && file_exists(URL_PUBLIC_UPLOADS.'book_curriculum_files/'.$val)) echo '&nbsp;<a target="_blank"  href="'.URL_PUBLIC_UPLOADS2.'book_curriculum_files/'.$val.'">'.$val.'</a>';
						if(!empty($val)) echo '&nbsp;<a onclick="return openBookPreviewImage()" href="javascript: void(0)">'.$val.'</a>';
					?>
					<input type="hidden" id="preview_imagecanvas" name="preview_imagecanvas">
					<input type="hidden" id="book_preview_image_s3" name="book_preview_image_s3" value="<?php echo $record->book_image_arr['preview_image']; ?>" />
				</div>
			</div>

			<div class="col-sm-12">
				<div class="input-group ">
					<label><?php echo get_languageword('Preview_File');?> <code><small><?php echo "('.mp2, .mp3, .mp4, .3gp, .webm, .aac, .wav, .wmv, .flv, .avi, .ogg, .jpg, .jpeg, .png, .svg, .bmp'".get_languageword('_are_allowed_formats_for_preview_file').")"; ?></small></code></label>
					<?php

						$val = (!empty($record->preview_file)) ? $record->preview_file : '';

						$element = array(
							'type'	=>	'file',
							'name'	=>	'preview_file',
							'id'	=>	'preview_file',
							'class' => 	'form-control',
							'placeholder' => get_languageword('Preview_File'),
						);
						echo form_input($element);
						//if(!empty($val) && file_exists(URL_PUBLIC_UPLOADS.'book_curriculum_files/'.$val)) echo '&nbsp;<a target="_blank" href="'.URL_PUBLIC_UPLOADS2.'book_curriculum_files/'.$val.'">'.$val.'</a>';
						if(!empty($val))
							if($record->book_image_arr['preview_file_mimetype'] == 'application/pdf')
								echo '&nbsp;<a target="_blank" href="'.$record->book_image_arr['preview_file_presignedUrl'].'">'.$val.'</a>';
							else
								echo '&nbsp;<a onclick="return openBookPreviewFile()" href="javascript: void(0)">'.$val.'</a>';
					?>
					<input type="hidden" id="book_preview_file_s3" name="book_preview_file_s3" value="<?php echo $record->book_image_arr['preview_file']; ?>" />
					<input type="hidden" id="book_preview_file_name" name="book_preview_file_name" value="<?php echo $record->book_image_arr['preview_file_name']; ?>" />
					<input type="hidden" id="book_preview_file_s3_mimetype" name="book_preview_file_s3_mimetype" value="<?php echo $record->book_image_arr['preview_file_mimetype']; ?>" />
				</div>
			</div>

			<div class="col-sm-12">
				<h4><?php echo get_languageword('Curriculum'); ?><?php if(empty($record->sc_id)) echo required_symbol();?> <code><small><?php echo get_languageword('maximum_allowed_file_size_is_20_MB_for_each_file').")"; ?></small> </code></h4>
			</div>

			<div class="col-sm-12 add-curriculum">
				<div id="add_curriculum">
					<?php

	                        $appending_div = "div_curclm";
	                        $key = 1;
	                        $cls = "";
	                        $max_curr 	= (!empty($record->sellingbook_curriculum)) ? 25-count($record->sellingbook_curriculum) : 24;
	                        $btn_action = '<span title="'.get_languageword('add_more').'" class="btn btn-success" id="add_curclm_field" onclick=\'append_field('.$max_curr.', "add_curriculum", this.id, "'.$appending_div.'", "Title", "Source", "lesson_title", "lesson_url", "Free");\'><i class="fa fa-plus"></i></span> ';

	                ?>

					<div class="row <?php echo $cls; ?>" id="<?php echo $appending_div.$key; ?>">

						<input type="hidden" name="removed_curriculum">

						<div class="col-sm-5 ">
							<label>Title <?php echo $key; ?></label>
					    	<input type="text" name="lesson_title[]" class="form-control" />
							<input type="hidden" name="curriculum_id[]" class="form-control" />
							<input type="hidden" name="book_preview_file_s3_mimetype[]" class="form-control" />
						</div>
						<div class="col-sm-2 ">
							<label>Source Type</label>
							<?php

								$sourcetype_opts = array(
														'url' 	=> get_languageword('URL'),
														'file' 	=> get_languageword('file')
													);


								echo form_dropdown('source_type[]', $sourcetype_opts, '', 'id="source_type_'.$key.'" class="form-control cls-source_type" ');
							?>
						</div>
						<div class="col-sm-3 ">
							<label>Source <?php echo $key; ?></label>
							<div class="cls-source" id="source_<?php echo $key; ?>">
								<input type="text" name="lesson_url[<?php echo $key-1; ?>]" class="form-control" />
							</div>
						</div>
						<div class="col-sm-1 ">
							<label>Free <?php echo $key; ?></label>
							<div class="cls-source" id="is_free_<?php echo $key; ?>">
								<input style="opacity:1;" type="checkbox" name="is_free[<?php echo $key-1; ?>]" class="form-control free-check-box" />
							</div>
						</div>
						<div class="col-sm-1">
							<label>&nbsp;</label>
							<?php echo $btn_action; ?>
						</div>
					</div>
				</div>
			</div>

			<p>&nbsp;</p>

			<div class="col-sm-12 add-exam">
				<div id="add_exam">
					<?php

	                        $appending_div = "div_exam";
	                        $key = 1;
	                        $cls = "";
	                        $max_exam 	= (!empty($record->sellingbook_exam)) ? 25-count($record->sellingbook_exam) : 24;
	                        $btn_action = '<span style="margin-top: 30px;" title="'.get_languageword('add_more').'" class="btn btn-success" id="add_exam_field" onclick=\'append_exam_field('.$max_exam.', "add_exam", this.id, "'.$appending_div.'", "Question", "question");\'><i class="fa fa-plus"></i></span> ';

	                ?>

					<div class="row <?php echo $cls; ?>" id="<?php echo $appending_div.$key; ?>">
						<input type="hidden" name="removed_exam">

						<div class="col-sm-9">
							<label>Question <?php echo $key; ?></label>
					    	<input type="text" name="question[]" class="form-control" />
							<input type="hidden" name="exam_id[]" class="form-control" />
						</div>

						<div class="col-sm-2">
							<label>Answer</label>
							<?php
								$answer_opts = array(
														'1' => get_languageword('Yes'),
														'0' => get_languageword('No')
													);

								echo form_dropdown('answer[]', $answer_opts, '', 'id="answer_'.$key.'" class="form-control cls-answer" ');
							?>
						</div>

						<div class="col-sm-1">
							<label>&nbsp;</label>
							<?php echo $btn_action; ?>
						</div>
					</div>
				</div>
			</div>

			<p>&nbsp;</p>

			<div class="col-sm-3 ">
				<div class="input-group ">
					<label><?php echo get_languageword('Actual_Price');?><?php echo required_symbol();?><br><br><br><br><br></label>
					<?php

						$val = set_value('actual_price', (!empty($record->actual_price)) ? $record->actual_price : '0');

						$element = array(
							'type'	=>	'text',
							'name'	=>	'actual_price',
							'id'	=>	'actual_price',
							'value'	=>	$val,
							'class' => 'form-control',
							'placeholder' => get_languageword('Actual_Price'),
						);
						echo form_input($element);
					?>
				</div>
			</div>
			
			<div class="col-sm-3 ">
				<div class="input-group ">
					<label><?php echo get_languageword('Apply_Discount_In_Percentage');?><br><code><small>Optionally, you can apply discount or leave it empty</small> </code><br><br><br></label>
					<?php

						$element = array(
							'type'	=>	'text',
							'name'	=>	'book_discount_percent',
							'id'	=>	'book_discount_percent',
							'value'	=>	'',
							'class' => 'form-control',
							'placeholder' => get_languageword('Discount_Percentage'),
						);
						echo form_input($element);
					?>
				</div>
			</div>

			<div class="col-sm-3 ">
				<div class="input-group ">
					<label><?php echo get_languageword('Discounted_Price');?><?php echo required_symbol();?> <br><code><small>If you don't want to apply discount simply repeat the actual price here, this price consider as final price</small> </code></label>
					<?php

						$val = set_value('book_price', (!empty($record->book_price)) ? $record->book_price : '');

						$element = array(
							'type'	=>	'text',
							'name'	=>	'book_price',
							'id'	=>	'book_price',
							'value'	=>	$val,
							'class' => 'form-control',
							'placeholder' => get_languageword('Discounted_Price'),
						);
						echo form_input($element);
					?>
				</div>
			</div>

			

			<div class="col-sm-3 ">
				<div class="input-group ">
					<label><?php echo get_languageword('Maximum_number_of_Downloads');?><?php echo required_symbol();?><br><br><br><br></label>
					<?php

						$val = set_value('max_downloads', (!empty($record->max_downloads)) ? $record->max_downloads : '99');

						$element = array(
							'type'	=>	'number',
							'name'	=>	'max_downloads',
							'id'	=>	'max_downloads',
							'value'	=>	$val,
							'class' => 'form-control',
							'placeholder' => get_languageword('max_downloads'),
						);
						echo form_input($element);
					?>
				</div>
			</div>

			<div class="col-sm-6 " style="display: none;">
				<div class="input-group ">
					<label><?php echo get_languageword('Status');?><?php echo required_symbol();?></label>
					<div class="dark-picker dark-picker-bright">
					<?php

						$status_opts = array(
												'Active' 	=> get_languageword('Active'),
												'Inactive' 	=> get_languageword('Inactive')
											);

						$val = set_value('status', (!empty($record->status)) ? $record->status : '');

						echo form_dropdown('status', $status_opts, $val, 'class="select-picker" ');
					?>
					</div>
				</div>
			</div>

			<?php
					$sc_id = "";
					$actn_btn_txt = get_languageword('publish');
					if(!empty($record->sc_id)) {
						$sc_id = $record->sc_id;
						$actn_btn_txt = get_languageword('update');
					}
			?>

			<input type="hidden" name="sc_id" value="<?php echo $sc_id; ?>" />

			<div class="col-sm-12 ">
				<div class="col-sm-6">
					<button class="btn-link-dark dash-btn pull-right" name="submitbutt" type="Submit"><?php echo $actn_btn_txt;?></button>
				</div>
				<div class="col-sm-6 ">
					<button onclick="location.href='<?php echo URL_SELLER_LIST_SELLING_BOOKS; ?>'" class="btn-link-dark dash-btn pull-left" type="button" ><?php echo get_languageword('cancel');?></button>
				</div>
			</div>

		</form>
	</div>

</div>


<script>

$(document).ready(function() {
    var $modal = $('#modal');
    var image = document.getElementById('sample_image');
    var cropper;
		var croplwidth = 400;
    var croplheight = 400;
    var clickeditemname = "";
		var varaspectRatio = 1;

    //$("body").on("change", ".image", function(e){
    $('#book_image').change(function(event){
			varaspectRatio = 1;
			 croplwidth = 400;
     	croplheight = 400;
			 clickeditemname = "#book_image";
        var files = event.target.files;
        var done = function (url) {
            image.src = url;
            $modal.modal('show');
        };
        //var reader;
        //var file;
        //var url;

        if (files && files.length > 0)
        {
            /*file = files[0];
            if(URL)
            {
                done(URL.createObjectURL(file));
            }
            else if(FileReader)
            {*/
                reader = new FileReader();
                reader.onload = function (event) {
                    done(reader.result);
                };
                reader.readAsDataURL(files[0]);
            //}
        }
    });


		$('#preview_image').change(function(event){
			varaspectRatio = 16/9;
			croplwidth = 1000;
       		croplheight = 400;
			clickeditemname = "#preview_image";

        var files = event.target.files;
        var done = function (url) {
            image.src = url;
            $modal.modal('show');
        };
        //var reader;
        //var file;
        //var url;

        if (files && files.length > 0)
        {
            /*file = files[0];
            if(URL)
            {
                done(URL.createObjectURL(file));
            }
            else if(FileReader)
            {*/
                reader = new FileReader();
                reader.onload = function (event) {
                    done(reader.result);
                };
                reader.readAsDataURL(files[0]);
            //}
        }
    });


    $modal.on('shown.bs.modal', function() {
        cropper = new Cropper(image, {
            aspectRatio: varaspectRatio,
            viewMode: 0,
            preview: '.preview',
            cropBoxResizable:false,
            zoomable:true
        });
    }).on('hidden.bs.modal', function() {
        cropper.destroy();
        cropper = null;
    });

    $("#crop").click(function(){
        canvas = cropper.getCroppedCanvas({
            width: croplwidth,
            height: croplheight,
        });
        $('.processing-inside2').css('display','block');

				$(clickeditemname+'canvas').val (canvas.toDataURL('image/png'));

				$modal.modal('hide');

        canvas.toBlob(function(blob) {
            //url = URL.createObjectURL(blob);
            var reader = new FileReader();
            reader.readAsDataURL(blob); 
            reader.onloadend = function() {
            var base64data = reader.result;  
            
					

            }
        });
    });
    //https://stackoverflow.com/questions/13198131/how-to-save-an-html5-canvas-as-an-image-on-a-server
});

function openBookImage() {
	var img = $('#book_image_s3').val();

	var image = new Image();
	image.src = "data:image/jpg;base64," + img;

	var w = window.open("");
	w.document.write(image.outerHTML);
}

function openBookPreviewImage() {
	var img = $('#book_preview_image_s3').val();

	var image = new Image();
	image.src = "data:image/jpg;base64," + img;

	var w = window.open("");
	w.document.write(image.outerHTML);
}

function openBookPreviewFile() {
	//mp2|mp3|mp4|3gp|pdf|webm|aac|wav|wmv|flv|avi|ogg|jpg|jpeg|png|svg|bmp

	var file = $('#book_preview_file_s3').val();
	var file_name = $('#book_preview_file_name').val();

	var file_mimetype = $('#book_preview_file_s3_mimetype').val();

	var explode_mimetype = file_mimetype.split('/');
	var file_format = explode_mimetype[0];

	if(file_format == 'image') {
		var open_file = new Image();
		open_file.src = "data:image/jpg;base64," + file;
		var w = window.open("");
		w.document.write(open_file.outerHTML);
	} else if(file_format == 'application') {
		let pdfWindow = window.open("");
		pdfWindow.document.write("<html<head><title>"+file_name+"</title><style>body{margin: 0px;}iframe{border-width: 0px;}</style></head>");
		pdfWindow.document.write("<body><embed width='100%' height='100%' src='data:"+file_mimetype+";base64, " + encodeURI(file)+"#toolbar=0&navpanes=0&scrollbar=0'></embed></body></html>");
	} else if(file_format == 'audio') {
		let pdfWindow = window.open("");
		pdfWindow.document.write("<html<head><title>"+file_name+"</title><style>body{margin: 100px;}iframe{border-width: 0px;}</style></head>");
		pdfWindow.document.write("<body align='center' onload='document.getElementById('aud').play()'><audio id='aud' controls='controls' autobuffer='autobuffer' autoplay='autoplay'><source src='data:"+file_mimetype+";base64, "+file+"' /></audio></body></html>");
	} else if(file_format == 'video') {
		let pdfWindow = window.open("");
		pdfWindow.document.write("<html<head><title>"+file_name+"</title><style>body{margin: 100px;}iframe{border-width: 0px;}</style></head>");
		pdfWindow.document.write("<body align='center' onload='document.getElementById('vid').play()'><video id='vid' controls='controls' autobuffer='autobuffer' autoplay='autoplay' src='data:"+file_mimetype+";base64, "+file+"'>Your browser does not support HTML5 video.</video></body></html>");
	}

	/*var image = new Image();
	image.src = "data:image/jpg;base64," + img;

	var w = window.open("");
	w.document.write(image.outerHTML);*/
}

function openBookCurriculumFile(s3_file, mimetype, file_ext, file_name) {
	var file = s3_file;

	var explode_mimetype = mimetype.split('/');
	var file_format = explode_mimetype[0];

	//alert('file_format :: ' + file_format);
	//alert('file :: ' + file);

	//'mp2|mp3|mp4|3gp|pdf|ppt|pptx|doc|docx|rtf|rtx|txt|text|webm|aac|wav|wmv|flv|avi|ogg|jpg|jpeg|png|gif|svg|bmp';

	if(file_format == 'image') {
		var open_file = new Image();
		open_file.src = "data:image/jpg;base64," + file;
		var w = window.open("");
		w.document.write(open_file.outerHTML);
	} else if(file_format == 'application') {
		let pdfWindow = window.open("");
		pdfWindow.document.write("<html<head><title>"+file_name+"</title><style>body{margin: 0px;}iframe{border-width: 0px;}</style></head>");
		pdfWindow.document.write("<body><embed width='100%' height='100%' src='data:application/"+file_ext+";base64, " + encodeURI(file)+"#toolbar=0&navpanes=0&scrollbar=0'></embed></body></html>");
	} else if(file_format == 'audio') {
		let pdfWindow = window.open("");
		pdfWindow.document.write("<html<head><title>"+file_name+"</title><style>body{margin: 100px;}iframe{border-width: 0px;}</style></head>");
		pdfWindow.document.write("<body align='center' onload='document.getElementById('aud').play()'><audio id='aud' controls='controls' autobuffer='autobuffer' autoplay='autoplay'><source src='data:audio/"+file_ext+";base64, "+file+"' /></audio></body></html>");
	} else if(file_format == 'video') {
		let pdfWindow = window.open("");
		pdfWindow.document.write("<html<head><title>"+file_name+"</title><style>body{margin: 100px;}iframe{border-width: 0px;}</style></head>");
		pdfWindow.document.write("<body align='center' onload='document.getElementById('vid').play()'><video id='vid' controls='controls' autobuffer='autobuffer' autoplay='autoplay' src='data:video/"+file_ext+";base64, "+file+"'>Your browser does not support HTML5 video.</video></body></html>");
	}
}
</script>

<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Crop Image Before Upload</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="img-container">
                    <div class="row">
                        <div class="col-md-8">
                            <img src="" id="sample_image" />
                        </div>
                        <div class="col-md-4">
                            <!-- <div class="preview"></div> -->
                            <h5><b>Instructions:</b></h5>
                            <ol><b>Zooming:</b> If desired image area not coming inside the blue line box, use mouse wheel to zoom in or zoom out the image to fit into blue box.</ol>
                            <ol><b>Cropping:</b> If image out from blue box, click to drag blue box form center to select desire area to be cropped.</ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="crop">Crop</button>
            </div>
        </div>
    </div>
</div>
<!-- Dashboard panel ends -->