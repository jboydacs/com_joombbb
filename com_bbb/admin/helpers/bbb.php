<?php
/**
 * @version		$Id: #component#.php 170 2013-11-12 22:44:37Z michel $
 * @package		Joomla.Framework
 * @subpackage		HTML
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
require_once __DIR__ . '/bbb-api.php';


class BBBHelper
{
    
      
    protected $salt;
    protected $url;
    protected $dialNumber;
    
    function __construct () {
  	$params = JComponentHelper::getParams('com_bbb');
        $this->salt = $params->get('salt');
    	$this->url = $params->get('url');
    	$this->dialNumber = $params->get('dialNumber');
    }
    
    public function meeting($id = 1, $username = null, $password = null) {
    		
		if($id)	{		
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query = "SELECT * FROM `#__bigbluebutton_meetings` WHERE `id`=".$id;
			$db->setQuery($query);
			$data = $db->loadObject();
			$paidId = 13;
			$user = JFactory::getUser();	
			
			$startTimeInSeconds = $data->start_time / 1000;
			$endTimeInSeconds = $data->end_time / 1000;
			
			$allowedgroupsids = json_decode($data->allowed_groups);
			/*
				If duration is zero it causes an audio error, 
				so if ever a user sets the duration to zero, 
				instead of passing a zero value, pass the default value of 120 to get out from the error.
			*/
			$duration = ($data->duration == 0) ? 120 : $data->duration;
			
			$goCreateParameters = true;
			
			
			
			$isUserAllowed = false;
			$countAllowedUser = 0;
			$allowedusers_ids = json_decode($data->allowed_users);
			
			/*
				If a user is selected, only the selected users are allowed to enter the room.
				If no user is selected, all users are allowed to enter the room.
			*/
			if( !(empty($allowedusers_ids)) && ($data->allowed_users != '') ) {
				foreach($allowedusers_ids as $alloweduserid){
					if($alloweduserid > 0){
						$countAllowedUser++;
					}
					
					if($alloweduserid == $user->id){
						$isUserAllowed = true;
						break;
					}
				}
			}
			
			$isUserModerator = false;
			$moderators_ids = json_decode($data->event_moderators);
			
			foreach($moderators_ids as $moderatorid){
				if($moderatorid == $user->id){
					$isUserModerator = true;
					break;
				}
			}
			
			$isGroupAllowed = false;
			$countAllowedGroup = 0;
			$allowedgroups_ids = json_decode($data->allowed_groups);
			$allowedgroups_idsS = ($allowedgroups_ids) ? implode(', ', $allowedgroups_ids): '0';
			$query = $db->getQuery(true);
			$query = "SELECT * FROM `#__user_usergroup_map` WHERE `user_id`=".$user->id." AND `group_id` IN (".$allowedgroups_idsS.")";
			$db->setQuery($query);
			$isUserIntheGroup = $db->loadObjectList();
			
			/*
				If a Group is selected, only the selected Group(s) are allowed to enter the room.
				If no Group is selected, all Group(s) are allowed to enter the room.
			*/
			if( !(empty($allowedgroups_ids)) && ($data->allowed_groups != '') ) {
				foreach($allowedgroups_ids as $allowedgroupid){
					if($allowedgroupid > 0){
						$countAllowedGroup++;
					}
				}
				if($isUserIntheGroup){
					$isGroupAllowed = true;
				}
			}
			
			if($countAllowedGroup == 0 && $countAllowedUser == 0) {
				$isGroupAllowed = true;
				$isUserAllowed = true;
			}
			
			
		//Do the conditions whether the bbb event will go and join or not.
		if ( !($data->attendeePW == $password || $data->moderatorPW == $password) ||
			(time() < $startTimeInSeconds) || 
			!(time() <= $endTimeInSeconds) || 
			( $startTimeInSeconds >= $endTimeInSeconds ) ||
			( $isUserAllowed == false && $isGroupAllowed == false && $isUserModerator == false ) ) {
			
			//Conditions
			if ( !($data->attendeePW == $password || $data->moderatorPW == $password) ) {
				$output = "Sorry password don't match";
			} else if ( $startTimeInSeconds >= $endTimeInSeconds ){
				$output = "Invalid Time Set.";
			} else if ( time() < $startTimeInSeconds ){
				$output = "Event is not yet available";
			} else if ( time() > $endTimeInSeconds ) {
				$output = "Event is already finished";
			} else {
				$output = "BBB Creation Failed.";
			}
			
			if( $isUserAllowed == false && $countAllowedUser != 0){
				$output .= "<br/> User is not allowed to enter the room";
			}
			
			if( $isGroupAllowed == false && $countAllowedGroup != 0 ){
				$output .= "<br/> User's Group is not allowed to enter the room";
			}
			
			$goCreateParameters = false;
			
			
			$groupsOfUser = JAccess::getGroupsByUser($user->id);
			//If Group is paid, display the lesson.
			if (in_array($paidId, $allowedgroupsids)){
				$goCreateParameters = true;
			}
			
		} 
		
		/*
			User is allowed:
			-A Super User.
			-Add the ID of the group if you want a group that will always be allowed to enter the room.
		*/
		$query = $db->getQuery(true);
		$query = "SELECT * FROM `#__user_usergroup_map` WHERE `user_id`=".$user->id." AND `group_id` IN (8)";
		$db->setQuery($query);
		$isSuperUser = $db->loadObjectList();
		
		//passed through the condition, and if event is open is checked.
		if( ($goCreateParameters) || 
			($data->event_isopen == 1) ||
			($isSuperUser) ){
			$creationParams = array(
				'meetingId' => $data->meeting_id,
				'meetingName' => $data->meetingName,
				'attendeePw' => $data->attendeePW,
				'moderatorPw' => $data->moderatorPW,
				'logoutUrl' => 'http://www.intenga.com/donations',
				'welcomeMsg' => 'Welcome to '.$data->meetingName,
				'dialNumber' => $this->dialNumber,
				'voiceBridge' => $data->voiceBridge,
				'startTime' => $data->start_time,
				'endTime' => $data->end_time,
				'maxParticipants' => $data->maxParticipants,
				'record' => $data->record,
				'duration' => $duration
			);
			$goJoin = true;
			$bbb = new BBB($this->salt, $this->url);

			
			try {$result = $bbb->createMeetingWithXmlResponseArray($creationParams);}
				catch (Exception $e) {
					echo 'Caught exception: ', $e->getMessage(), "\n";
					$goJoin = false;
			}
			
			$infoParams = array(
				'meetingId' => $data->meeting_id,
				'password' => $data->moderatorPW
			);
			
			try {$meetingInfo = $bbb->getMeetingInfoWithXmlResponseArray($infoParams);}
				catch (Exception $e) {
					echo 'Caught exception: ', $e->getMessage(), "\n";
			} 
			
			
			//Start Free Users
				$query = $db->getQuery(true);
				$query = "SELECT * FROM `#__bigbluebutton_freeusers` WHERE `event_id`=".$data->id;
				$db->setQuery($query);
				$eventWFreeUser = $db->loadObject();
				//if user count is equal or greater than 2 or if a user is a free user, and the user is not a returning user.
				//count($a)
				$free_users_id = json_decode($eventWFreeUser->users_id);
				
				$lessonIsPaid = false;
				
				
				$userReEntered = false;
				if(in_array($user->id, $free_users_id)){
					$userReEntered = true;
				}
				
				//if false means user is a paid user.
				$userIsFree = true;
				
				$groupsOfUser = JAccess::getGroupsByUser($user->id);
				
				//If Group is paid, display the lesson.
				if (in_array($paidId, $allowedgroupsids)){
					$goJoin = true;
					
					$lessonIsPaid = true;
					
					if(in_array($paidId, $groupsOfUser)){
						$userIsFree = false;
					}
					
				}
				
				$countFree_users_id = count($free_users_id);
				if ( ($eventWFreeUser->users_count >= 2 ||  $countFree_users_id >= 2) && ($lessonIsPaid == true) && ($userIsFree == true) && ($userReEntered == false) && ($isUserModerator == false) ) {
					$goJoin = false;
					$output = "We are sorry but only 2 free users can enter this room.";
				}
			//End Free Users
			
			
			$allowedParticipants = $meetingInfo['participantCount'] - $meetingInfo['moderatorCount'];
			if ( (($allowedParticipants >= $data->maxParticipants) && ($allowedParticipants > 0)) && ($data->maxParticipants > 0) && ($isUserModerator == false) ) {
				$goJoin = false;
				$output = "We are sorry but this classroom is full. Why not join one of our other classes.";
			}
			
			
			if ($goJoin == true) {
				if ($result == null) {    
					$output = "Failed to get any response. Maybe we can't contact the BBB server.";
				}	
				else { 
					if ($result['returncode'] == 'SUCCESS') {
						$output = $this->getlink ($id, $username, $password);
						
						
						//if false means user is a paid user.
						$userIsFree = true;
						$groupsOfUser = JAccess::getGroupsByUser($user->id);
						//If Group is paid, display the lesson.
						if(in_array($paidId, $groupsOfUser)){
							$userIsFree = false;
						}
						
						if($userIsFree == true){
							//Start Free Users
							$query = $db->getQuery(true);
							$query = "SELECT * FROM `#__bigbluebutton_freeusers` WHERE `event_id`=".$data->id;
							$db->setQuery($query);
							$eventWithFreeUser = $db->loadObject();
							
							$freeusers = array();
							
							$freeusers[] = $user->id;
							
							if(!$eventWithFreeUser){
								$users_id = json_encode($freeusers);
								
								//Insert/Update BBB Allowed Free Users
								$bbbFreeUsers = new stdClass();
								$bbbFreeUsers->event_id = $data->id;
								$bbbFreeUsers->users_id = $users_id;
								$bbbFreeUsers->users_count = 1;
								
								// Insert the objects into the database.
								$result = $db->insertObject('#__bigbluebutton_freeusers', $bbbFreeUsers);
								$thisId = $db->insertid(); 
							} else {
								$user_reenter = 0;
								
								$ex_users_id = json_decode($eventWithFreeUser->users_id);
								
								foreach($ex_users_id as $exusrid){
									if($user->id == $exusrid) $user_reenter = 1;
									$freeusers[] = $exusrid;
								}
								
								if($user_reenter == 0){		
									$users_id = json_encode($freeusers);
									$bbbFreeUsers = new stdClass();
									$bbbFreeUsers->id = (int)$eventWithFreeUser->id;
									$bbbFreeUsers->event_id = $data->id;
									$bbbFreeUsers->users_id = $users_id;
									$bbbFreeUsers->users_count = $eventWithFreeUser->users_count + 1;
									
									// Insert the objects into the database.
									$updateBbbMeeting = $db->updateObject('#__bigbluebutton_freeusers', $bbbFreeUsers, 'id');
								}
							}
							//End Free Users
						}
					}
					else {
						$output = "Meeting creation failed";
					}
				}
			}
		}
	} else {
		$output = 'BBB ID not set';
	}
    	return $output;	
    }

    protected function getlink ($id = null, $username= null, $password= null) {
	
		if($id){
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query = "SELECT * FROM `#__bigbluebutton_meetings` WHERE `id`=".(int)$id;
			$db->setQuery($query);
			$bbbdata = $db->loadObject();
		
			$joinParams = array(
				'meetingId' => $bbbdata->meeting_id, 			
				'username' => $username,	
				'password' => $password,
				'logoutUrl' => 'http://www.intenga.com/donations'				
			);
					
			$bbb = new BBB($this->salt, $this->url);
			$itsAllGood = true;
			try {$result = $bbb->getJoinMeetingURL($joinParams);}
				catch (Exception $e) {
					echo 'Caught exception: ', $e->getMessage(), "\n";
					$itsAllGood = false;
				}

			if ($itsAllGood == true) {
				return $result;
			}
		}
    }
    
    public function isMeetingRunning ($meetingId = 1) {
    	
    	$bbb = new BBB($this->salt, $this->url);
    	$itsAllGood = true;
		
		if($meetingId){
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query = "SELECT * FROM `#__bigbluebutton_meetings` WHERE `id`=".(int)$meetingId;
			$db->setQuery($query);
			$bbbdata = $db->loadObject();
				
			try {$result = $bbb->isMeetingRunningWithXmlResponseArray($bbbdata->meeting_id);}
				catch (Exception $e) {
					echo 'Caught exception: ', $e->getMessage(), "\n";
					$itsAllGood = false;
				}

			if ($itsAllGood == true) {
				return $result['running'];
			}
		}
   }
  
    public function endMeeting ($meetingId = null, $password= null) {
  	
  	$bbb = new BBB($this->salt, $this->url);
	
	if($meetingId){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query = "SELECT * FROM `#__bigbluebutton_meetings` WHERE `id`=".(int)$meetingId;
		$db->setQuery($query);
		$bbbdata = $db->loadObject();
		
		$endParams = array(
			'meetingId' => $bbbdata->meeting_id, 			
			'password' => $password,		
		);
		$itsAllGood = true;
		
		try {$result = $bbb->endMeetingWithXmlResponseArray($endParams);}
		catch (Exception $e) {
			echo 'Caught exception: ', $e->getMessage(), "\n";
			$itsAllGood = false;
		}

		if ($itsAllGood == true) {

			if ($result == null) {
				echo "Failed to get any response. Maybe we can't contact the BBB server.";
			}	
			else { 
				if ($result['returncode'] == 'SUCCESS') {
					echo "<p>Meeting succesfullly ended.</p>";
				}
				else {
					echo "<p>Failed to end meeting.</p>";
				}
			}
		}
	}
}
    
    public function getRecordings ($meetingId = 0) {
    	
		$bbb = new BBB($this->salt, $this->url);
		
		//if a bbb meeting is selected, get the recordings of that specified meeting, else list all the recordings for all the meetings
		$meetingQuery = ($meetingId) ? "WHERE id = ".$meetingId : '';
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query = "SELECT * FROM `#__bigbluebutton_meetings` ".$meetingQuery;
		$db->setQuery($query);
		$bbbdata = $db->loadObjectList();
		
		
		
		$itsAllGood = true;
		
		//Initialize this variable to be json encoded to be passed through the url.
		$final = array();
		
		if($meetingId){
			$query = $db->getQuery(true);
			$query = "SELECT * FROM `#__bigbluebutton_meetings` WHERE `id`=".(int)$meetingId;
			$db->setQuery($query);
			$bbbd = $db->loadObject();
			
			$recordingsParams = array( 'meetingId' => $bbbd->meeting_id, );
		
			try {$result = $bbb->getRecordingsWithXmlResponseArray($recordingsParams);}
			catch (Exception $e) {
				echo 'Caught exception: ', $e->getMessage(), "\n";
				$itsAllGood = false;
			}

			if ($itsAllGood == true) {
				if ($result == null) {
					echo "Failed to get any response. Maybe we can't contact the BBB server.";
				} else { 
						if ($result['returncode'] == 'SUCCESS') {
							foreach ((array) $result as $data) {
								$item = array();
								$item['recordId'] = (string) $data['recordId'][0];
								$item['playbackFormatUrl']= (string) $data['playbackFormatUrl'][0];					
								
								$item['meetingId'] = (string) $data['meetingId'][0];
								$item['startTime']= (int) $data['startTime'][0];	
								$item['endTime'] = (int) $data['endTime'][0];
								
								$final[] = $item;
							}
						} else {
							echo "<p>Failed to get meeting info.</p>";
						}
					}
				}
		} else {
			foreach ($bbbdata as $mdata) {
				$recordingsParams = array( 'meetingId' => $mdata->meeting_id, );
				
				try {$result = $bbb->getRecordingsWithXmlResponseArray($recordingsParams);}
				catch (Exception $e) {
					echo 'Caught exception: ', $e->getMessage(), "\n";
					$itsAllGood = false;
				}

				if ($itsAllGood == true) {
					if ($result == null) {
						echo "Failed to get any response. Maybe we can't contact the BBB server.";
					}	
					else { 
						
						if ($result['returncode'] == 'SUCCESS') {
							foreach ((array) $result as $data) {
								$item = array();
								$item['recordId'] = (string) $data['recordId'][0];
								$item['playbackFormatUrl']= (string) $data['playbackFormatUrl'][0];					
								
								$item['meetingId'] = (string) $data['meetingId'][0];
								$item['startTime']= (int) $data['startTime'][0];	
								$item['endTime'] = (int) $data['endTime'][0];
								
								$final[] = $item;
							}
						} else {
							echo "<p>Failed to get meeting info.</p>";
						}
					}
				}
			}
		}	
		
			foreach($final as $finalData){
				if(trim($finalData['recordId']) != ''){
					$query = $db->getQuery(true);
					$query = "SELECT id FROM `#__bigbluebutton_recordings` WHERE `recording_id` = '".$finalData['recordId']."'";
					$db->setQuery($query);
					$isRecordExist = $db->loadResult();

					if(!$isRecordExist){
						$query = $db->getQuery(true);
						$query = "SELECT meetingName, meeting_description FROM `#__bigbluebutton_meetings` WHERE `meeting_id` = '".$finalData['meetingId']."'";
						$db->setQuery($query);
						$bbbNameDesc = $db->loadObject();
					
						$recordingDetails = new stdClass();
						$recordingDetails->meeting_id = $finalData['meetingId'];
						$recordingDetails->recording_id = $finalData['recordId'];
						$recordingDetails->recording_title =  $bbbNameDesc->meetingName;
						$recordingDetails->recording_description = $bbbNameDesc->meeting_description;
						$recordingDetails->recording_url = $finalData['playbackFormatUrl'];
						$recordingDetails->start_time = $finalData['startTime'];
						$recordingDetails->end_time = $finalData['endTime'];
						// Insert the objects into the database.
						$res = $db->insertObject('#__bigbluebutton_recordings', $recordingDetails);
						$thisId = $db->insertid();
					}
				}
			}
			
			echo json_encode($final);
    }
	
	public function getBbbRecordings ($meetingId = 0) {
    	
		$bbb = new BBB($this->salt, $this->url);
		
		//if a bbb meeting is selected, get the recordings of that specified meeting, else list all the recordings for all the meetings
		$meetingQuery = ($meetingId) ? "WHERE id = ".$meetingId : '';
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query = "SELECT * FROM `#__bigbluebutton_meetings` ".$meetingQuery;
		$db->setQuery($query);
		$bbbdata = $db->loadObjectList();
		
		
		
		$itsAllGood = true;
		
		//Initialize this variable to be json encoded to be passed through the url.
		$final = array();
		
		if($meetingId){
			$query = $db->getQuery(true);
			$query = "SELECT * FROM `#__bigbluebutton_meetings` WHERE `id`=".(int)$meetingId;
			$db->setQuery($query);
			$bbbd = $db->loadObject();
			
			$recordingsParams = array( 'meetingId' => $bbbd->meeting_id, );
		
			try {$result = $bbb->getRecordingsWithXmlResponseArray($recordingsParams);}
			catch (Exception $e) {
				echo 'Caught exception: ', $e->getMessage(), "\n";
				$itsAllGood = false;
			}

			if ($itsAllGood == true) {
				if ($result == null) {
					echo "Failed to get any response. Maybe we can't contact the BBB server.";
				} else { 
						if ($result['returncode'] == 'SUCCESS') {
							foreach ((array) $result as $data) {
								$item = array();
								$item['recordId'] = (string) $data['recordId'][0];
								$item['playbackFormatUrl']= (string) $data['playbackFormatUrl'][0];					
								
								$item['meetingId'] = (string) $data['meetingId'][0];
								$item['startTime']= (int) $data['startTime'][0];	
								$item['endTime'] = (int) $data['endTime'][0];
								
								$final[] = $item;
							}
						} else {
							echo "<p>Failed to get meeting info.</p>";
						}
					}
				}
		} else {
			foreach ($bbbdata as $mdata) {
				$recordingsParams = array( 'meetingId' => $mdata->meeting_id, );
				
				try {$result = $bbb->getRecordingsWithXmlResponseArray($recordingsParams);}
				catch (Exception $e) {
					echo 'Caught exception: ', $e->getMessage(), "\n";
					$itsAllGood = false;
				}

				if ($itsAllGood == true) {
					if ($result == null) {
						echo "Failed to get any response. Maybe we can't contact the BBB server.";
					}	
					else { 
						
						if ($result['returncode'] == 'SUCCESS') {
							foreach ((array) $result as $data) {
								$item = array();
								$item['recordId'] = (string) $data['recordId'][0];
								$item['playbackFormatUrl']= (string) $data['playbackFormatUrl'][0];					
								
								$item['meetingId'] = (string) $data['meetingId'][0];
								$item['startTime']= (int) $data['startTime'][0];	
								$item['endTime'] = (int) $data['endTime'][0];
								
								$final[] = $item;
							}
						} else {
							echo "<p>Failed to get meeting info.</p>";
						}
					}
				}
			}
		}	
		
			foreach($final as $finalData){
				if(trim($finalData['recordId']) != ''){
					$query = $db->getQuery(true);
					$query = "SELECT id FROM `#__bigbluebutton_recordings` WHERE `recording_id` = '".$finalData['recordId']."'";
					$db->setQuery($query);
					$isRecordExist = $db->loadResult();

					if(!$isRecordExist){
						$query = $db->getQuery(true);
						$query = "SELECT meetingName, meeting_description FROM `#__bigbluebutton_meetings` WHERE `meeting_id` = '".$finalData['meetingId']."'";
						$db->setQuery($query);
						$bbbNameDesc = $db->loadObject();
					
						$recordingDetails = new stdClass();
						$recordingDetails->meeting_id = $finalData['meetingId'];
						$recordingDetails->recording_id = $finalData['recordId'];
						$recordingDetails->recording_title =  $bbbNameDesc->meetingName;
						$recordingDetails->recording_description = $bbbNameDesc->meeting_description;
						$recordingDetails->recording_url = $finalData['playbackFormatUrl'];
						$recordingDetails->start_time = $finalData['startTime'];
						$recordingDetails->end_time = $finalData['endTime'];
						// Insert the objects into the database.
						$res = $db->insertObject('#__bigbluebutton_recordings', $recordingDetails);
						$thisId = $db->insertid();
					}
				}
			}
			
			return $final;
    }
    
    public function publishRecordings($recordId = null) {
    	
    	$bbb = new BBB($this->salt, $this->url);
    	$recordingParams = array(
    		'recordId' => $recordId,
    		'publish' => 'true'
    	);
    	
    	$itsAllGood = true;
	try {$result = $bbb->publishRecordingsWithXmlResponseArray($recordingParams);}
		catch (Exception $e) {
			echo 'Caught exception: ', $e->getMessage(), "\n";
			$itsAllGood = false;
		}

	if ($itsAllGood == true) {
		echo $result['published'];
	}
    }
    
    public function deleteRecordings($recordId = null) {
    	
    	$bbb = new BBB($this->salt, $this->url);
    	$recordingParams = array(
    		'recordId' => $recordId,
    	);
    	$itsAllGood = true;
	try {$result = $bbb->deleteRecordingsWithXmlResponseArray($recordingParams);}
		catch (Exception $e) {
			echo 'Caught exception: ', $e->getMessage(), "\n";
			$itsAllGood = false;
		}

	if ($itsAllGood == true) {
		print_r($result);
	}
    }
      
}

class buildSubMenu
{
	
	/*
	 * Submenu for Joomla 3.x
	 */
	public static function addSubmenu($vName = 'meetings')
	{
        	if(version_compare(JVERSION,'3','<')){
		JSubMenuHelper::addEntry(
			JText::_('Meetings'),
			'index.php?option=com_bbb&view=meetings',
			($vName == 'meetings')
		);	
	} else {
		JHtmlSidebar::addEntry(
			JText::_('Meetings'),
			'index.php?option=com_bbb&view=meetings',
			($vName == 'meetings')
		);	
	}
	
	
	if(version_compare(JVERSION,'3','<')){
		JSubMenuHelper::addEntry(
			JText::_('Categories'),
			'index.php?option=com_bbb&view=categories',
			($vName == 'categories')
		);	
	} else {
		JHtmlSidebar::addEntry(
			JText::_('Categories'),
			'index.php?option=com_bbb&view=categories',
			($vName == 'categories')
		);	
	}
	
	
	if(version_compare(JVERSION,'3','<')){
		JSubMenuHelper::addEntry(
			JText::_('Types'),
			'index.php?option=com_bbb&view=types',
			($vName == 'types')
		);	
	} else {
		JHtmlSidebar::addEntry(
			JText::_('Types'),
			'index.php?option=com_bbb&view=types',
			($vName == 'types')
		);	
	}
	
	
	if(version_compare(JVERSION,'3','<')){
		JSubMenuHelper::addEntry(
			JText::_('Recording'),
			'index.php?option=com_bbb&view=records',
			($vName == 'records')
		);	
	} else {
		JHtmlSidebar::addEntry(
			JText::_('Recording'),
			'index.php?option=com_bbb&view=records',
			($vName == 'records')
		);	
	}
	
	if(version_compare(JVERSION,'3','<')){
		JSubMenuHelper::addEntry(
			JText::_('Excluded Groups'),
			'index.php?option=com_bbb&view=excludegroups',
			($vName == 'excludegroups')
		);	
	} else {
		JHtmlSidebar::addEntry(
			JText::_('Excluded Groups'),
			'index.php?option=com_bbb&view=excludegroups',
			($vName == 'excludegroups')
		);	
	}
	
	if(version_compare(JVERSION,'3','<')){
		JSubMenuHelper::addEntry(
			JText::_(''),
			'index.php?option=com_bbb&view=',
			($vName == '')
		);	
	} else {
		JHtmlSidebar::addEntry(
			JText::_(''),
			'index.php?option=com_bbb&view=',
			($vName == '')
		);	
	}

	}
	
	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param   integer  The category ID.
	 *
	 * @return  JObject
	 * @since   1.6
	 */
	public static function getActions($categoryId = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;
	
		if (empty($categoryId))
		{
			$assetName = 'com_bbb';
			$level = 'component';
		}
		else
		{
			$assetName = 'com_bbb.category.'.(int) $categoryId;
			$level = 'category';
		}
	
		$actions = JAccess::getActions('com_bbb', $level);
	
		foreach ($actions as $action)
		{
			$result->set($action->name,	$user->authorise($action->name, $assetName));
		}
	
		return $result;
	}	
	/**
	 * 
	 * Get the Extensions for Categories
	 */
	public static function getExtensions() 
	{
						
		static $extensions;
		
		if(!empty($extensions )) return $extensions;
		
		jimport('joomla.utilities.xmlelement');
		
		$xml = simplexml_load_file(JPATH_ADMINISTRATOR.'/components/com_bbb/elements/extensions.xml', 'JXMLElement');		        
		$elements = $xml->xpath('extensions');
		$extensions = $xml->extensions->xpath('descendant-or-self::extension');
		
		return $extensions;
	} 	

	
	/**
	 *
	 * Returns views that associated with categories
	 */
	public static function getCategoryViews()
	{
	
		$extensions = self::getExtensions();
		$views = array();
		foreach($extensions as $extension ) {
			$views[$extension->listview->__toString()] = 'com_bbb.'.$extension->name->__toString();
		}
		return $views;
	}	
}

/**
 * Utility class for categories
 *
 * @static
 * @package 	Joomla.Framework
 * @subpackage	HTML
 * @since		1.5
 */
abstract class JHtmlBBB
{
	/**
	 * @var	array	Cached array of the category items.
	 */
	protected static $items = array();
	
	/**
	 * Returns the options for extensions list
	 * 
	 * @param string $ext - the extension
	 */
	public static function extensions($ext) 
	{
		$extensions = BBBHelper::getExtensions();
		$options = array();
		
		foreach ($extensions as $extension) {   
		
			$option = new stdClass();
			$option->text = JText::_(ucfirst($extension->name));
			$option->value = 'com_bbb.'.$extension->name;
			$options[] = $option;			
		}		
		return JHtml::_('select.options', $options, 'value', 'text', $ext, true);
	}
	
	/**
	 * Returns an array of categories for the given extension.
	 *
	 * @param	string	The extension option.
	 * @param	array	An array of configuration options. By default, only published and unpulbished categories are returned.
	 *
	 * @return	array
	 */
	public static function categories($extension, $cat_id,$name="categories",$title="Select Category", $config = array('attributes'=>'class="inputbox"','filter.published' => array(0,1)))
	{

			$config	= (array) $config;
			$db		= JFactory::getDbo();

			$query = $db->getQuery(true);

			$query->select('a.id, a.title, a.level');
			$query->from('#__bigbluebutton_category AS a');
			$query->where('a.parent_id > 0');

			// Filter on extension.
			if($extension)
			    $query->where('extension = '.$db->quote($extension));
			
			$attributes = "";
			
			if (isset($config['attributes'])) {
				$attributes = $config['attributes'];
			}
			
			// Filter on the published state
			if (isset($config['filter.published'])) {
				
				if (is_numeric($config['filter.published'])) {
					
					$query->where('a.published = '.(int) $config['filter.published']);
					
				} else if (is_array($config['filter.published'])) {
					
					JArrayHelper::toInteger($config['filter.published']);
					$query->where('a.published IN ('.implode(',', $config['filter.published']).')');
					
				}
			}

			$query->order('a.lft');

			$db->setQuery($query);
			$items = $db->loadObjectList();
			
			// Assemble the list options.
			self::$items = array();
			self::$items[] = JHtml::_('select.option', '', JText::_($title));
			foreach ($items as &$item) {
								
				$item->title = str_repeat('- ', $item->level - 1).$item->title;
				self::$items[] = JHtml::_('select.option', $item->id, $item->title);
			}

		return  JHtml::_('select.genericlist', self::$items, $name, $attributes, 'value', 'text', $cat_id, $name);
		//return self::$items;
	}
}

