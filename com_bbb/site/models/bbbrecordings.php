<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT_ADMINISTRATOR."/helpers/bbb.php";

class BbbModelBbbRecordings extends JModelLegacy
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
		$query->order('display_time ASC');
		$db->setQuery($query);
		$bbbdataAll = $db->loadObjectList();
		
		/* foreach($bbbdataAll as $data){
			$eventDateTs = $data->display_time;
			$displayTimeSecs = $eventDateTs/1000;
			if(time() > $displayTimeSecs){
				continue;
			} else {
				$dataArr[] = $data;
			}
		} */
		$bbbdata = $bbbdataAll;
		
		if($categoryId){
			$dataArr = array();
			$query = $db->getQuery(true)
					->select('id, meeting_id, cat_id, meetingName, meeting_description, voiceBridge, maxParticipants, record, event_moderators, allowed_users, allowed_groups, display_time, start_time, end_time, all_moderator, event_isopen, duration, created')
					->from($db->quoteName('#__bigbluebutton_meetings'))
					->where('cat_id = '.(int)$categoryId);
			$query->order('display_time ASC');
			$db->setQuery($query);
			$bbbdataWithCatId = $db->loadObjectList();
			
			/* foreach($bbbdataWithCatId as $data){
				$eventDateTs = $data->display_time;
				$displayTimeSecs = $eventDateTs/1000;
				if(time() > $displayTimeSecs){
					continue;
				} else {
					$dataArr[] = $data;
				}
			} */
			$bbbdata = $bbbdataWithCatId;
		} else if($today){
			$dataArr = array();
			foreach($bbbdata as $data){
				/* $eventDateTs = $data->display_time;
				$displayTimeSecs = $eventDateTs/1000;
				if(time() > $displayTimeSecs){
					continue;
				} */
				
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
	
	//Get Recordings from the database
	public function getBbbDbRecordings($meetingId, $categoryId){
		$db = JFactory::getDBO();
		$input = JFactory::getApplication()->input;  	
		$user = JFactory::getUser();
		$bbbRecordingsTemp = array();
		$bbbRecordingsArr = array();
		$bbb = new BBBHelper();
		$meetingId = ($meetingId)?$meetingId:0;
		$bbbRecordingsTemp = $bbb->getBbbRecordings($meetingId);
		
		
		
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
		
		
		$meetingIds = array();
		foreach($bbbRecordingsTemp as $bbbRecording){
			if($bbbRecording["recordId"]){
				$mid = $bbbRecording["meetingId"];
				$query = $db->getQuery(true)
					->select('id, meeting_id, cat_id, meetingName, meeting_description, voiceBridge, maxParticipants, record, event_moderators, allowed_users, allowed_groups, display_time, start_time, end_time, all_moderator, event_isopen, duration, created')
					->from($db->quoteName('#__bigbluebutton_meetings'))
					->where('meeting_id = '.$db->quote($mid)); 
				$query->order('display_time ASC');
				$db->setQuery($query);
				$meetingD = $db->loadObject();
				
				$eventSkip = 0;
				
				$eventModeratorsArr = json_decode($meetingD->event_moderators);
				$allowedGroupsArr = json_decode($meetingD->allowed_groups);
				$allowedUsersArr = json_decode($meetingD->allowed_users);
				
				$countEventModeratorsArr = count(array_filter($eventModeratorsArr));
				$countAllowedGroupsArr = count(array_filter($allowedGroupsArr));
				$countAllowedUsersArr = count(array_filter($allowedUsersArr));
				
				$groups = $user->get('groups');
				
				$userAllowed = false;
				$userIsTeacher = false;
				
				$eventGroupIsInExGroup = false;
				
				//if there are no groups assigned
				if( ($countAllowedUsersArr > 0)){
					foreach($allowedUsersArr as $userId){
						if($user->id == $userId){
							$userAllowed = true;break;
						}
					}
				}
				
				//if there are no groups assigned
				if(($countAllowedGroupsArr > 0)){
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
				if( ($countEventModeratorsArr > 0)){
					foreach($eventModeratorsArr as $teacherId){
						if($user->id == $teacherId) $userIsTeacher = true;break;
					}
				}
				
				//No Restrictions. neutralize
				if( (($countAllowedUsersArr == 0) && ($countAllowedGroupsArr == 0)) || ($userIsTeacher) ){
					$userAllowed = true;
				}
				
				//If a category is selected, filter by category
				if($categoryId){
					if($categoryId != $meetingD->cat_id) $eventSkip = 1;
				}
				
				//If the user belongs to the excluded group, do not display public events, and if not a teacher.
				//Display event if the event only if the excluded group is assigned in the event.
				if( ($userInExcludedGroup) && ($eventGroupIsInExGroup!=true) && ($userIsTeacher!=true) ){
					continue;
				} else {
					
				}
				
				if( ($eventSkip) || !($userAllowed) ) continue;
				
				$bbbRecording["meetingid"] = $meetingD->meeting_id;
				$bbbRecording["meetingdbid"] = $meetingD->id;
				$bbbRecording["categoryid"] = $meetingD->cat_id;
				$bbbRecording["title"] = $meetingD->meetingName;
				$bbbRecording["description"] = $meetingD->meeting_description;
				$bbbRecording["display_time"] = $meetingD->display_time;
				
				$bbbRecordingsArr[] = $bbbRecording;
			}
		}
		
		//Sort By Date
		if($bbbRecordingsArr){
			$recordingsCount = count($bbbRecordingsArr)-1;
			for($counter = $recordingsCount; $counter>=0; $counter--){
					$meetingIds[] = $bbbRecordingsArr[$counter][meetingid];
			}
		}
		//Last Stop Here
		$implodedMeetingIds = implode('","', $meetingIds);
		
		$query = $db->getQuery(true)
			->select('id, meeting_id, recording_id, recording_title, recording_description, recording_url, start_time, end_time')
			->from($db->quoteName('#__bigbluebutton_recordings'))
			->where('meeting_id IN ("'.$implodedMeetingIds.'")'); 
		$query->order('start_time DESC');
		$db->setQuery($query);
		$bbbRecordingsResult = $db->loadObjectList();
		
		$recResult = array();
		
		foreach($bbbRecordingsResult as $bbbRecs){
			
			$query = $db->getQuery(true)
				->select('id, meeting_id, cat_id, meetingName, meeting_description, voiceBridge, maxParticipants, record, event_moderators, allowed_users, allowed_groups, display_time, start_time, end_time, all_moderator, event_isopen, duration, created')
				->from($db->quoteName('#__bigbluebutton_meetings'))
				->where('meeting_id = "'.$bbbRecs->meeting_id.'"'); 
			$db->setQuery($query);
			$res = $db->loadObject();
			
			$bbbRecs->display_time = $res->display_time;
			$bbbRecs->event_moderators = json_decode($res->event_moderators);
			
			$recResult[] = $bbbRecs;
		}
		
		
		return $recResult;
	}
}