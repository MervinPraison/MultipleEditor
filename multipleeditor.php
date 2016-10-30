<?php

/*

Plugin Name: Miltiple Editor

Description: wysiwyg editor for sidebar info

Author:  Mervin Praison

Version: 0.1

Author URI: https://mer.vin/

*/



$sidebareditor = new sidebareditor();



class sidebareditor {



	function sidebareditor() {

		add_action('admin_menu', array(&$this, 'create_box'));

		add_action('admin_print_scripts', array(&$this, 'scripts'));

		add_action('admin_print_styles', array(&$this, 'styles'));

		add_action('save_post', array(&$this, 'sidebareditor_save_postdata'));

	}

	

	function create_box() {

	

		add_meta_box( 'side_info', 'Sidebar', array(&$this, 'sidebar_box'), 'page','normal','high');

		add_meta_box( 'side_info', 'Sidebar', array(&$this, 'sidebar_box'), 'post','normal','high');

	

	}



	function scripts() {

		$plugin_path = '/'.PLUGINDIR.'/'.plugin_basename(dirname(__FILE__));

		wp_enqueue_script('sidebareditor_init',$plugin_path . '/init.js', 'tiny_mce','',true);

	}

	function styles() {

		$plugin_path = '/'.PLUGINDIR.'/'.plugin_basename(dirname(__FILE__));

		echo '<link href="' .$plugin_path . '/style.css" type="text/css" rel="stylesheet" media="screen" />';

	}

	



	function sidebar_box() {



		global $post;



		wp_tiny_mce(false, array("editor_selector" => "theSidebar" ));



		echo '<div id="sidebar_box_wrapper" class=" hide-if-no-js" style="padding-bottom:20px;">';



		echo '<p style="overflow:hidden;margin:0;">

			<a href="media-upload.php?post_id='.$post->ID.'&type=image&TB_iframe=1&width=640&height=774" id="sidebareditor_add_image" class="thickbox alignleft">Insert Image</a>

			<a class="alignright" id="edSideButtonHTML" onclick="switchSidebarEditors.go(\'theSidebar\', \'html\');">HTML</a>

			<a class="active alignright" id="edSideButtonPreview"  onclick="switchSidebarEditors.go(\'theSidebar\', \'tinymce\');">Visual</a>

		</p>';



		echo '<textarea class="theSidebar" id="theSidebar" style="width:100%;height:200px" name="sidebar">'.

		get_post_meta($post->ID,'side_info', true)

		.'</textarea>';

		

		echo '<input type="hidden" name="sidebareditor_save" id="sidebareditor_save" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';



		echo '</div>';



	}// end function



	function sidebareditor_save_postdata( $post_id ) {



		if (isset($_POST['sidebareditor_save'])) {

			if ( !wp_verify_nonce( $_POST['sidebareditor_save'], plugin_basename(__FILE__) )) {

				return $post_id;

			}

		}



		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 

		return $post_id;



		// Check permissions

		if ( 'page' == $_POST['post_type'] ) {

			if ( !current_user_can( 'edit_page', $post_id ) ) return $post_id;

		} else {

			if ( !current_user_can( 'edit_post', $post_id ) ) return $post_id;

		}



		$content = wpautop($_POST['sidebar']);



		if (get_post_meta($post_id,'side_info')) {

		update_post_meta($post_id,'side_info',$content);

		} else {

		add_post_meta($post_id,'side_info',$content, true);

		}

		return $content;

	}



}//end class



?>
