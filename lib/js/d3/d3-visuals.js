function soundAlarms(text){
  alert(text);
}

//set the data into an array used in Word Cloud
function setWords(data){
  var arrWords = [];
  for(var post in data){
    
  }
  
}


//2015-10-21T20:31:51Z
//2015-01-22T07:59:00Z
function isLate(submission, due){
  if(due == null){
    late = false;
  }else{
    var date1 = new Date(submission);
    var date2 = new Date(due);
    var lateBy = date1 - date2;
    if(lateBy < 0){
      late = false;
    }else{
      late = true;
    }
  }
  return late;
}


//////////////////////////////////
//	Tabulate data   //
//Create table
//http://stackoverflow.com/questions/9268645/creating-a-table-linked-to-a-csv-file
//////////////////////////////////
function tabulate(data, columns, div, caption) {
  //console.log(data);
  var setMap = 0;
  if(!columns){
    columns = Object.keys(data[0]);
    setMap = 1;
  }
  //console.log(columns);
  var divContainer = "#"+div;
    var table = d3.select(divContainer).append("table").attr("class", "tablesorter statTable "),
	tableId = table.attr("id", caption),
	//tcaption = table.append("caption"),
	thead = table.append("thead"),
	tbody = table.append("tbody");
	
    //append caption to table
    //tcaption.text(caption);
    
    // append the header row
    thead.append("tr")
	.selectAll("th")
	.data(columns)
	.enter()
	.append("th")
	.text(function(column) { return column; });

    // create a row for each object in the data
    var rows = tbody.selectAll("tr")
	.data(data)
	.enter()
	.append("tr");

    // create a cell in each row for each column

      var cells = rows.selectAll("td")
	.data(function(row) {
	    return columns.map(function(column) {
	      return {column: column, value: row[column]};
	    });
	})
	.enter()
	.append("td")
	.text(function(d) { return d.value; });


}

//////////////////////////////////
//	Missing Students from Discussion   //
//////////////////////////////////
function missing(data,div) {

  var divContainer = "#"+div;
  d3.select(divContainer).append("html").append(function(data) { return data + "<br>"; });

}
//////////////////////////////////
//	Set SNA Data for Download   //
//create data for SNA download
//////////////////////////////////
function setSNADownload(data) {
  var output = [];
  var i =0;
  for(var itemID in data["links"]["message_order"]){
    item = data["links"]["message_order"][itemID]
    //console.log(item);
    postedByID = item.posted_by;
    replyToID = item.reply_to;
    if(replyToID){
      replyTo_name = data["nodes"][replyToID].name;
    }else{
      replyTo_name = "";
    }
    output[i]= {"assignment_id": data['topic'].assignment_id, "vert1_name": data["nodes"][postedByID].name,"vert2_name": replyTo_name,"vert1_id": item.posted_by,"vert2_id": item.reply_to,"topic_id": data['topic'].topic_id,"topic_title": data['topic'].topic_title,"topic_url": data['topic'].url,"thread_id": item.thread,"created_at": item.posted_on, "message_id": item.message_id, "message_text": item.posted_message};
    i = i+1;
  }
  //console.log(output);
  return output;
}


//////////////////////////////////
//	Set Node Links Data    //
//set the data into an array used in the Force Directed charts.  Also process through nodes and links for generating thread level information.
//////////////////////////////////
function setNodeLinks(data){
  console.log(data);
    var arr=[];
    arr["nodes"] = [];
    arr["links"] = [];
    arr["pnodes"] = [];
    arr["plinks"] = [];
    arr["threads"] = [];
    arr["posts"] = [];
    arr['userData'] = [];
    arr['userData']['people'] = [];
    arr['userData']['post_counts'] = [];
    arr['userData']['word_counts'] = [];
    arr['userData']['posted_word_counts'] = [];
    arr["missing"] = [];
    arr["enrollments"] = data["enrollments"];
    //arr["missing"] = data["missing"]["id"];
    //arr["enrollments"] = data["enrollments"];
    var i=0;

    for(var id in data["missing"]["id"]){
      canvas_id = data["missing"]["id"][id];
      arr["missing"][id] = {"canvas_id": canvas_id, "name": data["enrollments"][canvas_id].name, "role": data["enrollments"][canvas_id].role, "enrollment_state": data["enrollments"][canvas_id].enrollment_state};
      }
    for(var item in data["nodes"]){
      arr.nodes[data["nodes"][item].source] = {"canvas_id": data["nodes"][item].canvas_id, "name": data["nodes"][item].name, "initials": data["nodes"][item].initials, "post_count": data["nodes"][item].post_count, "posts_received": data["nodes"][item].posts_received, "word_count": 0, "word_count_avg": 0, "group": 1, "deleted": 0};
      arr.userData.people[data["nodes"][item].canvas_id] = {"canvas_id": data["nodes"][item].canvas_id, "name": data["nodes"][item].name, "initials": data["nodes"][item].initials, "post_count": data["nodes"][item].post_count, "posts_received": data["nodes"][item].posts_received, "word_count": 0, "word_count_avg": 0, "group": 1, "deleted": 0};
      arr.userData['post_counts'].push(data["nodes"][item].post_count);
      arr.userData['word_counts'].push(data["nodes"][item].word_count);
      }

    var i=0;
    for(var item in data["links"]['thread_order']){
	var arrLink = data["links"]['thread_order'][item];
	arr.pnodes[i] = {"source": i, "message_id": arrLink.message_id, "canvas_id": data["nodes"][arrLink.posted_by].canvas_id, "name": data["nodes"][arrLink.posted_by].name, "initials": data["nodes"][arrLink.posted_by].initials, "post_count": data["nodes"][arrLink.posted_by].post_count, "posts_received": data["nodes"][arrLink.posted_by].posts_received, "posted_on": arrLink.posted_on, "posted_late": isLate(arrLink.posted_on, data["topic"].due_at), "rating_count": arrLink.rating_count, "rating_sum": arrLink.rating_sum, "unread": arrLink.unread, "liked": arrLink.liked, "wordCount": arrLink.posted_word_count, "posted_message": arrLink.posted_message, "thread_start": arrLink.thread_start};
	arr.posts[arrLink.message_id] = {"source": i}
	arr.userData['posted_word_counts'].push(arrLink.posted_word_count);
	i++;
    }
    i=0;
    for(var item in data["links"]['thread_order']){
      var arrLink = data["links"]['thread_order'][item];
      if(arrLink['unread']==true){
	$group = 1;
      }else{
	$group = 2;
      }

      arr.links[i] = {"source": arrLink.source, "target": arrLink.target, "group": $group, "wordCount": arrLink.posted_word_count};
      var ratio;
      //console.log(arrLink); 
      //cycle through and save the word counts for each person in nodes if post isn't deleted
      if(arrLink.deleted != "true"){
	nSource = arrLink.source;
	arr.nodes[nSource].word_count = arr.nodes[nSource].word_count + arrLink.posted_word_count;
	arr.nodes[nSource].word_count_avg = Math.round(arr.nodes[nSource].word_count/arr.nodes[nSource].post_count);
	arr.userData.people[arrLink.posted_by].word_count = arr.userData.people[arrLink.posted_by].word_count + arrLink.posted_word_count;
	arr.userData.people[arrLink.posted_by].word_count_avg = Math.round(arr.userData.people[arrLink.posted_by].word_count/arr.userData.people[arrLink.posted_by].post_count);
      }
     //console.log(arr); 
      //collect a count of the number of words of posts for a thread
      var t = arrLink.thread;
      if(arrLink.subthread == 0){
	var ptarget = arr.posts[arrLink.thread].source;
	ratio =  arrLink.posted_word_count/arrLink.posted_word_count;
	arr.threads[t]={"deleted": arrLink.deleted, "thread": arrLink.thread, "posted_wordCount": arrLink.posted_word_count, "total_wordCount": arrLink.posted_word_count, "ratio": "1:"+ ratio};
      }else{
	var ptarget = arr.posts[arrLink.subthread].source;
	arr.threads[t].total_wordCount = arr.threads[t].total_wordCount + arrLink.posted_word_count;
	ratio = Math.round(arr.threads[t].total_wordCount/arr.threads[t].posted_wordCount);
	arr.threads[t].ratio = "1:"+ratio;
      }
      arr.plinks[i] = {"source": i, "target": ptarget, "group": $group, "wordCount": arrLink.posted_word_count, "posted_on": arrLink.posted_on, "posted_late": isLate(arrLink.posted_on, data["topic"].due_at), "rating_count": arrLink.rating_count, "rating_sum": arrLink.rating_sum, "unread": arrLink.unread, "liked": arrLink.liked};
      i++;
    }

  //console.log(arr);
    return arr;
}

//////////////////////////////////
//	Set Chord Data    //
//set the data into a matrix of relationships between posts organized for use with the Chord Diagram.
//////////////////////////////////
function setChord(data){
  //console.log(data['nodes']);
    var arr=[];
    arr["matrix"] = [];
    arr["users"] = [];
    arr["relations"] = [];
    var author_id = data['topic']['author_id'];
    var replyToName;
    var i=0;
    for(var item in data["nodes"]){
      arr.matrix[data["nodes"][item].source] = data["nodes"][item].relationships;
      arr.users[data["nodes"][item].source] = {};
      arr.users[data["nodes"][item].source].name = data["nodes"][item].name;
      arr.users[data["nodes"][item].source].initials = data["nodes"][item].initials;
      arr.users[data["nodes"][item].source].post_count = data["nodes"][item].post_count;
    }
    for(var item in data["links"]['thread_order']){
      var arrLink = data["links"]['thread_order'][item];
      var postByName = data['nodes'][arrLink.posted_by].name;
      var postSource = data['nodes'][arrLink.posted_by].source;
      var postMessage = data["links"]['thread_order'][item].posted_message;
      //console.log(postByName);
      //console.log(postSource);
      //console.log(postMessage);
      if(arrLink.reply_to != null){
	replyToName = data['nodes'][arrLink.reply_to].name;
      }else{
	//Occasionally the author isn't set in the API.
	if(data['nodes'][author_id] != null){
	  replyToName = data['nodes'][author_id].name;
	}else{
	  replyToName = "";
	}
      }
      
      //console.log(replyToName);
      snarelationship = postByName +"_"+ replyToName;
      if (arr.relations[snarelationship]){
	arr.relations[snarelationship].value += 1;
      }else{
        arr.relations[snarelationship]={};
        arr.relations[snarelationship].from = postByName;
        arr.relations[snarelationship].to = replyToName;
        arr.relations[snarelationship].value = 1;
      }
      replyToName ="";
    }
    console.log(arr);
    return arr;
}

//format data for dateline.
function setDateline(data){
  var arr=[];
  var i=0;
  for(var item in data["timeline"]["date"]){
    arr[i]={"count":data["timeline"]["date"][item].count, "post_date":data["timeline"]["date"][item].post_date};
    i++;
  }
  return arr;
}


//////////////////////////////////
//	Set Timeline Data //
//format data for timeline.
//////////////////////////////////
function setTimeline(data){
  var arr=[];
  var i=0;
  for(var item in data["timeline"]["datetime"]){
    arr[i]={"count":data["timeline"]["datetime"][item].count, "post_datetime":data["timeline"]["datetime"][item].post_datetime};
    i++;
  }
return arr;
}

//////////////////////////////////
//	Set Fueq data //
//format data for timeline.
//////////////////////////////////

function setWordFreq(data){
  var obj = data["totals"]['messageData']['frequencies'];
  var arr=[];
  var i=0;
  for (var key in obj) {
      if (obj.hasOwnProperty(key)) {
	if(obj[key] > 1){
	  arr[i]={"text":key, "frequency": obj[key]};
	 i++;
	}
      }
  };
var result = arr.join(',');

return arr;
}

//////////////////////////////////
//	Set default variables
//////////////////////////////////

var width = 600,
    height = 350,
    r = 15;
    r_min=5;
    r_max=40;
var height_original = height;
var chartSet = 0;

//////////////////////////////////
//	Make SNA Connections    //
//////////////////////////////////
//////////////////////////////////
var makeForceDirected = function(data){
  chartSet = 1;
  var snaData = setNodeLinks(data);
//var snaData = data;
//var snaData = {"nodes":[{"name":"Myriel","group":1},{"name":"Napoleon","group":1},{"name":"Mlle.Baptistine","group":1},{"name":"Mme.Magloire","group":1},{"name":"CountessdeLo","group":1},{"name":"Geborand","group":1},{"name":"Champtercier","group":1},{"name":"Cravatte","group":1},{"name":"Count","group":1},{"name":"OldMan","group":1},{"name":"Labarre","group":2},{"name":"Valjean","group":2},{"name":"Marguerite","group":3},{"name":"Mme.deR","group":2},{"name":"Isabeau","group":2},{"name":"Gervais","group":2},{"name":"Tholomyes","group":3},{"name":"Listolier","group":3},{"name":"Fameuil","group":3},{"name":"Blacheville","group":3},{"name":"Favourite","group":3},{"name":"Dahlia","group":3},{"name":"Zephine","group":3},{"name":"Fantine","group":3},{"name":"Mme.Thenardier","group":4},{"name":"Thenardier","group":4},{"name":"Cosette","group":5},{"name":"Javert","group":4},{"name":"Fauchelevent","group":0},{"name":"Bamatabois","group":2},{"name":"Perpetue","group":3},{"name":"Simplice","group":2},{"name":"Scaufflaire","group":2},{"name":"Woman1","group":2},{"name":"Judge","group":2},{"name":"Champmathieu","group":2},{"name":"Brevet","group":2},{"name":"Chenildieu","group":2},{"name":"Cochepaille","group":2},{"name":"Pontmercy","group":4},{"name":"Boulatruelle","group":6},{"name":"Eponine","group":4},{"name":"Anzelma","group":4},{"name":"Woman2","group":5},{"name":"MotherInnocent","group":0},{"name":"Gribier","group":0},{"name":"Jondrette","group":7},{"name":"Mme.Burgon","group":7},{"name":"Gavroche","group":8},{"name":"Gillenormand","group":5},{"name":"Magnon","group":5},{"name":"Mlle.Gillenormand","group":5},{"name":"Mme.Pontmercy","group":5},{"name":"Mlle.Vaubois","group":5},{"name":"Lt.Gillenormand","group":5},{"name":"Marius","group":8},{"name":"BaronessT","group":5},{"name":"Mabeuf","group":8},{"name":"Enjolras","group":8},{"name":"Combeferre","group":8},{"name":"Prouvaire","group":8},{"name":"Feuilly","group":8},{"name":"Courfeyrac","group":8},{"name":"Bahorel","group":8},{"name":"Bossuet","group":8},{"name":"Joly","group":8},{"name":"Grantaire","group":8},{"name":"MotherPlutarch","group":9},{"name":"Gueulemer","group":4},{"name":"Babet","group":4},{"name":"Claquesous","group":4},{"name":"Montparnasse","group":4},{"name":"Toussaint","group":5},{"name":"Child1","group":10},{"name":"Child2","group":10},{"name":"Brujon","group":4},{"name":"Mme.Hucheloup","group":8}],
//"links":[{"source":1,"target":0,"value":1},{"source":2,"target":0,"value":8},{"source":3,"target":0,"value":10},{"source":3,"target":2,"value":6},{"source":4,"target":0,"value":1},{"source":5,"target":0,"value":1},{"source":6,"target":0,"value":1},{"source":7,"target":0,"value":1},{"source":8,"target":0,"value":2},{"source":9,"target":0,"value":1},{"source":11,"target":10,"value":1},{"source":11,"target":3,"value":3},{"source":11,"target":2,"value":3},{"source":11,"target":0,"value":5},{"source":12,"target":11,"value":1},{"source":13,"target":11,"value":1},{"source":14,"target":11,"value":1},{"source":15,"target":11,"value":1},{"source":17,"target":16,"value":4},{"source":18,"target":16,"value":4},{"source":18,"target":17,"value":4},{"source":19,"target":16,"value":4},{"source":19,"target":17,"value":4},{"source":19,"target":18,"value":4},{"source":20,"target":16,"value":3},{"source":20,"target":17,"value":3},{"source":20,"target":18,"value":3},{"source":20,"target":19,"value":4},{"source":21,"target":16,"value":3},{"source":21,"target":17,"value":3},{"source":21,"target":18,"value":3},{"source":21,"target":19,"value":3},{"source":21,"target":20,"value":5},{"source":22,"target":16,"value":3},{"source":22,"target":17,"value":3},{"source":22,"target":18,"value":3},{"source":22,"target":19,"value":3},{"source":22,"target":20,"value":4},{"source":22,"target":21,"value":4},{"source":23,"target":16,"value":3},{"source":23,"target":17,"value":3},{"source":23,"target":18,"value":3},{"source":23,"target":19,"value":3},{"source":23,"target":20,"value":4},{"source":23,"target":21,"value":4},{"source":23,"target":22,"value":4},{"source":23,"target":12,"value":2},{"source":23,"target":11,"value":9},{"source":24,"target":23,"value":2},{"source":24,"target":11,"value":7},{"source":25,"target":24,"value":13},{"source":25,"target":23,"value":1},{"source":25,"target":11,"value":12},{"source":26,"target":24,"value":4},{"source":26,"target":11,"value":31},{"source":26,"target":16,"value":1},{"source":26,"target":25,"value":1},{"source":27,"target":11,"value":17},{"source":27,"target":23,"value":5},{"source":27,"target":25,"value":5},{"source":27,"target":24,"value":1},{"source":27,"target":26,"value":1},{"source":28,"target":11,"value":8},{"source":28,"target":27,"value":1},{"source":29,"target":23,"value":1},{"source":29,"target":27,"value":1},{"source":29,"target":11,"value":2},{"source":30,"target":23,"value":1},{"source":31,"target":30,"value":2},{"source":31,"target":11,"value":3},{"source":31,"target":23,"value":2},{"source":31,"target":27,"value":1},{"source":32,"target":11,"value":1},{"source":33,"target":11,"value":2},{"source":33,"target":27,"value":1},{"source":34,"target":11,"value":3},{"source":34,"target":29,"value":2},{"source":35,"target":11,"value":3},{"source":35,"target":34,"value":3},{"source":35,"target":29,"value":2},{"source":36,"target":34,"value":2},{"source":36,"target":35,"value":2},{"source":36,"target":11,"value":2},{"source":36,"target":29,"value":1},{"source":37,"target":34,"value":2},{"source":37,"target":35,"value":2},{"source":37,"target":36,"value":2},{"source":37,"target":11,"value":2},{"source":37,"target":29,"value":1},{"source":38,"target":34,"value":2},{"source":38,"target":35,"value":2},{"source":38,"target":36,"value":2},{"source":38,"target":37,"value":2},{"source":38,"target":11,"value":2},{"source":38,"target":29,"value":1},{"source":39,"target":25,"value":1},{"source":40,"target":25,"value":1},{"source":41,"target":24,"value":2},{"source":41,"target":25,"value":3},{"source":42,"target":41,"value":2},{"source":42,"target":25,"value":2},{"source":42,"target":24,"value":1},{"source":43,"target":11,"value":3},{"source":43,"target":26,"value":1},{"source":43,"target":27,"value":1},{"source":44,"target":28,"value":3},{"source":44,"target":11,"value":1},{"source":45,"target":28,"value":2},{"source":47,"target":46,"value":1},{"source":48,"target":47,"value":2},{"source":48,"target":25,"value":1},{"source":48,"target":27,"value":1},{"source":48,"target":11,"value":1},{"source":49,"target":26,"value":3},{"source":49,"target":11,"value":2},{"source":50,"target":49,"value":1},{"source":50,"target":24,"value":1},{"source":51,"target":49,"value":9},{"source":51,"target":26,"value":2},{"source":51,"target":11,"value":2},{"source":52,"target":51,"value":1},{"source":52,"target":39,"value":1},{"source":53,"target":51,"value":1},{"source":54,"target":51,"value":2},{"source":54,"target":49,"value":1},{"source":54,"target":26,"value":1},{"source":55,"target":51,"value":6},{"source":55,"target":49,"value":12},{"source":55,"target":39,"value":1},{"source":55,"target":54,"value":1},{"source":55,"target":26,"value":21},{"source":55,"target":11,"value":19},{"source":55,"target":16,"value":1},{"source":55,"target":25,"value":2},{"source":55,"target":41,"value":5},{"source":55,"target":48,"value":4},{"source":56,"target":49,"value":1},{"source":56,"target":55,"value":1},{"source":57,"target":55,"value":1},{"source":57,"target":41,"value":1},{"source":57,"target":48,"value":1},{"source":58,"target":55,"value":7},{"source":58,"target":48,"value":7},{"source":58,"target":27,"value":6},{"source":58,"target":57,"value":1},{"source":58,"target":11,"value":4},{"source":59,"target":58,"value":15},{"source":59,"target":55,"value":5},{"source":59,"target":48,"value":6},{"source":59,"target":57,"value":2},{"source":60,"target":48,"value":1},{"source":60,"target":58,"value":4},{"source":60,"target":59,"value":2},{"source":61,"target":48,"value":2},{"source":61,"target":58,"value":6},{"source":61,"target":60,"value":2},{"source":61,"target":59,"value":5},{"source":61,"target":57,"value":1},{"source":61,"target":55,"value":1},{"source":62,"target":55,"value":9},{"source":62,"target":58,"value":17},{"source":62,"target":59,"value":13},{"source":62,"target":48,"value":7},{"source":62,"target":57,"value":2},{"source":62,"target":41,"value":1},{"source":62,"target":61,"value":6},{"source":62,"target":60,"value":3},{"source":63,"target":59,"value":5},{"source":63,"target":48,"value":5},{"source":63,"target":62,"value":6},{"source":63,"target":57,"value":2},{"source":63,"target":58,"value":4},{"source":63,"target":61,"value":3},{"source":63,"target":60,"value":2},{"source":63,"target":55,"value":1},{"source":64,"target":55,"value":5},{"source":64,"target":62,"value":12},{"source":64,"target":48,"value":5},{"source":64,"target":63,"value":4},{"source":64,"target":58,"value":10},{"source":64,"target":61,"value":6},{"source":64,"target":60,"value":2},{"source":64,"target":59,"value":9},{"source":64,"target":57,"value":1},{"source":64,"target":11,"value":1},{"source":65,"target":63,"value":5},{"source":65,"target":64,"value":7},{"source":65,"target":48,"value":3},{"source":65,"target":62,"value":5},{"source":65,"target":58,"value":5},{"source":65,"target":61,"value":5},{"source":65,"target":60,"value":2},{"source":65,"target":59,"value":5},{"source":65,"target":57,"value":1},{"source":65,"target":55,"value":2},{"source":66,"target":64,"value":3},{"source":66,"target":58,"value":3},{"source":66,"target":59,"value":1},{"source":66,"target":62,"value":2},{"source":66,"target":65,"value":2},{"source":66,"target":48,"value":1},{"source":66,"target":63,"value":1},{"source":66,"target":61,"value":1},{"source":66,"target":60,"value":1},{"source":67,"target":57,"value":3},{"source":68,"target":25,"value":5},{"source":68,"target":11,"value":1},{"source":68,"target":24,"value":1},{"source":68,"target":27,"value":1},{"source":68,"target":48,"value":1},{"source":68,"target":41,"value":1},{"source":69,"target":25,"value":6},{"source":69,"target":68,"value":6},{"source":69,"target":11,"value":1},{"source":69,"target":24,"value":1},{"source":69,"target":27,"value":2},{"source":69,"target":48,"value":1},{"source":69,"target":41,"value":1},{"source":70,"target":25,"value":4},{"source":70,"target":69,"value":4},{"source":70,"target":68,"value":4},{"source":70,"target":11,"value":1},{"source":70,"target":24,"value":1},{"source":70,"target":27,"value":1},{"source":70,"target":41,"value":1},{"source":70,"target":58,"value":1},{"source":71,"target":27,"value":1},{"source":71,"target":69,"value":2},{"source":71,"target":68,"value":2},{"source":71,"target":70,"value":2},{"source":71,"target":11,"value":1},{"source":71,"target":48,"value":1},{"source":71,"target":41,"value":1},{"source":71,"target":25,"value":1},{"source":72,"target":26,"value":2},{"source":72,"target":27,"value":1},{"source":72,"target":11,"value":1},{"source":73,"target":48,"value":2},{"source":74,"target":48,"value":2},{"source":74,"target":73,"value":3},{"source":75,"target":69,"value":3},{"source":75,"target":68,"value":3},{"source":75,"target":25,"value":3},{"source":75,"target":48,"value":1},{"source":75,"target":41,"value":1},{"source":75,"target":70,"value":1},{"source":75,"target":71,"value":1},{"source":76,"target":64,"value":1},{"source":76,"target":65,"value":1},{"source":76,"target":66,"value":1},{"source":76,"target":63,"value":1},{"source":76,"target":62,"value":1},{"source":76,"target":48,"value":1},{"source":76,"target":58,"value":1}]}
  var pDomain = d3.extent(snaData.userData.post_counts.sort(d3.ascending))
  var quantilePosts = d3.scale.quantile().domain(pDomain).range(d3.range(10))
  var wDomain = d3.extent(snaData.userData.word_counts.sort(d3.ascending))
  var quantileWords = d3.scale.quantile().domain(wDomain).range(d3.range(10))
  
height = height_original + (data['totals']['total_participants'] * 8);
console.log(snaData);
  var force = d3.layout.force()
      .nodes(snaData.nodes)
      .links(snaData.links)
      .size([width, height])
      .charge(-500)
      .linkDistance(100)
      .on("tick", tick)
      .start();

  var drag = force.drag()
      .on("dragstart", dragstart);
  
  d3.select("svg")
       .remove();
       
  var svg = d3.select("#networkDirected").append("svg")
      .attr("width", width)
      .attr("height", height)
      .attr("xmlns", "http://www.w3.org/2000/svg");
  
    link = svg.selectAll(".link")
        .data(snaData.links)
        .enter().append("line")
        .attr("class", "link");

    node = svg.selectAll(".node")
      .data(snaData.nodes)
      .enter().append("g")
        .attr("class", "node")
        .on("dblclick", dblclick)
        .call(drag);

  
  //// The default size.
      node.append("circle")
        .attr("class", "networkOrange")
        .attr("r", function(d) { return d.post_count *2; });
	//.attr("r",function(d) { return quantilePosts(d.post_count) *2; })
	
  //change node size on change of #nodeSize select	
  d3.select("#nodeSize").on("change", function() {
    if(this.value == 'sent'){
      node.selectAll('circle').transition().duration(2500).delay(500).attr('r', function(d){ return d.post_count *2;})
    }else if(this.value == 'received'){
      node.selectAll('circle').transition().duration(2500).delay(500).attr('r', function(d){ return d.posts_received *2;})
    }else if(this.value == 'total'){
      node.selectAll('circle').transition().duration(2500).delay(500).attr('r', function(d){ return (d.post_count + d.posts_received)*2;})
    }else if(this.value == 'word'){
      node.selectAll('circle').transition().duration(2500).delay(500).attr('r', function(d){ return quantileWords(d.word_count) *4;})
    }else if(this.value == 'word_avg'){
      node.selectAll('circle').transition().duration(2500).delay(500).attr('r', function(d){ return quantileWords(d.word_count_avg) *2;})
    }else if(this.value == 'betweenness'){
      makeBetween(data);
    }
  });

      
    //node.append("text")
    //  .attr("x", -6)
    //  .attr("y", ".32em")
    //  .attr("class", "shadow")
    //  .text(function(d) { return d.name; });
  

    node.append("text")
      .attr("x", -6)
      //.attr("y", ".31em")
      .attr("dy", "15")
      .style("font-size",10 + "px")
      .style("color","#000")
      .text(function(d) { return d.name; });
  
  function tick() {
    link.attr("x1", function(d) { return d.source.x; })
        .attr("y1", function(d) { return d.source.y; })
        .attr("x2", function(d) { return d.target.x; })
        .attr("y2", function(d) { return d.target.y; });

    node.attr("transform", function(d) { return "translate(" + Math.max(r, Math.min(width - r, d.x)) + ", " + Math.max(r, Math.min(height - r, d.y)) + ")"; })
  }

  function dblclick(d) {
    d3.select(this).classed("fixed", d.fixed = false);
  }

  function dragstart(d) {
    d3.select(this).classed("fixed", d.fixed = true);
  }
 
 //add to a div the names of students that did not post a message 
  missing = d3.select("#missing")
    .html("")
    .append("h4")
    .text("Students With No Participation");
  missing = d3.select("#missing")
    .append("ul");
      item = missing.selectAll("li")
      .data(snaData.missing
      .filter(function(d) { return d != null; })
    )
    .enter()
    .append("li")
    .text(function(d) { return d != null ? d.name : null; })
    .filter(function(d) { return d.role != "StudentEnrollment"; }).remove();

}
////////////////////////////////////////////////////////////////////
//	Make Network Stream  //
////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
var makeFDposts = function(data, userID){
  chartSet = 1;
  var snaData = setNodeLinks(data);
//var snaData = data;
//var snaData = {"nodes":[{"name":"Myriel","group":1},{"name":"Napoleon","group":1},{"name":"Mlle.Baptistine","group":1},{"name":"Mme.Magloire","group":1},{"name":"CountessdeLo","group":1},{"name":"Geborand","group":1},{"name":"Champtercier","group":1},{"name":"Cravatte","group":1},{"name":"Count","group":1},{"name":"OldMan","group":1},{"name":"Labarre","group":2},{"name":"Valjean","group":2},{"name":"Marguerite","group":3},{"name":"Mme.deR","group":2},{"name":"Isabeau","group":2},{"name":"Gervais","group":2},{"name":"Tholomyes","group":3},{"name":"Listolier","group":3},{"name":"Fameuil","group":3},{"name":"Blacheville","group":3},{"name":"Favourite","group":3},{"name":"Dahlia","group":3},{"name":"Zephine","group":3},{"name":"Fantine","group":3},{"name":"Mme.Thenardier","group":4},{"name":"Thenardier","group":4},{"name":"Cosette","group":5},{"name":"Javert","group":4},{"name":"Fauchelevent","group":0},{"name":"Bamatabois","group":2},{"name":"Perpetue","group":3},{"name":"Simplice","group":2},{"name":"Scaufflaire","group":2},{"name":"Woman1","group":2},{"name":"Judge","group":2},{"name":"Champmathieu","group":2},{"name":"Brevet","group":2},{"name":"Chenildieu","group":2},{"name":"Cochepaille","group":2},{"name":"Pontmercy","group":4},{"name":"Boulatruelle","group":6},{"name":"Eponine","group":4},{"name":"Anzelma","group":4},{"name":"Woman2","group":5},{"name":"MotherInnocent","group":0},{"name":"Gribier","group":0},{"name":"Jondrette","group":7},{"name":"Mme.Burgon","group":7},{"name":"Gavroche","group":8},{"name":"Gillenormand","group":5},{"name":"Magnon","group":5},{"name":"Mlle.Gillenormand","group":5},{"name":"Mme.Pontmercy","group":5},{"name":"Mlle.Vaubois","group":5},{"name":"Lt.Gillenormand","group":5},{"name":"Marius","group":8},{"name":"BaronessT","group":5},{"name":"Mabeuf","group":8},{"name":"Enjolras","group":8},{"name":"Combeferre","group":8},{"name":"Prouvaire","group":8},{"name":"Feuilly","group":8},{"name":"Courfeyrac","group":8},{"name":"Bahorel","group":8},{"name":"Bossuet","group":8},{"name":"Joly","group":8},{"name":"Grantaire","group":8},{"name":"MotherPlutarch","group":9},{"name":"Gueulemer","group":4},{"name":"Babet","group":4},{"name":"Claquesous","group":4},{"name":"Montparnasse","group":4},{"name":"Toussaint","group":5},{"name":"Child1","group":10},{"name":"Child2","group":10},{"name":"Brujon","group":4},{"name":"Mme.Hucheloup","group":8}],
//"links":[{"source":1,"target":0,"value":1},{"source":2,"target":0,"value":8},{"source":3,"target":0,"value":10},{"source":3,"target":2,"value":6},{"source":4,"target":0,"value":1},{"source":5,"target":0,"value":1},{"source":6,"target":0,"value":1},{"source":7,"target":0,"value":1},{"source":8,"target":0,"value":2},{"source":9,"target":0,"value":1},{"source":11,"target":10,"value":1},{"source":11,"target":3,"value":3},{"source":11,"target":2,"value":3},{"source":11,"target":0,"value":5},{"source":12,"target":11,"value":1},{"source":13,"target":11,"value":1},{"source":14,"target":11,"value":1},{"source":15,"target":11,"value":1},{"source":17,"target":16,"value":4},{"source":18,"target":16,"value":4},{"source":18,"target":17,"value":4},{"source":19,"target":16,"value":4},{"source":19,"target":17,"value":4},{"source":19,"target":18,"value":4},{"source":20,"target":16,"value":3},{"source":20,"target":17,"value":3},{"source":20,"target":18,"value":3},{"source":20,"target":19,"value":4},{"source":21,"target":16,"value":3},{"source":21,"target":17,"value":3},{"source":21,"target":18,"value":3},{"source":21,"target":19,"value":3},{"source":21,"target":20,"value":5},{"source":22,"target":16,"value":3},{"source":22,"target":17,"value":3},{"source":22,"target":18,"value":3},{"source":22,"target":19,"value":3},{"source":22,"target":20,"value":4},{"source":22,"target":21,"value":4},{"source":23,"target":16,"value":3},{"source":23,"target":17,"value":3},{"source":23,"target":18,"value":3},{"source":23,"target":19,"value":3},{"source":23,"target":20,"value":4},{"source":23,"target":21,"value":4},{"source":23,"target":22,"value":4},{"source":23,"target":12,"value":2},{"source":23,"target":11,"value":9},{"source":24,"target":23,"value":2},{"source":24,"target":11,"value":7},{"source":25,"target":24,"value":13},{"source":25,"target":23,"value":1},{"source":25,"target":11,"value":12},{"source":26,"target":24,"value":4},{"source":26,"target":11,"value":31},{"source":26,"target":16,"value":1},{"source":26,"target":25,"value":1},{"source":27,"target":11,"value":17},{"source":27,"target":23,"value":5},{"source":27,"target":25,"value":5},{"source":27,"target":24,"value":1},{"source":27,"target":26,"value":1},{"source":28,"target":11,"value":8},{"source":28,"target":27,"value":1},{"source":29,"target":23,"value":1},{"source":29,"target":27,"value":1},{"source":29,"target":11,"value":2},{"source":30,"target":23,"value":1},{"source":31,"target":30,"value":2},{"source":31,"target":11,"value":3},{"source":31,"target":23,"value":2},{"source":31,"target":27,"value":1},{"source":32,"target":11,"value":1},{"source":33,"target":11,"value":2},{"source":33,"target":27,"value":1},{"source":34,"target":11,"value":3},{"source":34,"target":29,"value":2},{"source":35,"target":11,"value":3},{"source":35,"target":34,"value":3},{"source":35,"target":29,"value":2},{"source":36,"target":34,"value":2},{"source":36,"target":35,"value":2},{"source":36,"target":11,"value":2},{"source":36,"target":29,"value":1},{"source":37,"target":34,"value":2},{"source":37,"target":35,"value":2},{"source":37,"target":36,"value":2},{"source":37,"target":11,"value":2},{"source":37,"target":29,"value":1},{"source":38,"target":34,"value":2},{"source":38,"target":35,"value":2},{"source":38,"target":36,"value":2},{"source":38,"target":37,"value":2},{"source":38,"target":11,"value":2},{"source":38,"target":29,"value":1},{"source":39,"target":25,"value":1},{"source":40,"target":25,"value":1},{"source":41,"target":24,"value":2},{"source":41,"target":25,"value":3},{"source":42,"target":41,"value":2},{"source":42,"target":25,"value":2},{"source":42,"target":24,"value":1},{"source":43,"target":11,"value":3},{"source":43,"target":26,"value":1},{"source":43,"target":27,"value":1},{"source":44,"target":28,"value":3},{"source":44,"target":11,"value":1},{"source":45,"target":28,"value":2},{"source":47,"target":46,"value":1},{"source":48,"target":47,"value":2},{"source":48,"target":25,"value":1},{"source":48,"target":27,"value":1},{"source":48,"target":11,"value":1},{"source":49,"target":26,"value":3},{"source":49,"target":11,"value":2},{"source":50,"target":49,"value":1},{"source":50,"target":24,"value":1},{"source":51,"target":49,"value":9},{"source":51,"target":26,"value":2},{"source":51,"target":11,"value":2},{"source":52,"target":51,"value":1},{"source":52,"target":39,"value":1},{"source":53,"target":51,"value":1},{"source":54,"target":51,"value":2},{"source":54,"target":49,"value":1},{"source":54,"target":26,"value":1},{"source":55,"target":51,"value":6},{"source":55,"target":49,"value":12},{"source":55,"target":39,"value":1},{"source":55,"target":54,"value":1},{"source":55,"target":26,"value":21},{"source":55,"target":11,"value":19},{"source":55,"target":16,"value":1},{"source":55,"target":25,"value":2},{"source":55,"target":41,"value":5},{"source":55,"target":48,"value":4},{"source":56,"target":49,"value":1},{"source":56,"target":55,"value":1},{"source":57,"target":55,"value":1},{"source":57,"target":41,"value":1},{"source":57,"target":48,"value":1},{"source":58,"target":55,"value":7},{"source":58,"target":48,"value":7},{"source":58,"target":27,"value":6},{"source":58,"target":57,"value":1},{"source":58,"target":11,"value":4},{"source":59,"target":58,"value":15},{"source":59,"target":55,"value":5},{"source":59,"target":48,"value":6},{"source":59,"target":57,"value":2},{"source":60,"target":48,"value":1},{"source":60,"target":58,"value":4},{"source":60,"target":59,"value":2},{"source":61,"target":48,"value":2},{"source":61,"target":58,"value":6},{"source":61,"target":60,"value":2},{"source":61,"target":59,"value":5},{"source":61,"target":57,"value":1},{"source":61,"target":55,"value":1},{"source":62,"target":55,"value":9},{"source":62,"target":58,"value":17},{"source":62,"target":59,"value":13},{"source":62,"target":48,"value":7},{"source":62,"target":57,"value":2},{"source":62,"target":41,"value":1},{"source":62,"target":61,"value":6},{"source":62,"target":60,"value":3},{"source":63,"target":59,"value":5},{"source":63,"target":48,"value":5},{"source":63,"target":62,"value":6},{"source":63,"target":57,"value":2},{"source":63,"target":58,"value":4},{"source":63,"target":61,"value":3},{"source":63,"target":60,"value":2},{"source":63,"target":55,"value":1},{"source":64,"target":55,"value":5},{"source":64,"target":62,"value":12},{"source":64,"target":48,"value":5},{"source":64,"target":63,"value":4},{"source":64,"target":58,"value":10},{"source":64,"target":61,"value":6},{"source":64,"target":60,"value":2},{"source":64,"target":59,"value":9},{"source":64,"target":57,"value":1},{"source":64,"target":11,"value":1},{"source":65,"target":63,"value":5},{"source":65,"target":64,"value":7},{"source":65,"target":48,"value":3},{"source":65,"target":62,"value":5},{"source":65,"target":58,"value":5},{"source":65,"target":61,"value":5},{"source":65,"target":60,"value":2},{"source":65,"target":59,"value":5},{"source":65,"target":57,"value":1},{"source":65,"target":55,"value":2},{"source":66,"target":64,"value":3},{"source":66,"target":58,"value":3},{"source":66,"target":59,"value":1},{"source":66,"target":62,"value":2},{"source":66,"target":65,"value":2},{"source":66,"target":48,"value":1},{"source":66,"target":63,"value":1},{"source":66,"target":61,"value":1},{"source":66,"target":60,"value":1},{"source":67,"target":57,"value":3},{"source":68,"target":25,"value":5},{"source":68,"target":11,"value":1},{"source":68,"target":24,"value":1},{"source":68,"target":27,"value":1},{"source":68,"target":48,"value":1},{"source":68,"target":41,"value":1},{"source":69,"target":25,"value":6},{"source":69,"target":68,"value":6},{"source":69,"target":11,"value":1},{"source":69,"target":24,"value":1},{"source":69,"target":27,"value":2},{"source":69,"target":48,"value":1},{"source":69,"target":41,"value":1},{"source":70,"target":25,"value":4},{"source":70,"target":69,"value":4},{"source":70,"target":68,"value":4},{"source":70,"target":11,"value":1},{"source":70,"target":24,"value":1},{"source":70,"target":27,"value":1},{"source":70,"target":41,"value":1},{"source":70,"target":58,"value":1},{"source":71,"target":27,"value":1},{"source":71,"target":69,"value":2},{"source":71,"target":68,"value":2},{"source":71,"target":70,"value":2},{"source":71,"target":11,"value":1},{"source":71,"target":48,"value":1},{"source":71,"target":41,"value":1},{"source":71,"target":25,"value":1},{"source":72,"target":26,"value":2},{"source":72,"target":27,"value":1},{"source":72,"target":11,"value":1},{"source":73,"target":48,"value":2},{"source":74,"target":48,"value":2},{"source":74,"target":73,"value":3},{"source":75,"target":69,"value":3},{"source":75,"target":68,"value":3},{"source":75,"target":25,"value":3},{"source":75,"target":48,"value":1},{"source":75,"target":41,"value":1},{"source":75,"target":70,"value":1},{"source":75,"target":71,"value":1},{"source":76,"target":64,"value":1},{"source":76,"target":65,"value":1},{"source":76,"target":66,"value":1},{"source":76,"target":63,"value":1},{"source":76,"target":62,"value":1},{"source":76,"target":48,"value":1},{"source":76,"target":58,"value":1}]}

var r_scale = d3.scale.linear()
    .domain([0, 500])
    .range([10, 40]);
height = height_original + (data['totals']['total_participants'] * 8);
console.log(snaData);
  var force = d3.layout.force()
      .nodes(snaData.pnodes)
      .links(snaData.plinks)
      .size([width, height])
      .charge(-100)
      .linkDistance(25)
      .on("tick", tick)
      .start();

  var drag = force.drag()
      .on("dragstart", dragstart);
  
  d3.select("svg")
       .remove();
       
  var svg = d3.select("#networkDirected").append("svg")
      .attr("width", width)
      .attr("height", height)
      .attr("xmlns", "http://www.w3.org/2000/svg");
  
    link = svg.selectAll(".link")
        .data(snaData.plinks)
        .enter().append("line")
        .attr("class", "link");

    node = svg.selectAll(".node")
      .data(snaData.pnodes)
      .enter().append("g")
        .attr("class", "node")
        .on("dblclick", dblclick)
        .call(drag);

    var pDomain = d3.extent(snaData.userData.posted_word_counts.sort(d3.ascending))
    var quantilePosts = d3.scale.quantile().domain(pDomain).range(d3.range(5, 15));
    
  //// The default size.
      node.append("circle")
        //.attr("class", "networkOrange")	
    //change node color depending on read/unread
        .attr("class", "networkBlue")
	//.attr("r", function(d){return r_scale(d.wordCount)})
	//.attr("r",function(d) { return quantilePosts(d.post_count) *3; })
	.attr("r",r_min)
        .attr('fill-opacity', function(d) {
	  if( d.thread_start == true){
	    return 1;
	  }else{
	    return 0.7;
	  }
	});
    node.selectAll('circle').transition().duration(2500).delay(500).attr("r",r_min);
    node.append("text")
      .attr("x", -6)
      //.attr("y", ".31em")
      .attr("dy", "15")
      .style("font-size",10 + "px")
      .style("color","#000")
      .attr("class", function(d) { return d.initials; })
      .text(function(d) { return d.initials; });
      
  ////change node size on change of #nodeSize select	
  d3.select("#nodeColor").on("change", function() {
    if(this.value == 'none'){
      
      node.selectAll('circle').attr("r",1);
      node.selectAll('circle').transition().duration(2500).delay(500).attr("class", "networkBlue")
	//.attr("r",function(d) { return quantilePosts(d.post_count) *3; })
	.attr("r",r_min);
    }else if(this.value == 'length'){
      
      node.selectAll('circle').attr("r",1);
      node.selectAll('circle').transition().duration(2500).delay(500).attr("class", "networkBlue")
	//.attr("r",function(d) { return quantilePosts(d.post_count) *3; })
	.attr("r",function(d) { return quantilePosts(d.wordCount); });
    }else if(this.value == 'unread'){
      
      node.selectAll('circle').attr("r",1);
      node.selectAll('circle').transition().duration(2500).delay(500).attr("class", function(d) {
	  if(d.unread == true){
	    return "networkOrange";
	  }else{
	    return "networkBlue";
	  }
	})
	.attr("r", function(d) {
	  if(d.unread == true){
	    return "16";
	  }else{
	    return r_min;
	  }
	    });
    //}else if(this.value == 'users'){
    //  node.selectAll('circle').transition().duration(2500).delay(500).attr("class", function(d) { return d.initials; })
    }else if(this.value == 'isolated'){
      
      node.selectAll('circle').attr("r",1);
      node.selectAll('circle').transition().duration(2500).delay(500).attr("class", function(d) {
	  if(d.posts_received == 0){
	    return "networkOrange"
	  }else{
	    return "networkBlue"
	  }
	})
	.attr("r", function(d) {
	  if(d.posts_received == 0){
	    return "16";
	  }else{
	    return r_min;
	  }
	    });
	
    }else if(this.value == 'late'){
      
      node.selectAll('circle').attr("r",1);
      node.selectAll('circle').transition().duration(2500).delay(500).attr("class", function(d) {
	  if(d.posted_late == true){
	    return "networkOrange"
	  }else{
	    return "networkBlue"
	  }
	})
	.attr("r", function(d) {
	  if(d.posted_late == true){
	    return "16";
	  }else{
	    return r_min;
	  }
	    });
//    }else if(this.value == 'pcount'){
//      node.selectAll('circle').transition().duration(2500).delay(500).attr("class", function(d) {
//	  if(d.post_count >5){
//	    return "networkGreen"
//	  }else{
//	    return "networkBlue"
//	  }
//	})
//    }else if(this.value == 'likes'){
//      node.selectAll('circle').transition().duration(2500).delay(500).attr("class", function(d) { 
//	  if(d.liked == true){
//	    return "networkPink"
//	  }else{
//	    return "networkBlue"
//	  }
//	})
    }
  });    
       // Add an elaborate mouseover title for each chord.
    node.append("title").text(function(d) {
      var text_message = document.createElement("div");
      text_message.innerHTML = d.posted_message;
      var textLength = text_message.textContent.length;
      var hellip = "";
      if (textLength > 250) hellip = "...";
      var shorten_text = jQuery.trim(text_message.textContent).substring(0, 850)+ hellip;
      return "author: " +d.name + "\n"+"Date: "+ d.posted_on +"\n" + shorten_text;
    });

    function mouseover(d, i) {
      node.classed("fade", function(p) {
	return p.source.index != i
	    && p.target.index != i;
      });
    }
  
  function tick() {
    link.attr("x1", function(d) { return d.source.x; })
        .attr("y1", function(d) { return d.source.y; })
        .attr("x2", function(d) { return d.target.x; })
        .attr("y2", function(d) { return d.target.y; });


    node.attr("transform", function(d) { return "translate(" + Math.max(r, Math.min(width - r, d.x)) + ", " + Math.max(r, Math.min(height - r, d.y)) + ")"; })

  }

  function dblclick(d) {
    d3.select(this).classed("fixed", d.fixed = false);
  }

  function dragstart(d) {
    d3.select(this).classed("fixed", d.fixed = true);
  }

}
//////////////////////////////////
//	  Make Chord of SNA     //
//////////////////////////////////
//////////////////////////////////
var makeChordMatrix = function(data){
  var snaData = setChord(data);
  snaData.users = snaData.users.filter(function(){return true;});
   chartSet = 1;

    d3.select("svg")
       .remove();

  //set the colors
  var fill = d3.scale.category20();
  //var fill = d3.scale.category20b();
  //var fill = d3.scale.category20c();
  
  var width = 650,
      height = 500,
      outerRadius = Math.min(width, height) / 2 - 10,
      innerRadius = outerRadius - 24;
  
  var formatPercent = d3.format(".1%");
  
  var arc = d3.svg.arc()
      .innerRadius(innerRadius)
      .outerRadius(outerRadius);
  
  var layout = d3.layout.chord()
      .padding(.04)
      .sortSubgroups(d3.descending)
      .sortChords(d3.ascending);
  
  var path = d3.svg.chord()
      .radius(innerRadius);
  
  var svg = d3.select("#chord").append("svg")
      .attr("width", width)
      .attr("height", height)
      .append("g")
      .attr("id", "circle")
      .attr("transform", "translate(" + width / 2 + "," + height / 2 + ")")
      .attr("xmlns", "http://www.w3.org/2000/svg");
  
  svg.append("circle")
      .attr("r", outerRadius);
  
  //d3.csv("cities.csv", function(cities) {
  //d3.json("matrix.json", function(matrix) {
  var users = snaData.users;
  var matrix = snaData.matrix;
    // Compute the chord layout.
    layout.matrix(matrix);

    // Add a group per neighborhood.
    var group = svg.selectAll(".group")
      .data(layout.groups)
      .enter().append("g")
      .attr("class", "group")
      .on("mouseover", mouseover);

    // Add a mouseover title.
    group.append("title").text(function(d, i) {
      return users[i].name+ ": " + Math.round(d.value) + " total posts";
    });
    

    // Add the group arc.
    var groupPath = group.append("path")
	.attr("id", function(d, i) { return "group" + i; })
	.attr("d", arc)
	.style("fill", function(d) { return fill(d.index); });

    // Add a text label.
    var nameDisplay = {
      fullName: d3.text(function(d, i) { return users[i].name; }),
      initals: d3.text(function(d, i) { return users[i].initials; }),
      none: d3.text(function(d, i) { return ""; })
    };
    
    var groupText = group.append("text")
	.attr("class", "chordUser")
	.attr("x", 6)
	.attr("y", ".35em");

    groupText.append("textPath")
	.attr("xlink:href",  function(d, i) { return "#group" + i; })
	//.text(function(d, i) { return users[i].name; })
	.text(function(d, i) { return users[i].initials; });

//Attempt to overwrite the display name with initials if name is too long. Unsure how to get this to happen at the moment.
//not functional code----
	//groupText.append("textPath")
	//  .filter(function(d, i) {groupText.filter(function(d, i) { return groupPath[0][i].getTotalLength() / 2 - 16 < this.getComputedTextLength(); })
	//    .text(function(d, i) { return users[i].initials; });
//----not functional code

    // Remove the labels that don't fit. 
    groupText.filter(function(d, i) { return groupPath[0][i].getTotalLength() / 2 - 16 < this.getComputedTextLength(); })
	.remove();

    // Add the chords.
    var chord = svg.selectAll(".chord")
      .data(layout.chords)
      .enter().append("path")
      .attr("class", "chord")
      .style("fill", function(d) { return fill(d.source.index); })
      .attr("d", path);

    // Add an elaborate mouseover title for each chord.
    chord.append("title").text(function(d) {
      return users[d.source.index].name + " -> " + users[d.target.index].name + ": " + d.source.value + " posts sent : " + formatPercent(d.source.value/users[d.source.index].post_count )
	 + "\n" + users[d.target.index].name + " -> " + users[d.source.index].name + ": " + d.target.value + " posts sent : " + formatPercent(d.target.value/users[d.target.index].post_count );
    });

    function mouseover(d, i) {
      chord.classed("fade", function(p) {
	return p.source.index != i
	    && p.target.index != i;
      });
    }
    //});
  //});

}




//////////////////////////////////
//	 Make Post Timeline  1   //
//////////////////////////////////
//////////////////////////////////
//http://bl.ocks.org/mbostock/1667367
var makeTimeline1 = function(data){
    //console.log(data);
    data = setTimeline(data);
    //console.log(data);

 var margin = {top: 10, right: 10, bottom: 100, left: 40},
    margin2 = {top: 430, right: 10, bottom: 20, left: 40},
    width = 960 - margin.left - margin.right,
    height = 500 - margin.top - margin.bottom,
    height2 = 500 - margin2.top - margin2.bottom;

var parseDate = d3.time.format("%Y-%m-%d %X").parse;

var x = d3.time.scale().range([0, width]),
    x2 = d3.time.scale().range([0, width]),
    y = d3.scale.linear().range([height, 0]),
    y2 = d3.scale.linear().range([height2, 0]);

var xAxis = d3.svg.axis().scale(x).orient("bottom"),
    xAxis2 = d3.svg.axis().scale(x2).orient("bottom"),
    yAxis = d3.svg.axis().scale(y).orient("left");

var brush = d3.svg.brush()
    .x(x2)
    .on("brush", brushed);

var area = d3.svg.area()
    .interpolate("monotone")
    .x(function(d) { return x(d.post_datetime); })
    .y0(height)
    .y1(function(d) { return y(d.count); });

var area2 = d3.svg.area()
    .interpolate("monotone")
    .x(function(d) { return x2(d.post_datetime); })
    .y0(height2)
    .y1(function(d) { return y2(d.count); });

  d3.select("svg")
    .remove();
var svg = d3.select("#timeArea").append("svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom);

svg.append("defs").append("clipPath")
    .attr("id", "clip")
    .append("rect")
    .attr("width", width)
    .attr("height", height);

var focus = svg.append("g")
    .attr("class", "focus")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

var context = svg.append("g")
    .attr("class", "context")
    .attr("transform", "translate(" + margin2.left + "," + margin2.top + ")");

//d3.csv("https://secure.bluehost.com/~mclewisc/lti/threads/test/data/sp500.csv", type, function(error, data) {
  x.domain(d3.extent(data.map(function(d) { return d.post_datetime; })));
  y.domain([0, d3.max(data.map(function(d) { return d.count; }))]);
  x2.domain(x.domain());
  y2.domain(y.domain());

  focus.append("path")
      .datum(data)
      .attr("class", "area")
      .attr("d", area);

  focus.append("g")
      .attr("class", "x axis")
      .attr("transform", "translate(0," + height + ")")
      .call(xAxis);

  focus.append("g")
      .attr("class", "y axis")
      .call(yAxis);

  context.append("path")
      .datum(data)
      .attr("class", "area")
      .attr("d", area2);

  context.append("g")
      .attr("class", "x axis")
      .attr("transform", "translate(0," + height2 + ")")
      .call(xAxis2);

  context.append("g")
      .attr("class", "x brush")
      .call(brush)
      .selectAll("rect")
      .attr("y", -6)
      .attr("height", height2 + 7);
//});

function brushed() {
  x.domain(brush.empty() ? x2.domain() : brush.extent());
  focus.select(".area").attr("d", area);
  focus.select(".x.axis").call(xAxis);
}

function type(d) {
  d.post_datetime = parseDate(d.post_datetime);
  d.count = +d.count;
  return d;
}

  //
  //d3.select("#num_participants").text("Total Participants: " + data.totalParticipants);
  //d3.select("#num_posts").text("Total Posts: " + data.totalPosts);
  //d3.select("#first_post").text("First Post: " + data.date[0].post_date);
  //d3.select("#last_post").text("Last Posts: " +data.date[data.date.length -1].post_date);
}


//////////////////////////////////
//	 Make Post Timeline 2     //
//////////////////////////////////
//////////////////////////////////
//http://bl.ocks.org/mbostock/5977197
var makeTimeline2 = function(data){
  fullData=data;
  data = setDateline(data);
  //console.log(data);

var margin = {top: 20, right: 20, bottom: 30, left: 40},
    width = 460 - margin.left - margin.right,
    height = 100 - margin.top - margin.bottom;

var x = d3.scale.ordinal()
    .rangeRoundBands([0, width], 1, 1);

var y = d3.scale.linear()
    .range([height, 0]);

var xAxis = d3.svg.axis()
    .scale(x)
    .orient("bottom");

var yAxis = d3.svg.axis()
    .scale(y)
    .orient("left")
    .ticks(5, " ");
    
  d3.select("svg")
    .remove();
    
var svg = d3.select("#timeBar").append("svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
    .append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

//d3.tsv("data.tsv", type, function(error, data) {
  x.domain(data.map(function(d) { return d.post_date; }));
  y.domain([0, d3.max(data, function(d) { return d.count; })]);

  svg.append("g")
      .attr("class", "x axis")
      .attr("transform", "translate(0," + height + ")")
      .call(xAxis);

  svg.append("g")
      .attr("class", "y axis")
      .call(yAxis)
    .append("text")
      .attr("transform", "rotate(-90)")
      .attr("y", 6)
      .attr("dy", ".71em")
      .style("text-anchor", "end")
      .text("Count");

  svg.selectAll(".bar")
      .data(data)
    .enter().append("rect")
      .attr("class", "bar")
      .attr("x", function(d) { return x(d.post_date); })
      //.attr("width", x.rangeBand())
      .attr("width", 10)
      .attr("y", function(d) { return y(d.count); })
      .attr("height", function(d) { return height - y(d.count); });

//});

function type(d) {
  d.count = +d.count;
  return d;
}

  //d3.select("#num_participants").text("Total Participants: " + fullData.totals.total_participants);
  //d3.select("#num_posts").html("<br>Total Posts: " + fullData.totals.total_posts);
  //d3.select("#first_post").text("<br>First Post: " + timelineData[0].post_date);
  //d3.select("#last_post").text("<br>Last Posts: " + timelineData[timelineData.length -1].post_date);
}




 //////////////////////////////////
//	   Make Time Stream      //
//////////////////////////////////
//////////////////////////////////
//http://bl.ocks.org/mbostock/4254963
function makeSream(data){ 

 var width = 960,
    height = 500;
  
  var m = 5, // number of series
      n = 90; // number of values
  
  // Generate random data into five arrays.
  data = d3.range(m).map(function() {
    return d3.range(n).map(function() {
      return Math.random() * 100 | 0;
    });
  });
  
  var x = d3.scale.linear()
      .domain([0, n - 1])
      .range([0, width]);
  
  var y = d3.scale.ordinal()
      .domain(d3.range(m))
      .rangePoints([0, height], 1);
  
  var color = d3.scale.ordinal()
      .range(["#98abc5", "#8a89a6", "#7b6888", "#6b486b", "#a05d56"]);
  
  var area = d3.svg.area()
      .interpolate("basis")
      .x(function(d, i) { return x(i); })
      .y0(function(d) { return -d / 2; })
      .y1(function(d) { return d / 2; });
  
  var svg = d3.select("#stream").append("svg")
      .attr("width", width)
      .attr("height", height);
  
  svg.selectAll("path")
      .data(data)
      .enter().append("path")
      .attr("transform", function(d, i) { return "translate(0," + y(i) + ")"; })
      .style("fill", function(d, i) { return color(i); })
      .attr("d", area);
   
}

 //////////////////////////////////
//     Make Adjacency Matrix     //
//////////////////////////////////
////////////////////////////////// 
//http://bost.ocks.org/mike/miserables/
var makeAdjMatrix = function(data){
  chartSet = 1;
  var snaData = setNodeLinks(data);
  console.log(snaData);
    d3.select("svg")
       .remove();
       
  var margin = {top: 100, right: 100, bottom: 10, left: 100},
      width = 225,
      height = 225;
  
  var x = d3.scale.ordinal().rangeBands([0, width]),
      z = d3.scale.linear().domain([0, 4]).clamp(true),
      c = d3.scale.category10().domain(d3.range(10));
  
  var svg = d3.select("#matrixHeatmap").append("svg")
      .attr("width", width + margin.left + margin.right)
      .attr("height", height + margin.top + margin.bottom)
      .style("margin-left", margin.left + "px")
      .append("g")
      .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

//d3.json("data/lesMis.json", function(snaData) {
  var matrix = [],
      nodes = snaData.nodes,
      n = nodes.length;

  // Compute index per node.
  nodes.forEach(function(node, i) {
    node.index = i;
    node.count = 0;
    matrix[i] = d3.range(n).map(function(j) { return {x: j, y: i, z: 0}; });
  });

  // Convert links to matrix; count character occurrences.
  snaData.links.forEach(function(link) {
    matrix[link.source][link.target].z += link.value;
    matrix[link.target][link.source].z += link.value;
    matrix[link.source][link.source].z += link.value;
    matrix[link.target][link.target].z += link.value;
    nodes[link.source].count += link.value;
    nodes[link.target].count += link.value;
  });

  // Precompute the orders.
  var orders = {
    name: d3.range(n).sort(function(a, b) { return d3.ascending(nodes[a].name, nodes[b].name); }),
    count: d3.range(n).sort(function(a, b) { return nodes[b].count - nodes[a].count; }),
    post_count: d3.range(n).sort(function(a, b) { return nodes[b].post_count - nodes[a].post_count; }),
    posts_received: d3.range(n).sort(function(a, b) { return nodes[b].posts_received - nodes[a].posts_received; }),
    group: d3.range(n).sort(function(a, b) { return nodes[b].group - nodes[a].group; })
  };

  // The default sort order.
  x.domain(orders.name);

  svg.append("rect")
      .attr("class", "background")
      .attr("width", width)
      .attr("height", height);

  var row = svg.selectAll(".row")
      .data(matrix)
    .enter().append("g")
      .attr("class", "row")
      .attr("transform", function(d, i) { return "translate(0," + x(i) + ")"; })
      .each(row);

  row.append("line")
      .attr("x2", width);

  row.append("text")
      .attr("x", -6)
      .attr("y", x.rangeBand() / 2)
      .attr("dy", ".32em")
      .attr("text-anchor", "end")
      .text(function(d, i) { return nodes[i].name; });

  var column = svg.selectAll(".column")
      .data(matrix)
    .enter().append("g")
      .attr("class", "column")
      .attr("transform", function(d, i) { return "translate(" + x(i) + ")rotate(-90)"; });

  column.append("line")
      .attr("x1", -width);

  column.append("text")
      .attr("x", 6)
      .attr("y", x.rangeBand() / 2)
      .attr("dy", ".32em")
      .attr("text-anchor", "start")
      .text(function(d, i) { return nodes[i].name; });

  function row(row) {
    var cell = d3.select(this).selectAll(".cell")
        .data(row.filter(function(d) { return d.z; }))
        .enter().append("rect")
        .attr("class", "cell")
        .attr("x", function(d) { return x(d.x); })
        .attr("width", x.rangeBand())
        .attr("height", x.rangeBand())
        .style("fill-opacity", function(d) { return z(d.z); })
        .style("fill", function(d) { return nodes[d.x].group == nodes[d.y].group ? c(nodes[d.x].group) : null; })
        .on("mouseover", mouseover)
        .on("mouseout", mouseout);
  }

  function mouseover(p) {
    d3.selectAll(".row text").classed("active", function(d, i) { return i == p.y; });
    d3.selectAll(".column text").classed("active", function(d, i) { return i == p.x; });
  }

  function mouseout() {
    d3.selectAll("text").classed("active", false);
  }

  d3.select("#order").on("change", function() {
    clearTimeout(timeout);
    order(this.value);
  });

  function order(value) {
    x.domain(orders[value]);

    var t = svg.transition().duration(2500);

    t.selectAll(".row")
        .delay(function(d, i) { return x(i) * 4; })
        .attr("transform", function(d, i) { return "translate(0," + x(i) + ")"; })
      .selectAll(".cell")
        .delay(function(d) { return x(d.x) * 4; })
        .attr("x", function(d) { return x(d.x); });

    t.selectAll(".column")
        .delay(function(d, i) { return x(i) * 4; })
        .attr("transform", function(d, i) { return "translate(" + x(i) + ")rotate(-90)"; });
  }

  var timeout = setTimeout(function() {
    order("count");
    d3.select("#order").property("selectedIndex", 1).node().focus();
  }, 5000);
//});  

}


 //////////////////////////////////
//     Make betweenness with jsnetworkx     //
//////////////////////////////////
var makeBetween = function(data){
  chartSet = 1;
  var snaData = setNodeLinks(data);
  console.log(snaData);
    d3.select("svg")
       .remove();
       
  height = height_original + (data['totals']['total_participants'] * 8);
   
   //var margin = {top: 100, right: 100, bottom: 10, left: 100}

  var x = d3.scale.ordinal().rangeBands([0, width]),
      z = d3.scale.linear().domain([0, 4]).clamp(true),
      c = d3.scale.category10().domain(d3.range(10));
  
  var svg = d3.select("#networkDirected").append("svg")
      .attr("width", width)
      .attr("height", height)
      //.style("margin-left", margin.left + "px")
      .append("g");
      //.attr("transform", "translate(" + margin.left + "," + margin.top + ")");
      
        //change node size on change of #nodeSize select	
  d3.select("#nodeSize").on("change", function() {
    if(this.value == 'sent'){
      makeForcedDirected(data);
    }else if(this.value == 'received'){
      makeForcedDirected(data);
    }else if(this.value == 'total'){
      makeForcedDirected(data);
    }else if(this.value == 'word'){
      makeForcedDirected(data);
    }else if(this.value == 'word_avg'){
      makeForcedDirected(data);
    }
  });
  createNetwork(snaData.links);
   
  function onlyUnique(value, index, self) {
    return self.indexOf(value) === index;
  }
  
  function createNetwork(edgelist) {
    var nodeHash = {};
    var edgeHash = {};
    var nodes = [];
    var edges = [];
  
    edgelist.forEach(function (edge) {
      if (!nodeHash[edge.source]) {
	nodeHash[edge.source] = {id: edge.source, label: edge.source};
	nodes.push(nodeHash[edge.source]);
      }
      if (!nodeHash[edge.target]) {
	nodeHash[edge.target] = {id: edge.target, label: edge.target};
	nodes.push(nodeHash[edge.target]);
      }
      //if (edge.wordCount == 5) {
	edges.push({id: nodeHash[edge.source].id + "-" + nodeHash[edge.target].id, source: nodeHash[edge.source], target: nodeHash[edge.target], weight: edge.wordCount});
      //}
    });
    
    console.log(edges);
    createForceNetwork(nodes, edges);
  }
  
  function createForceNetwork(nodes, edges) {
    //create a network from an edgelist
    var node_data = nodes.map(function (d) {return d.id});
    var edge_data = edges.map(function (d) {return [d.source.id, d.target.id]; });
    console.log(node_data);
    console.log(edge_data);
    //var G = new jsnx.cycleGraph();
    var G = new jsnx.Graph();
    G.addNodesFrom(node_data);
    G.addEdgesFrom(edge_data);
    
    var betweenness = jsnx.betweennessCentrality(G);
    console.log(betweenness);
    var eigenvector = jsnx.eigenvectorCentrality(G);
    console.log(eigenvector);
    var clustering = jsnx.clustering(G);
    console.log(clustering);
    //var shortestPath = jsnx.shortestPath(G, {source: 28, target: 31})
    
    //console.log(shortestPath);
    
    var evExtent = d3.extent(d3.values(eigenvector._numberValues));
    var bwExtent = d3.extent(d3.values(betweenness._numberValues));
    var clExtent = d3.extent(d3.values(clustering._stringValues));
    console.log(evExtent);
    var colorScale = d3.scale.linear().domain([0,1]).range(["#ffff99", "#ff6600"]);
    var sizeScale = d3.scale.linear().domain([0,1]).range([4,10]);
    function byEV() {
      sizeScale.domain(clExtent);
      colorScale.domain(evExtent);
      d3.selectAll("circle")
	.transition()
	.duration(1000)
	//.style("fill", function (d) {return colorScale(eigenvector._stringValues[d.id])})
	//.attr("r", function (d) {return sizeScale(eigenvector._stringValues[d.id])})
	.attr("class", "networkBlue")
	.attr("r", 10)
	.transition()
	.delay(2000)
	.each("end", function() {
	  byBW();
	})
    }
    
    function byBW() {
      sizeScale.domain(evExtent);
      colorScale.domain(bwExtent);
      d3.selectAll("circle")
	.transition()
	.duration(1000)
	//.style("fill", function (d) {return colorScale(betweenness._stringValues[d.id])})
	.attr("r", function (d) {return sizeScale(betweenness._stringValues[d.id])})
	.attr("class", "networkBlue")
	.transition()
	.delay(2000)
	.each("end", function() {
	  byCL();
	})
    
    }
    
    function byCL() {
      sizeScale.domain(bwExtent);
      colorScale.domain(clExtent);
      d3.selectAll("circle")
	.transition()
	.duration(1000)
	//.style("fill", function (d) {return colorScale(clustering._stringValues[d.id])})
	.attr("r", function (d) {return sizeScale(clustering._stringValues[d.id])})
	.attr("class", "networkBlue")
	.transition()
	.delay(2000)
	.each("end", function() {
	  byEV();
	})
    }    
	/////////////////////////////////////////////////////////////////////////////////////
    var force = d3.layout.force()
	.nodes(snaData.nodes)
	.links(snaData.links)
	.size([width, height])
	.charge(-500)
	.linkDistance(100)
	.on("tick", tick)
	.start();
  
    var drag = force.drag()
	.on("dragstart", dragstart);
    
    d3.select("svg")
	 .remove();
	 
    var svg = d3.select("#networkDirected").append("svg")
	.attr("width", width)
	.attr("height", height)
	.attr("xmlns", "http://www.w3.org/2000/svg");
    
      link = svg.selectAll(".link")
	  .data(snaData.links)
	  .enter().append("line")
	  .attr("class", "link");
  
      node = svg.selectAll(".node")
	.data(snaData.nodes)
	.enter().append("g")
	  .attr("class", "node")
	  .on("dblclick", dblclick)
	  .call(drag);
  
    
    //// create circles
      byEV();
	  
    //change node size on change of #nodeSize select	
    d3.select("#nodeSize").on("change", function() {
      if(this.value == 'sent'){
	node.selectAll('circle').transition().duration(2500).delay(500).attr('r', function(d){ return d.post_count *2;})
      }else if(this.value == 'received'){
	node.selectAll('circle').transition().duration(2500).delay(500).attr('r', function(d){ return d.posts_received *2;})
      }else if(this.value == 'total'){
	node.selectAll('circle').transition().duration(2500).delay(500).attr('r', function(d){ return (d.post_count + d.posts_received)*2;})
      }else if(this.value == 'word'){
	node.selectAll('circle').transition().duration(2500).delay(500).attr('r', function(d){ return quantileWords(d.word_count) *4;})
      }else if(this.value == 'word_avg'){
	node.selectAll('circle').transition().duration(2500).delay(500).attr('r', function(d){ return quantileWords(d.word_count_avg) *2;})
      }else if(this.value == 'betweenness'){
	makeBetween(data);
      }
    });
  
	
      //node.append("text")
      //  .attr("x", -6)
      //  .attr("y", ".32em")
      //  .attr("class", "shadow")
      //  .text(function(d) { return d.name; });
    
  
      node.append("text")
	.attr("x", -6)
	//.attr("y", ".31em")
	.attr("dy", "15")
	.style("font-size",10 + "px")
	.style("color","#000")
	.text(function(d) { return snaData['nodes'][d.id].name; });
    
    function tick() {
      link.attr("x1", function(d) { return d.source.x; })
	  .attr("y1", function(d) { return d.source.y; })
	  .attr("x2", function(d) { return d.target.x; })
	  .attr("y2", function(d) { return d.target.y; });
  
      node.attr("transform", function(d) { return "translate(" + Math.max(r, Math.min(width - r, d.x)) + ", " + Math.max(r, Math.min(height - r, d.y)) + ")"; })
    }
  
    function dblclick(d) {
      d3.select(this).classed("fixed", d.fixed = false);
    }
  
    function dragstart(d) {
      d3.select(this).classed("fixed", d.fixed = true);
    }
  }
}

////////////////////////////////// 
//http://bl.ocks.org/emeeks/9915de8989e2a5c34652
//var makeBetween = function(data){
//  chartSet = 1;
//  var snaData = setNodeLinks(data);
//  console.log(snaData);
//    d3.select("svg")
//       .remove();
//       
//  height = height_original + (data['totals']['total_participants'] * 8);
//   
//   //var margin = {top: 100, right: 100, bottom: 10, left: 100}
//
//  var x = d3.scale.ordinal().rangeBands([0, width]),
//      z = d3.scale.linear().domain([0, 4]).clamp(true),
//      c = d3.scale.category10().domain(d3.range(10));
//  
//  var svg = d3.select("#networkDirected").append("svg")
//      .attr("width", width)
//      .attr("height", height)
//      //.style("margin-left", margin.left + "px")
//      .append("g");
//      //.attr("transform", "translate(" + margin.left + "," + margin.top + ")");
//      
//        //change node size on change of #nodeSize select	
//  d3.select("#nodeSize").on("change", function() {
//    if(this.value == 'sent'){
//      makeForcedDirected(data);
//    }else if(this.value == 'received'){
//      makeForcedDirected(data);
//    }else if(this.value == 'total'){
//      makeForcedDirected(data);
//    }else if(this.value == 'word'){
//      makeForcedDirected(data);
//    }else if(this.value == 'word_avg'){
//      makeForcedDirected(data);
//    }
//  });
//  createNetwork(snaData.links);
//   
//  function onlyUnique(value, index, self) {
//    return self.indexOf(value) === index;
//  }
//  
//  function createNetwork(edgelist) {
//    var nodeHash = {};
//    var edgeHash = {};
//    var nodes = [];
//    var edges = [];
//  
//    edgelist.forEach(function (edge) {
//      if (!nodeHash[edge.source]) {
//	nodeHash[edge.source] = {id: edge.source, label: edge.source};
//	nodes.push(nodeHash[edge.source]);
//      }
//      if (!nodeHash[edge.target]) {
//	nodeHash[edge.target] = {id: edge.target, label: edge.target};
//	nodes.push(nodeHash[edge.target]);
//      }
//      //if (edge.wordCount == 5) {
//	edges.push({id: nodeHash[edge.source].id + "-" + nodeHash[edge.target].id, source: nodeHash[edge.source], target: nodeHash[edge.target], weight: edge.wordCount});
//      //}
//    });
//    
//    console.log(edges);
//    createForceNetwork(nodes, edges);
//  }
//  function createForceNetwork(nodes, edges) {
//  
//  //create a network from an edgelist
//  
//  var node_data = nodes.map(function (d) {return d.id});
//  var edge_data = edges.map(function (d) {return [d.source.id, d.target.id]; });
//  console.log(node_data);
//  console.log(edge_data);
//  //var G = new jsnx.cycleGraph();
//  var G = new jsnx.Graph();
//  G.addNodesFrom(node_data);
//  G.addEdgesFrom(edge_data);
//  
//  var betweenness = jsnx.betweennessCentrality(G);
//  console.log(betweenness);
//  var eigenvector = jsnx.eigenvectorCentrality(G);
//  console.log(eigenvector);
//  var clustering = jsnx.clustering(G);
//  console.log(clustering);
//  //var shortestPath = jsnx.shortestPath(G, {source: 28, target: 31})
//  
//  //console.log(shortestPath);
//  
//  //var evExtent = d3.extent(d3.values(eigenvector._stringValues));
//  var evExtent = d3.extent(d3.values(eigenvector._numberValues));
//  var bwExtent = d3.extent(d3.values(betweenness._stringValues));
//  var clExtent = d3.extent(d3.values(clustering._stringValues));
//  console.log(evExtent);
//  var colorScale = d3.scale.linear().domain([0,1]).range(["#ffff99", "#ff6600"]);
//  var sizeScale = d3.scale.linear().domain([0,1]).range([4,10]);
//  
//  function byEV() {
//    sizeScale.domain(clExtent);
//    colorScale.domain(evExtent);
//    d3.selectAll("circle")
//      .transition()
//      .duration(1000)
//      //.style("fill", function (d) {return colorScale(eigenvector._stringValues[d.id])})
//      //.attr("r", function (d) {return sizeScale(eigenvector._stringValues[d.id])})
//      .attr("class", "networkBlue")
//      .attr("r", 10)
//      .transition()
//      .delay(2000)
//      .each("end", function() {
//	byBW();
//      })
//  }
//  
//  function byBW() {
//    sizeScale.domain(evExtent);
//    colorScale.domain(bwExtent);
//    d3.selectAll("circle")
//      .transition()
//      .duration(1000)
//      //.style("fill", function (d) {return colorScale(betweenness._stringValues[d.id])})
//      .attr("r", function (d) {return sizeScale(betweenness._stringValues[d.id])})
//      .attr("class", "networkBlue")
//      .transition()
//      .delay(2000)
//      .each("end", function() {
//	byCL();
//      })
//  
//  }
//  
//  function byCL() {
//    sizeScale.domain(bwExtent);
//    colorScale.domain(clExtent);
//    d3.selectAll("circle")
//      .transition()
//      .duration(1000)
//      //.style("fill", function (d) {return colorScale(clustering._stringValues[d.id])})
//      .attr("r", function (d) {return sizeScale(clustering._stringValues[d.id])})
//      .attr("class", "networkBlue")
//      .transition()
//      .delay(2000)
//      .each("end", function() {
//	byEV();
//      })
//  
//  }
//  
//    var force = d3.layout.force().nodes(nodes).links(edges)
//    //.size([500,500])
//    //.charge(-300)
//    //.chargeDistance(100)
//    //.gravity(0.05)
//    //.on("tick", updateNetwork);
//      .size([width, height])
//      .charge(-100)
//      .linkDistance(25)
//    .on("tick", tick)
//    .start();
//  
//  //var drag = force.drag();
//  var drag = force.drag()
//      .on("dragstart", dragstart);
//      
//  function dblclick(d) {
//    d3.select(this).classed("fixed", d.fixed = false);
//  }
//
//  function dragstart(d) {
//    d3.select(this).classed("fixed", d.fixed = true);
//  }
//  
//    var edgeEnter = svg.selectAll("g.edge")
//    .data(edges, function (d) {return d.id})
//    .enter()
//    .append("g")
//    .attr("class", "link");
//    //.attr("class", "edge");
//  
//    edgeEnter
//    .append("line")
//    .style("stroke-width", function (d) {return d.border ? "3px" : "1px"})
//    .style("stroke", "black")
//    .style("pointer-events", "none");
//  
//    var nodeEnter = svg.selectAll("g.node")
//    .data(nodes, function (d) {return d.id})
//    .enter()
//    .append("g")
//    .attr("class", "node")
//    .on("dblclick", dblclick)
//    .call(drag);
//  
//    nodeEnter.append("circle")
//    .attr("r", 8)
//        .attr("class", "node")
//  //  .attr("class", "foreground")
//  //  .style("fill", function (d) {return colors(d.module)})
//    .style("stroke", "black")
//    .style("stroke-width", function (d) {return d.border ? "3px" : "1px"})
//  
//    nodeEnter.append("text")
//    //.style("text-anchor", "middle")
//    //.attr("y", 3)
//    //.style("stroke-width", "1px")
//    //.style("stroke-opacity", 0.75)
//    //.style("stroke", "white")
//    //.style("font-size", "8px")
//    .text(function (d) {return snaData['nodes'][d.id].name})
//    .attr("x", -6)
//      //.attr("y", ".31em")
//      .attr("dy", "15")
//      .style("font-size",10 + "px")
//      .style("color","#000")
//    .style("pointer-events", "none")
//  
//    //nodeEnter.append("text")
//    //.style("text-anchor", "middle")
//    //.attr("y", 3)
//    //.style("font-size", "8px")
//    //.text(function (d) {return snaData['nodes'][d.id].name})
//    //.style("pointer-events", "none")
//  
//    //force.start();
//  
//    byEV();
//  
////    function updateNetwork(e) {
////  
////      svg.selectAll("line")
////      .attr("x1", function (d) {return d.source.x})
////      .attr("y1", function (d) {return d.source.y})
////      .attr("x2", function (d) {return d.target.x})
////      .attr("y2", function (d) {return d.target.y});
////  
////      svg.selectAll("g.node")
////	.attr("transform", function (d) {return "translate(" + d.x + "," + d.y + ")"});
////  
////    }
//    function tick() {
////      svg.selectAll("line")
////	  .attr("x1", function(d) { return d.source.x; })
////	  .attr("y1", function(d) { return d.source.y; })
////	  .attr("x2", function(d) { return d.target.x; })
////	  .attr("y2", function(d) { return d.target.y; });
////  
////  
////      svg.selectAll("g.node")
////	.attr("transform", function(d) { return "translate(" + Math.max(r, Math.min(width - r, d.x)) + ", " + Math.max(r, Math.min(height - r, d.y)) + ")"; })
//	svg.selectAll("line")
//      .attr("x1", function (d) {return d.source.x})
//      .attr("y1", function (d) {return d.source.y})
//      .attr("x2", function (d) {return d.target.x})
//      .attr("y2", function (d) {return d.target.y});
//  
//      svg.selectAll("g.node")
//	.attr("transform", function (d) {return "translate(" + d.x + "," + d.y + ")"});
//  
//    }
//  }
//}


 //////////////////////////////////
//	     Make Cloud          //
//////////////////////////////////
////////////////////////////////// 
var makeCloud = function(data){
  var snaData = setWordFreq(data);
  console.log(snaData);
  var keywords = ["threadz"]
  
    d3.select("svg")
       .remove();
       
// //http://sheriframadan.com/2012/05/creating-a-word-cloud-with-php/
// //https://github.com/jasondavies/d3-cloud
  var fill = d3.scale.category20();
    d3.layout.cloud().size([1000, height])
	.words(snaData)
	.padding(2)
	.rotate(function(d) { return ~~(Math.random() * 2) * 90; })
	.font("Impact")
	.fontSize(function(d) { return d.frequency * 5; })
	.on("end", draw)
	.start();
    function draw(words) {
      d3.select("#wordCloud").append("svg")
	  .attr("width", 1000)
	  .attr("height", height)
	.append("g")
	  .attr("transform", "translate(100,100)")
	.selectAll("text")
	  .data(words)
	  .enter().append("text")
	  .style("font-size", function(d) { return d.size + "px"; })
	    .style("fill", function(d) { return (keywords.indexOf(d.text) > -1 ? "red" : "black"); })
	    .style("opacity", .75)
	    .attr("text-anchor", "middle")
	    .attr("transform", function(d) {
	      return "translate(" + [d.x, d.y] + ")rotate(" + d.rotate + ")";
	    })
	  .text(function(d) { return d.text; });
	  
    }
////http://www.niemanlab.org/2011/10/word-clouds-considered-harmful/




//// Word cloud layout by Jason Davies, http://www.jasondavies.com/word-cloud/
//// Algorithm due to Jonathan Feinberg, http://static.mrfeinberg.com/bv_ch03.pdf
//
//    
//    wordScale=d3.scale.linear().domain([0,100]).range([10,160]).clamp(true);
//    var keywords = ["layout", "zoom", "circle", "style", "append", "attr"]
//
//     d3.layout.cloud().size([800, 800])
//      .words(snaData)
//      .rotate(function(d) { return d.text.length > 5 ? 0 : 90; })
//      .fontSize(function(d) { return wordScale(d.frequency); })
//      .on("end", draw)
//      .start();
//      
//      function draw(words) {
//	d3.select("#wordCloud").append("svg")
//	  .attr("width", 800)
//	  .attr("height", 800)
//	  .append("g").attr("id", "wordCloudG")
//	    .attr("transform","translate(100,100)")
//	  .selectAll("text")
//	  .data(snaData)
//	  .enter().append("text")
//	  .style("font-size", function(d) { return d.size + "px"; })
//	    .style("fill", function(d) { return (keywords.indexOf(d.text) > -1 ? "red" : "black"); })
//	    .style("opacity", .75)
//	    .attr("text-anchor", "middle")
//	    .attr("transform", function(d) {
//	      return "translate(" + [d.x, d.y] + ")rotate(" + d.rotate + ")";
//	    })
//	  .text(function(d) { return d.text; });
//      }

}

 //////////////////////////////////
//	   Make Statistics      //
//////////////////////////////////
////////////////////////////////// 
var makeStatistics = function(data){
  var snaData = setNodeLinks(data);
  //console.log(snaData);
  var statsTable = data['totals'];
  d3.select("#tPart").text("Total Participants: " + statsTable.total_participants);
  d3.select("#tPost").text("Total Posts: " +statsTable.total_posts);
  d3.select("#tDeleted").text("Total Posts Deleted: " +statsTable.total_deleted);
  d3.select("#tThread").text("Total Threads: "+statsTable.total_threads);
  
  $('#userStats').html("");
  $('#userStats').append("<button id='userDownload' onclick='$(\"#userPosts\").tableToCSV()' title='Export Users Table'>Export Users Table</button>")
  var userTable = tabulate(snaData.nodes, ["name", "post_count", "word_count", "word_count_avg"], "userStats", "userPosts");
  
  //console.log(snaData.threads);
  var arrThreadStat = snaData.threads.filter(function(){return true;});
  $('#threadStats').html("");
  $('#threadStats').append("<button id='threadDownload' onclick='$(\"#threadPosts\").tableToCSV()' title='Export Threads Table'>Export Threads Table</button>")
  var threadTable = tabulate(arrThreadStat, ["thread", "posted_wordCount", "total_wordCount", "ratio"], "threadStats", "threadPosts");


  //tablesorter
  $('#userStats').tablesorter({
      theme: 'blue',
      widgets: ['zebra'],
      widgetOptions:{zebra:['even', 'odd']}
  });
  
  $(".statTable").tablesorter( {sortList: [[0,0]]} );

}


 //////////////////////////////////
//	   Make Data Set      //
//////////////////////////////////
////////////////////////////////// 
var makeDataSet = function(data){
  
  var snaOutput = setSNADownload(data);
  $('#snaRaw').html("");
  $('#snaRaw').append("<button id='snaDownlaod' onclick='$(\"#snaPosts\").tableToCSV()' title='Export SNA Data'>Export SNA Data</button>")
  var snaTable = tabulate(snaOutput, "", "snaRaw", "snaPosts");

  //tablesorter

  //$(".statTable").tablesorter( {sortList: [[0,0]]} );

}