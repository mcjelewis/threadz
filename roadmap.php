<?php
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//  Name: Threadz
//  Author: Matt Lewis
//  Organization: Eastern Washington University - Instructional Technology
//  Copyright: Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License
//  Version: 1.0
//  Page Description: This page displays the content in the 'Road Map' tab.
//  Called From: threadz.php
//  Uses: 
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Threadz - Road Map</title>
    <meta charset="utf-8">
    <meta name="description" content="Visualize Canvas LMS discussions with various social network graphs.">
    <meta name="author" content="Matt Lewis">
    <meta name="robots" content="noindex,nofollow">
</head>
<body id="creditsbody">
<!--  <div id='topNav'>
    <ul>
      <li><a href="dvt.php"><span>Topics</span></a></li>
    </ul>
  </div>-->
 
  <h3>Road Map</h3>
  <p>There are several items that will continue to be worked on to improve Threadz.  Below is a list of some of those items and a rough timeframe of when they might be implemented.</p>
  <ul>
      <li>Fall 2015
         <ul>
            <li>Export data</li>
            <li>Save images: saving css stylesheets</li>
            <li>Statistics - ratio of original thread word count to total posts in thread</li>
            <li>Word Cloud</li>
         </ul>
      </li>
      <li>Reported Bugs
         <ul>
            <li>Chord Diagram / Change of discussion unresponsive
               <ul>
                  <li>Problem: If the session times out, no notification message appears when trying to access other discussions or the chord diagram.</li>
                  <li>Work Around: Until this issue is addressed, the solution is to reinstigate Threadz by clicking the Threadz link in the left navigation.</li>
               </ul>
            </li>
            <li>Chord Diagram tool-tip information data not showing on mobile device
               <ul>
                  <li>Problem: If accessing the chord diagram on a moblile device, the count of post information does not show because mobile doesn't allow for hover effects.</li>
                  <li>Work Around: No current work around for this issue.</li>
               </ul>
            </li>
         </ul>
      </li>
   </ul>

</body>
</html>
