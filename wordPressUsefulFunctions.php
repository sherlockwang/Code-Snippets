<?php 

/**
*	Enqueue SASS compiled CSS file
*	Enqueue owl carousel slider
*/
function child_theme_style() {
  wp_enqueue_style( 'child-theme-style', get_stylesheet_directory_uri() . '/css/style.css', '', '1.0');
  wp_enqueue_style( 'owl-slider-css', get_stylesheet_directory_uri() . '/js/owl.carousel.css', '', '1.0');
  wp_enqueue_script( 'owl-slider-js', get_stylesheet_directory_uri() . '/js/owl.carousel.min.js', array('jquery'), '1.0.0', false );
  wp_enqueue_script( 'child-custom-js', get_stylesheet_directory_uri() . '/js/custom.js', array('jquery'), '1.0.0', false );
}
// Set priority to 1000 for JointsWP child-theme
add_action('wp_enqueue_scripts', 'child_theme_style', 1000);

/*
* Remove Parent custom.js
*/
function customjs_dequeue_script() {
   wp_dequeue_script( 'customjs' );
}
add_action( 'wp_print_scripts', 'customjs_dequeue_script' );

/**
*	Sort Search Result by Custom Taxonomy (....?&orderby=taxonomy)
*	Not Work with custom Taxonomy filter
*/
function orderby_tax_clauses( $clauses, $wp_query ) {
    global $wpdb;
    $taxonomies = get_taxonomies();
    foreach ($taxonomies as $taxonomy) {
        if ( isset( $wp_query->query['orderby'] ) && $taxonomy == $wp_query->query['orderby'] ) {
            $clauses['join'] .=<<<SQL
LEFT OUTER JOIN {$wpdb->term_relationships} ON {$wpdb->posts}.ID={$wpdb->term_relationships}.object_id
LEFT OUTER JOIN {$wpdb->term_taxonomy} USING (term_taxonomy_id)
LEFT OUTER JOIN {$wpdb->terms} USING (term_id)
SQL;
            $clauses['where'] .= " AND (taxonomy = '{$taxonomy}' OR taxonomy IS NULL)";
            $clauses['groupby'] = "object_id";
            $clauses['orderby'] = "GROUP_CONCAT({$wpdb->terms}.name ORDER BY name ASC) ";
            $clauses['orderby'] .= ( 'ASC' == strtoupper( $wp_query->get('order') ) ) ? 'ASC' : 'DESC';
        }
    }
    return $clauses;
}
add_filter('posts_clauses', 'orderby_tax_clauses', 10, 2 );

/**
*	Build Custom Owl Slider by categories. 
*	Work with CPT (Create a custom post type with a Custom Taxonomy first, then use this function to generate slider)
*/
function get_feature_slider ($cat) {
	$slider_cat = $cat;
	// Set args to get custom post
	$args = array(
			'post_type' => 'feature_slides',
			'ignore_sticky_posts' => 1, 
			'posts_per_page' => -1,
			'orderby' => 'title',
			'order'   => 'ASC',
			'tax_query' => array(
					array(
						'taxonomy' => 'slide_cat',
						'field' => 'slug',
						'terms' => $slider_cat,
					),
				),
		);
	$slides = new WP_Query($args);
	// Use print to set html structure for slider
	print ('<div class="feature-slider">');
	while ($slides->have_posts()) : $slides->the_post();

		print ('<div class="feature-slide"><div class="slide-image">');
		the_post_thumbnail('full');
		print ('</div><div class="slide-caption"><div class="slide-tilte">');
		the_title();
		print ('</div><div class="slide-content">');
		the_content();
		print ('</div></div></div>');

	endwhile; wp_reset_query();
	print ('</div>');
}

/**
*	List Terms of Frame (A content type) content type
*/
function get_frame_terms ($post) {
	// Get taxonomy of a wordpress post
	$taxonomy = get_object_taxonomies($post);
	$terms = '';
	$args = array("fields" => "names");
	foreach ($taxonomy as $tax) {
		// Get terms by args
		$terms = wp_get_post_terms( $post->ID, $tax, $args );
		$string = '';
		foreach ($terms as $value) {
			$string .= ",$value";
		}
		$string = substr($string, 1);
		print '<div class="' . $tax . '">' . $string . '</div>';
	}
}

/**
*	Get Terms of a Taxonomy
*/
function get_single_tax_terms ($post, $tax) {
	$args = array("fields" => "names");
	// Get terms by args
	$terms = wp_get_post_terms( $post->ID, $tax, $args );
	// Get taxonomy name
	$tax_name = get_taxonomy($tax)->labels->name;
	$string = '';
	foreach ($terms as $value) {
		$string .= ",$value";
	}
	$string = substr($string, 1);
	print '<div class="' . $tax . '">' . $tax_name . ": " . $string . '</div>';
}

/**
*	Set meta field to orderby query
*/
function custom_orderby( $query ) {  
    if( $_GET['orderby'] == 'suggested_retail_price' ){
        set_query_var('orderby', 'meta_value_num');
        set_query_var('meta_key', $_GET['orderby']);
        set_query_var('order', $_GET['order']);
    } else {
    	set_query_var('orderby', $_GET['orderby']);
    	set_query_var('order', $_GET['order']);
    }
}
add_action( 'pre_get_posts', 'custom_orderby' ); 

/**
*	Build taxonomy terms to checkbox
*/
function build_checkbox ($taxonomy) {
	$tags = get_terms($taxonomy);
	$taxonomy_name = get_taxonomy($taxonomy)->labels->name;
	$checkboxes = '<div class="search-property ' . $taxonomy . '"><div class="search-checkbox-label">' . $taxonomy_name . '</div><div class="search-checkbox-container">' ;
	foreach($tags as $tag) :
		$checkboxes .='<input type="checkbox" name="' . $taxonomy . '[]" value="' . $tag->slug . '" id="tag-' . $tag->term_id . '" /><label for="tag-' . $tag->term_id . '">' . $tag->name . '</label><br>';
	endforeach;
	$checkboxes .= '</div></div>';
	print $checkboxes;
}

/**
* Add Category to HTML Class
*/
function add_slug_class_wp_list_categories($list) {
	$cats = get_categories('hide_empty=0');
	foreach($cats as $cat) {
		$find = 'cat-item-' . $cat->term_id . '"';
		$replace = 'cat-item-' . $cat->slug . ' cat-item-' . $cat->term_id . '"';
		$list = str_replace( $find, $replace, $list );
		$find = 'cat-item-' . $cat->term_id . ' ';
		$replace = 'cat-item-' . $cat->slug . ' cat-item-' . $cat->term_id . ' ';
		$list = str_replace( $find, $replace, $list );
	}
	return $list;
}
function the_categories_with_class($separator = ' ') {
	foreach((get_the_category()) as $cat) {
    	echo $separator . '<a href="' . get_category_link($cat->term_id) . '"  class="' . $cat->slug . '">' . $cat->cat_name . '</a>';
    }
}
add_filter('wp_list_categories', 'add_slug_class_wp_list_categories');

/**
 * Do the work to pagination work on custom post types listing pages.
 *
 */
define('PER_PAGE_DEFAULT', 10);
function custom_query_posts(array $query = array())
{
        global $wp_query;
        wp_reset_query();
 
        $paged = get_query_var('paged') ? get_query_var('paged') : 1;
 
        $defaults = array(
                'paged'                         => $paged,
                'posts_per_page'        => PER_PAGE_DEFAULT
        );
        $query += $defaults;
 
        $wp_query = new WP_Query($query);
}
