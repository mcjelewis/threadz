<?php
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//  Name: Threadz
//  Author: Matt Lewis
//  Organization: Eastern Washington University - Instructional Technology
//  Copyright: Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License
//  Version: 1.0
//  Page Description: This page displays the content in the 'About'  .
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

   <div id="creditsbody">
    <p>Built as a Learning Tools Interoperability (LTI) integration for the learning management system Canvas, Threadz is a discussion visualization tool that adds graphs and statistics to online discussions.</p>
    <p>Online discussions provide valuable information about the dynamics of a course and its constituents.  Much of this information is found within the content of the posts, but other elements are hidden within the social network connection and interactions between students and between students and instructors.  Threadz is a tool that extracts this hidden information and puts it on display.</p>
    <p>The visual representations created from social network connections and interactions between students and instructors in a discussion assist in identifying specific behaviors and characteristics within the course, such as: learner isolation, non-integrated groups, instructor-centric discussions, and key integration (power) users and groups. By identifying these behaviors and characteristics, the instructor can affect change in these interactions to help make the discussions and classroom discourse more accessible to all.</p>
    <p>The permissions to use this tool are not restricted by role. Both students and teachers are able to access Threadz when made available.  To disable Threadz, within the course settings Navigation tab, move Threadz below the hide from students line.</p>
  
  <h3>Contact</h3>
    <p>This tool was created at Eastern Washington University by Instructional Technology Design and Development. Please contact Matt Lewis (mlewis23@ewu.edu) for more information.</p>
  
  <h3>Acknowledgements</h3>
    <p><a href='http://www.snappvis.org/'>SNAPP (Social Network Adapting Pedagogical Practice)</a><br>
    Dr. Shane Dawson etal.  <br>
    The SNAPP tool is the work of Dr. Shane Dawson etal. and is a similar visualization tool, that creates visualizations of the interactions within a discussion forum.  SNAPP however is not compatible with Canvas, thus the need for Threadz.
    </p>
    
    <p><a href='https://code.google.com/p/basiclti4wordpress/source/browse/trunk/producer/mu-plugins/IMSBasicLTI/ims-blti/?r=2'>IMSBasicLTI</a><br>
    Copyright (c) 2007 Andy Smith  <br>
    The cody provided by Andy Smith creates a secure Oauth class to access the lti launch data.
    </p>
    
    <p><a href='http://d3js.org/'>D3.js]</a><br>
    Copyright (c) 2010-2015, Michael Bostock  <br>
    D3.js is a JavaScript library for manipulating documents based on data. D3 helps you bring data to life using HTML, SVG, and CSS. D3's emphasis on web standards gives you the full capabilities of modern browsers without tying yourself to a proprietary framework, combining powerful visualization components and a data-driven approach to DOM manipulation.  
    </p>
  
  <h3>License</h3>
    <p>
      <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/4.0/"><img alt="Creative Commons License" style="border-width:0" src="https://i.creativecommons.org/l/by-nc-sa/4.0/88x31.png" /></a>
      <br>
      <span xmlns:dct="http://purl.org/dc/terms/" property="dct:title">Threadz</span> by <span xmlns:cc="http://creativecommons.org/ns#" property="cc:attributionName">Eastern Washington University - Instructional Technology</span> is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/4.0/">Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License</a>.
    </p>
   </div>
</body>
</html>
