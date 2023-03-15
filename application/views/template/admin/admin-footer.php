<script src='<?php echo URL_ADMIN_JS;?>adminlte.min.js'></script>
<?php
if(isset($grocery) && $grocery == TRUE) 
{
?>
<?php foreach($js_files as $file): 
//echo basename($file).'<br>';
//if(in_array(basename($file), array('lazyload-min.js'))) {
?>
<script src="<?php echo $file; ?>"></script>
<?php //}
endforeach; ?>
<?php } ?>


<?php if(!empty($activemenu) && $activemenu == "seller_selling_books") { ?>
<script type="text/javascript" src="<?php echo URL_FRONT_JS;?>jquery.magnific-popup.js"></script>
<script> 
$(document).on('click', '.delete-icon-grocery', function() {

    return confirm("<?php echo get_languageword('Are you sure that you want to delete this record?'); ?>");
});
</script>
<?php } ?>




<!--
<script type="text/javascript">
$(document).ready(function(){
  $('.tDiv3').append('<a id="my_button" href="#">new button</a>');
});
</script>
-->
<!--<script src='<?php echo URL_ADMIN_JS;?>lib.min.js'></script>
<script src='<?php echo URL_ADMIN_JS;?>app.min.js'></script>-->

<?php if(isset($activemenu) && $activemenu == 'purchased_books'): ?>
	<script>
		//update the seller due amount
		function get_seller_amt_due() {
			let due_amt = 0.00;
			let item_price = parseFloat($('#item_price').val());
			let trans_fees = parseFloat($('#field-fee').val());
			let paid_to_seller = parseFloat($('#field-paid_to_seller').val());
			let admin_commission_val = parseFloat($('#admin_commission_val').val());
			due_amt = item_price - admin_commission_val - trans_fees - paid_to_seller;
			return due_amt.toFixed(2);
		}

		$(document).on('keyup', '#field-paid_to_seller,#field-fee', function() {
			let due_amt = get_seller_amt_due();
			if(due_amt < 0) {
				alert('Due amount cannot be negative, because of negative due value the page will refresh automatically to avoid any human errors');
				window.location.reload();
			} else {
				$('#field-total_seller_due').val(get_seller_amt_due());
			}
		});

		$(document).ready(function(){
			let plant_tree_url = ($('#plant_tree_link').length > 0) ? $('#plant_tree_link').val() : '';
			if(plant_tree_url != '') {
				$('#form-button-save').closest('.form-button-box').after(`
					<div class="form-button-box">
						<input type="button" onclick="window.location.href='`+plant_tree_url+`';" value="Retry Plant Tree" class="btn btn-large">
					</div>
				`);
			}
		});
	</script>
<?php endif; ?>

<?php if(isset($activemenu) && $activemenu == 'settings'): ?>
	<script>
		$(document).ready(function(){
			$('select[name="field[311]"]').change();
			$('select[name="field[315]"]').change();
		});
		$('select[name="field[311]"]').change(function(){
			if($(this).val() == 'YES') {
				$('input[name="field[312]"]').closest('.form-field-box').show();
				$('input[name="field[313]"]').closest('.form-field-box').show();
			} else {
				$('input[name="field[312]"]').closest('.form-field-box').hide();
				$('input[name="field[313]"]').closest('.form-field-box').hide();
			}
		});
		
		
			$('select[name="field[315]"]').change(function(){
			if($(this).val() == 'Yes') {
				$('input[name="field[316]"]').closest('.form-field-box').show();
				$('input[name="field[317]"]').closest('.form-field-box').show();
				$('input[name="field[318]"]').closest('.form-field-box').show();
				$('input[name="field[319]"]').closest('.form-field-box').show();
			} else {
				$('input[name="field[316]"]').closest('.form-field-box').hide();
				$('input[name="field[317]"]').closest('.form-field-box').hide();
				$('input[name="field[318]"]').closest('.form-field-box').hide();
				$('input[name="field[319]"]').closest('.form-field-box').hide();
			}
		});
		
		
	</script>
<?php endif; ?>

</section>
	</div>

		<footer class="main-footer">
		</footer>
</div>
	
		</body>
</html>