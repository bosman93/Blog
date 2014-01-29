function getStyleList() {
	
	var allLinks = document.getElementsByTagName('link');
	
	result = new Array();
	
	for(i = 0; i < allLinks.length; ++i) {
		if(allLinks[i].type == "text/css"){
			result.push(allLinks[i]);	
		}
	}
	return result;
}

var list = getStyleList();

function addStyleSelector(list) {
	
	var f = document.createElement("form");
	
	var s = document.createElement("select"); //input element, text
	
	for(it = 0; it < list.length; ++it) {
		var opt = document.createElement('option');
		opt.innerHTML = list[it].title;				// podpis
		opt.setAttribute('value',list[it].title);
		opt.setAttribute('name', it);
		opt.setAttribute('onclick','disableAll(); setActive(event);');
		
		s.appendChild(opt);
	}
	f.appendChild(s);

	document.getElementById('style').appendChild(f);
}

function disableAll() {
	
	for(it = 0; it < list.length; ++it) {
		list[it].disabled = true;	
	}
}
function setActive(e) {
	
	list[e.target.getAttribute('name')].disabled = false;
	
	// ustalenie daty wygasniecia ciasteczka
	var d = new Date();
	d.setTime(d.getTime()+( 7   *24*60*60*1000)); // teraz ustawione na 7 dni
	var expires = d.toGMTString();
	
	document.cookie="currentStyleID="+e.target.getAttribute('name') + ";expires=" + expires; // zapisz ciasteczko
}




function styleInit() { // wybor stylu na podstawie ciasteczka
	
	var lastStyleID = getCookie();
	
	disableAll();
	if ((lastStyleID != "" )&& (typeof(list[lastStyleID]) != 'undefined')) //jesli nie jest puste
  	{
  		list[lastStyleID].disabled = false; // ustaw poprzedni styl jako aktywny
  	}
	else
  	{
		list[0].disabled = false; // ustaw pierwszy styl z listy gdy nie ma ciasteczka lub jest puste
  	}
}

function getCookie()
{
	var name = "currentStyleID="
	var ca = document.cookie.split(';'); // pobranie ciasteczek i oddzielenie ich od siebie 
	
	for(var i=0; i < ca.length; i++)
	{
		var c = ca[i].trim(); // wyrzucenie bialych znakow (tutaj raczej nie trzeba ale profilaktycznie...
		if (c.indexOf(name)==0){
			return c.substring(name.length,c.length); // wyciagniecie wartosci ID
		}
	}
	return "";
}
