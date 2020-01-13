<?php
class user
{
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function register($user){
        $username = (string) $user['username'];
        $password = (string) $user['password'];
        $created_date = date("Y-m-d");
        $modified_date = date("Y-m-d");
        $sql = "INSERT INTO user (username, password, created_date, modified_date) VALUES (?,?,?,?)";
        $stmt = $this->conn->prepare($sql);
        if ( false===$stmt ) {
            return_fail('prepare_failed',htmlspecialchars($this->conn->error));
        }
        $rc = $stmt->bind_param("ssss",$username, $password, $created_date, $modified_date);
        if ( false===$rc ) {
            return_fail('bind_param_failed',htmlspecialchars($stmt->error));
        }
        $rc = $stmt->execute();
        if ( false===$rc ) {
            return_fail('execute_failed',htmlspecialchars($this->conn->errno).":".htmlspecialchars($stmt->error));
        }
        $insert_id = $this->conn->insert_id;
        $stmt->close();
        return_success("user_register_success",$insert_id);
    }
        
    public function login($user){
        $username = (string) $user['username'];
        $password = (string) $user['password'];
        $sql = "SELECT id FROM user WHERE username = ? AND password = ?";
        $stmt = $this->conn->prepare($sql);
        if ( false===$stmt ) {
            return $this->return_fail('prepare_failed: ' . htmlspecialchars($this->conn->error));
        }
        $rc = $stmt->bind_param("ss",$username,$password);
        if ( false===$rc ) {
            return $this->return_fail('bind_param_failed: ' . htmlspecialchars($stmt->error));
        }
        $rc = $stmt->execute();
        if ( false===$rc ) {
            return $this->return_fail('execute_failed: error code '.htmlspecialchars($this->conn->errno) . htmlspecialchars($stmt->error),htmlspecialchars($this->conn->errno));
        }
        $result = $stmt->get_result();
        $affected_rows = $result->num_rows;
        $stmt->close();
        if($affected_rows > 0 ){
            $data = $result->fetch_all(MYSQLI_ASSOC);
            $result = $data[0];
            return_success("user_login_success",$result);
        }else{
            return_fail("user_login_fail","MC and Password does not match");
        }
    }
    private function get_user($id){
        $sql = "SELECT * FROM user WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if ( false===$stmt ) {
          return_fail('prepare_failed get_user',htmlspecialchars($this->conn->error));
        }
        $rc = $stmt->bind_param("i",$id);
        if ( false===$rc ) {
          return_fail('bind_param_failed get_user', htmlspecialchars($stmt->error));
        }
        $rc = $stmt->execute();
        if ( false===$rc ) {
          return_fail('execute_failed get_user',htmlspecialchars($this->conn->errno) .":". htmlspecialchars($stmt->error));
        }
        $result = $stmt->get_result();
        $affected_rows = $result->num_rows;
        $stmt->close();
        if($affected_rows > 0 ){
          $data = $result->fetch_all(MYSQLI_ASSOC);
          $return_data = array(true,$data[0]);
          return $return_data;
        }else{
          $return_data = array(false);
          return $return_data;
        }
    }
    public function update($user){
      // get original data
      if(!isset($user['id'])){
        // what the hell you are , bad guy!
        // don't give me shit
        return_fail('bad_request',"user_id does not include to update date");
      }
      $id = (int) isset($user['id']) ? $user['id'] : null;
      $orginal_data = $this->get_user($id);
      if(!$orginal_data[0]){
        return_fail('bad_request',"user_id does not exist in our database");// 
      }
      $org_user = $orginal_data[1];
      $username = (string) isset($user['username']) ?  $user['username'] : $org_user['username'];
      $password =  (string) isset($user['password']) ? $user['password'] : $org_user['password'] ;
      $modified_date = date("Y-m-d"); // so even user data does not change, the modified date is changed and get success on update process
      $sql = "UPDATE user SET username = ?, password = ?, modified_date = ? WHERE id = ?";
      $stmt = $this->conn->prepare($sql);
      if ( false === $stmt ) {
        return_fail('prepare_failed',htmlspecialchars($this->conn->error));
      }
      $rc = $stmt->bind_param("sssi",$username,$password,$modified_date,$id);
      if ( false===$rc ) {
        return_fail('bind_param_failed',htmlspecialchars($stmt->error));
      }
      $rc = $stmt->execute();
      if ( false===$rc ) {
        return_fail('execute_failed',htmlspecialchars($this->conn->errno).":".htmlspecialchars($stmt->error));
      }
      $affected_rows = $stmt->affected_rows;
      $stmt->close();
      if($affected_rows > 0 ){
        return_success("user_update_success",$affected_rows);
      }
      else{
        return_fail("user_update_fail",$affected_rows);
      }
    }
    public function get_self($id){
      $orginal_data = $this->get_user($id);
      if(!$orginal_data[0]){
        return_fail('bad_request',"user_id does not exist in our database");// 
      }
      // TODO : delete password key in php array
      $orginal_data[1]['password'] = null;
      return_success('get_self_success',$orginal_data[1]);
    }

    public function delete($user){
      $id = $user['dbid_id'];
      $sql = "DELETE FROM user WHERE id = ".$id;
      if ($this->conn->query($sql) === TRUE) {
        if(mysqli_affected_rows($this->conn) > 0) return $this->return_success("user deleted record");
          else return $this->return_fail("no user row is deleted ");
      } else {
            return $this->return_fail("Error deleting user record: " . $this->conn->error);
      }
    }

        
    public function select($user){
      
      $id = (int) isset($user['id']) ? $user['id'] : 0;
      $sql = "select user.* from user WHERE  user.id > ? LIMIT 50";
      $stmt = $this->conn->prepare($sql);
      if ( false===$stmt ) {
          return_fail('prepare_failed', htmlspecialchars($this->conn->error));
      }
      $rc = $stmt->bind_param("i",$id);
      if ( false===$rc ) {
          return_fail('bind_param_failed',htmlspecialchars($stmt->error));
      }
      $rc = $stmt->execute();
      if ( false===$rc ) {
          return_fail('execute_failed: error code ',htmlspecialchars($this->conn->errno) ." : ". htmlspecialchars($stmt->error));
      }
      $result = $stmt->get_result();
      $affected_rows = $result->num_rows;
      if($affected_rows > 0 ){
          $data = $result->fetch_all(MYSQLI_ASSOC);
          return_success(" select all e _select_all_success",$data);
      }else{
          return_fail(" user select fail no record _select_all_fail");
      }
      $stmt->close();
    }

} /// end for class
?>