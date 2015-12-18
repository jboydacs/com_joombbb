<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT_ADMINISTRATOR."/helpers/bbb.php";

class BbbModelBbbModLessons extends JModelLegacy
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
			
			if (!(in_array($user->id, $eventModeratorsArr))){
				continue;
			} 
			
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
					->select('id, meeting_id, cat_id, type_id, meetingName, meeting_description, voiceBridge, maxParticipants, record, event_moderators, allowed_users, allowed_groups, display_time, start_time, end_time, all_moderator, event_isopen, duration, created')
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
	
	
	public function delete(){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$jinput = JFactory::getApplication()->input;
		
		$event_id = $jinput->get('event_id');
		
		if($event_id){
			// delete the lesson with this meeting ID.
			$conditions = $db->quoteName('id') . ' = '. $event_id;
			$query->delete($db->quoteName('#__bigbluebutton_meetings'));
			$query->where($conditions);
			  
			$db->setQuery($query);
			$result = $db->execute();
		}
	}
	
	public function save(){
		$flag = true;
		
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
		$jinput = JFactory::getApplication()->input;
		
		$uri = & JFactory::getURI(); 
		$pageURL = $uri->toString();
		
		$bbb_fe_timestamp = $jinput->get('bbb_fe_timestamp');
		$task = $jinput->get('task');
		$lesson_title = trim($jinput->get('lesson_title', '', 'RAW'));
		$lesson_description = trim($jinput->get('lesson_description', '', 'RAW'));
		$voiceBridge = $this->generateVoiceBridge();
		$category_id = $jinput->get('category_id');
		$type_id = $jinput->get('type_id');
		
		$user = JFactory::getUser();
		$moderators_idArr = array();
		$moderators_idArr[] = $user->id;
		
		$moderators_json = json_encode($moderators_idArr);
	
		//Generate a random password if this entry is new.
		$meeting_id = JUserHelper::genRandomPassword(31);
		$moderatorPW = JUserHelper::genRandomPassword(32);
		$attendeePW = JUserHelper::genRandomPassword(30);
		//if lesson does not exist insert the data
		// Create and populate an object.
		//Save Lesson Details
		$bbbMeeting = new stdClass();
		$bbbMeeting->meeting_id = $meeting_id;
		$bbbMeeting->cat_id = $category_id;
		$bbbMeeting->type_id = $type_id;
		$bbbMeeting->meetingName = $lesson_title;
		$bbbMeeting->meeting_description = $lesson_description;
		$bbbMeeting->moderatorPW = $moderatorPW;
		$bbbMeeting->attendeePW = $attendeePW;
		$bbbMeeting->maxParticipants = 12;
		$bbbMeeting->voiceBridge = $voiceBridge;
		$bbbMeeting->record = "true";
		$bbbMeeting->duration = 120;
		$bbbMeeting->event_isopen = $event_isopen;
		$bbbMeeting->event_moderators = $moderators_json;
		$bbbMeeting->allowed_users = $allowed_users_json;
		$bbbMeeting->allowed_groups = $allowed_groups_json;
		$bbbMeeting->all_moderator = $all_moderator;
		$bbbMeeting->start_time = $bbb_fe_timestamp - 900000;
		//3600000 = 60 minutes
		$bbbMeeting->end_time = $bbb_fe_timestamp + 3600000;
		$bbbMeeting->display_time = $bbb_fe_timestamp;
		
		/* echo var_dump($bbbMeeting); die(); */
		
		// Insert the objects into the database.
		$result = $db->insertObject('#__bigbluebutton_meetings', $bbbMeeting);
		$thisId = $db->insertid();
		
		$app->redirect($pageURL);
		
		return $flag;
	}
	
	public function update(){
		$flag = true;
		
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
		$jinput = JFactory::getApplication()->input;
		
		$uri = & JFactory::getURI(); 
		$pageURL = $uri->toString();
		
		$bbb_fe_timestamp = $jinput->get('bbb_fe_timestamp');
		$task = $jinput->get('task');
		$lesson_title = trim($jinput->get('lesson_title', '', 'RAW'));
		$lesson_description = trim($jinput->get('lesson_description', '', 'RAW'));
		$voiceBridge = $this->generateVoiceBridge();
		$category_id = $jinput->get('category_id');
		$type_id = $jinput->get('type_id');
		
		$event_id = $jinput->get('event_id');
		
		$user = JFactory::getUser();
		$moderators_idArr = array();
		$moderators_idArr[] = $user->id;
		
		$moderators_json = json_encode($moderators_idArr);
	
		if($event_id){
			//if lesson does not exist insert the data
			// Create and populate an object.
			//Save Lesson Details
			$bbbMeeting = new stdClass();
			$bbbMeeting->id = (int)$event_id;
			$bbbMeeting->cat_id = $category_id;
			$bbbMeeting->type_id = $type_id;
			$bbbMeeting->meetingName = $lesson_title;
			$bbbMeeting->meeting_description = $lesson_description;
			$bbbMeeting->moderatorPW = $moderatorPW;
			$bbbMeeting->attendeePW = $attendeePW;
			$bbbMeeting->maxParticipants = 12;
			$bbbMeeting->record = "true";
			$bbbMeeting->duration = 120;
			$bbbMeeting->event_isopen = $event_isopen;
			$bbbMeeting->event_moderators = $moderators_json;
			$bbbMeeting->allowed_users = $allowed_users_json;
			$bbbMeeting->allowed_groups = $allowed_groups_json;
			$bbbMeeting->all_moderator = $all_moderator;
			$bbbMeeting->start_time = $bbb_fe_timestamp - 900000;
			/* $bbbMeeting->end_time = $bbb_fe_timestamp + 4500000; */
			//3600000 = 60 minutes
			$bbbMeeting->end_time = $bbb_fe_timestamp + 3600000;
			$bbbMeeting->display_time = $bbb_fe_timestamp;
			
			/* echo var_dump($bbbMeeting); die(); */
			
			// Insert the objects into the database.
			// Insert the objects into the database.
			$updateBbbMeeting = $db->updateObject('#__bigbluebutton_meetings', $bbbMeeting, 'id');
		}
		$app->redirect($pageURL);
		
		return $flag;
	}
	
	public function generateVoiceBridge(){
		$voiceBridge = rand(10000, 99999); 
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id,meeting_id');
		$query->from('#__bigbluebutton_meetings');
		$query->where('voiceBridge = '.$voiceBridge);
		$db->setQuery($query);
		$voiceBridgeExsist = $db->loadObjectList();
		
		if($voiceBridgeExsist){
			$voiceBridge = generateVoiceBridge();
		}
		
		return $voiceBridge;
	}
	
	
	/**
	 * Method to get the list of all users displayed in a dropdown.
	 *
	 * @return  array  An array of JHtml options.
	 */
	public function getUsersList(){
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id, name, email');
		$query->from('#__users');
		$query->order('name ASC');
		$db->setQuery($query);
		$users = $db->loadObjectList();
		$options  = array();

		if ($users){
				$options[] = JHtml::_('select.option', 0, '--');
			foreach ($users as $user){
				//Exclude superusers and administrators from the list 
				if(($user->id == 663) || ($user->name == 'Super User') || ($user->name == 'Super Users') || ($user->name == 'manager') || ($user->name == 'Admin') || ($user->name == 'Administrator')) continue;
				
				$options[] = JHtml::_('select.option', $user->id, $user->name);
			}	
		}
		return $options; 
	}
	
	/**
	 * Method to get the list of all Groups displayed in a dropdown.
	 *
	 * @return  array  An array of JHtml options.
	 */
	public function getGroupsList(){
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id, title, parent_id');
		$query->from('#__usergroups');
		$db->setQuery($query);
		$groups = $db->loadObjectList();
		$options  = array();

		if ($groups){
				$options[] = JHtml::_('select.option', 0, '--');
			foreach ($groups as $group){
				//Exclude these groups from the list; 
				if( ($group->id == 1) || ($group->id == 2) || ($group->id == 3) || ($group->id == 4) || ($group->id == 5) || ($group->id == 6) || ($group->id == 7) || ($group->id == 8) || ($group->id == 9) ) continue;
				
				$options[] = JHtml::_('select.option', $group->id, $group->title);
			}	
		}
		return $options; 
	}
	
	
	/**
	 * Method to get the list of all Categories displayed in a dropdown.
	 *
	 * @return  array  An array of JHtml options.
	 */
	public function getCategoriesList(){
	
		$db = JFactory::getDBO();
		// Add one category by default if none exist already
		$sql = "SELECT id from #__bigbluebutton_category";
		$db->setQuery($sql);
		$catid = $db->loadResult();
		
		
		$query = $db->getQuery(true);
		$query->select('id, title');
		$query->from('#__bigbluebutton_category');
		$db->setQuery($query);
		$categoriess = $db->loadObjectList();
		$options  = array();

		if ($categoriess){
				$options[] = JHtml::_('select.option', 0, '--');
			foreach ($categoriess as $category){
				$options[] = JHtml::_('select.option', $category->id, $category->title);
			}	
		}
		return $options; 
	}
	
	/**
	 * Method to get the list of all Types displayed in a dropdown.
	 *
	 * @return  array  An array of JHtml options.
	 */
	public function getTypesList(){
	
		$db = JFactory::getDBO();
		// Add one types by default if none exist already
		$sql = "SELECT id from #__bigbluebutton_type";
		$db->setQuery($sql);
		$typeid = $db->loadResult();
		
		
		$query = $db->getQuery(true);
		$query->select('id, title');
		$query->from('#__bigbluebutton_type');
		$db->setQuery($query);
		$types = $db->loadObjectList();
		$options  = array();

		if ($types){
				$options[] = JHtml::_('select.option', 0, '--');
			foreach ($types as $type){
				$options[] = JHtml::_('select.option', $type->id, $type->title);
			}	
		}
		return $options; 
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
}