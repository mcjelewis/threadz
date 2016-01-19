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
                <li><a href="#matrix"><span>Matrix</span></a></li>
                <li><a href="#statistics"><span>Statistics</span></a></li>
                <li><a href="#dataset"><span>Data Set</span></a></li>
                <!-- <li><a href="#stream"><span>Timestream</span></a></li>-->
                <!-- <li><a href="#cloud"><span>Word Cloud</span></a></li>-->
                <!-- <li><a href="about.php"><span>About</span></a></li>-->
                <!-- <li><a href="help.php"><span>Help</span></a></li>-->
                <!-- <li><a href="roadmap.php"><span>Road Map</span></a></li>-->
                
                <li><a href="help.php"><span>Help</span></a></li>
            </ul>
            <div id="network">
                <div id='networkSize'>Size by:
                    <select id="nodeSize">
                        <option value="sent">Posts Sent</option>
                        <option value="received">Posts Received</option>
                        <option value="total">Total Posts</option>
                        <option value="word">Word Count</option>
                        <option value="word_avg">Word Count Avg</option>
                    </select>
                </div>
                <div class='d3-visual'><div id='networkDirected'></div></div>
                <div id='right-container'>
                    <h4>Network Diagram</h4>
                    <p>The network visualization shows a typical line/node graph that connects users together.  Each node represents a different user in the discussion and each line represents a post from one user to another.  The relative size of the circles (nodes) changes to represent the value select for either number of posts sent, posts recieved, total posts, total word count of posts sent, and avg word count of post sent. </p>
                    <p>This visualization is useful to quickly discern any individual or group that is isolated or is a driving hub within the forum.</p>
                    <p>The nodes in this chart are movable to help single out individuals or groups when the network of connections gets too complex visually.</p>
                    <p>To manipulate a node, click and drag a node circle to another part of the page.  To release the node, double click.</p>
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
                    <p>The timeline visualization displays the count of discussion posts by date. This visual can help you determin the rate of submissions and determin if there are any patterns to those submissions.</p>
                </div>
                <div class='discussionLink'></div>
            </div>
            <div id="matrix" class='matrix'>
                <div id='matrixOrder'>Order By:
                    <select id="order">
                        <option value="name">Name</option>
                        <option value="count">Frequency</option>
                        <option value="post_count">Posts Sent</option>
                        <option value="posts_received">Posts Received</option>
                        <!--<option value="group">Group</option>-->
                    </select>
                </div>
                <div class='d3-visual'><div id='matrixHeatmap'></div></div>
                <div id='right-container'>
                    <h4>Matrix Heatmap</h4>
                    <p>The matrix visualization the number of communications a person was a part of.</p>
                    <p>The color of the cell between two students shows the frequency of connections. The darker the cell the higher the frequency.</p>
                    <p>The order of the matrix can be set to the total frequency of connections, number of posts sent, number of posts recieved, or name.</p>
                </div>
                <div class='discussionLink'></div>
            </div> 
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
            <!--<div id="cloud"></div>-->
            <!--<div id="stream"></div>-->
            <!--<div id="about"></div>-->
            <!--<div id="roadmap"></div>-->
            <div id="help"></div>
        </div>
        <div id='saveImage'>
            <button class="btn btn-success" id="save_as_svg" value="">Save as SVG</button>
            <button class="btn btn-success" id="save_as_pdf" value="">Save as PDF</button>
            <button class="btn btn-success" id="save_as_png" value="">Save as High-Res PNG</button>
         </div>
        <div id="svgdataurl"></div>
        
        <!-- Hidden <FORM> to submit the SVG data to the server, which will convert it to SVG/PDF/PNG downloadable file.
         The form is populated and submitted by the JavaScript below. -->
        <form id="svgform" method="post" action="/cgi-bin/svgDownload.pl">
            <input type="hidden" id="output_format" name="output_format" value="">
            <input type="hidden" id="data" name="data" value="">
        </form>
    </div>
    <div id='noTopics'>
        <h3>No Discussion Topics Avaliable</h3>
        <p>Before you can use Threadz, there must be at minimum of one published disucssion in your course with one or more submitted posts.  Please check to make sure that your course meets these requirments.</p>
    </div>
    <script>
        //jquery submit Discussion Topic choice on change
        $(function() {
            //hide the network and matrix forms until a discussion has been selected.
            $('#networkSize').hide();
            $('#matrixOrder').hide();
            $('#saveImage').hide();
            
            <?php echo $topicsAvaliable ?>
            
            //submit the topic list dropdown menu on change
            $('#topic_list').change(function() {
                $('#formTopics').submit();
            });

            $('#formTopics').submit(function() {
                    $.ajax({
                           type: "POST",
                           url: "ajax.php",
                           data: $("#formTopics").serialize(), // serializes the form's elements.
                           dataType: 'json',
                           success: function(data){
                                if(data == "Expired Session, please reauthenticate Threadz."){
                                    console.log(data)
                                    $('topics').html(data);
                                }else{
                                    d3data = data;
                                    $('#networkDirected').html(d3data);
                                    $('#dataTest').html(d3data);
                                    $('#networkDirected').append(makeForceDirected(d3data));
                                    //$('#saveImage').html('<button id="save">Save as Image</button>');
                                    $('#welcome').hide();
                                    $('#networkSize').show();
                                    $('#matrixOrder').show();
                                    $('#vis_container').tabs('option', 'disabled',[]);
                                    $('#vis_container').tabs({active: 0});
                                    $('.discussionLink').html("<a class='mini' target='_blank' href='" + d3data.topic.url + "'>go to Discussion</a>");
                                    $('#saveImage').show();
                                }
                           }
                           //dataType: 'json',
                           //encode: true
                    })
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
            
            //create tabs navigation for the visualization data
            $('#vis_container').tabs({
                beforeActivate: function (event, ui) {
                    var runD3 = ui.newPanel.attr('id');
                    switch(runD3){
                        case 'network':
                            $('line').css({"stroke": "#eee", "stroke-width": "1.5px"});
                            $('#networkDirected').append(makeForceDirected(d3data));
                            $('#nodeSize').val('sent');
                            $('#right-container').show();
                            $('#saveImage').show();
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
                            $('#saveImage').show();
                            break;
                        case 'matrix':
                            $('line').css({"stroke": "#fff", "stroke-width": ".5px"});
                            $('text.active').css({"fill": "red"});
                            $('#matrix').append(makeAdjMatrix(d3data));
                            $('#saveImage').show();
                            break;
                        case 'statistics':
                            $('#statistics').append(makeStatistics(d3data));
                            $('#saveImage').hide();
                            break;
                        case 'dataset':
                            $('#dataset').append(makeDataSet(d3data));
                            $('#saveImage').hide();
                            break;
                        case 'stream':
                            //$('#stream').append(makeStream(d3data));
                            break;
                        case 'cloud':
                            //$('#cloud').append(makeCloud(d3data));
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
                disabled: [0,1,2,3,4,5],
                active: 6
            });
        });
    </script>
    <script>
        (function ($) {
            $.extend($.fn, {
                makeCssInline: function () {
                    this.each(function (idx, el) {
                        var style = el.style;
                        var properties = [];
                        for (var property in style) {
                            if ($(this).css(property)) {
                                properties.push(property + ':' + $(this).css(property));
                            }
                        }
                        this.style.cssText = properties.join(';');
                        $(this).children().makeCssInline();
                    });
                }
            });
        }(jQuery));

        //http://d3export.housegordon.org/
        //https://github.com/agordon/d3export_demo
        /*
           Utility function: populates the <FORM> with the SVG data
           and the requested output format, and submits the form.
        */
        function submit_download_form(output_format)
        {
                // Get the d3js SVG element
                // Extract the data as SVG text string
                var svg = "";
                //https://github.com/Karl33to/jquery.inlineStyler
                //add to the svg the css to inline style
                $(window).load(function() {
                    //$('iFrame').contents().find('svg').inlineStyler( );
                    $('iframe').contents().find('svg').inlineStyler( );
		});
                
                    var svg = $('svg')[0];
                var svg_xml = (new XMLSerializer).serializeToString(svg);
        
                // Submit the <FORM> to the server.
                // The result will be an attachment file to download.
                var form = document.getElementById("svgform");
                form['output_format'].value = output_format;
                form['data'].value = svg_xml ;
                form.submit();
        }
        
        /*
            One-time initialization
        */
        $(document).ready(function() {
            // Attached actions to the buttons
            $("#save_as_svg").click(function() { submit_download_form("svg"); });
            $("#save_as_pdf").click(function() { submit_download_form("pdf"); });
            $("#save_as_png").click(function() { submit_download_form("png"); });
        });
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