// JavaScript Document

function start_autocomp(eid){
	// An XHR DataSource
	var myServer = "../helpers/autocomplete.php";
	var mySchema = ["Services", "service", "keyword", "link", "superscore", "matchid", "h", "w", "kid","sid","uid"];
	var myDataSource = new YAHOO.widget.DS_XHR(myServer, mySchema);
	
	
	// APPENDING to the QUERY (to send SERVICE to ACTION)
	//myXHRDataSource.scriptQueryAppend = "abc=123&def=456"; 
	// ...or configure the response type to be JSON (default)
	//myDataSource.responseType = YAHOO.widget.DS_XHR.TYPE_JSON;
	myDataSource.scriptQueryAppend = "action=server";
	
	// Match case sensitivity if true
	myDataSource.queryMatchCase = false;
	// Match results of query strings that are *subsets* of the current query string
	//myDataSource.queryMatchSubset = true;
	// Disable the cache
	myDataSource.maxCacheEntries = 60;

	
	var myinput_var = "myInput"+ eid;
	var myresult_var = "myResults"+ eid;
	var myAutoComp = new YAHOO.widget.AutoComplete(myinput_var,myresult_var, myDataSource);
	// Query Delay
	myAutoComp.queryDelay = 0.2; 
	// Container will expand and collapse vertically
	myAutoComp.animVert = true;
	// Container animation will take 2 seconds to complete
	myAutoComp.animSpeed = 0.2;
	myAutoComp.alwaysShowContainer = true;
	myAutoComp.minQueryLength = 1;
	
	var help = "<div class='feedback' id=\"intro\">things you can try:<br/><br/><b>games, blog, news, video, photo, chat, comics</b><br/><br/>check out our special filters:<br/><br/><b>!popular, !active, !new</b></div>";
	myAutoComp.setBody(help);
	document.getElementById('myResults'+eid).childNodes[0].style.display = "block";
			
	var ac_input = document.getElementById('myInput'+eid);
	ac_input.focus();
	// Aligns to input box
	myAutoComp.doBeforeExpandContainer = function(oTextbox, oContainer, sQuery, aResults) {
		var pos = YAHOO.util.Dom.getXY(oTextbox);
		pos[1] += YAHOO.util.Dom.get(oTextbox).offsetHeight;
		YAHOO.util.Dom.setXY(oContainer,pos);
		return true;
	};
	
	
	// This function returns markup that bolds the original query,
	// and also displays to additional pieces of supplemental data.
	myAutoComp.formatResult = function(aResultItem, sQuery) {

	   var s = aResultItem[0]; // the entire result key
	   var k = aResultItem[1];
	   var link = aResultItem[2];
	   var score = aResultItem[3];
	   var id = aResultItem[4];
	   
	   var h = aResultItem[5];
	   var w = aResultItem[6];
	   
	   var kid = aResultItem[7];
	   var sid = aResultItem[8];
	   var uid = aResultItem[9];
	
	   var sLoc = (s.toLowerCase()).search(sQuery.toLowerCase());
	   var kLoc = (k.toLowerCase()).search(sQuery.toLowerCase());
	   
	   if (sLoc != -1){
		   var sPre = s.substr(0,sLoc);
		   var sKey = s.substr(sLoc, sQuery.length);
		   var sSum = sLoc+sQuery.length;
		   var sRemainder = s.substr(sSum); // the rest of the result
		   
	   }else{
		   var sPre = s;
		   var sRemainder = "";
		   var sKey = "";  
	   }
	   
		
	   if (s == k){
		   	var kPre = "";
			var kRemainder = "";
			var kKey = "";

	   }else{
		   
		   if (kLoc != -1){
			   var kPre = k.substr(0,kLoc);
			   var kKey = k.substr(kLoc, sQuery.length);
			   var kSum = kLoc+sQuery.length;
			   var kRemainder = k.substr(kSum); // the rest of the result
			   

		   }else{
			   var kPre = k;
			   var kRemainder = "";
			   var kKey = "";
		   }
		   

	   }
	   
		var aMarkup = ["<div><div> ",
		  sPre,
		  "<span class='s_key'>", sKey,"</span>",
		  sRemainder,
		  "<span class='score'>",score,"</span>",
		  "</div>",
		  "<div><span class = 'k_result'> ",
		  kPre,
		  "<span class='k_key'>", kKey,"</span>",
		  kRemainder,
		  "</span> ", 
		  "</div></div>"
		  ];
		//var aMarkup = ["<div>", s,"</div>","<div>",k,"</div>"];
		
	  	return (aMarkup.join(""));
	};

	var dataRequest = function(oSelf , sQuery){
		if(sQuery.length!=0){
			myAutoComp.setBody("<div class='feedback' id=\"searching\">Searching...</div>");
		}else{
			myAutocomp.setBody(help);
		}
	}
 	myAutoComp.dataRequestEvent.subscribe(dataRequest);
	    // Show custom message if no results found
    var myOnDataReturn = function(sType, aArgs) {
        var myAutoComp = aArgs[0];
        var sQuery = aArgs[1];
        var aResults = aArgs[2];

        if(aResults.length == 0) {
				myAutoComp.setHeader("<div id='header'>No hay resultados</div>");
            myAutoComp.setBody("<div class='feedback' id=\"noresults\">Try shortening your query, our services are limited at the moment</div>");
            	document.getElementById('myResults'+eid).childNodes[0].style.display = "block";
        }else{
			myAutoComp.setHeader("<div id='header'>Best Matches</div>");
        }
    };
    myAutoComp.dataReturnEvent.subscribe(myOnDataReturn);
    
	function fnCallback(e, args) {
		var sel_s = args[2][0];
		var sel_k = args[2][1];
    	var sel_link = args[2][2];
    	var sel_score= args[2][3];
    	var sel_id = args[2][4];
		var sel_h = args[2][5];
		var sel_w = args[2][6];
		var sel_kid = args[2][7];
		var sel_sid = args[2][8];
		var sel_uid = args[2][9];
		
		var uid = document.Show.uid.value; 
		link = selectLink(eid, sel_link, sel_h, sel_w, sel_sid);
		if(link != null){
			document.getElementById('widget_'+eid).innerHTML  = link;
		}else{
			document.getElementById('widget_'+eid).innerHTML = "Widget ran away :(";
		}
		var params = "id=" + sel_id +"&kid=" + sel_kid +"&sid=" + sel_sid +"&uid="+sel_uid+"&userid="+uid;
		var url = "../helpers/usage.php";
		ajax(url, params);
		createIcon(sel_s, eid);
		var header = "<div><div class='hide' onClick='hidePanel("+eid+")'></div><div class='close' onClick='destroyPanel("+eid+")'></div></div><div class='service'>"+sel_s+"</div>";
		YAHOO.example.container.widget[eid].setHeader(header);
		YAHOO.example.container.widget[eid].setBody("");
		echolet = false;
		/*settings_icon = {
		  tl: { radius: 5 },
		  tr: { radius: 5 },
		  bl: { radius: 5 },
		  br: { radius: 5 },
		  antiAlias: true,
		  autoPad: true
		}
		var corners_icons = new curvyCorners(settings_icon, "icons");
		corners_icons.applyCornersToAll();*/


 	};
	myAutoComp.itemSelectEvent.subscribe(fnCallback);

}

function changeStyleById(hover, id){
	switch (hover){
		case 1:
			nodeObj = document.getElementById('icon_'+id);
   			nodeObj.style.color = '#373737';
			break;
		case 2:
			nodeObj = document.getElementById('icon_'+id);
   			nodeObj.style.color = '#737373';
			break;
	}
}

function selectLink(eid, link, h,w, sid){
	h = parseInt(h) + 30;
	w = parseInt(w) + 10;
	var debug = 0;
	var scriptpos = link.search(/<script/);
	var echoappspos = link.search(/echoapps/);
	
	if(echoappspos >=0){
		var brackpos = link.indexOf("/");
		var appname = link.substring(brackpos+1);
		var link_div = "<div id='"+appname+"'></div>";
		var source = "/"+link+".js";
		
		if(debug == 1){
			alert(appname);
			alert(source);
			alert(link_div);
		}

		var headID = document.getElementsByTagName("head")[0];         
		var newScript = document.createElement('script');
		newScript.type = 'text/javascript';
		newScript.src = source;
		headID.appendChild(newScript);		
		return link_div;
	}else if(scriptpos>=0){
		/*var start1 = scriptpos;
		var stop1 = link.search(/<\/script>/)+9;
		var link_script = link.substring(start1, stop1);
		var start2 = stop1;
		var link_div = link.substring(start2);
		var source = link_script.substring(link_script.indexOf('src="')+5, link_script.indexOf('">') );*/
		
		if(debug == 1){
			alert(link_script);
			alert(source);
			alert(link_div);
		}

		/*var nodeObj = document.getElementsById("echo_icons");         
		var newScript = document.createElement('div');
		newScript.class = 'icons';
		newScript.innerHTML = "blah";
		nodeObj.innerHTML = "yo";
		var newScript = document.createElement('script');
		newScript.type = 'text/javascript';
		newScript.src = source;
		nodeObj.appendChild(newScript);*/
		
		newlink = "<iframe src='/helpers/iframy.php?linkid="+sid+"' height='"+h+"' width='"+w+"' scrolling='no' marginwidth='0' marginheight='0' frameborder='0' vspace='0' hspace='0' ></iframe>";
		return newlink;	
	}else{
		return link;
	}

}




function genHex(){
colors = new Array(14);
colors[0]="0";
colors[1]="1";
colors[2]="2";
colors[3]="3";
colors[4]="4";
colors[5]="5";
colors[5]="6";
colors[6]="7";
colors[7]="8";
colors[8]="9";
colors[9]="a";
colors[10]="b";
colors[11]="c";
colors[12]="d";
colors[13]="e";
colors[14]="f";

digit = new Array(5);
color="";
for (i=0;i<6;i++){
	digit[i]=colors[Math.round(Math.random()*14)];
	color = color+digit[i];
	
}
return "111111";
// document.getElementById('hexn').innerText="#"+color
}

