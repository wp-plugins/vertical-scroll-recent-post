<?php

/*
Plugin Name: Vertical scroll recent post
Plugin URI: http://www.gopiplus.com/work/2010/07/18/vertical-scroll-recent-post/
Description: Vertical scroll recent post plugin scroll the recent post title in the widget, the post scroll from bottom to top vertically, check the live demo.
Author: Gopi.R
Author URI: http://www.gopiplus.com/work/2010/07/18/vertical-scroll-recent-post/
Version: 11.1
Tags: Vertical, scroll, recent, post, title, widget
vsrp means Vertical scroll recent post
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

function vsrp() 
{
	global $wpdb;
	$num_user = get_option('vsrp_select_num_user');
	$dis_num_user = get_option('vsrp_dis_num_user');
	$dis_num_height = get_option('vsrp_dis_num_height');
	$vsrp_select_categories = get_option('vsrp_select_categories');
	$vsrp_select_orderby = get_option('vsrp_select_orderby');
	$vsrp_select_order = get_option('vsrp_select_order');
	
	if(!is_numeric($num_user))
	{
		$num_user = 5;
	} 
	if(!is_numeric($dis_num_height))
	{
		$dis_num_height = 30;
	}
	if(!is_numeric($dis_num_user))
	{
		$dis_num_user = 5;
	}

	$sSql = query_posts('cat='.$vsrp_select_categories.'&orderby='.$vsrp_select_orderby.'&order='.$vsrp_select_order.'&showposts='.$num_user);

	$vsrp_data = $sSql;
	$vsrp_html = "";
	$vsrp_x = "";
	if ( ! empty($vsrp_data) ) 
	{
		$vsrp_count = 0;
		foreach ( $vsrp_data as $vsrp_data ) 
		{
			$vsrp_post_title = $vsrp_data->post_title;
			
			$get_permalink = get_permalink($vsrp_data->ID);
			
			$vsrp_post_title = substr($vsrp_post_title, 0, 130);

			$dis_height = $dis_num_height."px";
			$vsrp_html = $vsrp_html . "<div class='vsrp_div' style='height:$dis_height;padding:2px 0px 2px 0px;'>"; 
			$vsrp_html = $vsrp_html . "<a href='$get_permalink'>$vsrp_post_title</a>";
			$vsrp_html = $vsrp_html . "</div>";
			
			$vsrp_post_title = mysql_real_escape_string(trim($vsrp_post_title));
			$get_permalink = mysql_real_escape_string($get_permalink);
			$vsrp_x = $vsrp_x . "vsrp_array[$vsrp_count] = '<div class=\'vsrp_div\' style=\'height:$dis_height;padding:2px 0px 2px 0px;\'><a href=\'$get_permalink\'>$vsrp_post_title</a></div>'; ";	
			$vsrp_count++;
			
		}
		$dis_num_height = $dis_num_height + 4;
		if($vsrp_count >= $dis_num_user)
		{
			$vsrp_count = $dis_num_user;
			$vsrp_height = ($dis_num_height * $dis_num_user);
		}
		else
		{
			$vsrp_count = $vsrp_count;
			$vsrp_height = ($vsrp_count*$dis_num_height);
		}
		$vsrp_height1 = $dis_num_height."px";
		?>	
		<div style="padding-top:8px;padding-bottom:8px;">
			<div style="text-align:left;vertical-align:middle;text-decoration: none;overflow: hidden; position: relative; margin-left: 1px; height: <?php echo $vsrp_height1; ?>;" id="vsrp_Holder">
				<?php echo $vsrp_html; ?>
			</div>
		</div>
		<script type="text/javascript">
		var vsrp_array	= new Array();
		var vsrp_obj	= '';
		var vsrp_scrollPos 	= '';
		var vsrp_numScrolls	= '';
		var vsrp_heightOfElm = '<?php echo $dis_num_height; ?>'; // Height of each element (px)
		var vsrp_numberOfElm = '<?php echo $vsrp_count; ?>';
		var vsrp_scrollOn 	= 'true';
		function vsrp_createscroll() 
		{
			<?php echo $vsrp_x; ?>
			vsrp_obj	= document.getElementById('vsrp_Holder');
			vsrp_obj.style.height = (vsrp_numberOfElm * vsrp_heightOfElm) + 'px'; // Set height of DIV
			vsrp_content();
		}
		</script>
		<script type="text/javascript">
		vsrp_createscroll();
		</script>
		<?php
	}
	else
	{
		echo "<div style='padding-bottom:5px;padding-top:5px;'>No data available!</div>";
	}
	wp_reset_query();
}

function vsrp_install() 
{
	add_option('vsrp_title', "Recent Post");
	add_option('vsrp_select_num_user', "10");
	add_option('vsrp_dis_num_user', "5");
	add_option('vsrp_dis_num_height', "65");
	add_option('vsrp_select_categories', "");
	add_option('vsrp_select_orderby', "ID");
	add_option('vsrp_select_order', "DESC");
}

function vsrp_control() 
{
	echo 'Vertical scroll recent post. To change the setting goto Vertical scroll recent post link under setting tab.';
}

function vsrp_widget($args) 
{
	extract($args);
	echo $before_widget . $before_title;
	echo get_option('vsrp_title');
	echo $after_title;
	vsrp();
	echo $after_widget;
}

function vsrp_admin_options() 
{
	global $wpdb;
	?>
	<div class="wrap">
	  <div class="form-wrap">
		<div id="icon-edit" class="icon32"></div>
		<h2>Vertical scroll recent post</h2>
		<?php	
		$vsrp_title = get_option('vsrp_title');
		$vsrp_select_num_user = get_option('vsrp_select_num_user');
		$vsrp_dis_num_user = get_option('vsrp_dis_num_user');
		$vsrp_dis_num_height = get_option('vsrp_dis_num_height');
		$vsrp_select_categories = get_option('vsrp_select_categories');
		$vsrp_select_orderby = get_option('vsrp_select_orderby');
		$vsrp_select_order = get_option('vsrp_select_order');
		if (isset($_POST['vsrp_form_submit']) && $_POST['vsrp_form_submit'] == 'yes')
		{
			check_admin_referer('vsrp_form_setting');
			$vsrp_title = stripslashes($_POST['vsrp_title']);
			$vsrp_select_num_user = stripslashes($_POST['vsrp_select_num_user']);
			$vsrp_dis_num_user = stripslashes($_POST['vsrp_dis_num_user']);
			$vsrp_dis_num_height = stripslashes($_POST['vsrp_dis_num_height']);
			$vsrp_select_categories = stripslashes($_POST['vsrp_select_categories']);
			$vsrp_select_orderby = stripslashes($_POST['vsrp_select_orderby']);
			$vsrp_select_order = stripslashes($_POST['vsrp_select_order']);
			update_option('vsrp_title', $vsrp_title );
			update_option('vsrp_select_num_user', $vsrp_select_num_user );
			update_option('vsrp_dis_num_user', $vsrp_dis_num_user );
			update_option('vsrp_dis_num_height', $vsrp_dis_num_height );
			update_option('vsrp_select_categories', $vsrp_select_categories );
			update_option('vsrp_select_orderby', $vsrp_select_orderby );
			update_option('vsrp_select_order', $vsrp_select_order );
			?>
			<div class="updated fade">
				<p><strong>Details successfully updated.</strong></p>
			</div>
			<?php
		}
		?>
		<form name="vsrp_form" method="post" action="">
		    <h3>Widget setting</h3>
			
			<label for="tag-width">Widget title</label>
			<input name="vsrp_title" type="text" value="<?php echo $vsrp_title; ?>"  id="vsrp_title" size="50" maxlength="150">
			<p>Please enter your widget title.</p>
			
			<label for="tag-width">Height</label>
			<input name="vsrp_dis_num_height" type="text" value="<?php echo $vsrp_dis_num_height; ?>"  id="vsrp_dis_num_height" maxlength="4">
			<p>Please enter your height. If any overlap in the scroll at front end, <br />You should arrange this height (increase/decrease this height). (Example: 65)</p>
			
			<label for="tag-width">Display count</label>
			<input name="vsrp_dis_num_user" type="text" value="<?php echo $vsrp_dis_num_user; ?>"  id="vsrp_dis_num_user" maxlength="2">
			<p>Please enter your display count. Display number of post at the same time in scroll. (Example: 5)</p>
			
			<label for="tag-width">Scroll post count</label>
			<input name="vsrp_select_num_user" type="text" value="<?php echo $vsrp_select_num_user; ?>"  id="vsrp_select_num_user" maxlength="3">
			<p>Please enter your scroll post count. Enter max number of post to scroll. (Example: 10)</p>
			
			<label for="tag-width">Enter categories</label>
			<input name="vsrp_select_categories" type="text" value="<?php echo $vsrp_select_categories; ?>"  id="vsrp_select_categories" maxlength="150">
			<p>Please enter category IDs, separated by commas.</p>
			
			<label for="tag-width">Select orderbys</label>
			<select name="vsrp_select_orderby" id="vsrp_select_orderby">
				<option value='ID' <?php if($vsrp_select_orderby == 'ID') { echo "selected='selected'" ; } ?>>ID</option>
				<option value='author' <?php if($vsrp_select_orderby == 'author') { echo "selected='selected'" ; } ?>>Author</option>
				<option value='title' <?php if($vsrp_select_orderby == 'title') { echo "selected='selected'" ; } ?>>Title</option>
				<option value='rand' <?php if($vsrp_select_orderby == 'rand') { echo "selected='selected'" ; } ?>>Random order</option>
				<option value='date' <?php if($vsrp_select_orderby == 'date') { echo "selected='selected'" ; } ?>>Date</option>
				<option value='category' <?php if($vsrp_select_orderby == 'category') { echo "selected='selected'" ; } ?>>Category</option>
				<option value='modified' <?php if($vsrp_select_orderby == 'modified') { echo "selected='selected'" ; } ?>>Modified</option>
			</select>
			<p>Please select your orderby option.</p>
			
			<label for="tag-width">Select order</label>
			<select name="vsrp_select_order" id="vsrp_select_order">
				<option value='ASC' <?php if($vsrp_select_order == 'ASC') { echo "selected='selected'" ; } ?>>ASC</option>
				<option value='DESC' <?php if($vsrp_select_order == 'DESC') { echo "selected='selected'" ; } ?>>DESC</option>
			</select>
			<p>Please select your order.</p>
			
			<div style="height:10px;"></div>
			<input name="vsrp_submit" id="vsrp_submit" class="button" value="Submit" type="submit" />&nbsp;
			<a class="button" target="_blank" href="http://www.gopiplus.com/work/2010/07/18/vertical-scroll-recent-post/">Help</a>
			<input type="hidden" name="vsrp_form_submit" value="yes"/>
			<?php wp_nonce_field('vsrp_form_setting'); ?>
		</form>
		</div>
		<h3>Plugin configuration option</h3>
		<ol>
			<li>Add directly in to the theme using PHP code.</li>
			<li>Drag and drop the widget to your sidebar.</li>
		</ol>
	  <p class="description">Check official website for more information <a target="_blank" href="http://www.gopiplus.com/work/2010/07/18/vertical-scroll-recent-post/">click here</a></p>
	</div>
	<?php
}

function vsrp_add_to_menu() 
{
	add_options_page('Vertical scroll recent post', 'Vertical scroll recent post', 'manage_options', 'vertical-scroll-recent-post', 'vsrp_admin_options' );
}

if (is_admin()) 
{
	add_action('admin_menu', 'vsrp_add_to_menu');
}

function vsrp_init()
{
	if(function_exists('wp_register_sidebar_widget')) 
	{
		wp_register_sidebar_widget('vertical-scroll-recent-post','Vertical scroll recent post', 'vsrp_widget');
	}
	
	if(function_exists('wp_register_widget_control')) 
	{
		wp_register_widget_control('vertical-scroll-recent-post',array('Vertical scroll recent post', 'widgets'), 'vsrp_control');
	} 
}

function vsrp_add_javascript_files() 
{
	if (!is_admin())
	{
		wp_enqueue_script( 'vertical-scroll-recent-post', get_option('siteurl').'/wp-content/plugins/vertical-scroll-recent-post/vertical-scroll-recent-post.js');
	}	
}

function vsrp_deactivation() 
{
	delete_option('vsrp_title');
	delete_option('vsrp_dis_num_user');
	delete_option('vsrp_select_num_user');
	delete_option('vsrp_dis_num_height');
	delete_option('vsrp_select_categories');
	delete_option('vsrp_select_orderby');
	delete_option('vsrp_select_order');
}

add_action('init', 'vsrp_add_javascript_files');
add_action("plugins_loaded", "vsrp_init");
register_activation_hook(__FILE__, 'vsrp_install');
register_deactivation_hook(__FILE__, 'vsrp_deactivation');
?>