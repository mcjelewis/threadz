<?php
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//  Name: Threadz
//  Author: Matt Lewis
//  Organization: Eastern Washington University - Instructional Technology
//  Copyright: Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License
//  Version: 1.0
//  Page Description: This page connects to the Canvas LMS and saves topic and post discussion data into SESSION.
//  Embedded Into: tokenAuth.php
//  Uses: functionsLMS.php
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

if($current_token){
    $authorization = "Authorization: Bearer ". $current_token;
    unset($_SESSION['select_list_option']);
    unset($_SESSION['check_list_option']);

    $urlRoster = $_SESSION['domainLMS']."/api/v1/courses/".$_SESSION['courseID']."/enrollments?per_page=50";
    $arrCurlRoster=getCanvasAPIcurl($authorization, $urlRoster);

    //get the data out of the curl API call and add it to Session
    getCanvasRoster($arrCurlRoster, $authorization);

    //Count the number of topics in course
    $_SESSION['countOfTopic'] = 0;
    
    $urlTopics = $_SESSION['domainLMS']."/api/v1/courses/".$_SESSION['courseID']."/discussion_topics?per_page=95&plain_messages=true&order_by=title";
    $arrCurlTopics=getCanvasAPIcurl($authorization, $urlTopics);

    //get the data out of the curl API call and add it to Session
    getCanvasTopicList($arrCurlTopics, $authorization);

    //get groups https://canvas.auckland.ac.nz:443/api/v1/courses/41704/groups
    $urlGroup =  $_SESSION['domainLMS']."/api/v1/courses/".$_SESSION['courseID']."/groups?per_page=50";
    $arrCurlGroup=getCanvasAPIcurl($authorization, $urlGroup);
    //get the data out of the curl API call and add it to Session
    getCanvasGroup($arrCurlGroup, $authorization);
    //get group discussion loop 
    if ( array_key_exists( "groups", $_SESSION) ) {
        $_SESSION['select_list_option'] .="<optgroup label='Group discussion'>";
        foreach( $_SESSION['groups'] as $group){
            $urlTopics = $_SESSION['domainLMS']."/api/v1/groups/".$group['id']."/discussion_topics?per_page=95&plain_messages=true&order_by=title";
            $arrCurlTopics=getCanvasAPIcurl($authorization, $urlTopics);
    
            //get the data out of the curl API call and add it to Session
            getCanvasGroupTopicList($arrCurlTopics, $authorization, $group['id']);
        }
        $_SESSION['select_list_option'] .="</optgroup>";
    }

}
?>