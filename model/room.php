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
    public function login($room){
        $roomname = (string) $room['roomname'];
        $password = (string) $room['password'];
        $sql = "SELECT id FROM room WHERE roomname = ? AND password = ?";
        $stmt = $this->conn->prepare($sql);
        if ( false===$stmt ) {
            return $this->return_fail('prepare_failed: ' . htmlspecialchars($this->conn->error));
        }
        $rc = $stmt->bind_param("ss",$roomname,$password);
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
            return_success("room_login_success",$result);
        }else{
            return_fail("room_login_fail","MC and Password does not match");
        }
    }
private function get_room($room_id){
    $sql = "SELECT * FROM room WHERE db_id = ?";
    $stmt = $this->conn->prepare($sql);
    if ( false===$stmt ) {
      return_fail('prepare_failed',htmlspecialchars($this->conn->error));
    }
    $rc = $stmt->bind_param("i",$room_id);
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
public function update_role($room){
  if(!isset($room['db_id']) || !isset($room['role'])){
    return_fail('bad_request',"room_id or role does not include to update date");
  }
  //echo json_encode($room);
  $role = (int) isset($room['role']) ? $room['role'] : null ;
  $modified_date = date("Y-m-d"); // so even room data does not change, the modified date is changed and get success on update process
  $db_id = (int) isset($room['db_id']) ? $room['db_id'] : null;
  $sql = "UPDATE room SET  role = ?, modified_date = ? WHERE db_id = ?";
  $stmt = $this->conn->prepare($sql);
  if ( false === $stmt ) {
    return_fail('prepare_failed',htmlspecialchars($this->conn->error));
  }
  //echo "bind param is ".$role." : " .$modified_date." : ".$db_id;
  $rc = $stmt->bind_param("isi",$role,$modified_date,$db_id);
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
    return_success("role_update_success",$affected_rows);
  }
  else{
    return_fail("role_update_fail",$affected_rows);
  }
}
public function update($room){
  // get original data
  if(!isset($room['db_id'])){
    // what the hell you are , bad guy!
    // don't give me shit
    return_fail('bad_request',"room_id does not include to update date");
  }
  $db_id = (int) isset($room['db_id']) ? $room['db_id'] : null;
  $orginal_data = $this->get_room($db_id);
  if(!$orginal_data[0]){
    return_fail('bad_request',"room_id does not exist in our database");// 
  }
  $org_room = $orginal_data[1];
  $rank = (int) isset($room['rank']) ?  $room['rank'] : $org_room['rank'];
  $name =  isset($room['name']) ? $room['name'] : $org_room['name'] ;
  $unit =  isset($room['unit']) ? $room['unit'] : $org_room['unit'] ;
  $phone_no =  isset($room['phone_no']) ? $room['phone_no'] : $org_room['phone_no'];
  $role =  (int) isset($room['role']) ? $room['role'] : $org_room['role'];
  //$password =  isset($room['password']) ? md5($room['password']) : $org_room['password'];
  $password =  isset($room['password']) ? $room['password'] : $org_room['password'];
  $modified_date = date("Y-m-d"); // so even room data does not change, the modified date is changed and get success on update process
  $sql = "UPDATE room SET rank = ?, name = ?, unit = ?, phone_no = ?,role = ?, password = ?, modified_date = ? WHERE db_id = ?";
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
    return_success("room_update_success",$affected_rows);
  }
  else{
    return_fail("room_update_fail",$affected_rows);
  }
}
public function get_self($db_id){
  $orginal_data = $this->get_room($db_id);
  if(!$orginal_data[0]){
    return_fail('bad_request',"room_id does not exist in our database");// 
  }
  $orginal_data[1]['password'] = null;
  return_success('get_self_success',$orginal_data[1]);
}
public function search($data,$room){
  $academy = isset($room['academy']) ? $room['academy'] : null ;
  $intake = isset($room['intake']) ? $room['intake'] : null ;
  $keyword = isset($data['keyword']) ? $data['keyword'] : null;
  $keyword = "%${keyword}%";
  //"%{$_POST['room']}%";
  // translated data 
  //$sql = "SELECT mc_type.name AS mc_type , room.mc AS mc, rank.name AS rank , room.name AS name, room.unit AS unit, room.phone_no AS phone_no FROM room LEFT JOIN mc_type ON room.mc_type = mc_type.db_id LEFT JOIN rank ON room.rank = rank.db_id  WHERE (room.intake = ? AND room.academy = ?) AND (room.mc LIKE ? OR room.name LIKE ? OR room.unit LIKE ? ) LIMIT 10";
  $sql = "SELECT db_id,mc_type,mc,rank,name,unit,phone_no,academy,intake,role FROM room WHERE (room.intake = ? AND room.academy = ?) AND (room.mc LIKE ? OR room.name LIKE ? OR room.unit LIKE ? ) LIMIT 10";
  $stmt = $this->conn->prepare($sql);
  if ( false===$stmt ) {
    return_fail('prepare_failed', htmlspecialchars($this->conn->error));
  }
  $rc = $stmt->bind_param("iisss",$intake,$academy,$keyword,$keyword,$keyword);
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
    return_success("search_success",$data);
  }else{
    return_fail("search_fail");
  }
  $stmt->close();
}
public function delete($room){
  $this->db_id = $room['db_id'];
  $sql = "DELETE FROM room WHERE db_id = ".$this->db_id;
  if ($this->conn->query($sql) === TRUE) {
    if(mysqli_affected_rows($this->conn) > 0) return $this->return_success("room deleted record");
      else return $this->return_fail("no room row is deleted ");
  } else {
        return $this->return_fail("Error deleting room record: " . $this->conn->error);
  }
}
public function select($room = array()){
  /* read query 
  SELECT t.db_id as telegraph_id, u1.name as sender_name, t.title, t.dto, u2.name as receiver_name, d.delicate FROM telegraph as t INNER JOIN delicate as d ON t.db_id = d.telegraph_id INNER JOIN unit as u1 ON t.sender = u1.db_id INNER JOIN unit as u2 ON d.receiver_id = u2.db_id WHERE t.db_id = 7 
  */
  /*
  $sql = "SELECT t.picture_data,t.db_id as telegraph_id, u1.name as sender_name, t.title, t.dto, u2.name as receiver_name, d.delicate FROM telegraph as t INNER JOIN delicate as d ON t.db_id = d.telegraph_id INNER JOIN unit as u1 ON t.sender = u1.db_id INNER JOIN unit as u2 ON d.receiver_id = u2.db_id WHERE t.db_id = ".$telegraph_id; */
  
  //$sql = "SELECT * FROM api_laptop ORDER BY id DESC";
  //$sql="SELECT * FROM telegraph WHERE db_id =$db_id";
  //if($db_id == 0 ) $sql = "SELECT * FROM telegraph"; // may be select all
  //$sql = "SELECT api_laptop.id,api_laptop.available_count, api_laptop.model, api_laptop.price, api_processor.name as processor, api_company.name as compnay, api_graphic.name as graphic, api_ran.name as ran, api_storage.name as storage, api_screen_size.name as screen_size, api_color.name as color, api_laptop.feature, api_laptop.created_date, api_laptop.modified_date from api_laptop JOIN api_processor ON api_laptop.processor = api_processor.id JOIN api_graphic ON api_laptop.graphic = api_graphic.id JOIN api_company ON api_laptop.company = api_company.id JOIN api_ran ON api_laptop.ran = api_ran.id JOIN api_storage ON api_laptop.storage = api_storage.id JOIN api_screen_size ON api_laptop.screen_size = api_screen_size.id JOIN api_color ON api_laptop.color = api_color.id ORDER BY api_laptop.id DESC ";
  // we have to filter out for current room 
  // currently select all room from table 
  // select all room  (for system admin)
  // select intake room (for normal room)
  // select one room (for login )
  // select course room ( for course admin and room)
  if(isset($room['academy']) AND isset($room['intake']) AND isset($room['db_id'])) $this->db_id = $room['db_id'];
  else { return $this->return_fail("requested room academy and intake data need to be provided"); }
/*
  $sql = "SELECT mc_type.name as mc_type,
room.mc as mc,
rank.name as rank, 
room.name  as name,
academy.name as academy, 
room.intake as intake,
room.unit as unit,
room.phone_no as phone_no
FROM room 
LEFT JOIN mc_type ON room.mc_type = mc_type.db_id 
LEFT JOIN academy ON room.academy = academy.db_id 
LEFT JOIN rank ON room.rank = rank.db_id ;";
*/
/*
  $sql = "SELECT room.db_id as db_id, mc_type.name as mc_type, room.mc as mc, rank.name as rank, room.name as name, academy.name as academy, room.intake as intake, room.unit as unit, room.phone_no as phone_no FROM room LEFT JOIN mc_type ON room.mc_type = mc_type.db_id LEFT JOIN academy ON room.academy = academy.db_id LEFT JOIN rank ON room.rank = rank.db_id WHERE academy = ".$room['academy']. " AND intake= ".$room['intake']. "  ORDER BY mc_type,mc ASC";
*/
  $sql = "SELECT db_id,mc_type,mc,rank,name,unit,phone_no,role,academy,intake FROM room WHERE academy = ".$room['academy']. " AND intake= ".$room['intake']. "  ORDER BY mc_type,mc ASC";
          $result=$this->conn->query($sql);
          $result_data = array();
          if ($result->num_rows >0){
            // something something
              while($row=$result->fetch_assoc()){
              //  echo "hello".$row['db_id'];
                //echo $row;
                $result_data[] = $row;
              }
              return $this->return_success("select read data ok",$result_data);
          }
          else{
            return $this->return_fail("no read data is selected");
          }
}
public function config(){
  $config = array();
  // compnay
  $sql = "SELECT * FROM mc_type";
  $result=$this->conn->query($sql);
  $result_data = array();
  $result_data[0] = "--ပြန်တမ်းဝင်အမျိုးအစား ရွေးပါ--";
  if ($result->num_rows >0){
    while($row=$result->fetch_assoc()){
      $row_db_id = $row['db_id'];
      $row_name = $row['name'];
      $result_data[$row_db_id] = $row_name;
    }
    $config['mc_type']= $result_data;
  }
  else{
   return_fail("no mc_type data is selected");
  }
  // processor
  $sql = "SELECT * FROM rank";
  $result=$this->conn->query($sql);
  $result_data = array();
  $result_data[0] = "-- အဆင့် ရွေးပါ --";
  if ($result->num_rows >0){
    while($row=$result->fetch_assoc()){
      $row_db_id = $row['db_id'];
      $row_name = $row['name'];
      $result_data[$row_db_id] = $row_name;
    }
    $config['rank']= $result_data;
  }
  else{
   return_fail("no rank data is selected");
  }
  // academy
  $sql = "SELECT * FROM academy";
  $result=$this->conn->query($sql);
  $result_data = array();
  $result_data[0] = "-- ဗလ သင်တန်းကျောင်း ရွေးပါ --";
  if ($result->num_rows >0){
    while($row=$result->fetch_assoc()){
      $row_db_id = $row['db_id'];
      $row_name = $row['name'];
      $result_data[$row_db_id] = $row_name;
    }
    $config['academy']= $result_data;
  }
  else{
   return_fail("no academy data is selected");
  }
  // role
  $sql = "SELECT * FROM role";
  $result=$this->conn->query($sql);
  $result_data = array();
  $result_data[0] = "-- role ရွေးပါ --";
  if ($result->num_rows >0){
    while($row=$result->fetch_assoc()){
      $row_db_id = $row['db_id'];
      $row_name = $row['name'];
      $result_data[$row_db_id] = $row_name;
    }
    $config['role']= $result_data;
  }
  else{
   return_fail("no role data is selected");
  }
  return_success("select config data ok",$config);
} // end for config 
} /// end for class
?>