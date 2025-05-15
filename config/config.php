// config/config.php
<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'billing_system');

define('BASE_URL', 'http://localhost/billing_system');
define('SITE_NAME', 'Billing System');

// Session timeout in seconds (30 minutes)
define('SESSION_TIMEOUT', 1800);

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

// config/database.php
<?php
class Database {
    private $connection;
    
    public function __construct() {
        $this->connect();
    }
    
    private function connect() {
        try {
            $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($this->connection->connect_error) {
                throw new Exception("Connection failed: " . $this->connection->connect_error);
            }
        } catch (Exception $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    public function query($sql) {
        $result = $this->connection->query($sql);
        if (!$result) {
            throw new Exception("Query failed: " . $this->connection->error);
        }
        return $result;
    }
    
    public function escape($value) {
        return $this->connection->real_escape_string($value);
    }
    
    public function getLastId() {
        return $this->connection->insert_id;
    }
    
    public function beginTransaction() {
        $this->connection->begin_transaction();
    }
    
    public function commit() {
        $this->connection->commit();
    }
    
    public function rollback() {
        $this->connection->rollback();
    }
}
?>