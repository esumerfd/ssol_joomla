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

require_once ('daycalendar.php');

$model = &$this->getModel();
$gcids = $model->getState('gcids');

$itemID = null;
if(!empty($gcids)){
	$itemID = GCalendarUtil::getItemId($gcids[0]);
	foreach ($gcids as $cal) {
		$id = GCalendarUtil::getItemId($cal);
		if($id != $itemID){
			$itemID = null;
			break;
		}
	}
	if($itemID !=null){
		$component	= &JComponentHelper::getComponent('com_gcalendar');
		$menu = &JSite::getMenu();
		$item = $menu->getItem($itemID);
		$backLinkView = $item->query['view'];
		echo "<table><tr><td valign=\"middle\">\n";
		echo '<a href="'.JRoute::_('index.php?option=com_gcalendar&view='.$backLinkView.'&Itemid='.$itemID)."\">\n";
		echo "<img id=\"prevBtn_img\" height=\"16\" border=\"0\" width=\"16\" alt=\"backlink\" src=\"components/com_gcalendar/hiddenviews/day/tmpl/back.png\"/>\n";
		echo "</a></td><td valign=\"middle\">\n";
		echo '<a href="'.JRoute::_('index.php?option=com_gcalendar&view='.$backLinkView.'&Itemid='.$itemID).'">'.JText::_( 'CALENDAR_BACK_LINK' )."</a>\n";
		echo "</td></tr></table>\n";
	}
}


echo "<div class=\"gcalendarDaySingleView\">\n";

$calendar = new DayCalendar($model);
$calendar->weekStart = 1;
$calendar->showSelectionList = false;
$calendar->showToolbar = false;
$calendar->forceView ='day';
$calendar->display();

echo "</div>\n";
echo "<div style=\"text-align:center;margin-top:10px\" id=\"gcalendar_powered\"><a href=\"http://gcalendar.laoneo.net\">Powered by GCalendar</a></div>\n";
?>