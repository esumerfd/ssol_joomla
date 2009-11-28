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

defined('_JEXEC') or die('Restricted access');

require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_gcalendar'.DS.'libraries'.DS.'rss-calendar'.DS.'defaultcalendar.php');

class DayCalendar extends DefaultCalendar{
	
	function DayCalendar($model){
		$this->DefaultCalendar($model);
	}
	
	function printToolBar(){
		$document =& JFactory::getDocument();
		$document->setTitle('GCalendar: '.$this->getViewTitle($this->year, $this->month, $this->day, $this->getWeekStart(), $this->view));
		echo "<div style=\"text-align:center;\"><b>".$this->getViewTitle($this->year, $this->month, $this->day, $this->getWeekStart(), $this->view)."</b></div>\n";
	}
}
?>