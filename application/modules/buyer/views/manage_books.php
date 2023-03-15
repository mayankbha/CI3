<!-- Dashboard panel ends -->
<?php echo $this->session->flashdata('message');?>
<div class="dashboard-panel">
<?php 
if(isset($subjects))
neatPrint($subjects);
if(count($books) > 0) { ?>
<?php echo form_open('buyer/manage-books', 'id="buyer_subject_mngt" class="form-multi-select"');?>
	<div class="custom_accordion">
		<?php
            foreach($books as $key=>$val) {
            
            $category = explode('_', $key);

            //Category Details
            $category_id   = $category[0];
            $category_slug = $category[1];
            $category_name = $category[2];

            ?>
		<h3><?php echo $category_name;?></h3>
		<div class="row">
		<?php
		$i = $counter  = 1;
		foreach($val as $key1=>$val1) 
		{
			
			$book   = explode('_', $val1);
            //Book Details
            $book_id   = $book[0];
            $book_slug = $book[1];
            $book_name = $book[2];

			if($i == 1)
			{
			?>
			<div class="col-md-4 col-sm-6">
			<?php
			} 
			
			?>
				<div class="input-group ">
					<div class="checkbox">
						<label>
						<input type="checkbox" value="<?php echo $book_id;?>" name="buyer_books[]" <?php if(in_array($book_id, $studnentPrefferedBookIds)) echo "checked";?>>
							<span class="checkbox-content">
								<span class="item-content"><?php echo $book_name;?></span>
								<i aria-hidden="true" class="fa fa-check "></i>
								<i class="check-square"></i>
							</span>
						</label>
					</div>
				</div>
			<?php
			$i++;
			if($i == 3 || count($val) == $counter) { // three items in a row. Edit this to get more or less items on a row
        echo '</div>';
        $i = 1;
    }
	
	$counter++;
		} ?>
		</div>
	
			<?php }
			?>
			</div>
			<button class="btn-link-dark dash-btn" name="Submit" type="Submit"><?php echo get_languageword('UPDATE');?></button>
</form>
			
			<?php
			} ?>
</div>