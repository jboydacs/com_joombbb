<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT_ADMINISTRATOR."/helpers/bbb.php";

class BbbModelBbbLoadIframe extends JModelLegacy
{
	//Last Stop display the list of bbb events based on the post parameters passed, e.g: Today, Categories
	public function getList(){
		$input = JFactory::getApplication()->input;  	
		$id = $input->get('id');
		$categoryId = $input->get('categoryid');
		$today = $input->get('today');
		$alllessons = $input->get('alllessons');
		$user = JFactory::getUser();
		
		/* $php_date = getdate($timestamp);
		// or if you want to output a date in year/month/day format:
		$date = date("Y/m/d", $timestamp); // see the date manual page for format options */

		$db = JFactory::getDbo();
		$dataArr = array();
		
		$query = $db->getQuery(true)
				->select('id, meeting_id, cat_id, type_id, meetingName, meeting_description, voiceBridge, maxParticipants, record, event_moderators, allowed_users, allowed_groups, display_time, start_time, end_time, all_moderator, event_isopen, duration, created')
				->from($db->quoteName('#__bigbluebutton_meetings'));
		$db->setQuery($query);
		$bbbdata = $db->loadObjectList();
		
		if($categoryId){
			$query = $db->getQuery(true)
					->select('id, meeting_id, cat_id, meetingName, meeting_description, voiceBridge, maxParticipants, record, event_moderators, allowed_users, allowed_groups, display_time, start_time, end_time, all_moderator, event_isopen, duration, created')
					->from($db->quoteName('#__bigbluebutton_meetings'))
					->where('cat_id = '.(int)$categoryId);
			$db->setQuery($query);
			$bbbdata = $db->loadObjectList();
		} else if($today){
			foreach($bbbdata as $data){
				$eventDateTs = $data->display_time;
				$datem = date("m", $eventDateTs/1000);
				$dated = date("d", $eventDateTs/1000);
				$datey = date("Y", $eventDateTs/1000);
				$todayString = $datem.$dated.$datey;
				if($today == $todayString){
					$dataArr[] = $data;
				}
			}
			$bbbdata = $dataArr;
		} else if($alllessons){
			$bbbdata = $bbbdata;
		}
		
		return $bbbdata;
	}
	
	public function getCategories(){
		$db = JFactory::getDBO();
		$input = JFactory::getApplication()->input;  	
		$user = JFactory::getUser();
		
		$query = $db->getQuery(true);
		$query->select('id, title');
		$query->from('#__bigbluebutton_category');
		$db->setQuery($query);
		$categoriess = $db->loadObjectList();
		
		return $categoriess;
	}
	
}