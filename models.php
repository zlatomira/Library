<?php
include "autoload.php";

class Database {
  /**
     * Connection
     * @var type 
     */
    private static $conn;
    private static $db_conn;
    private static $db_conn_library;
    private $host;
    private $dbname;
    private $username;
    private $password;

    public function __construct() {
        $this->host = env('DB_HOST');
        $this->username = env('DB_USERNAME');
        $this->password = env('DB_PASSWORD');
    }

    /**
     * Connect to the database postgres
     * @return \connection
     * @throws \Exception
     */
    public function load_data() {
    	  if (is_null(static::$db_conn)) {
        //connect to the postgresql database
    		try {
		    		static::$db_conn = pg_connect("host = $this->host dbname = postgres user = $this->username password = $this->password");
		    		$this->createDatabaseIfNotExists();
		    		$this->createTableIfNotExists();
		    	}  catch (Exception $e) {
		    		echo $e->getMessage();
		    	}
		    }
    }
    /**
    /**
     * Connect to the database library
     * @return \connection
     * @throws \Exception
     */
    public function connectToLibraryDB() {
    	try {
        static::$db_conn_library = pg_connect("host = $this->host dbname = library user = $this->username password = $this->password");

    	}  catch (Exception $e) {
    		echo $e->getMessage();
    	}
    }

    public function createDatabaseIfNotExists() {
        // check if library database exists
        $dbquery = pg_query(static::$db_conn, "SELECT 1 from pg_database WHERE datname='library'");
        // Get the result
        $dbexists = pg_fetch_array($dbquery);
        // if the query doen't return a result
        // then create the database
        if (empty($dbexists)) {
            $libdatabase =  $dbquery = pg_query(static::$db_conn, "CREATE DATABASE library WITH OWNER = postgres ENCODING = 'UTF8' LC_COLLATE = 'en_US.UTF-8' LC_CTYPE = 'en_US.UTF-8' TABLESPACE = pg_default CONNECTION LIMIT = -1 template 'template0'");
            $status = pg_result_status($libdatabase);
            if ( pg_result_status($libdatabase) == 1) {
                //connect to the library database
                 $this->connectToLibraryDB();
            }
           
        } else {
            //connect to the library database
            $this->connectToLibraryDB();
        }
    }

    public function createTableIfNotExists() {
          $tablequery = pg_query(static::$db_conn_library, "SELECT to_regclass('public.books')");
          $table_exists = pg_fetch_row($tablequery);
          if (!in_array("books", $table_exists)) {
          	// if table books doesn't exist then create it
            $newtable = pg_query(static::$db_conn_library, "CREATE TABLE books (unique_value text PRIMARY KEY, author text, title text, created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP)");
          }
    }

    /**
     * Save books into the books table
     */
    public function save($arr) {
        if (is_null(static::$db_conn_library)) {
            static::$db_conn_library = pg_connect("host = $this->host dbname = library user = $this->username password = $this->password");
        }
        // if a record already exists the just the timestamp will be updated
       $query_on_conflict = 'INSERT INTO books (unique_value, author, title) VALUES ($1, $2, $3) ON CONFLICT (unique_value) DO UPDATE SET created_at = current_timestamp';
        $insert = pg_prepare(static::$db_conn_library, "insert_query", $query_on_conflict);
        $keys = array_keys($arr);
        for($i = 0; $i < count($arr); $i++) {
            foreach($arr[$keys[$i]] as $key => $value) {
                $unique_v = $value['name'] . $value['author'];
                $details = array($unique_v , $value['author'], $value['name'] );
                $insert = pg_execute(static::$db_conn_library, "insert_query", $details);
                if (!$insert) {
                    echo "An error occured while inserting books in the database";
                }
            }
        }
    }
    /**
     * Query all books records
		 * @return the query result
     */
    public function queryBooksTable() {
        if (is_null(static::$db_conn_library)) {
            static::$db_conn_library = pg_connect("host = $this->host dbname = library user = $this->username password = $this->password");
        }
        $books_query = pg_query(static::$db_conn_library, "SELECT * from public.books");
        $books_array = array();
        while ($row = pg_fetch_array($books_query)) {
            $books_array[] = array('title' => $row['title'], 'author' => $row['author']);
        }
        $json_books = json_encode($books_array, JSON_UNESCAPED_UNICODE );
        return($json_books);
    }
    /**
     * Query books table by athor name
     */
    public function searchAuthor($author) {
        if (is_null(static::$db_conn_library)) {
            static::$db_conn_library = pg_connect("host = $this->host dbname = library user = $this->username password = $this->password");
        }
        echo $author;
        $author_query = pg_select(static::$db_conn_library, 'public.books', array("author" => $author));
        $json_result = json_encode($author_query,  JSON_UNESCAPED_UNICODE);
        print_r($json_result);
    }

}


?>