//global variables
let currDate = new Date();
let currMonth = currDate.getMonth();
let currYear = currDate.getFullYear();
const month_names = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
let user = "";
let user_id = -1;
let token = "";
let eventListeners = [];
let eventListenersCount = 0;


//Makes the calendar by taking in the year and month of the current calendar view
function makeCalendar(year, month){
    $("#tablediv").empty();
    eventListeners = [];
    eventListenersCount = 0;

    //builds the table
    const table = document.createElement("table");
    let table_id = month+" "+year;
    table.setAttribute("id", table_id);
    table.innerHTML = "<thead><tr><th id='title' colspan='7'></th></tr><tr><th>Sunday</th><th>Monday</th><th>Tuesday</th><th>Wednesday</th><th>Thursday</th><th>Friday</th><th>Saturday</th></tr></thead>";
    document.getElementById("tablediv").appendChild(table);
    
    const tbody = document.createElement("tbody");
    tbody.setAttribute("id", "tablebody");
    document.getElementById(table_id).appendChild(tbody);
    
    document.getElementById("title").innerHTML = month_names[month] + " " + year;
    
    let tmp = month_names[month] + " " + 1 + " " + year;
    let firstdayNum = new Date(tmp).getDay();
    let lastDate = new Date(year, month+1, 0).getDate();

    //creates rows and cells, adds in dates
    for(i=1; i<=7; i++){
        const week = document.createElement("tr");
        week.setAttribute("id", "week"+i);
        document.getElementById("tablebody").appendChild(week);
    }
    let count = 1;
    for(i=0; i<(firstdayNum+lastDate); i++){
        const day = document.createElement("td");
        let weekNum = Math.floor(i/7)+1;
        if(i>=firstdayNum){
            day.appendChild(document.createTextNode(count));
            day.setAttribute("id", "day"+count);
            count++;
        }
        document.getElementById("week"+weekNum).appendChild(day);
    }

    currYear = year;
    currMonth = month;

    //checks if there's a logged in user, shows parts of page only accessible to logged
    //in users, fetches all of the user's events, shared events, and shared calendars
    if(user!=="" && user_id!==-1){
        $("#displayeventdiv").hide();
        $("#editeventinfodiv").hide();
        $("#editeventinfodiv").children().val("");
        $("#addeventdiv").show();

        const data = { 'user_id': user_id, 'token': token };

        fetch("GetAllEvents.php", {
            method: 'POST',
            body: JSON.stringify(data), 
            headers: { 'content-type': 'application/json' }
        })
        .then(response => response.json())
        .then(function(data){
            //calls show events to display all of the events in the calendar
            showEvents(data);
        })
        .catch(err => console.error(err));
    }
}

//calls check login when the page loads
document.addEventListener("DOMContentLoaded", function(event){
    checkLogin();
}, false);

//fetch to get session variables data
//this function helps for when the page refreshes to ensure the logged in user's info is 
//all still present on the page
function checkLogin(){
    fetch("GetUserInfo.php", {
        method: 'POST',
        headers: { 'content-type': 'application/json' }
    })
    .then(response => response.json())
    .then(function(data){
        user = data.user;
        user_id = data.user_id;
        token = data.token;
        //if there is a user logged in, displays all the relevant areas of the html page
        if(data.success){
            $("#logindiv").hide();
            $("#registerdiv").hide();
            $("#logoutdiv").show();
            $("#sharecalendardiv").show();
            document.getElementById("userdisplay").innerHTML = "Hello "+user+"!";
            document.getElementById("Logoutbutton").addEventListener("click", logout, false);
            $("#eventdiv").show();
            $("#addeventdiv").show();
            $("#addeventinfodiv").hide();
            document.getElementById("addeventbutton").addEventListener("click", addEvent, false);
            document.getElementById("submiteventbutton").addEventListener("click", submitEvent, false);
            document.getElementById("gobackbutton").addEventListener("click", function(event){
                $("#addeventinfodiv").slideUp("slow");
                $("#addeventdiv").show();
            }, false);
            document.getElementById("sharecalendarbutton").addEventListener("click", shareCalendar, false);
            
        }
        makeCalendar(currYear, currMonth);
    })
    .catch(err => console.error(err));
}


//Displays the next month
function getNextMonth(){
    let next = new Month(currYear, currMonth).nextMonth();
    let nextM = next.month;
    let nextY = next.year;
    makeCalendar(nextY, nextM);
}
//Displays the previous month
function getPrevMonth(){
    let previous = new Month(currYear, currMonth).prevMonth();
    let prevM = previous.month;
    let prevY = previous.year;
    makeCalendar(prevY, prevM);
}

//Adds event listener to next month and previous month buttons
document.getElementById("Previousbutton").addEventListener("click", getPrevMonth, false);
document.getElementById("Nextbutton").addEventListener("click", getNextMonth, false);


//Helper functions provided for building the calendar table and traversing through months
(function(){
    Date.prototype.deltaDays=function(c){
        return new Date(this.getFullYear(),this.getMonth(),this.getDate()+c)
    };
    Date.prototype.getSunday=function(){
        return this.deltaDays(-1*this.getDay())
    }
})();
function Week(c){
    this.sunday=c.getSunday();
    this.nextWeek=function(){
        return new Week(this.sunday.deltaDays(7))
    };
    this.prevWeek=function(){
        return new Week(this.sunday.deltaDays(-7))
    };
    this.contains=function(b){
        return this.sunday.valueOf()===b.getSunday().valueOf()
    };
    this.getDates=function(){
        var b=[]
        for(a=0;7>a;a++)
        b.push(this.sunday.deltaDays(a));
        return b}
    }
function Month(c,b){
    this.year=c;
    this.month=b;
    this.nextMonth=function(){
        return new Month(c+Math.floor((b+1)/12),(b+1)%12)
    };
    this.prevMonth=function(){
        return new Month(c+Math.floor((b-1)/12),(b+11)%12)
    };
    this.getDateObject=function(a){
        return new Date(this.year,this.month,a)
    };
    this.getWeeks=function(){
        var a=this.getDateObject(1),b=this.nextMonth().getDateObject(0),c=[],a=new Week(a);
        for(c.push(a);!a.contains(b);)a=a.nextWeek(),c.push(a);
        return c}
    };

   
    
//log in function 
function loginAjax(event) {
    //gets username and password from the form, fetch to check if username and password are valid
    const username = document.getElementById("username").value; 
    const password = document.getElementById("password").value; 

    const data = { 'username': username, 'password': password };

    fetch("Login_ajax.php", {
            method: 'POST',
            body: JSON.stringify(data),
            headers: { 'content-type': 'application/json' }
        })
        .then(response => response.json())
        .then(function(data){
            console.log(data.success ? "You've been logged in!" : `You were not logged in ${data.message}`)
            //if log in is successful, shows and hides all the relevant parts of the page for a logged
            //in user, adds event listeners to all buttons that are now shown
            if(data.success){
                user = data.username;
                user_id = data.user_id;
                token = data.token;

                $("#logindiv").hide();
                $("#registerdiv").hide();
                $("#logoutdiv").show();
                $("#sharecalendardiv").show();
                document.getElementById("userdisplay").innerHTML = "Hello "+user+"!";
                document.getElementById("Logoutbutton").addEventListener("click", logout, false);
                $("#eventdiv").show();
                $("#addeventdiv").show();
                $("#addeventinfodiv").hide();
                document.getElementById("addeventbutton").addEventListener("click", addEvent, false);
                document.getElementById("submiteventbutton").addEventListener("click", submitEvent, false);
                document.getElementById("gobackbutton").addEventListener("click", function(event){
                    $("#addeventinfodiv").slideUp("slow");
                    $("#addeventinfodiv").children().val("");
                    $("#addeventdiv").show();
                }, false);
                document.getElementById("sharecalendarbutton").addEventListener("click", shareCalendar, false);

                //calls make calendar again to so that the user's events will show up
                makeCalendar(currYear, currMonth);
            }
            else{
                alert(data.message);
                $("#password").val("");
            }
        })
        .catch(err => console.error(err));
}

//log out function
function logout(){
    //fetch to php file that destroys session
    const data = { 'user_id': user_id, 'token': token };
    fetch("Logout.php", {
        method: 'POST',
        body: JSON.stringify(data), 
        headers: { 'content-type': 'application/json' }
    })
    .then(response => response.json())
    .then(function(data){
        console.log(data.success ? "You've been logged out!" : `You were not logged out ${data.message}`)
    })
    .catch(err => console.error(err));
    //sets global variables back to original values, shows and hides all relevant html parts
    //for when a user is viewing the calendar but logged out
    user = "";
    user_id = -1;
    token = "";
    eventListeners = [];
    eventListenersCount = 0;
    currEventArr = [];
    $("#username").val("");
    $("#password").val("");
    $("#RegPassword").val("");
    $("#RegPassword2").val("");
    $("#RegUsername").val("");
    $("#logindiv").show();
    $("#registerdiv").show();
    $("#logoutdiv").hide();
    $("#sharecalendardiv").hide();
    $("#eventdiv").hide();
    $("#addeventinfodiv").hide();
    $("#addeventdiv").hide();
    //calls make calendar to remake it without showing any events
    makeCalendar(currYear, currMonth);
}
    
//registers a new user
function registerAjax(event){
    //Gets username and password from form, fetch to upload new username and password (hashed) to 
    //the MySQL users table
    const username = document.getElementById("RegUsername").value; 
    const password = document.getElementById("RegPassword").value; 
    const password2 = document.getElementById("RegPassword2").value;
    //Checks to ensure valid username and password structure are entered
    if(username.localeCompare("")==0 || password.localeCompare("")==0){
        alert("Incomplete info: please register with username and password");
    }
    else if(username.includes(",")){
        alert("Can not use a comma in username");
    }
    else if(password.localeCompare(password2)!=0){
        alert("Passwords don't match - enter same password");
        $("#RegPassword").val("");
        $("#RegPassword2").val("");
    }
    else{
        const data = { 'username': username, 'password': password };

        fetch("Register_ajax.php", {
            method: 'POST', 
            body: JSON.stringify(data),
            headers: { 'content-type': 'application/json' }
        })
        .then(response => response.json())
        .then(function(data){
            console.log(data.success ? "You've Registered!" : `You were not registered ${data.message}`);
            if(data.success){
                alert("You've been registered! Now log in!");
                document.getElementById("registerdiv").style.display = "none";
            }
            else{
                //If username already exists, requires the user to enter a new username
                alert("Username already exists - use a different one");
                $("#RegUsername").val("");
                $("#RegPassword").val("");
                $("#RegPassword2").val("");
            }
        }) 
        .catch(err => console.error(err)); 
    }
}
//adds event listeners to the login and register buttons
document.getElementById("Loginbutton").addEventListener("click", loginAjax, false);
document.getElementById("Registerbutton").addEventListener("click", registerAjax, false);



//global array of colors for the different event tag types
const tags = {
    None: "black",
    Work: "rgb(" + 110 + "," + 20 + "," + 20 +")",
    School: "darkgreen",
    Event: "darkblue",
    Sports: "rgb(" + 20 + "," + 138 + "," + 173 + ")",
    Family: "purple",
    Social: "rgb("+ 134 + "," + 89 + "," + 3 + ")",
    Religion: "rgb(" + 230 + "," + 48 + "," + 48 + ")"
}

//dispalys all events in the cells of the calendar
function showEvents(data){
    //for each event, pulls all data from the event and checks if the event exists in the current 
    //month and year being dispalyed by the calendar
    for(i=0; i<data.length; i++){
        let event_id = data[i].event_id;
        let event_title = data[i].title;
        let event_month = data[i].month;
        let event_day = data[i].day;
        let event_year = data[i].year;
        let event_tag = data[i].tag;
        let event_tag_enable = data[i].tag_enable;
        if(event_month==currMonth && event_year==currYear && document.getElementById("event"+event_id)==null){
            //if the event exists in the current month/year, makes an button element for the specific
            //event
            //also checks if the event hasn't already been added to the calendar - avoids double adding
            //events in the case that a user X shares a specific event with user Y but also shares their
            //whole calendar with user Y
            let eventbutton = document.createElement("button");
            eventbutton.setAttribute("id", "event"+event_id);
            eventbutton.setAttribute("type", "button");
            eventbutton.className = "eventbutton";
            eventbutton.setAttribute("listener", false)
            eventbutton.innerHTML = event_title;
            eventbutton.style.backgroundColor =  'rgb(' + 218 + ',' + 172 + ',' + 157 + ')';
            eventbutton.style.border = "none";
            eventbutton.style.textDecoration = "underline";
            eventbutton.style.cursor = "pointer";
            eventbutton.style.fontSize = "12px";
            if(event_tag_enable.localeCompare("Enable")==0){
                eventbutton.style.color = tags[event_tag];
            }
            if(data[i].sharer==""){
                eventbutton.style.fontWeight = "700";
            }
            //adds the button into the correct day in the calendar
            document.getElementById("day"+event_day).innerHTML += "<br>";
            document.getElementById("day"+event_day).appendChild(eventbutton);
            document.getElementById("day"+event_day).innerHTML += "<br>"; 

        }
    }
    //calls make buttons to add event listeners to all of the buttons
    makeButtons(data);
}

//adds event listeners
function makeButtons(data){
    for(i=0; i<data.length; i++){
        if(data[i].month==currMonth && data[i].year==currYear){
            let event_id = data[i].event_id;
            let event_title = data[i].title;
            let eventArr = data[i];
            let str = "event"+event_id;
            if(eventListeners.includes(str)==false){
                //if the event is in the current month/year and hasn't already been added to the
                //event listeners global array, adds an event listener and adds it to the array
                eventListeners[eventListenersCount] = str;
                eventListenersCount++;
                document.getElementById("event"+event_id).addEventListener("click", function(){
                    eventClicked(eventArr);
                }, false);
            }
        }
    }
}

//global array to hold the current event
let currEventArr = [];

//displays all information of a specific event when an event button in the calendar box is clicked
function eventClicked(eventArr){
    currEventArr = eventArr;
    //hides and removes data from all relevant html parts
    $("#displayeventdiv").hide();
    $("#editeventinfodiv").hide();
    $("#addeventinfodiv").hide();
    $("#editeventinfodiv").children().val("");
    document.getElementById("gobackbutton2").removeEventListener("click", goBack2, false);
    document.getElementById("deletebutton").removeEventListener("click", deleteEvent, false);
    document.getElementById("editbutton").removeEventListener("click", editEvent, false);
    $("#displayeventinfodiv").empty();
    $("#enablebutton").hide();
    $("#enablelabel").hide();
    $("#enablebutton").prop("checked", false);
    $("#disablebutton").hide();
    $("#disablelabel").hide();
    $("#disablebutton").prop("checked", false);
    $("#addeventdiv").hide();

    let monthNum = parseInt(eventArr.month);
    monthNum = monthNum+1;
    //displays information for the specific event into the display event info div
    document.getElementById("displayeventinfodiv").innerHTML += "Title: " + "<u>" + eventArr.title + "</u>" + "<br>" + 
    "Time: " + eventArr.time + "<br>" + "Date: " + monthNum+"/"+eventArr.day+"/"+eventArr.year + "<br>"  + "Description: " + eventArr.description + "<br><br>"
    + "<div style='color: " + tags[eventArr.tag] + "'>Tag: " + eventArr.tag + "</div>";
    //check to ensure the edit and delete buttons are hidden if the event was created
    //by another user and shared with the current user
    if(eventArr.sharer!=""){
        document.getElementById("displayeventinfodiv").innerHTML += "Event Creator: " + eventArr.sharer;
        $("#editbutton").hide();
        $("#deletebutton").hide();
    }
    //Displays/hides relevant buttons/labels for disable and enable depending on the status of the
    //event's tag
    else if(eventArr.tag.localeCompare("None")!=0){
        if(eventArr.tag_enable.localeCompare("Enable")==0){
            $("#enablelabel").hide();
            $("#disablelabel").show();
            $("#disablebutton").show();
        }
        else{
            $("#disablelabel").hide();
            $("#enablelabel").show();
            $("#enablebutton").show();
        }
        // document.getElementById("displayeventinfodiv").innerHTML += "<br>";
        $("#editbutton").show();
        $("#deletebutton").show(); 
    }
    //displays the information
    $("#displayeventdiv").slideDown("slow");

    //adds event listeners to all new buttons displayed
    document.getElementById("gobackbutton2").addEventListener("click", goBack2, false);
    document.getElementById("deletebutton").addEventListener("click", deleteEvent, false);
    document.getElementById("editbutton").addEventListener("click", editEvent, false);
    document.getElementById("enablebutton").addEventListener("click", changeEnable, false);
    document.getElementById("disablebutton").addEventListener("click", changeEnable, false);
}
//changes the status of whether the tag of the event is enabled or not when the enable/disable
//button is clicked, fetches to update this information in the events table
function changeEnable(){
    let event_id = currEventArr.event_id;
    let event_tag_enable = "Enable";
    if(currEventArr.tag_enable.localeCompare("Enable")==0){
        event_tag_enable = "Disable";
    }
    const data = { 
        'user_id': user_id,
        'token': token,
        'event_id': event_id,
        'event_tag_enable': event_tag_enable
    };
    
    fetch("EditEventEnable.php", {
        method: 'POST', 
        body: JSON.stringify(data),
        headers: { 'content-type': 'application/json' }
    })
    .then(response => response.json())
    .then(function(data){
        console.log(data.success ? "Tag enable changed" : `Tag enable not changed ${data.message}`);
        //calls make calendar to so that the event is showed with it's tag changed - the color of 
        //the font of the event button changes
        makeCalendar(currYear, currMonth);
    })
    .catch(err => console.error(err));   
}
//go back button is pressed, hides/empties the div that displays the event info
function goBack2(){
    $("#displayeventdiv").slideUp();
    $("#displayeventinfodiv").empty();
    $("#addeventdiv").show();
}
//event delete button is clicked, fetch to delete the event from the event table
function deleteEvent(){
    let eventArr = currEventArr;
    const data = { 
        'user_id': user_id,
        'token': token,
        'event_id': eventArr.event_id
    };
    
    fetch("DeleteEvent.php", {
        method: 'POST', 
        body: JSON.stringify(data),
        headers: { 'content-type': 'application/json' }
    })
    .then(response => response.json())
    .then(function(data){
        alert(data.success ? "Event deleted" : `Event not deleted ${data.message}`);
        console.log(data.success ? "Event deleted" : `Event not deleted ${data.message}`);
        if(data.success){
            //removes event from the calendar
            $("#event"+eventArr.event_id).remove();
            $("#displayeventdiv").slideUp();
            $("#displayeventinfodiv").empty();
            $("#addeventdiv").show();
        }
    })
    .catch(err => console.error(err));   
}
//edit button is pressed, fills in the div for editing an event with the event's info and
//displays it
function editEvent(){
    let eventArr = currEventArr;

    document.getElementById("editgobackbutton").removeEventListener("click", goBack, false);
    document.getElementById("editeventbutton").removeEventListener("click", submitEdits, false);
    $("#displayeventdiv").hide();
    $("#displayeventinfodiv").empty();
    $("#editeventtitle").val(eventArr.title);
    $("#editeventtime").val(eventArr.time);
    let tmp = parseInt(eventArr.month);
    let tmp2 = parseInt(eventArr.day);
    tmp++;
    if(tmp<10){
        tmp = "0"+tmp;
    }
    if(tmp2<10){
        tmp2 = "0"+tmp2
    }
    $("#editeventdate").val(eventArr.year+"-"+tmp+"-"+tmp2);
    $("#editeventdescription").val(eventArr.description);
    $("#editeventtag").val(eventArr.tag);
    $("#editeventinfodiv").show();

    //adds event listeners to the new buttons displayed
    document.getElementById("editgobackbutton").addEventListener("click", goBack, false);
    document.getElementById("editeventbutton").addEventListener("click", submitEdits, false);
}
//go back button is clicked in the edit event area, hides the div
function goBack(){
    $("#editeventinfodiv").slideUp();
    $("#editeventinfodiv").children().val("");
    $("#addeventdiv").show();
}
//if submit edit is clicked, pulls the data from the forms and uses a fetch to update
//the info for the event in the table
function submitEdits(){
    let event_id = currEventArr.event_id;
    const evedittitle = document.getElementById("editeventtitle").value;
    const evedittime = document.getElementById("editeventtime").value;
    const eveditdate = document.getElementById("editeventdate").value;
    const eveditdescription = document.getElementById("editeventdescription").value;
    let tmp = document.getElementById("editeventtag");
    const evedittag = tmp.options[tmp.selectedIndex].value;
    //checks to ensure all info is filled 
    if(evedittag==""){
        evedittag = "None";
    }
    if(evedittitle==""){
        alert("Add a title to your event");
    }
    else if(evedittime==""){
        alert("Choose a time for your event");
    }
    else if(eveditdate==""){
        alert("Choose a date for your event");
    }
    else{
        const arr = eveditdate.split("-");
        const eveditday = arr[2];
        const eveditmonth = arr[1]-1;
        const evedityear = arr[0];
        const data2 = { 
            'ev_id': event_id,
            'user_id': user_id,
            'token': token,
            'evtitle': evedittitle,
            'evtime': evedittime,
            'evmonth': eveditmonth,
            'evday': eveditday,
            'evyear': evedityear,
            'evdescription': eveditdescription,
            'evtag': evedittag
        };

        fetch("AddEditEvent.php", {
            method: 'POST', 
            body: JSON.stringify(data2),
            headers: { 'content-type': 'application/json' }
        })
        .then(res => {
            return res.json();
        })
        .then(function(stuff){
            alert(stuff.success ? "Event edited!" : `Event not edited ${data.message}`);
            console.log(stuff.success ? "Event edited!" : `Event not edited ${data.message}`);
        })
        .catch(err2 => console.error(err2));
        //hides the div for editing event
        $("#editeventinfodiv").children().val("");
        $("#editeventinfodiv").hide();
        $("#addeventdiv").show();
        //calls make calendar to update the table with the edited event
        makeCalendar(currYear, currMonth);
    }
}

let userShareCount = 0;
//add event button is clicked, displays the html div for inputting info for a new event
function addEvent(){
    userShareCount = 0;
    $("#eventuserdiv").empty();
    document.getElementById("eventuserdiv").innerHTML = "Share With"
    $("#addeventinfodiv").slideDown("slow");
    $("#eventuserminusbutton").hide();
    $("#addeventdiv").hide();
    //event listeners for the add user/remove user buttons for adding users to share the calendar with
    document.getElementById("eventuserbutton").addEventListener("click", addUserBox, false);
    document.getElementById("eventuserminusbutton").addEventListener("click", removeUserBox, false);
}
//adds another input box for the user to input another username to share the event with
function addUserBox(){
    userShareCount++;
    let str = "eventuser"+userShareCount;
    document.getElementById("eventuserdiv").innerHTML+="<input type='text' id='"+str+"' placeholder='username'/>"
    $("#eventuserminusbutton").show();
    document.getElementById("eventuserbutton").innerHTML = "Add Another User";
}
//removes an input box for the user to remove a username to share the event with
function removeUserBox(){
    const div = document.getElementById("eventuserdiv");
    div.removeChild(div.lastChild);
    userShareCount--;
    if(userShareCount<1){
        $("#eventuserminusbutton").hide();
        document.getElementById("eventuserbutton").innerHTML = "Add User";
    }
}

//submit event button clicked, takes info from forms and fetch to upload data to the events table
function submitEvent(){
    const evtitle = document.getElementById("eventtitle").value;
    const evtime = document.getElementById("eventtime").value;
    const evdate = document.getElementById("eventdate").value;
    const evdescription = document.getElementById("eventdescription").value;
    let tmp = document.getElementById("eventtag");
    const evtag = tmp.options[tmp.selectedIndex].value;
    let evusers = "";
    for(i=1; i<=userShareCount; i++){
        let tmp = "eventuser"+i;
        if(i==userShareCount){
            evusers = evusers + document.getElementById(tmp).value;
        }
        else{
            evusers = evusers + document.getElementById(tmp).value + ",";
        }
    }
    //checks to ensure all information is filled out
    if(evtitle==""){
        alert("Add a title to your event");
    }
    else if(evtime==""){
        alert("Choose a time for your event");
    }
    else if(evdate==""){
        alert("Choose a date for your event");
    }
    else{
        const arr = evdate.split("-");
        const evday = arr[2];
        const evmonth = arr[1]-1;
        const evyear = arr[0];
        const data = { 
            'user_id': user_id,
            'token': token,
            'evtitle': evtitle,
            'evtime': evtime,
            'evmonth': evmonth,
            'evday': evday,
            'evyear': evyear,
            'evdescription': evdescription,
            'evtag': evtag, 
            'evtag_enable': "Enable",
            'evusers': evusers,
        };

        fetch("AddEvent.php", {
            method: 'POST', 
            body: JSON.stringify(data),
            headers: { 'content-type': 'application/json' }
        })
        .then(response => {
            return response.json();
        })
        .then(function(data){
            makeCalendar(currYear, currMonth);
            alert(data.success ? "Event added!" : `Event not added ${data.message}`);
            console.log(data.success ? "Event added!" : `Event not added ${data.message}`);
        })
        .catch(err => console.error(err)); 
        //hides the div for adding a new event
        $("#addeventinfodiv").children().val("");
        $("#addeventinfodiv").hide();
        $("#addeventdiv").show();
    
    }
}


//share calendar button is clicked, gets username from form and fetch to add data to share table
function shareCalendar(){
    const usertoshare = document.getElementById("usertoshare").value;
    if(usertoshare==""){
        alert("Enter a username");
    }
    else{
        const data = { 
            'user_id': user_id,
            'token': token,
            'usertoshare': usertoshare
        };

        fetch("ShareCalendar.php", {
            method: 'POST', 
            body: JSON.stringify(data),
            headers: { 'content-type': 'application/json' }
        })
        .then(response => response.json())
        .then(function(data){
            alert(data.success ? "Calendar shared!" : `Calendar not shared ${data.message}`);
            console.log(data.success ? "Calendar shared!" : `Calendar not shared ${data.message}`);
        })
        .catch(err => console.error(err)); 
    }
    $("#usertoshare").val("");
}

