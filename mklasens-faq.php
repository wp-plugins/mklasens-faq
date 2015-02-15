<?php
defined( 'ABSPATH' ) or die( 'You can\'t access this file directly!');
/**
 * Plugin Name: mklasen's FAQ
 * Plugin URI: http://plugins.mklasen.com/faq/
 * Description: Add easy Frequently Asked Questions to your WordPress website. Answers are shown (slide-down) after a visitor clicks on a question.
 * Version: 1.0.1
 * Author: Marinus Klasen
 * Author URI: http://mklasen.com
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 
 
 Copyright 2015  Marinus Klasen  (email : marinus@mklasen.nl)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 
 */
 
	
	/* **************************
	#
	#  Register Post Type for FAQ
	#
	*************************** */ 
	
	function register_post_type_faq() {
	
		$labels = array(
			'name'               => _x( 'FAQ', 'post type general name', 'mklasens-faq-textdomain' ),
			'singular_name'      => _x( 'Question', 'post type singular name', 'mklasens-faq-textdomain' ),
			'menu_name'          => _x( 'FAQ', 'admin menu', 'mklasens-faq-textdomain' ),
			'name_admin_bar'     => _x( 'Question', 'add new on admin bar', 'mklasens-faq-textdomain' ),
			'add_new'            => _x( 'Add Question', 'item', 'mklasens-faq-textdomain' ),
			'add_new_item'       => __( 'Add New Question', 'mklasens-faq-textdomain' ),
			'new_item'           => __( 'New Question', 'mklasens-faq-textdomain' ),
			'edit_item'          => __( 'Edit Question', 'mklasens-faq-textdomain' ),
			'view_item'          => __( 'View Question', 'mklasens-faq-textdomain' ),
			'all_items'          => __( 'All Questions', 'mklasens-faq-textdomain' ),
			'search_items'       => __( 'Search Questions', 'mklasens-faq-textdomain' ),
			'parent_item_colon'  => __( 'Parent Question:', 'mklasens-faq-textdomain' ),
			'not_found'          => __( 'No questions found.', 'mklasens-faq-textdomain' ),
			'not_found_in_trash' => __( 'No questions found in Trash.', 'mklasens-faq-textdomain' ), 
		);
	
		$args = array(
			'labels'             => $labels,
			'taxonomies' 		 => array('faq-category'),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'faq' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 6,
			'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments', 'revisions' ),
			'menu_icon'			 => 'dashicons-editor-ul' 
		);
	
		register_post_type( 'faq', $args );
		
		register_taxonomy(
			'faq-category',
			'faq',
			array(
				'label' => __( 'Categories' ),
				'rewrite' => array( 'slug' => 'category' ),
				'hierarchical' => true,
			)
		);
	
		
	}
	
	add_action('init', 'register_post_type_faq');
	
	
	/* **************************
	#
	#  Include Styles and Scripts for Front-End
	#
	*************************** */ 
		
	function mklasens_faq_enqueue() {
		wp_register_script('mklasens-faq-js', plugins_url('js/index.js', __FILE__), array('jquery'), '', true);
		wp_register_style('mklasens-faq-css', plugins_url('css/index.css', __FILE__), false);
		wp_enqueue_script('jquery');
		wp_enqueue_script('mklasens-faq-js');
		wp_enqueue_style('mklasens-faq-css');
	}
	
	add_action( 'wp_enqueue_scripts', 'mklasens_faq_enqueue' );
	
	
	/* **************************
	#
	#  Shortcode for Front-End
	#
	*************************** */ 
	
	function mklasens_faq( $atts ) {
      $atts = shortcode_atts( array(
 	      'category' => ''
      ), $atts );
      
      // Get items
		$args = array(
			'post_type' => 'faq',
			'orderby'   => 'menu_order',
			'posts_per_page' => 9999,
			'post_parent' => 0,
			'order' => 'ASC',
			'faq-category' => $atts['category']
		);
	
		$posts = get_posts($args);
		$output = '';
		$output .= '<div class="mklasens-faq">';
			
		foreach($posts as $post) {
			$children = get_children(array(
				'order' => 'ASC',
				'post_parent' => $post->ID,
				'orderby'   => 'menu_order',
			));
			$output .= '<div class="parent-posts" data-id="'.$post->ID.'">';
			if (!empty($post->post_title)) {
				$output .= '<div data-id="'.$post->ID.'" class="question"><div class="icon"></div>'.$post->post_title.'</div>';
				if (!empty($post->post_content)) {
					$output .= '<div data-id="'.$post->ID.'" class="answer">'.$post->post_content.'</div>';
				}
			}
			foreach ($children as $child) {
				if (!empty($child->post_title)) {
				$output .= '<div class="child-posts">';
					$output .= '<div data-id="'.$child->ID.'" class="question"><div class="icon"></div>'.$child->post_title.'</div>';
					if (!empty($child->post_content)) {
						$output .= '<div data-id="'.$child->ID.'" class="answer">'.$child->post_content.'</div>';
					}
				$output .= '</div>';
				}
			}
			$output .='</div>';
		}
		$output .= '</div>';
		return $output;
	}
	add_shortcode( 'faq', 'mklasens_faq' );


	
	/* **************************
	#
	#  Button for adding FAQ to Page/Post
	#
	*************************** */ 
	
		
	/**
	 * Adds a box to the main column on the Post and Page edit screens.
	 */
	function mklasens_faq_media_button_metabox() {
	
		$screens = array( 'post', 'page' );
	
		foreach ( $screens as $screen ) {
	
			add_meta_box(
				'mklasen_add_faq_content',
				__( 'Add FAQ', 'mklasens-faq-textdomain' ),
				'mklasens_faq_media_button_metabox_content',
				$screen
			);
		}
	}
	add_action( 'add_meta_boxes', 'mklasens_faq_media_button_metabox' );
	
	/**
	 * Prints the box content.
	 * 
	 * @param WP_Post $post The object for the current post/page.
	 */
	function mklasens_faq_media_button_metabox_content( $post ) {
		
		$cats = get_categories('taxonomy=faq-category');
		
		echo '<div class="mklasens_select_faq" id="mklasens_select_faq" style="display: none;">';
			echo '<h1>Which FAQ\'s would you like to show?</h1>';
			if (!$cats) {
				echo '<p>No FAQ categories yet. Show all faq\'s or <a href="edit-tags.php?taxonomy=faq-category&post_type=faq">make one</a></p>.';
			}
			echo '<select class="select_faq_category">';
				echo '<option value="">Show all FAQ\'s...</option>';
				if ($cats) {			
					foreach ($cats as $cat) {
						echo '<option value="'.$cat->name.'">'.$cat->name.'</option>';
					}
					
				}
			echo '</select>';
			submit_button('Add FAQ', '', 'mklasen_submit_add_faq');
		echo '</div>';
	}
	
	
	/**
	 * 
	 * Include admin js/css
	 * 
	 */
	
	function mklasens_faq_scripts() {
		wp_enqueue_script( 'mklasen-faq-admin-js', plugin_dir_url( __FILE__ ) . '/js/admin.js', 'jquery', '', true );
		wp_enqueue_style( 'mklasen-faq-admin-css', plugin_dir_url( __FILE__ ) . '/css/admin.css' );
	}
	
	add_action('admin_enqueue_scripts', 'mklasens_faq_scripts');
	
	
	/**
	 * 
	 * Add media button for FAQ
	 * 
	 */
		
	
	function mklasens_faq_media_button() {
		$screen = get_current_screen();
		if ($screen->parent_base != 'edit')
			return;
			
		        // do a version check for the new 3.5 UI
		        $version = get_bloginfo('version');
		        
		        if ($version < 3.5) {
		            // show button for v 3.4 and below
		            $image_btn = "notsetyet.png";
		            echo '<a href="#TB_inline?width=480&height=300&inlineId=mklasens_select_faq" class="thickbox" id="add_wpdb_faq" title="' . __("Add FAQ", 'mklasen') . '"><img src="'.$image_btn.'" alt="' . __("Add FAQ", 'mklasens-faq-textdomain') . '" /></a>';
		        } else {
		            // display button matching new UI
		            echo '<style>
		            		.mklasens_faq_media_icon  {
		            			display: inline-block;
								width: 18px;
								height: 18px;
								vertical-align: sub;
								margin: 0 2px;
							}
		                    span.mklasens_faq_media_icon:before {
			                    font: 400 18px/1 dashicons;
								speak: none;
								-webkit-font-smoothing: antialiased;
								-moz-osx-font-smoothing: grayscale;
								content: "\f203";
								color: #888;
		                    }
		                 </style>
		                  <a href="#TB_inline?width=480&height=300&inlineId=mklasens_select_faq" class="thickbox button mklasens_faq_link" id="add_mklasens_faq" title="' . __("Add FAQ", 'mklasens-faq-textdomain') . '"><span class="mklasens_faq_media_icon "></span> ' . __("Add FAQ", 'mklasens-faq-textdomain') . '</a>';
		        }

	}
	add_action('media_buttons', 'mklasens_faq_media_button', 15);
	
	/**
	 * 
	 * Manage Columns
	 * 
	 */
		
	
	add_filter( 'manage_edit-faq_columns', 'set_custom_edit_faq_columns' );
	
	add_action( 'manage_faq_posts_custom_column' , 'custom_faq_column', 10, 2 );
	
	function set_custom_edit_faq_columns($columns) {
	    unset( $columns['author'] );
	    unset( $columns['comments'] );
	    unset( $columns['date'] );
		$columns['faq-categories'] = __( 'Categories', 'your_text_domain' );
	
	    return $columns;
	}
	
	function custom_faq_column( $column, $post_id ) {
	    switch ( $column ) {

        case 'faq-categories' :
            $terms = get_the_term_list( $post_id , 'faq-category' , '' , ',' , '' );
            if ( is_string( $terms ) )
                echo $terms;
            else
                _e( '- No category -', 'your_text_domain' );
            break;
	
	    }
	}