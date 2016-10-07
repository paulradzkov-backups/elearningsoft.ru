<?php
/**
* Testimonials Component for Joomla 3
* @package Testimonials
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.controllerform');
 
/**
 * Topic Controller
 */
class TestimonialsControllerTopic extends JControllerForm
{
      	protected function allowEdit($data = array(), $key = 'id')
        {

            // Check specific edit permission then general edit permission.
             return JFactory::getUser()->authorise('core.edit', 'com_testimonials');             
        }

	function apply() {
		print_r($_REQUEST);
		die;
		/*if (parent::save()) {

		}*/
	}
	public function addImage(){
	    if($this->allowEdit()){
		$model = parent::getModel();
		switch($_SERVER['REQUEST_METHOD']){
		    case 'POST':
			$model->uploadImage();
			break;
		    case 'DELETE':
			print_r($_REQUEST);
			break;
		}
	    }
	    die();
	}
}
