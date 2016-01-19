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
<script>
  $(document).ready(function() {
    $( "#accordion" ).accordion({
      collapsible: true,
      heightStyle: "content",
      active: 3
    });
  });
  </script>
<body>
<div id='accordion'>
   
        
    <h3>Road Map</h3>
    <div id='RoadMap'>
        <p>There are several items that will continue to be worked on to improve Threadz.  Below is a list of some of those items and a rough timeframe of when they might be implemented.</p>
        <ul>
            <li>Next Up
               <ul>
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
    </div>
        
    <h3>License</h3>
    <div id='license'>
        <p>
          <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/4.0/"><img alt="Creative Commons License" style="border-width:0" src="https://i.creativecommons.org/l/by-nc-sa/4.0/88x31.png" /></a>
          <br>
          <span xmlns:dct="http://purl.org/dc/terms/" property="dct:title">Threadz</span> by <span xmlns:cc="http://creativecommons.org/ns#" property="cc:attributionName">Eastern Washington University - Instructional Technology</span> is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/4.0/">Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License</a>.
        </p>
    </div>
        
    <h3>Acknowledgements</h3>
    <div id='acknowledgements'>
        <p><a href='http://www.snappvis.org/'>SNAPP (Social Network Adapting Pedagogical Practice)</a><br>
        Dr. Shane Dawson etal.  <br>
        The SNAPP tool is the work of Dr. Shane Dawson etal. and is a similar visualization tool, that creates visualizations of the interactions within a discussion forum.  SNAPP however is not compatible with Canvas, thus the need for Threadz.
        </p>
        
        <p><a href='https://code.google.com/p/basiclti4wordpress/source/browse/trunk/producer/mu-plugins/IMSBasicLTI/ims-blti/?r=2'>IMSBasicLTI</a><br>
        Copyright (c) 2007 Andy Smith  <br>
        The cody provided by Andy Smith creates a secure Oauth class to access the lti launch data.
        </p>
        
        <p><a href='http://d3js.org/'>D3.js</a><br>
        Copyright (c) 2010-2015, Michael Bostock  <br>
        D3.js is a JavaScript library for manipulating documents based on data. D3 helps you bring data to life using HTML, SVG, and CSS. D3's emphasis on web standards gives you the full capabilities of modern browsers without tying yourself to a proprietary framework, combining powerful visualization components and a data-driven approach to DOM manipulation.  
        </p>
    </div>
     <h3>About</h3>
    <div id="about">
        <p>Built as a Learning Tools Interoperability (LTI) integration for the learning management system Canvas, Threadz is a discussion visualization tool that adds graphs and statistics to online discussions.</p>
        <p>Online discussions provide valuable information about the dynamics of a course and its constituents.  Much of this information is found within the content of the posts, but other elements are hidden within the social network connection and interactions between students and between students and instructors.  Threadz is a tool that extracts this hidden information and puts it on display.</p>
        <p>The visual representations created from social network connections and interactions between students and instructors in a discussion assist in identifying specific behaviors and characteristics within the course, such as: learner isolation, non-integrated groups, instructor-centric discussions, and key integration (power) users and groups. By identifying these behaviors and characteristics, the instructor can affect change in these interactions to help make the discussions and classroom discourse more accessible to all.</p>
        <p>The permissions to use this tool are not restricted by role. Both students and teachers are able to access Threadz when made available.  To disable Threadz, within the course settings Navigation tab, move Threadz below the hide from students line.</p>
      
        <h3>Contact</h3>
        <p>This tool was created at Eastern Washington University by Instructional Technology Design and Development. Please contact Matt Lewis (mlewis23@ewu.edu) for more information.</p>
      

        <h3>Discussion Topic List</h3>
        <p>Select the title of a discussion from the drop down list above the chart tabs.  The list includes all published discussions that have one or more posts submitted to it.</p>
      
        <h3>Saving Graphics</h3>
        <p>On each of the charts there are buttons to save the graphic as a SVG, PDF or PNG file types. At this time, the graphics do not mantain the full set of formatting added to what you see on screen. While not perfect, the saved image is a good start.</p>
      
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
</div>

</body>
</html>
