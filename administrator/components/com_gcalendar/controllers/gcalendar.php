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

/**
 * GCalendar Controller
 *
 */
class GCalendarsControllerGCalendar extends GCalendarsController
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
	}

	/**
	 * display the edit form
	 * @return void
	 */
	function edit()
	{
		JRequest::setVar( 'view', 'GCalendar' );
		JRequest::setVar( 'layout', 'form'  );
		JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}

	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	function save()
	{
		$model = $this->getModel('GCalendar');

		if ($model->store($post)) {
			$msg = JText::_( 'Calendar saved!' );
		} else {
			$msg = JText::_( 'Error saving calendar' );
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$link = 'index.php?option=com_gcalendar';
		$this->setRedirect($link, $msg);
	}

	/**
	 * remove record(s)
	 * @return void
	 */
	function remove()
	{
		$model = $this->getModel('GCalendar');
		if(!$model->delete()) {
			$msg = JText::_( 'Error: One or more calendars could not be deleted' );
		} else {
			$msg = JText::_( 'Calendar(s) deleted' );
		}

		$this->setRedirect( 'index.php?option=com_gcalendar', $msg );
	}

	/**
	 * cancel editing a record
	 * @return void
	 */
	function cancel()
	{
		$msg = JText::_( 'Operation cancelled' );
		$this->setRedirect( 'index.php?option=com_gcalendar', $msg );
	}
}
?>
