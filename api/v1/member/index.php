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
include('../../../model/member.php');
include('../../../model/room.php');
//include('../../../middleware/member_middleware.php');

$member = new member($conn);
$room = new room($conn);
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
                // middleware to check system member 
                //middleware_system_member($request_data);
                // we have to get code to room id
                $request_data['room'] = $room->get_id_by_code($request_data);
                $member->register($request_data);
                break;
            case 'update':
                // middleware to check system member 
                //middleware_system_member($request_data);
                $member->update($request_data);
                break;
            case 'delete':
                // middleware to check system member 
                //middleware_system_member($request_data);
                $member->delete($request_data);
                break;
            case 'select_all':
                $member->select_all();
                break;
            case 'room_list':
                $member->room_list($request_data);
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