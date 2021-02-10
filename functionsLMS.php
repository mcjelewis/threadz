<?php
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//  Name: Threadz
//  Author: Matt Lewis
//  Organization: Eastern Washington University - Instructional Technology
//  Copyright: Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License
//  Version: 1.0
//  Used by: canvas-data.php
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//NOTES:
//Because the format of the data being returned by Canvas may not be standard to another LMS, specific functions have been
//created for the Canvas data connection and extraction.  If you are planning on adding Threadz to a different LMS,
//you will need to create similar data functions and add them to this page.
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function getCanvasAPIcurl($authorization, $url){
        //// Get cURL resource
            $curl = curl_init();
            // Set some options - we are passing in a useragent too here
            curl_setopt($curl, CURLOPT_URL,$url);
            //curl_setopt($curl, CURLOPT_PROXY, $_SESSION['proxy']);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json', $authorization ));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_VERBOSE, 1);
            curl_setopt($curl, CURLOPT_HEADER, 1);
            //curl_setopt($curl, CURLOPT_HTTPGET, true);
            // Send the request & save response to $resp
            $data = curl_exec($curl);
            $info = curl_getinfo($curl);

            if(curl_errno($curl) !=0){
                echo "error numbers:". curl_errno($curl) . "<br>";
                echo "error message:". curl_error($curl) . "<br>";
                echo '<br>Took ' . $info['total_time'] . ' seconds to send a request to ' . $info['url'];
                curl_close($curl);
                exit();
            }
            
            // Close request to clear up some resources
            curl_close($curl);

        $arrCurl['header'] = substr( $data, 0, $info['header_size']);
        $arrCurl['headerLinks'] = getCanvasHeaderLinks($arrCurl['header']);
        $arrCurl['body'] = substr($data, $info['header_size']);
        $arrCurl['response'] = $data;
        $arrCurl['info'] = $info;

        return $arrCurl;
}
function getCanvasHeaderLinks($header){
        //The header Canvas returns for the curent, next, first, and last links is formated like:
            //  <https://[domainLMS]/api/v1/courses/[courseID]/enrollments?page=1&per_page=3>; rel="current",  
            //  <https://[domainLMS]/api/v1/courses/[courseID]/enrollments?page=2&per_page=3>; rel="next",  
            //  <https://[domainLMS]/api/v1/courses/[courseID]/enrollments?page=1&per_page=3>; rel="first",  
            //  <https://[domainLMS]/api/v1/courses/[courseID]/enrollments?page=2&per_page=3>; rel="last"
        //We need to extract the links to determine if there are more pages of data in the API.
        //Note: until we started removing the < and > characters, the content between was hidden (see below).  I don't understand why, but removing them solved that issue.
        //Split full header into an array with the :
        $headerArray= explode(': ',$header);
        //find the location in array of the 'next' url.
        foreach($headerArray as $key=> $val){
            if(strpos($val,'rel=')){
                $urlKey = $key;
                break;
            }
        }
        $string = $headerArray[$urlKey];
        //split this into it's own array on rel=
        $links = explode('rel=', $string);
        $headerReplace = array('<', '>',';');
        foreach($links as $key =>$val){
            $pos1a = 0;
            $pos2a = 0;
            if($key != 0){
                //Because the link precedes name, splitting the array at 'rel=' places the name into the following key.
                //To place the name in as the key, we find the 
                $pos1a = strpos($val,'"');
                $pos2a = strpos($val,'"', 1);
                $title = substr($val, $pos1a+1, $pos2a-1);
                if($key == 1){
                    $headerLinks[$title] = str_replace($headerReplace, "", substr($links[$key-1],$pos1b));
                }else{
                    $headerLinks[$title] = str_replace($headerReplace, "", substr($links[$key-1],$pos2b+2));
                    
                }
            }
            $pos1b= $pos1a;
            $pos2b= $pos2a;
        }
        return $headerLinks;
}
function getCanvasRoster($arrCurlRoster, $authorization){
        $jsonRoster = json_decode($arrCurlRoster['body'], true);
        $_SESSION['course']['roster']['students'] = array();
        //Depending on the role of the users ($_SESSION['roles']) there are some other data points that are available from the enrollment api call.
        //Currently there is no use case set for applying this data to the analytics, so it is not being captured.  But in the future there might be a need.
        //total_activity_time
        //last_activity_at
        //grades:current_score
        //grades:final_score

        foreach($jsonRoster as $roster){
            $_SESSION['course']['roster'][$roster['user_id']] = array(
                'json' => $jsonData,
                'user_id'=> $roster['user_id'],
                'section_integration_id'=> $roster['section_integration_id'],
                'name'=> $roster['user']['name'],
                'enrollment_state'=> $roster['enrollment_state'],
                'role'=> $roster['type'],
            );
            $_SESSION['course']['roster']['students'][] = $roster['user_id'];
        }
        //if there are more records to be returned, Canvas will have the link in the url in the header
        if(array_key_exists('next', $arrCurlRoster['headerLinks'])){
            $arrCurlRoster=getCanvasAPIcurl($authorization, $arrCurlRoster['headerLinks']['next']);
            getCanvasRoster($arrCurlRoster);
        }
}

function getCanvasGroup($arrCurlGroup, $authorization){
    $jsonGroups = json_decode($arrCurlGroup['body'], true);
    $_SESSION['groups']= array();
    //Depending on the role of the users ($_SESSION['roles']) there are some other data points that are available from the enrollment api call.
    //Currently there is no use case set for applying this data to the analytics, so it is not being captured.  But in the future there might be a need.
    //total_activity_time
    //last_activity_at
    //grades:current_score
    //grades:final_score

    foreach($jsonGroups as $group){
        $_SESSION['groups'][$group['id']] = array(
            'json'=> $group,
            'id'=> $group['id'],
            'name'=> $group['name'],
            'has_submission'=> $group['has_submission'],
            'leader'=> $group['leader'],
            'roster'=> [],
        );
        //now get group users
        $urlGroup = $_SESSION['domainLMS']."/api/v1/groups/".$group['id']."/users";
        $arrCurlGroupUsers=getCanvasAPIcurl($authorization, $urlGroup);
        
        getCanvasGroupUsersData($arrCurlGroupUsers, $authorization, $group['id'] );
        //$_SESSION['course']['roster']['students'][] = $roster['user_id'];
    }
    //if there are more records to be returned, Canvas will have the link in the url in the header
    if(array_key_exists('next', $arrCurlRoster['headerLinks'])){
        $arrCurlRoster=getCanvasAPIcurl($authorization, $arrCurlRoster['headerLinks']['next']);
        getCanvasGroup($arrCurlRoster,$authorization);
    }
}
function getCanvasGroupUsersData($arrCurlGroupUsers, $authorization, $groupId) {
    $jsonUsers = json_decode($arrCurlGroupUsers['body'], true);
    //print_r( $jsonUsers );
    foreach($jsonUsers as $user){
        $_SESSION['groups'][$groupId]['roster'][] = $user['id'];
    }
}

function getCanvasTopicList($arrCurlTopics, $authorization){
        //Save json Data of all course discussion topics into SESSION
        $jsonTopics = json_decode($arrCurlTopics['body'], true);
        $_SESSION['jsonTopics'] = $jsonTopics;
        if ( ! isset( $_SESSION['check_list_option'] ) ) {
            $_SESSION['check_list_option'] = "";
        }
        
        //Step through each json record, saving data into SESSION
        foreach($jsonTopics as $topic){

            $topic_id = $topic['id'];
            $topic_title = $topic['title'];
            //echo $topic_id . "<br>";
            if($topic['assignment_id']){
                $due_at = $topic['assignment']['due_at'];
            }else{
                $due_at = "";
            }
            $_SESSION['topicList'][$topic_id] = array(
                'topic_id' => $topic_id,
                'topic_title'=> $topic_title,
                'topic_subentry_count' => $topic['discussion_subentry_count'],
                'published'=> $topic['published'],
                'require_initial_post'=> $topic['require_initial_post'],
                'users_can_see_posts'=> $topic['users_can_see_posts'],
                'assignment_id'=> $topic['assignment_id'],
                'due_at'=> $due_at,
                'message' => $topic['message'],
                'author_id' => $topic['author']['id'],
                'topic_url' => $topic['url'],
                'discussion_type' => $topic['discussion_type'],
                'topic_subentry_count' => $topic['discussion_subentry_count'],
                'topic_word_count' => 0,
                'allow_rating' => $topic['allow_rating'],
                'unread_count' => $topic['unread_count'],
                'group_category_id' => $topic['group_category_id'],
                'topic_children' => $topic['topic_children'],
            );

            //$urlTopic = $_SESSION['domainLMS']."/api/v1/courses/".$_SESSION['courseID']."/discussion_topics/".$topic_id."/view.json";
            $urlTopic = $_SESSION['domainLMS']."/api/v1/courses/".$_SESSION['courseID']."/discussion_topics/".$topic_id."/view";
            $arrCurlTopic=getCanvasAPIcurl($authorization, $urlTopic);

            getCanvasTopicData($arrCurlTopic, $authorization, $topic_id);
            
            //create HTML display Topic list for published discussions and with discussions with one or more posts.
            if($topic['published'] == true && $topic['discussion_subentry_count']>0){
                $_SESSION['select_list_option'] .="<option value='".$topic_id."'>".$topic_title."</option>";
                $_SESSION['check_list_option'] .="<label><input type='checkbox' name='topicList[]' checked value='".$topic_id."'>".$topic_title.'</label><br>';
                //count the number of topics that are published and have more than one post, this count determins what
                //initially gets displayed on threadz.php
                $_SESSION['countOfTopic']++;
            }
        }
//        var_dump($_SESSION['course']['roster']);
//        echo "<br>========<br>";
//        var_dump($_SESSION['topicList'][3034092]);
//exit();
        //if there are more records to be returned, Canvas will have the link in the url in the header
        if ( $arrCurlTopics['headerLinks'] ) {
            if(array_key_exists('next', $arrCurlTopics['headerLinks'])){
                $arrCurlTopics=getCanvasAPIcurl($authorization, $arrCurlTopics['headerLinks']['next']);
                getCanvasTopicList($arrCurlTopics);
            }
        }
}
function getCanvasTopicData($arrCurlTopic, $authorization, $topic_id){
        //save individual discussion topic post data into SESSION using the topic id as the key name
        $jsonData = json_decode($arrCurlTopic['body'], true);

        if($jsonData.status != "unauthenticated" && $jsonData.id && $jsonData.message ){
            $_SESSION['json_'.$topic_id] = $jsonData;
            $_SESSION['arrTopics'][$topic_id]['json'] = $jsonData;
            $_SESSION['arrTopics'][$topic_id]['settings'] = $_SESSION['topicList'][$topic_id];
            //    'json' => $jsonData,
            //save abridged topic data into SESSION
            //$_SESSION['arrTopics'][$topic_id] = array(
            //    'json' => $jsonData,
            //    'topic_id'=> $topic['id'],
            //    'published'=> $topic['published'],
            //    'url'=> $topic['url'],
            //    'topic_title' => $topic_title,
            //    'message' => $topic['message'],
            //    'author_id' => $topic['author']['id'],
            //    'due_date' => $topic['due_date'],
            //    'topic_url' => $topic['url'],
            //    'assignment_id' => $topic['assignment_id'],
            //    'discussion_type' => $topic['discussion_type'],
            //    'topic_subentry_count' => $topic['discussion_subentry_count'],
            //    'topic_word_count' => 0,
            //    'allow_rating' => $topic['allow_rating'],
            //    'require_initial_post' => $topic['allow_rating'],
            //    'unread_count' => $topic['unread_count'],
            //);
            
            //if there are more records to be returned, Canvas will have the link in the url in the header
            if(array_key_exists('next', $arrCurlTopic['headerLinks'])){
                $arrCurlTopic=getCanvasAPIcurl($authorization, $arrCurlTopic['headerLinks']['next']);
                getCanvasTopicData($arrCurlTopic, $authorization, $topic_id);
            }
        }
}

function getCanvasGroupTopicList($arrCurlTopics, $authorization, $groupId){
    //Save json Data of all course discussion topics into SESSION
    $jsonTopics = json_decode($arrCurlTopics['body'], true);
    $_SESSION['jsonTopics'] = $jsonTopics;
    if ( ! isset( $_SESSION['check_list_option'] ) ) {
        $_SESSION['check_list_option'] = "";
    }
    
    //Step through each json record, saving data into SESSION
    foreach($jsonTopics as $topic){

        $topic_id = $topic['id'];
        $topic_title = $topic['title'];
        //echo $topic_id . "<br>";
        if($topic['assignment_id']){
            $due_at = $topic['assignment']['due_at'];
        }else{
            $due_at = "";
        }
        $_SESSION['topicList'][$topic_id] = array(
            'topic_id' => $topic_id,
            'topic_title'=> $topic_title,
            'topic_subentry_count' => $topic['discussion_subentry_count'],
            'published'=> $topic['published'],
            'require_initial_post'=> $topic['require_initial_post'],
            'users_can_see_posts'=> $topic['users_can_see_posts'],
            'assignment_id'=> $topic['assignment_id'],
            'due_at'=> $due_at,
            'message' => $topic['message'],
            'author_id' => $topic['author']['id'],
            'topic_url' => $topic['url'],
            'discussion_type' => $topic['discussion_type'],
            'topic_subentry_count' => $topic['discussion_subentry_count'],
            'topic_word_count' => 0,
            'allow_rating' => $topic['allow_rating'],
            'unread_count' => $topic['unread_count'],
            'group_category_id' => $topic['group_category_id'],
            'topic_children' => $topic['topic_children'],
            'groupId' => $groupId,
        );

        //$urlTopic = $_SESSION['domainLMS']."/api/v1/courses/".$_SESSION['courseID']."/discussion_topics/".$topic_id."/view.json";
        $urlTopic = $_SESSION['domainLMS']."/api/v1/groups/".$groupId."/discussion_topics/".$topic_id."/view";
        $arrCurlTopic=getCanvasAPIcurl($authorization, $urlTopic);

        getCanvasTopicData($arrCurlTopic, $authorization, $topic_id);
        
        //create HTML display Topic list for published discussions and with discussions with one or more posts.
        if($topic['published'] == true && $topic['discussion_subentry_count']>0){
            $_SESSION['select_list_option'] .="<option value='".$topic_id."'>".$topic_title."</option>";
            $_SESSION['check_list_option'] .="<label><input type='checkbox' name='topicList[]' checked value='".$topic_id."'>".$topic_title.'</label><br>';
            //count the number of topics that are published and have more than one post, this count determins what
            //initially gets displayed on threadz.php
            $_SESSION['countOfTopic']++;
        }
    }
//        var_dump($_SESSION['course']['roster']);
//        echo "<br>========<br>";
//        var_dump($_SESSION['topicList'][3034092]);
//exit();
    //if there are more records to be returned, Canvas will have the link in the url in the header
    if ( $arrCurlTopics['headerLinks'] ) {
        if(array_key_exists('next', $arrCurlTopics['headerLinks'])){
            $arrCurlTopics=getCanvasAPIcurl($authorization, $arrCurlTopics['headerLinks']['next']);
            getCanvasGroupTopicList($arrCurlTopics, $authorization, $groupId);
        }
    }
}
?>