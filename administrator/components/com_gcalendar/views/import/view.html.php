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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

/**
 * GCalendars View
 *
 */
class GCalendarsViewImport extends JView
{
	/**
	 * GCalendars view display method
	 * @return void
	 **/
	function display($tpl = null)
	{
		JToolBarHelper::title(   JText::_( 'GCALENDAR_MANAGER' ),  'calendar');
		JToolBarHelper::custom('add', 'new.png', 'new.png', 'add', false);
		JToolBarHelper::cancel();

		$items = $this->get( 'OnlineData');
		$this->assignRef('online_items', $items);
		$dbitems = $this->get( 'DBData');
		$this->assignRef('db_items', $dbitems);
		parent::display($tpl);
	}
}
