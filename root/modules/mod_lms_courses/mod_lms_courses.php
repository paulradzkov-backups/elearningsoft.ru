<?php

if (!defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) { die( 'Restricted access' ); }

/* functions declaration */
if (!defined( '_JLMS_COURSES_MODULE_FUNCS' )) {
	/** ensure that functions are declared only once */
	define( '_JLMS_COURSES_MODULE_FUNCS', 1 );

	function JLMS_showHF_list($uc, &$params, $Itemid_lms){
		$paramlink = $params->get('link', 'index.php?option=com_joomla_lms&Itemid={Itemid}&task=subscription&course_id={course_id}&after_reg=1');
		$menulist = $params->get('menulist', 1);
		$lifecourse = $params->get('lifecourse', 1);
		$link = ($paramlink)?$paramlink:'index.php?option=com_joomla_lms&Itemid={Itemid}&task=subscription&course_id={course_id}&after_reg=1';
		$timecolumn = $params->get('timecolumn', 1);
		$maxsymbols = $params->get('maxsymbols', 0);
		$max_courses = intval($params->def('maxcourses', 10));
		$JLMS_CONFIG = & JLMSFactory::getConfig();
		?>
		<ul class="menu<?php echo $params->get( 'class_sfx' );?>">
		<?php
			$i = 0;
			$k = 1;
			foreach($uc as $uc1){
				$add_class = '';
				if ($JLMS_CONFIG->get('course_id') && $JLMS_CONFIG->get('course_id') == $uc1->course_id) {
					$add_class = 'active ';
				} elseif (isset($_REQUEST['id']) && isset($_REQUEST['task']) && ($_REQUEST['task'] == 'course_guest' || $_REQUEST['task'] == 'subscription') && $_REQUEST['id'] == $uc1->course_id) {
					$add_class = 'active ';
				}
				?>
				<li class="<?php echo $add_class;?>item">
				<?php
				if (class_exists('JFactory')) {
					$link = JRoute::_('index.php?option=com_joomla_lms&task=details_course&id='.$uc1->course_id.'&Itemid='.$Itemid_lms);
				} else {
					$link = sefRelToAbs('index.php?option=com_joomla_lms&task=details_course&id='.$uc1->course_id.'&Itemid='.$Itemid_lms);
				}
				?>
				<a href="<?php echo $link;?>">
					<span>
						<?php echo ($maxsymbols ? ( (strlen($uc1->course_name) > $maxsymbols) ? (substr($uc1->course_name,0,$maxsymbols).'...') : $uc1->course_name ) : $uc1->course_name);?>
					</span>
				</a>
				<?php
				if($timecolumn && isset($uc1->unixend) && $uc1->unixend){
					if(isset($uc1->expired_link)){
						$expired_link = '<a href="'.$uc1->expired_link.'">expired</a>';
						jlms_courses_showDaysLeft($expired_link);
					} else {
						$days = round((($uc1->unixend-time())/86400<1)?($uc1->unixend-time())/86400+1:($uc1->unixend-time())/86400);					
						$days = ($days == 1) ? $days.' day left' : $days.' days left';
						jlms_courses_showDaysLeft($days);
					}
				}
				?>
				</li>
				<?php
			}
		?>
		</ul>
		<?php
	}

	function JLMS_showHF($uc, &$params, $style=0, $Itemid_lms){
		$paramlink = $params->get('link', 'index.php?option=com_joomla_lms&Itemid={Itemid}&task=subscription&course_id={course_id}&after_reg=1');
		$menulist = $params->get('menulist', 1);
		$lifecourse = $params->get('lifecourse', 1);
		$link = ($paramlink)?$paramlink:'index.php?option=com_joomla_lms&Itemid={Itemid}&task=subscription&course_id={course_id}&after_reg=1';
		$timecolumn = $params->get('timecolumn',1);
		$maxsymbols = $params->get('maxsymbols',0);
		$max_courses = intval($params->def('maxcourses', 10));
		switch($style){
			case '0':
				?>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td nowrap="nowrap">
							<?php
							foreach($uc as $uc1){
								if (class_exists('JFactory')) {
									$link = JRoute::_('index.php?option=com_joomla_lms&task=details_course&id='.$uc1->course_id.'&Itemid='.$Itemid_lms);
								} else {
									$link = sefRelToAbs('index.php?option=com_joomla_lms&task=details_course&id='.$uc1->course_id.'&Itemid='.$Itemid_lms);
								}
								?>
								<a class="mainlevel<?php echo $params->get( 'class_sfx' );?>" href="<?php echo $link;?>">
									<?php echo ($maxsymbols ? ( (strlen($uc1->course_name) > $maxsymbols) ? (substr($uc1->course_name,0,$maxsymbols).'...') : $uc1->course_name ) : $uc1->course_name);?>
								</a>
								<?php
								if($timecolumn && isset($uc1->unixend) && $uc1->unixend){
									if(isset($uc1->expired_link)){
										$expired_link = '<a href="'.$uc1->expired_link.'">expired</a>';
										jlms_courses_showDaysLeft($expired_link);
									} else {
										$days = round((($uc1->unixend-time())/86400<1)?($uc1->unixend-time())/86400+1:($uc1->unixend-time())/86400);					
										$days = ($days == 1) ? $days.' day left' : $days.' days left';
										jlms_courses_showDaysLeft($days);
									}
								}
								?>
								<?php	
							}
							?>
						</td>
					</tr>
				</table>
				<?php
				break;
			case '1':
				?>
				<ul id="mainlevel<?php echo $params->get( 'class_sfx' );?>">
					<?php
					foreach($uc as $uc1){
					?>
					<li>
						<?php
						if (class_exists('JFactory')) {
							$link = JRoute::_('index.php?option=com_joomla_lms&task=details_course&id='.$uc1->course_id.'&Itemid='.$Itemid_lms);
						} else {
							$link = sefRelToAbs('index.php?option=com_joomla_lms&task=details_course&id='.$uc1->course_id.'&Itemid='.$Itemid_lms);
						}
						?>
						<a class="mainlevel<?php echo $params->get( 'class_sfx' );?>" href="<?php echo $link;?>">
							<span>
								<?php echo ($maxsymbols ? ( (strlen($uc1->course_name) > $maxsymbols) ? (substr($uc1->course_name,0,$maxsymbols).'...') : $uc1->course_name ) : $uc1->course_name);?>
							</span>
						</a>
						<?php
						if($timecolumn && isset($uc1->unixend) && $uc1->unixend){
							if(isset($uc1->expired_link)){
								$expired_link = '<a href="'.$uc1->expired_link.'">expired</a>';
								jlms_courses_showDaysLeft($expired_link);
							} else {
								$days = round((($uc1->unixend-time())/86400<1)?($uc1->unixend-time())/86400+1:($uc1->unixend-time())/86400);					
								$days = ($days == 1) ? $days.' day left' : $days.' days left';
								jlms_courses_showDaysLeft($days);
							}
						}
						?>
					</li>
					<?php
					}
					?>
				</ul>
				<?php
				break;	
		}
	}

	function JLMS_showVI($uc, &$params, $Itemid_lms){
		$paramlink = $params->get('link', 'index.php?option=com_joomla_lms&Itemid={Itemid}&task=subscription&course_id={course_id}&after_reg=1');
		$menulist = $params->get('menulist', 1);
		$lifecourse = $params->get('lifecourse', 1);
		$link = ($paramlink)?$paramlink:'index.php?option=com_joomla_lms&Itemid={Itemid}&task=subscription&course_id={course_id}&after_reg=1';
		$timecolumn = $params->get('timecolumn',1);
		$maxsymbols = $params->get('maxsymbols',0);
		$max_courses = intval($params->def('maxcourses', 10));
		?>
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<?php
			$k = 1;
			foreach($uc as $uc1){
				//if ($i == $max_courses) { break; }
				$link1 = str_replace('{Itemid}', $Itemid_lms, $link);
				$link1 = str_replace('{course_id}', $uc1->course_id, $link1);
				$class = ($menulist)?array('class="sectiontableentry'.($k).'"', ''):array('', 'class="mainlevel"');
				if (!isset($uc1->publish_start)) { $uc1->publish_start = 0; }
				if (!isset($uc1->publish_end)) { $uc1->publish_end = 0; }
				
				if (class_exists('JFactory')) {
					$link = JRoute::_('index.php?option=com_joomla_lms&task=details_course&id='.$uc1->course_id.'&Itemid='.$Itemid_lms);
				} else {
					$link = sefRelToAbs('index.php?option=com_joomla_lms&task=details_course&id='.$uc1->course_id.'&Itemid='.$Itemid_lms);
				}
				?>
				<tr>
					<td>
						<a class="mainlevel<?php echo $params->get( 'class_sfx' );?>" href="<?php echo $link;?>">
							<span>
								<?php echo ($maxsymbols ? ( (strlen($uc1->course_name) > $maxsymbols) ? (substr($uc1->course_name,0,$maxsymbols).'...') : $uc1->course_name ) : $uc1->course_name);?>
							</span>
						</a>
					</td>
					<?php
					if($timecolumn && isset($uc1->unixend) && $uc1->unixend){
						?>
						<td>
						<?php
						if($timecolumn && isset($uc1->unixend) && $uc1->unixend){
							if(isset($uc1->expired_link)){
								$expired_link = '<a href="'.$uc1->expired_link.'">expired</a>';
								jlms_courses_showDaysLeft($expired_link);
							} else {
								$days = round((($uc1->unixend-time())/86400<1)?($uc1->unixend-time())/86400+1:($uc1->unixend-time())/86400);					
								$days = ($days == 1) ? $days.' day left' : $days.' days left';
								jlms_courses_showDaysLeft($days);
							}
						}
						?>
						</td>
						<?php
					}
					?>
				</tr>
				<?php
				$k = 3 - $k;
			}
			?>
		</table>
		<?php
	}

	function jlms_courses_showDaysLeft($days_text){
		$days_text_out = '';
//		$days_text_out .= '<br />';
		$days_text_out .= '<small>';
		$days_text_out .= '(';
		$days_text_out .= $days_text;
		$days_text_out .= ')';
		$days_text_out .= '</small>';
		
		echo $days_text_out;
	}
	
	function jlms_courses_AllChilds($id, &$all_cats, $child_ids){
		foreach($all_cats as $cat){
			if($id == $cat->parent){
				$child_ids[] = $cat->id;
				$child_ids = jlms_courses_AllChilds($cat->id, $all_cats, $child_ids);
			} 
		}
		return $child_ids;
	}

}

$max_courses = intval($params->def('maxcourses', 10));
if (!$max_courses) {
	$max_courses = 1000;
}
if (!class_exists('JLMSFactory')) {
	require_once(JPATH_SITE . DS . "components" . DS . "com_joomla_lms" . DS . "includes" . DS . "classes" . DS . "lms.factory.php");
}
$JLMS_CONFIG = & JLMSFactory::getConfig();
$db = & JFactory::getDBO();
$user = & JFactory::getUser();

$uc = array();
$courses_teachstr = '0';
if ($max_courses) {
	$all_available_courses = $params->def('allavailablecourses', 1);
	$show_teacher_courses = $params->def('teachercourses', 1);
	$show_learner_courses = $params->def('learnercourses', 1);
	$lifecourse = $params->def('lifecourse', 1);
	$timecolumn = $params->def('timecolumn', 1);

	$query = "SELECT u.lms_usertype_id, t.roletype_id FROM #__lms_users as u, #__lms_usertypes as t"
	 . "\n WHERE u.user_id = '".$user->get('id')."' AND u.lms_usertype_id = t.id AND t.roletype_id <> 3"
	 . "\n ORDER BY t.roletype_id DESC LIMIT 0,1";
	$db->setQuery($query);
	$temp_role_type = 0;
	$ur = $db->loadObject();
	if(is_object($ur) && isset($ur->roletype_id)){
		$temp_role_type = $ur->roletype_id;
	}

	$query_where = "";

	if($all_available_courses){
		$query = "SELECT *"
		. "\n FROM #__lms_course_cats"
		. "\n ORDER BY id"
		;
		$db->setQuery($query);
		$all_cats = $db->loadObjectList();
		
		$restricted_where = "";
		$query = "SELECT a.group_id FROM #__lms_users_in_global_groups as a WHERE a.user_id = '".$user->get('id')."'";
		$db->setQuery($query);
		$user_group_ids = $db->loadResultArray();
		$restricted_where .= "\n AND ( restricted = 0";
		if (count($user_group_ids)) {
			$restricted_where .= "\n OR (restricted = 1 AND (groups LIKE '%|".$user_group_ids[0]."|%'";
			for($i=1;$i<count($user_group_ids);$i++) {
				$restricted_where .= "\n OR groups like '%|".$user_group_ids[$i]."|%'";
			}
			$restricted_where .=  "\n))";
		}
		$restricted_where .=  "\n)";
		
			
		$query = "SELECT *"
		. "\n FROM #__lms_course_cats"
		. "\n WHERE 1"
		. $restricted_where
		;
		$db->setQuery($query);
		$restricted_cats = $db->loadObjectList();
		
		$restricted_childs = array();
		foreach($restricted_cats as $restricted_cat){
			$restricted_childs[] = jlms_courses_AllChilds($restricted_cat->id, $all_cats, array($restricted_cat->id));
		}
		if(isset($restricted_childs) && count($restricted_childs)){
			$restricted_cat_ids = array();
			foreach($restricted_childs as $restricted_child){
				if(isset($restricted_child) && count($restricted_child)){
					$restricted_cat_ids = array_merge($restricted_cat_ids, $restricted_child);
				}
			}
			if(isset($restricted_cat_ids) && $restricted_cat_ids){
				$query_where .= "\n AND ls.cat_id IN (".implode(",", $restricted_cat_ids).")";
			}
		}

		//----------------------------------------------------------------------------
//		$query = "SELECT ls.id"
		$query = "SELECT ls.id as course_id, ls.*"
		. "\n FROM #__lms_courses as ls"
		. "\n WHERE  ls.published = 1"
		. "\n AND ( ((ls.publish_start = 1) AND (ls.start_date <= '".date('Y-m-d')."')) OR (ls.publish_start = 0) )"
		. "\n AND ( ((ls.publish_end = 1) AND (ls.end_date >= '".date('Y-m-d')."')) OR (ls.publish_end = 0) ) "
		. $query_where
		. "\n ORDER BY ".($JLMS_CONFIG->get('lms_courses_sortby', 0) ? "ls.ordering, ls.course_name, ls.id" : "ls.course_name, ls.ordering, ls.id")
		;
		$db->setQuery($query);
		$all_available_course = $db->loadObjectList();
		
		if(isset($all_available_course) && count($all_available_course)){
			$uc = $all_available_course;
		}
		//----------------------------------------------------------------------------
	}

	if ($show_teacher_courses) {
		/*if ($temp_role_type == 4) { // administrator role
		} elseif ($temp_role_type == 2) {// teacher role
		}*/
		if ($temp_role_type == 4) {
			$query = "SELECT distinct course_id FROM #__lms_user_courses";
		} else {
			if ($temp_role_type) {
				$query = "SELECT distinct course_id FROM #__lms_user_courses WHERE user_id = '".$user->get('id')."' AND role_id IN (1,4)";
			} else {
				$query = "SELECT distinct course_id FROM #__lms_user_courses WHERE user_id = '".$user->get('id')."' AND role_id IN (4)";
			}
		}
		$db->setQuery($query);
		$courses_teach = $db->LoadResultArray();
		
		if (!empty($courses_teach)) {
			$courses_teachstr = implode($courses_teach, ',');
			$query = "SELECT ls.id as course_id, ls.*"
			. "\n FROM #__lms_courses ls"
			. "\n WHERE ls.id IN (".$courses_teachstr.")"
//			. "\n AND ( ((ls.publish_start = 1) AND (ls.start_date <= '".date('Y-m-d')."')) OR (ls.publish_start = 0) )"
//			. "\n AND ( ((ls.publish_end = 1) AND (ls.end_date >= '".date('Y-m-d')."')) OR (ls.publish_end = 0) ) "
			. "\n ORDER BY ".($JLMS_CONFIG->get('lms_courses_sortby', 0) ? "ls.ordering, ls.course_name, ls.id" : "ls.course_name, ls.ordering, ls.id")
			;
			$db->setQuery($query);
			$teacher_courses = $db->loadObjectList();
			
			$tmp_uc = array();
			$i=0;
			if(count($uc)){
				foreach($uc as $ucitem){
					$tmp_uc[$i] = $ucitem;
					foreach($teacher_courses as $tc){
						if($ucitem->course_id == $tc->course_id){
							$tmp_uc[$i] = $tc;
						}
					}
					$i++; 
				}
			} else {
				foreach($teacher_courses as $tc){
					$tmp_uc[$i] = $tc;
					$i++; 
				}
			}
			
			if(isset($tmp_uc) && count($tmp_uc)){
				$uc = $tmp_uc;
			}
		}
	}
	
//	if ($max_courses > count($uc) && $show_learner_courses){
	if ($show_learner_courses){
		//published courses
		$query = "SELECT id FROM #__lms_courses as ls"
		. "\n WHERE 1"
		. "\n AND ls.published = 1"
		.(strlen($courses_teachstr) ? "\n AND id NOT IN ($courses_teachstr)" : "")
		. "\n AND ( ((ls.publish_start = 1) AND (ls.start_date <= '".date('Y-m-d')."')) OR (ls.publish_start = 0) )"
		. "\n AND ( ((ls.publish_end = 1) AND (ls.end_date >= '".date('Y-m-d')."')) OR (ls.publish_end = 0) ) "
		;
		$db->setQuery($query);
		$pubcourses = $db->loadResultArray();
		$pubcoursesstr = implode($pubcourses, ',');
		if (!empty($pubcourses)) {
			$pubcoursesstr = implode($pubcourses, ',');
			//get courses time for logged in user
			$query = "SELECT ls.id as course_id, ls.course_name, ls.paid, luig.*,"
			. "\n UNIX_TIMESTAMP(luig.start_date) AS unixstart, UNIX_TIMESTAMP(luig.end_date) AS unixend"
			. "\n FROM #__lms_users_in_groups luig"
			. "\n INNER JOIN #__lms_courses ls ON ls.id = luig.course_id"
			. "\n INNER JOIN #__users u ON u.id = luig.user_id"
			. "\n WHERE 1"
			. "\n AND luig.user_id = ".$user->get('id')
			. "\n AND luig.course_id IN ($pubcoursesstr)"
//			.($lifecourse ? "\n AND ( ((luig.publish_start = 1) AND (luig.start_date <= '".date('Y-m-d')."')) OR (luig.publish_start = 0) )" : "")
//			.($lifecourse ? "\n AND ( ((luig.publish_end = 1) AND (luig.end_date >= '".date('Y-m-d')."')) OR (luig.publish_end = 0) ) " : "")
			. "\n ORDER BY ".($JLMS_CONFIG->get('lms_courses_sortby', 0) ? "ls.ordering, ls.course_name, ls.id" : "ls.course_name, ls.ordering, ls.id")
			;
			$db->setQuery($query);
			$learner_courses = $db->loadObjectList();
			

			$unixnow = strtotime(date("Y-m-d"));		

			$tmp_learner_courses = array();
			$i=0;
			foreach($learner_courses as $lc){
				if(!$lifecourse && isset($lc->unixend) && $lc->unixend){
					if($timecolumn && $lc->unixend < $unixnow){
						$tmp_learner_courses[$i] = $lc;
						if(isset($lc->paid) && $lc->paid){
							$tmp_learner_courses[$i]->expired_link = $link = 'index.php?option=com_joomla_lms&task=subscription&course_id={course_id}&after_reg=1&Itemid={Itemid}';
						} else {
							$tmp_learner_courses[$i]->expired_link = $link = 'index.php?option=com_joomla_lms&task=details_course&cid={course_id}&Itemid={Itemid}';
						}
						$tmp_learner_courses[$i]->expired_link = str_replace('{course_id}', $lc->course_id, $tmp_learner_courses[$i]->expired_link);
						$tmp_learner_courses[$i]->expired_link = str_replace('{Itemid}', $lc->course_id, $tmp_learner_courses[$i]->expired_link);
						$tmp_learner_courses[$i]->expired_link = JRoute::_($tmp_learner_courses[$i]->expired_link);
						$i++;
					} else 
					if(isset($lc->unixend) && $lc->unixend && $lc->unixend >= $unixnow){
						$tmp_learner_courses[$i] = $lc;
						$i++;
					}
				} else 
				if($lifecourse){
					if($timecolumn && isset($lc->unixend) && $lc->unixend && $lc->unixend < $unixnow){
						$tmp_learner_courses[$i] = $lc;
						if(isset($lc->paid) && $lc->paid){
							$tmp_learner_courses[$i]->expired_link = $link = 'index.php?option=com_joomla_lms&task=subscription&course_id={course_id}&after_reg=1&Itemid={Itemid}';
						} else {
							$tmp_learner_courses[$i]->expired_link = $link = 'index.php?option=com_joomla_lms&task=details_course&cid={course_id}&Itemid={Itemid}';
						}
						$tmp_learner_courses[$i]->expired_link = str_replace('{course_id}', $lc->course_id, $tmp_learner_courses[$i]->expired_link);
						$tmp_learner_courses[$i]->expired_link = str_replace('{Itemid}', $lc->course_id, $tmp_learner_courses[$i]->expired_link);
						$tmp_learner_courses[$i]->expired_link = JRoute::_($tmp_learner_courses[$i]->expired_link);
						$i++;
					} else 
					if((isset($lc->unixend) && $lc->unixend && $lc->unixend >= $unixnow) || (isset($lc->unixend) && !$lc->unixend) || (!isset($lc->unixend))){
						$tmp_learner_courses[$i] = $lc;
						$i++;
					}
				}
			}
			
			if(isset($tmp_learner_courses) && count($tmp_learner_courses)){
				$learner_courses = array();
				$learner_courses = $tmp_learner_courses;
			}
			
			$tmp_uc = array();
			$i=0;
			if(count($uc)){
				foreach($uc as $ucitem){
					$tmp_uc[$i] = $ucitem;
					foreach($learner_courses as $lc){
						if($ucitem->course_id == $lc->course_id){
							$tmp_uc[$i] = $lc;
						}
					}
					$i++; 
				}
			} else {
				foreach($learner_courses as $lc){
					$tmp_uc[$i] = $lc;
					$i++; 
				}
			}
			
			if(isset($tmp_uc) && count($tmp_uc)){
				$uc = $tmp_uc;
			}
		}
	}
	$tmp_uc = array();
	$i=0;
	foreach($uc as $ucitem){
		if($i < $max_courses){
			$tmp_uc[$i] = $ucitem;
			$i++;
		} else {
			break;
		}
	}
	if(isset($tmp_uc) && count($tmp_uc)){
		$uc = $tmp_uc;
	}
}

	$Itemid_lms = $JLMS_CONFIG->get('Itemid');

	$paramlink = $params->def('link', 'index.php?option=com_joomla_lms&Itemid={Itemid}&task=subscription&course_id={course_id}&after_reg=1');
	$menulist = $params->def('menulist', 0);
	$lifecourse = $params->def('lifecourse', 1);
	$link = ($paramlink)?$paramlink:'index.php?option=com_joomla_lms&Itemid={Itemid}&task=subscription&course_id={course_id}&after_reg=1';
	$timecolumn = $params->def('timecolumn',1);
	$maxsymbols = $params->def('maxsymbols',0);
	
	//cache all courses titles into JLMSTitles object for lms router
	$lms_titles_cache = & JLMSFactory::getTitles();
	$lms_titles_cache->setArray('courses', $uc, 'course_id', 'course_name');

	switch ( $params->get('menulist', 0) ) {
		case '1':
			JLMS_showVI($uc, $params, $Itemid_lms);
			break;
		case '2':
			JLMS_showHF($uc, $params, 0, $Itemid_lms);
			break;
		case '3':
			JLMS_showHF($uc, $params, 1, $Itemid_lms);
			break;
		case '0':
		default:	
			JLMS_showHF_list($uc, $params, $Itemid_lms);
		break;	
	}
?>