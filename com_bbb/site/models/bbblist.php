<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT_ADMINISTRATOR."/helpers/bbb.php";

class BbbModelBbbList extends JModelLegacy
{
	//Last Stop display the list of bbb events based on the post parameters passed, e.g: Today, Categories
	public function getList(){
		$input = JFactory::getApplication()->input;  	
		$id = $input->get('id');
		$categoryId = $input->get('categoryid');
		$today = $input->get('today');
		$alllessons = $input->get('alllessons');
		$user = JFactory::getUser();
		$paidId = 13;
		/* $php_date = getdate($timestamp);
		// or if you want to output a date in year/month/day format:
		$date = date("Y/m/d", $timestamp); // see the date manual page for format options */

		$db = JFactory::getDbo();
		$dataArr = array();
		
		$query = $db->getQuery(true)
				->select('id, access, meeting_id, cat_id, type_id, meetingName, meeting_description, voiceBridge, maxParticipants, record, event_moderators, allowed_users, allowed_groups, display_time, start_time, end_time, all_moderator, event_isopen, duration, created')
				->from($db->quoteName('#__bigbluebutton_meetings'));
		$query->order('display_time ASC');
		$db->setQuery($query);
		$bbbdataAll = $db->loadObjectList();
		
		$query = $db->getQuery(true)
			->select('id, group_id, group_name')
			->from($db->quoteName('#__bigbluebutton_excludedgroups'));
		$db->setQuery($query);
		$excludedGroups = $db->loadObjectList();
		
		$exGroupArr = array();
		foreach($excludedGroups as $exGroup){
			$exGroupArr[$exGroup->group_id] = $exGroup->group_id;
		}
		
		$userInExcludedGroup = false;
		
		//if there are no groups assigned
		foreach($excludedGroups as $exGroup){
			if(in_array($exGroup->group_id, JAccess::getGroupsByUser($user->id))){
				$userInExcludedGroup = true;break;
			}
		}
		
		foreach($bbbdataAll as $data){
			$eventSkip = 0;	
			$eventModeratorsArr = json_decode($data->event_moderators);
			$allowedGroupsArr = json_decode($data->allowed_groups);
			$allowedUsersArr = json_decode($data->allowed_users);
			
			$countEventModeratorsArr = count(array_filter($eventModeratorsArr));
			$countAllowedGroupsArr = count(array_filter($allowedGroupsArr));
			$countAllowedUsersArr = count(array_filter($allowedUsersArr));
			
			$groups = $user->get('groups');
			
			$userAllowed = false;
			$userIsTeacher = false;
			
			$eventGroupIsInExGroup = false;
			
			//if there are groups assigned
			if( ($countAllowedUsersArr > 0)){
				foreach($allowedUsersArr as $userId){
					if($user->id == $userId){
						$userAllowed = true;break;
					}
				}
			}
			
			//if there are one or more groups assigned
			if( ($countAllowedGroupsArr > 0)){
				foreach($allowedGroupsArr as $groupId){
				
					if(in_array($groupId, $exGroupArr)){
						$eventGroupIsInExGroup = true;
					}
				
					if(in_array($groupId, JAccess::getGroupsByUser($user->id))){
						$userAllowed = true;break;
					}
					
				}
			}
			
			//if there are one or more Moderators assigned
			if( ($countEventModeratorsArr > 0)){
				foreach($eventModeratorsArr as $teacherId){
					if($user->id == $teacherId) $userIsTeacher = true;break;
				}
			}
			
			//No Restrictions. neutralize
			if( (($countAllowedUsersArr == 0) && ($countAllowedGroupsArr == 0)) || ($userIsTeacher) ){
				$userAllowed = true;
			}
			
			//If Group is paid, display the lesson.
			if (in_array($paidId, $allowedGroupsArr)){
				$userAllowed = true;
			}
			
			$eventEndDateTs = $data->end_time;
			$eventEndDateSecs = $eventEndDateTs/1000;
			
			//If the user belongs to the excluded group, do not display public events, and if not a teacher.
			//Display event if the event only if the excluded group is assigned in the event.
			if( ($userInExcludedGroup) && ($eventGroupIsInExGroup!=true) && ($userIsTeacher!=true) ){
				continue;
			} else {
				
			}
			
			if((time() > $eventEndDateSecs) || !($userAllowed)) continue;
				
			$dataArr[] = $data;
			
		}
		$bbbdata = $dataArr;
		
		if($categoryId){
			$dataArr = array();
			$query = $db->getQuery(true)
					->select('id, access, meeting_id, cat_id, type_id, meetingName, meeting_description, voiceBridge, maxParticipants, record, event_moderators, allowed_users, allowed_groups, display_time, start_time, end_time, all_moderator, event_isopen, duration, created')
					->from($db->quoteName('#__bigbluebutton_meetings'))
					->where('cat_id = '.(int)$categoryId);
			$query->order('display_time ASC');
			$db->setQuery($query);
			$bbbdataWithCatId = $db->loadObjectList();
			
			$query = $db->getQuery(true)
				->select('id, group_id, group_name')
				->from($db->quoteName('#__bigbluebutton_excludedgroups'));
			$db->setQuery($query);
			$excludedGroups = $db->loadObjectList();
			
			$exGroupArr = array();
			foreach($excludedGroups as $exGroup){
				$exGroupArr[$exGroup->group_id] = $exGroup->group_id;
			}
			
			$userInExcludedGroup = false;
			
			//if there are no groups assigned
			foreach($excludedGroups as $exGroup){
				if(in_array($exGroup->group_id, JAccess::getGroupsByUser($user->id))){
					$userInExcludedGroup = true;break;
				}
			}
			
			foreach($bbbdataWithCatId as $data){
				$eventSkip = 0;	
				$eventModeratorsArr = json_decode($data->event_moderators);
				$allowedGroupsArr = json_decode($data->allowed_groups);
				$allowedUsersArr = json_decode($data->allowed_users);
				
				$countEventModeratorsArr = count(array_filter($eventModeratorsArr));
				$countAllowedGroupsArr = count(array_filter($allowedGroupsArr));
				$countAllowedUsersArr = count(array_filter($allowedUsersArr));
				
				$groups = $user->get('groups');
				
				$eventGroupIsInExGroup = false;
				
				$userAllowed = false;
				$userIsTeacher = false;
				//if there are no groups assigned
				if( ($countAllowedUsersArr > 0)){
					foreach($allowedUsersArr as $userId){
						if($user->id == $userId){
							$userAllowed = true;break;
						}
					}
				}
				
				//if there are no groups assigned
				if( ($countAllowedGroupsArr > 0)){
					foreach($allowedGroupsArr as $groupId){
					
						if(in_array($groupId, $exGroupArr)){
							$eventGroupIsInExGroup = true;
						}
					
						if(in_array($groupId, JAccess::getGroupsByUser($user->id))){
							$userAllowed = true;break;
						}
					}
				}
				
				//if there are no Moderators assigned
				if(($countEventModeratorsArr > 0)){
					foreach($eventModeratorsArr as $teacherId){
						if($user->id == $teacherId) $userIsTeacher = true;break;
					}
				}
				
				//No Restrictions. neutralize
				if( (($countAllowedUsersArr == 0) && ($countAllowedGroupsArr == 0)) || ($userIsTeacher) ){
					$userAllowed = true;
				}
			
				//If Group is paid, display the lesson.
				if (in_array($paidId, $allowedGroupsArr)){
					$userAllowed = true;
				}
			
				$eventEndDateTs = $data->end_time;
				$eventEndDateSecs = $eventEndDateTs/1000;
				
				//If the user belongs to the excluded group, do not display public events, and if not a teacher.
				//Display event if the event only if the excluded group is assigned in the event.
				if( ($userInExcludedGroup) && ($eventGroupIsInExGroup!=true) && ($userIsTeacher!=true) ){
					continue;
				} else {
					
				}
				
				if((time() > $eventEndDateSecs) || !($userAllowed)) continue;
					
				
				$dataArr[] = $data;
				
			}
			$bbbdata = $dataArr;
		} else if($today){
			$dataArr = array();
			foreach($bbbdata as $data){
				$eventEndDateTs = $data->end_time;
				$eventEndDateSecs = $eventEndDateTs/1000;
				if((time() > $eventEndDateSecs)) continue;
				
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
	
	//To load the bbb event inside the websie, it will be loaded in an ifrmae.
	//a bbb load iframe menu must be created. so it will be the menu/page where the iframe will be loaded.
	//This function will get that menu
	public function getBbbIframeMenu(){
		$db = JFactory::getDBO();
		$input = JFactory::getApplication()->input;  	
		$user = JFactory::getUser();
	
		$query = $db->getQuery(true);
		$query->select('id,menutype,title,alias,note,path,link,type,published,parent_id,component_id,template_style_id');
		$query->from('#__menu');
		$query->where('link = "index.php?option=com_bbb&view=bbbloadiframe"');
		$db->setQuery($query);
		$iframeMenu = $db->loadObjectList();
		
		return $iframeMenu;
	}

	/* public function isAllowedToEnterPaidLesson(){
		$isallowed = false;
		
		$db = JFactory::getDBO();
		$input = JFactory::getApplication()->input;  	
		$user = JFactory::getUser();
		
		
		
		return $isallowed;
	}	 */
}