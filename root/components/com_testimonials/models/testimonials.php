<?php
/**
* Testimonials Component for Joomla 3
* @package Testimonials
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');
 
/**
 * Testimonials Model
 */
class TestimonialsModelTestimonials extends JModelList
{
	
	
   /**
	 * Method to build an SQL query to load the list data
	 */
	protected function getListQuery()
	{		
		$Gparams = JFactory::getApplication()->getParams();
		
		$searchids = array();
		$tags = $Gparams->get('tags');
		$alltags = $Gparams->get('all_tags');
		if (sizeof($tags) && is_array($tags) && !$alltags)
		{
			foreach ( $tags as $tagid ) 
			{
				if ($tagid) $searchids[] = $tagid; 
			}
		}
		
		$params =  TestimonialHelper::getSettings();

		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		$tagquery = '(SELECT GROUP_CONCAT(`tag_name`," ") FROM `#__tm_testimonials_tags` WHERE id IN (SELECT id_tag
					FROM `#__tm_testimonials_conformity` WHERE id_ti=`t`.id) AND published=1)';
				     
		// Select required fields from the categories.
		$query->select('t.*');
		$query->select($tagquery.' AS `tags`');
		$query->from('`#__tm_testimonials` AS `t`');
		if ($params->get('use_cb'))
		{
			$query->select('compr.avatar');
			$query->join('LEFT', '#__comprofiler AS compr ON compr.user_id = t.user_id_t');
		}
		if ($params->get('use_jsoc'))
		{
			$query->select('jsoc.thumb AS avatar');
			$query->join('LEFT', '#__community_users AS jsoc ON jsoc.userid = t.user_id_t');
		}
		
		$query->group('t.id');
		
		$id = (int)JFactory::getApplication()->input->getInt('id');
		if (isset($id)) 
		{
			if ($id>0) $query->where('t.`id`='.$id);
		}
		
		$query->join('LEFT', '#__users AS u ON u.id = t.user_id_t');
		
				
		if (!JFactory::getUser()->authorise('core.publish', 'com_testimonials'))
		{
			$query->where('t.`published`=1');
		}
		if (sizeof($searchids)>0)
		{
			$query->join('LEFT', '#__tm_testimonials_conformity AS c ON t.id = c.id_ti');
			$query->where('c.`id_tag` IN ('.implode(',',$searchids).')');
		}
		
		if ($params->get('show_lasttofirst')==1)
		{
			$order = 't.ordering DESC';
		} else $order = 't.ordering ASC';
		$query->order($order);
				
		return $query;
	}

		
	public function getHtml()
	{
		$params =  TestimonialHelper::getSettings();
		$Gparams = JFactory::getApplication()->getParams();
		$tmpl = $Gparams->get('template');
		$template = $params->get('template');
		$template=$template?$template:'default';
		$template = $tmpl?$tmpl:$template;
		$template = (JFactory::getApplication()->input->getVar('temp')) ? JFactory::getApplication()->input->getVar('temp') : $template;
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$query->select('`html`');
		$query->from('`#__tm_testimonials_templates`');
		$query->where('`temp_name`='.$db->quote($template));
		$db->setQuery($query);
		return $db->loadResult();		
	}
	
	public function getCustomArray() 
	{
	    $result = array();
	    $db = JFactory::getDbo();
	    $query	= $db->getQuery(true);
	    $query->select('CONCAT("[",c.name,"]") AS `key`');
	    $query->from('`#__tm_testimonials_custom` AS c');
	    $query->where('c.`published`=1');
	    $query->order(' c.ordering ');
	    $db->setQuery($query);
	    $data = $db->loadAssocList();
	    foreach($data as $row){
		$result[] = $row['key'];
	    }
	    return $result;
	}
	
	 public function getCustomFields($id=0)
    {
    	$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$query->select('CONCAT("[",c.name,"]") AS `key`, f.value AS `value`, c.type');
		$query->from('`#__tm_testimonials_items_fields` AS f');
		$query->join('LEFT', '#__tm_testimonials_custom AS c ON c.id = f.field_id');
		$query->where('f.`item_id`='.$db->quote($id));
		$query->where('c.`published`=1');
		$query->order(' c.ordering ');

		$db->setQuery($query);
		return $db->loadObjectList();
    }

    public function getIntroText(){
	$params =  TestimonialHelper::getSettings();
	$result = $params->get('tm_intro_text');
	if(preg_match_all('|\[([^\]]+)_summary\]|i', $result, $matches)){
	    $tags = $params->get('tags');
	    $alltags = $params->get('all_tags');
	    $searchids = array();
	    if (sizeof($tags) && is_array($tags) && !$alltags){
		foreach ( $tags as $tagid ){
		    if ($tagid) $searchids[] = $tagid; 
		}
	    }
	    $db = $this->getDbo();
	    foreach($matches[1] as $fieldTag){
		$query	= $db->getQuery(true);
		$query->select('tf.value, tf.item_id');
		$query->from('#__tm_testimonials_items_fields tf');
		$query->join('INNER', '#__tm_testimonials_custom tc ON tc.id = tf.field_id');
		$query->join('INNER', '#__tm_testimonials t ON t.id = tf.item_id');
		if (sizeof($searchids)>0){
		    $query->join('INNER', '#__tm_testimonials_conformity AS c ON tf.item_id = c.id_ti');
		    $query->where('c.`id_tag` IN ('.implode(',',$searchids).')');
		}
		$query->where('tc.name='.$db->quote(trim($fieldTag)));
		$query->where('tc.published=1');
		$query->where('t.published=1');
		$query->group('t.id');
		$query2 = $db->getQuery(true);
		$query2->select('SUM( value ) AS votes_summary, COUNT( item_id ) AS total_votes');
		$query2->from("($query) AS tt");
		$db->setQuery($query2);
		$data = $db->loadObject();
		if($data->total_votes>0){
		    $rating = round($data->votes_summary/$data->total_votes);
		    $replace = '';
		    for($a=1;$a<6;$a++){
			$replace .= '<i class="star-rating-i-'.($rating>=$a ? 'on' : 'off').'">&nbsp&nbsp&nbsp&nbsp</i>';
		    }
		    $replace = '<span class="tm_stars" title="'.$data->total_votes.' '.($data->total_votes>1 ? JText::_('COM_TESTIMONIALS_VOTES') :JText::_('COM_TESTIMONIALS_VOTE') ).'">'.$replace.'</span>';
		    $result = str_ireplace('['.$fieldTag.'_summary]', $replace, $result);
		}else{
		    $result = str_ireplace('['.$fieldTag.'_summary]', '', $result);
		}
	    }
	}
	$result = JHTML::_( 'content.prepare', $result );
	return $result;
    }

}
