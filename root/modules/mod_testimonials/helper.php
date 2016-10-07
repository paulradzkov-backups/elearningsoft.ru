<?php
/**
* Testimonials Module for Joomla 3
* @package Testimonials
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die;

jimport( 'joomla.application.component.model' );

if (!class_exists('JModel')) 
{
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_testimonials'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'topics.php');
}

JModelLegacy::addIncludePath(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_testimonials'.DIRECTORY_SEPARATOR.'models');

if (!class_exists('TestimonialHelper'))
{
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_testimonials'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'testimonial.php');
}

class modTestimonialsHelper
{
	static $count_modules = 0;
        
        static function getList($params)
	{
		
		$settings = modTestimonialsHelper::getModuleSettings();
		$model = JModelLegacy::getInstance($name = 'Topics', $prefix = 'TestimonialsModel', array('ignore_request' => true));
		$items = $model->getItemsByItemId($settings);
                
		return $items;

	}
	
	public static function getModuleSettings()
	{
	    $module = JModuleHelper::getModule('mod_testimonials');
	    $params = new JRegistry;
	    $params->loadString($module->params);
	    return $params;
	}

	function getAvatar($photo='', $cb_avatar='')
	{
	    $this->config = modTestimonialsHelper::getModuleSettings();
		$this->nophoto=false;
		$avatar = '';
			if ($photo != ''){
				if (file_exists(JPATH_SITE."/".$photo)) {
					$path = explode('/', $photo);
					$fname = array_pop($path);
					$path = implode('/', $path);
					if(file_exists(JPATH_SITE.'/'.$path."/th_".$fname)){
					    list($width, $height, $type) = getimagesize(JPATH_SITE.'/'.$path."/th_".$fname);
					    if ($width == $this->config->get('th_width')) {
						$avatar = $path."/th_".$fname;
					    } else {
						jimport( 'joomla.filesystem.file' );
						JFile::delete(JPATH_SITE.'/'.$path."/th_".$fname);
						$TimgHelper = new TimgHelper();
						$thumb = $TimgHelper->captureImage($TimgHelper->resize(JPATH_SITE."/".$photo, $this->config->get('th_width', '110'), $this->config->get('th_width', '110')), $photo);
						JFile::write(JPATH_SITE.'/'.$path."/th_".$fname, $thumb);
						if(file_exists(JPATH_SITE.'/'.$path."/th_".$fname)) $avatar = $path."/th_".$fname;
					    }
					}else{
					    jimport( 'joomla.filesystem.file' );
					    $TimgHelper = new TimgHelper();
					    $thumb = $TimgHelper->captureImage($TimgHelper->resize(JPATH_SITE."/".$photo, $this->config->get('th_width', '110'), $this->config->get('th_width', '110')), $photo);
					    JFile::write(JPATH_SITE.'/'.$path."/th_".$fname, $thumb);
					    if(file_exists(JPATH_SITE.'/'.$path."/th_".$fname)) $avatar = $path."/th_".$fname;
					}
				}else {
					$this->nophoto=true;
				}
			}else{
				if ($this->config->get('use_cb') == 1) {
					$check = explode('/',@$cb_avatar);
					$check = $check[0];

					if (isset($cb_avatar) && $check != 'gallery') {
						if (file_exists(JPATH_SITE."/images/comprofiler/tn".$cb_avatar)) {
							$avatar = "images/comprofiler/tn".$cb_avatar;
						}else{
							$this->nophoto=true;
						}
					}elseif (isset($cb_avatar) && $check == 'gallery') {
						if (file_exists(JPATH_SITE."/images/comprofiler/".$cb_avatar)) {
							$avatar = "images/comprofiler/".$cb_avatar;
						}else{
							$this->nophoto=true;
						}
					}else{
						$this->nophoto=true;
					}
				}
				 if ($this->config->get('use_jsoc') == 1 && $cb_avatar) {
					if (file_exists(JPATH_SITE."/".$cb_avatar)) {
							$avatar = JUri::root()."/".$cb_avatar;
							$this->nophoto=false;
						}else{
							$this->nophoto=true;
						}
				}
				if (!$avatar) $this->nophoto=true;
			}
			if(!empty($avatar)) $avatar = '<img class="avatar" id="t_avatar" align="left" src="'.JURI::base().'/'.$avatar.'" border="0" style="padding-right: 5px;" alt=""/>';
		return $avatar;
	}
	
	public function tmItemId()
	{
		$db = JFactory::getDBO();
         $query = $db->getQuery(true);
         $query->select('id');
         $query->from('`#__menu` AS `m`');
         $query->where('m.link="index.php?option=com_testimonials&view=testimonials" AND m.type="component" AND m.published<>-2 ');
         $query->order('m.id');
         $db->setQuery($query);
        $tmItemId = $db->loadResult();
        return $tmItemId;
	}
	
	function ResizeImage($photo, $ipath="images/testimonials"){
		$filepath = JPATH_BASE.DIRECTORY_SEPARATOR.$ipath.DIRECTORY_SEPARATOR.$photo;
		$filepath_thumb = JPATH_BASE.DIRECTORY_SEPARATOR."images/testimonials".DIRECTORY_SEPARATOR."th_".$photo;
				if (file_exists($filepath) &&  is_file($filepath))
				{
				list($width, $height, $type) = getimagesize($filepath);
						
					if ($width > TestimonialHelper::getSettings()->get('th_width',150)){
						$new_width = TestimonialHelper::getSettings()->get('th_width',150);
						$new_height = round(($height*$new_width)/$width);
			
						$image_p = imagecreatetruecolor($new_width, $new_height);					
						switch ($type)
						{
							case 3:
								$image = imagecreatefrompng($filepath);
							break;
							case 2:
								$image = imagecreatefromjpeg($filepath);
							break;
							case 1:
								$image = imagecreatefromgif($filepath);
							break;
							case 6:
								$image = imagecreatefromwbmp($filepath);
							break;				
						}	
			
						imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
	
						// saving
						switch($type)
						{
							case 3:
								@imagepng($image_p, $filepath_thumb);
							break;
							case 2:
								@imagejpeg($image_p, $filepath_thumb);
							break;
							case 1:
								@imagegif($image_p, $filepath_thumb);
							break;			
						}
					} else {
						copy($filepath, $filepath_thumb);
					}
				}
			return true;
	}
	
	function tmm_UTF8string_check($string) {
		return preg_match('%(?:
		[\xC2-\xDF][\x80-\xBF]
		|\xE0[\xA0-\xBF][\x80-\xBF]
		|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}
		|\xED[\x80-\x9F][\x80-\xBF]
		|\xF0[\x90-\xBF][\x80-\xBF]{2}
		|[\xF1-\xF3][\x80-\xBF]{3}
		|\xF4[\x80-\x8F][\x80-\xBF]{2}
		)+%xs', $string);
	}
	
	function tmm_string_substr($str, $offset, $length = NULL) {
		if (modTestimonialsHelper::tmm_UTF8string_check($str)) {
			return modTestimonialsHelper::tmm_UTF8string_substr($str, $offset, $length);
		} else {
			return substr($str, $offset, $length);
		}
	}
	
	function tmm_UTF8string_substr($str, $offset, $length = NULL) {
	   if ( $offset >= 0 && $length >= 0 ) {
	
		   if ( $length === NULL ) {
			   $length = '*';
		   } else {
			   if ( !preg_match('/^[0-9]+$/', $length) ) {
				   trigger_error('utf8_substr expects parameter 3 to be long', E_USER_WARNING);
				   return '';//FALSE;
			   }
	
			   $strlen = strlen(utf8_decode($str));
			   if ( $offset > $strlen ) {
				   return '';
			   }
	
			   if ( ( $offset + $length ) > $strlen ) {
				  $length = '*';
			   } else {
				   $length = '{'.$length.'}';
			   }
		   }
	
		   if ( !preg_match('/^[0-9]+$/', $offset) ) {
			   trigger_error('utf8_substr expects parameter 2 to be long', E_USER_WARNING);
			   return '';//FALSE;
		   }
	
		   $pattern = '/^.{'.$offset.'}(.'.$length.')/us';
	
		   preg_match($pattern, $str, $matches);
	
		   if ( isset($matches[1]) ) {
			   return $matches[1];
		   }
	
		   return '';//FALSE;
	
	   } else {
		   preg_match_all('/./u', $str, $ar);
		   if( $length !== NULL ) {
			   return join('',array_slice($ar[0],$offset,$length));
		   } else {
			   return join('',array_slice($ar[0],$offset));
		   }
	   }
	}
}
