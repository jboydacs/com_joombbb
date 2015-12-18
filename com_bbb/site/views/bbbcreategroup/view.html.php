<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the EventLoader Component
 */
class BbbViewBbbCreateGroup extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null)
	{		
		$model = $this->getModel();
		$app = JFactory::getApplication();
		
		$menu      = $app->getMenu(); // Load the JMenuSite Object
		$menuActive    = $menu->getActive(); // Load the Active Menu Item as an stdClass Object
		
		$user = JFactory::getUser();
		
		$input = JFactory::getApplication()->input;  	
		$task = $input->get('task');
		
		
		if($task == 'save'){
			$model->save();
		} else if($task == 'delete'){
			$model->delete();
		} else if($task == 'update'){
			$model->update();
		}
		
		$bbbCategories = $model->getCategories();
		$bbbEvents = $model->getList();
		$iframeMenu = $model->getBbbIframeMenu();
		
		//User List
		$userOptions = $model->getUsersList();
		$this->userOptions = $userOptions;
		
		$allUsers = $model->getUsersListDetails();
		$this->allUsers = $allUsers;
		/* echo var_dump($allUsers); */
		
		//echo var_dump($userOptions);
		//Group List
		$groupOptions = $model->getGroupsList();
		$this->groupOptions = $groupOptions;
		
		//Categories List
		$categoriesOptions = $model->getCategoriesList();
		$this->categoriesOptions = $categoriesOptions;
		
		//Types List
		$typesOptions = $model->getTypesList();
		$this->typesOptions = $typesOptions;
		
		$this->moderatorDropdownList = JHTML::_('select.genericlist', $userOptions, 'moderators_id[]', 'class="inputbox"', 'value', 'text', 0);
		$this->selectedUserDropdownList = JHTML::_('select.genericlist', $userOptions, 'selected_user_id', 'class="inputbox"', 'value', 'text', 0);
		$this->allowedGroupsDropdownList = JHTML::_('select.genericlist', $groupOptions, 'allowed_groups_id[]', 'class="inputbox"', 'value', 'text', 0);
		$this->typesDropdownList = JHTML::_('select.genericlist', $typesOptions, 'type_id', 'class="inputbox" required="" aria-required="true"', 'value', 'text', 0);
		$this->categoriesDropdownList = JHTML::_('select.genericlist', $categoriesOptions, 'category_id', 'class="inputbox" required="" aria-required="true"', 'value', 'text', 0);
		
		$this->user = $user;
		$this->iframeMenu = $iframeMenu[0];
		$this->menuActive = $menuActive;
		$this->bbbCategories = $bbbCategories;
		$this->bbbEvents = $bbbEvents;
		
		
		// Display the view
		parent::display($tpl);
	}
}