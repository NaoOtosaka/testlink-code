<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/ 
 *
 * Filename $RCSfile: int_eventum.php,v $
 *
 * @version $Revision: 1.2 $
 * @modified $Date: 2007/12/12 18:22:06 $ $Author: havlat $
 *
 * @author Stefan Stefanov
 *
 * Constants used throughout TestLink are defined within this file
 * they should be changed for your environment
 */

/** Interface name */
define('BUG_INTERFACE_CLASSNAME',"eventumInterface");

class eventumInterface extends bugtrackingInterface
{
	//members to store the bugtracking information
	var $m_dbHost = BUG_TRACK_DB_HOST;
	var $m_dbName = BUG_TRACK_DB_NAME;
	var $m_dbUser = BUG_TRACK_DB_USER;
	var $m_dbPass = BUG_TRACK_DB_PASS;
	var $m_dbType = BUG_TRACK_DB_TYPE;
	var $m_showBugURL = BUG_TRACK_HREF;
	var $m_enterBugURL = BUG_TRACK_ENTER_BUG_HREF;
	
	/**
	 * Return the URL to the bugtracking page for viewing 
	 * the bug with the given id. 
	 *
	 * @param int id the bug id
	 * 
	 * @return string returns a complete URL to view the bug
	 **/
	function buildViewBugURL($id)
	{
		return $this->m_showBugURL.urlencode($id);
	}
	
	/**
	 * Returns the status of the bug with the given id
	 * this function is not directly called by TestLink. 
	 *
	 * @return string returns the status of the given bug (if found in the db), or false else
	 **/
	function getBugStatus($id)
	{
		if (!$this->isConnected())
			return false;

		$status = false;
		
	$query = "SELECT eventum_status.sta_is_closed as status FROM eventum_status INNER JOIN eventum_issue ON eventum_issue.iss_sta_id = eventum_status.sta_id WHERE eventum_issue.iss_id='" . $id."'";
		
		$result = $this->m_dbConnection->exec_query($query);
		if ($result)
		{
			$status = $this->m_dbConnection->fetch_array($result);
			if ($status)
			{
				$status = $status['status'];
			}	
			else
				$status = null;
		}
		return $status;
		
	}
		
	/**
	 * Returns the status in a readable form (HTML context) for the bug with the given id
	 *
	 * @param int id the bug id
	 * 
	 * @return string returns the status (in a readable form) of the given bug if the bug
	 * 		was found , else false
	 **/
	function getBugStatusString($id)
	{
		
		$status = $this->getBugStatus($id);
		
		$str = htmlspecialchars($id);
		
		//if the bug wasn't found the status is null and we simply display the bugID
		if ($status !== false)
		{
			//the status values depends on your eventum configuration at config_inc.php in $g_status_enum_string, 
			//below is the default:
			//'0 : discovery, 0 : requirements, 0 : implementation, 0 : evaluation and testing, 1 : released,1 :  killed'
			//strike through all bugs that have a killed or released status.. 

			if ($status == '1')
				$str = "<del>".$id."</del>";	
		}
		
		return $str;
	}
	/**
	 * Fetches the bug summary from the eventum db
	 *
	 * @param int id the bug id
	 * 
	 * @return string returns the bug summary if bug is found, else false
	 **/
	function getBugSummaryString($id)
	{
		if (!$this->isConnected())
			return false;

		$status = null;
		$query = "SELECT iss_summary as summary FROM eventum_issue WHERE iss_id='".$id."'";
		
		$result = $this->m_dbConnection->exec_query($query);
		if ($result)
		{
			$summary = $this->m_dbConnection->fetch_array($result);

			if ($summary)
				$summary = $summary['summary'];
			else
				$summary = null;
		}
		return $summary;
	}

  /**
	 * checks a bug id for validity  
	 * 
	 * @return bool returns true if the bugid has the right format, false else
	 **/
	function checkBugID($id)
	{
	  $status_ok=1;	
	  $ereg_forbidden_chars='[a-zA-Z,$-+]';
 		if (eregi($ereg_forbidden_chars, $id))
		{
			$status_ok=0;	
		} 	
    else 
    {
      $status_ok=(intval($id) > 0);	
    }
		return $status_ok;
	}	

  /**
	 * checks is bug id is present on BTS
	 * 
	 * @return bool 
	 **/
	function checkBugID_existence($id)
	{
	  $status_ok=0;	
	
		$query = "SELECT eventum_status.sta_title as status FROM eventum_status INNER JOIN eventum_issue ON eventum_issue.iss_sta_id = eventum_status.sta_id WHERE eventum_issue.iss_id='" . $id."'";
	
		$result = $this->m_dbConnection->exec_query($query);
		if ($result && ($this->m_dbConnection->num_rows($result) == 1) )
		{
			$status_ok = 1;    
		}
		
		return $status_ok;
	}	
	

}
?>