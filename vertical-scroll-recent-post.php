<?php
/*
Plugin Name: Vertical Scroll Recent Post
Plugin URI: http://www.gopiplus.com/work/2010/07/18/vertical-scroll-recent-post/
Description: Vertical Scroll Recent Post plugin scroll the recent post title in the widget, the post scroll from bottom to top vertically.
Author: Gopi Ramasamy
Author URI: http://www.gopiplus.com/work/2010/07/18/vertical-scroll-recent-post/
Version: 11.7.1
Tags: Vertical, scroll, recent, post, title, widget
vsrp means Vertical Scroll Recent Post
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

function vsrp() {
    global $wpdb;

    $dis_num_height = get_option( 'vsrp_dis_num_height' );
    $vsrp_title_length = get_option( 'vsrp_title_length' );
    $dis_num_user = get_option( 'vsrp_dis_num_user' );
    $num_user = get_option( 'vsrp_select_num_user' );
    $vsrp_select_categories = get_option( 'vsrp_select_categories' );
    $vsrp_select_orderby = get_option( 'vsrp_select_orderby' );
    $vsrp_select_order = get_option( 'vsrp_select_order' );
    $vsrp_show_date = get_option( 'vsrp_show_date' );
    $vsrp_date_format = get_option( 'vsrp_date_format' );
    $vsrp_show_category_link = get_option( 'vsrp_show_category_link' );
    $vrsp_show_thumb = get_option( 'vrsp_show_thumb' );
    $vsrp_speed = get_option( 'vsrp_speed' );
    $vsrp_seconds = get_option( 'vsrp_seconds' );
    
    if( !is_numeric( $num_user ) ) {
        $num_user = 5;
    } 
    if( !is_numeric( $dis_num_height ) ) {
        $dis_num_height = 30;
    }
    if( !is_numeric( $dis_num_user ) ) {
        $dis_num_user = 5;
    }

    $sSql = query_posts( 'cat='.$vsrp_select_categories.'&orderby='.$vsrp_select_orderby.'&order='.$vsrp_select_order.'&showposts='.$num_user );

    $vsrp_data = $sSql;
    $vsrp_html = "";
    $vsrp_x = "";
    if ( !empty( $vsrp_data ) ) {
        $vsrp_count = 0;
        foreach ( $vsrp_data as $vsrp_data ) {
            $vsrp_post_title = $vsrp_data->post_title;
            $get_permalink = get_permalink( $vsrp_data->ID );
            if ( strlen( $vsrp_post_title ) > $vsrp_title_length ) {
                $vsrp_post_title = substr( $vsrp_post_title, 0, $vsrp_title_length );
                $vsrp_post_title .= '...';
            }
            $vsrp_post_date = date( $vsrp_date_format, strtotime( $vsrp_data->post_date ) );

            $dis_height = $dis_num_height."px";
            $vsrp_html = $vsrp_html . "<div class='vsrp_div' style='height:$dis_height;'>";
            if ( $vrsp_show_thumb ) {
                $vsrp_html = $vsrp_html . get_the_post_thumbnail( $vsrp_data->ID, array( $dis_num_height, $dis_num_height ), array( 'class' => 'vsrp_thumb' ) );
            }
            $vsrp_html = $vsrp_html . "<div class=\"vsrp_text\"><a href='$get_permalink'>$vsrp_post_title</a>";
            if ( $vsrp_show_date ) {
                $vsrp_html = $vsrp_html . " -- <span class='vrsp_date'>$vsrp_post_date</span>";
            }
            $vsrp_html = $vsrp_html . "</div></div>";
            
            $vsrp_post_title = esc_sql( trim( $vsrp_post_title ) );
            $get_permalink = esc_sql( $get_permalink );
            $vsrp_x = $vsrp_x . "vsrp_array[ $vsrp_count ] = '<div class=\'vsrp_div\' style=\'height:$dis_height;\'>";
            if ( $vrsp_show_thumb ) {
                $vsrp_x = $vsrp_x . get_the_post_thumbnail( $vsrp_data->ID, array( $dis_num_height, $dis_num_height ), array( 'class' => 'vsrp_thumb' ) );
            }
            $vsrp_x = $vsrp_x . "<div class=\"vsrp_text\"><a href=\'$get_permalink\'>$vsrp_post_title</a>";
            if ( $vsrp_show_date ) {
                $vsrp_x = $vsrp_x . " -- <span class=\'vrsp_date\'>$vsrp_post_date</span>";
            }
            $vsrp_x = $vsrp_x . "</div></div>'; ";
            $vsrp_count++;
        }
        $dis_num_height = $dis_num_height + 8;
        if( $vsrp_count >= $dis_num_user ) {
            $vsrp_count = $dis_num_user;
            $vsrp_height = ( $dis_num_height * $dis_num_user );
        } else {
            $vsrp_count = $vsrp_count;
            $vsrp_height = ( $vsrp_count * $dis_num_height );
        }
        $vsrp_height1 = $dis_num_height."px";
        ?>
        <div id="vsrp_widget">
            <div style="<?php echo $vsrp_height1; ?>;" id="vsrp_Holder">
                <?php echo $vsrp_html; ?>
            </div>
            <?php if ( $vsrp_show_category_link ): ?>
                <span id="vsrp_category_link">
                    <a href="<?php echo get_category_link( $vsrp_select_categories ); ?>">
                        <?php _e( 'Show all of ', 'vertical-scroll-recent-post'); echo get_option( 'vsrp_title' ); ?>
                    </a>
                </span>
            <?php endif; ?>
        </div>
        <script type="text/javascript">
            var vsrp_array  = new Array();
            var vsrp_obj = '';
            var vsrp_scrollPos = '';
            var vsrp_numScrolls = '';
            var vsrp_heightOfElm = '<?php echo $dis_num_height; ?>';
            var vsrp_numberOfElm = '<?php echo $vsrp_count; ?>';
            var vsrp_speed = '<?php echo $vsrp_speed; ?>';
            var vsrp_seconds = '<?php echo $vsrp_seconds; ?>';
            var vsrp_scrollOn   = 'true';
            function vsrp_createscroll() {
                <?php echo $vsrp_x; ?>
                vsrp_obj = document.getElementById( 'vsrp_Holder' );
                vsrp_obj.style.height = (vsrp_numberOfElm * vsrp_heightOfElm) + 'px';
                vsrp_content();
            }
        </script>
        <script type="text/javascript">vsrp_createscroll();</script>
        <?php
    } else {
        echo "<div class=\"vsrp_error\">" . __( 'No data available', 'vertical-scroll-recent-post' ) . "</div>";
    }
    wp_reset_query();
}

function vsrp_install() {
    add_option( 'vsrp_title', "Recent Post" );
    add_option( 'vsrp_dis_num_height', 35 );
    add_option( 'vsrp_title_length', 30 );
    add_option( 'vsrp_dis_num_user', 5 );
    add_option( 'vsrp_select_num_user', 10 );
    add_option( 'vsrp_select_categories', "1" );
    add_option( 'vsrp_exclude_categories', 0 );
    add_option( 'vsrp_select_orderby', "date" );
    add_option( 'vsrp_select_order', "DESC" );
    add_option( 'vsrp_show_date', 0 );
    add_option( 'vsrp_date_format', get_option( 'date_format' ) );
    add_option( 'vsrp_show_category_link', 0 );
    add_option( 'vrsp_show_thumb', 0 );
    add_option( 'vsrp_speed', 2 );
    add_option( 'vsrp_seconds', 2 );
}

function vsrp_control() {
    echo '<p><b>';
    _e( 'Vertical Scroll Recent Post', 'vertical-scroll-recent-post' );
    echo '.</b> ';
    _e( 'Check official website for more information', 'vertical-scroll-recent-post' );
    ?> <a target="_blank" href="http://www.gopiplus.com/work/2010/07/18/vertical-scroll-recent-post/"><?php _e( 'click here', 'vertical-scroll-recent-post' ); ?></a></p><?php
}

function vsrp_widget($args) {
    extract($args);
    echo $before_widget . $before_title;
    echo get_option( 'vsrp_title' );
    echo $after_title;
    vsrp();
    echo $after_widget;
}

function vsrp_admin_options() {
    global $wpdb;
    $txt_example = __( 'Example', 'vertical-scroll-recent-post' );
    wp_enqueue_script( 'vertical-scroll-recent-post', plugins_url().'/vertical-scroll-recent-post/vertical-scroll-recent-post.js' );
    ?>
    <div class="wrap">
        <?php
        $vsrp_title = get_option( 'vsrp_title' );
        $vsrp_dis_num_height = get_option( 'vsrp_dis_num_height' );
        $vsrp_title_length = get_option( 'vsrp_title_length' );
        $vsrp_dis_num_user = get_option( 'vsrp_dis_num_user' );
        $vsrp_select_num_user = get_option( 'vsrp_select_num_user' );
        $vsrp_select_categories = get_option( 'vsrp_select_categories' );
        $vsrp_exclude_categories = get_option( 'vsrp_exclude_categories' );
        $vsrp_select_orderby = get_option( 'vsrp_select_orderby' );
        $vsrp_select_order = get_option( 'vsrp_select_order' );
        $vsrp_show_date = get_option( 'vsrp_show_date' );
        $vsrp_date_format = get_option( 'vsrp_date_format' );
        $vsrp_show_category_link = get_option( 'vsrp_show_category_link' );
        $vrsp_show_thumb = get_option( 'vrsp_show_thumb' );
        $vsrp_speed = get_option( 'vsrp_speed' );
        $vsrp_seconds = get_option( 'vsrp_seconds' );
        if ( isset( $_POST[ 'vsrp_form_submit' ] ) && $_POST[ 'vsrp_form_submit' ] == 'yes' ) {
            check_admin_referer( 'vsrp_form_setting' );
            $vsrp_title = stripslashes( $_POST[ 'vsrp_title' ] );
            $vsrp_dis_num_height = stripslashes( $_POST[ 'vsrp_dis_num_height' ] );
            $vsrp_title_length = stripslashes( $_POST[ 'vsrp_title_length' ] );
            $vsrp_dis_num_user = stripslashes( $_POST[ 'vsrp_dis_num_user' ] );
            $vsrp_select_num_user = stripslashes( $_POST[ 'vsrp_select_num_user' ] );
            $vsrp_select_orderby = stripslashes( $_POST[ 'vsrp_select_orderby' ] );
            $vsrp_select_order = stripslashes( $_POST[ 'vsrp_select_order' ] );
            $vsrp_show_category_link = stripslashes( $_POST[ 'vsrp_show_category_link' ] );
            $vrsp_show_thumb = stripslashes( $_POST[ 'vrsp_show_thumb' ] );
            $vsrp_speed = stripslashes( $_POST[ 'vsrp_speed' ] );
            $vsrp_seconds = stripslashes( $_POST[ 'vsrp_seconds' ] );
            $vsrp_show_date = stripslashes( $_POST[ 'vsrp_show_date' ] );
            $vsrp_date_format = stripslashes( $_POST[ 'vsrp_date_format' ] );
            $vsrp_exclude_categories = stripslashes( $_POST[ 'vsrp_exclude_categories' ] );
            if ( !isset( $_POST[ 'vsrp_select_categories' ] ) ) {
                $vsrp_select_categories = array( 1 );
            } else {
                $vsrp_select_categories = $_POST[ 'vsrp_select_categories' ];
            }
            if ( $vsrp_exclude_categories == 1 ) {
                $tmp = implode( ",-", $vsrp_select_categories );
                $tmp = "-".$tmp;
            } else {
                $tmp = implode( ",", $vsrp_select_categories );
            }
            $vsrp_select_categories = stripslashes( $tmp );
            
            //TODO check if value is empty
            update_option( 'vsrp_title', $vsrp_title );
            update_option( 'vsrp_dis_num_height', $vsrp_dis_num_height );
            update_option( 'vsrp_title_length', $vsrp_title_length );
            update_option( 'vsrp_dis_num_user', $vsrp_dis_num_user );
            update_option( 'vsrp_select_num_user', $vsrp_select_num_user );
            update_option( 'vsrp_select_categories', $vsrp_select_categories );
            update_option( 'vsrp_exclude_categories', $vsrp_exclude_categories );
            update_option( 'vsrp_select_orderby', $vsrp_select_orderby );
            update_option( 'vsrp_select_order', $vsrp_select_order );
            update_option( 'vsrp_show_date', $vsrp_show_date );
            update_option( 'vsrp_date_format', $vsrp_date_format );
            update_option( 'vsrp_show_category_link', $vsrp_show_category_link );
            update_option( 'vrsp_show_thumb', $vrsp_show_thumb );
            update_option( 'vsrp_speed', $vsrp_speed );
            update_option( 'vsrp_seconds', $vsrp_seconds );
            ?>
            <div class="updated fade">
                <p><strong><?php _e( 'Details successfully updated.', 'vertical-scroll-recent-post' ); ?></strong></p>
            </div>
            <?php
        }
        ?>
        <div id="icon-edit" class="icon32"></div>
        <h2><?php _e( 'Vertical Scroll Recent Post', 'vertical-scroll-recent-post' ); ?></h2>
        <h2 class="nav-tab-wrapper">
			<a href="#vsrp-tab-general" class="nav-tab nav-tab-active"><?php _e( 'General settings', 'vertical-scroll-recent-post' ); ?></a>
			<a href="#vsrp-tab-display" class="nav-tab"><?php _e( 'Display options', 'vertical-scroll-recent-post' ); ?></a>
			<a href="#vsrp-tab-scrolling" class="nav-tab"><?php _e( 'Scrolling options', 'vertical-scroll-recent-post' ); ?></a>
		</h2>
        <form name="vsrp_form" method="post" action="">
            <div class="table " id="vsrp-tab-general">
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th><?php _e( 'Widget title', 'vertical-scroll-recent-post' ); ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span><?php _e( 'Widget title', 'vertical-scroll-recent-post' ); ?></span></legend>
                                <label for="vsrp_title">
                                    <input name="vsrp_title" type="text" value="<?php echo $vsrp_title; ?>" id="vsrp_title" size="30" maxlength="150" />
                                    <br /><?php _e( 'Please enter your widget\'s title.', 'vertical-scroll-recent-post' ); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Post title\'s height', 'vertical-scroll-recent-post' ); ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span><?php _e( 'Post title\'s height', 'vertical-scroll-recent-post' ); ?></span></legend>
                                <label for="vsrp_dis_num_height">
                                    <input name="vsrp_dis_num_height" type="number" value="<?php echo $vsrp_dis_num_height; ?>" id="vsrp_dis_num_height" />
                                    <br /><?php _e( 'Please enter desired height for each post\'s title in widget. <br /> If any overlap in widget at front end, 
                                        you should change this value.', 'vertical-scroll-recent-post' ); ?> (<?php echo $txt_example; ?>: 35)
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Post title\'s length', 'vertical-scroll-recent-post' ); ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span><?php _e( 'Post title\'s length', 'vertical-scroll-recent-post' ); ?></span></legend>
                                <label for="vsrp_title_length">
                                    <input name="vsrp_title_length" type="number" value="<?php echo $vsrp_title_length; ?>" id="vsrp_title_length" />
                                    <br /><?php _e( 'Please enter desired length for each post\'s title in widget.', 'vertical-scroll-recent-post' ); ?>
                                    (<?php echo $txt_example; ?>: 30)
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Select orderby field', 'vertical-scroll-recent-post' ); ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span><?php _e( 'Select orderby field', 'vertical-scroll-recent-post' ); ?></span></legend>
                                <label for="vsrp_select_orderby">
                                    <select name="vsrp_select_orderby" id="vsrp_select_orderby">
                                        <option value='ID' <?php if ( $vsrp_select_orderby == 'ID' ) echo "selected='selected'";?> >ID</option>
                                        <option value='author' <?php if ( $vsrp_select_orderby == 'author' ) echo "selected='selected'"; ?> >Author</option>
                                        <option value='title' <?php if ( $vsrp_select_orderby == 'title' ) echo "selected='selected'"; ?> >Title</option>
                                        <option value='rand' <?php if ( $vsrp_select_orderby == 'rand' ) echo "selected='selected'"; ?> >Random order</option>
                                        <option value='date' <?php if ( $vsrp_select_orderby == 'date' ) echo "selected='selected'"; ?> >Date</option>
                                        <option value='category' <?php if ( $vsrp_select_orderby == 'category' ) echo "selected='selected'"; ?> >Category</option>
                                        <option value='modified' <?php if ( $vsrp_select_orderby == 'modified' ) echo "selected='selected'"; ?> >Modified</option>
                                    </select>
                                    <br /><?php _e( 'Please select which way you want to order the posts in widget.', 'vertical-scroll-recent-post' ); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Select order', 'vertical-scroll-recent-post' ); ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span><?php _e( 'Select order', 'vertical-scroll-recent-post' ); ?></span></legend>
                                <label for="vsrp_select_order">
                                    <select name="vsrp_select_order" id="vsrp_select_order">
                                        <option value='ASC' <?php if ( $vsrp_select_order == 'ASC' ) echo "selected='selected'"; ?> >ASC</option>
                                        <option value='DESC' <?php if ( $vsrp_select_order == 'DESC' ) echo "selected='selected'"; ?> >DESC</option>
                                    </select>
                                    <br /><?php _e( 'Please select the order you want your post\'s to be displayed.', 'vertical-scroll-recent-post' ); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="table ui-tabs-hide" id="vsrp-tab-display">
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th><?php _e( 'Categories to be displayed', 'vertical-scroll-recent-post' ); ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span><?php _e( 'Categories to be displayed', 'vertical-scroll-recent-post' ); ?></span></legend>
                                <label for="vsrp_select_categories">
                                    <select name="vsrp_select_categories[]" multiple >
                                        <?php
                                            $vsrp_select_categories = explode( ',', $vsrp_select_categories);
                                            $categories = get_terms( 'category', 'orderby=id&hide_empty=0' );
                                            foreach( $categories as $category ) {
                                                echo '<option value="' . $category->term_id . '" ';
                                                if ( $vsrp_exclude_categories )
                                                    $category->term_id = "-".$category->term_id;
                                                if ( in_array( $category->term_id, $vsrp_select_categories ) ) echo "selected=\"selected\" ";
                                                echo '>' . $category->name . '</option>';
                                            }
                                        ?>
                                    </select>
                                    <br /><?php _e( 'Please select the categories you want to be displayed.', 'vertical-scroll-recent-post' ); ?>
                                    <br /><span class="description"><?php _e( 'You can choose multiple categories by holding the CTRL button and left mouse click', 'vertical-scroll-recent-post' ); ?> </span>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Exclude categories', 'vertical-scroll-recent-post' ); ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span><?php _e( 'Exclude categories', 'vertical-scroll-recent-post' ); ?></span></legend>
                                <label for="vsrp_exclude_categories">
                                    <select name="vsrp_exclude_categories" id="vsrp_exclude_categories">
                                        <option value='1' <?php if ( $vsrp_exclude_categories == 1 ) echo "selected='selected'"; ?> >Yes</option>
                                        <option value='0' <?php if ( $vsrp_exclude_categories == 0 ) echo "selected='selected'"; ?> >No</option>
                                    </select>
                                    <br /><?php _e( 'Please select this option if you want to exclude the above categories.', 'vertical-scroll-recent-post' ); ?>
                                    
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Display date of post', 'vertical-scroll-recent-post' ); ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span><?php _e( 'Display date of post', 'vertical-scroll-recent-post' ); ?></span></legend>
                                <label for="vsrp_show_date">
                                    <select name="vsrp_show_date" id="vsrp_show_date">
                                        <option value='1' <?php if ( $vsrp_show_date == 1 ) echo "selected='selected'"; ?> >Yes</option>
                                        <option value='0' <?php if ( $vsrp_show_date == 0 ) echo "selected='selected'"; ?> >No</option>
                                    </select>
                                    <br /><?php _e( 'Please select if you want to display date of post.', 'vertical-scroll-recent-post' ); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Date Format', 'vertical-scroll-recent-post' ); ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span><?php _e('Date Format', 'vertical-scroll-recent-post' ); ?></span></legend>
                                <?php
                                    $date_formats = array_unique( apply_filters( 'date_formats', array( 'Y-m-d', 'm/d/Y', 'd/m/Y', get_option( 'date_format' ) ) ) );

                                    foreach ( $date_formats as $format ) {
                                        echo "<label title='" . esc_attr( $format ) . "'><input type='radio' name='vsrp_date_format' value='" . esc_attr( $format ) . "'";
                                        if ( $vsrp_date_format === $format ) {
                                            echo " checked='checked'";
                                        }
                                        echo ' /> <span>' . date_i18n( $format ) . "</span></label><br />\n";
                                    }
                                ?>
                                <span class="description"><?php _e( 'Last one contains the WordPress date format', 'vertical-scroll-recent-post' ); ?> </span>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Display post\'s thumbnail', 'vertical-scroll-recent-post' ); ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span><?php _e( 'Display post\'s thumbnail', 'vertical-scroll-recent-post' ); ?></span></legend>
                                <label for="vrsp_show_thumb">
                                    <select name="vrsp_show_thumb" id="vrsp_show_thumb">
                                        <option value='1' <?php if ( $vrsp_show_thumb == 1 ) echo "selected='selected'"; ?> >Yes</option>
                                        <option value='0' <?php if ( $vrsp_show_thumb == 0 ) echo "selected='selected'"; ?> >No</option>
                                    </select>
                                    <br /><?php _e( 'Please select if you want to display post\'s thumbnail.', 'vertical-scroll-recent-post' ); ?>
                                    <br /><span class="description"><?php _e( 'You may add a CSS rule to class vsrp_thumb to style the thumbnails', 'vertical-scroll-recent-post' ); ?> </span>
                                    <br /><span class="description"><?php _e( 'by default the size is same as post title\'s height', 'vertical-scroll-recent-post' ); ?> </span>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Display link to category', 'vertical-scroll-recent-post' ); ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span><?php _e( 'Display link to category', 'vertical-scroll-recent-post' ); ?></span></legend>
                                <label for="vsrp_show_category_link">
                                    <select name="vsrp_show_category_link" id="vsrp_show_category_link">
                                        <option value='1' <?php if ( $vsrp_show_category_link == 1 ) echo "selected='selected'"; ?> >Yes</option>
                                        <option value='0' <?php if ( $vsrp_show_category_link == 0 ) echo "selected='selected'"; ?> >No</option>
                                    </select>
                                    <br /><?php _e( 'Please select if you want to display link to category posts.', 'vertical-scroll-recent-post' ); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="table ui-tabs-hide" id="vsrp-tab-scrolling">
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th><?php _e( 'Posts shown simultaneously', 'vertical-scroll-recent-post' ); ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span><?php _e( 'Posts shown simultaneously', 'vertical-scroll-recent-post' ); ?></span></legend>
                                <label for="vsrp_dis_num_user">
                                    <input name="vsrp_dis_num_user" type="number" value="<?php echo $vsrp_dis_num_user; ?>" id="vsrp_dis_num_user" />
                                    <br /><?php _e( 'Please enter how many post titles you want to be <br />
                                        displayed simultaneously in the widget.', 'vertical-scroll-recent-post' ); ?> (<?php echo $txt_example; ?>: 5)
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Totall posts displayed in scroll', 'vertical-scroll-recent-post' ); ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span><?php _e( 'Totall posts displayed in scroll', 'vertical-scroll-recent-post' ); ?></span></legend>
                                <label for="vsrp_select_num_user">
                                    <input name="vsrp_select_num_user" type="number" value="<?php echo $vsrp_select_num_user; ?>" id="vsrp_select_num_user" />
                                    <br /><?php _e( 'Please enter how many post titles you want to be <br />
                                        shown in widget at totall.', 'vertical-scroll-recent-post' ); ?> (<?php echo $txt_example; ?>: 10)
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Scrolling Speed', 'vertical-scroll-recent-post' ); ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span><?php _e( 'Scrolling Speed', 'vertical-scroll-recent-post' ); ?></span></legend>
                                <label for="vsrp_speed">
                                    <?php _e( 'Slow', 'vertical-scroll-recent-post' ); ?> 
                                        <input name="vsrp_speed" type="range" value="<?php echo $vsrp_speed; ?>"  id="vsrp_speed" min="1" max="5" /> 
                                    <?php _e( 'Fast', 'vertical-scroll-recent-post' ); ?> 
                                    <br /><?php _e( 'Select how fast you want the widget to scroll.', 'vertical-scroll-recent-post' ); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Seconds to scroll', 'vertical-scroll-recent-post' ); ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span><?php _e( 'Select order', 'vertical-scroll-recent-post' ); ?></span></legend>
                                <label for="vsrp_seconds">
                                    <input name="vsrp_seconds" type="number" value="<?php echo $vsrp_seconds; ?>" id="vsrp_seconds" />
                                    <br /><?php _e( 'Every how many seconds you want the widget to scroll', 'vertical-scroll-recent-post' ); ?> (<?php echo $txt_example; ?>: 5)
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            
            <input name="vsrp_submit" id="vsrp_submit" class="button-primary" value="<?php _e( 'Submit', 'vertical-scroll-recent-post' ); ?>" type="submit" />
            <a class="button" target="_blank" href="http://www.gopiplus.com/work/2010/07/18/vertical-scroll-recent-post/"><?php _e( 'Help', 'vertical-scroll-recent-post' ); ?></a>
            <input type="hidden" name="vsrp_form_submit" value="yes" />
            <?php wp_nonce_field( 'vsrp_form_setting' ); ?>
        </form>
        <h3><?php _e( 'Plugin configuration option', 'vertical-scroll-recent-post' ); ?></h3>
        <ol>
            <li><?php _e( 'Add directly in to the theme using the following PHP code:', 'vertical-scroll-recent-post' ); ?>
                <br /><code>&lt;?php if ( function_exists( 'vsrp' ) ) vsrp(); ?&gt;</code>
            </li>
            <li><?php _e( 'Drag and drop the widget to the desired sidebar.', 'vertical-scroll-recent-post' ); ?></li>
        </ol>
        <p class="description">
            <?php _e( 'Check official website for more information', 'vertical-scroll-recent-post' ); ?> 
            <a target="_blank" href="http://www.gopiplus.com/work/2010/07/18/vertical-scroll-recent-post/">
                <?php _e( 'click here', 'vertical-scroll-recent-post' ); ?>
            </a>
        </p>
        <script type="text/javascript">
            vsrp_options();
        </script>
    </div>
    <?php
}

function vsrp_add_to_menu() {
    add_options_page( __( 'Vertical Scroll Recent Post', 'vertical-scroll-recent-post' ), 
            __( 'Vertical Scroll Recent Post', 'vertical-scroll-recent-post' ), 'manage_options', 'vertical-scroll-recent-post', 'vsrp_admin_options' );
}

if ( is_admin() ) {
    add_action( 'admin_menu', 'vsrp_add_to_menu' );
}

function vsrp_init() {
    if ( function_exists( 'wp_register_sidebar_widget' ) ) {
        wp_register_sidebar_widget( 'vertical-scroll-recent-post', __( 'Vertical Scroll Recent Post', 'vertical-scroll-recent-post' ), 'vsrp_widget' );
    }
    
    if ( function_exists( 'wp_register_widget_control' ) ) {
        wp_register_widget_control( 'vertical-scroll-recent-post', array( __( 'Vertical Scroll Recent Post', 'vertical-scroll-recent-post' ), 'widgets' ), 'vsrp_control' );
    } 
}

function vsrp_add_javascript_files() {
    wp_enqueue_script( 'vertical-scroll-recent-post', plugins_url().'/vertical-scroll-recent-post/vertical-scroll-recent-post.js' );
    wp_enqueue_style(  'vertical-scroll-recent-post', plugins_url().'/vertical-scroll-recent-post/vertical-scroll-recent-post.css');
}

function vsrp_deactivation() {
    delete_option( 'vsrp_title' );
    delete_option( 'vsrp_dis_num_height' );
    delete_option( 'vsrp_title_length' );
    delete_option( 'vsrp_dis_num_user' );
    delete_option( 'vsrp_select_num_user' );
    delete_option( 'vsrp_select_categories' );
    delete_option( 'vsrp_exclude_categories' );
    delete_option( 'vsrp_select_orderby' );
    delete_option( 'vsrp_select_order' );
    delete_option( 'vsrp_show_date' );
    delete_option( 'vsrp_date_format' );
    delete_option( 'vsrp_show_category_link' );
    delete_option( 'vrsp_show_thumb' );
    delete_option( 'vsrp_speed' );
    delete_option( 'vsrp_seconds' );
    delete_option( 'vsrp_timeout' );
}

function vsrp_textdomain() {
    load_plugin_textdomain( 'vertical-scroll-recent-post', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action( 'plugins_loaded', 'vsrp_textdomain' );
add_action( 'plugins_loaded', 'vsrp_init' );

add_action( 'wp_enqueue_scripts', 'vsrp_add_javascript_files' );
add_action( 'widgets_init', 'vsrp_add_javascript_files');
register_activation_hook( __FILE__, 'vsrp_install' );
register_deactivation_hook( __FILE__, 'vsrp_deactivation' );
?>