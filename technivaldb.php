<?php
/**
 * technivaldb.php
 * Copyright: Zeust the Unoobian <2noob2banoob@gmail.com>, 2015
 *
 * This is the backend of the registration list. It probides a
 * database abstraction class.
 *
 * This file is part of Technival-inschrijflijst.
 *
 * Technival-inschrijflijst is free software: you can redistribute it
 * and/or modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation, either version 3 of
 * the License, or (at your option) any later version.
 *
 * DigitalLockin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with DigitalLockin. If not, see <http://www.gnu.org/licenses/>.
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
	private $tablename_occasions;
	private $tablespec =
		"id integer PRIMARY KEY, name text NOT NULL, occasion integer NOT NULL";
	private $tablespec_occasions =
		"occasion integer PRIMARY KEY, description text";
	private $con;           /** Database connection */
	private $st_insert;     /** Compiled participant insertion SQL statement */
	private $st_insert_occ; /** Compiled occasion insertion SQL statement */
	private $st_modify_occ; /** Compiled occasion insertion SQL statement */
	private $sql_fetch_all; /** Non-compiled all-participant-fetching SQL statement */
	private $st_fetch;      /** Compiled occasion-selective participant-fetching SQL statement */
	private $sql_fetch_occ; /** Non-compiled SQL statement to fetch occasions */
	private $st_fetch_one_occ; /** Compiled SQL statement to check definedness of one occasion */
	
	/**
	 * Constructor. Opens database connection and constructs SQL statement templates.
	 * Also creates database and tables if they do not exist.
	 * Parameters:
	 *   $db:       database name
	 *   $table:    table name
	 *   $recreate: (optional, default=false) if true, recreate tables even if they
	 *              already exist. This will delete all entries and update the table
	 *              specifications if they have changed in the code.
	 */
	public function __construct($db, $table, $recreate=false) {
		// Open database connection
		$this->dbname = $db;
		$this->open_con();
		// Other initialisation stuff
		$this->tablename = $this->con->quote($table);
		$this->tablename_occasions = $this->con->quote($table."_occ");
		$this->create_tables($recreate);
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
	 * Create the tables if they do not exist or if $ifexists (optional, default=false)
	 * is true. If the tables are created, they will be empty and according to the
	 * specification in the current version of the code.
	 */
	private function create_tables($ifexists=false) {
		if($ifexists)
			$this->con->exec("DROP TABLE IF EXISTS $this->tablename, $this->tablename_occasions");
		$sql_participants = "CREATE TABLE IF NOT EXISTS $this->tablename($this->tablespec)";
		$sql_occasions = "CREATE TABLE IF NOT EXISTS $this->tablename_occasions($this->tablespec_occasions)";
		if($this->con->exec($sql_participants) === false)
			$this->raise_error("Could not create participant table");
		if($this->con->exec($sql_occasions) === false)
			$this->raise_error("Could not create occasion table");
	}
	
	/**
	 * Finish SQL statement templates by inserting the table name in them,
	 * and precompile them if possible.
	 */
	public function construct_statements() {
		$this->sql_fetch_all = "SELECT name, occasion FROM $this->tablename";
		$this->st_insert = $this->con->prepare("INSERT INTO $this->tablename VALUES(NULL, ?, ?)");
		if(!$this->st_insert) $this->raise_error("Cannot compile insert statement");
		$this->st_fetch = $this->con->prepare("SELECT name, occasion FROM $this->tablename WHERE occasion=?");
		if(!$this->st_fetch) $this->raise_error("Cannot compile selective fetch statement");
		$this->sql_fetch_occ = "SELECT occasion, description FROM $this->tablename_occasions";
		$this->st_insert_occ = $this->con->prepare("INSERT INTO $this->tablename_occasions VALUES(?, ?)");
		$this->st_modify_occ = $this->con->prepare("UPDATE $this->tablename_occasions SET description=? WHERE occasion=?");
		$this-> st_fetch_one_occ = $this->con->prepare("SELECT occasion from $this->tablename_occasions WHERE occasion=?");
	}
	
	/**
	 * Raise an Exception with the message given in $msg. If a database
	 * connection is open, append the latest database error before raising.
	 */
	private function raise_error($msg) {
		// Prevent attacks on the user's browser through malevolent error messages'
		$msg = htmlspecialchars($msg);
		// Add information about the last encountered database error
		if($this->con) {
			$e = $this->con->errorInfo();
			$msg .= "<br/>\nSQL error ($e[0], $e[1]): $e[2]";
		}
		// Raise the error
		throw new Exception("<p>$msg</p>");
	}
	
	/**
	 * Insert a new entry into the database for person $name participating
	 * during occasion $occasion (optional, default=1).
	 */
	public function new_participant($name, $occasion=1) {
		// Prevent attacks on the user's browser through malevolent input
		if(!is_int($occasion)) $occasion = htmlspecialchars($occasion);
		$name = htmlspecialchars($name);
		// Insert the participant into the database
		if($this->st_insert->execute(array($name, $occasion)) === false)
			$this->raise_error("Cannot insert $name at occasion $occasion");
	}
	
	/**
	 * Get an array of occasions in the format [id:int -> description:str]
	 */
	public function get_occasions() {
		$res = $this->con->query($this->sql_fetch_occ) -> fetchAll();
		$occasions = Array();
		for($i=0; $i<count($res); $i++)
			$occasions[$res[$i]["occasion"]] = $res[$i]["description"];
		return $occasions;
	}
	
	/**
	 * Check if an occasion is already defined
	 */
	private function occasion_is_defined($occasion) {
		if(!$this->st_fetch_one_occ->execute(Array($occasion)))
			$this->raise_error("Cannot check if occasion is defined");
		return $this->st_fetch_one_occ->fetch() !== false;
	}
	
	/**
	 * Define a new occasion or redefine an existing one.
	 */
	public function define_occasion($id, $description) {
		// Prevent attacks on the user's browser through malevolent input
		if(!is_int($id)) $id = htmlspecialchars($id);
		$description = htmlspecialchars($description);
		// Insert or rename the occasion in the database
		if($this->occasion_is_defined($id)) {
			if($this->st_modify_occ->execute(array($description, $id)) === false)
				$this->raise_error("Cannot insert occasion $description ($id)");
		} else {
			if($this->st_insert_occ->execute(array($id, $description)) === false)
				$this->raise_error("Cannot insert occasion $description ($id)");
		}
	}
	
	/**
	 * Fetch all participants. Returns an array of participants.
	 * Each participant is an array with both numeric and
	 * associative keys. If an $occasion (optional, default=null) is
	 * provided, only participants in that occasion are returned.
	 * Otherwise all participants in all occasions are returned.
	 * The participant format is (name:str, occasion:int, occasionstr:str).
	 */
	public function get_participants($occasion=null) {
		if(is_null($occasion))
			$res = $this->con->query($this->sql_fetch_all);
		else {
			if(!$this->st_fetch->execute(array($occasion)))
				$this->raise_error("Selective query for occasion $occasion failed");
			$res = $this->st_fetch;
		}
		$participants = $res -> fetchAll();
		$occasions = $this->get_occasions();
		for($i=0; $i<count($participants); $i++) {
			$occ = $participants[$i]["occasion"];
			if(isset($occasions[$occ])) $occdesc = $occasions[$occ];
			else $occdesc = "";
			$participants[$i][2] = $participants[$i]["occasionstr"] = $occdesc;
		}
		return $participants;
	}
	
	/**
	 * Close database connection.
	 * Also undefines all compiled SQL statements.
	 */
	public function close_con(){
		$this->st_insert = null;
		$this->st_insert_occ = null;
		$this->st_modify_occ = null;
		$this->st_fetch = null;
		$this->st_fetch_one_occ = null;
		$this->con = null;
	}
}
?>
