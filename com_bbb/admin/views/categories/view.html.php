<?php
/**
* @version		$Id:meeting.php 1 2015-03-05 16:31:34Z Jibon $
* @package		BBB
* @subpackage 	Views
* @copyright	Copyright (C) 2015, Jibon Lawrence Costa. All rights reserved.
* @license #http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

 
class BBBViewcategories  extends JViewLegacy {


	protected $items;

	protected $pagination;

	protected $state;
	
	
	/**
	 *  Displays the list view
 	 * @param string $tpl   
     */
	public function display($tpl = null)
	{
		
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		buildSubMenu::addSubmenu('categories');

		$this->addToolbar();
		if(!version_compare(JVERSION,'3','<')){
			$this->sidebar = JHtmlSidebar::render();
		}
		
		if(version_compare(JVERSION,'3','<')){
			$tpl = "25";
		}
		parent::display($tpl);
	}
	
	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 */
	protected function addToolbar()
	{
		
		$canDo = buildSubMenu::getActions();
		$user = JFactory::getUser();
		JToolBarHelper::title( JText::_( 'Meeting' ), 'generic.png' );
		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('meeting.add');
			JToolBarHelper::custom('category.add', 'new.png', 'new.png', 'Add Category', false);
			JToolBarHelper::custom('type.add', 'new.png', 'new.png', 'Add Type', false);
			JToolBarHelper::custom('excludegroup.add', 'new.png', 'new.png', 'Exclude a Group', false);
		}	
		
		if (($canDo->get('core.edit')))
		{
			JToolBarHelper::editList('category.edit');
		}
		
				
				

		if ($canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('', 'categories.delete', 'Delete');
		}
				
		
		JToolBarHelper::preferences('com_bbb', '550');  
		if(!version_compare(JVERSION,'3','<')){		
			JHtmlSidebar::setAction('index.php?option=com_bbb&view=categories');
		}
				
					
	}	
	

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 */
	protected function getSortFields()
	{
		return array(
		 	          'a.title' => JText::_('title'),
	     	          'a.id' => JText::_('JGRID_HEADING_ID'),
	     		);
	}	
}
?>
