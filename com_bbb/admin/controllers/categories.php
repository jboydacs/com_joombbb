<?php
/**
* @version		$Id$ $Revision$ $Date$ $Author$ $
* @package		BBB
* @subpackage 	Controllers
* @copyright	Copyright (C) 2015, Jibon Lawrence Costa.
* @license 		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/

// 

defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');
/**
 * Meeting list controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  BBB
 */
class BBBControllerCategories extends JControllerAdmin
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config	An optional associative array of configuration settings.
	 *
	 * @return  BBBControllerCategories
	 * @see     JController
	 */
	public function __construct($config = array())
	{
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

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function saveOrderAjax()
	{
		// Get the input
		$pks   = $this->input->post->get('cid', array(), 'array');
		$order = $this->input->post->get('order', array(), 'array');

		// Sanitize the input
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
	}
	/**
	 * Function that allows child controller access to model data
	 * after the item has been deleted.
	 *
	 * @param   JModelLegacy  $model  The data model object.
	 * @param   integer       $ids    The array of ids for items being deleted.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	protected function postDeleteHook(JModelLegacy $model, $ids = null)
	{
	}

}
