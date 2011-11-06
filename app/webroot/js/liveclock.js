///////////////////////////////////////////////////////////
// "Live Clock Advanced" script - Version 1.0
// By Mark Plachetta (astroboy@zip.com.au)
//
// Get the latest version at:
// http://www.zip.com.au/~astroboy/liveclock/
//
// Based on the original script: "Upper Corner Live Clock"
// available at:
// - Dynamic Drive (http://www.dynamicdrive.com)
// - Website Abstraction (http://www.wsabstract.com)
// ========================================================
// CHANGES TO ORIGINAL SCRIPT:
// - Gave more flexibility in positioning of clock
// - Added date construct (Advanced version only)
// - User configurable
// ========================================================
// Both "Advanced" and "Lite" versions are available free
// of charge, see the website for more information on the
// two scripts.
///////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////
/////////////// CONFIGURATION /////////////////////////////

	// Set the clock's font face:
	var myfont_face = "Arial";

	// Set the clock's font size (in point):
	var myfont_size = "110%";

	// Set the clock's font color:
	var myfont_color = "#444";
	
	// Set the clock's background color:
	var myback_color = "#FFFFFF";

	// Set the text to display before the clock:
	var mypre_text = "";

	// Set the width of the clock (in pixels):
	var mywidth = 300;

	// Display the time in 24 or 12 hour time?
	// 0 = 24, 1 = 12
	var my12_hour = 0;

	// How often do you want the clock updated?
	// 0 = Never, 1 = Every Second, 2 = Every Minute
	// If you pick 0 or 2, the seconds will not be displayed
	var myupdate = 1;

	// Display the date?
	// 0 = No, 1 = Yes
	var DisplayDate = 1;

/////////////// END CONFIGURATION /////////////////////////
///////////////////////////////////////////////////////////

// Browser detect code
        var ie4=document.all;
        var ns4=document.layers;
        var ns6=document.getElementById&&!document.all;

// Global varibale definitions:

	var dn = "";
	var mn = ".";
	var old = "";

// The following arrays contain data which is used in the clock's
// date function. Feel free to change values for Days and Months
// if needed (if you wanted abbreviated names for example).
	var DaysOfWeek = new Array(7);
		DaysOfWeek[0] = "Sonntag";
		DaysOfWeek[1] = "Montag";
		DaysOfWeek[2] = "Dienstag";
		DaysOfWeek[3] = "Mittwoch";
		DaysOfWeek[4] = "Donnerstag";
		DaysOfWeek[5] = "Freitag";
		DaysOfWeek[6] = "Samstag";

	var MonthsOfYear = new Array(12);
		MonthsOfYear[0] = "Januar";
		MonthsOfYear[1] = "Februar";
		MonthsOfYear[2] = "März";
		MonthsOfYear[3] = "April";
		MonthsOfYear[4] = "Mai";
		MonthsOfYear[5] = "Juni";
		MonthsOfYear[6] = "Juli";
		MonthsOfYear[7] = "August";
		MonthsOfYear[8] = "September";
		MonthsOfYear[9] = "Oktober";
		MonthsOfYear[10] = "November";
		MonthsOfYear[11] = "Dezember";

// This array controls how often the clock is updated,
// based on your selection in the configuration.
	var ClockUpdate = new Array(3);
		ClockUpdate[0] = 0;
		ClockUpdate[1] = 1000;
		ClockUpdate[2] = 60000;
		
		
// For Version 4+ browsers, write the appropriate HTML to the
// page for the clock, otherwise, attempt to write a static
// date to the page.
	var elementAdded = false;

// The main part of the script:
	function show_clock() {		
		//if (ie4||ns6) { //document.write('<span id="LiveClockIE" style="width:'+mywidth+'px; background-color:'+myback_color+'"></span>'); }
		if (!elementAdded) {
			var clockspan = document.createElement("span");
			var clockspan_id = document.createAttribute("id");		
			clockspan.setAttribute('id','LiveClockTime'); 
			
			var datespan = document.createElement("span");
			var datekspan_id = document.createAttribute("id");		
			datespan.setAttribute('id','LiveClockDate');
			
			var clockdiv = document.getElementById("clock");			 
			clockdiv.appendChild(datespan);
			clockdiv.appendChild(clockspan);
			elementAdded = true;
			//document.write('<span id="LiveClockIE" style="width:'+mywidth+'px; background-color:'+myback_color+'"></span>'); }
		}
		/*
		}
		else if (document.layers) { document.write('<ilayer bgColor="'+myback_color+'" id="ClockPosNS" visibility="hide"><layer width="'+mywidth+'" id="LiveClockNS"></layer></ilayer>'); }
		else { old = "true"; show_clock(); }		
		
		if (old == "die") { return; }
		 */
	//show clock in NS 4
		if (ns4)
                document.ClockPosNS.visibility="show";
	// Get all our date variables:
		var Digital = new Date();
		var day = Digital.getDay();
		var mday = Digital.getDate();
		var month = Digital.getMonth();
		var hours = Digital.getHours();

		var minutes = Digital.getMinutes();
		var seconds = Digital.getSeconds();

	// Fix the "mn" variable if needed:
		if (mday == 1) { mn = "."; }
		else if (mday == 2) { mn = "."; }
		else if (mday == 3) { mn = "."; }
		else if (mday == 21) { mn = "."; }
		else if (mday == 22) { mn = "."; }
		else if (mday == 23) { mn = "."; }
		else if (mday == 31) { mn = "."; }

	// Set up the hours for either 24 or 12 hour display:
		if (my12_hour) {
			dn = "AM";
			if (hours > 12) { dn = "PM"; hours = hours - 12; }
			if (hours == 0) { hours = 12; }
		} else {
			dn = "";
		}
		if (minutes <= 9) { minutes = "0"+minutes; }
		if (seconds <= 9) { seconds = "0"+seconds; }

	// This is the actual HTML of the clock. If you're going to play around
	// with this, be careful to keep all your quotations in tact.
		myclock = '';
		mydate = '';
		//myclock += '<font style="color:'+myfont_color+'; font-family:'+myfont_face+'; font-size:'+myfont_size+';">';
		myclock += mypre_text;
		if (DisplayDate) {
			mydate = DaysOfWeek[day]+', '+mday+mn+' '+MonthsOfYear[month];
		}
		myclock += hours+':'+minutes;
		if ((myupdate < 2) || (myupdate == 0)) { myclock += ':'+seconds; }
		myclock += ' '+dn;
		
		//myclock += '</font>';

		if (old == "true") {
			document.write(myclock);
			old = "die";
			return;
		}

	// Write the clock to the layer:
		if (ns4) {
			clockpos = document.ClockPosNS;
			liveclock = clockpos.document.LiveClockNS;
			liveclock.document.write(myclock);
			liveclock.document.close();
		} else if (ie4) {
			LiveClockIE.innerHTML = myclock;
		} else if (ns6){
			document.getElementById("LiveClockDate").innerHTML = mydate;
			document.getElementById("LiveClockTime").innerHTML = myclock;
                }            

	if (myupdate != 0) { setTimeout("show_clock()",ClockUpdate[myupdate]); }
}