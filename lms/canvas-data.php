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
    
    $urlRoster = $_SESSION['domainLMS']."/api/v1/courses/".$_SESSION['courseID']."/enrollments?per_page=100";
    $arrCurlRoster=getCanvasAPIcurl($authorization, $urlRoster);

    //get the data out of the curl API call and add it to Session
    getCanvasRoster($arrCurlRoster, $authorization);

    //Count the number of topics in course
    $_SESSION['countOfTopic'] = 0;
    
    $urlTopics = $_SESSION['domainLMS']."/api/v1/courses/".$_SESSION['courseID']."/discussion_topics?per_page=100";
    $arrCurlTopics=getCanvasAPIcurl($authorization, $urlTopics);

    //get the data out of the curl API call and add it to Session
    getCanvasTopicList($arrCurlTopics, $authorization);

}
?>
