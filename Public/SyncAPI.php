<?php
/**
 * Created by PhpStorm.
 * User: bengeos
 * Date: 3/7/17
 * Time: 8:50 AM
 */
require_once("../Private/DataBase.php");
$method = $_SERVER['REQUEST_METHOD'];
$DataBase = new DataBase();
//print_r($DataBase->addMessage($data1));
if ($method === 'POST') {
    writelog("POST Request");
    writelog("POST Request: -> ".json_encode($_POST));
    if(isset($_POST)){
        $device = array();
        $device[deviceTable::DEVICE_NAME] = $_POST['device_id'];
        $device[deviceTable::DEVICE_PASS] = $_POST['secret'];
        $Device = $DataBase->getDeviceByNameAndPass($device);
        writelog("Found Device ID".json_encode($Device));
        if(isset($Device) && isset($Device['ID'])){
            $newMessage = array();
            $newMessage[messageTable::MESSAGE_ID] = $_POST['message_id'];
            $newMessage[messageTable::MESSAGE_FROM] = $_POST['from'];
            $newMessage[messageTable::MESSAGE_CONTENT] = $_POST['message'];
            $newMessage[messageTable::MESSAGE_SENT_TO] = $_POST['sent_to'];
            $newMessage[messageTable::MESSAGE_DEVICE_ID] = $Device['ID'];
            $newMessage[messageTable::MESSAGE_IS_OUTGOING] = 0;
            $newMessage[messageTable::MESSAGE_IS_DELIVERED] = 1;
            $state = $DataBase->addMessage($newMessage);
            $payload = array();
            $payload['success'] = true;
            $payload['error'] = null;
            $res = array();
            $res['payload'] = $payload;
            echo json_encode($res);
        }
    }
}elseif ($method === 'GET'){
    writelog("Get Request");
    writelog("Get Request: -> ".json_encode($_GET));
    if(isset($_GET['task']) && $_GET['task'] == "send"){
        $pendingDevice = $DataBase->getPendingDevice();
        if(isset($pendingDevice) && isset($pendingDevice['ID'])){
            $pendingSMS = $DataBase->getPendingSMSByDeviceID($pendingDevice);
            writelog("Pending Message: -> ".json_encode($pendingSMS));
            $payload = array();
            $payload['task'] = "send";
            $payload['secret'] = $pendingDevice[deviceTable::DEVICE_PASS];
            $payload['messages'] = $pendingSMS;
            $res = array();
            $res['payload'] = $payload;
            writelog("Sending message".json_encode($res));
            echo json_encode($res);
        }else{
            $payload = array();
            $payload['success'] = "true";
            $payload['error'] = null;
            $res = array();
            $res['payload'] = $payload;
            echo json_encode($res);
        }
    }elseif(isset($_GET['task'])){
        $payload = array();
        $payload['task'] = "send";
        $payload['error'] = null;
        $res = array();
        $res['payload'] = $payload;
        echo json_encode($res);
    }
}
function writelog($txt_data){
    file_put_contents("log.txt","\n".json_encode($txt_data),FILE_APPEND | LOCK_EX);
}