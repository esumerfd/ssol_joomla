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

require_once ('calendar/gcalendar.php');

$params = $this->params;
echo "<div class=\"contentpane".$params->get('pageclass_sfx')."\">\n";

echo $params->get( 'textbefore' );

$model = &$this->getModel();
$calendar = new GCalendar($model);
$calendar->weekStart = $params->get('weekstart');
$calendar->showSelectionList = $params->get('show_selection') == 'yes';
$calendar->dateFormat = $params->get('dateformat');
$calendar->columnInWeekViewEqual = $params->get('columnInWeekViewEqual') == 'yes';
$calendar->defaultView = $params->get('defaultView');
$calendar->display();

echo $params->get( 'textafter' );
echo "</div>\n";
echo "<div style=\"text-align:center;margin-top:10px\" id=\"gcalendar_powered\"><a href=\"http://gcalendar.laoneo.net\">Powered by GCalendar</a></div>\n";
?>
