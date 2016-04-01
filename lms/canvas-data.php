<?php
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//  Name: Threadz
//  Author: Matt Lewis
//  Organization: Eastern Washington University - Instructional Technology
//  Copyright: Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License
//  Version: 1.0
//  Page Description: This page connects to the Canvas LMS and saves topic and post discussion data into SESSION.
//  Embedded Into: tokenAuth.php
//  Uses: function.php
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

    //Collect the list of discussion topics and data for the course, then collect the post data for each discussion.
    //This data will then be used on the chart/graph pages with the D3.js library.
    //By collecting all the data, there is no need to keep the access token saved into SESSION.

    if($current_token){
        $urlTopics = $_SESSION['domainLMS']."/api/v1/courses/".$_SESSION['courseID']."/discussion_topics?per_page=50&access_token=". $current_token;
        //$urlTopics = ".$domainCanvas."/test/data/topics.json";
        //NOTE: There is an issue with Sonicwall and the basic file_get_contents() function.  If you are using Sonicwall you will want to look at this thread at php bugs: https://bugs.php.net/bug.php?id=40197
        $dataTopics = file_get_contents($urlTopics);
        //$dataTopics = connectCanvasAPI($urlTopics,'','Get',$_SESSION['proxy']);
        $jsonTopics = json_decode($dataTopics, true);
        
        //Save json Data of all course discussion topics into SESSION
        $_SESSION['jsonTopics'] = $jsonTopics;
        
        //Count the number of topics in course
        $_SESSION['countOfTopic'] = 0;
        
        //Step through each json record, saving data into SESSION
        foreach($jsonTopics as $topic){
            $topic_id = $topic['id'];
            $topic_title = $topic['title'];
            $_SESSION['topicList'][$topic_id] = array('topic_id' => $topic_id, 'topic_title'=> $topic_title, 'topic_subentry_count' => $topic['discussion_subentry_count'], 'published'=> $topic['published']);
            
            $urlTopic = $_SESSION['domainLMS']."/api/v1/courses/".$_SESSION['courseID']."/discussion_topics/".$topic_id."/view.json?access_token=". $current_token;
            //$urlTopic = "".$domainCanvas."/test/data/posts.json";
            $dataPosts = file_get_contents($urlTopic);
            $jsonData = json_decode($dataPosts, true);
    
            //save individual discussion topic post data into SESSION using the topic id as the key name
            $_SESSION['json_'.$topic_id] = $jsonData;
            $arrTopic[$topic_id] = array(
                'json' => $jsonData,
                'topic_id'=> $topic['id'],
                'published'=> $topic['published'],
                'url'=> $topic['url'],
                'topic_title' => $topic_title,
                'message' => $topic['message'],
                'author_id' => $topic['author']['id'],
                'topic_url' => $topic['url'],
                'assignment_id' => $topic['assignment_id'],
                'discussion_type' => $topic['discussion_type'],
                'topic_subentry_count' => $topic['discussion_subentry_count'],
                'topic_word_count' => 0,
            );
            
            //save abridged topic data into SESSION
            $_SESSION['arrTopics'] = $arrTopic;
            //create HTML display Topic list for published discussions and with discussions with one or more posts.
            if($topic['published'] == true && $topic['discussion_subentry_count']>0){
               // $select_list_option.= "<option value='".$topic_id."'>".$topic_title."</option>";
               //set class so we will know which tabs to activate
               $select_list_option.= "<option class='".$topic['discussion_type']."' value='".$topic_id."'>".$topic_title."</option>";
                //count the number of topics that are published and have more than one post, this count determins what
                //initially gets displayed on threadz.php
                $_SESSION['countOfTopic']++;
            }
        }
    }
?>
