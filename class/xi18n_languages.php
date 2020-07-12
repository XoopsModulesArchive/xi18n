<?php
// xi18n_languages.php,v 1
//  ---------------------------------------------------------------- //
// Author: Bruno Barthez	                                           //
// ----------------------------------------------------------------- //

include_once XOOPS_ROOT_PATH."/class/xoopsobject.php";
/**
* xi18n_languages class.  
* $this class is responsible for providing data access mechanisms to the data source 
* of XOOPS user class objects.
*/


class xi18n_languages extends XoopsObject
{ 
	var $db;

// constructor
	function xi18n_languages ($id=null)
	{
		$this->db =& Database::getInstance();
		$this->initVar("lang_id",XOBJ_DTYPE_INT,null,false,10);
		$this->initVar("lang_title",XOBJ_DTYPE_TXTBOX, null, false);
		$this->initVar("dirname",XOBJ_DTYPE_TXTBOX, null, false);
		$this->initVar("code",XOBJ_DTYPE_TXTBOX, null, false);
		$this->initVar("codeml",XOBJ_DTYPE_TXTBOX, null, false);
		if ( !empty($id) ) {
			if ( is_array($id) ) {
				$this->assignVars($id);
			} else {
					$this->load(intval($id));
			}
		} else {
			$this->setNew();
		}
		
	}

	function load($id)
	{
		$sql = 'SELECT * FROM '.$this->db->prefix("xi18n_languages").' WHERE lang_id='.$id;
		$myrow = $this->db->fetchArray($this->db->query($sql));
		$this->assignVars($myrow);
		if (!$myrow) {
			$this->setNew();
		}
	}

	function getAllxi18n_languages($criteria=array(), $asobject=false, $sort="lang_id", $order="ASC", $limit=0, $start=0)
	{
		$db =& Database::getInstance();
		$ret = array();
		$where_query = "";
		if ( is_array($criteria) && count($criteria) > 0 ) {
			$where_query = " WHERE";
			foreach ( $criteria as $c ) {
				$where_query .= " $c AND";
			}
			$where_query = substr($where_query, 0, -4);
		} elseif ( !is_array($criteria) && $criteria) {
			$where_query = " WHERE ".$criteria;
		}
		if ( !$asobject ) {
			$sql = "SELECT lang_id FROM ".$db->prefix("xi18n_languages")."$where_query ORDER BY $sort $order";
			$result = $db->query($sql,$limit,$start);
			while ( $myrow = $db->fetchArray($result) ) {
				$ret[] = $myrow['xi18n_languages_id'];
			}
		} else {
			$sql = "SELECT * FROM ".$db->prefix("xi18n_languages")."$where_query ORDER BY $sort $order";
			$result = $db->query($sql,$limit,$start);
			while ( $myrow = $db->fetchArray($result) ) {
				$ret[] = new xi18n_languages ($myrow);
			}
		}
		return $ret;
	}
}
// -------------------------------------------------------------------------
// ------------------xi18n_languages user handler class -------------------
// -------------------------------------------------------------------------
/**
* xi18n_languageshandler class.  
* This class provides simple mecanisme for xi18n_languages object
*/

class Xoopsxi18n_languagesHandler extends XoopsObjectHandler
{

	/**
	* create a new xi18n_languages
	* 
	* @param bool $isNew flag the new objects as "new"?
	* @return object xi18n_languages
	*/
	function &create($isNew = true)	{
		$xi18n_languages = new xi18n_languages();
		if ($isNew) {
			$xi18n_languages->setNew();
		}
		return $xi18n_languages;
	}

	/**
	* retrieve a xi18n_languages
	* 
	* @param int $id of the xi18n_languages
	* @return mixed reference to the {@link xi18n_languages} object, FALSE if failed
	*/
	function &get($id)	{
			$sql = 'SELECT * FROM '.$this->db->prefix('xi18n_languages').' WHERE lang_id='.$id;
			if (!$result = $this->db->query($sql)) {
				return false;
			}
			$numrows = $this->db->getRowsNum($result);
			if ($numrows == 1) {
				$xi18n_languages = new xi18n_languages();
				$xi18n_languages->assignVars($this->db->fetchArray($result));
				return $xi18n_languages;
			}
				return false;
	}

/**
* insert a new xi18n_languages in the database
* 
* @param object $xi18n_languages reference to the {@link xi18n_languages} object
* @param bool $force
* @return bool FALSE if failed, TRUE if already present and unchanged or successful
*/
	function insert(&$xi18n_languages, $force = false) {
		Global $xoopsConfig;
		if (get_class($xi18n_languages) != 'xi18n_languages') {
				return false;
		}
		if (!$xi18n_languages->isDirty()) {
				return true;
		}
		if (!$xi18n_languages->cleanVars()) {
				return false;
		}
		foreach ($xi18n_languages->cleanVars as $k => $v) {
				${$k} = $v;
		}
		$now = "date_add(now(), interval ".$xoopsConfig['server_TZ']." hour)";
		if ($xi18n_languages->isNew()) {
			// ajout/modification d'un xi18n_languages
			$xi18n_languages = new xi18n_languages();
			$format = "INSERT INTO %s (lang_id, lang_title, dirname, code, codeml)";
			$format .= "VALUES (%u, %s, %s, %s, %s)";
			$sql = sprintf($format , 
			$this->db->prefix('xi18n_languages'), 
			$lang_id
			,$this->db->quoteString($lang_title)
			,$this->db->quoteString($dirname)
			,$this->db->quoteString($code)
			,$this->db->quoteString($codeml)
			);
			$force = true;
		} else {
			$format = "UPDATE %s SET ";
			$format .="lang_id=%u, lang_title=%s, dirname=%s, code=%s, codeml=%s";
			$format .=" WHERE lang_id = %u";
			$sql = sprintf($format, $this->db->prefix('xi18n_languages'),
			$lang_id
			,$this->db->quoteString($lang_title)
			,$this->db->quoteString($dirname)
			,$this->db->quoteString($code)
			,$this->db->quoteString($codeml)
			, $lang_id);
		}
		if (false != $force) {
			$result = $this->db->queryF($sql);
		} else {
			$result = $this->db->query($sql);
		}
		if (!$result) {
			return false;
		}
		if (empty($lang_id)) {
			$lang_id = $this->db->getInsertId();
		}
		$xi18n_languages->assignVar('lang_id', $lang_id);
		return true;
	}

	/**
	 * delete a xi18n_languages from the database
	 * 
	 * @param object $xi18n_languages reference to the xi18n_languages to delete
	 * @param bool $force
	 * @return bool FALSE if failed.
	 */
	function delete(&$xi18n_languages, $force = false)
	{
		if (get_class($xi18n_languages) != 'xi18n_languages') {
			return false;
		}
		$sql = sprintf("DELETE FROM %s WHERE lang_id = %u", $this->db->prefix("xi18n_languages"), $xi18n_languages->getVar('lang_id'));
		if (false != $force) {
			$result = $this->db->queryF($sql);
		} else {
			$result = $this->db->query($sql);
		}
		if (!$result) {
			return false;
		}
		return true;
	}

	/**
	* retrieve xi18n_languagess from the database
	* 
	* @param object $criteria {@link CriteriaElement} conditions to be met
	* @param bool $id_as_key use the UID as key for the array?
	* @return array array of {@link xi18n_languages} objects
	*/
	function &getObjects($criteria = null, $id_as_key = false)
	{
		$ret = array();
		$limit = $start = 0;
		$sql = 'SELECT * FROM '.$this->db->prefix('xi18n_languages');
		if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
			$sql .= ' '.$criteria->renderWhere();
		if ($criteria->getSort() != '') {
			$sql .= ' ORDER BY '.$criteria->getSort().' '.$criteria->getOrder();
		}
		$limit = $criteria->getLimit();
		$start = $criteria->getStart();
		}
		$result = $this->db->query($sql, $limit, $start);
		if (!$result) {
			return $ret;
		}
		while ($myrow = $this->db->fetchArray($result)) {
			$xi18n_languages = new xi18n_languages();
			$xi18n_languages->assignVars($myrow);
			if (!$id_as_key) {
				$ret[] =& $xi18n_languages;
			} else {
				$ret[$myrow['lang_id']] =& $xi18n_languages;
			}
			unset($xi18n_languages);
		}
		return $ret;
	}

	/**
	* count xi18n_languagess matching a condition
	* 
	* @param object $criteria {@link CriteriaElement} to match
	* @return int count of xi18n_languagess
	*/
	function getCount($criteria = null)
	{
		$sql = 'SELECT COUNT(*) FROM '.$this->db->prefix('xi18n_languages');
		if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
			$sql .= ' '.$criteria->renderWhere();
		}
		$result = $this->db->query($sql);
		if (!$result) {
			return 0;
		}
		list($count) = $this->db->fetchRow($result);
		return $count;
	} 

	/**
	* delete xi18n_languagess matching a set of conditions
	* 
	* @param object $criteria {@link CriteriaElement} 
	* @return bool FALSE if deletion failed
	*/
	function deleteAll($criteria = null)
	{
		$sql = 'DELETE FROM '.$this->db->prefix('xi18n_languages');
		if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
			$sql .= ' '.$criteria->renderWhere();
		}
		if (!$result = $this->db->query($sql)) {
			return false;
		}
		return true;
	}
}


?>