<?php if(!empty($categories)) { ?>
<div class="col-xs-8 col-sm-3 sidebar-offcanvas" id="sidebar">
    <div class="panel-group dashboard-menu" id="accordion">
        <div class="dashboard-profile">
            <h3><?php echo get_languageword('categories'); ?></h3>
        </div>
        <ul class="cat-list">
            <?php 

            $counterSum=0;
                foreach ($categories as $key => $cat) {
                    $counterSum+=$cat->book_count;
            }

            ?>
            <li class="<?php if(isset($active_cat) && $active_cat == 'all_books') echo 'active'; ?>"><a href="<?php echo URL_HOME_BUY_BOOKS;?>"> <?php echo get_languageword('all_selling_books') .' ('. $counterSum .')' ; ?></a></li>
            <?php foreach ($categories as $row) { 

                ?>
                <li class="<?php if(isset($active_cat) && $active_cat == $row->slug) echo 'active'; ?>" title="<?php echo $row->name; ?>" ><a href="<?php echo URL_HOME_BUY_BOOKS.'/'.$row->slug;?>"> <?php echo $row->name .' ('. $row->book_count .')'; ?></a></li>
            <?php } ?>
        </ul>
    </div>
    <!-- /.panel-group -->
</div>
<?php } ?>