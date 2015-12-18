   <?php
 defined('_JEXEC') or die('Restricted access');
/**
* @version		$Id:meeting.php  1 2015-03-05 16:31:34Z Jibon $
* @package		BBB
* @subpackage 	Models
* @copyright	Copyright (C) 2015, Jibon Lawrence Costa. All rights reserved.
* @license 		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/
 defined('_JEXEC') or die('Restricted access');
 
 jimport('joomla.user.helper');
/**
 * BBBModelMeeting 
 * @author Jibon Lawrence Costa
 */
if(version_compare(JVERSION,'3','<')){ 
	jimport('joomla.application.component.modeladmin');
	jimport('joomla.application.component.modelform');
 } 
 
class BBBModelMeeting  extends JModelAdmin { 

		
/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form. [optional]
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not. [optional]
	 *
	 * @return  mixed  A JForm object on success, false on failure

	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_bbb.meeting', 'meeting', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}
		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$app  = JFactory::getApplication();
		$data = $app->getUserState('com_bbb.edit.meeting.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		
		}
		
		if(!version_compare(JVERSION,'3','<')){
			$this->preprocessData('com_bbb.meeting', $data);
		}
		

		return $data;
	}
	
	public function save(array $data)
	{
		$flag = true;
		
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
		$jinput = JFactory::getApplication()->input;
		
		$jform = $jinput->get('jform', array(), 'array');
		
		$moderators_id = $jinput->get('moderators_id', array(), 'array');
		
		$modidCount = count($moderators_id);
		$moderators_idArr = array();
		if($modidCount > 1){
			foreach($moderators_id as $modid){
				if($modid != 0) $moderators_idArr[] = $modid;
			}
		}
		$moderators_json = json_encode($moderators_idArr);
		
		$allowed_users_id = $jinput->get('allowed_users_id', array(), 'array');
		
		$alloweduseridCount = count($allowed_users_id);
		$allowedusers_idArr = array();
		if($alloweduseridCount > 1){
			foreach($allowed_users_id as $auid){
				if($auid != 0) $allowedusers_idArr[] = $auid;
			}
		}
		$allowed_users_json = json_encode($allowedusers_idArr);
		
		$allowed_groups_id = $jinput->get('allowed_groups_id', array(), 'array');
		
		$allowedgroupidCount = count($allowed_groups_id);
		$allowedgroups_idArr = array();
		if($allowedgroupidCount > 1){
			foreach($allowed_groups_id as $agid){
				if($agid != 0) $allowedgroups_idArr[] = $agid;
			}
		}
		$allowed_groups_json = json_encode($allowedgroups_idArr);
		//Last Stop Here. Work in the edit.php for te groups and users allow to remove when button(remove) is clicked. see moderator.
		//Get Requests
		$id = $jinput->get('id');
		$category_id = $jinput->get('category_id');
		$type_id = $jinput->get('type_id');
		$task = $jinput->get('task');
		$thisId = $id;
		$meetingName = $jform[meetingName];
		$meeting_description = $jform[meeting_description];
		$access = $jinput->get('access');
		//Last Stop Here.. 
		//Rules: needs to be 5 digits.. and only 5 digits.
		//Randomize Voice Bridge.. condition: do a query, then if exist randmize again..
		 
		$voiceBridge = $this->generateVoiceBridge();
		/* $voiceBridge = $jform[voiceBridge]; */
		$maxParticipants = $jform[maxParticipants];
		$record = $jform[record];
		$duration = $jform[duration];
		
		$event_isopen = $jinput->get('event_isopen');
		$all_moderator = $jinput->get('all_moderator');
		
		$start_date_timestamp = $jinput->get('start_date_timestamp');
		$end_date_timestamp = $jinput->get('end_date_timestamp');
		$display_date_timestamp = $jinput->get('display_date_timestamp');
		
		//if lesson exist update the data
		if($id){
			// Create and populate an object.
			//Save Lesson Details
			$bbbMeeting = new stdClass();
			$bbbMeeting->id = (int)$id;
			$bbbMeeting->cat_id = $category_id;
			$bbbMeeting->type_id = $type_id;
			$bbbMeeting->meetingName = $meetingName;
			$bbbMeeting->meeting_description = $meeting_description;
			$bbbMeeting->maxParticipants = $maxParticipants;
			$bbbMeeting->voiceBridge = $voiceBridge;
			$bbbMeeting->record = $record;
			$bbbMeeting->access = $access;
			$bbbMeeting->duration = $duration;
			$bbbMeeting->event_isopen = $event_isopen;
			$bbbMeeting->event_moderators = $moderators_json;
			$bbbMeeting->allowed_users = $allowed_users_json;
			$bbbMeeting->allowed_groups = $allowed_groups_json;
			$bbbMeeting->all_moderator = $all_moderator;
			$bbbMeeting->start_time = $start_date_timestamp;
			$bbbMeeting->end_time = $end_date_timestamp;
			$bbbMeeting->display_time = $display_date_timestamp;
			
			// Insert the objects into the database.
			$updateBbbMeeting = $db->updateObject('#__bigbluebutton_meetings', $bbbMeeting, 'id');
		} else {
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
			$bbbMeeting->meetingName = $meetingName;
			$bbbMeeting->meeting_description = $meeting_description;
			$bbbMeeting->moderatorPW = $moderatorPW;
			$bbbMeeting->attendeePW = $attendeePW;
			$bbbMeeting->maxParticipants = $maxParticipants;
			$bbbMeeting->voiceBridge = $voiceBridge;
			$bbbMeeting->record = $record;
			$bbbMeeting->access = $access;
			$bbbMeeting->duration = $duration;
			$bbbMeeting->event_isopen = $event_isopen;
			$bbbMeeting->event_moderators = $moderators_json;
			$bbbMeeting->allowed_users = $allowed_users_json;
			$bbbMeeting->allowed_groups = $allowed_groups_json;
			$bbbMeeting->all_moderator = $all_moderator;
			$bbbMeeting->start_time = $start_date_timestamp;
			$bbbMeeting->end_time = $end_date_timestamp;
			$bbbMeeting->display_time = $display_date_timestamp;
			
			// Insert the objects into the database.
			$result = $db->insertObject('#__bigbluebutton_meetings', $bbbMeeting);
			$thisId = $db->insertid();
		}
		
		$url = '';
		
		if($task == 'apply'){
			$url = JRoute::_('/administrator/index.php?option=com_bbb&view=meeting&layout=edit&id=' . (int)$thisId);
		} else {
			$url = JRoute::_('/administrator/index.php?option=com_bbb&view=meetings');
		}
		
		$app->redirect($url);
		
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
	
	
}
?>