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

jimport('joomla.application.component.controller');

/**
 * GCalendar Component Controller
 *
 */
class GCalendarsController extends JController
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct(){
		parent::__construct();
	}


	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	function display()
	{
		parent::display();
	}

	function import(){
		if($this->isLoggedIn()){
			JRequest::setVar( 'view', 'import'  );
		}else{
			JRequest::setVar( 'nextTask', 'import'  );
			JRequest::setVar( 'view', 'login'  );
		}
		JRequest::setVar('hidemainmenu', 0);

		parent::display();
	}

	function isLoggedIn(){
		global $_SESSION, $_GET;
		if (!isset($_SESSION['sessionToken']) && !isset($_GET['token'])) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

}
?>
