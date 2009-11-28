<?php
/**
 * GCalendar is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * GCalendar is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GCalendar.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Allon Moritz
 * @copyright 2007-2009 Allon Moritz
 * @version $Revision: 2.1.2 $
 */

/**
 * Util class.
 *
 */
class GCalendarUtil{

	function ensureSPIsLoaded(){
		jimport('simplepie.simplepie');

		if(!class_exists('SimplePie_GCalendar')){
			require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_gcalendar'.DS.'libraries'.DS.'sp-gcalendar'.DS.'simplepie-gcalendar.php');
		}
	}

	function loadJQuery(){
		static $jQueryloaded;
		if($jQueryloaded == null){
			$params   = JComponentHelper::getParams('com_languages');
			if($params->get('loadJQuery', 'yes') == 'yes'){
				$document =& JFactory::getDocument();
				$document->addScript('administrator/components/com_gcalendar/libraries/jquery/jquery-1.3.2.js');
				$document->addScriptDeclaration("jQuery.noConflict();");
			}
			$jQueryloaded = 'loaded';
		}
	}

	function getComponentParameter($key){
		$params   = JComponentHelper::getParams('com_gcalendar');
		return $params->get($key);
	}

	function getFrLanguage(){
		$conf	=& JFactory::getConfig();
		return $conf->getValue('config.language');
//		$params   = JComponentHelper::getParams('com_languages');
//		return $params->get('site', 'en-GB');
	}

	function getItemId($cal_id){
		$component	= &JComponentHelper::getComponent('com_gcalendar');
		$menu = &JSite::getMenu();
		$items		= $menu->getItems('componentid', $component->id);

		if (is_array($items)){
			global $mainframe;
			$pathway	= &$mainframe->getPathway();
			foreach($items as $item) {
				$paramsItem	=& $menu->getParams($item->id);
				$calendarids = $paramsItem->get('calendarids');
				$contains_gc_id = FALSE;
				if ($calendarids){
					if( is_array( $calendarids ) ) {
						$contains_gc_id = in_array($cal_id,$calendarids);
					} else {
						$contains_gc_id = $cal_id == $calendarids;
					}
				}
				if($contains_gc_id){
					return $item->id;
				}
			}
		}
		return null;
	}

	function getFadedColor($pCol, $pPercentage = 85) {
		$pPercentage = 100 - $pPercentage;
		$rgbValues = array_map( 'hexDec', GCalendarUtil::str_split( ltrim($pCol, '#'), 2 ) );

		for ($i = 0, $len = count($rgbValues); $i < $len; $i++) {
			$rgbValues[$i] = decHex( floor($rgbValues[$i] + (255 - $rgbValues[$i]) * ($pPercentage / 100) ) );
		}

		return '#'.implode('', $rgbValues);
	}

	/**
	 * The php string split method for beeing php 4 compatible.
	 *
	 */
	function str_split($string,$string_length=1) {
		if(strlen($string)>$string_length || !$string_length) {
			do {
				$c = strlen($string);
				$parts[] = substr($string,0,$string_length);
				$string = substr($string,$string_length);
			} while($string !== false);
		} else {
			$parts = array($string);
		}
		return $parts;
	}
}
?>