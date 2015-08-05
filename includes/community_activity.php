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
function badgeos_get_last_earnings ( $topCount=10 ) { 		
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
