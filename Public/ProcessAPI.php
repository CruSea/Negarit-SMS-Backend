<?php

/**
 * Created by PhpStorm.
 * User: bengeos
 * Date: 3/3/17
 * Time: 9:07 AM
 */
require_once("../Private/DataBase.php");
abstract class ReqFormat {
    const USERNAME = 'username';
    const PASSWORD = 'userpass';
    const SERVICE = 'service';
    const PARAM = 'param';
}
abstract class Response {
    const RESPONSE = 'Response';
    const REQUEST_ERROR = 'Request_Error';
    const LOG_RESPONSE = 'Log_Response';
    const LOG_ID = 'Log_ID';
    const UPLOAD_RESPONSE = 'Upload_Response';
}
abstract class RespErr {
    const PARAMETER_ERROR = 'Parameter_Error';
    const SERVICE_REQUEST = 'Service_Request';
    const REQUEST_FORMAT =  'Request_Format';
    const AUTHENTICATION =  'Authentication';
}
abstract class Services {
    const GetAllMessages = 'getAllMessages';
    const AddNewMessage = 'addNewMessage';
    const DeleteMessage = 'deleteMessage';
}
class ProcessAPI
{
    /**
     * ProcessAPI constructor.
     */
    public function __construct()
    {
        print_r("asd");
        $this->DataBase = new DataBase();
        $this->api_Response = array();
        $this->api_Request = array();
    }
    public function processRequest($request){
        $this->api_Request = array_change_key_case($request, CASE_LOWER);
        if($this->isValidRequest($this->api_Request)){
            $this->api_Response[Response::REQUEST_ERROR] = "success";
        }
        return json_encode($this->api_Response);
    }
    private function isValidRequest($data){
        if (isset($data[ReqFormat::USERNAME]) && isset($data[ReqFormat::PASSWORD]) && isset($data[ReqFormat::SERVICE]) && isset($data[ReqFormat::PARAM])) {
            return true;
        } else {
            $error[RespErr::REQUEST_FORMAT] = Error::INVALID_REQUEST;
            $this->api_Response[Response::REQUEST_ERROR] = $error;
            return false;
        }
    }
}