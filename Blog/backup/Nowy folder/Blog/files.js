function newFileFormButton() { 
	var container = document.getElementById("file_list"); 
	var input_counter = container.getElementsByTagName("input").length + 1; 
	var elem = document.createElement("input"); 
	
	elem.type = "file"; 
	elem.name = "file_" + input_counter;
	elem.id   = "file_" + input_counter;
	elem.className = "flie_list_elem";
	
	container.appendChild(elem);
}
