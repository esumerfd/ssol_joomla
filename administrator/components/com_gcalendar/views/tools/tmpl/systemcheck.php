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
?>

<table class="adminlist">
	<thead>
		<tr>
			<th>Status</th>
			<th width="40">Name</th>
			<th>Description</th>
			<th>Solution</th>
		</tr>
	</thead>
	<?php
	$data = array();
	$data[] = checkRemoteConnection();
	$data[] = checkPhpVersion();
	$data[] = checkCacheForGCalendarView();
	$tmp = checkDB();
	$data = array_merge($data, $tmp);
	foreach ($data as $test) {
		echo "<tr>\n";
		$img = "components/com_gcalendar/views/tools/tmpl/ok.png";
		if($test['status']=="ok")
		$img = "components/com_gcalendar/views/tools/tmpl/failure.png";
		echo "<td width=\"17\" align=\"center\"><img src=\"".$img."\" width=\"16\" height=\"16\"/></td>\n";
		echo "<td width=\"120\">".$test['name']."</td><td>".$test['description']."</td><td>".$test['solution']."</td>";
		echo "</tr>\n";
	}
	?>
</table>

	<?php
	function checkDB() {
		$tmp = array();
		$db =& JFactory::getDBO();
		$query = "SELECT id, calendar_id, name, color, magic_cookie  FROM #__gcalendar";
		$db->setQuery( $query );
		$results = $db->loadObjectList();
		if(empty($results)){
			$tmp[] = array('name'=>'DB Entries Check', 'description'=>'No DB data found.', 'status'=>'ok', 'solution'=>'');
		}else{
			foreach ($results as $result) {
				$feed = new SimplePie_GCalendar();
				$feed->set_show_past_events(FALSE);
				$feed->set_sort_ascending(TRUE);
				$feed->set_orderby_by_start_date(TRUE);
				$feed->set_expand_single_events(TRUE);
				$feed->enable_order_by_date(FALSE);
				$feed->enable_cache(FALSE);
				$feed->set_cal_language(GCalendarUtil::getFrLanguage());
				$feed->set_timezone(GCalendarUtil::getComponentParameter('timezone'));

				$url = SimplePie_GCalendar::create_feed_url($result->calendar_id, $result->magic_cookie);
				$feed->set_feed_url($url);
				$feed->init();
				$feed->handle_content_type();

				if ($feed->error()){
					$desc = "The following Simplepie error occurred when reading calendar ".$result->name.":<br>".$feed->error();
					$solution = "<ul><li>If the error is the same as in the connection test use the solution described there.</li>";
					$solution .= "<li>Please check your shared settings of the calendar and the events, ";
					$solution .= "if you do not share your calendar with the public the <a href=\"http://code.google.com/apis/calendar/docs/2.0/developers_guide_protocol.html#AuthMagicCookie\">magic cookie</a> field must be set.</li>";
					$solution .= "<li>Run the <a href=\"components/com_gcalendar/libraries/sp-gcalendar/sp_compatibility_test.php\">simplepie compatibility test</a> and check if your system does meet the minimum requirements of simplepie.</li>";
					$solution .= "<li><b>If the problem still exists check the forum at <a href=\"http://gcalendar.laoneo.net\">gcalendar.laoneo.net</a>.</b></li>";
					$status = 'failure';
				}else{
					$solution = '';
					$status = 'ok';
					$desc = 'Simplepie could read the events without any problems from calendar '.$result->name.'.';
				}
				$tmp[] = array('name'=>$result->name.' Check', 'description'=>$desc, 'status'=>$status, 'solution'=>$solution);
			}
		}
		return $tmp;
	}


	function checkRemoteConnection(){
		$desc = '';
		$solution = '';
		$status = 'ok';
		if (function_exists('curl_exec')){
			$ch=curl_init();
			curl_setopt ($ch, CURLOPT_URL,'www.google.com');
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch,CURLOPT_VERBOSE,false);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			$page=curl_exec($ch);
			// $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if(curl_errno($ch)){
				$desc = 'Curl could not retrieve remote content from www.google.com. The following error occured:'.curl_error($ch);
				$solution = 'Please contact your web hoster and check if their firewall blocks curl http calls to google.com.';
			}else{
				$desc = 'Curl could sucessfully retrieve remote content from www.google.com.';
			}
			curl_close($ch);
		}else{
			$fp = fsockopen("www.google.com", 80, $errno, $errstr, 5);
			if (!$fp) {
				$desc = 'A connection to www.google.com could not be established. The following error occured:'.$errstr.' ('.$errno.')';
				$solution = 'Please contact your web hoster and check if their firewall blocks http calls to google.com.';
				$status = 'failure';
			} else {
				$desc = 'A connection to www.google.com could successfully be established.';
			}
		}
		return array('name'=>'Google Connection Check', 'description'=>$desc, 'status'=>$status, 'solution'=>$solution);
	}

	function checkPhpVersion(){
		$desc = "Your PHP version is ".phpversion().". This is enough to run imports and all other tasks.";
		$status = 'ok';
		$solution = '';
		if(phpversion() < '5.1.4') {
			$desc = "Your PHP version is ".phpversion().". This is not enough to run imports but the rest should work properly.";
			$status = 'failure';
			$solution = 'Contact your web hoster and check if it possible to upgrade your php version to 5.1.4.';
		}
		return array('name'=>'PHP Version Check', 'description'=>$desc, 'status'=>$status, 'solution'=>$solution);
	}

	function checkCacheForGCalendarView() {
		$cacheDir =  JPATH_BASE.DS.'cache'.DS.'com_gcalendar';
		$desc = "The directory ".$cacheDir." which is used by the GCalendar view as cache directory is writable, this means you can enable caching in the GCalendar view.";
		$status = 'ok';
		$solution = '';
		JFolder::create($cacheDir, 0755);
		if ( !is_writable( $cacheDir ) ) {
			$desc = "The directory ".$cacheDir." which is used by the GCalendar view as cache directory is not writable, this means you can't enable caching in the GCalendar view.";
			$status = 'failure';
			$solution = 'Set manually the write permission for the folder '.$cacheDir.' to writable.';
		}
		return array('name'=>'GCalendar View Cache Dir Check', 'description'=>$desc, 'status'=>$status, 'solution'=>$solution);
	}
	?>
