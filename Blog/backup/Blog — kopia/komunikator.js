var last = -1;
var interval;

function checkboxClicked(e){
	if(e.target.checked){
		document.getElementsByName('komunikaty')[0].style.backgroundColor='#fff';
		document.getElementsByName('imie')[0].style.backgroundColor='#fff';
		document.getElementsByName('dowyslania')[0].style.backgroundColor='#fff';
			
		document.getElementsByName('btn')[0].disabled = false;
		document.getElementsByName('imie')[0].disabled = false;
		document.getElementsByName('dowyslania')[0].disabled = false;
		
		start();
	}
	else{
		document.getElementsByName('komunikaty')[0].style.backgroundColor='#ccc';
		document.getElementsByName('imie')[0].style.backgroundColor='#ccc';
		document.getElementsByName('dowyslania')[0].style.backgroundColor='#ccc';
		
		document.getElementsByName('btn')[0].disabled = true;
		document.getElementsByName('imie')[0].disabled = true;
		document.getElementsByName('dowyslania')[0].disabled = true;
		
		mstop();
	}
}
	
var xmlHttp = stworzObiektXMLHttp(); 
function stworzObiektXMLHttp(){
	var xmlHttp;
	if(window.ActiveXObject){
		try{
			xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
		catch (e){
			xmlHttp = false;
		}
	}else{
		try{
			xmlHttp = new XMLHttpRequest();
		}
		catch (e){
			xmlHttp = false;
		}
	}

	if (!xmlHttp)
		alert("Błąd podczas tworzenia obiektu XMLHttpRequest.");
	else
		return xmlHttp;
}

function start(){
	xmlHttp = stworzObiektXMLHttp();
	xmlHttp.onreadystatechange = obsluzOdpowiedzSerwera;
	xmlHttp.open("GET", "komunikator.php?last="+last+"&blog="+getURLParam('nazwa'), true);
	xmlHttp.send();
	//clearTimeout(interval);
	//interval = setTimeout('start()', 1000);
}
//interval = window.setTimeout("start();",1000);
//

function mstop(){
	xmlHttp.onreadystatechange = null;
	xmlHttp.abort();
	//clearTimeout(interval);
}

function obsluzOdpowiedzSerwera(){
	if (xmlHttp.readyState == 3 && xmlHttp.status == 200){
		//alert(xmlHttp.responseXML.getElementsByTagName('last')[0].firstChild.nodeValue);
		var txt = '<response>';
		txt=txt+xmlHttp.responseText;
		txt=txt+'</response>';
		//var tekst = xmlHttp.responseXML.getElementsByTagName("response");
		//alert(all);
		if (window.DOMParser){
			parser=new DOMParser();
			xmlDoc=parser.parseFromString(txt,"text/xml");
		}else // Internet Explorer
		{
			xmlDoc=new ActiveXObject("Microsoft.XMLDOM");
			xmlDoc.async=false;
			xmlDoc.loadXML(txt);
		} 
		//alert(xmlDoc.getElementsByTagName('last').length);
		var len = xmlDoc.getElementsByTagName('last').length;
		var dlen = xmlDoc.getElementsByTagName('desc').length;
		
		if(len>0){
			var nlast = xmlDoc.getElementsByTagName('last')[len-1].firstChild.nodeValue;
			if(nlast>0 &&dlen>0){
				var ndesc = xmlDoc.getElementsByTagName('desc')[dlen-1].firstChild.nodeValue;
				last = nlast;
				document.getElementsByName('komunikaty')[0].value = '' + ndesc + '';
			}
		}
	}
	if (xmlHttp.readyState == 4 ){
		xmlHttp.open("GET", "komunikator.php?last="+last+"&blog="+getURLParam('nazwa'), true);
		xmlHttp.send();
	}
	
	
	//interval = setTimeout('start()', 1000);
}

function getURLParam(strParamName){
  var strReturn = "";
  var strHref = window.location.href;
  if ( strHref.indexOf("?") > -1 ){
    var strQueryString = strHref.substr(strHref.indexOf("?")).toLowerCase();
    var aQueryString = strQueryString.split("&");
    for ( var iParam = 0; iParam < aQueryString.length; iParam++ ){
      if (aQueryString[iParam].indexOf(strParamName + "=") > -1 ){
        var aParam = aQueryString[iParam].split("=");
        strReturn = aParam[1];
        break;
      }
    }
  }
  return strReturn;
}


function imieReset(){
	document.getElementsByName('imie')[0].style.backgroundColor='#fff';
}

function dowyslaniaReset(){
	document.getElementsByName('dowyslania')[0].style.backgroundColor='#fff';
}
function btnClicked(e){
	var name = encodeURIComponent(document.getElementsByName('imie')[0].value);
	var msg = encodeURIComponent(document.getElementsByName('dowyslania')[0].value);
	if(name == ''){
		document.getElementsByName('imie')[0].style.backgroundColor='#fcc';
		setTimeout('imieReset()', 200);
		return;
	}
	if(msg == ''){
		document.getElementsByName('dowyslania')[0].style.backgroundColor='#fcc';
		setTimeout('dowyslaniaReset()', 200);
		return;
	}
	var xmlHttp2 = stworzObiektXMLHttp();
	xmlHttp2.open("GET", "komunikator_send.php?name="+name+"&msg="+msg+"&blog="+getURLParam('nazwa'), true);
	//xmlHttp.onreadystatechange = obsluzOdpowiedzSerwera;//!!!!!!!!!!!!
	xmlHttp2.send(null);
	//mstop();
}