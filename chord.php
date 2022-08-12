<?php
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//  Name: Threadz
//  Author: Matt Lewis
//  Organization: Eastern Washington University - Instructional Technology
//  Copyright: Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License
//  Version: 1.0
//  Page Description: This page creates the chord diagram used on the 'Chord' tab.
//  Called From: threadz.php
//  Uses: function.php
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
session_start();
require_once("functions.php");
$topic_id = $_SESSION['topic_id'];
$domainThreadz = $_SESSION['domainThreadz'];
$d3data = json_decode($_SESSION['discussionData'], true);
if(isset($_COOKIE['Threadz'])){

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Threadz - Chord Diagram</title>
    <meta charset="utf-8">
    <meta name="description" content="Visualize Canvas LMS discussions with various social network graphs.">
    <meta name="author" content="Matt Lewis">
    <meta name="robots" content="noindex,nofollow">

   <!-- <script type="text/javascript" src="<?php echo $domainThreadz ?>/lib/js/d3/d3.v3.min.js"></script>
    <script type="text/javascript" src="<?php echo $domainThreadz ?>/js/d3-visuals.js"></script>-->

</head>
<script>
   $(document).ready(function() {
         $('#saveImage').show();
   });
</script>
<body>
<?php
    if(isset($_COOKIE['Threadz'])){
?>
        <div class='d3-visual'>
            <div id="chord">
                <script>makeChordMatrix(<?php echo $_SESSION['discussionData']; ?>);</script>
            </div>
        </div>
        <div id='right-container'>
            <h4>Chord Diagram</h4>
            <p>The chord diagram puts a path to the connections shown in the other graph. The direction of the posts is represented by the thickness of the line. </p>
            <p>Hover over the outer edge of the circle on a specific user to display the number of posts sent and recieved. Hover over the line that connects two students to see the count of posts from either direction.</p>
        </div>
        <br><a class='mini' target='_blank' href='<?php echo $d3data['topic']['topic_url'] ?>'>go to Discussion</a>
<?php
    }else{
        echo 'Expired Session, please reauthenticate Threadz.';
    }
?>
</body>
</html>