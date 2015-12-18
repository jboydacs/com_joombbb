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
 
class BBBModelExcludeGroup  extends JModelAdmin { 

		
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
		$form = $this->loadForm('com_bbb.excludegroup', 'excludegroup', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = $app->getUserState('com_bbb.edit.excludegroup.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		
		}
		
		if(!version_compare(JVERSION,'3','<')){
			$this->preprocessData('com_bbb.excludegroup', $data);
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
		$moderators_json = json_encode($moderators_id);
		
		$id = $jinput->get('id');
		$task = $jinput->get('task');
		$thisId = $id;
		$group_id = $jinput->get('excluded_group_id');
		
		$query	= $db->getQuery(true);		
		$query->select('id, parent_id, lft, rgt, title');
		$query->from('#__usergroups');
		$query->where('id = ' . $group_id);
		$db->setQuery($query);
		$exGroup = $db->loadObject();
		
		//if lesson exist update the data
		if($id){
			// Create and populate an object.
			$bbbExcludeGroup = new stdClass();
			$bbbExcludeGroup->id = (int)$id;
			$bbbExcludeGroup->group_id = $exGroup->id;
			$bbbExcludeGroup->group_name = $exGroup->title;
			
			// Insert the objects into the database.
			$updateBbbExcludeGroup = $db->updateObject('#__bigbluebutton_excludedgroups', $bbbExcludeGroup, 'id');
		} else {
			// Create and populate an object.
			$bbbExcludeGroup = new stdClass();
			$bbbExcludeGroup->group_id = $exGroup->id;
			$bbbExcludeGroup->group_name = $exGroup->title;
		
			// Insert the objects into the database.
			$result = $db->insertObject('#__bigbluebutton_excludedgroups', $bbbExcludeGroup);
			$thisId = $db->insertid();
		}
		
		$url = '';
		
		if($task == 'apply'){
			$url = JRoute::_('/administrator/index.php?option=com_bbb&view=excludegroup&layout=edit&id=' . (int)$thisId);
		} else {
			$url = JRoute::_('/administrator/index.php?option=com_bbb&view=excludegroups');
		}
		
		$app->redirect($url); 
		
		
		return $flag;
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
		$app = JFactory::getApplication();
		$jinput = JFactory::getApplication()->input;
		$jform = $jinput->get('jform', array(), 'array');
		$id = $jinput->get('id');
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id, title, parent_id');
		$query->from('#__usergroups');
		$db->setQuery($query);
		$groups = $db->loadObjectList();
		$options  = array();
		
		$currGroupId = 0;
		if($id){
			$query = $db->getQuery(true);
			$query->select('group_id');
			$query->from('#__bigbluebutton_excludedgroups');
			$query->where('id = ' . $id);
			$db->setQuery($query);
			$currGroupId = $db->loadResult();
		}
		
		$query = $db->getQuery(true);
		$query->select('id, group_name, group_id');
		$query->from('#__bigbluebutton_excludedgroups');
		$db->setQuery($query);
		$exGroups = $db->loadObjectList();
		
		
		if ($groups){
				$options[] = JHtml::_('select.option', 0, '--');
			foreach ($groups as $group){
				$continue = false;
				foreach ($exGroups as $exGroup){
					if($group->id == $exGroup->group_id){
						$continue = true; break;	
					}
				}
				
					//Exclude these groups from the list; 
					if( ($group->id == 1) || ($group->id == 2) || ($group->id == 3) || ($group->id == 4) || ($group->id == 5) || ($group->id == 6) || ($group->id == 7) || ($group->id == 8) || ($group->id == 9) || ($continue) && ($currGroupId != $group->id ) ) continue;
					
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
	
	
}
?>