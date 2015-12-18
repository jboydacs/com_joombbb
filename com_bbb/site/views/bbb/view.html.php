<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the EventLoader Component
 */
class BbbViewBbb extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null)
	{		
		$model = $this->getModel();
		$meetingDetails = $model->getMeeting();
		$app = JFactory::getApplication();
		
		$this->meetingDetails = $meetingDetails;
		
		if($this->meetingDetails['status'] == 'yes'){
			$app->redirect($this->meetingDetails['url']);
		}
		
		// Display the view
		parent::display($tpl);
	}
}