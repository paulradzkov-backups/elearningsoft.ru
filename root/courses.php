<?php
/*
23 october 2009
 * * *
// Syntax: scorm_play.php/jlms/scorms/{uniquenumber}/{anyfolder}/filename.ext 
*/
@set_time_limit(3600);
//error_reporting(0);
if (!defined('_JEXEC')) { define( '_JEXEC', 1 ); }
if (!defined('JPATH_BASE')) { define('JPATH_BASE', dirname(__FILE__) ); }
if (!defined('DS')) { define( 'DS', DIRECTORY_SEPARATOR ); }
	$joomla_10 = false;
	$joomla_15 = false;
	$GLOBALS['joomla_old'] = true;
	if (file_exists(JPATH_BASE.'/includes/joomla.php') && file_exists(JPATH_BASE.'/globals.php') && file_exists(JPATH_BASE.'/configuration.php')) {
		$joomla_10 = true;
		$joomla_15 = false;
	}
	if (file_exists(JPATH_BASE.'/includes/joomla.php') && file_exists(JPATH_BASE.'/configuration.php') && file_exists(JPATH_BASE.'/libraries/loader.php')) {
		$joomla_15 = true;
		$GLOBALS['joomla_old'] = false;
	}
	if ($joomla_10) {
		if (!defined('_VALID_MOS')) { define( '_VALID_MOS', 1 ); }
		include_once( 'globals.php' );
		require_once( 'configuration.php' ); //load Joomla config variables
		if (file_exists($mosConfig_absolute_path . '/includes/mambo.php')) { //check if our site based on Mambo
			$isMambo = true;
			require_once( $mosConfig_absolute_path . '/includes/mambo.php' );
		} elseif (file_exists($mosConfig_absolute_path . '/includes/joomla.php')) {
			$isMambo = false; //we use Joomla!
			require_once( $mosConfig_absolute_path . '/includes/joomla.php' );
		}

		if (file_exists( $mosConfig_absolute_path . "/language/" . $mosConfig_lang . ".php" )) {
			include_once( $mosConfig_absolute_path . "/language/" . $mosConfig_lang . ".php" );
		} else {
			include_once( $mosConfig_absolute_path . "/language/english.php" );
		}
		$database = new database( $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix );
		$mainframe = new mosMainFrame( $database, '', '.' );
		$mainframe->initSession();
		$my = $mainframe->getUser();
		require_once($mainframe->getCfg('absolute_path')."/administrator/includes/pcl/pclzip.lib.php");
		require_once($mainframe->getCfg('absolute_path') . '/administrator/includes/pcl/pclerror.lib.php' );
		$joomla_15 = false;
	} elseif ($joomla_15) {
		require_once( JPATH_BASE .DS.'includes'.DS.'defines.php' );
		require_once( JPATH_BASE .DS.'includes'.DS.'framework.php' );
		//require_once( JPATH_BASE .DS.'includes'.DS.'helper.php' );

		//JDEBUG ? $_PROFILER->mark( 'afterLoad' ) : null;
		if (class_exists('JApplication')) {
			$mainframe =& JApplication::getInstance('site');
		} else {
			$mainframe =& JFactory::getApplication('site');
		}

		if (!class_exists('JPluginHelper')) { // J1.5RC4 compat
			$mainframe->initialise();
		}

		JPluginHelper::importPlugin('system');
		require_once(JPATH_BASE.DS.'administrator'.DS.'includes'.DS.'pcl'.DS.'pclzip.lib.php');
		require_once(JPATH_BASE.DS.'administrator'.DS.'includes'.DS.'pcl'.DS.'pclerror.lib.php' );
		$joomla_10 = false;
	}
	$GLOBALS['joomla_old'] = true;
	if (file_exists(JPATH_BASE.'/includes/joomla.php') && file_exists(JPATH_BASE.'/globals.php') && file_exists(JPATH_BASE.'/configuration.php')) {
		$joomla_10 = true;
	}
	if (file_exists(JPATH_BASE.'/includes/joomla.php') && file_exists(JPATH_BASE.'/configuration.php') && file_exists(JPATH_BASE.'/libraries/loader.php')) {
		$joomla_15 = true;
		$GLOBALS['joomla_old'] = false;
	}

	global $joomla_old;
	if ($joomla_old) {
		global $database;
	} else {
		$database = & JFactory::getDBO();
	}

	if (!$joomla_15) {
		//die('Joomla 1.5 only');
	}
	if (!defined('_JLMS_EXEC')) {
		define('_JLMS_EXEC', 1);
	}
	require_once( JPATH_BASE . '/components/com_joomla_lms/includes/jlms_docs_process.php' ); // function to detect mime-type
	if ($joomla_15) {
		$user =& JFactory::getUser();
		$user_id = $user->get('id');
	} else {
		$user_id = isset($my->id) ? $my->id : 0;
	}
	$user_id = intval($user_id);
	if (!$user_id) {
		die('USER: Restricted access');
	}


    $scorm_rel_path = scormplay_get_file_argument('scorm_play.php');
    $scorm_rel_path = urldecode($scorm_rel_path);
	// relative path must start with '/'
	if (!$scorm_rel_path) {
		die('No valid arguments supplied');
	} else if ($scorm_rel_path{0} != '/') {
		die('No valid arguments supplied');
	}
	
	//get config values
	$query = "SELECT * FROM `#__lms_config`";
	$database->SetQuery( $query );
	$lms_cfg = $database->LoadObjectList();
	$lms_cfg_scorms = '';
	foreach ($lms_cfg as $lcf) {
		if ($lcf->lms_config_var == 'scorm_folder') {
			$lms_cfg_scorms = $lcf->lms_config_value;
		}
	}

	$fullpath = dirname(__FILE__).'/courses'.$scorm_rel_path;
	$args = explode('/', trim($scorm_rel_path, '/'));
	
	
	if (!count($args)) { // always at least folder with unique name
        die('No valid arguments supplied');
    }
	/*if ($args[0] == 'jlms' && $args[1] == 'scorms') {
		
	} else {
		die('No valid arguments supplied');
	}*/
	//$args[2] - unique SCORM number
	$scorm_unique_identifier = $args[0];
	if ($scorm_unique_identifier == 'check_player_version') {
		die('1.0.6');
	}
	//TODO: check if it contains only numbers and letters and '_'

	//SCORM:     XXXXX(X symbols)_XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX(32 symbols)
	//ZIP:   XXXXX(X symbols)_ZIP_XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX(32 symbols)
	if (preg_match('/^[a-z0-9_-]+$/i', $scorm_unique_identifier)) {
		//ok
	} else {
		die('ERROR: invalid arguments');
	}

	$args2 = explode('_', trim($scorm_unique_identifier));
	$is_zip = false;
	$is_scorm = false;
	/*if (count($args2) == 2 && strlen($args2[1]) == 32 && strlen($args2[0]) > 3) {
		$is_scorm = true;
	} elseif (count($args2) == 3 && strtolower($args2[1]) == 'zip' && strlen($args2[2]) == 32) {
		$is_zip = true;
	} else {
		die('ERROR: unknown resource');
	}*/
	$course_id = 0;
	/*if ($is_scorm) {
		$query = "SELECT * FROM #__lms_scorm_packages WHERE folder_srv_name = ".$database->quote($scorm_unique_identifier);
		$database->SetQuery($query);
		$scorm_info = $database->LoadObjectList();
		if (count($scorm_info) == 1) {
			$course_id = $scorm_info[0]->course_id;
			//TODO: check if user has access to the course
		} else {
			die('SCORM: Restricted access');
		}
	} elseif ($is_zip) {
		$query = "SELECT * FROM #__lms_documents_zip WHERE zip_folder = ".$database->quote($scorm_unique_identifier);
		$database->SetQuery($query);
		$scorm_info = $database->LoadObjectList();
		if (count($scorm_info) == 1) {
			$course_id = $scorm_info[0]->course_id;
			//TODO: check if user has access to the course
		} else {
			die('DOCUMENT: Restricted access');
		}
	}*/
	$course_id = intval($course_id);

$allow_access = true;
	//check course access
	/*
	$allow_access = false;
	if ($course_id) {
		$query = "SELECT t.roletype_id FROM #__lms_users as u, #__lms_usertypes as t"
		 . "\n WHERE u.user_id = $user_id AND u.lms_usertype_id = t.id AND t.roletype_id IN (2,4)"
		 . "\n ORDER BY t.roletype_id DESC LIMIT 0,1";
		$database->SetQuery($query);
		$user_roletype = $database->LoadResult();
		if ($user_roletype == 4 ) {
			$allow_access = true;
		} else {
			$query = "SELECT user_id FROM #__lms_user_courses WHERE user_id = $user_id AND course_id = $course_id";
			$database->SetQuery($query);
			$user_course = $database->LoadResult();
			if ($user_course) {
				$allow_access = true;
			} else {
				$query = "SELECT a.course_id FROM #__lms_users_in_groups as a, #__lms_courses as b WHERE a.user_id = $user_id AND b.id = $course_id"
				. "\n AND a.course_id = b.id"
				. "\n AND ( b.published = 1"
				. "\n AND ( ((b.publish_start = 1) AND (b.start_date <= '".date('Y-m-d')."')) OR (b.publish_start = 0) )"
				. "\n AND ( ((b.publish_end = 1) AND (b.end_date >= '".date('Y-m-d')."')) OR (b.publish_end = 0) ) )"
	
				. "\n AND ( ((a.publish_start = 1) AND (a.start_date <= '".date('Y-m-d')."')) OR (a.publish_start = 0) )"
				. "\n AND ( ((a.publish_end = 1) AND (a.end_date >= '".date('Y-m-d')."')) OR (a.publish_end = 0) )"
				;
				$database->SetQuery($query);
				$user_learner = $database->LoadResult();
				if ($user_learner) {
					$allow_access = true;
				}
			}
		}
	} else {
		//TODO: check access to library
		$allow_access = true;
	}*/
	if (!$allow_access) {
		die('PERMISSIONS ERROR: Restricted access');
	}

	if (is_dir($fullpath)) {
		if (file_exists($fullpath.'/index.html')) {
			$fullpath = rtrim($fullpath, '/').'/index.html';
			$args[] = 'index.html';
		} else if (file_exists($fullpath.'/index.htm')) {
			$fullpath = rtrim($fullpath, '/').'/index.htm';
			$args[] = 'index.htm';
		} else if (file_exists($fullpath.'/Default.htm')) {
			$fullpath = rtrim($fullpath, '/').'/Default.htm';
			$args[] = 'Default.htm';
		} else {
			// security: do not return directory node!
			die('ERROR: requested resource not found');
		}
	}
    
    if (!file_exists($fullpath)) {
        die('ERROR: requested resource not found');
    } 

	//session_write_close(); // unlock session during fileserving
	$filename = $args[count($args)-1];
	scormplay_send_requested_file($fullpath, $filename);//, $lifetime, $CFG->filteruploadedfiles, false, $forcedownload);

function scormplay_readfile_chunked($filename, $retbytes=true) {
    $chunksize = 1*(1024*1024); // 1MB chunks - must be less than 2MB!
    $buffer = '';
    $cnt =0;
    $handle = fopen($filename, 'rb');
    if ($handle === false) {
        return false;
    }

    while (!feof($handle)) {
        @set_time_limit(60*60); //reset time limit to 60 min - should be enough for 1 MB chunk
        $buffer = fread($handle, $chunksize);
        echo $buffer;
        ob_flush();
        flush();
        if ($retbytes) {
            $cnt += strlen($buffer);
        }
    }
    $status = fclose($handle);
    if ($retbytes && $status) {
        return $cnt; // return num. bytes delivered like readfile() does.
    }
    return $status;
} 

function scormplay_mimeinfo($element, $filename) {
    static $mimeinfo = null;
    if (is_null($mimeinfo)) {
    	if (eregi('\.([a-z0-9]+)$', $filename, $match)) {
        	$mimeinfo = JLMS_GetMimeType($match[1]);
        	if (isset($mimeinfo['type'])) {
	            return $mimeinfo['type'];
	        }
        }
    }
    return 'application/octet-stream';
}

function scormplay_send_requested_file($path, $filename, $lifetime = 'default' , $filter=0, $pathisstring=false) {
	if ($lifetime === 'default') {
		$lifetime = 86400; // cache for 1 day
	}

	$mimetype     = scormplay_mimeinfo('type', $filename);
	$lastmodified = filemtime($path);
	$filesize     = filesize($path);

	//IE compatibiltiy HACK!
	if (ini_get('zlib.output_compression')) {
		@ini_set('zlib.output_compression', 'Off');
	}

	//try disabling automatic sid rewrite in cookieless mode
	@ini_set("session.use_trans_sid", "false");

	header('Last-Modified: '. gmdate('D, d M Y H:i:s', $lastmodified) .' GMT');

	// if user uses IE, urlencode the filename so that multibyte file name will show up correctly
	if (ereg('MSIE ([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT'])) {
		$filename = rawurlencode($filename);
	}

	@header('Content-Disposition: inline; filename="'.$filename.'"');

	@header('Cache-Control: max-age='.$lifetime);
	@header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .' GMT');
	@header('Pragma: ');

	if ($mimetype == 'text/plain') {
		@header('Content-Type: Text/plain; charset=utf-8');
	} else {
		@header('Content-Type: '.$mimetype);
	}
	@header('Content-Length: '.$filesize);
	while (@ob_end_flush()); //flush the buffers (save some memory)
	scormplay_readfile_chunked($path);
	die;
}

function scormplay_get_file_argument($scriptname) {
	global $_SERVER;
	$relativepath = false;
	if (!empty($_SERVER['PATH_INFO'])) {
		$path_info = $_SERVER['PATH_INFO'];
		// check if PATH_INFO works == must not contain the script name
		if (!strpos($path_info, $scriptname)) {
			$relativepath = $path_info;
			$relativepath = str_replace('\\\'', '\'', $relativepath);
			$relativepath = str_replace('\\"', '"', $relativepath);
			$relativepath = str_replace('\\', '/', $relativepath);
			$relativepath = ereg_replace('[[:cntrl:]]|[<>"`\|\':]', '', $relativepath);
			$relativepath = ereg_replace('\.\.+', '', $relativepath);
			$relativepath = ereg_replace('//+', '/', $relativepath);
			$relativepath = ereg_replace('/(\./)+', '/', $relativepath);
		}
	}
	return $relativepath;
}
?>