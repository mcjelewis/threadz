<?php
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//  Name: Threadz
//  Author: Matt Lewis
//  Organization: Eastern Washington University - Instructional Technology
//  Copyright: Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License
//  Version: 1.0
//  Page Description: This page displays the HTML for the LTI. 
//  Uses: ajax.php, d3-visuals.js
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
ini_set('session.gc_maxlifetime', 1800);
session_start();
$topic_id = $_POST['topic_id'];
$_SESSION['graph'] = $_POST['graph'];
$domainThreadz = $_SESSION['domainThreadz'];
$js_topic_id =$_SESSION['js_topic_id'];
$discussionData = json_encode($_SESSION['d3_'.$js_topic_id]);

//Hide content depending on if there are any discussion topics in course.
if($_SESSION['countOfTopic'] > 0 ){
    $topicsAvaliable = "$('#noTopics').hide();";
}else{
    $topicsAvaliable = "$('#topics').hide();";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Threadz - Topic List</title>
    <meta charset="utf-8">
    <meta name="description" content="Visualize Canvas LMS discussions with various social network graphs.">
    <meta name="author" content="Matt Lewis">
    <meta name="robots" content="noindex,nofollow">

    <!--Stylesheets-->
    <link rel="stylesheet" href="<?php echo $domainThreadz ?>/css/base.css?<?php echo date('YmdHis') ?>"/>
    <link href='https://fonts.googleapis.com/css?family=Volkhov:400,400italic' rel='stylesheet' type='text/css'>
        
    <!--D3 Stylesheets-->
    <link rel="stylesheet" href="<?php echo $domainThreadz ?>/css/d3-viz.css?<?php echo date('YmdHis') ?>"/>
    
    <!--Jquery Stylesheets-->
    <link rel="stylesheet" href="<?php echo $domainThreadz ?>/css/tablesorter/blue/style.css"/>
    <link rel="stylesheet" href="<?php echo $domainThreadz ?>/css/overcastBluesky/jquery-ui.css"/>
    <!--<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">-->
    
    <!--Jquery Libraries-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
    <script type="text/javascript" src="<?php echo $domainThreadz ?>/lib/js/jquery/jquery.tablesorter.min.js"></script>
    <script type="text/javascript" src="<?php echo $domainThreadz ?>/lib/js/jquery/jquery.tableToCSV.js"></script>
    <script type="text/javascript" src="<?php echo $domainThreadz ?>/lib/js/jquery/jquery.inlineStyler.min.js"></script>
    <!--[if !IE]> -->
        <script type="text/javascript" async src="<?php echo $domainThreadz ?>/lib/js/jquery/excanvas.min.js"></script>
    <!-- <![endif]-->

    <!--D3 Libraries -->
    <script type="text/javascript" src="<?php echo $domainThreadz ?>/lib/js/d3/d3.v3.min.js"></script>
    <script type="text/javascript" src="<?php echo $domainThreadz ?>/lib/js/d3/d3-mapper.js"></script>
    <script type="text/javascript" src="<?php echo $domainThreadz ?>/lib/js/d3/underscore.js"></script>
    <script type="text/javascript" src="<?php echo $domainThreadz ?>/lib/js/d3/d3.layout.cloud.js"></script>
    <script type="text/javascript" src="<?php echo $domainThreadz ?>/lib/js/d3/d3-visuals.js?<?php echo date('YmdHis') ?>"></script>

    <!--JSNetworkX Library -->
    <script type="text/javascript" src="<?php echo $domainThreadz ?>/lib/js/jsnetworkx.js"></script>
</head>
<body>
    <h2 id='beta'>Threadz</h2>
    <div id='topics'>
        <div id="welcome">
            <p>Getting Started: Select a discussion from the list of published discussion titles.</p>
        </div>
        <div id="selectTopic">
            <form name='formTopics' id='formTopics' action="" method="Post">
                Discussion Topic Name: <select name='topic_id' id='topic_list'>
                    <option value='0'>Select a Discussion</option>
                    <?php echo $_SESSION['select_list_option']; ?>
                </select>
            </form>
        </div>
        <div id='topNav'><br>
            <a href="index.php"><span>Index</span></a>
        </div>
       <div id="vis_container">
            <ul>
                <li><a href="#network"><span>Network</span></a></li>
                <!--<li><a href="#chord"><span>Chord</span></a></li>-->
                <li><a href="chord.php"><span>Chord</span></a></li>
                <li><a href="#timeline"><span>Timeline</span></a></li>
                <li><a href="#statistics"><span>Statistics</span></a></li>
                <li><a href="#dataset"><span>Data Set</span></a></li>
                <li><a href="help.php"><span>Help</span></a></li>
                <!--<li><a href="#cloud"><span>Word Cloud</span></a></li>-->
                <!--<li><a href="#matrix"><span>Matrix</span></a></li>-->
                <!--<li><a href="#stream"><span>Stream</span></a></li>-->
                <!-- <li><a href="about.php"><span>About</span></a></li>-->
                <!-- <li><a href="help.php"><span>Help</span></a></li>-->
                <!-- <li><a href="roadmap.php"><span>Road Map</span></a></li>-->
                
            </ul>
            <div id="network">
                <div id="nodeType">
                    <input type="radio" name="nodeTypeDisplay" value='threads' id="radio1" checked="checked"><label for="radio1">Display by User</label>
                    <input type="radio" name="nodeTypeDisplay" value='people' id="radio2"><label for="radio2">Display by Posts/Replies</label>
                </div>
            <!--<div id='nodeType'>
                    <button id='nodeType-posts'>Display by Posts/Replies</button>
                    <button id='nodeType-people'>Display by User</button>
                </div>-->
                <div id='networkSize'>Size by:
                    <select id="nodeSize">
                        <option value="sent">Posts/Replies Sent</option>
                        <option value="received">Replies Received</option>
                        <option value="total">Total Posts/Replies</option>
                        <option value="word">Word Count</option>
                        <!--<option value="word_avg">Word Count Avg</option>-->
                        <!--<option value="betweenness">Betweenness Score</option>-->
                    </select>
                </div>
                <div id='networkColor'>Highlight:
                    <select id="nodeColor">
                        <option value="none"></option>
                        <option value="length">Post/Reply Length</option>
                        <option value="late">Late Posts/Replies</option>
                        <option value="unread">Unread Posts/Replies</option>
                        <option value="isolated">Isolated Learners</option>
                        <!--<option value="pcount">Count</option>
                        <option value="likes">Likes</option>
                        <option value="users">Users</option>-->
                    </select>
                </div>
                <div class='d3-visual'>
                    <div id='networkDirected'></div>
                    <div id='missing'></div>
                </div>
                <div id='right-container'>
                    <h4>Network Diagram</h4>
                    <p>The network visualization shows a typical line/node graph that connects users together.  </p>
                    <p>This visualization is useful to quickly discern any individual or group that is isolated or is a driving hub within the forum.</p>
                    <p>The nodes in this chart are movable to help single out individuals or groups when the network of connections gets too complex visually.</p>
                    <p>To manipulate a node, click and drag a node circle to another part of the page.  To release the node, double click.</p>
                    <h4>Key</h4>
                    <div id='userKey'>
                        <p>Each node represents a different user in the discussion and each line represents a connection from one user to another.  The relative size of the circles (nodes) changes to represent the quantity of number of posts/replies sent, replies received, or total of sent/received. </p>
                    </div>
                    <div id='threadKey'>
                        <p>Each node represents a different post in the discussion. Darker colors signify the original post of the thread, lighter shades show replies. Orange circles (nodes) represent highlighted students or posts depending on selection.</p>
                        <div><img src='images/blue-dot1.png' align="middle" width=20px heigth=20px alt="dark blue dot"><i>original thread post</i></div>
                        <div><img src='images/blue-dot2.png' align="middle" width=20px heigth=20px alt="light blue dot"><i>response to a post</i></div>
                        <div><img src='images/orange-dot1.png' align="middle" width=20px heigth=20px alt="dark orange dot"><i>late, unread, or isolated learner</i></div>
                            
                    </div>
                    <div id='missing>'></div>
                </div>
                <div class='discussionLink'></div>
            </div>
            <div id="chord"></div>  
            <div id="timeline">
                <div class='d3-visual'>
                    <div id="timeBar"></div>
                    <div id="timeArea"></div>
                </div>
                <div id='right-container'>
                    <h4>Timeline</h4>
                    <p>The timeline visualization displays the count of discussion posts by date. This visual can help you determine the rate of submissions and determine if there are any patterns to those submissions.</p>
                </div>
                <div class='discussionLink'></div>
            </div>
            <!--<div id="matrix" class='matrix'>
                <div id='matrixOrder'>Order By:
                    <select id="order">
                        <option value="name">Name</option>
                        <option value="count">Frequency</option>
                        <option value="post_count">Posts Sent</option>
                        <option value="posts_received">Posts Received</option>
                    </select>
                </div>
                <div class='d3-visual'><div id='matrixHeatmap'></div></div>
                <div id='right-container'>
                    <h4>Matrix Heatmap</h4>
                    <p>The matrix visualization the number of communications a person was a part of.</p>
                    <p>The color of the cell between two students shows the frequency of connections. The darker the cell the higher the frequency.</p>
                    <p>The order of the matrix can be set to the total frequency of connections, number of posts sent, number of posts received, or name.</p>
                </div>
                <div class='discussionLink'></div>
            </div> -->
            <div id="statistics">
                <div id="genStats">
                    <div id="tPart"></div>
                    <div id="tPost"></div>
                    <div id="tDeleted"></div>
                    <div id="tThread"></div>
                </div>  
                <br><div id="userStats" class="statTable"></div>
                <br><div id="threadStats" class="statTable"></div>
                <!--<div id="dataWord"><br/>Thread Word Counts - ratio of original thread word count to total posts in thread</div>-->
            </div>  
            <div id="dataset">
                <br><div id="snaRaw" clas="statTable"></div>
            </div>
            <div id="cloud">
                <div class='d3-visual'><div id='wordCloud'></div></div>
            </div>
            <!--<div id="stream">
                <div class='d3-visual'><div id='networkStream'></div></div>
                <div id='right-container'>
                    <h4>Network Stream</h4>
                    <p>This network visualization shows ...</p>
                </div>
                <div class='discussionLink'></div>
            </div>-->
            <!--<div id="about"></div>-->
            <!--<div id="roadmap"></div>-->
            <div id="help"></div>
        </div>
    </div>
    <div id='noTopics'>
        <h3>No Discussion Topics Available</h3>
        <p>Before you can use Threadz, there must be at minimum of one published disucssion in your course with one or more submitted posts.  Please check to make sure that your course meets these requirements.</p>
    </div>
    <script>
        //jquery submit Discussion Topic choice on change
        $(function() {
            //hide the network and matrix forms until a discussion has been selected.
            $('#networkSize').hide();
            $('#matrixOrder').hide();
            
            <?php echo $topicsAvaliable ?>
            
            //submit the topic list dropdown menu on change
            $('#topic_list').change(function() {
                $('#formTopics').submit();
            });

            $('#formTopics').submit(function() {
                    var dtype = $("#topic_list option:selected").attr("class");
                    var disabled = [];
                    var activeTab = 0;
                    //Types of discussions are 'side_comment' or 'threaded'
                    //If this is a threaded discussion, you can disable tabs if desired.
                    //if(dtype!="threaded"){
                    //        var disabled = [0,1,3,4];
                    //        var activeTab = 5;
                    //}
                    $.ajax({
                           type: "POST",
                           url: "ajax.php",
                           data: $("#formTopics").serialize(), // serializes the form's elements.
                           dataType: 'json',
                           success: function(data){
                                if(data == "Expired Session, please reauthenticate Threadz."){
                                    //console.log(data)
                                    $('topics').html(data);
                                }else{
                                    d3data = data;
                                    $('#networkDirected').html(d3data);
                                    $('#dataTest').html(d3data);
                                    $('#networkDirected').append(makeForceDirected(d3data));
                                    $('#welcome').hide();
                                    $('#networkSize').show();
                                    $('#networkColor').hide();
                                    $('#userKey').show();
                                    $('#threadKey').hide();
                                    $('#nodeType-posts').show();
                                    $('#nodeType-people').hide();
                                    $('#matrixOrder').show();
                                    $('#vis_container').tabs('option', 'disabled',disabled);
                                    $('#vis_container').tabs({active: activeTab});
                                    $('.discussionLink').html("<a class='mini' target='_blank' href='" + d3data.topic.topic_url + "'>go to Discussion</a>");
                                    $('input:radio[name="nodeTypeDisplay"][value="threads"]').prop('checked',true);
                                    $('#nodeSize').val('sent');
                                    $('#nodeColor').val('none');
                                }
                           }
                           //dataType: 'json',
                           //encode: true
                    });
                    //the json data returned from ajax.php
                    //.done(function(data){
                    //    var arrData = data;
                    //    console.log(data);
                    //    //$('#network').html(JSON.stringify(data));
                    //    $('#network').html(data);
                    //    $('#network').append('did it work?<br/>');
                    //});

                return false;
                //event.preventDefault();
                });
            
            
                        //submit the topic list dropdown menu on change
            //$('#nodeType-posts').click(function() {
            //        $('#networkDirected').append(makeFDposts(d3data));
            //        $('#networkColor').show();
            //        $('#networkSize').hide();
            //        $('#nodeType-posts').hide();
            //        $('#nodeType-people').show();
            //    });
            //
            //$('#nodeType-people').click(function(){
            //        $('#networkDirected').append(makeForceDirected(d3data));
            //        $('#networkColor').hide();
            //        $('#networkSize').show();
            //        $('#nodeType-posts').show();
            //        $('#nodeType-people').hide();
            //    });
$('input:radio[name="nodeTypeDisplay"]').change(
    function(){
        var valRadio = $('input:radio[name="nodeTypeDisplay"]:checked').val();
        if( valRadio == 'threads'){
            $('#nodeSize').val('sent');
            $('#networkDirected').append(makeForceDirected(d3data));
            $('#networkColor').hide();
            $('#networkSize').show();
            $('#userKey').show();
            $('#threadKey').hide();
        }else if(valRadio == 'people'){
            $('#nodeColor').val('none').change();
            $('#networkDirected').append(makeFDposts(d3data));
            $('#networkColor').show();
            $('#networkSize').hide();
            $('#userKey').hide();
            $('#threadKey').show();
        }
    });
            
            
            //create tabs navigation for the visualization data
            $('#vis_container').tabs({
                beforeActivate: function (event, ui) {
                    var runD3 = ui.newPanel.attr('id');
                    switch(runD3){
                        case 'network':
                            $('line').css({"stroke": "#eee", "stroke-width": "1.5px"});
                            
                            var valRadio = $('input:radio[name="nodeTypeDisplay"]:checked').val();
                            if( valRadio == 'threads'){
                                $('#nodeSize').val('sent');
                                $('#networkDirected').append(makeForceDirected(d3data));
                                $('#networkColor').hide();
                                $('#networkSize').show();
                            }else if(valRadio == 'people'){
                                $('#nodeColor').val('none').change();
                                $('#networkDirected').append(makeFDposts(d3data));
                                $('#networkColor').show();
                                $('#networkSize').hide();
                            }

                            //$('#networkDirected').append(makeForceDirected(d3data));
                            //$('#nodeSize').val('sent');
                            $('#right-container').show();
                            $('#nodeType-posts').show();
                            $('#nodeType-people').hide();
                            break;
                        ////Due to an issue with the timing of the d3 rendering, the chord diagram needs to be created from its own page.
                        ////The function getComputedTextLength() is used within makeChodMatrix() on the d3-visulas.js page to calculate the length
                        ////of text taken up by the name of the user. This is then used to determin if the name will fit in the display space or not.
                        ////Unfortunately to be useful, that function has to be called after the chart is rendered, otherwise it will always return a
                        ////length of zero.  To render the chart in a timely manner, the Chord Diagram is no longer being generated via this jquery script
                        ////and is now being called from chord.php.
                        //case 'chord':
                        //    $('#chord').append(makeChordMatrix(d3data));
                        //    break;
                        case 'timeline':
                            //$('#timeline').append(makeTimeline1(d3data));
                            $('#timeline').append(makeTimeline2(d3data));
                            break;
                        case 'matrix':
                            $('line').css({"stroke": "#fff", "stroke-width": ".5px"});
                            $('text.active').css({"fill": "red"});
                            $('#matrix').append(makeBetween(d3data));
                            //$('#matrix').append(makeAdjMatrix(d3data));
                            break;
                        case 'statistics':
                            $('#statistics').append(makeStatistics(d3data));
                            break;
                        case 'dataset':
                            $('#dataset').append(makeDataSet(d3data));
                            break;
                        case 'stream':
                            //$('#stream').append(makeStream(d3data));
                            //$('#stream').append(makeFDposts(d3data));
                            break;
                        case 'cloud':
                            $('#cloud').append(makeCloud(d3data));
                            break;
                    }
                  }
                //beforeLoad: function( event, ui ) {
                //    ui.jqXHR.error(function() {
                //    ui.panel.html(
                //    "Please select a discussion from the list above." );
                //    });
                //}
                    
            });
            
            //set About tab as start and disable all graph tabs
            $('#vis_container').tabs({
                disabled: [0,1,2,3,4],
                active: 5
            });
        });
    </script>
    <script>
        //(function ($) {
        //    $.extend($.fn, {
        //        makeCssInline: function () {
        //            this.each(function (idx, el) {
        //                var style = el.style;
        //                var properties = [];
        //                for (var property in style) {
        //                    if ($(this).css(property)) {
        //                        properties.push(property + ':' + $(this).css(property));
        //                    }
        //                }
        //                this.style.cssText = properties.join(';');
        //                $(this).children().makeCssInline();
        //            });
        //        }
        //    });
        //}(jQuery));
        
    </script>
    <script>
        //http://blog.eliacontini.info/post/79860720828/export-to-csv-using-javascript-the-download
        //https://github.com/EliaContini/js-experiments/blob/master/exportToCSV/index.html
        // prepare CSV data
        function save2CSV(data, fileName){
            var csvData = new Array();
            //csvData.push('"Book title","Author"');
            data.forEach(function(item, index, array) {
                    csvData.push('"' + item.title + '","' + item.author + '"');
            });
            
            // download stuff
            //var fileName = "data.csv";
            var buffer = csvData.join("\n");
            var blob = new Blob([buffer], {
                    "type": "text/csv;charset=utf8;"			
            });
            var link = document.createElement("a");
            
            if(link.download !== undefined) { // feature detection
                    // Browsers that support HTML5 download attribute
                    link.setAttribute("href", window.URL.createObjectURL(blob));
                    link.setAttribute("download", fileName);
            }
            else if(navigator.msSaveBlob) { // IE 10+
                    navigator.msSaveBlob(blob, fileName);
            }
            else { 
                    // it needs to implement server side export
                    link.setAttribute("href", "http://www.example.com/export");
            }
            link.innerHTML = "Export to CSV";
            document.body.appendChild(link);
        }
    </script>
</body> 
</html>