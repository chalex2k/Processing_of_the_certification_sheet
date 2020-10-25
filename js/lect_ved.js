function getdetails(){
	let a1 = [];
	let a2 = [];
	let a3 = [];
	console.log(Number(document.getElementById('count_of_students').value));
	for(let i = 0; i < Number(document.getElementById('count_of_students').value); i++){
		//a1[i] = $('input[name="att1"]')[i].val();
		a1.push(document.getElementsByName('att1')[i].value);
		a2.push(document.getElementsByName('att2')[i].value);
		a3.push(document.getElementsByName('att3')[i].value);
	}
    $.ajax({
        type: "POST",
        url: "lect_ved.php",
        data: {save:true, att1:a1, att2:a2, att3:a3}
    }).done(function(result)
        {
            //$("#msg").html( " Request is " + result );
        });
}

function readCookie(name) {
	var name_cook = name+"=";
	var spl = document.cookie.split(";");
	
	for(var i=0; i<spl.length; i++) {
		var c = spl[i];
		while(c.charAt(0) == " ") {
			c = c.substring(1, c.length);
		}
		if(c.indexOf(name_cook) == 0) {	
			return c.substring(name_cook.length, c.length);
		}
	}
}

function getexcel(){
	let a1 = [];
	let a2 = [];
	let a3 = [];
	console.log(Number(document.getElementById('count_of_students').value));
	for(let i = 0; i < Number(document.getElementById('count_of_students').value); i++){
		//a1[i] = $('input[name="att1"]')[i].val();
		a1.push(document.getElementsByName('att1')[i].value);
		a2.push(document.getElementsByName('att2')[i].value);
		a3.push(document.getElementsByName('att3')[i].value);
	}
    $.ajax({
        type: "POST",
        url: "lect_ved.php",
        data: {load:true, att1:a1, att2:a2, att3:a3}
    }).done(function(result)
        {
            //$("#msg").html( " Request is " + result );
			//console.log(readCookie("fn"));
			var xlslink = readCookie("fn");
			var link = document.createElement('a');	
			link.setAttribute('href', xlslink); 
			link.setAttribute('download', "ved.xls");
			link.setAttribute('target','_blank');
			link.style.display = 'none';
			document.body.appendChild(link); 
			link.click(); 
			document.body.removeChild(link);
        });
}
