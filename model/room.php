<?php
class room
{
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function register($room){
        // restrict room input data
        
        $name = (string) isset($room['name']) ? $room['name'] : null;
        $owner = (int) isset($room['owner']) ? $room['owner'] : null;
        $code = (string) time();
        $created_date = date("Y-m-d");
        $modified_date = date("Y-m-d");

        $sql = "INSERT INTO room (name, owner, code, created_date, modified_date) VALUES (?,?,?,?,?)";
        $stmt = $this->conn->prepare($sql);
        if ( false===$stmt ) {
            return_fail('prepare_failed',htmlspecialchars($this->conn->error));
        }
        $rc = $stmt->bind_param("sisss",$name, $owner, $code, $created_date, $modified_date);
        if ( false===$rc ) {
            return_fail('bind_param_failed',htmlspecialchars($stmt->error));
        }
        $rc = $stmt->execute();
        if ( false===$rc ) {
            return_fail('execute_failed',htmlspecialchars($this->conn->errno).":".htmlspecialchars($stmt->error));
        }
        $insert_id = $this->conn->insert_id;
        $stmt->close();
        return array("code" => $code,"room"=>$insert_id,"user"=>$owner);
    }
    public function get_id_by_code($room){
      $code = isset($room['code']) ? $room['code'] : null;
      $sql = "SELECT * FROM room WHERE code = ?";
      $stmt = $this->conn->prepare($sql);
      if ( false===$stmt ) {
        return_fail('prepare_failed',htmlspecialchars($this->conn->error));
      }
      $rc = $stmt->bind_param("s",$code);
      if ( false===$rc ) {
        return_fail('bind_param_failed', htmlspecialchars($stmt->error));
      }
      $rc = $stmt->execute();
      if ( false===$rc ) {
        return_fail('execute_failed',htmlspecialchars($this->conn->errno) .":". htmlspecialchars($stmt->error));
      }
      $result = $stmt->get_result();
      $affected_rows = $result->num_rows;
      $stmt->close();
      if($affected_rows > 0 ){
        $data = $result->fetch_all(MYSQLI_ASSOC);
        return $data[0]['id'];
      }else{
        return_fail('room code does not match with any room');
      }
  }
      
    private function get_room($id){
        $sql = "SELECT * FROM room WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if ( false===$stmt ) {
          return_fail('prepare_failed',htmlspecialchars($this->conn->error));
        }
        $rc = $stmt->bind_param("i",$id);
        if ( false===$rc ) {
          return_fail('bind_param_failed', htmlspecialchars($stmt->error));
        }
        $rc = $stmt->execute();
        if ( false===$rc ) {
          return_fail('execute_failed',htmlspecialchars($this->conn->errno) .":". htmlspecialchars($stmt->error));
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
    
    
// public function update($room){
//   // get original data
//   if(!isset($room['id'])){
//     // what the hell you are , bad guy!
//     // don't give me shit
//     return_fail('bad_request',"room_id does not include to update date");
//   }
//   $id = (int) isset($room['id']) ? $room['id'] : null;
//   $orginal_data = $this->get_room($id);
//   if(!$orginal_data[0]){
//     return_fail('bad_request',"room_id does not exist in our database");// 
//   }
//   $org_room = $orginal_data[1];
//   $rank = (int) isset($room['rank']) ?  $room['rank'] : $org_room['rank'];
//   $name =  isset($room['name']) ? $room['name'] : $org_room['name'] ;
//   $unit =  isset($room['unit']) ? $room['unit'] : $org_room['unit'] ;
//   $phone_no =  isset($room['phone_no']) ? $room['phone_no'] : $org_room['phone_no'];
//   $role =  (int) isset($room['role']) ? $room['role'] : $org_room['role'];
//   //$password =  isset($room['password']) ? md5($room['password']) : $org_room['password'];
//   $password =  isset($room['password']) ? $room['password'] : $org_room['password'];
//   $modified_date = date("Y-m-d"); // so even room data does not change, the modified date is changed and get success on update process
//   $sql = "UPDATE room SET rank = ?, name = ?, unit = ?, phone_no = ?,role = ?, password = ?, modified_date = ? WHERE db_id = ?";
//   $stmt = $this->conn->prepare($sql);
//   if ( false === $stmt ) {
//     return_fail('prepare_failed',htmlspecialchars($this->conn->error));
//   }
//   $rc = $stmt->bind_param("isssissi",$rank,$name,$unit,$phone_no,$role,$password,$modified_date,$db_id);
//   if ( false===$rc ) {
//     return_fail('bind_param_failed',htmlspecialchars($stmt->error));
//   }
//   $rc = $stmt->execute();
//   if ( false===$rc ) {
//     return_fail('execute_failed',htmlspecialchars($this->conn->errno).":".htmlspecialchars($stmt->error));
//   }
//   $affected_rows = $stmt->affected_rows;
//   $stmt->close();
//   if($affected_rows > 0 ){
//     return_success("room_update_success",$affected_rows);
//   }
//   else{
//     return_fail("room_update_fail",$affected_rows);
//   }
// }
    public function get_self($db_id){
      $orginal_data = $this->get_room($id);
      if(!$orginal_data[0]){
        return_fail('bad_request',"room_id does not exist in our database");// 
      }
      return_success('get_self_success',$orginal_data[1]);
    }
        
    public function delete($room){
      $id = $room['id'];
      $sql = "DELETE FROM room WHERE id = ".$this->id;
      if ($this->conn->query($sql) === TRUE) {
        if(mysqli_affected_rows($this->conn) > 0) return $this->return_success("room deleted record");
          else return $this->return_fail("no room row is deleted ");
      } else {
            return $this->return_fail("Error deleting room record: " . $this->conn->error);
      }
    }
      
    public function select($room){
      
      $id = (int) isset($room['id']) ? $room['id'] : 0;
      $sql = "select room.* from room WHERE  room.id > ? LIMIT 50";
      $stmt = $this->conn->prepare($sql);
      if ( false===$stmt ) {
          return_fail('prepare_failed room select', htmlspecialchars($this->conn->error));
      }
      $rc = $stmt->bind_param("i",$id);
      if ( false===$rc ) {
          return_fail('bind_param_failed room select',htmlspecialchars($stmt->error));
      }
      $rc = $stmt->execute();
      if ( false===$rc ) {
          return_fail('execute_failed: room select error code ',htmlspecialchars($this->conn->errno) ." : ". htmlspecialchars($stmt->error));
      }
      $result = $stmt->get_result();
      $affected_rows = $result->num_rows;
      if($affected_rows > 0 ){
          $data = $result->fetch_all(MYSQLI_ASSOC);
          return_success("  room select_success",$data);
      }else{
          return_fail("  room select fail no record _select_all_fail");
      }
      $stmt->close();
    }
} /// end for class
?>