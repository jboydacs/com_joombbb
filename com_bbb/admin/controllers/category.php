<?php
/**
* @version		$Id:default.php 1 2015-03-05 16:31:34Z Jibon $
* @package		BBB
* @subpackage 	Controllers
* @copyright	Copyright (C) 2015, Jibon Lawrence Costa. All rights reserved.
* @license 		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controlleradmin');
jimport('joomla.application.component.controllerform');

/**
 * BBBMeeting Controller
 *
 * @package    BBB
 * @subpackage Controllers
 */
class BBBControllerCategory extends JControllerForm
{
	public function __construct($config = array())
	{
	
		$this->view_item = 'category';
		$this->view_list = 'categories';
		parent::__construct($config);
	}	
	
	/**
	 * Proxy for getModel.
	 *
	 * @param   string	$name	The name of the model.
	 * @param   string	$prefix	The prefix for the PHP class name.
	 *
	 * @return  JModel
	 * @since   1.6
	 */
	public function getModel($name = 'Category', $prefix = 'BBBModel', $config = array('ignore_request' => false))
	{
		$model = parent::getModel($name, $prefix, $config);
	
		return $model;
	}	
}// class
?>