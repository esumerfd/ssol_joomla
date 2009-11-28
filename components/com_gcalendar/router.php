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

require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_gcalendar'.DS.'util.php');

function GCalendarBuildRoute( &$query )
{
	$segments = array();
	$view = null;
	if(isset($query['view']))
	{
		$segments[] = $query['view'];
		$view = $query['view'];
		unset( $query['view'] );
	}
	if($view === 'event'){
		if(isset($query['eventID']))
		{
			$segments[] = $query['eventID'];
			unset( $query['eventID'] );
		}
		if(isset($query['gcid']))
		{
			$segments[] = $query['gcid'];
			unset( $query['gcid'] );
		}
	}else if($view === 'day'){
		if(isset($query['gcids']))
		{
			$segments[] = 'calendars';
			$calendars = $query['gcids'];
			if(empty($calendars))
			$calendars = array();
			if( !is_array( $calendars ) ) {
				$calendars = array($calendars);
			}
			$segments[] = implode("-", $calendars);
			unset( $query['gcids'] );
		}
	}else{
		if (isset($query['Itemid'])){
			$itemid = (int) $query['Itemid'];

			$menu = &JSite::getMenu();
			$params	=& $menu->getParams($itemid);
			if($params->get('calendarids')){
				$segments[] = 'calendars';
				$calendarids = $params->get('calendarids');
				if(empty($calendarids))
				$calendarids = array();
				if( !is_array( $calendarids ) ) {
					$calendarids = array($calendarids);
				}
				$segments[] = implode("-", $calendarids);
			}
		}
	}
	return $segments;
}

/**
 * @param	array
 * @return	array
 */
function GCalendarParseRoute( $segments )
{
	// Get the active menu item
	$menu =& JSite::getMenu();
	$item =& $menu->getActive();

	$vars = array();
	$view = $segments[0];
	//if the view is calendars it is a menu link
	if($view == 'calendars')
	$view = $item->query['view'];
	$vars['view'] = $view;

	switch($view)
	{
		case 'event':
			$vars['eventID'] = $segments[1];
			$vars['gcid'] = $segments[2];
			$vars['Itemid'] = GCalendarUtil::getItemId($segments[2]);
			break;
		case 'day':
			$vars['gcids'] = explode("-",$segments[2]);
			$calids = $vars['gcids']; 
			if(count($calids) > 0){
				$itemid = GCalendarUtil::getItemId($calids[0]);
				foreach ($calids as $cal) {
					$id = GCalendarUtil::getItemId($cal);
					if($id != $itemid){
						$itemid = null;
						break;
					}
				}
				if($itemid !=null){
					$vars['Itemid'] = $itemid;
				}
			}
			break;
		case 'google':
		case 'gcalendar':
			// do nothing
			break;
	}
	return $vars;
}
?>