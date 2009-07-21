<?php

/*
Plugin Name: Vertical scroll recent post
Plugin URI: http://gopi.coolpage.biz/demo/2009/07/18/vertical-scroll-recent-post/
Description: This will vertically scroll the recent post title in site side bar.
Author: Gopi.R
Author URI: http://gopi.coolpage.biz/demo/2009/07/18/vertical-scroll-recent-post/
Version: 1.0
Tags: Vertical, scroll, recent, post, title, widget, posts.
vsrp means Vertical scroll recent post
*/

function vsrp() 
{
	
	global $wpdb;
	
	$num_user = get_option('vsrp_select_num_user');
	$dis_num_user = get_option('vsrp_dis_num_user');

	$dis_num_height = get_option('vsrp_dis_num_height');

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

	$vsrp_data = $wpdb->get_results("SELECT ID,post_title,post_date FROM ". $wpdb->prefix . "posts WHERE 1 and post_type='post' and post_status = 'publish' order by ID desc limit 0, $num_user");

	if ( ! empty($vsrp_data) ) 
	{
		$vsrp_count = 0;
		foreach ( $vsrp_data as $vsrp_data ) 
		{
			$vsrp_post_title = $vsrp_data->post_title;
			
			$get_permalink = get_permalink($vsrp_data->ID);
			
			$vsrp_post_title = substr($vsrp_post_title, 0, 50);

			$dis_height = $dis_num_height."px";
			$vsrp_html = $vsrp_html . "<div class='vsrp_div' style='height:$dis_height;padding:2px 0px 2px 0px;'>"; 
			$vsrp_html = $vsrp_html . "&bull; <a href='$get_permalink'>$vsrp_post_title...</a>";
			$vsrp_html = $vsrp_html . "</div>";
			
			$vsrp_post_title = mysql_real_escape_string(trim($vsrp_post_title));
			$get_permalink = mysql_real_escape_string($get_permalink);
			$vsrp_x = $vsrp_x . "vsrp_array[$vsrp_count] = '<div class=\'vsrp_div\' style=\'height:$dis_height;padding:2px 0px 2px 0px;\'>&bull; <a href=\'$get_permalink\'>$vsrp_post_title...</a></div>'; ";	
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
			<div style="text-align:left;vertical-align:middle;text-decoration: none;overflow: hidden; position: relative; margin-left: 1px; height: <?php echo $$vsrp_height1; ?>;" id="vsrp_Holder">
				<?php echo $vsrp_html; ?>
			</div>
		</div>
		<script type="text/javascript" src="<?php echo get_option('siteurl'); ?>/wp-content/plugins/vertical-scroll-recent-post/vertical-scroll-recent-post.js"></script>
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
}

function vsrp_install() 
{
	add_option('vsrp_title', "Recent Post");
	add_option('vsrp_select_num_user', "10");
	add_option('vsrp_dis_num_user', "5");
	add_option('vsrp_dis_num_height', "30");
}

function vsrp_control() 
{
	$vsrp_title = get_option('vsrp_title');
	$vsrp_select_num_user = get_option('vsrp_select_num_user');
	$vsrp_dis_num_user = get_option('vsrp_dis_num_user');
	$vsrp_dis_num_height = get_option('vsrp_dis_num_height');
	
	if ($_POST['vsrp_submit']) 
	{
		$vsrp_title = stripslashes($_POST['vsrp_title']);
		$vsrp_select_num_user = stripslashes($_POST['vsrp_select_num_user']);
		$vsrp_dis_num_user = stripslashes($_POST['vsrp_dis_num_user']);
		$vsrp_dis_num_height = stripslashes($_POST['vsrp_dis_num_height']);
		
		update_option('vsrp_title', $vsrp_title );
		update_option('vsrp_select_num_user', $vsrp_select_num_user );
		update_option('vsrp_dis_num_user', $vsrp_dis_num_user );
		update_option('vsrp_dis_num_height', $vsrp_dis_num_height );
	}
	
	echo '<p>Title:<br><input  style="width: 200px;" type="text" value="';
	echo $vsrp_title . '" name="vsrp_title" id="vsrp_title" /></p>';
	
	echo '<p>Each title height in scroll:<br><input  style="width: 100px;" type="text" value="';
	echo $vsrp_dis_num_height . '" name="vsrp_dis_num_height" id="vsrp_dis_num_height" />(default: 30)if any overlap in the title at front end you should arrange this height</p>';
	
	echo '<p>Display number of post at the same time in scroll:<br><input  style="width: 100px;" type="text" value="';
	echo $vsrp_dis_num_user . '" name="vsrp_dis_num_user" id="vsrp_dis_num_user" /></p>';
	
	echo '<p>Enter max number of post to scroll:<br><input  style="width: 100px;" type="text" value="';
	echo $vsrp_select_num_user . '" name="vsrp_select_num_user" id="vsrp_select_num_user" /></p>';
	
	echo '<input type="hidden" id="vsrp_submit" name="vsrp_submit" value="1" />';
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

function vsrp_init()
{
	if(function_exists('register_sidebar_widget')) 
	{
		register_sidebar_widget('Vertical scroll recent post', 'vsrp_widget');
	}
	
	if(function_exists('register_widget_control')) 
	{
		register_widget_control(array('Vertical scroll recent post', 'widgets'), 'vsrp_control');
	} 
}

function vsrp_deactivation() 
{
	delete_option('vsrp_title');
	delete_option('vsrp_dis_num_user');
	delete_option('vsrp_select_num_user');
	delete_option('vsrp_dis_num_height');
}

add_action("plugins_loaded", "vsrp_init");
register_activation_hook(__FILE__, 'vsrp_install');
register_deactivation_hook(__FILE__, 'vsrp_deactivation');

?>