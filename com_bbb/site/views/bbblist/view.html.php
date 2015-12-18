<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the EventLoader Component
 */
class BbbViewBbbList extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null)
	{		
		$model = $this->getModel();
		$app = JFactory::getApplication();
		
		$menu      = $app->getMenu(); // Load the JMenuSite Object
		$menuActive    = $menu->getActive(); // Load the Active Menu Item as an stdClass Object
		
		$user = JFactory::getUser();
		
		 
		$bbbCategories = $model->getCategories();
		$bbbEvents = $model->getList();
		$iframeMenu = $model->getBbbIframeMenu();
		
		$this->user = $user;
		$this->iframeMenu = $iframeMenu[0];
		$this->menuActive = $menuActive;
		$this->bbbCategories = $bbbCategories;
		$this->bbbEvents = $bbbEvents;
		
		
		// Display the view
		parent::display($tpl);
	}
}