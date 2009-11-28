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

class JElementTextarea2 extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var	$_name = 'JElementTextarea2';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$rows = $node->attributes('rows');
		$cols = $node->attributes('cols');
		$class = ( $node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="text_area"' );

		$content = $value;
		$desc = '';
		foreach ($node->children() as $option)
		{
			if($option->name() == 'content' && empty($value))
			$content	= $option->data();
			if($option->name() == 'description')
			$desc	= $option->data();
		}

		$output = '<textarea name="'.$control_name.'['.$name.']" cols="'.$cols.'" rows="'.$rows.'" '.$class.' id="'.$control_name.$name.'" >'.$content.'</textarea>';
		if(!empty($desc))
		$output = $output.'<div>'.$desc.'</div>';

		return $output;
	}
}
