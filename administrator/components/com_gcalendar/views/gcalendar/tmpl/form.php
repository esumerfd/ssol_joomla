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

require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_gcalendar'.DS.'util.php');

$googleColors = array(
'A32929'
,'B1365F'
,'7A367A'
,'5229A3'
,'29527A'
,'2952A3'
,'1B887A'
,'28754E'
,'0D7813'
,'528800'
,'88880E'
,'AB8B00'
,'BE6D00'
,'B1440E'
,'865A5A'
,'705770'
,'4E5D6C'
,'5A6986'
,'4A716C'
,'6E6E41'
,'8D6F47');
$calendar = $this->gcalendar;
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="col100">
<fieldset class="adminform"><legend><?php echo JText::_( 'CALENDAR_DETAILS' ); ?></legend>

<table class="admintable" width="100%">
	<tr>
		<td width="100%" align="right" class="key"><label for="gcalendar"> <?php echo JText::_( 'CALENDAR_NAME' ); ?>:
		</label></td>
		<td><input class="text_area" type="text" name="name" id="name"
			size="100%" maxlength="250" value="<?php echo $calendar->name;?>" /></td>
	</tr>
	<tr>
		<td width="100%" align="right" class="key"><label for="gcalendar"> <?php echo JText::_( 'Calendar ID' ); ?>:
		</label></td>
		<td><input class="text_area" type="text" name="calendar_id"
			id="calendar_id" size="100%"
			value="<?php echo $calendar->calendar_id;?>" /></td>
	</tr>
	<tr>
		<td width="100%" align="right" class="key"><label for="gcalendar"> <?php echo JText::_( 'Magic Cookie' ); ?>:
		</label></td>
		<td><input class="text_area" type="text" name="magic_cookie"
			id="magic_cookie" size="100%"
			value="<?php echo $calendar->magic_cookie;?>" /></td>
	</tr>
	<tr>
		<td width="100%" align="right" class="key"><label for="gcalendar"> <?php echo JText::_( 'Color' ); ?>:
		</label></td>
		<td><input class="text_area" type="text" name="color" id="color" readonly
			size="100%" value="<?php echo $calendar->color;?>" style="background-color: <?php echo GCalendarUtil::getFadedColor($calendar->color);?>;" />
		<table>
			<tbody>
			<?php
			for ($i = 0; $i < count($googleColors); $i++) {
				if($i % 7 == 0)
				echo "<tr>\n";
				$c = $googleColors[$i];
				$cFaded = GCalendarUtil::getFadedColor($c);
				echo "<td onmouseover=\"this.style.cursor='pointer'\" onclick=\"document.getElementById('color').style.backgroundColor = '".$cFaded."';document.getElementById('color').value = '".$c."';\" style=\"background-color: ".$cFaded.";width: 20px;\"/><td>".$c."</td>\n";
				if($i % 7 == 6)
				echo "</tr>\n";
			}
			?>
			</tbody>
		</table>
		</td>
	</tr>
</table>
</fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="option" value="com_gcalendar" /> <input
	type="hidden" name="id" value="<?php echo $calendar->id; ?>" /> <input
	type="hidden" name="task" value="" /> <input type="hidden"
	name="controller" value="gcalendar" /></form>

<div align="center"><br>
<img src="components/com_gcalendar/images/gcalendar.gif" width="143"
	height="57"><br>
&copy;&nbsp;&nbsp;2009 <a href="http://gcalendar.laoneo.net"
	target="_blank">allon moritz</a></div>
