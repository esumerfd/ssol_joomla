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

jimport('joomla.application.component.model');

/**
 * GCalendar Model
 *
 */
class GCalendarsModelGCalendar extends JModel
{
	/**
	 * Constructor that retrieves the ID from the request
	 *
	 * @access	public
	 * @return	void
	 */
	function __construct()
	{
		parent::__construct();

		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
	}

	/**
	 * Method to set the calendar identifier
	 *
	 * @access	public
	 * @param	int Calendar identifier
	 * @return	void
	 */
	function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}


	/**
	 * Method to get a calendar
	 * @return object with data
	 */
	function getData()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = " SELECT * FROM #__gcalendar WHERE id = ".$this->_id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		if (!$this->_data) {
			$this->_data = new stdClass();
			$this->_data->id = 0;
			$this->_data->name = null;
			$this->_data->calendar_id = null;
			$this->_data->magic_cookie = null;
			$this->_data->color = 'A32929';
		}
		return $this->_data;
	}

	/**
	 * Method to store a record
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function store()	{
		$row =& $this->getTable();

		$data = JRequest::get( 'post' );

		// Bind the form fields to the calendar table
		if (!$row->bind($data)) {
			JError::raiseWarning( 500, $row->getError() );
			return false;
		}

		// Make sure the calendar record is valid
		if (!$row->check()) {
			JError::raiseWarning( 500, $row->getError() );
			return false;
		}

		if(strpos($row->color, '#') === 0)
		$row->color = str_replace("#","",$row->color);

		if(strpos($row->calendar_id, '@'))
		$row->calendar_id = str_replace("@","%40",$row->calendar_id);

		// Store the calendar table to the database
		if (!$row->store()) {
			JError::raiseWarning( 500, $row->getError() );
			return false;
		}

		return true;
	}

	/**
	 * Method to delete record(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function delete()
	{
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		$row =& $this->getTable();

		if (count( $cids ))		{
			foreach($cids as $cid) {
				if (!$row->delete( $cid )) {
					JError::raiseWarning( 500, $row->getError() );
					return false;
				}
			}
		}
		return true;
	}
		

}
?>
