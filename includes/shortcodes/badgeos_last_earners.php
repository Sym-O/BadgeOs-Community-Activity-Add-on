<?php

/**
 * Register [last_nominations] shortcode.
 *
 * @since 1.4.0
 */
function badgeos_register_last_earners_shortcode() {

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
				'description' => __( 'Number of earnings to display.', 'badgeos' ),
				'type'        => 'text',
				'default'     => 10,
				),
			'user_id' => array(
				'name'        => __( 'User ID', 'badgeos' ),
				'description' => __( 'Show only achievements earned by a specific user.', 'badgeos' ),
				'type'        => 'text',
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
	// check if shortcode has already been run
	if ( isset( $GLOBALS['badgeos_last_earners_shortcode'] ) ){
		return '';
	}

    global $user_ID;
	extract( shortcode_atts( array(
		'type'        => 'all',
		'limit'       => '10',
		'group_id'    => '0',
		'user_id'     => '0',
		'meta_key'    => '',
        'meta_value'  => '',
		'tag'         => 'all',
	), $atts, 'badgeos_last_earners' ) );

	wp_enqueue_style( 'badgeos-front' );
	wp_enqueue_script( 'badgeos_community-activity' );

	$data = array(
		'ajax_url'    => esc_url( admin_url( 'admin-ajax.php', 'relative' ) ),
		'type'        => $type,
		'limit'       => $limit,
		'group_id'    => $group_id,
		'user_id'     => $user_id,
		'meta_key'    => $meta_key,
        'meta_value'  => $meta_value,
		'tag'         => $tag,
	);
	wp_localize_script( 'badgeos-community-activity', 'badgeos', $data );

    // Get last earnings
	$last_earnings = badgeos_get_last_earnings($limit);
	
	$badges = '';

	// Filter
	//$badges .= '<div id="badgeos-last-earners-filters-wrap">';
	//$badges .= '</div><!-- #badgeos-last-earners-filters-wrap -->';

	// Content Container
    $badges .= '<div id="badgeos-last-earners-container">';
	reset($last_earnings);	
	foreach ($last_earnings as $earning){
		$user_info = get_userdata($earning->post_author);
		$achievement_id = get_post_meta($earning->ID,'_badgeos_log_achievement_id',true);
        if (strrpos($earning->post_name,"-credly",7)) {
            $achievement_type = "credly";
        } else {
            $achievement_type = get_post_type($achievement_id);
        }
        $avatar_url="";
        $achievement_url="";
        $image="";
        $description = "";

        // define each displayed element of the earning
        switch ($achievement_type){
        case "step":
            $parent_achievement = badgeos_get_parent_of_achievement($achievement_id);
            $avatar_url         = bp_core_get_user_domain( $user_info->id ).'achievements/"';
            $achievement_url    = get_permalink($parent_achievement);
            $achievement_image  = badgeos_get_achievement_post_thumbnail ($achievement_id);
            $description        = $user_info->first_name.' a validé une étape pour </br>"'.get_the_title($parent_achievement);
            break;
        case "credly":
            $avatar_url         = 'https://credly.com/recipients/'.get_post_meta($achievement_id,'_badgeos_credly_badge_id',true);
            $achievement_url    = 'https://credly.com/recipients/'.get_post_meta($achievement_id,'_badgeos_credly_badge_id',true);
            $achievement_image  = '<img class="badgeos-item-thumbnail wp-post-image" width="100" height="100" src="'.badgeos_community_activity_get_directory_url().'/images/credly-active.png"></img>';
            $description        = $user_info->first_name.' a publié sur Credly son badge </br>"'.get_the_title($achievement_id);
            break;
        default:
            $avatar_url         = bp_core_get_user_domain( $user_info->id ).'achievements/';
            $achievement_url    = get_permalink($achievement_id);
            $achievement_image  = badgeos_get_achievement_post_thumbnail ($achievement_id);
            $description        = $user_info->first_name.' a obtenu </br>"'.get_the_title($achievement_id);
            break;
        }

        // Compose the HTML message
		$badges .= '<div id= "badgeos-earner-item-'.$earning->ID.'" class="badgeos-earner-item">' ;  
		$badges .= '<div class="badgeos-earner-avatar">';
        $badges .= '<a href="'.$avatar_url.'" target="_blank" onmouseover="displayEarnerDescription('.$earning->ID.')" onmouseout="hideEarnerDescription('.$earning->ID.')" onclick="hideDescription('.$earning->ID.')">';
		$badges .= get_avatar( $user_info->id, 192);
        $badges .= '</a>';
		$badges .= '</div>';
		$badges .= '<div  class="badgeos-earner-achievement">';
        $badges .= '<a href="' . $achievement_url . '" target="_blank" onmouseover="displayEarnerDescription('.$earning->ID.')" onmouseout="hideEarnerDescription('.$earning->ID.')" onclick="hideDescription('.$earning->ID.')">';
		$badges .= $achievement_image;
        $badges .= '</a>';
		$badges .= '</div>';
        $badges .= '</div>';
        // Description of earning
        $today = date('Y-m-d h:i:s');
        $earned_date = $earning->post_date;
        $delay = abs(round((strtotime($today) - strtotime($earned_date))/(60*60*24)));
        $badges .= '<div class="badgeos-earner-description" id="earner-description-'.$earning->ID.'">'.$description.'"</br>il y a '.$delay.' jour(s)</div>';
    }

	$badges .= '<div style="clear: both;margin: 0px;padding: 0px;"></div>';
	$badges .= '</div><!-- #badgeos-last-nominations-container -->';

	// Reset Post Data
	wp_reset_postdata();

	// Save a global to prohibit multiple shortcodes
	$GLOBALS['badgeos_last_earners_shortcode'] = true;

	return $badges;
}
