<?php
/**
 * technivaldb.php
 * Author: Zeust the Unoobian <2noob2banoob@gmail.com>
 *
 * This is the backend of the registration list. It probides a
 * database abstraction class.
 *
 * This file is open-source and part of the open-source
 * Technival-inschrijflijst project. I have yet to decide upom
 * a license, but you are at the very least free to use this code
 * in your own code provided that your own code is also open-source
 * and compatible with the license I will choose in the future
 * (the most restrictive one I am considering is the GPL).
 */
/**
 * The TechnivalDB class provides an abstraction layer around the
 * database. The database structure is enforced by this class, the
 * database and table are automatically created if not yet existent
 * (with the option to delete and recreate the table), user input
 * is secured before being entered into queries and SQL query
 * details are hidden from the API-using code.
 */
Class TechnivalDB {
	private $dbname;
	private $tablename;
	private $tablespec =
		"id INTEGER PRIMARY KEY, name text NOT NULL, occasion integer NOT NULL";
	private $con;           /** Database connection */
	private $st_insert;     /** Compiled insertion SQL statement */
	private $sql_fetch_all; /** Non-compiled all-fetching SQL statement */
	private $st_fetch;      /** Compiled occasion-selective fetching SQL statement */
	
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
		$this->construct_statements();
	}
	
	/**
	 * (re-)Open database connection. If a connection is already open, it will be
	 * closed and a new one will be opened.
	 */
	public function open_con() {
		$this->con = new PDO("sqlite:$this->dbname.sqlite");
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
		if($this->con->exec($sql) === false) $this->raise_error("Could not create table");
	}
	
	/**
	 * Finish SQL statement templates by inserting the table name in them,
	 * and precompile them if possible.
	 */
	private function construct_statements() {
		$this->sql_fetch_all = "SELECT name, occasion FROM $this->tablename";
		$this->st_insert = $this->con->prepare("INSERT INTO $this->tablename VALUES(NULL, ?, ?)");
		if(!$this->st_insert) $this->raise_error("Cannot compile insert statement");
		$this->st_fetch = $this->con->prepare("SELECT name, occasion FROM $this->tablename WHERE occasion=?");
		if(!$this->st_fetch) $this->raise_error("Cannot compile selective fetch statement");
	}
	
	/**
	 * Raise an Exception with the message given in $msg. If a database
	 * connection is open, append the latest database error before raising.
	 */
	private function raise_error($msg) {
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
	public function new_participant($name, $occasion=1) {
		if($this->st_insert->execute(array($name, $occasion)) === false)
			$this->raise_error("Cannot insert $name at occasion $occasion");
	}
	
	/**
	 * Fetch all participants. Returns an array with both numeric and
	 * associative keys. If an $occasion (optional, default=null) is
	 * provided, only participants in that occasion are returned.
	 * Otherwise all participants in all occasions are returned.
	 */
	public function get_participants($occasion=null) {
		if(is_null($occasion))
			$res = $this->con->query($this->sql_fetch_all);
		else {
			if(!$this->st_fetch->execute(array($occasion)))
				$this->raise_error("Selective query for occasion $occasion failed");
			$res = $this->st_fetch;
		}
		return $res -> fetchAll();
	}
	
	/**
	 * Close database connection.
	 */
	public function close_con(){
		$this->con = null;
	}
}
?>
