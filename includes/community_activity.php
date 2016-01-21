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
    $args = array(
        'post_type'         => 'badgeos-log-entry',
        'posts_per_page'    => $topCount,
        's'                 => 'unlocked',
        'post_status'       => 'publish',
        'orderby'           => 'date', 
        'order'             => 'DESC'        
    );
    $res = new WP_Query($args);

    return  $res->posts;
}
