<?php
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//  Name: Threadz
//  Author: Matt Lewis
//  Organization: Eastern Washington University - Instructional Technology
//  Copyright: Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License
//  Version: 1.0
//  Page Description: This page launches the LTI. If LTI validates, redirects to the lms server oauth page which then forwards
//                    on to the URI set in the LTI. In this case that page is tokenAuth.php.
//  Called From: The LTI connection from LMS.
//  Uses: preferences.php, function.php, blti.php
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_destroy();
ini_set('session.gc_maxlifetime', 1800);
setcookie("Threadz",time(),time()+1800);
session_start();
error_reporting(E_ALL); ini_set('display_errors', 'On');
$_SESSION['expire'] = time() + (30 * 60);


//////////////////////////////////////////////////////
//Set other variables used in the data collection process
//$current_token = 1;
$post_count = 0;
$select_list_option="";
$array_title = "";
$_SESSION['postNum'] = 0;
empty($_SESSION['messageArray']);
unset($_SESSION['messageArray']);
//////////////////////////////////////////////////////

require_once("preferences.php");    
require_once("functions.php");
//require_once("ims-blti/blti.php");

//////////////////////////////////////////////////////

//The php script uses the server function 'allow_url_fopen' to collect the json data from the API data call.
//In order for this tool to function, your web server settings for 'allow_url_fopen' will need to be set to 'on'.
if(!ini_get('allow_url_fopen')) {
    die('Your web server admin will need to edit the php.ini file. <br>allow_url_fopen is disabled - the php function file_get_contents() will not work.');
}
if(!function_exists('curl_version')){
    die('Your web server admin will need to enable cURL in order for Threadz to run properly.');
}
//////////////////////////////////////////////////////

//Add in LMS data collection
        switch($lms) {
            //Canvas
            case "canvas":
                $_SESSION['dataPage'] = 'canvas-data.php';
                $_SESSION['userID'] = $_REQUEST['custom_canvas_user_id'];
                $_SESSION['courseID'] = $_REQUEST['custom_canvas_course_id'];
                break;
            //Moodle
            case "moodle":
                $_SESSION['dataPage'] = 'moodle-data.php';
                $_SESSION['userID'] = '';
                $_SESSION['courseID'] = '';
                break;
            //Sakai
            case "sakai":
                $_SESSION['dataPage'] = 'sakai-data.php';
                $_SESSION['userID'] = '';
                $_SESSION['courseID'] = '';
                break;
            //Blackboard
            case "blackboard":
                $_SESSION['dataPage'] = 'bb-data.php';
                $_SESSION['userID'] = '';
                $_SESSION['courseID'] = '';
                break;
            //Desire2Learn
            case "desire2learn";
                $_SESSION['dataPage'] = 'd2l-data.php';
                $_SESSION['userID'] = '';
                $_SESSION['courseID'] = '';
                break;
            default:
                echo "Please ask Admin set LMS in Threadz launch.";
                break;
        }
 
//////////////////////////////////////////////////////
//testing the returned data from lti launch
    //foreach($_REQUEST as $key => $val){
    //    echo $key ." = " . $val . "<br>";
    //}
    //exit();
    
//////////////////////////////////////////////////////
//roles, oauth_nonce and launch url provided back from lti launch data
$_SESSION['rolesLTI'] = $_REQUEST['roles'];
$_SESSION['roles'] = setRoles($_REQUEST['roles']);
$_SESSION['token_state_id'] = $_REQUEST['oauth_nonce'];
#$_SESSION['domainLMS'] = 'https://'. parse_url($_REQUEST['launch_presentation_return_url'], PHP_URL_HOST);

//////////////////////////////////////////////////////
//Get the token for the user
//redirects to the URI set in LTI
header('Location: '. $_SESSION['domainLMS'].'/login/oauth2/auth?client_id='.$_SESSION['client_id'].'&response_type=code&redirect_uri='.$_SESSION['domainThreadz'].'/tokenAuth.php&state='.$_SESSION['token_state_id']);
exit();   



?>
