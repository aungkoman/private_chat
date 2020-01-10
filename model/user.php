<?php
class user
{
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function register($user){
        // restrict user input data
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
private function get_user($user_id){
    $sql = "SELECT * FROM user WHERE db_id = ?";
    $stmt = $this->conn->prepare($sql);
    if ( false===$stmt ) {
      return_fail('prepare_failed',htmlspecialchars($this->conn->error));
    }
    $rc = $stmt->bind_param("i",$user_id);
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
public function update_role($user){
  if(!isset($user['db_id']) || !isset($user['role'])){
    return_fail('bad_request',"user_id or role does not include to update date");
  }
  //echo json_encode($user);
  $role = (int) isset($user['role']) ? $user['role'] : null ;
  $modified_date = date("Y-m-d"); // so even user data does not change, the modified date is changed and get success on update process
  $db_id = (int) isset($user['db_id']) ? $user['db_id'] : null;
  $sql = "UPDATE user SET  role = ?, modified_date = ? WHERE db_id = ?";
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
public function update($user){
  // get original data
  if(!isset($user['db_id'])){
    // what the hell you are , bad guy!
    // don't give me shit
    return_fail('bad_request',"user_id does not include to update date");
  }
  $db_id = (int) isset($user['db_id']) ? $user['db_id'] : null;
  $orginal_data = $this->get_user($db_id);
  if(!$orginal_data[0]){
    return_fail('bad_request',"user_id does not exist in our database");// 
  }
  $org_user = $orginal_data[1];
  $rank = (int) isset($user['rank']) ?  $user['rank'] : $org_user['rank'];
  $name =  isset($user['name']) ? $user['name'] : $org_user['name'] ;
  $unit =  isset($user['unit']) ? $user['unit'] : $org_user['unit'] ;
  $phone_no =  isset($user['phone_no']) ? $user['phone_no'] : $org_user['phone_no'];
  $role =  (int) isset($user['role']) ? $user['role'] : $org_user['role'];
  //$password =  isset($user['password']) ? md5($user['password']) : $org_user['password'];
  $password =  isset($user['password']) ? $user['password'] : $org_user['password'];
  $modified_date = date("Y-m-d"); // so even user data does not change, the modified date is changed and get success on update process
  $sql = "UPDATE user SET rank = ?, name = ?, unit = ?, phone_no = ?,role = ?, password = ?, modified_date = ? WHERE db_id = ?";
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
    return_success("user_update_success",$affected_rows);
  }
  else{
    return_fail("user_update_fail",$affected_rows);
  }
}
public function get_self($db_id){
  $orginal_data = $this->get_user($db_id);
  if(!$orginal_data[0]){
    return_fail('bad_request',"user_id does not exist in our database");// 
  }
  $orginal_data[1]['password'] = null;
  return_success('get_self_success',$orginal_data[1]);
}
public function search($data,$user){
  $academy = isset($user['academy']) ? $user['academy'] : null ;
  $intake = isset($user['intake']) ? $user['intake'] : null ;
  $keyword = isset($data['keyword']) ? $data['keyword'] : null;
  $keyword = "%${keyword}%";
  //"%{$_POST['user']}%";
  // translated data 
  //$sql = "SELECT mc_type.name AS mc_type , user.mc AS mc, rank.name AS rank , user.name AS name, user.unit AS unit, user.phone_no AS phone_no FROM user LEFT JOIN mc_type ON user.mc_type = mc_type.db_id LEFT JOIN rank ON user.rank = rank.db_id  WHERE (user.intake = ? AND user.academy = ?) AND (user.mc LIKE ? OR user.name LIKE ? OR user.unit LIKE ? ) LIMIT 10";
  $sql = "SELECT db_id,mc_type,mc,rank,name,unit,phone_no,academy,intake,role FROM user WHERE (user.intake = ? AND user.academy = ?) AND (user.mc LIKE ? OR user.name LIKE ? OR user.unit LIKE ? ) LIMIT 10";
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
public function delete($user){
  $this->db_id = $user['db_id'];
  $sql = "DELETE FROM user WHERE db_id = ".$this->db_id;
  if ($this->conn->query($sql) === TRUE) {
    if(mysqli_affected_rows($this->conn) > 0) return $this->return_success("user deleted record");
      else return $this->return_fail("no user row is deleted ");
  } else {
        return $this->return_fail("Error deleting user record: " . $this->conn->error);
  }
}
public function select($user = array()){
  /* read query 
  SELECT t.db_id as telegraph_id, u1.name as sender_name, t.title, t.dto, u2.name as receiver_name, d.delicate FROM telegraph as t INNER JOIN delicate as d ON t.db_id = d.telegraph_id INNER JOIN unit as u1 ON t.sender = u1.db_id INNER JOIN unit as u2 ON d.receiver_id = u2.db_id WHERE t.db_id = 7 
  */
  /*
  $sql = "SELECT t.picture_data,t.db_id as telegraph_id, u1.name as sender_name, t.title, t.dto, u2.name as receiver_name, d.delicate FROM telegraph as t INNER JOIN delicate as d ON t.db_id = d.telegraph_id INNER JOIN unit as u1 ON t.sender = u1.db_id INNER JOIN unit as u2 ON d.receiver_id = u2.db_id WHERE t.db_id = ".$telegraph_id; */
  
  //$sql = "SELECT * FROM api_laptop ORDER BY id DESC";
  //$sql="SELECT * FROM telegraph WHERE db_id =$db_id";
  //if($db_id == 0 ) $sql = "SELECT * FROM telegraph"; // may be select all
  //$sql = "SELECT api_laptop.id,api_laptop.available_count, api_laptop.model, api_laptop.price, api_processor.name as processor, api_company.name as compnay, api_graphic.name as graphic, api_ran.name as ran, api_storage.name as storage, api_screen_size.name as screen_size, api_color.name as color, api_laptop.feature, api_laptop.created_date, api_laptop.modified_date from api_laptop JOIN api_processor ON api_laptop.processor = api_processor.id JOIN api_graphic ON api_laptop.graphic = api_graphic.id JOIN api_company ON api_laptop.company = api_company.id JOIN api_ran ON api_laptop.ran = api_ran.id JOIN api_storage ON api_laptop.storage = api_storage.id JOIN api_screen_size ON api_laptop.screen_size = api_screen_size.id JOIN api_color ON api_laptop.color = api_color.id ORDER BY api_laptop.id DESC ";
  // we have to filter out for current user 
  // currently select all user from table 
  // select all user  (for system admin)
  // select intake user (for normal user)
  // select one user (for login )
  // select course user ( for course admin and user)
  if(isset($user['academy']) AND isset($user['intake']) AND isset($user['db_id'])) $this->db_id = $user['db_id'];
  else { return $this->return_fail("requested user academy and intake data need to be provided"); }
/*
  $sql = "SELECT mc_type.name as mc_type,
user.mc as mc,
rank.name as rank, 
user.name  as name,
academy.name as academy, 
user.intake as intake,
user.unit as unit,
user.phone_no as phone_no
FROM user 
LEFT JOIN mc_type ON user.mc_type = mc_type.db_id 
LEFT JOIN academy ON user.academy = academy.db_id 
LEFT JOIN rank ON user.rank = rank.db_id ;";
*/
/*
  $sql = "SELECT user.db_id as db_id, mc_type.name as mc_type, user.mc as mc, rank.name as rank, user.name as name, academy.name as academy, user.intake as intake, user.unit as unit, user.phone_no as phone_no FROM user LEFT JOIN mc_type ON user.mc_type = mc_type.db_id LEFT JOIN academy ON user.academy = academy.db_id LEFT JOIN rank ON user.rank = rank.db_id WHERE academy = ".$user['academy']. " AND intake= ".$user['intake']. "  ORDER BY mc_type,mc ASC";
*/
  $sql = "SELECT db_id,mc_type,mc,rank,name,unit,phone_no,role,academy,intake FROM user WHERE academy = ".$user['academy']. " AND intake= ".$user['intake']. "  ORDER BY mc_type,mc ASC";
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