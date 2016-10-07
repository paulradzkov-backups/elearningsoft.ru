<?php
/**
 * Testimonials Component for Joomla 3
 * @package Testimonials
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

class com_testimonialsInstallerScript {

    function install($parent) {
        $db = JFactory::getDBO();

        @mkdir(JPATH_SITE . "/images/testimonials");
        @chmod(JPATH_SITE . "/images/testimonials", 0757);

        @mkdir(JPATH_SITE . "/images/com_testimonials");
        @chmod(JPATH_SITE . "/images/com_testimonials", 0757);

        echo '<font style="font-size:2em; color:#55AA55;" >' . JText::_('COM_TESTIMONIAL_INSTALL_TEXT') . '</font><br/><br/>';
        $this->greetingText();
    }

    function uninstall($parent) {
        echo '<p>' . JText::_('COM_TESTIMONIAL_UNINSTALL_TEXT') . '</p>';
    }

    function update($parent) {
        $errors = false;
        $db = JFactory::getDBO();
        $query = 'CREATE TABLE IF NOT EXISTS `#__tm_testimonials_dashboard_items` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `title` varchar(255) NOT NULL,
                `url` varchar(255) NOT NULL,
                `icon` varchar(255) NOT NULL,
                `published` tinyint(1) NOT NULL,
                PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;';
        $db->setQuery($query);
        $errors = $db->query();

        $query = 'SELECT * FROM `#__tm_testimonials_dashboard_items` WHERE 1;';
        $db->setQuery($query);
        $db->query();
        //print_r($count);die();
        if ($db->getNumRows() === 0) {
            $return = urlencode(base64_encode((string) JUri::getInstance('index.php?option=com_testimonials')));
            $query = 'INSERT INTO `#__tm_testimonials_dashboard_items` (`id`, `title`, `url`, `icon`, `published`) VALUES
                (1, \'Testimonials Management\', \'index.php?option=com_testimonials&view=topics\', \'/administrator/components/com_testimonials/assets/images/management48.png\', 1),
                (2, \'Testimonials Settings\', \'index.php?option=com_config&view=component&component=com_testimonials&return=' . $return . '\', \'/administrator/components/com_testimonials/assets/images/settings48.png\', 1),
                (3, \'Help\', \'http://www.joomplace.com/video-tutorials-and-documentation/joomla-testimonials/index.html?description.htm\', \'/administrator/components/com_testimonials/assets/images/help48.png\', 1);
                ';
            $db->setQuery($query);
            $errors = $db->query();
        }
        $templates = array(
            'default' => '<div class=\"testimonial_inner\" itemprop=\"reviews\" itemscope itemtype=\"http://schema.org/Review\">\r\n   <div class=\"testimonial_caption\"><h4 itemprop=\"name\">[caption]<!--x--></h4></div>\r\n   <div class=\"testimonial_image\" itemprop=\"image\">[avatar]</div>\r\n   <div class=\"testimonial_text\" itemprop=\"reviewBody\">[testimonial]\r\n       <div class=\"testimonial_text_separator\"><!--x--></div>\r\n   </div>\r\n   <div class=\"testimonial_author\" itemprop=\"author\">[author] [Website]<!--x--></div>\r\n   <div class=\"testimonial_author_descr\">[author_descr] <b>[Email]<!--x--></b></div>\r\n   <div class=\"tm_clr\"><!--x--></div>\r\n</div>',
            'black' => '<div class=\"testimonial_inner\">\\r\\n<div class=\"testimonial_caption \"><h4>[caption]<!--x--></h4></div>	\\r\\n<div class=\"testimonial_image\">[avatar]</div>\\r\\n<div class=\"testimonial_text\">[testimonial]\\r\\n<div class=\"testimonial_text_separator \"><!--x--></div>			\\r\\n</div>			\\r\\n<div class=\"testimonial_author \">[author]<!--x--></div>\\r\\n<div class=\"testimonial_author_descr \">[author_descr]<!--x--></div>\\r\\n<div class=\"tm_clr\"><!--x--></div>\\r\\n</div>',
            'black2' => '<div class=\"testimonial_inner\" itemprop=\"reviews\" itemscope itemtype=\"http://schema.org/Review\">\r\n<div class=\"testimonial_caption \"><h4 itemprop=\"name\">[caption]<!--x--></h4></div>\r\n<div class=\"testimonial_image\" itemprop=\"image\">[avatar]</div>\r\n<div class=\"testimonial_text\" itemprop=\"reviewBody\">[testimonial]\r\n<div class=\"testimonial_text_separator \"><!--x--></div>\r\n</div>\r\n<div class=\"testimonial_author \" itemprop=\"author\">[author]<!--x--></div>\r\n<div class=\"testimonial_author_descr \">[author_descr]<!--x--></div>\r\n<div class=\"tm_clr\"><!--x--></div>\r\n</div>',
            'blacktip' => '<div class=\"testimonial\" itemprop=\"reviews\" itemscope itemtype=\"http://schema.org/Review\">\r\n<div class=\"testimonial_inner\">\r\n<div class=\"testimonial_caption \"><h4 itemprop=\"name\">[caption]<!--x--></h4></div>\r\n<div class=\"testimonial_text\" itemprop=\"reviewBody\">[testimonial]</div>\r\n</div> 	\r\n</div>\r\n<div class=\"avatar_on\">\r\n<div class=\"testimonial_steam \"><!--x--></div>\r\n<div class=\"testimonial_image\" itemprop=\"image\">[avatar]</div>\r\n<div class=\"testimonial_author \" itemprop=\"author\">[author]<!--x--></div>\r\n<div class=\"testimonial_author_descr \">[author_descr]<!--x--></div>\r\n<div class=\"tm_clr\"><!--x--></div> 	\r\n</div>',
            'classic' => '<div class=\"testimonial\" itemprop=\"reviews\" itemscope itemtype=\"http://schema.org/Review\">		\r\n<div class=\"testimonial_caption\" itemprop=\"name\">[caption]</div>\r\n<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\r\n<tbody>\r\n<tr>\r\n<td width=\"61\" valign=\"top\" class=\"testimonial_text\" itemprop=\"reviewBody\">[avatar][testimonial]</td>		\r\n</tr>		\r\n<tr>			\r\n<td align=\"right\" valign=\"top\" class=\"testimonial_author\" itemprop=\"author\">[author]</td>		\r\n</tr>		\r\n<tr>			\r\n<td align=\"right\" valign=\"top\" class=\"testimonial_author\">[author_descr]</td>		\r\n</tr>	\r\n</tbody>\r\n</table>	\r\n</div>',
            'gray' => '<div class=\"testimonial_inner\" itemprop=\"reviews\" itemscope itemtype=\"http://schema.org/Review\">			\r\n   <div class=\"testimonial_caption \"><h4 itemprop=\"name\">[caption]<!--x--></h4></div>			\r\n   <div class=\"testimonial_image\" itemprop=\"image\">[avatar]</div>			\r\n   <div class=\"testimonial_text\" itemprop=\"reviewBody\">[testimonial]				\r\n     <div class=\"testimonial_text_separator \"><!--x--></div>			\r\n   </div>	\r\n   <div class=\"testimonial_author \" itemprop=\"author\">[author]<!--x--></div>			\r\n   <div class=\"testimonial_author_descr \">[author_descr]<!--x--></div>			\r\n   <div class=\"tm_clr\"><!--x--></div>		\r\n</div>',
            'business' => '<span itemprop=\"reviews\" itemscope itemtype=\"http://schema.org/Review\">\r\n<div class=\"testimonial_text\">\r\n<h4 class=\"testimonial_caption\" itemprop=\"name\">[caption]</h4>\r\n<blockquote>\r\n<p itemprop=\"reviewBody\">[testimonial]</p>\r\n</blockquote>\r\n</div>\r\n<div class=\"testimonial_sign\" itemprop=\"image\">\r\n[avatar]\r\n<cite>\r\n<span class=\"testimonial_author\" itemprop=\"author\">[author]</span>\r\n<span class=\"testimonial_author_descr\">[author_descr][Website]</span>\r\n</cite>\r\n</div>\r\n</span>',
            'Test1' => '<div class="testimonial_inner">
		<div class="testimonial_caption"><h4>[caption]<!--x--></h4></div>
		<div class="testimonial_image">[avatar]</div>
		<div class="testimonial_text"><p>[testimonial]</p>
			<div class="testimonial_text_separator"><!--x--></div>
		</div>
	<div class="testimonial_author">[author] [Website]<!--x--></div>
	<div class="testimonial_author_descr">[author_descr]  <b><!--x--></b></div>
	<div class="tm_clr"><!--x--></div></div>',
            'Test2' => '<div class="testimonial_inner">
		<div class="testimonial_caption"><h4>[caption]<!--x--></h4></div>
		<div class="testimonial_image">[avatar]</div>
		<div class="testimonial_bg_left"><img src="img/bg_left.png" alt=""></div>
		<div class="testimonial_text"><p>[testimonial]</p>
			<div class="testimonial_text_separator"><!--x--></div>
		</div>
	<div class="testimonial_bg_right"><img src="img/bg_right.png" alt=""></div>	
	<div class="testimonial_author">[author] [Website]<!--x--></div>
	<div class="testimonial_author_descr">[author_descr]  <b><!--x--></b></div>
	<div class="tm_clr"><!--x--></div></div>',
            'Test3' => '<div class="testimonial_inner">
		<div class="testimonial_caption"><h4>[caption]<!--x--></h4></div>
		<div class="testimonial_image">[avatar]</div>
		<div class="testimonial_text"><p>[testimonial]</p>
		<div class="testimonial_text_separator"><!--x--></div>
		</div>
	<div class="testimonial_author">[author] [Website]<!--x--></div>
	<div class="testimonial_author_descr">[author_descr]  <b><!--x--></b></div>
	<div class="tm_clr"><!--x--></div></div>',
            'Test4' => '<div class="testimonial_inner">
		<div class="testimonial_caption"><h4>[caption]<!--x--></h4></div>
		<div class="testimonial_image">[avatar]</div>
		<div class="testimonial_text"><p>[testimonial]</p>
		<div class="testimonial_text_separator"><!--x--></div>
		</div>
	<div class="testimonial_author">[author] [Website]<!--x--></div>
	<div class="testimonial_author_descr">[author_descr]  <b><!--x--></b></div>
	<div class="tm_clr"><!--x--></div></div>',
            'Test5' => '<div class="testimonial_inner">
		<div class="testimonial_caption"><h4>[caption]<!--x--></h4></div>
		<div class="testimonial_image">[avatar]</div>
		<div class="testimonial_text"><p>[testimonial]</p>
		<div class="testimonial_text_separator"><!--x--></div>
		</div>
	<div class="testimonial_author">[author] [Website]<!--x--></div>
	<div class="testimonial_author_descr">[author_descr]  <b><!--x--></b></div>
	<div class="tm_clr"><!--x--></div></div>',
            'Test6' => '<div class="testimonial_inner">
		<div class="testimonial_caption"><h4>[caption]<!--x--></h4></div>
		<div class="testimonial_image">[avatar]</div>
		<div class="testimonial_text"><p>[testimonial]</p>
		<div class="testimonial_text_separator"><!--x--></div>
		</div>
	<div class="testimonial_author">[author] [Website]<!--x--></div>
	<div class="testimonial_author_descr">[author_descr]  <b><!--x--></b></div>
	<div class="tm_clr"><!--x--></div></div>'
        );



        foreach ($templates as $temp_name => $temp_html) {
            $query = 'SELECT * FROM `#__tm_testimonials_templates` WHERE `temp_name` = "' . $temp_name . '";';
            $db->setQuery($query);
            $db->query();
            if ($db->getNumRows() === 0) {
                $query = 'INSERT INTO `#__tm_testimonials_templates` (`temp_name`, `html`) VALUES (\'' . $temp_name . '\',\'' . $temp_html . '\')';
                $db->setQuery($query);
                $errors = $db->query();
            }
        }

        $query = 'SELECT `params` FROM `#__tm_testimonials_settings` WHERE `id`=1 LIMIT 1';
        $db->setQuery($query);
        $params = $db->loadResult();
        if ($params) {
            $query = 'UPDATE `#__extensions` SET `params`=\'' . $params . '\' WHERE `name`="com_testimonials"';
            $db->setQuery($query);
            $db->query();
        }
        
        $params = JComponentHelper::getParams("com_testimonials");
        if(!$params->get('tm_version'))
            $params->set('tm_version','1.7.1 (build 004)');
        if(!$params->get('curr_date'))
            $params->set('curr_date','');


        $query = 'DROP TABLE IF EXISTS `#__tm_version`;';
        $db->setQuery($query);
        $errors = $db->query();

        if ($errors) {
            echo '<font style="font-size:2em; color:#55AA55;" >' . JText::_('COM_TESTIMONIAL_UPDATE_TEXT') . '</font><br/><br/>';
            $this->greetingText();
        } else {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }
    }

    function preflight($type, $parent) {
        
    }

    function postflight($type, $parent) {
        $db = JFactory::getDBO();
        $query = 'SELECT `params` FROM `#__tm_testimonials_settings` WHERE `id`=1 LIMIT 1';
        $db->setQuery($query);
        $params = $db->loadResult();
        if ($params) {
            $query = 'UPDATE `#__extensions` SET `params`=\'' . $params . '\' WHERE `name`="com_testimonials"';
            $db->setQuery($query);
            $db->query();
        }



        $sql = "SHOW COLUMNS FROM #__tm_testimonials";
        $db->setQuery($sql);
        $results = $db->loadObjectList();

        $check = array('date_added', 'images');
        $res = 0;
        foreach ($results as $column) {
            if (in_array($column->Field, $check)) {
                $res = 1;
            }
        }
        if ($res == 0) {
            $sql = "ALTER TABLE `#__tm_testimonials` ADD `date_added` VARCHAR( 255 ) NOT NULL , ADD `images` TEXT NOT NULL ";
            $db->setQuery($sql);
            $db->query();
        }

        $comment = 0;
        foreach ($results as $column) {
            if ($column->Field == 'comment') {
                $comment = 1;
            }
        }

        if ($comment == 0) {
            $sql = "ALTER TABLE `#__tm_testimonials` ADD `comment` TEXT NOT NULL";
            $db->setQuery($sql);
            $db->query();
        }



        $query = "SHOW INDEX FROM #__tm_testimonials_conformity WHERE Key_name = 'id_ti';";
        $db->setQuery($query);
        $indexCheck = $db->loadResult();
        if (!$indexCheck) {
            $query = "ALTER TABLE  `#__tm_testimonials_conformity` ADD INDEX id_ti (  `id_ti` ,  `id_tag` ) ;";
            $db->setQuery($query);
            $db->query();
        }
    }

    function greetingText() {
        ?>
        <table border="1" cellpadding="5" width="100%" style="background-color: #F7F8F9; border: solid 1px #d5d5d5; width: 100%; padding: 10px; border-collapse: collapse;">
            <tr>
                <td colspan="2" style="background-color: #e7e8e9;text-align:left; font-size:16px; font-weight:400; line-height:18px ">
                    <strong><img src="<?php echo JURI::root(); ?>/administrator/components/com_testimonials/assets/images/tick.png"> Getting started.</strong> Helpfull links:
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-left:20px">
                    <div style="font-size:1.2em">
                        <ul>
                            <li><a href="index.php?option=com_testimonials&task=sample_data">Install Sample Data</a></li>
                            <li><a href="index.php?option=com_testimonials&view=topics">Manage Testimonials</a></li>
                            <li><a href="http://www.joomplace.com/video-tutorials-and-documentation/joomla-testimonials/index.html?description.htm" target="_blank">Component's help</a></li>
                            <li><a href="http://www.joomplace.com/forum/testimonials-component/page-1.html" target="_blank">Support forum</a></li>
                            <li><a href="http://www.joomplace.com/support/helpdesk/department/presale-questions" target="_blank">Submit request to our technicians</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="background-color: #e7e8e9;text-align:left; font-size:16px; font-weight:400; line-height:18px ">
                    <strong><img src="<?php echo JURI::root(); ?>/administrator/components/com_testimonials/assets/images/tick.png"> Say your "Thank you" to Joomla community</strong>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-left:20px">
                    <div style="font-size:1.2em">
                        <p style="font-size:12px;">
                            <span style="font-size:14pt;">Say your "Thank you" to Joomla community</span> for WonderFull Joomla CMS and <span style="font-size:14pt;">help it</span> by sharing your experience with this component. It will only take 1 min for registration on <a href="http://extensions.joomla.org" target="_blank">http://extensions.joomla.org</a> and 3 minutes to write useful review! A lot of people will thank you!<br />
                            <a href="http://extensions.joomla.org/extensions/contacts-and-feedback/testimonials-a-suggestions/11304" target="_blank"><img src="http://www.joomplace.com/components/com_jparea/assets/images/rate-2.png" title="Rate Us!" alt="Rate us at Extensions Joomla.org"  style="padding-top:5px;"/></a>
                        </p>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="background-color: #e7e8e9;text-align:left; font-size:14px; font-weight:400; line-height:18px ">
                    <strong><img src="<?php echo JURI::root(); ?>/administrator/components/com_testimonials/assets/images/tick.png">Latest changes: </strong>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-left:20px" align="justify">
                    -------------------- 1.7.1 [January-2013] ---------------<br />
                    - release for Joomla! 3
                </td>
            </tr>
        </table>
        <?php
    }

}