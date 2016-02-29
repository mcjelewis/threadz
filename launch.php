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
//  Uses: function.php, blti.php
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
session_destroy();
ini_set('session.gc_maxlifetime', 1800);
setcookie("Threadz",time(),time()+1800);
session_start();
error_reporting(E_ALL); ini_set('display_errors', 'On');
$_SESSION['expire'] = time() + (30 * 60);

//////////////////////////////////////////////////////
//Set LTI domain
//The domainThreadz variable needs to match the URI provided in the original LTI form. Any redirects from the OAuth2 process must use this domain.
$_SESSION['domainThreadz'] = "Server URL of where your Threadz folder is located";

//If you are going through a proxy, you can add that here, otherwise leave blank.
$_SESSION['proxy'] = '';

//////////////////////////////////////////////////////
//In it's current state, Threadz does not support other LMS platforms besides Instructure's Canvas.  Each LMS needs to be able to access
//discussion board data via an API.  Development needs to be done on each LMS page to access the data and save it in the array
//format used by the visualizations created in D3.

//Set LMS - uncomment the LMS used for this LTI
$lms = 'canvas';
//$lms = 'moodle';
//$lms = 'sakai';
//$lms = 'blackboard';
//$lms = 'desire2learn';

//////////////////////////////////////////////////////
//Set the varibles needed to collect the API discussion data using the users own access token.
//CThe current process for the creation of Canvas developer keys is to have your Canvas admin generate them. The Canvas guides have a helpful description about the process (https://guides.instructure.com/m/4214/l/441833-how-do-i-add-a-developer-key-for-an-account).
$_SESSION['client_id'] = 000;  //replace with your client id
$_SESSION['client_secret'] = "Your Dev Key"; //replace with your key


//////////////////////////////////////////////////////
//set the domainLMS from the launch url returned from LTI
$_SESSION['domainLMS'] = 'https://'. parse_url($_REQUEST['launch_presentation_return_url'], PHP_URL_HOST);
//////////////////////////////////////////////////////
//set variable to the shared secret used when setting up the lti.
$shared_secret = "threadz-v1";
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
    
require_once("functions.php");
require_once("ims-blti/blti.php");

//////////////////////////////////////////////////////

//The php script uses the server function 'allow_url_fopen' to collect the json data from the API data call.
//In order for this tool to function, your web server settings for 'allow_url_fopen' will need to be set to 'on'.
if(!ini_get('allow_url_fopen')) {
    die('Your web server admin will need to edit the php.ini file. <br>allow_url_fopen is disabled - the php function file_get_contents() will not work.');
}
 
//////////////////////////////////////////////////////

//Add in LMS data collection
        switch($lms) {
            //Canvas
            case "canvas":
                $_SESSION['dataPage'] = 'canvas-data.php';
                break;
            //Moodle
            case "moodle":
                $_SESSION['dataPage'] = 'moodle-data.php';
                break;
            //Sakai
            case "sakai":
                $_SESSION['dataPage'] = 'sakai-data.php';
                break;
            //Blackboard
            case "blackboard":
                $_SESSION['dataPage'] = 'bb-data.php';
                break;
            //Desire2Learn
            case "desire2learn";
                $_SESSION['dataPage'] = 'd2l-data.php';
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
//Course id, oauth_nonce and launch url provided back from lti launch data
$_SESSION['courseID'] = $_REQUEST['custom_canvas_course_id'];
$_SESSION['token_state_id'] = $_REQUEST['oauth_nonce'];
$_SESSION['domainLMS'] = 'https://'. parse_url($_REQUEST['launch_presentation_return_url'], PHP_URL_HOST);

//Get the token for the user
//redirects to the URI set in LTI
header('Location: '. $_SESSION['domainLMS'].'/login/oauth2/auth?client_id='.$_SESSION['client_id'].'&response_type=code&redirect_uri='.$_SESSION['domainThreadz'].'/tokenAuth.php&state='.$_SESSION['token_state_id']);
exit();


?>