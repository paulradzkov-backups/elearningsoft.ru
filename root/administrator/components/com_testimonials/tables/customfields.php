<?php
/**
* Testimonials Component for Joomla 3
* @package Testimonials
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.database.table');
 
/**
 * Customfields Table class
 */
class TestimonialsTableCustomfields extends JTable
{
        /**
         * Constructor
         *
         * @param object Database connector object
         */
        function __construct(&$db) 
        {
                parent::__construct('#__tm_testimonials_items_fields', 'item_id', $db);
        }
        
    	        
	     public function store($id, $fields=array())
		{
			parent::delete($id);
			$query = $this->_db->getQuery(true);
			foreach($fields[0] as $key=>$c)
			{
				if ($c)
				{
					$query = "INSERT INTO #__tm_testimonials_items_fields (`field_id`, `item_id`, `value`) " .
							" VALUES (".$key.", ".$id.", ".$this->_db->Quote($c).")";
					$this->_db->setQuery($query);
					$this->_db->query($query);
				}
			}
		}
}