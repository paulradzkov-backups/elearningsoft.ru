<?php

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class plgContentjlmsCoursesContentLoader extends JPlugin
{

	function plgContentjlmsCoursesContentLoader( &$subject, $params )
	{
		parent::__construct( $subject, $params );
	}

	function onPrepareContent( &$article, &$params, $limitstart )
	{
		$source_file_base 	= JURI::base();
		$plugin				=& JPluginHelper::getPlugin('content', 'jlmscoursescontentloader');
		$pluginParams		= new JParameter( $plugin->params );

		global $JLMS_CONFIG;
		$cur_course_id = 0;
		$is_joomlalms = false;
		if (is_object($JLMS_CONFIG) && method_exists($JLMS_CONFIG, 'get')) {
			$cur_course_id = intval($JLMS_CONFIG->get('course_id'));
			$is_joomlalms = true;
		}

		$p_courses_ids = $pluginParams->get('courses_ids', '');
		$p_resources_path = $pluginParams->get('resources_path', '');
		$p_resource_loader = 'courses.php';
		if (!$p_resources_path) {
			$p_resources_path = dirname(__FILE__)."/../courses/";
			if (!(file_exists($p_resources_path))) {
				$p_resources_path = '';
			}
		}
		$courses = array();
		if ($p_courses_ids) {
			$courses = explode(',', $p_courses_ids);
			if (count($courses)) {
				for ($i = 0, $n = count($courses); $i < $n; $i ++) {
					$courses[$i] = intval($courses[$i]);
				}
			}
			$courses = array_unique($courses);
		}
		if (!empty($courses) && $p_resources_path) {
			$non_ajax = true;
			if ($non_ajax && $is_joomlalms) {
				$document = &JFactory::getDocument();
				if (!defined('_JLMS_COURSESCONTENTLOADER_CSS_LOADED')) {
					$source_common_css_file = $p_resource_loader."/common/css/course_common.css";
					$internal_common_css_file = $p_resources_path."/common/css/course_common.css";
					if(file_exists($internal_common_css_file)){
						$document->addStyleSheet($source_file_base.$source_common_css_file);
					}
					if ($cur_course_id && in_array($cur_course_id, $courses)) {
						$source_common_css_file = $p_resource_loader."/course-".$cur_course_id."/css/local.css";
						$internal_common_css_file = $p_resources_path."/course-".$cur_course_id."/css/local.css";
						if(file_exists($internal_common_css_file)){
							$document->addStyleSheet($source_file_base.$source_common_css_file);
						}	
					}
					define('_JLMS_COURSESCONTENTLOADER_CSS_LOADED', 1);
				}
				if (!defined('_JLMS_COURSESCONTENTLOADER_JS_LOADED')) {
					JHTML::_('behavior.mootools');
					JHTML::_('behavior.tooltip');
					$source_common_js_file = $p_resource_loader."/common/js/common.js";
					$internal_common_js_file = $p_resources_path."/common/js/common.js";
					$source_js_text = "";
					$embed_javascript = false;
					$source_js_text .= "
					// <!--
					window.addEvent('domready', function(){
						//jlms_exec_js_start//";
					if(file_exists($internal_common_js_file)){
						$document->addScript($source_file_base.$source_common_js_file);
						$embed_javascript = true;
						$source_js_text .= "
						jlms_courses_common_init();";
					}
					if ($cur_course_id && in_array($cur_course_id, $courses)) {
						$source_common_js_file = $p_resource_loader."/course-".$cur_course_id."/js/init.js";
						$internal_common_js_file = $p_resources_path."/course-".$cur_course_id."/js/init.js";
						if(file_exists($internal_common_js_file)){
							$document->addScript($source_file_base.$source_common_js_file);
							$embed_javascript = true;
							$source_js_text .= "
						jlms_courses_local_init();";
						}
					}
					$source_js_text .= "
						//jlms_exec_js_end//
					});
					// -->
					";
					if ($embed_javascript) {
						$document->addScriptDeclaration($source_js_text);
					}
					define('_JLMS_COURSESCONTENTLOADER_JS_LOADED', 1);
				}
			}
		}
		if ($cur_course_id) {
			$text = $article->text;
			$text = str_replace('courses.php/course-local', 'courses.php/course-'.$cur_course_id, $text);
			$article->text = $text;
		}
	}
}
?>