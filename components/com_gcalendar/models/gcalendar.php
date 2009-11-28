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

defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_gcalendar'.DS.'util.php');
require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_gcalendar'.DS.'dbutil.php');

/**
 * GCalendar Model
 *
 */
class GCalendarModelGCalendar extends JModel {

	var $cached_data = null;

	/**
	 * Returns all calendars in the database. The returned
	 * rows contain an additional attribute selected which is set
	 * to true when the specific calendar is mentioned in the
	 * parameters property calendarids.
	 *
	 * @return the calendars specified in the database
	 */
	function getDBCalendars(){
		if($this->cached_data == null){
			$params = $this->getState('parameters.menu');
			$calendarids = null;
			if($params != null)
			$calendarids=$params->get('calendarids');

			$this->cached_data = GCalendarDBUtil::getAllCalendars();
		}
		return $this->cached_data;
	}

	function getGoogleCalendarFeeds($startDate, $endDate, $projection = null) {
		GCalendarUtil::ensureSPIsLoaded();
		$results = $this->getDBCalendars();
		if(empty($results))
		return null;

		$params = $this->getState('parameters.menu');
		$useCache = false;
		$calendarids = array();
		if($params != null){
			$useCache = $params->get('cache', 'no') == 'yes';
			$tmp = $params->get('calendarids');
			if(is_array($tmp))
			$calendarids = $tmp;
			else if(!empty($tmp))
			$calendarids[] = $tmp;
		}

		$calendars = array();
		foreach ($results as $result) {
			if(empty($result->calendar_id))
			continue;
			if(!empty($calendarids) && !in_array($result->id, $calendarids))
			continue;

			$feed = new SimplePie_GCalendar();
			$feed->set_show_past_events(FALSE);
			$feed->set_sort_ascending(TRUE);
			$feed->set_orderby_by_start_date(TRUE);
			$feed->set_expand_single_events(TRUE);
			$feed->enable_order_by_date(FALSE);
			$feed->enable_cache($useCache);
			if($useCache){
				// check if cache directory exists and is writeable
				$cacheDir =  JPATH_BASE.DS.'cache'.DS.'com_gcalendar';
				JFolder::create($cacheDir, 0755);
				if ( !is_writable( $cacheDir ) ) {
					JError::raiseWarning( 500, "Created cache at ".$cacheDir." is not writable, disabling cache.");
					$cache_exists = false;
				}else{
					$cache_exists = true;
				}

				//check and set caching
				$feed->enable_cache($cache_exists);
				if($cache_exists) {
					$feed->set_cache_location($cacheDir);
					$cache_time = (intval($params->get( 'cache_time', 3600 )));
					$feed->set_cache_duration($cache_time);
				}
			}
			$feed->set_projection($projection);
			$feed->set_start_date($startDate);
			$feed->set_end_date($endDate);
			$feed->set_max_events(100);
			$feed->put('gcid',$result->id);
			$feed->put('gccolor',$result->color);
			$feed->put('gcname',$result->name);
			$feed->set_cal_language(GCalendarUtil::getFrLanguage());
			$feed->set_timezone(GCalendarUtil::getComponentParameter('timezone'));

			$url = SimplePie_GCalendar::create_feed_url($result->calendar_id, $result->magic_cookie);
			$feed->set_feed_url($url);
			$feed->init();
			$feed->handle_content_type();
			$calendars[] = $feed;
		}

		return $calendars;
	}
}
