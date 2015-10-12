<?php
class Manga
{
	public $id;
	private $sql;
	
	function __construct(mysqli $conn, $id = null) {
		if (!empty($id)) $this->id = intval($id);
		$this->sql = $conn;
	}
	
	public function setID($id) {
		$this->id = intval($id);
	}
	
	public function AddNew() {
		$newid;
		$this->sql->query('INSERT INTO '.CFG_MYSQL_PREFIX.'mangas VALUES ();');
		if ($newrow = $this->sql->query('SELECT LAST_INSERT_ID();')) {
			$result = $newrow->fetch_array();
			$newid = $result['LAST_INSERT_ID()'];
		}
		if (!empty($newid)) {
			$this->id = $newid;
			return $newid;
		} else {return false;}	
	}
	
	public function SetTitle($title) {
		$title = $this->sql->real_escape_string($title);
		
		if ($this->sql->query('UPDATE '.CFG_MYSQL_PREFIX.'mangas SET `title` = "'.$title.'" WHERE `id` = '.$this->id.';')) {
			return true;
		} else {return false;}
	}
}
?>