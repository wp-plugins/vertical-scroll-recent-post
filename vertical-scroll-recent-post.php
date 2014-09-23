<?php
/*
Plugin Name: Vertical scroll recent post
Plugin URI: http://www.gopiplus.com/work/2010/07/18/vertical-scroll-recent-post/
Description: Vertical scroll recent post plugin scroll the recent post title in the widget, the post scroll from bottom to top vertically.
Author: Gopi Ramasamy
Author URI: http://www.gopiplus.com/work/2010/07/18/vertical-scroll-recent-post/
Version: 11.5
Tags: Vertical, scroll, recent, post, title, widget
vsrp means Vertical scroll recent post
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

function vsrp() {
    global $wpdb;

    $dis_num_height = get_option( 'vsrp_dis_num_height' );
    $dis_num_user = get_option( 'vsrp_dis_num_user' );
    $num_user = get_option( 'vsrp_select_num_user' );
    $vsrp_select_categories = get_option( 'vsrp_select_categories' );
    $vsrp_select_orderby = get_option( 'vsrp_select_orderby' );
    $vsrp_select_order = get_option( 'vsrp_select_order' );
    $vsrp_show_date = get_option( 'vsrp_show_date' );
    $vsrp_show_category_link = get_option( 'vsrp_show_category_link' );
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
            $get_permalink = get_permalink($vsrp_data->ID);
            $vsrp_post_title = substr($vsrp_post_title, 0, 130);
            $vsrp_post_date = date( 'd/m/Y', strtotime( $vsrp_data->post_date ) );

            $dis_height = $dis_num_height."px";
            $vsrp_html = $vsrp_html . "<div class='vsrp_div' style='height:$dis_height;'>" . "<a href='$get_permalink'>$vsrp_post_title</a>";
            if ( $vsrp_show_date ) {
                $vsrp_html = $vsrp_html . " -- <span class='vrsp_date'>$vsrp_post_date</span>";
            }
            $vsrp_html = $vsrp_html . "</div>";
            
            $vsrp_post_title = esc_sql( trim( $vsrp_post_title ) );
            $get_permalink = esc_sql( $get_permalink );
            $vsrp_x = $vsrp_x . "vsrp_array[ $vsrp_count ] = '<div class=\'vsrp_div\' style=\'height:$dis_height;\'><a href=\'$get_permalink\'>$vsrp_post_title</a>";
            if ( $vsrp_show_date ) {
                $vsrp_x = $vsrp_x . " -- <span class=\'vrsp_date\'>$vsrp_post_date</span>";
            }
            $vsrp_x = $vsrp_x . "</div>'; ";
            $vsrp_count++;
        }
        $dis_num_height = $dis_num_height + 4;
        if( $vsrp_count >= $dis_num_user ) {
            $vsrp_count = $dis_num_user;
            $vsrp_height = ( $dis_num_height * $dis_num_user );
        } else {
            $vsrp_count = $vsrp_count;
            $vsrp_height = ( $vsrp_count*$dis_num_height );
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
    add_option( 'vsrp_dis_num_user', 5 );
    add_option( 'vsrp_select_num_user', 10 );
    add_option( 'vsrp_select_categories', "" );
    add_option( 'vsrp_select_orderby', "date" );
    add_option( 'vsrp_select_order', "DESC" );
    add_option( 'vsrp_show_date', 0 );
    add_option( 'vsrp_show_category_link', 0 );
    add_option( 'vsrp_speed', 2 );
    add_option( 'vsrp_seconds', 2 );
}

function vsrp_control() {
    echo '<p><b>';
    _e( 'Vertical scroll recent post', 'vertical-scroll-recent-post' );
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
    ?>
    <div class="wrap">
        <div class="form-wrap">
            <div id="icon-edit" class="icon32"></div>
            <h2><?php _e( 'Vertical scroll recent post', 'vertical-scroll-recent-post' ); ?></h2>
            <?php   
            $vsrp_title = get_option( 'vsrp_title' );
            $vsrp_dis_num_height = get_option( 'vsrp_dis_num_height' );
            $vsrp_dis_num_user = get_option( 'vsrp_dis_num_user' );
            $vsrp_select_num_user = get_option( 'vsrp_select_num_user' );
            $vsrp_select_categories = get_option( 'vsrp_select_categories' );
            $vsrp_select_orderby = get_option( 'vsrp_select_orderby' );
            $vsrp_select_order = get_option( 'vsrp_select_order' );
            $vsrp_show_date = get_option( 'vsrp_show_date' );
            $vsrp_show_category_link = get_option( 'vsrp_show_category_link' );
            $vsrp_speed = get_option( 'vsrp_speed' );
            $vsrp_seconds = get_option( 'vsrp_seconds' );
            if ( isset( $_POST[ 'vsrp_form_submit' ] ) && $_POST[ 'vsrp_form_submit' ] == 'yes' ) {
                check_admin_referer( 'vsrp_form_setting' );
                $vsrp_title = stripslashes( $_POST[ 'vsrp_title' ] );
                $vsrp_select_num_user = stripslashes( $_POST[ 'vsrp_select_num_user' ] );
                $vsrp_dis_num_user = stripslashes( $_POST[ 'vsrp_dis_num_user' ] );
                $vsrp_dis_num_height = stripslashes( $_POST[ 'vsrp_dis_num_height' ] );
                $vsrp_select_categories = stripslashes( $_POST[ 'vsrp_select_categories' ] );
                $vsrp_select_orderby = stripslashes( $_POST[ 'vsrp_select_orderby' ] );
                $vsrp_select_order = stripslashes( $_POST[ 'vsrp_select_order' ] );
                $vsrp_speed = stripslashes( $_POST[ 'vsrp_speed' ] );
                $vsrp_seconds = stripslashes( $_POST[ 'vsrp_seconds' ] );
                $vsrp_show_date = stripslashes( $_POST[ 'vsrp_show_date' ] );
                $vsrp_show_category_link = stripslashes( $_POST[ 'vsrp_show_category_link' ] );
                //TODO check if value is empty
                update_option( 'vsrp_title', $vsrp_title );
                update_option( 'vsrp_dis_num_height', $vsrp_dis_num_height );
                update_option( 'vsrp_dis_num_user', $vsrp_dis_num_user );
                update_option( 'vsrp_select_num_user', $vsrp_select_num_user );
                update_option( 'vsrp_select_categories', $vsrp_select_categories );
                update_option( 'vsrp_select_orderby', $vsrp_select_orderby );
                update_option( 'vsrp_select_order', $vsrp_select_order );
                update_option( 'vsrp_show_date', $vsrp_show_date );
                update_option( 'vsrp_show_category_link', $vsrp_show_category_link );
                update_option( 'vsrp_speed', $vsrp_speed );
                update_option( 'vsrp_seconds', $vsrp_seconds );
                ?>
                <div class="updated fade">
                    <p><strong><?php _e( 'Details successfully updated.', 'vertical-scroll-recent-post' ); ?></strong></p>
                </div>
                <?php
            }
            ?>
            <form name="vsrp_form" method="post" action="">
                <h3><?php _e( 'Widget setting', 'vertical-scroll-recent-post' ); ?></h3>
                
                <label for="vsrp_title"><?php _e( 'Widget title', 'vertical-scroll-recent-post' ); ?></label>
                <input name="vsrp_title" type="text" value="<?php echo $vsrp_title; ?>" id="vsrp_title" size="30" maxlength="150" />
                <p><?php _e( 'Please enter your widget\'s title.', 'vertical-scroll-recent-post' ); ?></p>
                
                <label for="vsrp_dis_num_height"><?php _e( 'Post title\'s height', 'vertical-scroll-recent-post' ); ?></label>
                <input name="vsrp_dis_num_height" type="text" value="<?php echo $vsrp_dis_num_height; ?>" id="vsrp_dis_num_height" size="3" maxlength="4" />
                <p><?php _e( 'Please enter desired height for each post\'s title in widget. <br /> If any overlap in widget at front end, 
                             you should change this value.', 'vertical-scroll-recent-post' ); ?> (<?php echo $txt_example; ?>: 35)</p>
                
                <label for="vsrp_dis_num_user"><?php _e( 'Posts displayed', 'vertical-scroll-recent-post' ); ?></label>
                <input name="vsrp_dis_num_user" type="text" value="<?php echo $vsrp_dis_num_user; ?>" id="vsrp_dis_num_user" size="3" maxlength="4" />
                <p><?php _e( 'Please enter how many post titles you want to be <br />
                             displayed simultaneously in the widget.', 'vertical-scroll-recent-post' ); ?> (<?php echo $txt_example; ?>: 5)</p>
                
                <label for="vsrp_select_num_user"><?php _e( 'Posts at totall', 'vertical-scroll-recent-post' ); ?></label>
                <input name="vsrp_select_num_user" type="text" value="<?php echo $vsrp_select_num_user; ?>" id="vsrp_select_num_user" size="3" maxlength="4" />
                <p><?php _e( 'Please enter how many post titles you want to be <br />
                             shown in widget at totall.', 'vertical-scroll-recent-post' ); ?> (<?php echo $txt_example; ?>: 10)</p>
                
                <label for="vsrp_select_categories"><?php _e( 'Categories to be displayed', 'vertical-scroll-recent-post' ); ?></label>
                <input name="vsrp_select_categories" type="text" value="<?php echo $vsrp_select_categories; ?>" id="vsrp_select_categories" size="20" maxlength="150">
                <p><?php _e( 'If you want to display posts from specific categories <br />
                             Please enter category IDs, separated by commas.', 'vertical-scroll-recent-post' ); ?></p>
                
                <label for="vsrp_select_orderby"><?php _e( 'Select orderby field', 'vertical-scroll-recent-post' ); ?></label>
                <select name="vsrp_select_orderby" id="vsrp_select_orderby">
                    <option value='ID' <?php if ( $vsrp_select_orderby == 'ID' ) echo "selected='selected'";?> >ID</option>
                    <option value='author' <?php if ( $vsrp_select_orderby == 'author' ) echo "selected='selected'"; ?> >Author</option>
                    <option value='title' <?php if ( $vsrp_select_orderby == 'title' ) echo "selected='selected'"; ?> >Title</option>
                    <option value='rand' <?php if ( $vsrp_select_orderby == 'rand' ) echo "selected='selected'"; ?> >Random order</option>
                    <option value='date' <?php if ( $vsrp_select_orderby == 'date' ) echo "selected='selected'"; ?> >Date</option>
                    <option value='category' <?php if ( $vsrp_select_orderby == 'category' ) echo "selected='selected'"; ?> >Category</option>
                    <option value='modified' <?php if ( $vsrp_select_orderby == 'modified' ) echo "selected='selected'"; ?> >Modified</option>
                </select>
                <p><?php _e( 'Please select which way you want to order the posts in widget.', 'vertical-scroll-recent-post' ); ?></p>
                
                <label for="vsrp_select_order"><?php _e( 'Select order', 'vertical-scroll-recent-post' ); ?></label>
                <select name="vsrp_select_order" id="vsrp_select_order">
                    <option value='ASC' <?php if ( $vsrp_select_order == 'ASC' ) echo "selected='selected'"; ?> >ASC</option>
                    <option value='DESC' <?php if ( $vsrp_select_order == 'DESC' ) echo "selected='selected'"; ?> >DESC</option>
                </select>
                <p><?php _e( 'Please select your order.', 'vertical-scroll-recent-post' ); ?></p>
                
                <label for="vsrp_show_date"><?php _e( 'Display date of post', 'vertical-scroll-recent-post' ); ?></label>
                <select name="vsrp_show_date" id="vsrp_show_date">
                    <option value='1' <?php if ( $vsrp_show_date == 1 ) echo "selected='selected'"; ?> >Yes</option>
                    <option value='0' <?php if ( $vsrp_show_date == 0 ) echo "selected='selected'"; ?> >No</option>
                </select>
                <p><?php _e( 'Please select if you want to display date of post.', 'vertical-scroll-recent-post' ); ?></p>

                <label for="vsrp_show_category_link"><?php _e( 'Display link to category', 'vertical-scroll-recent-post' ); ?></label>
                <select name="vsrp_show_category_link" id="vsrp_show_category_link">
                    <option value='1' <?php if ( $vsrp_show_category_link == 1 ) echo "selected='selected'"; ?> >Yes</option>
                    <option value='0' <?php if ( $vsrp_show_category_link == 0 ) echo "selected='selected'"; ?> >No</option>
                </select>
                <p><?php _e( 'Please select if you want to display link to category posts.', 'vertical-scroll-recent-post' ); ?></p>

                <label for="vsrp_speed"><?php _e( 'Scrolling Speed', 'vertical-scroll-recent-post' ); ?></label>
                <?php _e( 'Slow', 'vertical-scroll-recent-post' ); ?> 
                    <input name="vsrp_speed" type="range" value="<?php echo $vsrp_speed; ?>"  id="vsrp_speed" min="1" max="5" /> 
                <?php _e( 'Fast', 'vertical-scroll-recent-post' ); ?> 
                <p><?php _e( 'Select how fast you want the widget to scroll.', 'vertical-scroll-recent-post' ); ?></p>
                
                <label for="vsrp_seconds"><?php _e( 'Seconds to scroll', 'vertical-scroll-recent-post' ); ?></label>
                <input name="vsrp_seconds" type="text" value="<?php echo $vsrp_seconds; ?>" id="vsrp_seconds" size="3" maxlength="4" />
                <p><?php _e( 'Every how many seconds you want the widget to scroll', 'vertical-scroll-recent-post' ); ?> (<?php echo $txt_example; ?>: 5)</p>
                
                <input name="vsrp_submit" id="vsrp_submit" class="button" value="<?php _e( 'Submit', 'vertical-scroll-recent-post' ); ?>" type="submit" />
                <a class="button" target="_blank" href="http://www.gopiplus.com/work/2010/07/18/vertical-scroll-recent-post/"><?php _e( 'Help', 'vertical-scroll-recent-post' ); ?></a>
                <input type="hidden" name="vsrp_form_submit" value="yes" />
                <?php wp_nonce_field( 'vsrp_form_setting' ); ?>
            </form>
        </div>
        <h3><?php _e( 'Plugin configuration option', 'vertical-scroll-recent-post' ); ?></h3>
        <ol>
            <li><?php _e( 'Add directly in to the theme using PHP code.', 'vertical-scroll-recent-post' ); ?></li>
            <li><?php _e( 'Drag and drop the widget to your sidebar.', 'vertical-scroll-recent-post' ); ?></li>
        </ol>
        <p class="description">
            <?php _e( 'Check official website for more information', 'vertical-scroll-recent-post' ); ?> 
            <a target="_blank" href="http://www.gopiplus.com/work/2010/07/18/vertical-scroll-recent-post/">
                <?php _e( 'click here', 'vertical-scroll-recent-post' ); ?>
            </a>
        </p>
    </div>
    <?php
}

function vsrp_add_to_menu() {
    add_options_page( __( 'Vertical scroll recent post', 'vertical-scroll-recent-post' ), 
            __( 'Vertical scroll recent post', 'vertical-scroll-recent-post' ), 'manage_options', 'vertical-scroll-recent-post', 'vsrp_admin_options' );
}

if (is_admin()) {
    add_action( 'admin_menu', 'vsrp_add_to_menu' );
}

function vsrp_init() {
    if ( function_exists( 'wp_register_sidebar_widget' ) ) {
        wp_register_sidebar_widget( 'vertical-scroll-recent-post', __( 'Vertical scroll recent post', 'vertical-scroll-recent-post' ), 'vsrp_widget' );
    }
    
    if ( function_exists( 'wp_register_widget_control' ) ) {
        wp_register_widget_control( 'vertical-scroll-recent-post', array( __( 'Vertical scroll recent post', 'vertical-scroll-recent-post' ), 'widgets' ), 'vsrp_control' );
    } 
}

function vsrp_add_javascript_files() {
    wp_enqueue_script( 'vertical-scroll-recent-post', plugins_url().'/vertical-scroll-recent-post/vertical-scroll-recent-post.js' );
    wp_enqueue_style(  'vertical-scroll-recent-post', plugins_url().'/vertical-scroll-recent-post/vertical-scroll-recent-post.css');
}

function vsrp_deactivation() {
    delete_option( 'vsrp_title' );
    delete_option( 'vsrp_dis_num_height' );
    delete_option( 'vsrp_dis_num_user' );
    delete_option( 'vsrp_select_num_user' );
    delete_option( 'vsrp_select_categories' );
    delete_option( 'vsrp_select_orderby' );
    delete_option( 'vsrp_select_order' );
    delete_option( 'vsrp_show_date' );
    delete_option( 'vsrp_show_category_link' );
    delete_option( 'vsrp_speed' );
    delete_option( 'vsrp_seconds' );
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