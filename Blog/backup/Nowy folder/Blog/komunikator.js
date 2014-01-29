var last = -1;
var interval;

var xmlHttp = new XMLHttpRequest();

function start(){
	xmlHttp = new XMLHttpRequest();
	xmlHttp.onreadystatechange = handleServerResponse;
	xmlHttp.open("GET", "komunikator.php?last="+last+"&blog="+getURLParam('nazwa'), true);
	xmlHttp.send();
}

function mstop(){
	xmlHttp.onreadystatechange = null;
	xmlHttp.abort();
}

function handleServerResponse(){
	if (xmlHttp.readyState == 3 && xmlHttp.status == 200){

		var txt = '<response>';			// znacznik korzenia XML
		txt=txt+xmlHttp.responseText;	// odpowiedź serwera
		txt=txt+'</response>';

		parser=new DOMParser();
		xmlDoc=parser.parseFromString(txt,"text/xml");	//stworzenie dokumentu xml <last> - ostatni wpis(nazwa pliku), <desc> - treści wpisów
		
		var len = xmlDoc.getElementsByTagName('last').length;//liczba elementów <last>
		var dlen = xmlDoc.getElementsByTagName('desc').length;//liczba elementów <desc>
		
		if(len>0){
			var nlast = xmlDoc.getElementsByTagName('last')[len-1].firstChild.nodeValue;// pobierz parametr <last> ostatniej odpowiedzi 
																									//serwera
			
			if(nlast>0 &&dlen>0){
				var ndesc = xmlDoc.getElementsByTagName('desc')[dlen-1].firstChild.nodeValue; // pobierz <desc> ostatniej odp. serwera
				last = nlast;
				document.getElementsByName('output_box')[0].value = '' + ndesc + ''; // aktualizuj okno komunikatora
			}
		}		
	}

	if (xmlHttp.readyState == 4 ){
		xmlHttp.open("GET", "komunikator.php?last="+len+"&blog="+getURLParam('nazwa'), true);
		xmlHttp.send();
	}

}

function getURLParam(strParamName){ // wyszukuje wartosc parametru z URI
	var strReturn = "";
	var strHref = window.location.href; //pobranie URI
	
	if ( strHref.indexOf("?") > -1 ){	// jeśli wysłano nazwę bloga GET-em
		var allParamStringArray = strHref.substr(strHref.indexOf("?")); // pozostawienie samych przesłanych parametrów
		var paramStringArray = allParamStringArray.split("&");	// tablica parametrów
		
		for ( var index = 0; index < paramStringArray.length; index++ )	{
			if (paramStringArray[index].indexOf(strParamName + "=") > -1 ){
				var param = paramStringArray[index].split("=");	// szukane parametry
				strReturn = param[1];
				break;
			}
		}
	}
 	return strReturn;
}

function buttonClicked(e){
	var name = encodeURIComponent(document.getElementsByName('nickname')[0].value); // zamiana spacji i znaków specjalnych na
	var msg  = encodeURIComponent(document.getElementsByName('input_box')[0].value); // znaczniki do adresu URI
	
	if(name == ''){
		alert("Przed wysłaniem uzupełnij pole: Imię");
		return;
	}
	if(msg == ''){
		alert("Przed wysłaniem uzupełnij pole: Treść");
		return;
	}
	var xmlHttp2 = new XMLHttpRequest();
	xmlHttp2.open("GET", "komunikator_send.php?name="+name+"&msg="+msg+"&blog="+getURLParam('nazwa'), true);

	xmlHttp2.send(null);
}

function checkboxClicked(e){
	if(e.target.checked){
		document.getElementsByName('output_box')[0].style.backgroundColor='#fff';
		document.getElementsByName('nickname')[0].style.backgroundColor='#fff';
		document.getElementsByName('input_box')[0].style.backgroundColor='#fff';
			
		document.getElementsByName('button')[0].disabled = false;
		document.getElementsByName('nickname')[0].disabled = false;
		document.getElementsByName('input_box')[0].disabled = false;
		
		start();
	}
	else{
		document.getElementsByName('output_box')[0].style.backgroundColor='#ccc';
		document.getElementsByName('nickname')[0].style.backgroundColor='#ccc';
		document.getElementsByName('input_box')[0].style.backgroundColor='#ccc';
		
		document.getElementsByName('button')[0].disabled = true;
		document.getElementsByName('nickname')[0].disabled = true;
		document.getElementsByName('input_box')[0].disabled = true;
		
		mstop();
	}
}