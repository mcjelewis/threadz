<?php
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//  Name: Threadz
//  Author: Matt Lewis
//  Organization: Eastern Washington University - Instructional Technology
//  Copyright: Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License
//  Version: 1.0
//  Page Description: This page sets the access token from the LTI for the user. If LTI validates, this page then collects data from LMS then redirects to display page threadz.
//  Called From: The LTI connection from LMS.
//  Uses: function.php, blti.php, lms/canvas-data.php (lms data collection page)
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
session_start();
//Redirect retuns the state and code variables.
$state_test = $_REQUEST['state'];
$code = $_REQUEST['code'];
$error = $_REQUEST['error'];

if($error){
    echo "Access Denied";
    exit();
}


//Check to make sure the state passed into the auth request is the same one returned.
if($_SESSION['token_state_id'] == $state_test){
    //Post request with client_id, client_secret, and code (returned from above redirect) to /login/oauth2/token to obtain access token.
    $token_url = $_SESSION['domainLMS'].'/login/oauth2/token';
    $token_data = array('client_id' => $_SESSION['client_id'], 'redirect_uri' => $_SESSION['domainThreadz'] . "/tokenAuth.php", 'client_secret' => $_SESSION['client_secret'], 'code' => $code);    

    //http://stackoverflow.com/questions/5647461/how-do-i-send-a-post-request-with-php
    //$token_options = array(
    //    // use key 'http' even if you send the request to https://...
    //    'http' => array(
    //        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
    //        'method'  => 'POST',
    //        'content' => http_build_query($token_data),
    //    ),
    //);
    //$context  = stream_context_create($token_options);
    //$result = file_get_contents($token_url, false, $context);
    
    $result = connectCanvasAPI($token_url,$token_data,'Post',$_SESSION['proxy']);
    
    // save token into variable
    $jsonToken = json_decode($result, true);
    $current_token = $jsonToken['access_token'];

    //Collect the data from the lms page
    include("lms/". $_SESSION['dataPage']);
    
    //save the html topic list into SESSION.
    $_SESSION['select_list_option']= $select_list_option;

    //redirect to display page
    header('Location: '.$_SESSION['domainThreadz'].'/threadz.php');
    exit();
}else{
    echo "Altered States.";
    exit();
}




?>