<?php
/**
 * @version		$Id:controller.php 1 2015-03-05Z Jibon $
 * @author	   	Jibon Lawrence Costa
 * @package    BBB
 * @subpackage Controllers
 * @copyright  	Copyright (C) 2015, Jibon Lawrence Costa. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
require_once JPATH_ROOT.'/administrator/components/com_bbb/helpers/bbb.php';

/**
 * BBB Standard Controller
 *
 * @package BBB   
 * @subpackage Controllers
 */
class BBBController extends JControllerLegacy
{
	/**
	 * @var		string	The default view.
	 * @since   1.6
	 */
	protected $default_view = 'meetings';
	
	/**
	 * Method to display a view.
	 *
	 * @param   boolean			If true, the view output will be cached
	 * @param   array  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController		This object to support chaining.
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
        $input = JFactory::getApplication()->input;
		$view   = $input->get('view', 'meetings');
		$layout = $input->get('layout', 'default');
		$id     = $input->get('id');

		parent::display();
	
		return $this;
	}
	
	public function endMeeting ($meetingId = null) {
		
		$input = JFactory::getApplication()->input;
		$meetingId = $input->get('meetingId');
		
		if($meetingId){
			$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query = "SELECT moderatorPW FROM `#__bigbluebutton_meetings` WHERE `id`=".$meetingId;
				$db->setQuery($query);
			$password = $db->loadResult();
			
			$bbb = new BBBHelper();
			$bbb->endMeeting($meetingId, $password);
			$app = &JFactory::getApplication();
			$app->redirect("index.php?option=com_bbb");
		}
		jexit();
	}
	
	public function getRecordings ($meetingId = 1) {
		
		$input = JFactory::getApplication()->input;
		$meetingId = $input->get('meetingId');
		$bbb = new BBBHelper();
		$bbb->getRecordings($meetingId);
		jexit();
	}
	
	public function publishRecordings($recordId = null){
		
		$input = JFactory::getApplication()->input;
		$recordId = $input->get('recordId');
		$bbb = new BBBHelper();
		$bbb->publishRecordings($recordId);
		$app = &JFactory::getApplication();
		$app->redirect("index.php?option=com_bbb&view=records&message=success");
		jexit();
	}
	
	public function deleteRecordings($recordId = null){
		
		$input = JFactory::getApplication()->input;
		$recordId = $input->get('recordId');
		$bbb = new BBBHelper();
		$bbb->deleteRecordings($recordId);
		$app = &JFactory::getApplication();
		$app->redirect("index.php?option=com_bbb&view=records&message=success");
		jexit();
	}

}// class
  
?>
