<?php
/**
* Community Activity achievements feature to BadgeOS plugin
*
* @package BadgeOS D2SI
* @subpackage Achievements
* @author D2SI
* @license http://www.gnu.org/licenses/agpl.txt GNU AGPL v3.0
* @link https://d2-si.fr
 */

/**
* Return a last earners array with max size matching topCount the arguments
*
* 
*/
function badgeos_get_last_earners ( $topCount=10 ) { 		
 badgeos_log ( 'badgeos_get_last_earners called with  $topCount='.$topCount);

 $args = array(
	'posts_per_page'   => $topCount,
	'offset'           => 0,
	'category'         => '',
	'category_name'    => '',
	'orderby'          => 'date',
	'order'            => 'DESC',
	'include'          => '',
	'exclude'          => '',
	'meta_key'         => '',
	'meta_value'       => '',
	'post_type'        => 'badgeos-log-entry',
	'post_mime_type'   => '',
	'post_parent'      => '',
	'author'	       => '',
	'post_status'      => 'publish',
	'suppress_filters' => true 
 );
 $res = get_posts( $args );
 badgeos_log ( "badgeos_get_last_earners for arg= ".badgeos_arrayStr ($args)."  return ".badgeos_arrayStr ($res));

 return  $res;
}


// TODO NON UTILE
// /* AJAX Helper for inserting last earners in achievement rendering
// *
// * @since 1.0.0
// * @return void
// */
//function badgeos_last_earners_filter($achievement_html, $goals_array = 0, $topCount=10){
//
//	badgeos_log ( "badgeos_last_earners_filter return HTML:".$achievement_html);
//    $show_goals = isset( $_REQUEST['show_goals'] ) ? $_REQUEST['show_goals'] : false;
//    $layout     = isset( $_REQUEST['layout'] )     ? $_REQUEST['layout']     : 'list';
//
//    $goals_array = ( $goals_array == 0 ) ? badgeos_get_last_earners( $user_ID, $topCount) : $goals_array;
//    $in_goals = in_array( get_the_ID() , $goals_array );
//
//        $achieved = badgeos_get_user_achievements( array( 'user_id' => $user_ID, 'achievement_id' => $achievement_id) );
//
//        // build button
//        $button = '';
//        if ($achieved && $in_goals)
//            $button = '<div class="goal-action"><img class="goal-no-action"  value="'.$achievement_id.'" src="'.badgeos_set_goals_get_directory_url().'/images/goal-success.png" title="Goal achieved"></img></div>';
//        else if (!$achieved && $in_goals)
//            $button = '<div class="goal-action"><img class="goal-action-img" value="'.$achievement_id.'" src="'.badgeos_set_goals_get_directory_url().'/images/goal-set.png" title="Click to UNset Goal"></img></div>';
//        else if (!$achieved && !$in_goals)
//            $button = '<div class="goal-action"><img class="goal-action-img" value="'.$achievement_id.'" src="'.badgeos_set_goals_get_directory_url().'/images/goal-to-set.png" title="Click to SET Goal"></img></div>';
//        
//        // Add button depending on layout
//        if ($layout == "list"){
//            $achievement_html = str_replace("<!-- .badgeos-item-image -->","<!-- .badgeos-item-image -->".$button, $achievement_html);
//            return $achievement_html;
//        }
//        else {
//            $button = '<div class="goal-action-container">'.$button;
//            $achievement_html = str_replace('<div class="badgeos-item-image">',$button.'<div class="badgeos-item-image">', $achievement_html);
//            $achievement_html = str_replace("<!-- .badgeos-item-image -->","</div><!-- .badgeos-item-image -->", $achievement_html);
//            return $achievement_html;
//        }
//}
//add_action( 'badgeos_get_last_earners', 'badgeos_last_earners_filter', 10, 2); // TODO Chelou ici...

