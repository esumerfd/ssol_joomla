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
class GCalendarController extends JController
{
	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	function display()
	{
		if(JRequest::getVar('view', null)=='event'){
			$document =& JFactory::getDocument();

			$viewType	= $document->getType();
			$viewName	= JRequest::getCmd( 'view', 'Event' );
			$viewLayout	= JRequest::getCmd( 'layout', 'default' );
				
			$this->addViewPath($this->_basePath.DS.'hiddenviews');
			$view = & $this->getView( $viewName, $viewType, '', array( 'base_path'=>$this->_basePath));
			$view->addTemplatePath($this->_basePath.DS.'hiddenviews'.DS.strtolower($viewName).DS.'tmpl');
		}

		if(JRequest::getVar('view', null)=='day'){
			$document =& JFactory::getDocument();

			$viewType	= $document->getType();
			$viewName	= JRequest::getCmd( 'view', 'Day' );
			$viewLayout	= JRequest::getCmd( 'layout', 'default' );
				
			$this->addViewPath($this->_basePath.DS.'hiddenviews');
			$view = & $this->getView( $viewName, $viewType, '', array( 'base_path'=>$this->_basePath));
			$view->addTemplatePath($this->_basePath.DS.'hiddenviews'.DS.strtolower($viewName).DS.'tmpl');
		}
		parent::display();
	}
}
?>
