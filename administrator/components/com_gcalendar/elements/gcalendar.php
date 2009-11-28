<?php
/**
 * @version		$Id: newsfeed.php 10381 2008-06-01 03:35:53Z pasamio $
 * @package		Joomla
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 *
 * @package 	GCalendar
 * @subpackage	Parameter
 * @since		1.5
 */

class JElementGCalendar extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var	$_name = 'GCalendar';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$db = &JFactory::getDBO();

		$section	= $node->attributes('section');
		$class		= $node->attributes('class');
		if (!$class) {
			$class = "inputbox";
		}

		if (!isset ($section)) {
			// alias for section
			$section = $node->attributes('scope');
			if (!isset ($section)) {
				$section = 'content';
			}
		}

		$query = 'SELECT id, name, calendar_id FROM #__gcalendar';
		$db->setQuery($query);
		$options = $db->loadObjectList();
		$result = '<select name="'.$control_name.'['.$name.'][]" id="'.$name.'" class="'.$class.'" multiple="multiple">';
		
		foreach( $options as $option ) {
			$display_name = $option->name;
			if(is_array( $value) ) {
				if( in_array( $option->id, $value ) ) {
					$result .= '<option selected="true" value="'.$option->id.'" >'.$display_name.'</option>';
				} else {
					$result .= '<option value="'.$option->id.'" >'.$display_name.'</option>';
				}
			} elseif ( $value ) {
				if( $value == $option->id ) {
					$result .= '<option selected="true" value="'.$option->id.'" >'.$display_name.'</option>';
				} else {
					$result .= '<option value="'.$option->id.'" >'.$display_name.'</option>';
				}
			} elseif ( !( $value ) ) {
				$result .= '<option value="'.$option->id.'" >'.$display_name.'</option>';
			}
		}
		$result .= '</select>';
		return $result;
		
	}
}
