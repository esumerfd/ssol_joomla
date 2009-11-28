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

require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_gcalendar'.DS.'libraries'.DS.'rss-calendar'.DS.'defaultcalendar.php');
require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_gcalendar'.DS.'util.php');

class GCalendar extends DefaultCalendar{

	var $dateFormat = 'dd/mm/yy';

	function GCalendar($model){
		$this->DefaultCalendar($model);
	}

	function printToolBar(){
		global $Itemid;
		$year = (int)$this->year;
		$month = (int)$this->month;
		$day = (int)$this->day;
		$view = $this->view;

		$document =& JFactory::getDocument();
		$document->setTitle('GCalendar: '.$this->getViewTitle($year, $month, $day, $this->getWeekStart(), $view));

		$mainFilename = "index.php?option=com_gcalendar&view=gcalendar&Itemid=".$Itemid;
		switch($view) {
			case "month":
				$nextMonth = ($month == 12) ? 1 : $month+1;
				$prevMonth = ($month == 1) ? 12 : $month-1;
				$nextYear = ($month == 12) ? $year+1 : $year;
				$prevYear = ($month == 1) ? $year-1 : $year;
				$prevURL = $mainFilename . "&gcalendarview=month&year=".$prevYear."&month=".$prevMonth;
				$nextURL = $mainFilename . "&gcalendarview=month&year=".$nextYear."&month=".$nextMonth;
				break;
			case "week":
				list($nextYear, $nextMonth, $nextDay) = explode(",", date("Y,n,j", strtotime("+7 days", strtotime("".$year."-".$month."-".$day))));
				list($prevYear, $prevMonth, $prevDay) = explode(",", date("Y,n,j", strtotime("-7 days", strtotime("".$year."-".$month."-".$day))));

				$prevURL = $mainFilename . "&gcalendarview=week&year=".$prevYear."&month=".$prevMonth."&day=".$prevDay;
				$nextURL = $mainFilename . "&gcalendarview=week&year=".$nextYear."&month=".$nextMonth."&day=".$nextDay;

				break;
			case "day":
				list($nextYear, $nextMonth, $nextDay) = explode(",", date("Y,n,j", strtotime("+1 day", strtotime("".$year."-".$month."-".$day))));
				list($prevYear, $prevMonth, $prevDay) = explode(",", date("Y,n,j", strtotime("-1 day", strtotime("".$year."-".$month."-".$day))));

				$prevURL = $mainFilename . "&gcalendarview=day&year=".$prevYear."&month=".$prevMonth."&day=".$prevDay;
				$nextURL = $mainFilename . "&gcalendarview=day&year=".$nextYear."&month=".$nextMonth."&day=".$nextDay;

				break;
		}

		$calCode  = "function jumpToDate(d){\n";
		$calCode .= "if(d == null) d = new Date();\n";
		$calCode .= "window.location = '".html_entity_decode(JRoute::_($mainFilename."&gcalendarview=".$view))."&day='+d.getDate()+'&month='+(d.getMonth()+1)+'&year='+d.getFullYear();\n";
		$calCode .= "};\n";
		$document->addScriptDeclaration($calCode);

		GCalendarUtil::loadJQuery();
		$document->addScript('administrator/components/com_gcalendar/libraries/jquery/ui/ui.core.js');
		$document->addScript('administrator/components/com_gcalendar/libraries/jquery/ui/ui.datepicker.js');
		$document->addStyleSheet('administrator/components/com_gcalendar/libraries/jquery/themes/redmond/ui.all.css');

		$daysLong = "[";
		$daysShort = "[";
		$daysMin = "[";
		$monthsLong = "[";
		$monthsShort = "[";
		$dateObject = JFactory::getDate();
		for ($i=0; $i<7; $i++) {
			$daysLong .= "'".$dateObject->_dayToString($i, false)."'";
			$daysShort .= "'".$dateObject->_dayToString($i, true)."'";
			$daysMin .= "'".substr($dateObject->_dayToString($i, true), 0, 2)."'";
			if($i < 6){
				$daysLong .= ",";
				$daysShort .= ",";
				$daysMin .= ",";
			}
		}

		for ($i=1; $i<=12; $i++) {
			$monthsLong .= "'".$dateObject->_monthToString($i, false)."'";
			$monthsShort .= "'".$dateObject->_monthToString($i, true)."'";
			if($i < 12){
				$monthsLong .= ",";
				$monthsShort .= ",";
			}
		}
		$daysLong .= "]";
		$daysShort .= "]";
		$daysMin .= "]";
		$monthsLong .= "]";
		$monthsShort .= "]";

		$calCode .= "jQuery(document).ready(function(){\n";
		$calCode .= "document.getElementById('gcdate').value = jQuery.datepicker.formatDate('".$this->dateFormat."', new Date(".$year.", ".$month." - 1, ".$day."));\n";
		//			$calCode .= "jQuery(\"#gcdate\").datepicker({changeYear: true});\n";
		$calCode .= "jQuery(\"#gcdate\").datepicker({dateFormat: '".$this->dateFormat."'});\n";
		$calCode .= "jQuery(\"#gcdate\").datepicker('option', 'dayNames', ".$daysLong.");\n";
		$calCode .= "jQuery(\"#gcdate\").datepicker('option', 'dayNamesShort', ".$daysShort.");\n";
		$calCode .= "jQuery(\"#gcdate\").datepicker('option', 'dayNamesMin', ".$daysMin.");\n";
		$calCode .= "jQuery(\"#gcdate\").datepicker('option', 'monthNames', ".$monthsLong.");\n";
		$calCode .= "jQuery(\"#gcdate\").datepicker('option', 'monthNamesShort', ".$monthsShort.");\n";
		$calCode .= "});\n";
		$document->addScriptDeclaration($calCode);

		echo "<div id=\"calToolbar\">\n";
		echo "<table style=\"margin: 0pt auto;\"><tr>\n";
		echo " <td valign=\"middle\"><a class=\"Item\" href=\"".JRoute::_($prevURL)."\" title=\"".JText::_('TOOLBAR_PREVIOUS').$this->getTranslatedViewName()."\">\n";
		$this->image("btn-prev.gif", JText::_('TOOLBAR_PREVIOUS').$this->getTranslatedViewName(), "prevBtn_img");
		echo "</a></td>\n";
		echo " <td valign=\"middle\"><span class=\"ViewTitle\">\n";
		echo $this->getViewTitle($year, $month, $day, $this->getWeekStart(), $view);
		echo "</span></td>\n";
		echo " <td valign=\"middle\"><a class=\"Item\" href=\"".JRoute::_($nextURL)."\" title=\"".JText::_('TOOLBAR_NEXT').$this->getTranslatedViewName()."\">\n";
		$this->image("btn-next.gif", JText::_('TOOLBAR_NEXT').$this->getTranslatedViewName(), "nextBtn_img");
		echo "</a></td>\n";
		echo "<td width=\"20px\"/>\n";
		$today = getdate();
		echo " <td valign=\"middle\">\n";
		echo "<button onClick=\"jumpToDate(null)\" title=\"".JText::_('TOOLBAR_JUMP').JText::_('TOOLBAR_TODAY')."\">".JText::_('TOOLBAR_TODAY')."</button>\n";
		echo "</td>\n";
		echo " <td valign=\"middle\"><input class=\"Item\"	type=\"text\" name=\"gcdate\" id=\"gcdate\" \n";
		echo "size=\"10\" maxlength=\"10\" title=\"".JText::_('TOOLBAR_SELECT_DATE')."\" /></td>";
		echo " <td valign=\"middle\">\n";
		echo "<button onClick=\"jumpToDate(jQuery.datepicker.parseDate('".$this->dateFormat."', document.getElementById('gcdate').value))\" title=\"".JText::_('TOOLBAR_JUMP')."\">".JText::_('TOOLBAR_GO')."</button>\n";
		echo "</td>\n";
		echo "<td width=\"20px\"/>\n";

		echo " <td valign=\"middle\"><a href=\"".JRoute::_($mainFilename."&gcalendarview=day&year=".$year."&month=".$month."&day=".$day)."\">\n";
		$this->image("cal-day.gif", JText::_('TOOLBAR_JUMP').$this->getTranslatedViewName('day'), "calday_img");
		echo "</a></td>\n";

		echo " <td valign=\"middle\"><a href=\"".JRoute::_($mainFilename."&gcalendarview=week&year=".$year."&month=".$month."&day=".$day)."\">\n";
		$this->image("cal-week.gif", JText::_('TOOLBAR_JUMP').$this->getTranslatedViewName('week'), "calweek_img");
		echo "</a></td>\n";

		echo " <td valign=\"middle\"><a href=\"".JRoute::_($mainFilename."&gcalendarview=month&year=".$year."&month=".$month."&day=".$day)."\">\n";
		$this->image("cal-month.gif", JText::_('TOOLBAR_JUMP').$this->getTranslatedViewName('month'), "calmonth_img");
		echo "</a></td></tr></table></div>\n";
	}

	/**
	 * This is an internal helper method and should not be called from outside of the class
	 * otherwise you know what you do.
	 *
	 */
	function image($name, $alt = "[needs alt tag]", $id="") {
		list($width, $height, $d0, $d1) = getimagesize(JPATH_SITE.DS.'components'.DS.'com_gcalendar'.DS.'views'.DS.'gcalendar'.DS.'tmpl'.DS.'img'.DS . $name);
		echo "<img src=\"".JURI::base() . "components/com_gcalendar/views/gcalendar/tmpl/img/" . $name."\"";
		echo " id=\"". $id."\" width=\"". $width."\" height=\"".$height."\" alt=\"".$alt."\" title=\"".$alt."\" border=\"0\" />";
	}
}
?>