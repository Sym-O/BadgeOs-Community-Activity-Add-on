<?php

/**
 * Register [last_nominations] shortcode.
 *
 * @since 1.4.0
 */
function badgeos_register_last_earners_shortcode() {
    badgeos_log ( 'badgeos_register_last_earners_shortcode');

	// Setup a custom array of achievement types
	$achievement_types = array_diff( badgeos_get_achievement_types_slugs(), array( 'step' ) );
	array_unshift( $achievement_types, 'all' );

    // Setup a custom array of achievement tag
    $achievement_tags = get_terms('post_tag', 'fields=names&orderby=name');
	array_unshift( $achievement_tags, 'all' );

	badgeos_register_shortcode( array(
		'name'            => __( 'Last Earners', 'badgeos' ),
		'description'     => __( 'Output a list of the last earners', 'badgeos' ),
		'slug'            => 'badgeos_last_earners',
		'output_callback' => 'badgeos_last_earners_shortcode',
		'attributes'      => array(
			'type' => array(
				'name'        => __( 'Achievement Type(s)', 'badgeos' ),
				'description' => __( 'Single, or comma-separated list of, achievement type(s) to display.', 'badgeos' ),
				'type'        => 'text',
				'values'      => $achievement_types,
				'default'     => 'all',
				),
            'limit' => array(
				'name'        => __( 'Limit', 'badgeos' ),
				'description' => __( 'Number of achievements to display.', 'badgeos' ),
				'type'        => 'text',
				'default'     => 10,
				),
			'show_filter' => array(
				'name'        => __( 'Show Filter', 'badgeos' ),
				'description' => __( 'Display filter controls.', 'badgeos' ),
				'type'        => 'select',
				'values'      => array(
					'true'  => __( 'True', 'badgeos' ),
					'false' => __( 'False', 'badgeos' )
					),
				'default'     => 'true',
				),
			'show_search' => array(
				'name'        => __( 'Show Search', 'badgeos' ),
				'description' => __( 'Display a search input.', 'badgeos' ),
				'type'        => 'select',
				'values'      => array(
					'true'  => __( 'True', 'badgeos' ),
					'false' => __( 'False', 'badgeos' )
					),
				'default'     => 'true',
				),
			'orderby' => array(
				'name'        => __( 'Order By', 'badgeos' ),
				'description' => __( 'Parameter to use for sorting.', 'badgeos' ),
				'type'        => 'select',
				'values'      => array(
					'menu_order' => __( 'Menu Order', 'badgeos' ),
					'ID'         => __( 'Achievement ID', 'badgeos' ),
					'title'      => __( 'Achievement Title', 'badgeos' ),
					'date'       => __( 'Published Date', 'badgeos' ),
					'modified'   => __( 'Last Modified Date', 'badgeos' ),
					'author'     => __( 'Achievement Author', 'badgeos' ),
					'rand'       => __( 'Random', 'badgeos' ),
					),
				'default'     => 'menu_order',
				),
			'order' => array(
				'name'        => __( 'Order', 'badgeos' ),
				'description' => __( 'Sort order.', 'badgeos' ),
				'type'        => 'select',
				'values'      => array( 'ASC' => __( 'Ascending', 'badgeos' ), 'DESC' => __( 'Descending', 'badgeos' ) ),
				'default'     => 'ASC',
				),
			'user_id' => array(
				'name'        => __( 'User ID', 'badgeos' ),
				'description' => __( 'Show only achievements earned by a specific user.', 'badgeos' ),
				'type'        => 'text',
				),
			'include' => array(
				'name'        => __( 'Include', 'badgeos' ),
				'description' => __( 'Comma-separated list of specific achievement IDs to include.', 'badgeos' ),
				'type'        => 'text',
				),
			'exclude' => array(
				'name'        => __( 'Exclude', 'badgeos' ),
				'description' => __( 'Comma-separated list of specific achievement IDs to exclude.', 'badgeos' ),
				'type'        => 'text',
				),
			'wpms' => array(
				'name'        => __( 'Include Multisite Achievements', 'badgeos' ),
				'description' => __( 'Show achievements from all network sites.', 'badgeos' ),
				'type'        => 'select',
				'values'      => array(
					'true'  => __( 'True', 'badgeos' ),
					'false' => __( 'False', 'badgeos' )
					),
				'default'     => 'false',
				),
			'layout' => array(
				'name'        => __( 'Layout', 'badgeos' ),
				'description' => __( 'Achievements layout', 'badgeos' ),
                'type'        => 'select',
                'values'      => array(
                    'grid' => __('Grid', 'badgeos'),
                    'list' => __('List', 'badgeos'),
                    ),
                'default'     => 'list',
 				),
            'tag' => array(
				'name'        => __( 'Achievement Tag(s)', 'badgeos' ),
				'description' => __( 'Single, or comma-separated list of, achievement tag(s) to display.', 'badgeos' ),
				'type'        => 'text',
				'values'      => $achievement_tags,
                'default'     => 'all',
                ),
		),
	) );
}
add_action( 'init', 'badgeos_register_last_earners_shortcode', 11 );

/**
 * Achievement List Shortcode.
 *
 * @since  1.0.0
 *
 * @param  array $atts Shortcode attributes.
 * @return string 	   HTML markup.
 */
function badgeos_last_earners_shortcode( $atts = array () ){
    badgeos_log ( 'badgeos_last_earners_shortcode  Array:'.badgeos_arrayStr ($atts));

	// check if shortcode has already been run
	if ( isset( $GLOBALS['badgeos_last_earners_shortcode'] ) ){
		badgeos_log ( "Warn  already executed badgeos_last_earners_shortcode");
		return '';
	}
	badgeos_log ( "first time executing global badgeos_last_earners");

    global $user_ID;
	extract( shortcode_atts( array(
		'type'        => 'all',
		'limit'       => '10',
		'show_filter' => true,
		'show_search' => true,
		'group_id'    => '0',
		'user_id'     => '0',
		'wpms'        => false,
		'orderby'     => 'menu_order',
		'order'       => 'ASC',
		'include'     => array(),
		'exclude'     => array(),
		'meta_key'    => '',
        'meta_value'  => '',
        'layout'      => 'list',
		'tag'         => 'all',
        'show_goals'  => 'true',
	), $atts, 'badgeos_last_earners' ) );

	wp_enqueue_style( 'badgeos-front' );
	wp_enqueue_script( 'badgeos_community-activity' );

	$data = array(
		'ajax_url'    => esc_url( admin_url( 'admin-ajax.php', 'relative' ) ),
		'type'        => $type,
		'limit'       => $limit,
		'show_filter' => $show_filter,
		'show_search' => $show_search,
		'group_id'    => $group_id,
		'user_id'     => $user_id,
		'wpms'        => $wpms,
		'orderby'     => $orderby,
		'order'       => $order,
		'include'     => $include,
		'exclude'     => $exclude,
		'meta_key'    => $meta_key,
        'meta_value'  => $meta_value,
        'layout'      => $layout,
		'tag'         => $tag,
        'show_goals'  => $show_goals,
	);
	wp_localize_script( 'badgeos-community-activity', 'badgeos', $data );

	$last_earners = badgeos_get_last_earners($limit);
	badgeos_log ( "List : ". badgeos_arrayStr ($last_earners));
	
	$badges = '';

	// Filter
	//$badges .= '<div id="badgeos-last-earners-filters-wrap">';
	//$badges .= '</div><!-- #badgeos-last-earners-filters-wrap -->';

	// Content Container
    $badges .= '<div id="badgeos-last-earners-container">';
	reset($last_earners);	
	foreach ($last_earners as $post){
		$user_info = get_userdata($post->post_author);
		$achievement_id = get_post_meta($post->ID,'_badgeos_log_achievement_id',true);

		$badges .= '<div id= "badgeos-earner-item-'.$post->ID.'" class="badgeos-earner-item">' ;  
		$badges .= '<div class="badgeos-earner-avatar">';
        $badges .= '<a href="'.bp_core_get_user_domain( $user_info->id ).'/achievements/" onmouseover="displayEarnerDescription('.$post->ID.')" onmouseout="hideEarnerDescription('.$post->ID.')" onclick="hideDescription('.$post->ID.')">';
		$badges .= get_avatar( $user_info->id, 192);
        $badges .= '</a>';
		$badges .= '</div>';
		$badges .= '<div  class="badgeos-earner-achievement">';
        $badges .= '<a href="' . get_permalink( $achievement_id ) . '" onmouseover="displayEarnerDescription('.$post->ID.')" onmouseout="hideEarnerDescription('.$post->ID.')" onclick="hideDescription('.$post->ID.')">';
		$badges .= badgeos_get_achievement_post_thumbnail ($achievement_id);
        $badges .= '</a>';
		$badges .= '</div>';
        $badges .= '</div>';
        $today = date('Y-m-d h:i:s');
        $earned_date = $post->post_date;
        $delay = round((strtotime($today) - strtotime($earned_date))/(60*60*24));
        $badges .= '<div class="badgeos-earner-description" id="earner-description-'.$post->ID.'">'.$user_info->first_name.' a obtenu </br>"'.get_the_title($achievement_id).'"</br>il y a '.$delay.' jour(s)</div>';
    }

	$badges .= '<div style="clear: both;margin: 0px;padding: 0px;"></div>';
	$badges .= '</div><!-- #badgeos-last-nominations-container -->';


	// Hidden fields and Load More button
	$badges .= '<input type="hidden" id="badgeos_achievements_offset" value="0">';
	$badges .= '<input type="hidden" id="badgeos_achievements_count" value="0">';
	$badges .= '<input type="button" id="last_nominations_load_more" value="' . esc_attr__( 'Load More', 'badgeos' ) . '" style="display:none;">';
	//$badges .= '<div class="badgeos-spinner"></div>';

	// Reset Post Data
	wp_reset_postdata();

	// Save a global to prohibit multiple shortcodes
	$GLOBALS['badgeos_last_earners_shortcode'] = true;
    badgeos_log ( 'badgeos_last_earners_shortcode return '.$badges);

	return $badges;

}
