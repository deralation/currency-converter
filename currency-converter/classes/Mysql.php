<?php
/*
 * MySQL class
 * for MySQLi Extension of PHP
 * @package contentmankit
 */

class MySQL {
	private $databaseNamePrefix;
	private $databaseName;
	private $tableName;
	
	private $username;
	private $password;
	
	private $host;
	private $port=3306;

	private $mysqli;

	private $lastInsertID;
    private $results;
	private $error;
	
	private $connected = false;

	static $queryCount = 0;
	static $cachedCount = 0;

	public function __construct() {
		$this->databaseNamePrefix = "test_";
		$this->host = MYSQLHOST;
	}
	
	/*
	 * Sets database hostname
	 * @param string $host server hostname
	 * @return bool
	 */
	public function setHost($host="localhost") {
		$this->host = $host;
		return true;
	}
	
	/*
	 * Sets database server port
	 * @param string $port server hostname
	 * @return bool
	 */
	public function setPort($port="3306") {
		$this->port = $port;
	}
	
	/*
	 * Sets database table
	 * @param string $name name of the table
	 * @return bool 
	 */
	public function setTable($name) {
		$this->tableName = $name;
		return true;
	}
	
	/*
	 * Sets database for queries
	 * @param string $name name of the database
	 * @return bool 
	 */
	public function setDatabase($name) {
		
		if(!isset($this->host) || !isset($this->username) || !isset($this->password)) {
			$this->error = "Please first set host, username and password";
			return false;
		}
		
		if(isset($this->databaseNamePrefix))
			$this->databaseName = $this->databaseNamePrefix.$name;	
		else
			$this->databaseName = $name;	

        if($this->connected)
        {	
            if(@mysqli_close($this->mysqli)) {
                $this->connected = false;
                $this->results = null;
                $this->connect();
            }
        } else {
            $this->connect();
        }
		
		return true;
	}
	
	/*
	 * Sets database user for connection
	 * @param string $user name of the user
	 * @return bool 
	 */
	public function setUser($user) {
		$this->username = $user;
		return true;
	}
	
	/*
	 * Sets database password for connection
	 * @param string $password password for the database connection
	 * @return bool 
	 */
	public function setPassword($password) {
		$this->password = $password;
		return true;
	}
	
	/*
	 * Connects to MySQL intances with user and object details set before calling this
	 * @return bool 
	 */
	public function connect() {
		$this->mysqli = @new mysqli($this->host, $this->username, $this->password, $this->databaseName, $this->port);
        if($this->mysqli->connect_errno) {
        	$this->error = "#".$this->mysqli->connect_errno.": ".$this->mysqli->connect_error;
			throw new Exception("Cannot connect to MySQL database: ".$this->error);
			return false;
        }
		$this->mysqli->set_charset('utf8');
		$this->connected = true;
		return true;
	}
	/*
	 * Inserts row in MySQL tbale
	 * @param string $tableName MySQL table name
	 * @param array $data Data to be inserted
	 * @return int get insert ID is returned 
	 */
	public function insert($tableName,$data) {

		if($this->host != MYSQLHOST) {
			$this->setHost(MYSQLHOST);
			$this->connect();
		}

		$success = false;
        $query = 'INSERT';
	
		$query.= ' INTO '.$tableName;

		if(is_array($data)) { 
			$columns = array();
			$values = "";
			if(count($data)>0) {
				$counter = 0;
				foreach($data as $key=>$value) {
					$columns[] = $key;
					if($counter>0) $values.=",";
					if($value===null) $values.= 'null';
					else $values.= '"'.$value.'"';
					$counter++;
				}
				
				$query.= "(".implode(', ',$columns).")";
				$query.= " VALUES (".$values.")";
                
				if(!$this->mysqli->query($query)){
					$this->error = $this->mysqli->error;
					throw new Exception($this->mysqli->error);
					return false;
				} 
				
			} else {
				throw new Exception("Empty array provided for database insert operation.");
				return false;
			}
		} else {
			throw new Exception("Provided data for insert operation is not an array.");
			return false;
		}

		$this->lastInsertID = $this->mysqli->insert_id;
		$this->affectedRowCount = mysqli_affected_rows($this->mysqli);
		return true;
	}

    public function getInsertID() {
		return $this->lastInsertID;
	}

    public function get($tableName,$id){
        if($this->host != MYSQLHOST) {
			$this->setHost(MYSQLHOST);
			$this->connect();
		}

        $query = "SELECT * ";
        $query.= "FROM ".$tableName." ";
        $query.= "WHERE id=".$id;

        if(!$result = $this->mysqli->query($query)){
            $this->error = $this->mysqli->error;
            throw new Exception($this->mysqli->error);
            return false;
        } 

        $this->results = mysqli_fetch_all($result,MYSQLI_ASSOC);
        $this->affectedRowCount = mysqli_affected_rows($this->mysqli);
		mysqli_free_result($result);
		return $this->results;
    }
}
?>