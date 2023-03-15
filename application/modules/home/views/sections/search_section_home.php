<?php if(!empty($location_opts) || !empty($book_opts)) { 

        echo form_open(URL_HOME_SEARCH_SELLER, 'id="search_form"');
?>

<ul class="home-search">


    <?php if(!empty($book_opts)) { ?>
    <li>
        <?php

                echo form_dropdown('book_slug[]', $book_opts, '', 'class="select-picker" required="required" ');

        ?>
    </li>
    <?php } ?>


    <li>
        <button type="submit" class="btn btn-search"><i class="fa fa-search"></i><?php echo get_languageword('Search');?></button>
    </li>


</ul>

<?php 
        echo form_close();
?>

<style>
    label.error {
        color: #FF3300;
        float: left;
        margin-top: -30px;
    }
</style>


<script src="<?php echo URL_FRONT_JS;?>jquery.js"></script>
<script src="<?php echo URL_FRONT_JS;?>jquery.validate.min.js"></script>
<script type="text/javascript"> 
  (function($,W,D)
   {
      var JQUERY4U = {};
   
      JQUERY4U.UTIL =
      {
          setupFormValidation: function()
          {
              //form validation rules
              $("#search_form").validate({
                  rules: {
                        "location_slug[]": {
                            required: true
                        },
                        "book_slug[]": {
                            required: true
                        }
                  },

                  messages: {
                        "book_slug[]": {
                            required: "<?php echo get_languageword('please_select_book'); ?>"
                        }
                  },

                  submitHandler: function(form) {
                      form.submit();
                  }
              });
          }
      }
         //when the dom has loaded setup form validation rules
     $(D).ready(function($) {
         JQUERY4U.UTIL.setupFormValidation();
     });
 })(jQuery, window, document);           
</script>




<?php } ?>
