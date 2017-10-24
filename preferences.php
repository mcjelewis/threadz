<?php
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//  Name: Threadz
//  Author: Matt Lewis
//  Organization: Eastern Washington University - Instructional Technology
//  Copyright: Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License
//  Version: 1.0
//  Page Description: This page adds the institutional preferences for the Treadz LTI.
//  Called From: launch.php
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

//////////////////////////////////////////////////////
//Set LTI domain
//The domainThreadz variable needs to match the URI provided in the original LTI form. Any redirects from the OAuth2 process must use this domain.
$_SESSION['domainThreadz'] = "Server URL of where your Threadz folder is located";


$_SESSION['domainLMS'] = "LMS URL";

//If you are going through a proxy, you can add that here, otherwise leave as null.
$_SESSION['proxy'] = null;

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
//set variable to the shared secret used when setting up the lti.
$shared_secret = "threadz";

//////////////////////////////////////////////////////
//Array of words to filter out for wordCloud
//Edit array to adjust the words to be removed from the word cloud analysis
$_SESSION['stopWords'] = array("i","me","my","myself","we","us","our","ours","ourselves","you","your","yours","yourself","yourselves","he","him","his","himself","she","her","hers","herself","it","its","itself","they","them","their","theirs","themselves","what","which","who","whom","whose","this","that","these","those","am","is","are","was","were","be","been","being","have","has","had","having","do","does","did","doing","will","would","should","can","could","ought","i'm","you're","he's","she's","it's","we're","they're","i've","you've","we've","they've","i'd","you'd","he'd","she'd","we'd","they'd","i'll","you'll","he'll","she'll","we'll","they'll","isn't","aren't","wasn't","weren't","hasn't","haven't","hadn't","doesn't","don't","didn't","won't","wouldn't","shan't","shouldn't","can't","cannot","couldn't","mustn't","let's","that's","who's","what's","here's","there's","when's","where's","why's","how's","a","an","the","and","but","if","or","because","as","until","while","of","at","by","for","with","about","against","between","into","through","during","before","after","above","below","to","from","up","upon","down","in","out","on","off","over","under","again","further","then","once","here","there","when","where","why","how","all","any","both","each","few","more","most","other","some","such","no","nor","not","only","own","same","so","than","too","very","say","says","said","shall");

//////////////////////////////////////////////////////
?>
