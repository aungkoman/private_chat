<?php
//include("../../../security.php");
ini_set("allow_url_fopen", true);
header('Content-Type: application/json');
// required headers
header("Access-Control-Allow-Origin: *");

//header("Content-Type: application/json; charset=UTF-8");
//header("Access-Control-Allow-Methods: POST");
//header("Access-Control-Max-Age: 3600");
//header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// require '../../../vendor/autoload.php'; // initialize composer library
// use \Firebase\JWT\JWT; // declaring class
// $jwt_password = $_SERVER['HTTP_JWT_PASSWORD']; // from enviroment variable



include('../../../config/return_function.php');
include('../../../config/conn.php');
include('../../../model/room.php');
include('../../../model/member.php');
//include('../../../middleware/room_middleware.php');

$room = new room($conn);
$member = new member($conn);
$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
	case 'POST':
        $request_data = $_POST;
        if(!isset($request_data['ops_type'])){
            return_fail('ops_type has to be provided in request');
        }
		$ops_type = (string) $request_data['ops_type'];
		//$jwt = $request_data['jwt'];
        switch ($ops_type){
            case 'register':
                // middleware to check system room 
                //middleware_system_room($request_data);
                $resp_array = $room->register($request_data);
                if(isset($resp_array['code'])){
                        // join to group
                        //echo "resp array is ".json_encode($resp_array);
                        $join_status = $member->owner_join($resp_array);
                        if(isset($join_status['status'])){
                                $join_status['room_code'] = $resp_array['code'];
                                return_success("room_created_joined",$resp_array['code']);
                        }
                }
                break;
            case 'login':
                // middleware to check system room 
                //middleware_system_room($request_data);
                $room->login($request_data);
                break;
            case 'update':
                // middleware to check system room 
                //middleware_system_room($request_data);
                $room->update($request_data);
                break;
            case 'delete':
                // middleware to check system room 
                //middleware_system_room($request_data);
                $room->delete($request_data);
                break;
            case 'select_all':
                $room->select_all();
                break;
            default :
                return_fail('unknow_ops_type',$ops_type);
                break;
        }
	default:
		# code...
		//echo "undefined method =>".$method;
		return_fail("unknow_method",$method);
		break;
}
?>