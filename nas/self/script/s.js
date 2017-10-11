var publicHostels = [];
var publicRooms = [];
function matchList(idV){
	if(arguments.length > 1){ el1 = "schoolLGs"; el2 = "area"; }else{el1 = "LGs"; el2 = "lg";}
	vat = idV.options[idV.selectedIndex].value;
	fmange = document.getElementById(el2);
	later = fmange.innerHTML;
	document.getElementById(el1).disabled = "disbaled";
	fmange.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Fetching matched LGs';
	params = {m:vat};
	$.post('self/server/statesys.php',params,function(data){
			$("#"+el1).html(data);
			fmange.innerHTML = later;
			document.getElementById(el1).disabled = "";
			if(arguments.length > 1){ listHostel(document.getElementById("schoolLGs")) }
		});
}


function customMessage(){
	this.warning = function warning(message){
		return '<div style=\'padding:12px; opacity:.99\'><div style=\'color:#F48622; font-size:16px; background:#FBE9BD; border-left:#F48622 thick solid; width:50%; padding:15px; font-family:Helvetica Neue,Helvetica,Arial,sans-serif;\'><i class=\'fa fa-exclamation-circle\'></i> '+message+'</div></div>';
	}
	this.error = function(message){
		return '<div style=\'padding:12px;\'><div align=\'left\' style=\'color:#ED050B; opacity:.99;width:50%; font-size:15px; background:#F9B4B0; border-left:#ED050B thick solid; padding:15px; font-family:Helvetica Neue,Helvetica,Arial,sans-serif; \' ><i class=\'fa fa-warning\'></i> '+message+'</div></div>';
	}
	this.success = function(message){
		return '<div style=\'padding:12px;\'><div style=\'color:#2B8E11; width:50%; opacity:.99; font-size:16px; background:#BCF8AD; border-left:#2B8E11 thick solid; padding:15px; font-family:Helvetica Neue,Helvetica,Arial,sans-serif;\'><i class=\'fa fa-check-square-o\'></i> '+message+'</div></div>';
	}
}
function mCra(message){
	$(document).ready(function(){
		document.getElementById("stow").style.display = "block";
		$("#stow").html(message);
		clearMessage();
	});
}
delay = 5;
function clearMessage(){
	if(delay === 0){
		clearTimeout(timer);
		delay = 4;
		$("#stow").fadeOut(2000, function(){
			$("#stow").html('');
		});
	}
	else{
		delay--;
		timer = setTimeout("clearMessage()",1000);
	}
}
function listHostel(el){
	vat = el.options[el.selectedIndex].value;
	selEl = document.getElementById("hostelName");
	selEl.disabled = "disabled";
	former = $("#chooseHostel").html();
	$("#chooseHostel").html("<i class='fa fa-spinner fa-spin'></i> fetching hostels.");
	hostels = [];
	url = 'X/public/api/any/get/hostel/'+vat;
	data = {};
	getFunc(url, data, function(response){
		selEl.innerHTML = "";
		if(response.error.status == 1){
			$("#chooseHostel").html('Sorry, Could not fetch Hotels');
		}else{
			$("#chooseHostel").html("Choose hostel");
			x = ambigSorter(response.content.data);
			publicHostels = x[0];
			publicRooms = x[1];
			if(x[0].length < 1){
				
			}else{
				tH = x[0];
				//for(var pi = 0; pi < selEl.length; pi++){
				//}
				for(op =0; op < tH.length; op++){
					var option = document.createElement("option");
					option.value = tH[op].hostelID;
					option.text = tH[op].hostelName;
					selEl.appendChild(option);
				}
				populateRooms(0);
				selEl.disabled = "";
			}
			//while()
		}
	});
}
function populateRooms(hostel){
	console.log(publicHostels);
	console.log(publicRooms)
	hostel = hostel + 1;
	var Rooms = publicRooms[hostel];
	var counter = 0;
	var el = document.getElementById("Rooms");
	el.innerHTML = "";
	if(Rooms.length < 1){
		el.disabled = "disabled";
	}else{
		while(counter < Rooms.length){
			if(parseInt(Rooms[counter].occupants) < parseInt(Rooms[counter].bedSpaces)){
				var option = document.createElement("option");
				option.value = Rooms[counter].roomID;
				option.text = Rooms[counter].roomName+"-"+Rooms[counter].roomDetails;
				el.appendChild(option);
			}else{				
			}
			counter++;
		}
		findTotal();
	}
	//console.log(publicRooms);
}
function findTotal(){
	selRoom = document.getElementById("Rooms").selectedIndex;
	selHostel = document.getElementById("hostelName").selectedIndex;
	d = parseInt(document.getElementById("config").selectedIndex);
	try{
		room = publicRooms[selHostel+1];
		roomlocate = room[selRoom];
		console.log(roomlocate);
		pricePerSpace = roomlocate.bedSpacePrice;
		beds = roomlocate.bedSpaces;
		var total;
		if(d == 0){
			total = parseInt(beds)*pricePerSpace;
			document.getElementById("OptComment").disabled = true;
		}else if(d == 1){
			total = pricePerSpace
			document.getElementById("OptComment").disabled = false;
		}else if(d == 2){
			total = pricePerSpace;
			document.getElementById("OptComment").disabled = true;
		}
		console.log(pricer(total));
		document.getElementById("totalAmount").innerHTML = pricer(total);
		$("#amountDue").val(total);
	}catch(Exception){
		
	}
}
function strrev(s){
	s = s+"";
    return s.split("").reverse().join("");
}
function pricer(pri){
	r = strrev(pri);
	len = r.length;
	start = 0;
	nstr  = "";
	l = 0;
	iS = false;
	while (start < len){
		if(l == 2 && (start+1) < len){
			nstr = ","+r.substr(start,1)+nstr;
			iS = true;
		}else{
			nstr = r.substr(start,1)+nstr;
		}
		start++;
		if (!(iS)){
			l++;
		}else{
			l = 0;
			iS = false;
		}
	}
return "&#8358;"+nstr;
}
function ambigSorter(data){
	sorted = [];
	hostels = [];
	hostelsObj = [];
	roomsObj = [];
	tempRooms = [];
	i = 0;
	while(i < data.length){
		if(findInArray(data[i].hostelID,hostels)){
			roomObj = {bedSpacePrice:data[i].bedSpacePrice, bedSpaces:data[i].bedSpaces, occupants:data[i].occupants, roomDetails:data[i].roomDetails, roomName:data[i].roomName, roomNumber:data[i].roomNumber, roomID:data[i].roomID};
			tempRooms.push(roomObj);
		}else{
			roomsObj.push(tempRooms);
			// Create the new hostelObj 
			hostelObj = {hostelID:data[i].hostelID, address:data[i].address, hostelName:data[i].hostelName, hostelState: data[i].hosteState};
			// Push the obj
			hostelsObj.push(hostelObj);
			//Initialize the rooms array
			tempRooms = [];
			roomObj = {bedSpacePrice:data[i].bedSpacePrice, bedSpaces:data[i].bedSpaces, occupants:data[i].occupants, roomDetails:data[i].roomDetails, roomName:data[i].roomName, roomNumber:data[i].roomNumber, roomID:data[i].roomID};
			//push
			tempRooms.push(roomObj);
			//update rooms array
			hostels.push(data[i].hostelID);
		}
		i++;
	}
	roomsObj.push(tempRooms);
	return [hostelsObj, roomsObj];
}
function findInArray(needle, arrayStack){
	found = false;
	for(counter=0; counter < arrayStack.length; counter++){
		if(needle === arrayStack[counter]){
			found = true;
			break;
		}
	}
	return found;
}
function postFunc(url, datum, callback){
	$.ajax({
		url: url,    //Your api url
		type: 'POST', //type is any HTTP method
		data: datum,      //Data as js object
		contentType: 'application/x-www-form-urlencoded',
		dataType: "json",
		success: function (response) {
			callback(response);
		}
	});
}
function putFunc(url, datum, callback){
	$.ajax({
		url: url,    //Your api url
		type: 'PUT', //type is any HTTP method
		data: datum,      //Data as js object
		contentType: 'application/x-www-form-urlencoded',
		dataType: "json",
		success: function (response) {
			callback(response);
		}
	});
}
function getFunc(url, datum, callback){
	$.ajax({
		url: url,    //Your api url
		type: 'GET', //type is any HTTP method
		data: datum,      //Data as js object
		contentType: 'application/x-www-form-urlencoded',
		dataType: "json",
		success: function (response) {
			callback(response);
		},
		error: function(err){
			callback(err);
		}
	});
}
function preview(){
	//try{
		$("#hosteln").html("Hostel Name: "+$("#hostelName option:selected").text());
		$("#hostela").html($("#schoolLGs option:selected").text()+", "+$("#hostelState option:selected").text()+" State");
		var k = $("#Rooms option:selected").text();
		k = k.split('-');
		$("#roomn").html(k[0]);
		$("#roomt").html(k[1]);
		$("#conf").html($("#config option:selected").text());
		$("#pr").html($("#totalAmount").html());
		$("#fns").html($("#firstname").val()+" "+$("#lastname").val());
		$("#sOg").html($("#gender option:selected").text());
		$("#bd").html($("#birthday").val());
		$("#pn").html($("#phone-no").val());
		$("#loc").html($("#LGs option:selected").text()+", "+$("#state option:selected").text());
		$("#cus").html($("#course").val());
		$("#pgT").html($("#progType option:selected").text());
		$("#inst").html($("#institution option:selected").text());
		$("#cdur").html($("#course-duration").val());
		$("#mtr").html($("#matric-number").val());
		$("#cy").html($("#level").val());
	/*}catch(e){
	}*/
	setTimeout(preview,1000);
}
function validate(id){
	document.getElementById("step"+id).click();
}
$(document).ready(function(){
		preview();
		fetchInvite();
	});
function fetchInvite(){
	try{
		custom = new customMessage();
		code = document.getElementById("inviteCode").value;
		datum = {};
		url = 'X/public/api/user/get/inviteDetails/'+code;
		getFunc(url,datum, function(response){
			if(arguments.length == 1){
				if(response.content.data.length > 0){
					mCra(custom.success("Your referer data has been succesfully loaded"));
					//console.log(response.content.data);
					var st = document.getElementById("hostelState");
					var hlg = document.getElementById("schoolLGs");
					var hna = document.getElementById("hostelName");
					var roo = document.getElementById("Rooms");
					var co = document.getElementById("config");
					var ema = document.getElementById("emal")
					var r = response.content.data;
					st.innerHTML = "<option value='"+r[0].hostelState+"'>"+r[0].state+"</option>";
					st.disabled = true;
					hlg.innerHTML = "<option value='"+r[0].hostelLG+"'>"+r[0].lg+"</option>";
					hlg.disabled = true;
					hna.innerHTML = "<option value='"+r[0].hid+"'>"+r[0].hostelName+"</option>";
					hna.disabled = true;
					roo.innerHTML = "<option value='"+r[0].rid+"'>"+r[0].roomName+"-"+r[0].roomDetails+"</option>";
					roo.disabled = true;
					document.getElementById("amountDue").value = r[0].bedSpacePrice;
					document.getElementById("totalAmount").innerHTML = pricer(r[0].bedSpacePrice);
					co.selectedIndex = 2;
					co.disabled = true;
					ema.value = r[0].inviteEmail;
					ema.disabled = true;
				}else{
					mCra(custom.error("The invite code is Invalid!"));
				}
			}else{
				mCra(custom.error(response));
			}
		});
	}catch(e){
		mCra(custom.warning("No Invite, Resume Normal Registration"));
	}
}
function validatePage(num){
	if(num == 0) return true;
	num = (num > 0)? num : 0;
	var c = 0;
	while(c < num){
		message = caser(c);
		if(message == ""){
			//return true;
		}else{
			custom = new customMessage();
			mCra(custom.error(message+"!"));
			return false;
		}
		c++;
	}
	return true;
}
function caser(pageNum){
	var message = "";
	switch (pageNum){
		case 0:
			if(isNaN(parseInt($("#amountDue").val()))){
				message = "You have not selected a valid hostel and room type";
			}else if(document.getElementById("config").selectedIndex == 1 && $("#OptComment").val() == ""){
				message = "You have to input at least 1 Invite email";
			}
			return message;
			break;
		case 1:
			if($("#firstname").val() == ""){
				message = "First name cannot be empty";
			}else if($("#lastname").val() == ""){
				message = "Last name cannot be empty";
			}else if($("#birthday").val() == ""){
				message = "Your birthday field cannot be empty";
			}else if($("#profilePic").val() == ""){
				message = "You may not have uploaded a passport";
			}else if($("#phone-no").val() == "" || $("#phone-no").val().length < 11){
				message = "Phone Number is invalid";
			}else if($("#LGs option:selected").text() == ""){
				message = "Local government is required";
			}else if($("#emal").val() == ""){
				message = "Valid email is required";
			}else{
			}
			return message;
			break;
		case 2:
			if($("#course").val() == ""){
				message = "Your course cannot be empty";
			}else if(isNaN(parseInt($("#course-duration").val()))){
				message = "Course duration is required";
			}else if($("#matric-number").val() == ""){
				message = "Matric Number is required";
			}else if(isNaN(parseInt($("#level").val()))){
				message = "Current Year/Level is required";
			}
			return message;
		case 3:
			if(document.getElementById("termsCheck").checked == false){
				message = "You have to agree to the terms and condition.";
			}
			return message;
	}
}