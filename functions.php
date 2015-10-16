<?php
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//  Name: Threadz
//  Author: Matt Lewis
//  Organization: Eastern Washington University - Instructional Technology
//  Copyright: Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License
//  Version: 1.0
//  Used by: launch.php, ajax.php
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//  Function Description: Creates the data array to be used by D3js. Steps through the LMS post data creating nodes and links.
//  Called From: ajax.php 
//  Uses: setPostData2Session(), replyPosts(), setDailyCount2Session()
function d3Data($topic_id){
    ////////////////////
    //create array name to use with D3.
    $array_title = "d3_".$topic_id;
    empty($_SESSION[$array_title]);
    unset($_SESSION[$array_title]);
    
    $jsonTopics = $_SESSION['jsonTopics'];
    $jsonData = $_SESSION['json_'.$topic_id];
    $_SESSION[$array_title]['totals']['total_threads'] = 0;
    $_SESSION[$array_title]['totals']['total_deleted'] = 0;
    
    //set general topic information into array
    $_SESSION[$array_title]['topic']['author_id'] = $_SESSION['arrTopics'][$topic_id]['author_id'];
    $_SESSION[$array_title]['topic']['topic_title'] = $_SESSION['arrTopics'][$topic_id]['topic_title'];
    $_SESSION[$array_title]['topic']['assignment_id'] = $_SESSION['arrTopics'][$topic_id]['assignment_id'];
    $_SESSION[$array_title]['topic']['topic_id'] = $_SESSION['arrTopics'][$topic_id]['topic_id'];
    $_SESSION[$array_title]['topic']['url'] = $_SESSION['arrTopics'][$topic_id]['url'];
    
    $sourceNum = 0;
    //create a blank array to hold the relationship counts for use in the nodes array
    foreach($jsonData['participants'] as $participant){
        $arrRelationships[$sourceNum] = 0;
        $sourceNum++;
    }
    //reset source number back to zero
   $sourceNum = 0;
   
    //create the nodes array
    foreach($jsonData['participants'] as $participant){
        preg_match_all('/\b\w/', $participant['display_name'], $initials);
        $_SESSION[$array_title]['nodes'][$participant['id']] = array(
            'canvas_id' => $participant['id'],
            'name' => $participant['display_name'],
            'initials' => implode("", $initials[0]),
            'avatar'=> $participant['avatar_image_url'],
            'post_count' => 0,
            'posts_received' => 0,
            'relationships' => $arrRelationships,
            "word_count" => 0,
            "word_count_avg" => 0,
            "group" => $topic_id,
            "source" => $sourceNum
        );
        $sourceNum++;
    }

    //step through the view (post) data.  collect and organize the data into the link and node arrays for discussion
    foreach($jsonData['view'] as $view){
        //removed deleted posts out of the mix for now. Current state is removing all subpost if main post gets deleted.
        if($view['deleted'] != 'true'){
            $reply_to = $_SESSION['topics'][$topic_id]['author']['id'];
            //the author id is not always set, default to 0 instead of null
            if($reply_to == null){
                $target = 0;
            }else{
                $target = $_SESSION[$array_title]['nodes'][$reply_to]['source'];
            }
            $posted_by = $view['user_id'];
            $edited_by = $view['editor_id'];
            //if a post is deleted, Canvas removes the user_id from the data.  Use the editor_id in its place as a surrogate for the user_id. Otherwise the connection to a person is lost and the post becomes orphaned
            //if($view['deleted'] == 'true'){
            //    $sourced_by = $edited_by;
            //}else{
            //    $sourced_by = $posted_by;
            //}
            $message_id = $view['id'];
            $_SESSION['postNum']++;
            $posted_word_count = 0;
            $posted_word_count += str_word_count(strip_tags($view['message']));
            $arrReply = array(
                'message_id' => $view['id'],
                'posted_by' => $posted_by,
                'edited_by' => $edited_by,
                'posted_message' => $view['message'],
                'posted_word_count' => $posted_word_count,
                'posted_on' => $view['created_at'],
                'reply_to' => $reply_to,
                'source' => $_SESSION[$array_title]['nodes'][$posted_by]['source'],
                'target' => $_SESSION[$array_title]['nodes'][$posted_by]['source'],
                //'source' => $_SESSION[$array_title]['nodes'][$sourced_by]['source'],
                //'target' => $_SESSION[$array_title]['nodes'][$sourced_by]['source'],
                'subthread' => 0,
                'deleted' => $view['deleted'],
                'thread' => $view['id']
            );
            $i=0;
    
        
            //Enter post data into saved Session arrays
            setPostData2Session($array_title, $reply_to, $posted_by, $arrReply, $topic_id, $message_id);
            //if post has replies, collect reply/posts with recursive replyPost() function
            //example: replyPosts($replies, $thread, $reply_to, $array_title, $topic_id, $post_count)
            if(array_key_exists('replies', $view)){
                for($a=0; $a<count($view['replies']); $a++){
                    replyPosts($view['replies'][$a], $view['id'], $posted_by, $array_title, $topic_id, $message_id);
                }
            }
            
            //totals array, count of participants
            $_SESSION[$array_title]['totals']['total_word_count'] =+ $_SESSION['arrTopics'][$topic_id]['topic_word_count'];
            $_SESSION[$array_title]['totals']['total_threads']++;
            if($view['deleted'] == 'true') $_SESSION[$array_title]['totals']['total_deleted']++;
            
            ////save the created at date of post
            //$_SESSION[$array_title]['timeline']['datetime'][$view['created_at']] = array($view['created_at'] => $view['created_at'], 'count' => 1) ;
        }
        
        if($view['deleted'] == 'true') $_SESSION[$array_title]['totals']['total_deleted']++;
    }
    //creates and saves into the timeline array ($_SESSION[$array_title]['timeline']['date]) the daily post counts
    setDailyCount2Session($_SESSION[$array_title]['timeline']['datetime'], $array_title);

    //totals array, count of participants
    $_SESSION[$array_title]['totals']['total_participants'] = count($jsonData['participants']);
    $_SESSION[$array_title]['totals']['total_posts'] = count($_SESSION[$array_title]['links']['message_order']);

}

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//  Function Description: Saves the data into SESSION arrays for use with the D3.js graphing.
//                        recursive function that steps through the Canvas json discussion API data.
//  Used By: d3Data()
function replyPosts($reply, $thread, $reply_to, $array_title, $topic_id, $message_id){
    $posted_by = $reply['user_id'];
    $edited_by = $reply['editor_id'];
    //if a reply is deleted, Canvas removes the user_id from the data.  Use the editor_id in its place as a surrogate for the user_id. Otherwise the connection to a person is lost and the post becomes orphaned
    if($reply['deleted'] == 'true'){
        $sourced_by = $edited_by;
    }else{
        $sourced_by = $posted_by;
    }
    $message_id = $reply['id'];
    $_SESSION['postNum']++;
    $posted_word_count = 0;
    $posted_word_count += str_word_count(strip_tags($reply['message']));
    $arrReply = array(
        'message_id' => $reply['id'],
        'posted_by' => $posted_by,
        'edited_by' => $edited_by,
        'posted_message' => $reply['message'],
        'posted_word_count' => $posted_word_count,
        'posted_on' => $reply['created_at'],
        'reply_to' => $reply_to,
        'source' => $_SESSION[$array_title]['nodes'][$sourced_by]['source'],
        'target' => $_SESSION[$array_title]['nodes'][$reply_to]['source'],
        'subthread' => $reply['parent_id'],
        'deleted' => $reply['deleted'],
        'thread' => $thread
    );

    
    setPostData2Session($array_title, $reply_to, $posted_by, $arrReply, $topic_id, $message_id);
    //if there are 'replies' items in replies, step through the data again
    if(array_key_exists('replies', $reply)){
        for($b=0; $b<count($reply['replies']); $b++){
            $rep = $reply['replies'][$b];
            replyPosts($rep, $thread, $posted_by, $array_title, $topic_id, $message_id);
        }
    }    
}


//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//  Function Description: Save the data coming from the discussion posts into arrays saved into Session.
//  Used By: d3Data() 
function setPostData2Session($arr_title, $reply_to, $posted_by, $arrReply, $topic_id, $message_id){
    //advance the count of posts in node array
    $_SESSION[$arr_title]['nodes'][$posted_by]['post_count']++;
    
    //set relationship count in node array
    $source_replyTo = $_SESSION[$arr_title]['nodes'][$reply_to]['source'];
    $source_postedBy = $_SESSION[$arr_title]['nodes'][$posted_by]['source'];
    
    //advance the count of posts received in node array
    $_SESSION[$arr_title]['nodes'][$reply_to]['posts_received']++;
    
    if(isset($_SESSION[$arr_title]['nodes'][$posted_by]['relationships'][$source_replyTo])){
        $_SESSION[$arr_title]['nodes'][$posted_by]['relationships'][$source_replyTo]++;
    }else{
        $_SESSION[$arr_title]['nodes'][$posted_by]['relationships'][$source_postedBy]++;
    }
    
    //set relationship count in node array
    $_SESSION[$arr_title]['nodes'][$posted_by]['word_count'] = $_SESSION[$arr_title]['nodes'][$posted_by]['word_count'] + $arrReply['posted_word_count'];
    
    //set the count of deleted posts
    if($arrReply['deleted'] == 'true'){
        $_SESSION[$arr_title]['nodes'][$posted_by]['deleted']++;
    }
    
    //save the post data into the links array
    $_SESSION[$arr_title]['links']['thread_order'][$_SESSION['postNum']]= $arrReply;
    $_SESSION[$arr_title]['links']['message_order'][$message_id]= $arrReply;
    
    //thread word counts saved into topic
    $_SESSION['arrTopics'][$topic_id]['topic_word_count'] = $arrReply['posted_word_count'];
    
    //set relationship count in node array
    $_SESSION[$arr_title]['nodes'][$posted_by]['word_count_avg'] = $_SESSION[$arr_title]['nodes'][$posted_by]['word_count']/$arrReply['posted_word_count'];
    
    //convert time to Pacific timezine add post to timeline array
    //$post_datetime = new DateTime($arrReply['posted_on']);
    //$post_time = $post_datetime->setTimezone(new DateTimeZone('America/Los_Angeles'));
    
    $post_time = $arrReply['posted_on'];
    if(isset($_SESSION[$arr_title]['timeline']['datetime'][$post_time])){
        $_SESSION[$arr_title]['timeline']['datetime'][$post_time]['count']++;
    }else{
        $_SESSION[$arr_title]['timeline']['datetime'][$post_time] = array('post_datetime' => $post_time, 'count' => 1);
    }
    
}

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//Build the export of the data

function buildExport(){
  $BuildExportHTML = "";
  $BuildExportHTML .= '<form name="exportsnapp" method="post" action="/FileSaver/FileSave.php">';
  $BuildExportHTML .= '<input type="hidden" id="fileext" name="fileext" value="">';
  $BuildExportHTML .= '<input type="hidden" name="imagedata">';
  $BuildExportHTML .= '</form>' ;
  return $BuildExportHTML;
}
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//Build an array with the day of each post.
//Used in conjunction with setDailycount2Session()

function buildTimelineShell($start, $end){

            //create array with all dates between the first and last posts
        //the DateInterval and DatePeriod php functions are 5.3 and greater
        //use the GetDays() until webserver is created
        $daterange = GetDays($start, $end);
    
        ////The below will not work until php is updated to 5.3
        ////Find the days between the start and end dates
        //$start = new DateTime( $start );
        //$end = new DateTime( $end );
        //$end = $end->modify( '+1 day' ); 
        //$interval = new DateInterval('P1D');
        //$daterange = new DatePeriod($begin, $interval ,$end);
    
    
    foreach($daterange as $date){
        //$date = setType($date, 'string');
        $day = substr($date,0,10);
        //$day = date_format($date,"Ymd");
        $arrPostDates[$day] = array('post_date'=> $day, 'count'=> 0);
    }
    return $arrPostDates;
}


//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//Step through an array of post days and tally a post count per day.
//Save array to session

function setDailyCount2Session($arrDateTime, $array_title){
    //Sort timeline array by datetime
    ksort($arrDateTime);
    
    //Set to current to grab the datetime of the first post entry
    current($arrDateTime);
    $start = key($arrDateTime);
    
    // move the internal pointer to the end of the array
    end($arrDateTime);
    $end = key($arrDateTime);
    
    //create array with all dates between the first and last posts
    $arrTimeline = buildTimelineShell($start, $end);
    
    //add counts of the number of posts made on a single day to the timeline array
    foreach($arrDateTime as $arrPostDateTime){
        $postDateTime = $arrPostDateTime['post_datetime'];
        //remove the time leaving just the date
        $post_date = substr($postDateTime,0,10);
        //count the number of posts on a specific day
        $arrTimeline[$post_date]['count']++;
    }
    //save data to Session timeline array
    $_SESSION[$array_title]['timeline']['date'] = $arrTimeline;
}



//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//http://edrackham.com/php/get-days-between-two-dates-using-php/
function GetDays($sStartDate, $sEndDate){  
  // Firstly, format the provided dates.  
  // This function works best with YYYY-MM-DD  
  // but other date formats will work thanks  
  // to strtotime().
  
  
  $sStartDate = gmdate("Y-m-d", strtotime($sStartDate));  
  $sEndDate = gmdate("Y-m-d", strtotime($sEndDate));  
  // Start the variable off with the start date  
  $aDays[] = $sStartDate;  
  
  // Set a 'temp' variable, sCurrentDate, with  
  // the start date - before beginning the loop  
  $sCurrentDate = $sStartDate;  
  
  // While the current date is less than the end date  
  while($sCurrentDate < $sEndDate){  
    // Add a day to the current date  
    $sCurrentDate = gmdate("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));  
  
    // Add this new day to the aDays array  
    $aDays[] = $sCurrentDate;  
  }  
  
  // Once the loop has finished, return the  
  // array of days.  
  return $aDays;  
}

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function setMessagesArray($data, $i){
    $dataCount = sizeof($data);
    for($a=1; $a<=$dataCount; $a++){
        if($data[$i]['parent_id'] == null){
            $threadID = $data[$i]['id'];
        }else{
            $threadID = $data[$i]['parent_id'];
        }
        $post_id = $data[$i]['id'];
        $_SESSION['messageArray'][$threadID][$post_id] = array('message'=> $data[$i]['message'], 'user_id'=> $data[$i]['id'], "post_id" => $post_id);
    
        //if(array_key_exists('replies', $data[$i])){
        if(isset($data[$i]['replies'])){
            $j=0;
            foreach($data[$i]['replies'] as $datasub){
                setMessagesArray($datasub[$j]['replies'], 0);
                $j++;
            }
        }
        $i++;
    }
}


//http://webtricksandtreats.com/export-to-csv-php/
function convert_to_csv($input_array, $output_file_name, $delimiter)
{
    /** open raw memory as file, no need for temp files */
    $temp_memory = fopen('php://memory', 'w');
    /** loop through array  */
    foreach ($input_array as $line) {
        /** default php csv handler **/
        fputcsv($temp_memory, $line, $delimiter);
    }
    /** rewrind the "file" with the csv lines **/
    fseek($temp_memory, 0);
    /** modify header to be downloadable csv file **/
    header('Content-Type: application/csv');
    header('Content-Disposition: attachement; filename="' . $output_file_name . '";');
    /** Send file to browser for download */
    fpassthru($temp_memory);
}
 
// https://www.exchangecore.com/blog/php-output-array-csv-headers/
//Takes in a filename and an array associative data array and outputs a csv file

function outputCsv($fileName, $assocDataArray)
{
    ob_clean();
    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Cache-Control: private', false);
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=' . $fileName);    
    if(isset($assocDataArray['0'])){
        $fp = fopen('php://output', 'w');
        fputcsv($fp, array_keys($assocDataArray['0']));
        foreach($assocDataArray AS $values){
            fputcsv($fp, $values);
        }
        fclose($fp);
    }
    ob_flush();
}

?>