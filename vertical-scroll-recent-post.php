<?php

/*
Plugin Name: Vertical scroll recent post
Plugin URI: http://www.gopiplus.com/work/2010/07/18/vertical-scroll-recent-post/
Description: Vertical scroll recent post plugin scroll the recent post title in the widget, the post scroll from bottom to top vertically, check the live demo.
Author: Gopi.R
Author URI: http://www.gopiplus.com/work/2010/07/18/vertical-scroll-recent-post/
Version: 9.0
Tags: Vertical, scroll, recent, post, title, widget
vsrp means Vertical scroll recent post
*/

/**
 *     Vertical scroll recent post
 *     Copyright (C) 2011  www.gopiplus.com
 * 
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 * 
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 * 
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <http://www.gnu.org/licenses/>.
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

	//$vsrp_data = $wpdb->get_results("SELECT ID,post_title,post_date FROM ". $wpdb->prefix . "posts WHERE 1 and post_type='post' and post_status = 'publish' order by ID desc limit 0, $num_user");

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
    <h2>Vertical scroll recent post</h2>
    </div>
	<?php
	$vsrp_title = get_option('vsrp_title');
	$vsrp_select_num_user = get_option('vsrp_select_num_user');
	$vsrp_dis_num_user = get_option('vsrp_dis_num_user');
	$vsrp_dis_num_height = get_option('vsrp_dis_num_height');
	$vsrp_select_categories = get_option('vsrp_select_categories');
	$vsrp_select_orderby = get_option('vsrp_select_orderby');
	$vsrp_select_order = get_option('vsrp_select_order');
	
	if (@$_POST['vsrp_submit']) 
	{
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
	}
	
	?>
	<form name="vsrp_form" method="post" action="">
	<table width="100%" border="0" cellspacing="0" cellpadding="3"><tr><td width="80%" align="left">
	<?php
	echo '<p>Title:<br><input  style="width: 200px;" type="text" value="';
	echo $vsrp_title . '" name="vsrp_title" id="vsrp_title" /></p>';
	
	echo '<p>Each title height in scroll:<br><input  style="width: 100px;" type="text" value="';
	echo $vsrp_dis_num_height . '" name="vsrp_dis_num_height" id="vsrp_dis_num_height" /> (Only Number)<br>';
	echo 'If any overlap in the title at front end, you should arrange(increase/decrease) the above height</p>';
	
	echo '<p>Display number of post at the same time in scroll:<br><input  style="width: 100px;" type="text" value="';
	echo $vsrp_dis_num_user . '" name="vsrp_dis_num_user" id="vsrp_dis_num_user" /></p>';
	
	echo '<p>Enter max number of post to scroll:<br><input  style="width: 100px;" type="text" value="';
	echo $vsrp_select_num_user . '" name="vsrp_select_num_user" id="vsrp_select_num_user" /></p>';
	
	echo '<p>Enter Categories:<br><input  style="width: 200px;" type="text" value="';
	echo $vsrp_select_categories . '" name="vsrp_select_categories" id="vsrp_select_categories" /> (Example: 1, 3, 4)<br>';
	echo 'Category IDs, separated by commas.</p>';
	
	echo '<p>Enter Orderbys:<br><input  style="width: 200px;" type="text" value="';
	echo $vsrp_select_orderby . '" name="vsrp_select_orderby" id="vsrp_select_orderby" /> (Any 1 from list)<br>';
	echo 'ID/author/title/rand/date/category/modified</p>';

	echo '<p>Enter order:<br><input  style="width: 100px;" type="text" value="';
	echo $vsrp_select_order . '" name="vsrp_select_order" id="vsrp_select_order" />';
	echo ' ASC/DESC </p>';


	echo '<input name="vsrp_submit" id="vsrp_submit" lang="publish" class="button-primary" value="Update Setting" type="Submit" />';
	
	?>
	</td>
	<td width="20%" align="left" valign="middle" style="">

	</td></tr></table>
	</form>
	<h2>Plugin configuration</h2>
	Option 1.Go to widget menu and drag and drop the "Vertical scroll recent post" widget to your sidebar location. <br />
	Option 2. Copy and past the below mentioned code to your desired template location.

    <h2>Paste the below code to your desired template location!</h2>
    <div style="padding-top:7px;padding-bottom:7px;">
    <code style="padding:7px;">
    &lt;?php if (function_exists (vsrp)) vsrp(); ?&gt;
    </code></div>
    <h2>About Plugin</h2>
    Check official website for live demo and help <a target="_blank" href='http://www.gopiplus.com/work/2010/07/18/vertical-scroll-recent-post/'>click here</a><br /> 
    <?php
}
function vsrp_add_to_menu() 
{
	add_options_page('Vertical scroll recent post', 'Vertical scroll recent post', 'manage_options', __FILE__, 'vsrp_admin_options' );
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

add_action('init', 'vsrp_add_javascript_files');

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

add_action("plugins_loaded", "vsrp_init");
register_activation_hook(__FILE__, 'vsrp_install');
register_deactivation_hook(__FILE__, 'vsrp_deactivation');
?>