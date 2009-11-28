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
class GCalendarModelDay extends JModel {

	function getGoogleCalendarFeeds($start, $end) {
		GCalendarUtil::ensureSPIsLoaded();
		$calendarids = null;
		$gcids = $this->getState('gcids');
		if(!empty($gcids))
		$calendarids = $gcids;
		
		$results = GCalendarDBUtil::getCalendars($calendarids);
		if(empty($results))
		return array();

		$calendars = array();
		foreach ($results as $result) {
			if(!empty($result->calendar_id)){
				$feed = new SimplePie_GCalendar();
				$feed->set_show_past_events(FALSE);
				$feed->set_sort_ascending(TRUE);
				$feed->set_orderby_by_start_date(TRUE);
				$feed->set_expand_single_events(TRUE);
				$feed->enable_order_by_date(FALSE);
				$feed->enable_cache(FALSE);
				$feed->set_start_date($start);
				$feed->set_end_date($end);
				$feed->set_max_events(100);
				$feed->put('gcid',$result->id);
				$feed->put('gccolor',$result->color);
				$feed->set_cal_language(GCalendarUtil::getFrLanguage());
				$feed->set_timezone(GCalendarUtil::getComponentParameter('timezone'));

				$url = SimplePie_GCalendar::create_feed_url($result->calendar_id, $result->magic_cookie);
				$feed->set_feed_url($url);
				$feed->init();
				$feed->handle_content_type();
				$calendars[] = $feed;
			}
		}
		return $calendars;
	}
}
