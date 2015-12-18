<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT_ADMINISTRATOR."/helpers/bbb.php";

class BbbModelBbb extends JModelLegacy
{
	public function getMeeting(){
		$input = JFactory::getApplication()->input;
		$id = $input->get('meetingID');
		$tempId = (int)$input->get('meetingID');
		$id = $id;
		$password = $input->get('password');
		$user = JFactory::getUser();
		$username = ($user->id) ? $user->name : 'Anonymous';
		$data = array();
		
		if($id){
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query = "SELECT * FROM `#__bigbluebutton_meetings` WHERE `id`=".(int)$id;
			$db->setQuery($query);
			$bbbdata = $db->loadObject();
			
			$isUserModerator = false;
			$moderators_ids = json_decode($bbbdata->event_moderators);
			
			foreach($moderators_ids as $moderatorid){
				if($moderatorid == $user->id){
					$isUserModerator = true;
					break;
				}
			}
			
			//add a field for moderator users. so if a user is a moderator the password that will be used will be the moderator password, else will be the attendee password.
			$password = ( ( ( $bbbdata->all_moderator == 1 ) && ( $user->id ) ) || ($isUserModerator) ) ? $bbbdata->moderatorPW : $bbbdata->attendeePW ;
			
			$bbb = new BBBHelper();
			//here the function is called here.
			$get = $bbb->meeting($id, $username, $password);
			if (preg_match("/meetingID/",$get)) {
				$data['status'] = "yes";
				$data['url']= $get;
				$data['message']= 'Success';
			}
			else {
				$data['status'] = "no";
				$data['url']= '';
				$data['message']= $get;
			}
		}
		return $data;
	}
}