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
include('../../../model/user.php');
//include('../../../middleware/user_middleware.php');

$user = new user($conn);
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
                // middleware to check system user 
                //middleware_system_user($request_data);
                $user->register($request_data);
                break;
            case 'login':
                // middleware to check system user 
                //middleware_system_user($request_data);
                $user->login($request_data);
                break;
            case 'update':
                // middleware to check system user 
                //middleware_system_user($request_data);
                $user->update($request_data);
                break;
            case 'delete':
                // middleware to check system user 
                //middleware_system_user($request_data);
                $user->delete($request_data);
                break;
            case 'select_all':
                $user->select_all();
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