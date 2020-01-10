<?php
class message
{
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function register($message){
        // restrict message input data
        $user = (int) isset($message['user']) ? $message['user'] : null;
        $room = (int) isset($message['room']) ? $message['room'] : null;
        $message = (string) isset($message['message']) ? $message['message'] : null;
        $created_date = date("Y-m-d H:i:s");  
       // echo "created date ".$created_date;
        $modified_date = date("Y-m-d H:i:s");  
        $sql = "INSERT INTO message (user, room, message, created_datetime, modified_datetime) VALUES (?,?,?,?,?)";
        $stmt = $this->conn->prepare($sql);
        if ( false===$stmt ) {
            return_fail('prepare_failed',htmlspecialchars($this->conn->error));
        }
        $rc = $stmt->bind_param("iisss",$user, $room, $message, $created_date, $modified_date);
        if ( false===$rc ) {
            return_fail('bind_param_failed',htmlspecialchars($stmt->error));
        }
        $rc = $stmt->execute();
        if ( false===$rc ) {
            return_fail('execute_failed',htmlspecialchars($this->conn->errno).":".htmlspecialchars($stmt->error));
        }
        $insert_id = $this->conn->insert_id;
        $stmt->close();
        return_success("message_register_success",$insert_id);
    }
        
    
private function get_message($message_id){
    $sql = "SELECT * FROM message WHERE db_id = ?";
    $stmt = $this->conn->prepare($sql);
    if ( false===$stmt ) {
      return_fail('prepare_failed',htmlspecialchars($this->conn->error));
    }
    $rc = $stmt->bind_param("i",$message_id);
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

public function update($message){
  // get original data
  if(!isset($message['db_id'])){
    // what the hell you are , bad guy!
    // don't give me shit
    return_fail('bad_request',"message_id does not include to update date");
  }
  $db_id = (int) isset($message['db_id']) ? $message['db_id'] : null;
  $orginal_data = $this->get_message($db_id);
  if(!$orginal_data[0]){
    return_fail('bad_request',"message_id does not exist in our database");// 
  }
  $org_message = $orginal_data[1];
  $rank = (int) isset($message['rank']) ?  $message['rank'] : $org_message['rank'];
  $name =  isset($message['name']) ? $message['name'] : $org_message['name'] ;
  $unit =  isset($message['unit']) ? $message['unit'] : $org_message['unit'] ;
  $phone_no =  isset($message['phone_no']) ? $message['phone_no'] : $org_message['phone_no'];
  $role =  (int) isset($message['role']) ? $message['role'] : $org_message['role'];
  //$password =  isset($message['password']) ? md5($message['password']) : $org_message['password'];
  $password =  isset($message['password']) ? $message['password'] : $org_message['password'];
  $modified_date = date("Y-m-d"); // so even message data does not change, the modified date is changed and get success on update process
  $sql = "UPDATE message SET rank = ?, name = ?, unit = ?, phone_no = ?,role = ?, password = ?, modified_date = ? WHERE db_id = ?";
  $stmt = $this->conn->prepare($sql);
  if ( false === $stmt ) {
    return_fail('prepare_failed',htmlspecialchars($this->conn->error));
  }
  $rc = $stmt->bind_param("isssissi",$rank,$name,$unit,$phone_no,$role,$password,$modified_date,$db_id);
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
    return_success("message_update_success",$affected_rows);
  }
  else{
    return_fail("message_update_fail",$affected_rows);
  }
}
public function get_self($db_id){
  $orginal_data = $this->get_message($db_id);
  if(!$orginal_data[0]){
    return_fail('bad_request',"message_id does not exist in our database");// 
  }
  $orginal_data[1]['password'] = null;
  return_success('get_self_success',$orginal_data[1]);
}

public function delete($message){
  if(!isset($message['id'])){
      return_fail('bad_request',"message id does not include to delete data");
  }
  $db_id = (int) isset($message['id']) ? $message['id'] : null;

  $sql = "DELETE FROM message WHERE id = ? ";
  $stmt = $this->conn->prepare($sql);
  if ( false === $stmt ) {
      return_fail('prepare_failed',htmlspecialchars($this->conn->error));
  }
  $rc = $stmt->bind_param("i",$db_id);
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
      return_success(" message  _delete_success",$affected_rows);
  }
  else{
      return_fail(" message   _delete_fail",$affected_rows);
  }
}

public function select($message){
  
  $user = (int) isset($message['user']) ? $message['user'] : null;
  $room = (int) isset($message['room']) ? $message['room'] : null;
  $id = (int) isset($message['room']) ? $message['id'] : 0 ;
  $sql = "select message.* from message, member WHERE message.room = ? AND member.user = ? AND message.room = member.room AND message.id > ? GROUP BY message.id LIMIT 10";
  $stmt = $this->conn->prepare($sql);
  if ( false===$stmt ) {
      return_fail('prepare_failed', htmlspecialchars($this->conn->error));
  }
  $rc = $stmt->bind_param("iii",$user, $room,$id);
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
      return_success(" messag e _select_all_success",$data);
  }else{
      return_fail(" message _select_all_fail");
  }
  $stmt->close();
}


} /// end for class
?>