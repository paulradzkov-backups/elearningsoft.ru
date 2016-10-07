<?php
/**
* Testimonials Component for Joomla 3
* @package Testimonials
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the Testimonials Component
 */
class TestimonialsViewTestimonials extends JViewLegacy
{
	protected $state = null;
	protected $item = null;
	protected $items = null;
	protected $pagination = null;
	
       function display($tpl = null) 
        {     	
        	$this->state		= $this->get('State');
			$this->items		= $items = $this->get('Items');

			$this->pagination	= $this->get('Pagination');
			$this->html			= $this->get('Html');
			$this->params		= $params =  TestimonialHelper::getSettings();
                        $params_com = TestimonialHelper::getParams();
			$this->custom_array = $this->get('CustomArray');
			$this->custom_array = (is_array($this->custom_array)?$this->custom_array :array());
			$this->intro_text = $this->get('IntroText');
			
			$Gparams = JFactory::getApplication()->getParams();
			
			$template = $params->get('template')?$params->get('template'):(($params_com->get('template'))?$params_com->get('template'):'default');
                       
			$tmpl = $Gparams->get('template');
			
			$template = $tmpl?$tmpl:$template;
			$template = (JFactory::getApplication()->input->getVar('temp')) ? JFactory::getApplication()->input->getVar('temp') : $template;
			$this->template = TestimonialHelper::needTemplate($template, $params);
			
			if($params->get('show_captcha'))
			{
				TestimonialHelper::enableCaptcha();
			}
			
			$document = JFactory::getDocument();
                        $document->addStyleSheet(JURI::root().'media/jui/css/bootstrap.min.css');
			$document->addStyleSheet(JURI::root().'administrator/components/com_testimonials/assets/css/testimonials.css');
			
			if (count($errors = $this->get('Errors'))) { JError::raiseWarning(500, implode("\n", $errors));	return false;}

			parent::display($tpl);
        }
        
    public function adminForm($id=0, $published=0)
    {
    	$user = JFactory::getUser();
    	$iCan = new stdClass();
    	$iCan->edit = $user->authorise('core.edit', 'com_testimonials');
    	$iCan->delete = $user->authorise('core.delete', 'com_testimonials');
    	$iCan->publish = $user->authorise('core.publish', 'com_testimonials');
    	$iCan->comment = $user->authorise('core.comment', 'com_testimonials');
    	if(JFactory::getApplication()->input->getVar('tmpl')=='component') $tmpl="&tmpl=component"; else $tmpl='';
		$db = JFactory::getDBO();

    	if ($iCan->edit || $iCan->delete || $iCan->publish || $iCan->comment) 
    	{
    	?>
		<div style="border:1px solid #cccccc;text-align:center;width:20px;float:right;">
			<?php 
			if ($iCan->publish) 
				{
					$img = $published?'tick.png':'publish_x.png';
			?>
			<a href="<?php echo JRoute::_('index.php?option=com_testimonials&task=topic.state&id='.$id.$tmpl); ?>" title="<?php echo ($published? Jtext::_('COM_TESTIMONIALS_UNPUBLISH'):Jtext::_('COM_TESTIMONIALS_PUBLISH')); ?>" >
				<i class="icon-<?php echo $published?'minus':'checkmark'; ?>" alt="<?php echo ($published?Jtext::_('COM_TESTIMONIALS_UNPUBLISH'):Jtext::_('COM_TESTIMONIALS_PUBLISH')); ?>" ></i>
			</a>
			<?php }
			
			if ($iCan->edit) 
			{ ?>
			<a target="_blank" href="<?php echo JRoute::_('index.php?option=com_testimonials&view=form&tmpl=component&id='.$id.$tmpl); ?>" title="<?php echo Jtext::_('COM_TESTIMONIALS_EDIT'); ?>" class="comtm_iframe">
				<i class="icon-edit" alt="<?php echo Jtext::_('COM_TESTIMONIALS_EDIT'); ?>"></i>
			</a>
			<?php 
			}
			if ($iCan->delete) 
				{
			?>
			<a href="javascript:void(0)" onclick="javascript:if (confirm('<?php echo JText::_('COM_TESTIMONIALS_CONFIRM_DELETE'); ?>')){ document.location.href='<?php echo JRoute::_('index.php?option=com_testimonials&task=topic.delete&id='.$id.$tmpl); ?>';}else return;" title="<?php echo Jtext::_('COM_TESTIMONIALS_DELETE'); ?>" >
				<i class="icon-delete"></i>
			</a>
			<?php }
			if ($iCan->comment) {
			?>
			<a href="javascript:void(0)" onclick="jQuery('#add_comment<?php echo $id;?>').slideToggle();" title="<?php echo JText::_('COM_TESTIMONIALS_CAN_COMMENT');?>" >
				<i class="icon-comment" alt="<?php echo Jtext::_('COM_TESTIMONIALS_CAN_COMMENT'); ?>"></i>
			</a>
			<?php }
			?>
		</div>
		<?php
    	}
    }
    
    public function getCustomFields($id=0)
    {
    	$model = $this->getModel('testimonials');
    	return $model->getCustomFields($id);
    }
}
