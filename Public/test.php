<?php
/**
 * Created by PhpStorm.
 * User: bengeos
 * Date: 2/22/17
 * Time: 7:43 PM
 */
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST') {
    // The request is using the POST method
    $data = $_POST['service'];
    $regex = '/^A/';
    if(preg_match($regex,$data)){
        print_r("Exp matched");
    }else{
        print_r("Exp Not matched");
    }
}elseif ($method === 'GET'){
    print_r("This is GET request");
}