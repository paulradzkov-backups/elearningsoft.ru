<?php
/**
* Testimonials plugin for Joomla 3
* @package Testimonials
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * Editor Testimonial buton
 *
 * @package Editors-xtd
 * @since 1.6
 */
class plgButtonTestimonial extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	function onDisplay($name, $asset, $author)
	{
		$js = "
		function jSelectTestimonial(id, title) {
			var tag = '{testimonial '+title+' '+1+'}';
			jInsertEditorText(tag, '".$name."');
			SqueezeBox.close();
		}";

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);

		JHTML::_('behavior.modal');

		$link = 'index.php?option=com_testimonials&amp;view=tags&amp;layout=modal&amp;tmpl=component';

		$button = new JObject();
		$button->set('modal', true);
		$button->set('link', $link);
		$button->set('text', JText::_('PLG_TESTIMONIAL_BUTTON_TEXT'));
		$button->set('name', 'article');
		$button->set('options', "{handler: 'iframe', size: {x: 770, y: 400}}");
		//if (JRequest::getVar('option')=='com_content')
		return $button; 
		//else return null;
	}
}
