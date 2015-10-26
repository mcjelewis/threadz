<?php
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//  Name: Threadz
//  Author: Matt Lewis
//  Organization: Eastern Washington University - Instructional Technology
//  Copyright: Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License
//  Version: 1.0
//  Page Description: This page displays the content in the 'Help' tab.
//  Called From: threadz.php
//  Uses: 
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Threadz - License</title>
    <meta charset="utf-8">
    <meta name="description" content="Visualize Canvas LMS discussions with various social network graphs.">
    <meta name="author" content="Matt Lewis">
    <meta name="robots" content="noindex,nofollow">
</head>
<script>
   $(document).ready(function() {
         $('#saveImage').hide();
   });
</script>
<body>
<!--  <div id='topNav'>
    <ul>
      <li><a href="dvt.php"><span>Topics</span></a></li>
    </ul>
  </div>-->
 <div id="creditsbody">
    <h3>Discussion Topic List</h3>
    <p>Select the title of a discussion from the drop down list above the chart tabs.  The list includes all published discussions that have one or more posts submitted to it.</p>
  <h3>Network</h3> 
    <p>The social network visualizations show typical line/node graphs that connects users together. Each node represents a different user in the discussion and each line represents a post from or to another user. The relative size of the circles (nodes) can be changed to represent the value selected for either the number of posts sent, posts received, total posts, total word count of posts sent, and the average word count of posts sent. These charts are useful to quickly discern any individual or group that is isolated or conversly who are the power users within the forum.</p>
    <p>The nodes in this chart are movable to help single out individuals or groups when the network of connections gets too complex visually. To manipulate a node, click and drag a node circle to another part of the page where it will then be locked into that location until being double clicked.</p>
  
  <h3>Chord Diagram</h3>
    <p>The chord diagram displays the directional paths of the interactions between two students or instructor. The count of posts is represented by the thickness of the line at either. Threadz uses a dynamic interface allowing the user to hover over the outer edge of the circle on a specific user to display the number of posts sent and received. Hover over the chord line that connects two students to display the count of posts from either direction.</p>
  
  <h3>Timeline</h3>
    <p>The timeline visualization displays the count of discussion posts by date. This visual can help you determin the rate of submissions and determin if there are any patterns to those submissions.</p>
    
  <h3>Matrix</h3>
    <p>The matrix visualization shows the number of communication interactions per person in a high to low color scale. The color of the cell between two students shows the frequency of connections. The darker the cell the higher the frequency. The order of the matrix can be set to the total frequency of connections, number of posts sent, number of posts received, or name.</p>
  
  <h3>Statistics</h3>
    <p>The Statistics tab shows the spreadsheet view of the discussion with counts of participants, posts, threads, words per post, words per thread.</p>
 </div>
</body>
</html>
