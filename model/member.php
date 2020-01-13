<?php
class member
{
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function owner_join($member){
        // restrict member input data
        $room = (int) isset($member['room']) ? $member['room'] : null;
        $user = (int) isset($member['user']) ? $member['user'] : null;
        $created_date = date("Y-m-d");
        $modified_date = date("Y-m-d");

        $sql = "INSERT INTO member (room, user, created_date, modified_date) VALUES (?,?,?,?)";
        $stmt = $this->conn->prepare($sql);
        if ( false===$stmt ) {
            return_fail('prepare_failed',htmlspecialchars($this->conn->error));
        }
        $rc = $stmt->bind_param("iiss",$room, $user, $created_date, $modified_date);
        if ( false===$rc ) {
            return_fail('bind_param_failed',htmlspecialchars($stmt->error));
        }
        $rc = $stmt->execute();
        if ( false===$rc ) {
            return_fail('execute_failed',htmlspecialchars($this->conn->errno).":".htmlspecialchars($stmt->error));
        }
        $insert_id = $this->conn->insert_id;
        $stmt->close();
        return array("status" => true,"member_id"=>$insert_id);
    }
    public function register($member){
        // restrict member input data
        $room = (int) isset($member['room']) ? $member['room'] : null;
        $user = (int) isset($member['user']) ? $member['user'] : null;
        $created_date = date("Y-m-d");
        $modified_date = date("Y-m-d");

        $sql = "INSERT INTO member (room, user, created_date, modified_date) VALUES (?,?,?,?)";
        $stmt = $this->conn->prepare($sql);
        if ( false===$stmt ) {
            return_fail('prepare_failed',htmlspecialchars($this->conn->error));
        }
        $rc = $stmt->bind_param("iiss",$room, $user, $created_date, $modified_date);
        if ( false===$rc ) {
            return_fail('bind_param_failed',htmlspecialchars($stmt->error));
        }
        $rc = $stmt->execute();
        if ( false===$rc ) {
            return_fail('execute_failed',htmlspecialchars($this->conn->errno).":".htmlspecialchars($stmt->error));
        }
        $insert_id = $this->conn->insert_id;
        $stmt->close();
        return_success("room_joined",$room);
    }
            
    private function get_member($id){
        $sql = "SELECT * FROM member WHERE id = ?";
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

    
// public function update($member){
//   // get original data
//   if(!isset($member['id'])){
//     // what the hell you are , bad guy!
//     // don't give me shit
//     return_fail('bad_request',"member_id does not include to update date");
//   }
//   $id = (int) isset($member['id']) ? $member['id'] : null;
//   $orginal_data = $this->get_member($id);
//   if(!$orginal_data[0]){
//     return_fail('bad_request',"member_id does not exist in our database");// 
//   }
//   $org_member = $orginal_data[1];
//   $rank = (int) isset($member['rank']) ?  $member['rank'] : $org_member['rank'];
//   $name =  isset($member['name']) ? $member['name'] : $org_member['name'] ;
//   $unit =  isset($member['unit']) ? $member['unit'] : $org_member['unit'] ;
//   $phone_no =  isset($member['phone_no']) ? $member['phone_no'] : $org_member['phone_no'];
//   $role =  (int) isset($member['role']) ? $member['role'] : $org_member['role'];
//   //$password =  isset($member['password']) ? md5($member['password']) : $org_member['password'];
//   $password =  isset($member['password']) ? $member['password'] : $org_member['password'];
//   $modified_date = date("Y-m-d"); // so even member data does not change, the modified date is changed and get success on update process
//   $sql = "UPDATE member SET rank = ?, name = ?, unit = ?, phone_no = ?,role = ?, password = ?, modified_date = ? WHERE db_id = ?";
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
//     return_success("member_update_success",$affected_rows);
//   }
//   else{
//     return_fail("member_update_fail",$affected_rows);
//   }
// }
public function get_self($id){
  $orginal_data = $this->get_member($id);
  if(!$orginal_data[0]){
    return_fail('bad_request',"member_id does not exist in our database");// 
  }
  $orginal_data[1]['password'] = null;
  return_success('get_self_success',$orginal_data[1]);
}

public function delete($member){
  $id = $member['id'];
  $sql = "DELETE FROM member WHERE db_id = ".$this->id;
  if ($this->conn->query($sql) === TRUE) {
    if(mysqli_affected_rows($this->conn) > 0) return $this->return_success("member deleted record");
      else return $this->return_fail("no member row is deleted ");
  } else {
        return $this->return_fail("Error deleting member record: " . $this->conn->error);
  }
}  

public function select($member){
      
  $id = (int) isset($member['id']) ? $member['id'] : 0;
  $sql = "select member.* from member WHERE  member.id > ? LIMIT 50";
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
      return_fail(" member select select fail no record _select_all_fail");
  }
  $stmt->close();
}
public function room_list($member){
  $user = (int) isset($member['user']) ? $member['user'] : 0;
  $sql = "select member.room,room.name from member,room WHERE member.user = ? AND member.room = room.id GROUP BY room.id";
  $stmt = $this->conn->prepare($sql);
  if ( false===$stmt ) {
      return_fail('prepare_failed', htmlspecialchars($this->conn->error));
  }
  $rc = $stmt->bind_param("i",$user);
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
      return_fail(" member select select fail no record _select_all_fail");
  }
  $stmt->close();
}
} /// end for class
?>