<?php
if (isset($_POST['submitted']) && isset($_POST['wpmi_product_nonce_field']) && wp_verify_nonce($_POST['wpmi_product_nonce_field'], 'wpmi_product_nonce')) {
    $products_groups = ( isset($_POST['wpmi_product_groups']) && !empty($_POST['wpmi_product_groups'])) ? $_POST['wpmi_product_groups'] : array() ;
    $products_list = $_POST['wpmi_product_list'];
    foreach ($products_list as $product_id) {
        if (array_key_exists($product_id, $products_groups)) {
            $update = update_post_meta($product_id, '_wpmi_subscription_groups', $products_groups[$product_id]);
        } else {
            $update = update_post_meta($product_id, '_wpmi_subscription_groups', NULL);
        }
    }
    echo '<div class="updated notice"><p>'.__('Products Has Been Saved.', 'wpmi').'</p></div>';
}
$current = ( isset($_GET['paged']) && !empty($_GET['paged']) ) ? $_GET['paged']: 1 ;
$product_title = ( isset($_GET['product_title']) && !empty($_GET['product_title']) ) ? $_GET['product_title']: '' ;
$count_products = wp_count_posts('product');
$all = $count_products->publish;
$limit = get_option( 'posts_per_page' );
$last = ceil($all/$limit);
$current = ($current>$last) ? $last : $current ;
$current = ($current<1) ? 1 : $current ;
$next = $current+1;
$prev = $current-1;
$offset = $prev * $limit;
$first = 1;
$url = 'admin.php?page=wpmi&tab=products';
if (isset($product_title) && !empty($product_title)) {
    $products_args = array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        's' => $product_title,
    );
} else {
    $products_args = array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => $limit,
        'offset' => $offset,
    );
}
$all_product = new WP_Query($products_args);
$all_groups = $this->group_api->get();
?>
    <div class="tablenav top">

        <div class="alignleft actions bulkactions">
            <form class="" action="<?php echo admin_url('admin.php') ?>" method="get">
                <input type="hidden" name="page" value="wpmi">
                <input type="hidden" name="tab" value="products">
                <input type="search" id="product_title" name="product_title" value="<?php echo $product_title ?>">
                <input type="submit" id="doaction" class="button action" value="<?php _e('Search', 'wpmi') ?>">
            </form>
        </div>

        <?php if (empty($product_title)): ?>
        <div class="tablenav-pages">
            <form class="" action="<?php echo admin_url('admin.php') ?>" method="get">
                <input type="hidden" name="page" value="wpmi">
                <input type="hidden" name="tab" value="products">
                <span class="displaying-num"><?php echo $all ?> <?php _e('Item', 'wpmi') ?></span>

                <span class="pagination-links">

                    <?php if ($current == 1): ?>
                        <span class="tablenav-pages-navspan" aria-hidden="true">«</span>
                        <span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
                    <?php else: ?>
                        <a class="prev-page" href="<?php echo admin_url($url.'&paged='.$first) ?>"><span class="screen-reader-text"><?php _e('First Page', 'wpmi') ?></span><span aria-hidden="true">«</span></a>
                        <a class="prev-page" href="<?php echo admin_url($url.'&paged='.$prev) ?>"><span class="screen-reader-text"><?php _e('Previous Page', 'wpmi') ?></span><span aria-hidden="true">‹</span></a>
                    <?php endif; ?>

                    <span class="paging-input">
                        <label for="current-page-selector" class="screen-reader-text"><?php _e('Current Page', 'wpmi') ?></label>
                            <input class="current-page" id="current-page-selector" type="text" name="paged" value="<?php echo $current ?>" size="2" aria-describedby="table-paging"> <?php _e('From', 'wpmi') ?> <span class="total-pages"><?php echo $last ?></span>
                    </span>

                    <?php if ($current == $last): ?>
                        <span class="tablenav-pages-navspan" aria-hidden="true">›</span>
                        <span class="tablenav-pages-navspan" aria-hidden="true">»</span>
                    <?php else: ?>
                        <a class="next-page" href="<?php echo admin_url($url.'&paged='.$next) ?>"><span class="screen-reader-text"><?php _e('Next Page', 'wpmi') ?></span><span aria-hidden="true">›</span></a>
                        <a class="last-page" href="<?php echo admin_url($url.'&paged='.$last) ?>"><span class="screen-reader-text"><?php _e('Last Page', 'wpmi') ?></span><span aria-hidden="true">»</span></a>
                    <?php endif; ?>

                </span>
            </form>
        </div><!-- tablenav-pages -->
        <?php endif; ?>
        <br class="clear">
    </div><!-- tablenav -->

    <form class="" action="" method="post">
        <table class="wp-list-table widefat fixed striped wpmi_product_list">
			<tbody>
                <?php
                    $couner = 0;
                    while ($all_product->have_posts()): $all_product->the_post();
                    $old_group = get_post_meta(get_the_id(), 'wpmi_subscription_group', true);
                    $product_groups = get_post_meta(get_the_id(), '_wpmi_subscription_groups', true);
                    $groups = array();

                    if (!empty($product_groups)) {
                        $groups = $product_groups;
                    }
                    if (isset($old_group) && !empty($old_group) && $old_group != 'none') {
                        $groups[] = $old_group;
                    }
                ?>
                <tr data-id="<?php echo $couner ?>">
					<th><a href="<?php echo get_edit_post_link(get_the_Id()) ?>" target="_blank"><?php the_title() ?></a></th>
					<th>
                        <select class="wpmi_select2" name="wpmi_product_groups[<?php the_id(); ?>][]" multiple="multiple">
                            <?php
                                if (isset($all_groups)) {
                                    $group_id = get_post_meta(get_the_id(), 'wpmi_subscription_group', true);
                                    foreach ($all_groups as $key => $group) {
                                        $selected = (in_array($group->id, $groups)) ? ' selected="selected"' : '';
                                        echo '<option value="'.$group->id.'" '.$selected.'>'.$group->name.' ( '.$group->total.' '.__('User', 'wpmi').')</option>';
                                    }
                                }
                            ?>
                        </select>
                    </th>
					<input type="hidden" value="<?php the_id(); ?>" name="wpmi_product_list[]">
				</tr>
                <?php ++$couner; endwhile; wp_reset_postdata(); ?>
            </tbody>
            <thead>
				<tr>
					<th><?php _e('Product', 'wpmi'); ?></th>
					<th><?php _e('Group', 'wpmi'); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
                    <th><?php _e('Product', 'wpmi'); ?></th>
					<th><?php _e('Group', 'wpmi'); ?></th>
				</tr>
			</tfoot>
        </table>
        <?php wp_nonce_field('wpmi_product_nonce', 'wpmi_product_nonce_field'); ?>
        <input type="hidden" name="submitted" id="submitted" value="true" />
        <input type="hidden" name="wallet_action" id="submitted" value="custom" />
        <?php submit_button(); ?>
    </form>
    <?php
        // $group_options = '';
        // if (isset($all_groups) && is_array($all_groups)) {
        //     foreach ($all_groups as $key => $group) {
        //         $group_options .= '<option value="'.$group->id.'">'.$group->name.' - '.$group->total.__('User', 'wpmi').'</option>';
        //     }
        // }
        // $product_options = '';
        // while ($product_has_not_field->have_posts()) {
        //     $product_has_not_field->the_post();
        //     $product_options .= '<option value="'.get_the_ID().'">'.get_the_title().'</option>';
        // }
    ?>

    <script type="text/javascript">
    jQuery(document).on('click', '.wpmi_add_row', function(event) {
        event.preventDefault();
        var id = jQuery(this).parents('tr').data('id')+1;
        var html =
                '<tr data-id="'+id+'">'+
                    '<th><select class="wpmi_select" name="wpmi_product['+id+'][post_id]"><?php echo $product_options ?></select></th>'+
                    '<th colspan="3"><select name="wpmi_product['+id+'][group_id]">'+
                    '<?php echo $group_options; ?>'+
                    '</select></th>'+
                '</tr>';
        jQuery(this).parents('tr').after(html);
    });
    </script>
