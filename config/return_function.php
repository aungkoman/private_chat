<?php
function return_success($msg,$data = array()){
    $return_obj['status'] = true;
    $return_obj['msg'] = $msg;
    $return_obj['data'] = $data;
    //return $return_obj;
    echo json_encode($return_obj);
    exit;
}
function return_fail($msg,$data=array()){
    $return_obj['status'] = false;
    $return_obj['msg'] = $msg;
    $return_obj['data'] = $data;
    //return $return_obj;
    echo json_encode($return_obj);
    exit;
}
?>