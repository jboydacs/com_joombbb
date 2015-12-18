 <?php
/**
* @version		$Id:view.html.php 1 2015-03-05 16:31:34Z Jibon $
* @package		BBB
* @subpackage 	Views
* @copyright	Copyright (C) 2015, Jibon Lawrence Costa. All rights reserved.
* @license #http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

 
class BBBViewexcludegroup extends JViewLegacy {

	
	protected $form;
	
	protected $item;
	
	protected $state;
	
	
	/**
	 *  Displays the list view
 	 * @param string $tpl   
     */
	public function display($tpl = null) 
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);
		
		// Initialiase variables.
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');
		
		$model = $this->getModel();
		$isNew = !($this->item->id);
		$this->isNew = $isNew;
		//Group List
		$groupOptions = $model->getGroupsList();
		$this->groupOptions = $groupOptions;
		
		if($isNew){
			$this->excludedGroupsDropdownList = JHTML::_('select.genericlist', $groupOptions, 'excluded_group_id', 'class="inputbox"', 'value', 'text', 0);
		} else {
			$this->excludedGroupId = $this->item->group_id;
		}
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		parent::display($tpl);	
	}	
}
?>