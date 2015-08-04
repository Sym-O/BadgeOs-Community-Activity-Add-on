<?php
/**
* Add specific log feature to BadgeOS plugin
*
* @package BadgeOS D2SI
* @subpackage Achievements
* @author D2SI
* @license http://www.gnu.org/licenses/agpl.txt GNU AGPL v3.0
* @link https://d2-si.fr
 */


function badgeos_getCurDateTime () {
 
    $datetime = new DateTime(); 
    $datetime->setTimezone($tz_object); 
    
    return $datetime->format('Y/m/d H:i:s');
}
/**
* log
*/
function badgeos_log ( $msg ) {
	if (WP_DEBUG && $msg) {
        error_log(
		    badgeos_getCurDateTime () ." - ".$msg.PHP_EOL, 3, "/temp/test.log");
	}
}

class ArrayValue implements JsonSerializable {
	public function __construct(array $array) {
		$this->array = $array;
	}

	public function jsonSerialize() {
		return $this->array;
	}
}


function badgeos_arrayStr ( $arr ) {
	/*reset($arr);
	foreach($arr as $index => $value) {
		if($index==0){
			$res = '[';
		}
		else{
			$res .= ',';
		}
		$res .= '\''.$index.'\'=>\''. implode($value).'\'';
	}
	$res .= ']';*/
	return json_encode(new ArrayValue($arr), JSON_UNESCAPED_UNICODE);;
}
