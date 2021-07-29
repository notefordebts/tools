<?php
 
class Db {
    
    private $conn; 
    
    
    
    public function __construct() {
       include_once 'config.php'; 
        try {
  $this->conn = $conn = new PDO("mysql:host=".MYHOST.";dbname=".DATABASE,DATABASE_USER, DATABASE_PASSWORD);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  //echo "Connected successfully";
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}
        
    }
    
    
    public function ip_add() {
        $ip = $this->get_ip();
        if($this->check_for_user_ip()){
            
        
        
        $sql = 'INSERT INTO users_ip(user_ip) VALUES(:ip)';
        $statement = $this->conn->prepare($sql);
        
        $statement->execute([
	':ip' => $ip
         
        ]);
        }else{
            
        $sql = "UPDATE users_ip SET date=NOW() WHERE user_ip=:ip";
        $statement = $this->conn->prepare($sql);
        
        $statement->execute([
            'ip' => $ip
        ]);
        }
        
        
        
        
    }
    
    
    public function find_user_id() {
        $ip = $this->get_ip();
        $sql = "SELECT id from users_ip WHERE user_ip=:ip";
        $statement = $this->conn->prepare($sql);
        $statement->execute([
	':ip' => $ip
            ]);
        $row = $statement->fetch();
        if(!$row){
            return false;
        }
       return  $row['id'];
    }
    
    
    public function add_domain_name($domain_name) {
        $this->ip_add();
        $user_id = $this->find_user_id();
        if($user_id == false){
        
            return false;
        }
        
        if($this->check_for_domain($domain_name)){
        
        $sql = "INSERT INTO more_tracking (users,domain_name) VALUES(:user_id,:domain_name) ";
        $statement = $this->conn->prepare($sql);
        $statement->execute([
	':user_id' => $user_id,
        ':domain_name' => $domain_name
                
            ]);
        }else{
            $sql = "UPDATE more_tracking SET date=NOW() WHERE users=:user_id AND domain_name = :domain_name";
        $statement = $this->conn->prepare($sql);
        
        $statement->execute([
            ':user_id' => $user_id,
            ':domain_name' => $domain_name
        ]);
            
            
        } 
    }
    
    public function check_for_user_ip() {
        $ip = $this->get_ip();
        $sql = "SELECT COUNT(*) FROM users_ip WHERE user_ip = :ip";
        $statement = $this->conn->prepare($sql);
        $statement->execute([
	':ip' => $ip
            ]);
         $data = $statement->fetchColumn();
         
         if($data < 1){
             return true;
    }else{
        return false;
    }
        
        
        
        
    }
    
    
    public function get_last_domains() {
       // $ip = $this->get_ip();
        $user_id = $this->find_user_id();
        
        if($user_id == false){
            return false;
        }
        
        
        $sql = "SELECT * FROM more_tracking WHERE users = :user_id ORDER BY date DESC LIMIT 10";
         $statement = $this->conn->prepare($sql);
         $statement->execute([
	':user_id' => $user_id
            ]);
         $result = $statement->fetchAll();
         
          return $result;
        
        
    }
    
    
    public function check_for_domain($domain_name){
        $user_id = $this->find_user_id();
        
            $sql = "SELECT COUNT(*) FROM more_tracking WHERE users = :user_id AND domain_name = :domain_name  ";
            $statement = $this->conn->prepare($sql);
            $statement->execute([
	':user_id' => $user_id,
        ':domain_name' => $domain_name
            ]);
         $data = $statement->fetchColumn();
         
         if($data < 1){
             
             return true;
        }else{
            
            return false;
    }
        
    }


    private function get_ip(){
        $ip = $_SERVER['REMOTE_ADDR'];
        return $ip;
    }
    
    
    
    public function clean_up_db(){
        //keeps db small by deleting   all records older than set days 
        //only ran from clean_cron.php file ran on a cron job every day 
      
       $days_before_to_remove = 5; 
       
       $sql = "DELETE  FROM users_ip 
WHERE  DATE(date) < DATE_SUB(CURDATE(), INTERVAL $days_before_to_remove DAY)";
       //$statement = $this->conn->prepare($sql);
       
      if( $this->conn->exec($sql)){
          echo "Deleted users ip <br>";
      }else{
          echo "no ips deleted <br>";
      }
      
      
       $sql = "DELETE  FROM  more_tracking
WHERE  DATE(date) < DATE_SUB(CURDATE(), INTERVAL $days_before_to_remove DAY)";
       //$statement = $this->conn->prepare($sql);
       
      if( $this->conn->exec($sql)){
          echo "Deleted domains <br> ";
      }else{
          echo "no domains   deleted <br>";
      }
       
        
        
        
    }    
}