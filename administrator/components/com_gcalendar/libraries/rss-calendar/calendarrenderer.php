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

class CalendarRenderer {
	var $calendar = null;

	function CalendarRenderer(&$calendar) {
		$this->calendar = &$calendar;
	}

	function itemsForDate($year, $month, $day) {
		$result = array();
		$gcal = $this->calendar;
		$feeds = $gcal->getFeeds();
		$requestedDayStart = mktime(0, 0, 0, $month, $day, $year);
		$requestedDayEnd = $requestedDayStart + 86400;
		if(!empty($feeds)){
			foreach($feeds as $feed){
				foreach($feed->get_items() as $item){
					if($requestedDayStart <= $item->get_start_date()
					&& $item->get_start_date() < $requestedDayEnd){
						$result[] = $item;
					}else if($requestedDayStart < $item->get_end_date()
					&& $item->get_end_date() <= $requestedDayEnd){
						$result[] = $item;
					}else if($item->get_start_date() <= $requestedDayStart
					&& $requestedDayEnd <= $item->get_end_date()){
						$result[] = $item;
					}
				}
			}
			usort($result, array("SimplePie_Item_GCalendar", "compare"));
		}
		return $result;
	}

	function printEvent($view, $item, $height = -1, $top = -1){
		$gcal = $this->calendar;
		if(!$gcal->getShowEventTitle())
		return;
		$feed = $item->get_feed();
		if($height > -1)
		echo "<div class=\"Event\" style=\"height:".$height."px; top:".$top."px\">";
		echo "<div class=\"gccal_".$feed->get('gcid')."\">";
		if($height > -1)
		echo "<div style=\"height:".($height-2)."px\">";
		EventRenderer::display($view ,$item);
		if($height > -1){
			echo "</div>";
			echo "</div>";
		}
		echo "</div>";
	}

	function printMonth($year, $month, $day) {
		$today = getdate();
		$gcal = $this->calendar;

		$startWeekDay = ((int)$gcal->getWeekStart())-1;
		$daysOffset = (strftime("%w", mktime(0, 0, 0, $month, 1, $year))+(7-$startWeekDay))%7;
		echo "<table id=\"gc_month_table".$gcal->id."\" class=\"gcalendarcal CalMonth\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\"><tr>";
		// print days of the week at the top
		$dateObject = JFactory::getDate();
		for ($i=0; $i<7; $i++) {
			echo "<th>".$dateObject->_dayToString(($i+$startWeekDay)%7, $gcal->getShortDayNames())."</th>\n";
		}
		echo "</tr>";
		for ($i=28; $i<33; $i++) {
			if (!checkdate($month, $i, $year)) {
				$lastDay = $i-1;
				$lastDaySlot = ($lastDay - 1) + $daysOffset;
				$lastDayOfWeek = $lastDaySlot % 7;
				$numberOfSlotsNeeded = $daysOffset + $lastDay;
				$numRows = ceil($numberOfSlotsNeeded / 7);
				break;
			}
		}

		$colWidth = "14%";
		$rowHeight = (int)round(100 / $numRows) . "%";
		for ($i=0;$i<$daysOffset;$i++) {
			if($i==0)echo "<tr>";
			echo "<td class=\"EmptyCell\" height=\"".$gcal->getCellHeight()."\"></td>\n";
		}
		for ($i=0;$i< $lastDay;$i++) {
			if (($i + $daysOffset) % 7 == 0) {
				echo "<tr>";
			}
			$thisDay = $i + 1;
			$myItems = $this->itemsForDate($year, $month, $thisDay);
			$calids = array();
			if ($myItems) {
				foreach($myItems as $item) {
					$feed = $item->get_feed();
					if(!in_array($feed->get('gcid'),$calids))
					$calids[] = $feed->get('gcid');
				}
			}
			$thisLink = $gcal->createLink($year, $month, $thisDay, $calids);
			echo "<td height=\"".$gcal->getCellHeight()."\" ";
			if (($thisDay == $today["mday"]) && ($month == $today["mon"])&&($year == $today["year"])) {
				echo "class=\"Today\"";
			}
			echo ">";
			if($gcal->getPrintDayLink() || count($myItems) > 0)
			echo "<a class=\"DayNum\" href=\"". $thisLink."\">".$thisDay."</a>";
			else
			echo "<p class=\"DayNum\">".$thisDay."</p>";
			if ($myItems) {
				foreach($myItems as $item) {
					$this->printEvent("month", $item);
				}
			}
			echo "</td>\n";
			if (($i + $daysOffset) % 7 == 6) {
				echo "</tr>";
			}
		}
		for ($i=$lastDay + $daysOffset;$i<($numRows * 7);$i++) {
			echo "<td class=\"EmptyCell\" height=\"".$gcal->getCellHeight()."\" ></td>";
			if($i==($numRows * 7-1))echo "</tr>\n";
		}
		echo "</table>";
	}

	function getDayLayout($year, $month, $day) {
		// A layout is a 2D array representation of a day's events, ready
		// to be written to HTML. The array contains zero or more columns
		// of timed events, followed by zero or one column of all-day
		// events. In either case, zero events means zero columns. In the
		// case of timed events, the number of columns depends on the
		// number of overlapping events. If no events overlap there will
		// be one column. For each event that overlaps events in other
		// columns, a new column is created. These columns translate
		// directly to screen.

		$items = $this->itemsForDate($year, $month, $day);
		if (!$items || !count($items)) return NULL;
		// from here on out we can assume that $items contains at least one item

		$columnCount = 1;
		$result = NULL;
		$openItems = array();
		$openItems[0] = array();
		$untimedItems = array();

		foreach($items as $item) {
			if ($item->get_day_type() == $item->SINGLE_WHOLE_DAY
			|| $item->get_day_type() == $item->MULTIPLE_WHOLE_DAY
			|| $item->get_day_type() == $item->MULTIPLE_PART_DAY) {
				$untimedItems[] = $item;
			}
			else {
				$itemStart = $item->get_start_date();
				// remove closed items, and determine the lowest column
				// with no overlap

				// $lowestColumn is 1-indexed.
				$lowestColumn = 0;
				for ($i=$columnCount-1;$i>=0;$i--) {
					$overlap = false;
					foreach ($openItems[$i] as $thisKey => $thisOpenItem) {
						if ($thisOpenItem->get_end_date() <= $itemStart) {
							unset($openItems[$i][$thisKey]);
						}
						else {
							$overlap = true;
						}
					}
					if (!$overlap) $lowestColumn = $i + 1;
				}

				if ($lowestColumn) {
					// an existing column has room for this item
					$openItems[$lowestColumn-1][] = $item;
					$result[$lowestColumn-1][] = $item;
				}
				else {
					// we need a new column
					$openItems[$columnCount][] = $item;
					$result[$columnCount][] = $item;
					$columnCount++;
				}
			}
		}
		$result[] = $untimedItems;

		return $result;

	}

	function printUntimedEventsForDay($layout, $view) {
		if ($layout) {
			$classString = ($view == "week") ? "Week" : "Day";
			$items = array_pop($layout);

			if ($items && count($items)) {
				for ($i=0;$i<count($items);$i++) {
					echo "<div style=\"margin-bottom: 5px;\">\n";
					$this->printEvent($view, $items[$i]);
					echo "</div>\n";
				}
			}
		}
	}

	function printTimedEventsForDay($layout, $view, $initialMinute) {
		if ($layout && (count($layout) > 1)) {
			// remove untimed? events from layout
			array_pop($layout);

			$whichCol = 0;
			$colWidth = (int)floor(100 / count($layout));
			foreach($layout as $col) {
				echo "<div class=\"Col\" style=\"width:".($colWidth-0.5)."%; left: ".($colWidth * $whichCol)."%\">\n";
				$currentOffset = $initialMinute;
				foreach($col as $item) {
					$myStart = $item->get_start_date();
					$myEnd = $item->get_end_date();

					// [TO DO] Do we need a more robust way to do this? Timed events are generally considered
					// to be confined to one day right now.

					// The fix here for midnight helps users who set their
					// end times to midnight, and in addition works with an iCal bug that sets a midnight end time
					// to the wrong day.
					if (!((int)strftime("%H%M", $myEnd))) {
						// End date is midnight.

						// iCal handles this wrong and sets the end date prior to the
						// start date. Fix this.

						// Now decrement end date just slightly so it's on the same day as start
						$myEnd--;
					}

					// $myStart and $myEnd are UNIX timestamps representing the start and
					// end time of the event

					// Get the number of minutes (past midnight) of the start and end times
					$myStartOffset = ((int)strftime("%H", $myStart) * 60) + (int)strftime("%M", $myStart);
					$myEndOffset = ((int)strftime("%H", $myEnd) * 60) + (int)strftime("%M", $myEnd);

					// Get the duration in minutes
					$myDuration = $myEndOffset - $myStartOffset;

					// Subtract initial minute from start to get actual offset
					$myStartOffset = $myStartOffset - $initialMinute;

					// Now, convert minute values to pixels.
					$myStartOffset = $myStartOffset * 64 / 60;
					$myDuration = $myDuration * 64 / 60;
					$this->printEvent($view, $item, $myDuration, $myStartOffset);
				}
				$whichCol++;
				echo "</div>\n";
			}
		}
	}

	function printDay($year, $month, $day) {
		$dayLayout = $this->getDayLayout($year, $month, $day);

		// get start time for the first event
		if (count($dayLayout) > 1) {
			$firstStart = $dayLayout[0][0]->get_start_date();
			$initialMinuteOffset = (int)strftime("%H", $firstStart) * 60;
		}
		else {
			$initialMinuteOffset = 540; // 9am
		}

		// prepare to print hour marks
		$firstHour = (int)($initialMinuteOffset / 60);

		// get end time for the last event
		$lastEnd = 0;
		for ($i=0;$i<count($dayLayout)-1;$i++) {
			if (count($dayLayout[$i])) {
				$thisEnd = $dayLayout[$i][count($dayLayout[$i])-1]->get_end_date();
				if ($thisEnd > $lastEnd) $lastEnd = $thisEnd;
			}
		}
		if ($lastEnd == 0) {
			$lastHour = 17;
		}
		else {
			$lastHour = (int)strftime("%H", $lastEnd) + 1;
		}
		echo "<div class=\"gcalendarcal CalDay\">\n";
		?>
<div class="UntimedEvents"><?php $this->printUntimedEventsForDay($dayLayout, "day"); ?>
</div>
<table class="TimedArea" cellspacing="0" cellpadding="0">
	<tr>
		<td class="DayAxis"><?php $this->printDayAxis($firstHour, $lastHour); ?>
		</td>
		<td class="TimedEvents"><!--[if IE]> &nbsp; <![endif]-->
		<div class="Inner"><?php $this->printTimedEventsForDay($dayLayout, "day", $initialMinuteOffset); ?>
		</div>
		</td>
	</tr>
	<?php
	echo "</table>\n";
	echo "</div>\n";
	echo "<div class=\"Clr\"></div>\n";
	}

	function printDayAxis($startHr, $endHr) {
		for ($hour=$startHr; $hour<=$endHr; $hour++) {
			echo "<div>\n";
			echo str_pad($hour, 2, "0", STR_PAD_LEFT);
			echo ":00";
			echo "</div>\n";
		}
	}

	function printWeek($year, $month, $day) {
		global $Itemid;
		$gcal = $this->calendar;
		$today = getdate();
		$firstDisplayedDate = $gcal->getFirstDayOfWeek($year, $month, $day, $gcal->getWeekStart());

		$dayLayouts = array();
		$lastHour = 0;
		$firstHour = 24;
		for ($j=0;$j<7;$j++) {
			$thisDate = strtotime('+'.$j.' days', $firstDisplayedDate);
			$displayedDates[] = getdate($thisDate);
		}
		foreach ($displayedDates as $dInfo) {
			$thisLayout = $this->getDayLayout($dInfo["year"], $dInfo["mon"], $dInfo["mday"]);
			if (count($thisLayout) > 1) {
				for ($i=0;$i<count($thisLayout)-1;$i++) {
					if (count($thisLayout[$i])) {
						$thisEndHour = (int)strftime("%H", $thisLayout[$i][count($thisLayout[$i])-1]->get_end_date());
						if ($thisEndHour > $lastHour) $lastHour = $thisEndHour;

						$thisStartHour = (int)strftime("%H", $thisLayout[0][0]->get_start_date());
						if ($thisStartHour < $firstHour) $firstHour = $thisStartHour;
					}
				}
			}

			$dayLayouts[] = $thisLayout;
		}

		// get start time for the first event
		if ($firstHour == 24) $firstHour = 9;

		$initialMinuteOffset = $firstHour * 60;

		// get end time for the last event
		if (!$lastHour) $lastHour = 17;

		echo "<table class=\"gcalendarcal CalWeek\" cellspacing=\"0\" cellpadding=\"0\">\n";
		echo "<tr>\n";
		echo "<td class=\"Empty\"></td>\n";
		$totalSubCols = 0;
		$totalEmptyCols = 0;
		$dayIndex = 0;
		$subColCounts = array();
		foreach($dayLayouts as $layout) {
			if (count($layout)) {
				$thisSubCount = ((count($layout) > 2) ? count($layout)-1 : 1);
				$subColCounts[$dayIndex] = $thisSubCount;
				$totalSubCols += $thisSubCount;
			}
			else {
				$thisSubCount = 0;
				$subColCounts[$dayIndex] = 0;
				$totalEmptyCols++;
			}
			$dayIndex++;
		}
		$dayIndex = 0;
		$totalNonEmptyWidth = 100 - 4
		- ($totalEmptyCols * 8);
		foreach ($displayedDates as $dInfo) {
			if(!$gcal->isColumnInWeekViewEqual()){
				$myColWidth = ($subColCounts[$dayIndex] ? (int)floor($subColCounts[$dayIndex] / $totalSubCols * $totalNonEmptyWidth): 8);
			}
			else{
				//we make them equal 100/7 = 14
				$myColWidth = 14;
			}
			echo "<th style=\"width: ".$myColWidth."%\">";
			$thisLink = "index.php?option=com_gcalendar&view=gcalendar&gcalendarview=day&year=" .
			$dInfo["year"] .
							"&month=" . $dInfo["mon"] . 
							"&day=" . $dInfo["mday"].'&Itemid='.$Itemid;

			echo "<a href=\"" . JRoute::_($thisLink) . "\">";
			$startWeekDay = $gcal->getWeekStart()-1;
			$dateObject = JFactory::getDate();
			echo $dateObject->_dayToString(($dayIndex+$startWeekDay)%7,true);
			echo " ";
			echo $dInfo["mday"];
			echo "</a>";
			echo "</th>\n";
			$dayIndex++;
		}
		echo "</tr>\n";
		echo "<tr class=\"UntimedEvents\">\n";
		echo "<td class=\"Empty\"></td>\n";
		$dayIndex = 0;
		foreach ($dayLayouts as $thisLayout) {
			$dInfo = $displayedDates[$dayIndex];
			echo "<td ";
			if (($dInfo["mday"] == $today["mday"]) && ($month == $today["mon"])&&($year == $today["year"])) {
				echo "class=\"Today\"";
			}
			echo ">\n";
			$this->printUntimedEventsForDay($thisLayout, "week");
			echo "</td>\n";
			$dayIndex++;
		}
		echo "</tr>\n";
		echo "<tr class=\"TimedEvents\">\n";
		echo "<td class=\"DayAxis\">\n";
		$this->printDayAxis($firstHour, $lastHour);
		echo "</td>\n";
		$dayIndex = 0;
		foreach ($dayLayouts as $thisLayout) {
			$dInfo = $displayedDates[$dayIndex];
			echo "<td ";
			if (($dInfo["mday"] == $today["mday"]) && ($month == $today["mon"])&&($year == $today["year"])) {
				echo "class=\"Today\"";
			}
			echo ">\n";
			echo "<div class=\"Inner\">\n";
			$this->printTimedEventsForDay($thisLayout, "week", $initialMinuteOffset);
			echo "</div></td>\n";
			$dayIndex++;
		}
		echo "</tr></table>\n";
	}

	function printCal() {
		$gcal = $this->calendar;
		$year = (int)$gcal->year;
		$month = (int)$gcal->month;
		$day = (int)$gcal->day;
		$view = $gcal->view;

		switch($view) {
			case "month":
				$this->printMonth($year, $month, $day);
				break;
			case "day":
				$this->printDay($year, $month, $day);
				break;
			case "week":
				$this->printWeek($year, $month, $day);
				break;
		}
	}
}
?>