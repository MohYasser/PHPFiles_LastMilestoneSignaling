<?php

class DB_Functions {
 
    private $conn;
 
    // constructor
    function __construct() {
        require_once 'DB_Connect.php';
        // connecting to database
        $db = new Db_Connect();
        $this->conn = $db->connect();
    }
 
    // destructor
    function __destruct() {
         
    }
 
    /**
     * Storing new user
     * returns user details
     */
    public function storeUser($name, $email, $password, $address, $phonenum) {
        $uuid = uniqid('', true);
        $hash = $this->hashSSHA($password);
        $encrypted_password = $hash["encrypted"]; // encrypted password
        $salt = $hash["salt"]; // salt
        
        $stmt = $this->conn->prepare("INSERT INTO users (unique_id, name, email, encrypted_password, salt, created_at, address, phonenum) VALUES (?, ?, ?, ?, ?, NOW(), ?, ?);");
        $stmt->bind_param("sssssss", $uuid, $name, $email, $encrypted_password, $salt, $address, $phonenum);
        $result = $stmt->execute();
        $stmt->close();
 
        // check for successful store
        if ($result) {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
 
            return $user;
        } else {
            return false;
        }
    }
 
    /**
     * Get user by email and password
     */
    public function getUserByEmailAndPassword($email, $password) {
 
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
 
        $stmt->bind_param("s", $email);
 
        if ($stmt->execute()) {
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
 
            // verifying user password
            $salt = $user['salt'];
            $encrypted_password = $user['encrypted_password'];
            $hash = $this->checkhashSSHA($salt, $password);
            // check for password equality
            if ($encrypted_password == $hash) {
                // user authentication details are correct
                return $user;
            }
        } else {
            return NULL;
        }
    }
 
    /**
     * Check user is existed or not
     */
    public function isUserExisted($email) {
        $stmt = $this->conn->prepare("SELECT email from users WHERE email = ?");
 
        $stmt->bind_param("s", $email);
 
        $stmt->execute();
 
        $stmt->store_result();
 
        if ($stmt->num_rows > 0) {
            // user existed 
            $stmt->close();
            return true;
        } else {
            // user not existed
            $stmt->close();
            return false;
        }
    }
 
    /**
     * Encrypting password
     * @param password
     * returns salt and encrypted password
     */
    public function hashSSHA($password) {
 
        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }
 
    /**
     * Decrypting password
     * @param salt, password
     * returns hash string
     */
    public function checkhashSSHA($salt, $password) {
 
        $hash = base64_encode(sha1($password . $salt, true) . $salt);
 
        return $hash;
    }

/** Till here there is nothing wrong with DB_Functions
	However we add the following for convenience sake(because of other alterations further in the project due to copying of some pieces of code from model solution of previous milestone)
	Some variable names are modified to match database names [the database is unaltered (your work)]
*/
 
    /*
    public function get_Details($item_id) {
        
        $sql = "SELECT DISTINCT shop_product_link.ID, shop_product_link.price , shop_product_link.sp_offers,  shop.shop_name, shop.lattitude, shop.longitude from 
        shop INNER JOIN shop_product_link ON shop.id= shop_product_link.shop_ID and shop_product_link.product_ID = $item_id;";
        $result = $this->conn->query($sql);
        $i=0;
      
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $array[$i]=array("error"=> FALSE ,
                  "uid"=> $row['ID'] ,   
                "name" => $row['shop_name'] ,
                "lat"=>$row['lattitude'] , 
               "long"=>$row['longitude']  ,
               "price"=> $row['price'], 
			   "sp_offers"=>$row['sp_offers']) ;

               $i++;
            }
            echo json_encode($array);
          } else {
            $array["error"] = TRUE;
            $array["error_msg"] = "No Items in database";
            echo json_encode($array);
          } 

    }*/

}
 
?>