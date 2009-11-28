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
 * @version $Revision: 0.3.0 $
 */

if (!defined('SIMPLEPIE_NAMESPACE_GOOGLE_CALENDAR_ITEM')) {
	define('SIMPLEPIE_NAMESPACE_GOOGLE_CALENDAR_ITEM', 'http://schemas.google.com/g/2005');
}

if (!defined('SIMPLEPIE_NAMESPACE_GOOGLE_CALENDAR_FEED')) {
	define('SIMPLEPIE_NAMESPACE_GOOGLE_CALENDAR_FEED', 'http://schemas.google.com/gCal/2005');
}

/**
 * SimplePie_GCalendar is the SimplePie extension which provides some
 * helper methods.
 *
 * @see http://code.google.com/apis/calendar/docs/2.0/reference.html
 */
class SimplePie_GCalendar extends SimplePie {

	var $show_past_events = FALSE;
	var $sort_ascending = TRUE;
	var $orderby_by_start_date = TRUE;
	var $expand_single_events = TRUE;
	var $cal_language = "";
	var $cal_query = "";
	var $meta_data = array();
	var $start_date = null;
	var $end_date = null;
	var $max_events = 25;
	var $projection = "full";
	var $timezone = "";

	/**
	 * If the method $this->get_items() should include past events.
	 */
	function set_show_past_events($value = FALSE){
		$this->show_past_events = $value;
	}

	/**
	 * If is set to true the closest event is the first in the returning items.
	 * So it makes sense to call enable_order_by_date(false) before fetching
	 * the data to prevent from sorting twice.
	 */
	function set_sort_ascending($value = TRUE){
		$this->sort_ascending = $value;
	}

	/**
	 * The method $this->get_items() will return the events ordered by
	 * the start date if set to true otherwise by the publish date.
	 *
	 */
	function set_orderby_by_start_date($value = TRUE){
		$this->orderby_by_start_date = $value;
	}

	/**
	 * If the method $this->get_items() should treat reccuring events
	 * as one item.
	 */
	function set_expand_single_events($value = TRUE){
		$this->expand_single_events = $value;
	}

	/**
	 * Sets the language of the feed. Something like
	 * en or en_GB or de.
	 */
	function set_cal_language($value = ""){
		$this->cal_language = $value;
	}

	/**
	 * If this parameter is set the feed will just contain events
	 * which contain the given parameter in the titel or the description.
	 *
	 * @param $value
	 */
	function set_cal_query($value = ""){
		$this->cal_query = $value;
	}

	/**
	 * Sets the start date of the events in the feed, set_end_date(...)
	 * must also be feeded with a value.
	 * If this value is set the set_show_past_events(...)
	 * will be ignored.
	 *
	 * @param $value must php timestamp
	 */
	function set_start_date($value = 0){
		$this->start_date = strftime('%Y-%m-%dT%H:%M:%S',$value);
	}

	/**
	 * Sets the end date of the events in the feed, set_start_date(...)
	 * must also be feeded with a value.
	 * If this value is set the set_show_past_events(...)
	 * will be ignored.
	 *
	 * @param $value must be php timestamp
	 */
	function set_end_date($value = 0){
		$this->end_date = strftime('%Y-%m-%dT%H:%M:%S',$value);
	}

	/**
	 * Sets the max events this feed should fetch
	 *
	 * @param $value the max events
	 */
	function set_max_events($value = 25){
		$this->max_events = $value;
	}

	/**
	 * Sets the projection of this feed. The given value must one of the
	 * following strings:
	 * - full
	 * - full-noattendees
	 * - composite
	 * - attendees-only
	 * - free-busy
	 * - basic
	 * Note if the feed projection is not full, some methodes in the returned
	 * items are empty. For example if the feed projection is basic the method
	 * SimplePie_Item_GCalendar->get_start_date() returns an empty string,
	 * because this information is only included in the full projection.
	 * @param $value
	 */
	function set_projection($value = "full"){
		if(!empty($value))
		$this->projection = $value;
	}

	/**
	 * Sets the timezone of this feed.
	 *
	 * @param $value
	 */
	function set_timezone($value = "") {
		if(!empty($value))
		$this->timezone = $value;
	}

	/**
	 * Overrides the default ini method and sets automatically
	 * SimplePie_Item_GCalendar as item class.
	 * It also sets the variables specified in this feed as query
	 * parameters.
	 */
	function init(){
		$this->set_item_class('SimplePie_Item_GCalendar');

		$new_url;
		if (!empty($this->multifeed_url)){
			$tmp = array();
			foreach ($this->multifeed_url as $value)
			$tmp[] = $this->check_url($value);
			$new_url = $tmp;
		}else{
			$new_url = $this->check_url($this->feed_url);
		}
		$this->set_feed_url($new_url);

		parent::init();
	}

	/**
	 * Creates an url depending on the variables $show_past_events, etc.
	 * and returns a valid google calendar feed url.
	 */
	function check_url($url_to_check){
		$new_url = parse_url($url_to_check);
		$path = $new_url['path'];
		$path = substr($path, 0, strrpos($path, '/')+1).$this->projection;
		$query = '';
		if(isset($new_url['query'])){
			$query = $new_url['query'];
		}
		$tmp = $new_url['scheme'].'://'.$new_url['host'].$path.'?'.$query;
		if(!empty($new_url['query']))
		$tmp = $this->append($tmp,'&');
		if($this->start_date==null && $this->end_date==null){
			if($this->show_past_events )
			$tmp = $this->append($tmp,'futureevents=false&');
			else
			$tmp = $this->append($tmp,'futureevents=true&');
		}
		if($this->start_date!=null){
			$tmp = $this->append($tmp,'start-min='.$this->start_date.'&');
		}
		if( $this->end_date!=null){
			$tmp = $this->append($tmp,'start-max='.$this->end_date.'&');
		}
		if($this->sort_ascending)
		$tmp = $this->append($tmp,'sortorder=ascending&');
		else
		$tmp = $this->append($tmp,'sortorder=descending&');
		if($this->orderby_by_start_date)
		$tmp = $this->append($tmp,'orderby=starttime&');
		else
		$tmp = $this->append($tmp,'orderby=lastmodified&');
		if($this->expand_single_events)
		$tmp = $this->append($tmp,'singleevents=true&');
		else
		$tmp = $this->append($tmp,'singleevents=false&');
		if(!empty($this->cal_language))
		$tmp = $this->append($tmp,'hl='.$this->cal_language.'&');
		if(!empty($this->cal_query))
		$tmp = $this->append($tmp,'q='.$this->cal_query.'&');
		if(!empty($this->timezone))
		$tmp = $this->append($tmp,'ctz='.$this->timezone.'&');
		$tmp = $this->append($tmp,'max-results='.$this->max_events);

		return $tmp;
	}

	/**
	 * Internal helper method to append a string to an other one.
	 */
	function append($value, $appendix){
		$pos = strpos($value,$appendix);
		if($pos === FALSE)
		$value .= $appendix;
		return $value;
	}

	/**
	 * Returns the timezone of the feed.
	 */
	function get_timezone(){
		$tzvalue = $this->get_feed_tags(SIMPLEPIE_NAMESPACE_GOOGLE_CALENDAR_FEED, 'timezone');
		return $tzvalue[0]['attribs']['']['value'];
	}

	/**
	 * Sets the given value for the given key which is accessible in the get(...) method.
	 * @param $key
	 * @param $value
	 */
	function put($key, $value){
		$this->meta_data[$key] = $value;
	}

	/**
	 * Returns the value for the given key which is set in the set(...) method.
	 * @param $key
	 * @return the value
	 */
	function get($key){
		return $this->meta_data[$key];
	}

	/**
	 * Creates a valid feed url for the given email address.
	 * If the magic cookie parameter is set it will return the private feed url.
	 */
	function create_feed_url($email_address, $magic_cookie = null){
		$type = 'public';
		if($magic_cookie != null)
		$type = 'private-'.$magic_cookie;

		return 'http://www.google.com/calendar/feeds/'.$email_address.'/'.$type.'/full';
	}
}

/**
 * The GCalendar Item which provides more google calendar specific
 * functions like the location of the event, etc.
 */
class SimplePie_Item_GCalendar extends SimplePie_Item {

	// static variables used as return value for get_day_type()
	var $SINGLE_WHOLE_DAY    = 1;
	var $SINGLE_PART_DAY     = 2;
	var $MULTIPLE_WHOLE_DAY  = 3;
	var $MULTIPLE_PART_DAY   = 4;

	//internal cache variables
	var $gc_id;
	var $gc_pub_date;
	var $gc_location;
	var $gc_status;
	var $gc_start_date;
	var $gc_end_date;
	var $gc_day_type;

	/**
	 * Returns the id of the event.
	 *
	 * @return the id of the event
	 */
	function get_id(){
		if(!$this->gc_id){
			$this->gc_id = substr($this->get_link(),strpos(strtolower($this->get_link()),'eid=')+4);
		}
		return $this->gc_id;
	}

	/**
	 * Returns the publish date as unix timestamp of the event.
	 *
	 * @return the publish date of the event
	 */
	function get_publish_date(){
		if(!$this->gc_pub_date){
			$pubdate = $this->get_date('Y-m-d\TH:i:s\Z');
			$this->gc_pub_date = SimplePie_Item_GCalendar::tstamptotime($pubdate);
		}
		return $this->gc_pub_date;
	}

	/**
	 * Returns the location of the event.
	 *
	 * @return the location of the event
	 */
	function get_location(){
		if(!$this->gc_location){
			$gd_where = $this->get_item_tags(SIMPLEPIE_NAMESPACE_GOOGLE_CALENDAR_ITEM, 'where');
			if(isset($gd_where[0]) &&
			isset($gd_where[0]['attribs']) &&
			isset($gd_where[0]['attribs']['']) &&
			isset($gd_where[0]['attribs']['']['valueString']))
			$this->gc_location = $gd_where[0]['attribs']['']['valueString'];
		}
		return $this->gc_location;
	}

	/**
	 * Returns the status of the event.
	 *
	 * @return the status of the event
	 */
	function get_status(){
		if(!$this->gc_status){
			$gd_where = $this->get_item_tags(SIMPLEPIE_NAMESPACE_GOOGLE_CALENDAR_ITEM, 'eventStatus');
			$this->gc_status = substr( $gd_status[0]['attribs']['']['value'], -8);
		}
		return $this->gc_status;
	}

	/**
	 * If the given format (must match the criterias of strftime)
	 * is not null a string is returned otherwise a unix timestamp.
	 *
	 * @see http://www.php.net/mktime
	 * @see http://www.php.net/strftime
	 * @param $format
	 * @return the start date of the event
	 */
	function get_start_date($format = null){
		if(!$this->gc_start_date){
			$when = $this->get_item_tags(SIMPLEPIE_NAMESPACE_GOOGLE_CALENDAR_ITEM, 'when');
			$startdate = $when[0]['attribs']['']['startTime'];
			$this->gc_start_date = SimplePie_Item_GCalendar::tstamptotime($startdate);
		}
		if($format != null)
		return strftime($format, $this->gc_start_date);
		return $this->gc_start_date;
	}

	/**
	 * If the given format (must match the criterias of strftime)
	 * is not null a string is returned otherwise a unix timestamp.
	 *
	 * @see http://www.php.net/mktime
	 * @see http://www.php.net/strftime
	 * @param $format
	 * @return the end date of the event
	 */
	function get_end_date($format = null){
		if(!$this->gc_end_date){
			$when = $this->get_item_tags(SIMPLEPIE_NAMESPACE_GOOGLE_CALENDAR_ITEM, 'when');
			$enddate = $when[0]['attribs']['']['endTime'];
			$this->gc_end_date = SimplePie_Item_GCalendar::tstamptotime($enddate);
		}
		if($format != null)
		return strftime($format, $this->gc_end_date);
		return $this->gc_end_date;
	}

	/**
	 * Returns the event type. One of the following constants:
	 *  - SINGLE_WHOLE_DAY
	 *  - SINGLE_PART_DAY
	 *  - MULTIPLE_WHOLE_DAY
	 *  - MULTIPLE_PART_DAY
	 *
	 * @return the event type
	 */
	function get_day_type(){
		if(!$this->gc_day_type){
			$SECSINDAY=86400;

			if (($this->get_start_date()+ $SECSINDAY) <= $this->get_end_date()) {
				if (($this->get_start_date()+ $SECSINDAY) == $this->get_end_date()) {
					$this->gc_day_type =  $this->SINGLE_WHOLE_DAY;
				} else {
					if ((date('g:i a',$this->get_start_date())=='12:00 am')&&(date('g:i a',$this->get_end_date())=='12:00 am')){
						$this->gc_day_type =  $this->MULTIPLE_WHOLE_DAY;
					}else{
						$this->gc_day_type =  $this->MULTIPLE_PART_DAY;
					}
				}
			}else
			$this->gc_day_type = $this->SINGLE_PART_DAY;
		}
		return $this->gc_day_type;
	}

	/**
	 * Returns a unix timestamp of the given iso date.
	 *
	 * @param $iso_date
	 * @return unix timestamp
	 */
	function tstamptotime($iso_date) {
		// converts ISODATE to unix date
		// 1984-09-01T14:21:31Z
		sscanf($iso_date,"%u-%u-%uT%u:%u:%uZ",$year,$month,$day,$hour,$min,$sec);
		$newtstamp = mktime($hour,$min,$sec,$month,$day,$year);
		return $newtstamp;
	}

	/**
	 * Returns an integer less than, equal to, or greater than zero if
	 * the first argument is considered to be respectively less than,
	 * equal to, or greater than the second.
	 * This function can be used to sort an array of SimplePie_Item_GCalendar
	 * items with usort.
	 *
	 * @see http://www.php.net/usort
	 * @param $gc_sp_item1
	 * @param $gc_sp_item2
	 * @return the comparison integer
	 */
	function compare($gc_sp_item1, $gc_sp_item2){
		$time1 = $gc_sp_item1->get_start_date();
		$time2 = $gc_sp_item2->get_start_date();
		$feed = $gc_sp_item1->get_feed();
		if(!$feed->orderby_by_start_date){
			$time1 = $gc_sp_item1->get_publish_date();
			$time2 = $gc_sp_item2->get_publish_date();
		}
		return $time1-$time2;
	}
}
?>