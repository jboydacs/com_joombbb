<?php
/**
* @version		$Id:edit.php 1 2015-03-05 16:31:34Z Jibon $
* @copyright	Copyright (C) 2015, Jibon Lawrence Costa. All rights reserved.
* @license 		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/
// no direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

// Set toolbar items for the page
$edit		= JFactory::getApplication()->input->get('edit', true);
$text = !$edit ? JText::_( 'New' ) : JText::_( 'Edit' );
JToolBarHelper::title(   JText::_( 'Category' ).': <small><small>[ ' . $text.' ]</small></small>' );
JToolBarHelper::apply('category.apply');
JToolBarHelper::save('category.save');
if (!$edit) {
	JToolBarHelper::cancel('category.cancel');
} else {
	// for existing items the button is renamed `close`
	JToolBarHelper::cancel( 'category.cancel', 'Close' );
}
?>
<script src="/media/jui/js/chosen.jquery.js" type="text/javascript"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<link rel="stylesheet" href="/media/jui/css/chosen.css" type="text/css"></link>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css"></link>
<script language="javascript" type="text/javascript">
Joomla.submitbutton = function(task)
{
	if (task == 'category.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}

</script>
<style>
	.bbb_edit_form .chzn-container{
		vertical-align: middle;
		margin: 4px 0;
	}
	#remove_select{
	  display: inline-block;
	  border: 1px solid #adadad;
	  padding: 0 8px;
	  vertical-align: middle;
	  cursor: pointer;
	  margin: 0 5px;
	}
	#addmoderator_button{
		display: inline-block;
		padding: 6px 9px;
		border-radius: 4px;
		border: 1px solid #ADADAD;
		background: #EFEFEF;
		margin: 3px 0 3px 125px;
		cursor: pointer;
	}
	#adduser_button{
		display: inline-block;
		padding: 6px 9px;
		border-radius: 4px;
		border: 1px solid #ADADAD;
		background: #EFEFEF;
		margin: 3px 0 3px 125px;
		cursor: pointer;
	}
	#addgroup_button{
		display: inline-block;
		padding: 6px 9px;
		border-radius: 4px;
		border: 1px solid #ADADAD;
		background: #EFEFEF;
		margin: 3px 0 3px 125px;
		cursor: pointer;
	}
	.datepicker_cls{
		width: 85px;
	}
	.event_datehr_cls, .event_datemin_cls{
		width: 65px;
	}
	.datepicker-cont{
		display: inline-block;
		margin-right: 10px;
	}
	.timehrmin-cont{
		display: inline-block;
		margin-right: 10px;
	}
</style>
	 	<form method="post" action="<?php echo JRoute::_('index.php?option=com_bbb&layout=edit&id='.(int) $this->item->id);  ?>" id="adminForm" name="adminForm" class="bbb_edit_form">
	 	<div class="col <?php if(version_compare(JVERSION,'3.0','lt')):  ?>width-60  <?php endif; ?>span8 form-horizontal fltlft">
		  <fieldset class="adminform">
			<legend><?php echo JText::_( 'Add a Category' ); ?></legend>
				<div class="control-group">
					<div class="control-label">					
						<span class="hasTooltip" title="Add the category"><?php echo 'Add the category'; ?></span>
						<span class="star">&nbsp;*</span>
					</div>
					<div class="controls">	
						<input aria-invalid="false" required="" aria-required="true" name="title" id="title" class="category" value="<?php echo $this->item->title ?>" />
					</div>
				</div>
				
          </fieldset>                      
        </div>
        <div class="col <?php if(version_compare(JVERSION,'3.0','lt')):  ?>width-30  <?php endif; ?>span2 fltrgt">
			        

        </div>                   
		<input type="hidden" name="option" value="com_bbb" />
	    <input type="hidden" name="cid[]" value="<?php echo $this->item->id ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="view" value="category" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>