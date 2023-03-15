<style>
	label.box{
		margin-left: 0px !important; margin-top: 10px; cursor: pointer;
	}
	input[type="radio"] {
		opacity: 1 !important;
	}
	subject {
		font-size: 12px
	}
</style>

<!-- Footer -->
<section class="footer" id="footer_sec">
    <a href="#" class="back-to-top show" title="Move to top"><i class="glyphicon glyphicon-menu-up"></i></a>
    <div class="container">
    
        <?php if(strip_tags($this->config->item('site_settings')->footer_section) == "On") {

                if(strip_tags($this->config->item('site_settings')->get_app_section) == "Off")
                    $col_size = 12;
                else
                    $col_size = 9;

                $query = "SELECT cat.* FROM ".TBL_CATEGORIES." cat 
                  WHERE is_parent=1 AND status=1 AND 
                  EXISTS (SELECT * FROM ".TBL_BOOK_CATEGORIES." cc WHERE cc.category_id=cat.id) 
                  ORDER BY cat.sort_order ASC ".$limit_cond." ";

                $categories = $this->db->query($query)->result();
            ?>
        <div class="row row-margin">
            <?php if(!empty($activemenu) && $activemenu == "home") echo $this->session->flashdata('message'); ?>
            <div class="col-lg-<?php echo $col_size;?> col-md-12 col-sm-12">
                <div class="row">
                    

                    <div class="col-sm-6">
                        <h4 class="footer-head"><?php echo get_languageword('categories');?></h4>
                        <ul class="footer-links">

                            <?php foreach ($categories as $row) { ?>
                            
                            <li>
                                <a href="<?php echo URL_HOME_BUY_BOOKS.'/'.$row->slug;?>">
                                    <?php echo $row->name; ?>
                                </a>
                            </li>

                            <?php } ?>
                         
                        </ul>
                    </div>
                    <div class="col-sm-6">
                        <h4 class="footer-head"><?php echo get_languageword('Legal');?></h4>
                        <ul class="footer-links">
                            <li><a href="<?php echo SITEURL2."/terms-and-conditions" ; ?>"><?php echo get_languageword('terms_And_Conditons');?></a></li>
                            
                            <li><a href="<?php echo SITEURL2."/privacy-policy"; ?>"><?php echo get_languageword('privacy_policy');?></a></li>

                            <li><a href="<?php echo SITEURL2."/refund-policy"; ?>"><?php echo get_languageword('refund_policy');?></a></li>

                            <li><a href="<?php echo SITEURL2."/cookies-policy"; ?>"><?php echo get_languageword('Cookies_Policy');?></a></li>

                            <li><a href="<?php echo SITEURL2."/disclaimer"; ?>"><?php echo get_languageword('Disclaimer');?></a></li>

                         
                        </ul>
                    </div>
                </div>
            </div>

        </div>
        <?php } ?>
        <?php if(strip_tags($this->config->item('site_settings')->primary_footer_section) == "On") { ?>
        <div class="row footer-copy-bar">
            <div class="col-md-12">
                <hr class="footer-hr">

                <span style="display:flex;justify-content:center;align-items:center;">
                    
                    <?php if(isset($this->config->item('site_settings')->rights_reserved_by) && $this->config->item('site_settings')->rights_reserved_by != '') {
                        echo "<span class='design-by'>".$this->config->item('site_settings')->rights_reserved_by."</span>";
                    }
                    ?>


                </span>


                
            </div>
        </div>
        <?php } ?>
    </div>

	<div class="modal fade" id="book_exam_quiz_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalLabel">Take Book Exam Quiz</h5>

					<!--<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button>-->
				</div>

				<form id="exam_quiz_form" action="">
					<div class="modal-body">
						<div class="img-container">
							<div class="row">
								<?php if(isset($record) && isset($record->sellingbook_exam) && !empty($record->sellingbook_exam)) { ?>
									<input type="hidden" name="total_book_exam_quiz" id="total_book_exam_quiz" value="<?php echo sizeof($record->sellingbook_exam); ?>" />

									<?php $i = 0; foreach($record->sellingbook_exam as $key => $val) { $i++; ?>
										<input type="hidden" name="sc_id" value="<?php echo $val->sc_id; ?>" />
										<input type="hidden" name="exam_id[]" value="<?php echo $val->exam_id; ?>" />
										<input type="hidden" name="correct_answer[]" value="<?php echo $val->answer; ?>" />

										<div class="col-sm-12">
											<?php echo $i.'. '. $val->question; ?>
										</div>

										<div class="col-sm-12">
											<div class="col-sm-2">
												<input type="radio" name="question[<?php echo $i; ?>]" class="common_book_exam_quiz_class" id="yes_<?php echo $i; ?>" value="1" />
												<label for="yes_<?php echo $i; ?>" class="box">Yes</label>
											</div>

											<div class="col-sm-2">
												<input type="radio" name="question[<?php echo $i; ?>]" class="common_book_exam_quiz_class" id="no_<?php echo $i; ?>" value="0" />
												<label for="no_<?php echo $i; ?>" class="box">No</label>
											</div>
										</div>
									<?php } ?>
								<?php } ?>
							</div>
						</div>
					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
						<button type="button" class="btn btn-primary" id="submit-exam-quiz">Submit</button>
					</div>
				</form>
			</div>
		</div>
	</div>

</section>
<!-- Ends Footer -->




<!-- Script files -->
<?php
//neatPrint($this->config->item('site_settings'));
if(isset($grocery) && $grocery == TRUE)
{
?>
<!--Image CRUD scripts-->
<?php foreach($js_files as $file): ?>
<script src="<?php echo $file; ?>"></script>
<?php endforeach; ?>
<?php
}
else
{
?>
<script src="<?php echo URL_FRONT_JS;?>jquery.js"></script>

<link rel="stylesheet" href="<?php echo URL_FRONT_CSS;?>jquery-ui.css">

<script src="<?php echo URL_FRONT_JS;?>jquery-ui.js"></script>

<link href="<?php echo base_url(); ?>assets/jplayer/dist/skin/pink.flag/css/jplayer.pink.flag.min.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="<?php echo base_url(); ?>assets/jplayer/dist/jplayer/jquery.jplayer.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/jplayer/dist/add-on/jplayer.playlist.min.js"></script>

<script>
  $( function() {
    $( ".custom_accordion" ).accordion({
        heightStyle: "content"
    });
  });
</script>
<?php
}
?>

<?php if(isset($texteditor) && $texteditor == TRUE) { ?>
<script src="<?php echo base_url(); ?>assets/grocery_crud/texteditor/ckeditor/ckeditor.js"></script>
<script src="<?php echo base_url(); ?>assets/grocery_crud/texteditor/ckeditor/adapters/jquery.js"></script>
<script src="<?php echo base_url(); ?>assets/grocery_crud/js/jquery_plugins/config/jquery.ckeditor.config.js"></script>
<?php } ?>
<?php if(!empty($activemenu) && $activemenu == "sell_books_online") { ?>
<script> 
let curriculumArr = [];
let examArr = [];
let removedCurrIds = [];
let removedExamIds = [];

<?php if(isset($record) && isset($record->sellingbook_curriculum) && !empty($record->sellingbook_curriculum)) { ?>
    curriculumArr = <?php echo json_encode($record->sellingbook_curriculum); ?>;
    $(document).ready(function(){
        curriculumArr.map(function(curriculum){
            $('#add_curclm_field').click();
            $(document).find('input[name="lesson_title[]"]:last').val(curriculum.title);
            $(document).find('input[name="curriculum_id[]"]:last').val(curriculum.file_id);
            $(document).find('input[name="book_preview_file_s3_mimetype[]"]:last').val(curriculum.file_name);
            $(document).find('select[name="source_type[]"]:last').val(curriculum.source_type);
            $(document).find('select[name="source_type[]"]:last').change();
            if(curriculum.is_free == '1') {
                $(document).find('.free-check-box:last').prop('checked', true);
            }
            if(curriculum.source_type == 'url') {
                $(document).find('.lesson-url-input:last').val(curriculum.file_name);
            }
            if(curriculum.source_type == 'file') {
				//$(document).find('.lesson-file-input:last').after(`<a onclick="window.open('`+curriculum.s3_file+`', '_blank');" href="javascript: void(0);">View Uploaded File</a>`);

				//$(document).find('.lesson-file-input:last').after(`<a target="_blank" href="<?php echo base_url(); ?>/assets/uploads/book_curriculum_files/`+curriculum.file_name+`">View Uploaded File</a>`);

				if(curriculum.file_ext == 'pdf')
					$(document).find('.lesson-file-input:last').after(`<a target="_blank" href="`+curriculum.presignedUrl+`">View Uploaded File</a>`);
				else
					$(document).find('.lesson-file-input:last').after(`<a onclick="openBookCurriculumFile('`+curriculum.s3_file+`', '`+curriculum.mimetype+`', '`+curriculum.file_ext+`', '`+curriculum.file_name+`');" href="javascript: void(0);">View Uploaded File</a>`);
					
            }
        });
    });
<?php } ?>

<?php if(isset($record) && isset($record->sellingbook_exam) && !empty($record->sellingbook_exam)) { ?>
    examArr = <?php echo json_encode($record->sellingbook_exam); ?>;
    $(document).ready(function(){
		examArr.map(function(exam){
            $('#add_exam_field').click();
            $(document).find('input[name="question[]"]:last').val(exam.question);
            $(document).find('input[name="exam_id[]"]:last').val(exam.exam_id);
            $(document).find('select[name="answer[]"]:last').val(exam.answer);
        });
    });
<?php } ?>

//Add/Remove Fields Dynamically - Start
function append_field(max_fields, wrapper_id, add_button_id, appending_div, lbl_txt1, lbl_txt2, field_name1, field_name2, lbl_txt3)
{
    var wrapper         = $("#"+wrapper_id); //Fields wrapper
    var add_button      = $("#"+add_button_id); //Add button ID
    var cls             = "";
    var attrs           = "";

    var i = ($('#'+wrapper_id+' .'+appending_div).length) + 1; //text box count

    if(i < max_fields) { //max input box allowed
        i++; //text box increment
        $(wrapper).append(`
            <div class="row `+appending_div+`" id="`+appending_div+i+`">
                <div class="col-sm-5 ">
                    <label>`+lbl_txt1+` `+i+`</label>
                    <input type="text" name="`+field_name1+`[]" class="form-control" />
                    <input type="hidden" name="curriculum_id[]" class="form-control" />
                    <input type="hidden" name="book_preview_file_s3_mimetype[]" class="form-control" />
                </div>
                <div class="col-sm-2 ">
                    <label>Source Type</label>
                    <select name="source_type[]" id="source_type_`+i+`" class="form-control cls-source_type">
                        <option value="url">URL</option>
                        <option value="file">File</option>
                    </select>
                </div>
                <div class="col-sm-3 ">
                    <label>`+lbl_txt2+` `+i+`</label>
                    <div class="cls-source" id="source_`+i+`">
                        <input type="text" name="`+field_name2+`[`+(i-1)+`]" class="form-control" />
                    </div>
                </div>
                <div class="col-sm-1 ">
                    <label>`+lbl_txt3+` `+i+`</label>
                    <div class="cls-source" id="is_free_`+i+`">
                        <input style="opacity:1;" type="checkbox" name="is_free[`+(i-1)+`]" class="form-control free-check-box" />
                    </div>
                </div>
                <div class="col-sm-1">
                    <label>&nbsp;</label>
                    <span title="<?php echo get_languageword('remove_this'); ?>" class="btn btn-danger" id="`+i+`" onclick="remove_field(\'`+wrapper_id+`\', this.id, \'`+appending_div+`\', \'`+lbl_txt1+`\', \'`+lbl_txt2+`\', \'`+field_name1+`\', \'`+field_name2+`\', \'`+lbl_txt3+`\');" ><i class="fa fa-minus"></i></span>
                </div>
            </div>`
        ); 
        //add input box
    }
}

function append_exam_field(max_fields, wrapper_id, add_button_id, appending_div, lbl_txt1, field_name1)
{
    var wrapper         = $("#"+wrapper_id); //Fields wrapper
    var add_button      = $("#"+add_button_id); //Add button ID
    var cls             = "";
    var attrs           = "";

    var i = ($('#'+wrapper_id+' .'+appending_div).length) + 1; //text box count

    if(i < max_fields) { //max input box allowed
        i++; //text box increment
        $(wrapper).append(`
            <div class="row `+appending_div+`" id="`+appending_div+i+`">
                <div class="col-sm-9">
                    <label>`+lbl_txt1+` `+i+`</label>
                    <input type="text" name="`+field_name1+`[]" class="form-control" />
                    <input type="hidden" name="exam_id[]" class="form-control" />
                </div>
                <div class="col-sm-2">
                    <label>Answer</label>
                    <select name="answer[]" id="answer_`+i+`" class="form-control cls-answer">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div class="col-sm-1">
                    <label>&nbsp;</label>
                    <span style="margin-top: 30px;" title="<?php echo get_languageword('remove_this'); ?>" class="btn btn-danger" id="`+i+`" onclick="remove_exam_field(\'`+wrapper_id+`\', this.id, \'`+appending_div+`\', \'`+lbl_txt1+`\',  \'`+field_name1+`\');" ><i class="fa fa-minus"></i></span>
                </div>
            </div>`
        ); 
        //add input box
    }
}

function remove_field(wrapper_id, remove_button_id, appending_div, lbl_txt1, lbl_txt2, field_name1, field_name2, lbl_txt3)
{
    let curr_id = $('#'+appending_div+remove_button_id).find('input[name="curriculum_id[]"]').val();
    if(curr_id != '') {
        removedCurrIds.push(curr_id);
        $('input[name="removed_curriculum"]').val(removedCurrIds.join(','));
    }
    $('#'+appending_div+remove_button_id).remove();
    sort_appended_fields(wrapper_id, appending_div, lbl_txt1, lbl_txt2, field_name1, field_name2, lbl_txt3);
}

function remove_exam_field(wrapper_id, remove_button_id, appending_div, lbl_txt1, field_name1)
{
    let exam_id = $('#'+appending_div+remove_button_id).find('input[name="exam_id[]"]').val();
    if(exam_id != '') {
        removedExamIds.push(exam_id);
        $('input[name="removed_exam"]').val(removedExamIds.join(','));
    }
    $('#'+appending_div+remove_button_id).remove();
    sort_appended_exam_fields(wrapper_id, appending_div, lbl_txt1, field_name1);
}

function sort_appended_fields(wrapper_id, appending_div, lbl_txt1, lbl_txt2, field_name1, field_name2, lbl_txt3)
{
    var field_val       = "";
    var div_field_id    = "";
    var selector        = $('#'+wrapper_id+' .'+appending_div);
    var i               = 1;

    $(selector).each(function() {
        i++;

        div_field_id    = appending_div+i;

        $(this).attr('id', div_field_id);
        $(this).find('label:first').text(lbl_txt1+' '+i);
        $(this).find('label').eq(2).text(lbl_txt2+' '+i);
        $(this).find('label').eq(3).text(lbl_txt3+' '+i);
        $(this).find('span:first').attr('id', i);
        $(this).find('.cls-source_type').attr('id', 'source_type_'+i);
        $(this).find('.cls-source').attr('id', 'source_'+i);
    });
}

function sort_appended_exam_fields(wrapper_id, appending_div, lbl_txt1, field_name1)
{
    var field_val       = "";
    var div_field_id    = "";
    var selector        = $('#'+wrapper_id+' .'+appending_div);
    var i               = 1;

    $(selector).each(function() {
        i++;

        div_field_id    = appending_div+i;

        $(this).attr('id', div_field_id);
        $(this).find('label:first').text(lbl_txt1+' '+i);
        $(this).find('span:first').attr('id', i);
        $(this).find('.cls-answer').attr('id', 'answer_'+i);
    });
}
//Add/Remove Fields Dynamically - End

//calculate discount percent
function calculate_discount_percent(actual_price, discounted_price) {
    let per = 0.00;
    actual_price = parseFloat(actual_price);
    discounted_price = parseFloat(discounted_price);
    if(actual_price > 0) {
        per = ((actual_price - discounted_price)*100)/actual_price;
    } else {
        per = 0.00;
    }
    return per.toFixed(2);
}

$(document).on('change', '.cls-source_type', function() {

    var ref = $(this);
    var sno = ref.attr('id').split('_')[2];
    var refval = ref.val();

    if(refval == "file") {

        $('#source_'+sno).html('<input type="file" name="lesson_file['+(sno-1)+']" class="form-control lesson-file-input" />');

    } else {

        $('#source_'+sno).html('<input type="text" name="lesson_url['+(sno-1)+']" class="form-control lesson-url-input" />');
    }


});



$(document).on('click', '.delete-icon-grocery', function() {
    return confirm("<?php echo get_languageword('Are you sure that you want to delete this record?'); ?>");
});

$(document).on('keyup', '#actual_price', function(){
    $('#book_price').val($(this).val());
    $('#book_discount_percent').val(calculate_discount_percent($(this).val(), $('#book_price').val()));
});

$(document).on('keyup', '#book_price', function(){
    $('#book_discount_percent').val(calculate_discount_percent($('#actual_price').val(), $(this).val()));
});

$(document).on('keyup', '#book_discount_percent', function(){
    let book_price = parseFloat($('#book_price').val());
    let actual_price = parseFloat($('#actual_price').val());
    let dis_per = parseFloat($(this).val());
    if(actual_price > 0) {
        book_price = (actual_price - (actual_price/100)*dis_per);
    } else {
        book_price = actual_price;
        $(this).val(0.00);
    }
    $('#book_price').val(book_price.toFixed(2));
});

$(document).ready(function(){
    $('#book_price').keyup();
});

</script>
<?php } ?>
	
<!--Bootstrap Page-->
<script src="<?php echo URL_FRONT_JS;?>bootstrap.min.js"></script>
<!--Profile Page-->
<script src="<?php echo URL_FRONT_JS;?>marquee.js"></script>
<script type="text/javascript" src="<?php echo URL_FRONT_JS;?>flatpickr.min.js"></script>

<script type="text/javascript" src="<?php echo URL_FRONT_JS;?>select2.min.js"></script>
<script type="text/javascript" src="<?php echo URL_FRONT_JS;?>owl.carousel.min.js"></script>
<script type="text/javascript" src="<?php echo URL_FRONT_JS;?>jRate.min.js"></script>
<script type="text/javascript" src="<?php echo URL_FRONT_JS;?>jquery.magnific-popup.js"></script>
<script type="text/javascript" src="<?php echo URL_FRONT_JS;?>jquery.smartmenus.js"></script>
<script type="text/javascript" src="<?php echo URL_FRONT_JS;?>jquery.smartmenus.bootstrap.js"></script>
<script type="text/javascript" src="<?php echo URL_FRONT_JS;?>flexgrid.js"></script>
<script type="text/javascript" src="<?php echo URL_FRONT_JS;?>countUp.js"></script>
<script type="text/javascript" src="<?php echo URL_FRONT_JS;?>jquery.dataTables.min.js"></script>
<!-- Custom Script -->
<script type="text/javascript" src="<?php echo URL_FRONT_JS;?>main.js"></script>

<!--Gallery-->
<script type="text/javascript" src="<?php echo URL_FRONT_JS;?>fileinput.min.js"></script>

<?php
//if($current_controller == 'home' && $current_method == 'contact_us')
//{
?>

<!--COntact us page-->
<!--script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC-h5q6y2eBfFV5X7QV6Z5mrFFU2s97XJs&sensor=false"></script>
<script>
$(document).ready(function () {
    "use strict";

    function e() {
        var e = {
                center: a,
                zoom: 10,
                /*scrollwheel:!0*/
                scrollwheel: false,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                styles: [{
                    featureType: "landscape",
                    stylers: [{
                        hue: "#FFBB00"
                }, {
                        saturation: 43.400000000000006
                }, {
                        lightness: 37.599999999999994
                }, {
                        gamma: 1
                }]
            }, {
                    featureType: "road.highway",
                    stylers: [{
                        hue: "#FFC200"
                }, {
                        saturation: -61.8
                }, {
                        lightness: 45.599999999999994
                }, {
                        gamma: 1
                }]
            }, {
                    featureType: "road.arterial",
                    stylers: [{
                        hue: "#FF0300"
                }, {
                        saturation: -100
                }, {
                        lightness: 51.19999999999999
                }, {
                        gamma: 1
                }]
            }, {
                    featureType: "road.local",
                    stylers: [{
                        hue: "#FF0300"
                }, {
                        saturation: -100
                }, {
                        lightness: 52
                }, {
                        gamma: 1
                }]
            }, {
                    featureType: "water",
                    stylers: [{
                        hue: "#0078FF"
                }, {
                        saturation: -13.200000000000003
                }, {
                        lightness: 2.4000000000000057
                }, {
                        gamma: 1
                }]
            }, {
                    featureType: "poi",
                    stylers: [{
                        hue: "#00FF6A"
                }, {
                        saturation: -1.0989010989011234
                }, {
                        lightness: 11.200000000000017
                }, {
                        gamma: 1
                }]
            }]

            },
            t = new google.maps.Map(document.getElementById("sellers_map"), e),
            s = {
                url: "assets/front/images/logo_google_map.png"
            },
            o = new google.maps.Marker({
                position: a,
                map: t,
                icon: s,
                animation: google.maps.Animation.BOUNCE
            });
        o.setMap(t);
        var n = new google.maps.InfoWindow({
            content: "<strong><?php echo $this->config->item('site_settings')->address; ?> <br> <?php echo strip_tags($this->config->item('site_settings')->city); ?>, <?php echo $this->config->item('site_settings')->state; ?> <?php echo $this->config->item('site_settings')->zipcode; ?></strong>"
        });
        google.maps.event.addListener(o, "click", function () {
            n.open(t, o)
        })
    }
    var a;
    a = new google.maps.LatLng("17.4459764", "78.38607860000002"), google.maps.event.addDomListener(window, "load", e), a = new google.maps.LatLng("17.4459764", "78.38607860000002")
});
</script-->

<?php //} ?>

<?php if(isset($activemenu) && $activemenu == 'my_book_purchases'): ?>
    <script>
        $(document).ready(function() {
            $('.view-icon-grocery.view-plant-tree-icon.crud-action').each(function(){
                $(this).attr('target','_blank');
                if($(this).attr('href') == 'javascript:void(0)') {
                    $(this).hide();
                }
                if($(this).attr('href') == '') {
                    $(this).attr('title', 'Planting tree is in progress');
                }
            });
        });
    </script>
<?php endif; ?>

<script>
    $(function() {
		var book_exam_quiz_answer_count = 0;

        $(".stu-certificate").attr("target", "_blank");

		$('.show_book_quiz_modal').attr('href', 'javascript: void(0);');
		//$('.download_book_quiz_link').attr('href', 'javascript: void(0);');

        $('.show_book_quiz_modal').click(function () {
			//alert('Show Modal');
            $("#book_exam_quiz_modal").modal("show");
        });

		$('.common_book_exam_quiz_class').click(function () {
			//alert('in common_book_exam_quiz_class');

			if($(this).prop('checked') == true)
				book_exam_quiz_answer_count = book_exam_quiz_answer_count + 1;
		});

		// on submit review 
		$('#submit-exam-quiz').on("click", function() {
			var total_book_exam_quiz = $('#total_book_exam_quiz').val();

			//alert('book_exam_quiz_answer_count :: ' + book_exam_quiz_answer_count);
			//alert('total_book_exam_quiz :: ' + total_book_exam_quiz);

			var success = true;

			if(book_exam_quiz_answer_count < total_book_exam_quiz) {
				success = false;
			}

			if(success) {
				$.ajax({
					type: 'POST',
					url: '<?php echo base_url("buyer/save_book_exam_quiz_result"); ?>',
					data: $('#exam_quiz_form').serialize(),
					success: function(data) {
						if (data.response == 'success') {
							$("#book_exam_quiz_modal").modal("hide");

							//alert(data.message);

							var n = noty({
										text: data.message,
										type: 'success',
										dismissQueue: false,
										//layout: 'topCenter',
										//theme: 'defaultTheme'
									});

							setTimeout(function() {
								$.noty.closeAll();

								location.reload();
							}, 5000);
						}
						if (data.response == 'error') {
						   alert(data.message);

								
						}
				   }
			   });
			} else {
				confirm('All question need to be answered. Please give all available answers.');
			}
	   });
    });
</script>

</body>

</html>
