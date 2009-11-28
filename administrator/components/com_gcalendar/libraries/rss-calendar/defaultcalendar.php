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

defined( '_JEXEC' ) or die( 'Restricted access' );

require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_gcalendar'.DS.'util.php');
require_once ('eventrenderer.php');
require_once ('calendarrenderer.php');

class DefaultCalendar{

	var $id = '';
	var $feedFetcher;
	var $defaultView = 'month';
	var $forceView = null;
	var $weekStart = '1';
	var $showEventTitle = true;
	var $shortDayNames = false;
	var $cellHeight = 90;
	var $printDayLink = true;
	var $showSelectionList = true;
	var $columnInWeekViewEqual = false;

	var $cal;
	var $month, $year, $day;
	var $view;
	var $feeds;

	function DefaultCalendar($feedFetcher){
		$this->feedFetcher = $feedFetcher;
	}

	function display(){
		$calculatedDate = $this->calculateDate();
		$dateObject = getdate($calculatedDate);
		$this->month = (int)$dateObject["mon"];
		$this->year = (int)$dateObject["year"];
		$this->day = (int)$dateObject["mday"];

		$this->view = JRequest::getVar('gcalendarview', $this->getDefaultView());
		if($this->getForceView() != null){
			$this->view = $this->getForceView();
		}

		$userAgent = "unk";
		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			$uaRaw = strtolower($_SERVER['HTTP_USER_AGENT']);
			if (strpos($uaRaw, "opera") !== false)
			$userAgent = "opera";
			elseif (strpos($uaRaw, "msie") !== false) {
				$userAgent = "ie";
			}
			else
			$userAgent = "other";
		}

		switch($this->view) {
			case "month":
				$start = mktime(0, 0, 0, $this->month, 1, $this->year);
				$end = strtotime( "+1 month", $start );
				break;
			case "day":
				$start = mktime(0, 0, 0, $this->month, $this->day, $this->year);
				$end = strtotime( "+1 day", $start );
				break;
			case "week":
				$start = $this->getFirstDayOfWeek($this->year, $this->month, $this->day, $this->getWeekStart());
				$end = strtotime( "+1 week +1 day", $start );
		}
		$this->feeds = $this->getGoogleCalendarFeeds($start, $end);
		$cal = new CalendarRenderer($this);

		$document =& JFactory::getDocument();
		$document->addScript('administrator/components/com_gcalendar/libraries/nifty/nifty.js');
		$document->addStyleSheet('administrator/components/com_gcalendar/libraries/nifty/niftyCorners.css');
		$document->addStyleSheet('administrator/components/com_gcalendar/libraries/rss-calendar/gcalendar.css');
		if ($userAgent == "ie") {
			$document->addStyleSheet('administrator/components/com_gcalendar/libraries/rss-calendar/gcalendar-ie6.css');
		}

		$feeds = $this->getFeeds();
		if(!empty($feeds)){
			$calCode = "window.addEvent(\"domready\", function(){\n";
			foreach($feeds as $feed){
				$calCode .= "Nifty(\"div.gccal_".$feed->get('gcid')."\",\"small\");\n";
				$document->addStyleDeclaration("div.gccal_".$feed->get('gcid')."{padding: 1px;margin:0 auto;background:".GCalendarUtil::getFadedColor($feed->get('gccolor'))."}");
			}
			$calCode .= "});";
			$document->addScriptDeclaration($calCode);
		}

		echo "<div class=\"gcalendar".$this->id."\">\n";
		if($this->showSelectionList){
			$this->printCalendarSelectionList();
		}
		$this->printToolBar();
		$cal->printCal();
		echo "</div>\n";
	}

	function calculateDate(){
		$today = getdate();
		$day = JRequest::getVar('day', $today["mday"]);
		$month = JRequest::getVar('month', $today["mon"]);
		$year = JRequest::getVar('year', $today["year"]);
		if (!checkdate($month, $day, $year)) {
			$day = 1;
		}
		return mktime(0, 0, 0, $month, $day, $year);
	}

	function getFeeds(){
		return $this->feeds;
	}

	function getDefaultView(){
		return $this->defaultView;
	}

	function getForceView(){
		return $this->forceView;
	}

	function getWeekStart(){
		return $this->weekStart;
	}

	function getShowEventTitle(){
		return $this->showEventTitle;
	}

	function getShortDayNames(){
		return $this->shortDayNames;
	}

	function getCellHeight() {
		return $this->cellHeight;
	}

	function isColumnInWeekViewEqual() {
		return $this->columnInWeekViewEqual;
	}

	function getPrintDayLink() {
		return $this->printDayLink;
	}

	function createLink($year, $month, $day, $calids){
		$calids = $this->getIdString($calids);
		return JRoute::_("index.php?option=com_gcalendar&view=gcalendar&gcalendarview=day&year=".$year."&month=".$month."&day=".$day.$calids);
	}

	function printToolbar(){
	}

	function printCalendarSelectionList(){
		JHTML::_('behavior.mootools');
		$document = &JFactory::getDocument();
		$document->addScript( 'administrator/components/com_gcalendar/libraries/rss-calendar/gcalendar.js' );
		$calendar_list = "<div id=\"gc_gcalendar_view_list\"><table width=\"100%\"><tr>\n";
		$feeds = $this->getFeeds();
		if(!empty($feeds)){
			$totalFeeds = count($feeds);
			$slice = 100/$totalFeeds;
			for ($i = 0; $i < $totalFeeds; $i++) {
				$feed = $feeds[$i];
				//				$calendar_list .="<tr>\n";
				$calendar_list .="<td width=\"".$slice."%\"><div class=\"gccal_".$feed->get('gcid')."\"><font color=\"#FFFFFF\">".$feed->get('gcname')."</font></div></td>\n";
				//			$calendar_list .="</tr>\n";
			}
		}
		$calendar_list .="</tr></table><br/></div>\n";
		echo $calendar_list;
		echo "<div align=\"center\" style=\"text-align:center\">\n";
		echo "<a id=\"gc_gcalendar_view_toggle\" name=\"gc_gcalendar_view_toggle\" href=\"#\">\n";
		echo "<img src=\"".JURI::base() . "administrator/components/com_gcalendar/libraries/rss-calendar/btn-down.png\" id=\"gc_gcalendar_view_toggle_status\" alt=\"".JText::_('CALENDAR_LIST')."\" title=\"".JText::_('CALENDAR_LIST')."\"/>";
		echo "</a></div>\n";
	}

	/**
	 * This is a helper method to get a readable title according to the given view, date and weekStart.
	 *
	 */
	function getViewTitle($year, $month, $day, $weekStart, $view) {
		$date = JFactory::getDate();
		$title = '';
		switch($view) {
			case "month":
				$title = $date->_monthToString($month)." ".$year;
				break;
			case "week":
				$firstDisplayedDate = DefaultCalendar::getFirstDayOfWeek($year, $month, $day, $weekStart);
				$lastDisplayedDate = strtotime("+6 days", $firstDisplayedDate);
				$infoS = getdate($firstDisplayedDate);
				$infoF = getdate($lastDisplayedDate);

				if ($infoS["year"] != $infoF["year"]) {
					$m1 = substr($infoS["month"], 0, 3);
					$m2 = substr($infoF["month"], 0, 3);

					$title = $infoS["year"] .' '.$m1.' '. $infoS["mday"] . " - " . $infoF["year"] . ' '.$m2.' ' . $infoF["mday"];
				}else if ($infoS["mon"] != $infoF["mon"]) {
					$m1 = substr($infoS["month"], 0, 3);
					$m2 = substr($infoF["month"], 0, 3);

					$title = $infoS["year"] . ' '.$m1.' '. $infoS["mday"] . ' - '.$m1.' ' . $infoF["mday"];
				} else {
					$title = $infoS["year"] . " " . $infoS["month"] . " ". $infoS["mday"] . " - " . $infoF["mday"];
				}
				break;
			case "day":
				$tDate = strtotime("${year}-${month}-${day}");
				$title = strftime("%a, %Y %b %e", $tDate);
				break;
		}
		return $title;
	}

	/**
	 * This is an internal helper method and should not be called from outside of the class
	 * otherwise you know what you do.
	 *
	 */
	function getFirstDayOfWeek($year, $month, $day, $weekStart) {
		$tDate = strtotime($year.'-'.$month.'-'.$day);

		switch($weekStart){
			case 1:
				$name = 'Sunday';
				break;
			case 2:
				$name = 'Monday';
				break;
			case 7:
				$name = 'Saturday';
				break;
			default:
				$name = 'Sunday';
		}
		if (strftime("%w", $tDate) == $weekStart-1) {
			return $tDate;
		}else {
			return strtotime("last ".$name, $tDate);
		}
	}

	/**
	 * This is an internal helper method and should not be called from outside of the class
	 * otherwise you know what you do.
	 *
	 */
	function getGoogleCalendarFeeds($start, $end){
		if($this->feeds == null){
			$feedFetcher = $this->feedFetcher;
			$this->feeds = $feedFetcher->getGoogleCalendarFeeds($start, $end);
		}
		return $this->feeds;
	}

	/**
	 * This is an internal helper method and should not be called from outside of the class
	 * otherwise you know what you do.
	 *
	 */
	function getIdString($calids){
		$calendars = '';
		$itemid = null;
		if(!empty($calids)){
			$calendars = '&gcids='.implode(',',$calids);
			$itemid = GCalendarUtil::getItemId($calids[0]);
			foreach ($calids as $cal) {
				$id = GCalendarUtil::getItemId($cal);
				if($id != $itemid){
					$itemid = null;
					break;
				}
			}
		}
		if($itemid !=null){
			return $calendars.'&Itemid='.$itemid;
		}
		return $calendars;
	}
	
	function getTranslatedViewName($view = null){
		if($view == null)
		$view = $this->view;
		if($view == 'month')
		return ' '.JText::_('VIEW_MONTH');
		if($view == 'week')
		return ' '.JText::_('VIEW_WEEK');
		if($view == 'day')
		return ' '.JText::_('VIEW_DAY');
		return '';
	}
}
?>