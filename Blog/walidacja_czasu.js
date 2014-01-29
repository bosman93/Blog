function dateChange(e){ 
	if(!/^(19|20)[0-9][0-9]-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/.test(e.target.value)) { 
		e.target.value=getCurrentDate(); 
	} 
} 
function timeChange(e){ 
	if(!/^([0-1][0-9]|2[0-3]):(0[1-9]|[1-5][0-9])$/.test(e.target.value)){ 
		e.target.value=getCurrentTime(); 
	} 
}

function getCurrentTime() { 
	var today = new Date();
	var HH = today.getHours();
	var MM = today.getMinutes();
	
	if(HH<10) 
		HH = "0"+HH; 
	if(MM<10) 
		MM = "0"+MM; 
	
	return HH+':'+MM; 
} 
   
function getCurrentDate() {
	var today = new Date(); 
	var year = today.getFullYear(); 
	var month = (today.getMonth()+1); 
	var day = today.getDate();
	 
	if(day < 10) 
		day = "0"+day;
	if(month < 10) 
		month = "0"+month; 
		
	return year + '-' + month + '-' + day; 
}

function setInitValues() { 
	document.forms[1].elements['date'].value = getCurrentDate();
	document.forms[1].elements['time'].value = getCurrentTime();
}
