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

class GCalendarDBUtil{

	/**
	 * Returns the database entries as objects with the column fields as
	 * variable (according to the joomla table framework).
	 *
	 * @param $calendarIDs the calendar ID's to find
	 */
	function getCalendars($calendarIDs) {
		$condition = '';
		if(!empty($calendarIDs)){
			if(is_array($calendarIDs)) {
				$condition = 'id IN ( ' . implode( ',', $calendarIDs ) . ')';
			} else {
				$condition = 'id = '.$calendarIDs;
			}
		}else
		return GCalendarDBUtil::getAllCalendars();

		$db =& JFactory::getDBO();
		$query = "SELECT id, calendar_id, name, color, magic_cookie  FROM #__gcalendar where ".$condition;
		$db->setQuery( $query );
		$results = $db->loadObjectList();
		return $results;
	}

	/**
	 * Returns all database entries as objects with the column fields as
	 * variable (according to the joomla table framework).
	 *
	 */
	function getAllCalendars() {
		$db =& JFactory::getDBO();
		$query = "SELECT id, calendar_id, name, color, magic_cookie  FROM #__gcalendar";
		$db->setQuery( $query );
		return $db->loadObjectList();
	}
}

?>