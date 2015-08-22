<?php
Class TechnivalDB {
	private $dbname;
	private $tablename;
	private $tablespec =
		"id INTEGER PRIMARY KEY, name text NOT NULL, occasion integer NOT NULL";
	private $con;
	private $st_insert;
	private $sql_read;
	
	public function __construct($db, $table, $recreate=false) {
		// Open database connection
		$this->dbname = $db;
		$this->con = new PDO("sqlite:".$this->dbname);
		if(!$this->con) throw new Exception("Could not open database: ".$error);
		$this->tablename = $this->con->quote($table);
		$this->create_table($recreate);
		$this->compile_statements();
	}
	
	private function create_table($ifexists=false) {
		if($ifexists) $this->con->exec("DROP TABLE IF EXISTS $this->tablename");
		$sql = "CREATE TABLE IF NOT EXISTS $this->tablename($this->tablespec)";
		if($this->con->exec($sql) === false) $this->report_error("Could not create table");
	}
	
	private function compile_statements() {
		$this->sql_read = "SELECT name, occasion FROM $this->tablename";
		$this->st_insert = $this->con->prepare("INSERT INTO $this->tablename VALUES(NULL, ?, ?)");
		if(!$this->st_insert) $this->report_error("Cannot compile insert statement");
	}
	
	private function report_error($msg) {
		if($this->con) {
			$e = $this->con->errorInfo();
			$msg .= "<br/>\nSQL error ($e[0], $e[1]): $e[2]";
		}
		throw new Exception("<p>$msg</p>");
	}
	
	public function insert($name, $occasion=1) {
		if($this->st_insert->execute(array($name, $occasion)) === false)
			$this->report_error("Cannot insert $name at occasion $occasion");
	}
	
	public function get_stuff() {
		return $this->con->query($this->sql_read) -> fetchAll();
	}
	
	public function close(){
		$this->con = null;
	}
}
?>
