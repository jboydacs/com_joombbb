<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the EventLoader Component
 */
class BbbViewBbbRecordings extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null)
	{		
		$model = $this->getModel();
		$app = JFactory::getApplication();
		$input = $app->input;
		$menu = $app->getMenu(); // Load the JMenuSite Object
		$menuActive    = $menu->getActive(); // Load the Active Menu Item as an stdClass Object
		$categoryid = $input->get('categoryid');
		$user = JFactory::getUser();
		$bbbCategories = $model->getCategories();
		$bbbEvents = $model->getList();
		$iframeMenu = $model->getBbbIframeMenu();
		$bbbrecordings = $model->getBbbDbRecordings(0,$categoryid);
		
		/* echo var_dump($bbbrecordings); */
		
		$groups = $user->get('groups');
		$this->user = $user;
		$this->bbbrecordings = $bbbrecordings;
		$this->iframeMenu = $iframeMenu[0];
		$this->menuActive = $menuActive;
		$this->bbbCategories = $bbbCategories;
		$this->bbbEvents = $bbbEvents;
		
		
		// Display the view
		parent::display($tpl);
	}
}