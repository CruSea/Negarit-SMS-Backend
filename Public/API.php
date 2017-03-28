<?php
/**
 * Created by PhpStorm.
 * User: bengeos
 * Date: 3/3/17
 * Time: 8:38 AM
 */
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}
require_once ("../Private/DataBase.php");
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
abstract class Results {
    const ALL = 'Companies';
    const ALL_MESSAGES = 'Messages';
    const All_COMPANIES = 'Companies';
    const All_DEVICES = 'Devices';
    const ADDED_MESSAGES = 'Added_Messages';
    const Auth_User = 'AuthUser';
    const LOG_RESPONSE = 'Log_Response';
    const LOG_ID = 'Log_ID';
    const UPLOAD_RESPONSE = 'Upload_Response';
}
abstract class userSignUp {
    const COMPANY_NAME = 'company_name';
    const FULL_NAME = 'full_name';
    const USER_NAME = 'user_name';
    const User_EMAIL = 'user_email';
    const USER_PASSWORD = 'user_pass';
}
abstract class RespErr {
    const PARAMETER_ERROR = 'Parameter_Error';
    const SERVICE_REQUEST = 'Service_Request';
    const REQUEST_FORMAT =  'Request_Format';
    const AUTHENTICATION =  'Authentication';
}
abstract class Services {
    const Get_All = 'get_all';
    const Get_Messages = 'get_messages';
    const Get_Companies = 'get_companies';
    const Get_Devices = 'get_devices';


    const Add_Messages = 'add_messages';
    const Add_Message = 'add_message';
    const Delete_Messages = 'delete_messages';
    const Delete_Message = 'delete_message';
    const Add_Companies = 'add_companies';



    const Authenticate_User = 'auth';
    const Sign_Up_User = 'sign_up';
    const Get_Companies_Devices = 'get_companies_devices';
}
function processAPI($request){
    $api_Request = array_change_key_case($request, CASE_LOWER);
    if(isValidRequest($api_Request)){
        return processRequest($api_Request);
    }else{
        $error[RespErr::REQUEST_FORMAT] = "Invalid Request Format";
        $api_Response[Response::REQUEST_ERROR] = $error;
    }
    return $api_Response;
}
function isValidRequest($data){
    if (isset($data[ReqFormat::USERNAME]) && isset($data[ReqFormat::PASSWORD]) && isset($data[ReqFormat::SERVICE]) && isset($data[ReqFormat::PARAM])) {
        return true;
    } else {
        return false;
    }
}
function RandomString()
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randstring = '';
    for ($i = 0; $i < 10; $i++) {
        $randstring .= $characters[rand(0, strlen($characters))];
    }
    return $randstring;
}
function processRequest($request){
    $api_Response = array();
    $myDatabase = new DataBase();
    $mainUser = array();
    $mainUser[userTable::USER_NAME] = $request[ReqFormat::USERNAME];
    $mainUser[userTable::USER_PASS] = $request[ReqFormat::PASSWORD];
    $mainUser[userTable::USER_EMAIL] = $request[ReqFormat::USERNAME];
    if ($request[ReqFormat::SERVICE] == Services::Authenticate_User){
        $auth = array();
        $auth[Results::Auth_User] = $myDatabase->checkUser($mainUser);
        $api_Response[Response::RESPONSE] = $auth;
    }elseif ($request[ReqFormat::SERVICE] == Services::Sign_Up_User){
        $param = json_decode($request[ReqFormat::PARAM], true);
        if (is_array($param)) {
            if(isset($param[userSignUp::COMPANY_NAME]) && isset($param[userSignUp::FULL_NAME]) && isset($param[userSignUp::USER_NAME]) && isset($param[userSignUp::User_EMAIL]) && isset($param[userSignUp::USER_PASSWORD])){
                $newUser = array();
                $newUser[userTable::USER_FullName] = $param[userSignUp::FULL_NAME];
                $newUser[userTable::USER_NAME] = $param[userSignUp::USER_NAME];
                $newUser[userTable::USER_EMAIL] = $param[userSignUp::User_EMAIL];
                $newUser[userTable::USER_PASS] = $param[userSignUp::USER_PASSWORD];
                $user_id = $myDatabase->addUser($newUser);
                $newCompany = array();
                $newCompany[companyTable::NAME] = $param[userSignUp::COMPANY_NAME];
                $newCompany[companyTable::DESCRIPTION] = "New Company";
                $newCompany[companyTable::ISACTIVE] = "1";
                $company_id = $myDatabase->addCompany($newCompany);
                if(($user_id>0) && ($company_id>0)){
                    $newUserCompanyRole = array();
                    $newUserCompanyRole[userTable::ID] = $user_id;
                    $newUserCompanyRole[companyTable::ID] = $company_id;
                    $newUserCompanyRole[roleTable::ID] = userROLE::ADMIN;
                    $state1 = $myDatabase->addUserCompanyRole($newUserCompanyRole);
                    $state2 = $myDatabase->addCompanyUser($newUserCompanyRole);
                    if($state1 && $state2){
                        $api_Response[Response::RESPONSE] = "Successfully Registered";
                    }else{
                        $api_Response[Response::REQUEST_ERROR] = "Registration has failed";
                    }
                }else{
                    $api_Response[Response::REQUEST_ERROR] = "The account is already taken!";
                }

            }else{
                $api_Response[Response::REQUEST_ERROR] = "Invalid User Sign up Format";
            }
        }
    } elseif($myDatabase->checkUser($mainUser) != null){
        if($request[ReqFormat::SERVICE] == Services::Get_All){
            $all_data = array();
            $all_data[Results::ALL] = $myDatabase->getAll_ByUser($mainUser);
            $api_Response[Response::RESPONSE] = $all_data;
        }elseif($request[ReqFormat::SERVICE] == Services::Get_Messages){
            $messages = array();
            $messages[Results::ALL_MESSAGES] = $myDatabase->getMessagesByUser($mainUser);
            $api_Response[Response::RESPONSE] = $messages;
        }elseif ($request[ReqFormat::SERVICE] == Services::Get_Companies) {
            $companies = array();
            $companies[Results::All_COMPANIES] = $myDatabase->getCompaniesByUser($mainUser);
            $api_Response[Response::RESPONSE] = $companies;
        }elseif ($request[ReqFormat::SERVICE] == Services::Get_Companies_Devices){
            $companies = array();
            $companies[Results::All_COMPANIES] = $myDatabase->getCompaniesDevicesByUser($mainUser);
            $api_Response[Response::RESPONSE] = $companies;
        }elseif ($request[ReqFormat::SERVICE] == Services::Get_Devices){
            $devices = array();
            $devices[Results::All_DEVICES] = $myDatabase->getDevicesByUser($mainUser);
            $api_Response[Response::RESPONSE] = $devices;
        }elseif ($request[ReqFormat::SERVICE] == Services::Add_Message){
            $params = json_decode($request[ReqFormat::PARAM], true);
            if (is_array($params)) {
                if(isset($params[messageTable::MESSAGE_CONTENT]) && isset($params[messageTable::MESSAGE_SENT_TO]) && isset($params[deviceTable::DEVICE_NAME])){
                    $device = $myDatabase->getDeviceByUserDeviceName($mainUser,$params);
                    if($device){
                        $newMessage = array();
                        $newMessage[messageTable::MESSAGE_CONTENT] = $params[messageTable::MESSAGE_CONTENT];
                        $newMessage[messageTable::MESSAGE_FROM] = $device[deviceTable::DEVICE_PHONE];
                        $newMessage[messageTable::MESSAGE_SENT_TO] = $params[messageTable::MESSAGE_SENT_TO];
                        $newMessage[messageTable::MESSAGE_DEVICE_ID] = $device[deviceTable::ID];
                        $newMessage[messageTable::MESSAGE_IS_OUTGOING] = 1;
                        $newMessage[messageTable::MESSAGE_IS_DELIVERED] = 0;
                        $newMessage[messageTable::MESSAGE_IS_DELIVERED] = 0;
                        $newMessage[messageTable::MESSAGE_ID] = RandomString().'-'.RandomString().'-'.date('Ymd').date('His');
                        $state = $myDatabase->addMessage($newMessage);
                        if($state > 0){
                            $AddedMessage = array();
                            $AddedMessage[Results::ADDED_MESSAGES] = $newMessage;
                            $api_Response[Response::RESPONSE] = $AddedMessage;
                        }
                    }
                }else{
                    $api_Response[Response::REQUEST_ERROR] = "Invalid param entries for add_messages service ".json_encode($params);
                }
            }else{
                $api_Response[Response::REQUEST_ERROR] = "Invalid param for add_messages service";
            }
        }elseif ($request[ReqFormat::SERVICE] == Services::Delete_Message){
            $params = json_decode($request[ReqFormat::PARAM], true);
            if (is_array($params)) {
                if(isset($params[messageTable::MESSAGE_ID]) && isset($params[deviceTable::DEVICE_NAME]) ){
                    $device = $myDatabase->getDeviceByUserDeviceName($mainUser,$params);
                    if($device){
                        $deleteMessage = array();
                        $deleteMessage[messageTable::MESSAGE_ID] = $params[messageTable::MESSAGE_ID];
                        $deleteMessage[messageTable::MESSAGE_DEVICE_ID] = $device[deviceTable::ID];
                        $state = $myDatabase->deleteMessageByMessageIDDeviceID($deleteMessage);
                        if($state){
                            $api_Response[Response::RESPONSE] = "Successfully Deleted";
                        }else{
                            $api_Response[Response::REQUEST_ERROR] = "Message Not Deleted";
                        }
                    }else{
                        $api_Response[Response::REQUEST_ERROR] = "Wrong Device Name";
                    }
                }else{
                    $api_Response[Response::REQUEST_ERROR] = "Invalid param entry for delete_message service";
                }
            }else{
                $api_Response[Response::REQUEST_ERROR] = "Invalid param for delete_message service";
            }
        }else{
            $api_Response[Response::REQUEST_ERROR] = "Unknown Service Request";
        }
    }else{
        $api_Response[Response::RESPONSE] = "Invalid User Account";
    }
    return $api_Response;
}

if(isset($_POST)){
    echo json_encode(processAPI($_POST));
}else{
    echo  "Invalid API request Made";
}

