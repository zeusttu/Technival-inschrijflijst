<?php
Class TechnivalDB {
	private $dbname;
	private $tablename;
	private $tablespec =
		"id INTEGER PRIMARY KEY, name text NOT NULL, occasion integer NOT NULL";
	private $con;       /** Database connection */
	private $st_insert; /** Compiled insertion SQL statement */
	private $sql_read;  /** Non-compiled fetching SQL statement */
	
	/**
	 * Constructor. Opens database connection and constructs SQL statement templates.
	 * Also creates database and table if they do not exist.
	 * Parameters:
	 *   $db:       database name
	 *   $table:    table name
	 *   $recreate: (optional, default=false) if true, recreate table even if it already
	 *              exists. This will delete all entries and update the table
	 *              specification if it has changed in the code.
	 */
	public function __construct($db, $table, $recreate=false) {
		// Open database connection
		$this->dbname = $db;
		$this->open_con();
		// Other initialisation stuff
		$this->tablename = $this->con->quote($table);
		$this->create_table($recreate);
		$this->compile_statements();
	}
	
	/**
	 * (re-)Open database connection. If a connection is already open, it will be
	 * closed and a new one will be opened.
	 */
	public function open_con() {
		$this->con = new PDO("sqlite:".$this->dbname);
		if(!$this->con) throw new Exception("Could not open database: ".$error);
	}
	
	/**
	 * Create the table if it does not exist or if $ifexists (optional, default=false)
	 * is true. If the table is created, it will be empty and according to the
	 * specification in the current version of the code.
	 */
	private function create_table($ifexists=false) {
		if($ifexists) $this->con->exec("DROP TABLE IF EXISTS $this->tablename");
		$sql = "CREATE TABLE IF NOT EXISTS $this->tablename($this->tablespec)";
		if($this->con->exec($sql) === false) $this->report_error("Could not create table");
	}
	
	/**
	 * Finish SQL statement templates by inserting the table name in them,
	 * and precompile them if possible.
	 */
	private function compile_statements() {
		$this->sql_read = "SELECT name, occasion FROM $this->tablename";
		$this->st_insert = $this->con->prepare("INSERT INTO $this->tablename VALUES(NULL, ?, ?)");
		if(!$this->st_insert) $this->report_error("Cannot compile insert statement");
	}
	
	/**
	 * Raise an Exception with the message given in $msg. If a database
	 * connection is open, append the latest database error before raising.
	 */
	private function report_error($msg) {
		if($this->con) {
			$e = $this->con->errorInfo();
			$msg .= "<br/>\nSQL error ($e[0], $e[1]): $e[2]";
		}
		throw new Exception("<p>$msg</p>");
	}
	
	/**
	 * Insert a new entry into the database for person $name participating
	 * during occasion $occasion (optional, default=1).
	 */
	public function insert($name, $occasion=1) {
		if($this->st_insert->execute(array($name, $occasion)) === false)
			$this->report_error("Cannot insert $name at occasion $occasion");
	}
	
	/**
	 * Fetch all participants. Returns an array with both numeric and
	 * associative keys.
	 */
	public function get_stuff() {
		return $this->con->query($this->sql_read) -> fetchAll();
	}
	
	/**
	 * Close database connection.
	 */
	public function close(){
		$this->con = null;
	}
}
?>
