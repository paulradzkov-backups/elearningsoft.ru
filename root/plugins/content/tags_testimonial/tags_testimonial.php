<?php
/**
* Testimonials Plugin for Joomla 3
* @package Testimonials
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');
JLoader::register('TimgHelper', JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_testimonials' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'Timg.php');

class plgContentTags_testimonial extends JPlugin
{
	public function onContentPrepare($context, &$article, &$params, $limitstart)
	{
                $lang = JFactory::getLanguage();
                $lang->load('plg_content_tags_testimonial');
                
                $app = JFactory::getApplication();
		
		$artext = $returntext = $tag = $replace = '';
		$count = 0;
		$testimonials = array();
		
		$regex = '|{testimonial(.*?)}|si';
		
		//$regex = '/{testimonial\s+(\w+)\s*(\d*)\s*}/i';	
		
		if (!isset($article->text)) return true;
		
		if ( strpos( $article->text, 'testimonial' ) === false ) {
			return true;
		}
		if ($article->text) 
		{		
			$Cparams = $this->getSettings();
			
			$Cparams->set('addingbyuser', JFactory::getUser()->authorise('core.create', 'com_testimonials'));

			preg_match_all( $regex, $article->text, $matches );
			if (count($matches[1]))
			{
					$templ	= $this->params->get('template');
					if (!$templ) $templ='black';
							
					JLoader::register('TemplateHelper', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_testimonials' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'template.php');
					$template = new TemplateHelper($templ);
					$template->addFancyBox();
					$template->addRating();
					$template->html = $this->getHtml($templ);
			}
			for($i=0;$i<count($matches[1]);$i++) {	
				if(isset($matches[1][$i])) {
					$testimonialstr = trim($matches[1][$i]);
					$testiarray = explode(' ', $testimonialstr);
					if (is_array($testiarray))
					{
						$testiarray = array_reverse($testiarray);
						if (count($testiarray)>1)
						{
							$count = (int) $testiarray[0];
							$tag = '';
							
							for ( $j = sizeof( $testiarray )-1; $j>0; $j-- ) 
							{
								$tag.= $testiarray[$j].' ';
							}
							$testimonials = $this->getTestimonials($tag, $count);
							$replace = $this->wrapToHtml($testimonials, $template);
						}
					} 									
					//
					$article->text = str_replace($matches[0][$i],$replace,$article->text);
				}
			}	
		}
		return;
	}
	
	function wrapToHtml($arr, $template) 
	{
		$return = $html = '';
		$return = '<div id="testimonials-list">';
		$custom_array = $this->getCustomArray();
		for ( $i = 0, $n = sizeof( $arr ); $i < $n; $i++ ) 
		{
			$item = $arr[$i];
			$item->customs = $this->getCustomFields($item->id);
			$return.=$template->showTestimonial($item, $template->html, $custom_array, (( $i<$n-1 )?0:1), (($i+1)%2 == 0)?'odd':'', true);
		}
		return $return.'</div>';
	}
	
	public function getSettings()
        {
        	$db = JFactory::getDbo();
			$db->setQuery('SELECT `params` FROM #__tm_testimonials_settings LIMIT 1');
			$sets = $db->loadResult();
			$settings = new JRegistry();
 			$settings->loadString($sets);
 			return $settings;
        }
        
  	public function getCustomArray() 
	{
		$return = array();
		$db = JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('CONCAT("[",c.name,"]") AS `key`');
		$query->from('`#__tm_testimonials_custom` AS c');
		$db->setQuery($query);
		$result = $db->loadObjectList();
		foreach($result as $res) $return[] = $res->key;
		return $return;
	}      
	public function getHtml($template_name)
	{
		$db = JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('`html`');
		$query->from('`#__tm_testimonials_templates`');
		$query->where('`temp_name`='.$db->quote($template_name));
		$db->setQuery($query);
		return $db->loadResult();
	}
	

 	public function getCustomFields($id=0)
    {
    	$db = JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('CONCAT("[",c.name,"]") AS `key`, f.value AS `value`, c.type');
		$query->from('`#__tm_testimonials_items_fields` AS f');
		$query->join('LEFT', '#__tm_testimonials_custom AS c ON c.id = f.field_id');
		$query->where('f.`item_id`='.$db->quote($id));
		$db->setQuery($query);
		return $db->loadObjectList();
    }

	function getTestimonials($tag, $count = 1) {
		$database = JFactory::getDbo();
		$testimonials = array();
		$sql = "SELECT params  FROM #__tm_testimonials_settings LIMIT 1";
		$database->setQuery($sql);
		$settings = json_decode($database->loadResult());
		
		$query	= $database->getQuery(true);
		$query->select('DISTINCT t.*');
		$query->from('`#__tm_testimonials` AS `t`');
		$query->join('LEFT', '#__tm_testimonials_conformity AS c ON t.id = c.id_ti');
		$query->join('LEFT', '#__tm_testimonials_tags AS `tag` ON `tag`.id = c.id_tag');
		$query->where('`tag`.`tag_name`="'.trim($tag).'"');
		$query->where('t.`published`=1');
		
		if (isset($settings->use_cb))
		if ($settings->use_cb== 1) {
			$query->select('compr.avatar');
			$query->join('LEFT', '#__comprofiler AS compr ON compr.user_id = t.user_id_t');
		}
		if (isset($settings->use_jsoc))
		if ($settings->use_jsoc== 1) {
			$query->select('jsoc.thumb AS avatar');
			$query->join('LEFT', '#__community_users AS jsoc ON jsoc.userid = t.user_id_t');
		}
		$database->setQuery($query);
		
		$raw_testimonials = $database->loadObjectList();
	
		shuffle($raw_testimonials);
		for($i=0; $i<$count; $i++) {
			if(isset($raw_testimonials[$i])) {
			$testimonials[] = $raw_testimonials[$i];
			}
		}
		return $testimonials;
	}
}

?>