<?php
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//  Name: Threadz
//  Author: Matt Lewis
//  Organization: Eastern Washington University - Instructional Technology
//  Copyright: Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License
//  Version: 1.0
//  Page Description: This page extracts the topic post data from SESSION and pushes it back to be used in the D3 function to create the graph.
//  Called From: threadz.php
//  Uses: function.php
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
session_start();
require_once("functions.php");
if(isset($_COOKIE['Threadz'])){
  $topic_id = $_POST['topic_id'];
  empty($_SESSION['d3_'.$topic_id]);
  unset($_SESSION['d3_'.$topic_id]);
  //the function d3Data() creates an array of lms data used by the d3 library
  d3Data($topic_id);

  //encode to json and save array to session
  $discussionData = json_encode($_SESSION['d3_'.$topic_id]);
  
  //Save to Session for Chord Diagram
  $_SESSION['discussionData'] = $discussionData;
  
  //send back to threadz.php
  echo $discussionData;
}else{
    echo 'Expired Session, please reauthenticate Threadz.';
}
?>



