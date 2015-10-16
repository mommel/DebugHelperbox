var warnings = true;
var fatal = true;
var other = true;
var errortimer = 0;
var selectedProject = 'Nothing';
var selectedServer = 'None';
var selectedUser = user + '.';
var pages = new Array();
var databaseType = 1;
var statusRefresh = true;
var serverlogMousebindung1=false;
var serverlogMousebindung2=false;
var serverlogMousebindung3=false;
var serverlogMousebindung4=false;

pages['btnRedmineToday'] = 'https://#############/time_entries?utf8=%E2%9C%93&f%5B%5D=spent_on&op%5Bspent_on%5D=t&f%5B%5D=user_id&op%5Buser_id%5D=%3D&v%5Buser_id%5D%5B%5D=me&f%5B%5D=&c%5B%5D=project&c%5B%5D=spent_on&c%5B%5D=user&c%5B%5D=activity&c%5B%5D=issue&c%5B%5D=comments&c%5B%5D=hours';
pages['btnRedmineIssue'] = 'https://#############/issues/12345';
pages['btnIntranet'] = '';
pages['btnWiki'] = '';
pages['btnSvn'] = '';
pages['btnGit'] = '';
pages['btnJenkins'] = '';
pages['btnRegEx'] = 'http://www.phpliveregex.com/';
pages['btnJQueryLocal'] = 'http://www.sitepoint.com/building-list-jquery-local-storage/';
pages['btnBootPly'] = 'http://www.bootply.com/';
pages['btnHoodie'] = 'http://www.hood.ie/';
pages['btnUmlaute'] = 'http://www.meb.uni-bonn.de/html_tutorial/zeichen.htm';

getProjects();

$.ajax({
    type: "POST",
    url: "assets/filterbubble.html",
    data: {},
    dataType: "html"
}).done(function(msg) {
    $(".filterbubble").html(msg);
    $('.checkbox').checkbox({
        buttonStyle: 'btn-base',
        buttonStyleChecked: 'btn-success',
    });

    $(".cb_other").click(function() {
        clearTimeout(errortimer);
        toggleStatus('Off');
        var value = $(this).is(':checked');
        var c = $(this).is("checked");
        console.log(value);
        console.log(c);
        /*
				if( value ) { 
					$( ".cb_other" ).attr( {checked: true} );
					other = true;
				}else{
					$( ".cb_other" ).attr( {checked: false} );
					other = false;
				};*/
        //startRefresh();
    });

});

$('#decHourBtn').click(function(event) {
    var decHour = $('#decHour').val();
    var decHourMinutes = decHour * 60;
});

$(function() {
    startRefresh();
});

function addMousebindung(serverLog){
    dobind=false;
    if(serverLog=="#vmlog" && serverlogMousebindung1==false){
        dobind = true;
        serverlogMousebindung1=true;
    }
    if(serverLog=="#l1log" && serverlogMousebindung2==false){
        dobind = true;
        serverlogMousebindung2=true;
    }
    if(serverLog=="#l2log" && serverlogMousebindung3==false){
        dobind = true;
        serverlogMousebindung3=true;
    }
    if(serverLog=="#l3log" && serverlogMousebindung4==false){
        dobind = true;
        serverlogMousebindung4=true;
    }

    if(dobind == true){
        $( serverLog ).mouseover(function() {
            stopTimer(errortimer,true);
        });
        $( serverLog ).mouseleave(function() {
            if(statusRefresh==true){
                startRefresh();
            }
        });
    }
}


function getProjects(){
    if(selectedUser != 'Development' && selectedUser != 'Testing.'){

        $.ajax({
            type: "POST",
            url: "http://" + selectedUser + server1 + "/getProjects.php",
            data: {dataType:dataTypeserver1},
            dataType: dataTypeserver1
        }).done(function(msg) {
            $("#windowsVmProjectsContent").html(msg);
        });

        $.ajax({
            type: "POST",
            url: "http://" + selectedUser + server2 + "/startpage-server/getProjects.php",
            data: {dataType:dataTypeserver2},
            dataType: dataTypeserver2
        }).done(function(msg) {
            $("#entwicklungLinux1ProjectsContent").html(msg);
        });        
        $.ajax({
            type: "POST",
            url: "http://" + selectedUser + server3 + "/startpage-server/getProjects.php",
            data: {dataType:dataTypeserver3},
            dataType: dataTypeserver3
        }).done(function(msg) {
            $("#entwicklungLinux2ProjectsContent").html(msg);
        });        
        $.ajax({
            type: "POST",
            url: "http://" + selectedUser + server4 + "/startpage-server/getProjects.php",
            data: {dataType:dataTypeserver4},
            dataType: dataTypeserver4
        }).done(function(msg) {
            $("#entwicklungLinux3ProjectsContent").html(msg);
        });
    }else{
        
        $.ajax({
            type: "POST",
            url: "http://" + server1 + "/developertoolbox/getProjects.php",
            data: {dataType:dataTypeserver1},
            dataType: dataTypeserver1
        }).done(function(msg) {
            $("#windowsVmProjectsContent").html(msg);
        });
        
        $.ajax({
            type: "POST",
            url: "http://" + server2 + "/developertoolbox/getProjects.php",
            data: {dataType:dataTypeserver2},
            dataType: dataTypeserver2
        }).done(function(msg) {
            $("#entwicklungLinux1ProjectsContent").html(msg);
        });
        $.ajax({
            type: "POST",
            url: "http://" + server3 + "/developertoolbox/getProjects.php",
            data: {dataType:dataTypeserver3},
            dataType: dataTypeserver3
        }).done(function(msg) {
            $("#entwicklungLinux2ProjectsContent").html(msg);
        });  
        $.ajax({
            type: "POST",
            url: "http://" + server4 + "/developertoolbox/getProjects.php",
            data: {dataType:dataTypeserver4},
            dataType: dataTypeserver4
        }).done(function(msg) {
            $("#entwicklungLinux3ProjectsContent").html(msg);
        });
    }    
}

function harErrorHide(){
    $("#harerror").addClass('hide');
};

function harOkHide(){
    $("#harok").addClass('hide');
};

function genXmlHide(){
    $("#genxmlstart").addClass('hide');
};
function toggleStatus(setter){
    if (setter!='On'){
       $('#showstatus').removeClass('glyphicon-time');
       $('#showstatus').addClass('glyphicon-ban-circle');
       $('#showstatus').removeClass('statusgreen');
       $('#showstatus').addClass('statusred');
    }else{
        $('#showstatus').removeClass('glyphicon-ban-circle');
        $('#showstatus').addClass('glyphicon-time');
        $('#showstatus').removeClass('statusred');
        $('#showstatus').addClass('statusgreen');
    }
}

function stopTimer(timer,automatic){
    clearTimeout(timer);   
    toggleStatus('Off');
    if(automatic==false){
        statusRefresh = false;
    }
}

function startRefresh() {
    if (selectedProject && selectedProject != '') {
        $('#selectedProjectUnBtn').val(selectedProject);
    }
    toggleStatus('On');

    errortimer = setTimeout(startRefresh, 1000);

    if(selectedUser != 'Development' && selectedUser != 'Testing.'){
        if (selectedProject && selectedProject != '' && selectedProject != 'Nothing' && selectedServer == selectedUser + server1 ) {   
            $.ajax({
                type: "POST",
                url: "http://" + selectedUser + server1 + "/errorlog.php?proj="+selectedProject+"&other=" + other,
                data: {
                    'database': selectedProject,
                    'dataType': dataTypeserver1
                },
                dataType: dataTypeserver1
            }).done(function(msg) {
                $("#vmlog").html(msg);
                addMousebindung("#vmlog");
            });
        }

        if (selectedProject && selectedProject != '' && selectedProject != 'Nothing' && selectedServer == selectedUser + server2 ) {   
            $.ajax({
                type: "POST",
                url: "http://" + selectedUser + server2 + "/startpage-server/errorlog.php?proj="+selectedProject+"&other=" + other,
                data: {
                    'database': selectedProject,
                    'dataType': dataTypeserver2
                },
                dataType: dataTypeserver2
            }).done(function(msg) {
                $("#l1log").html(msg);
                addMousebindung("#l1log");
            });
        }

        if (selectedProject && selectedProject != '' && selectedProject != 'Nothing'   && selectedServer == selectedUser + server3 ) {  
            $.ajax({
                type: "POST",
                url: "http://" + selectedUser + server3 + "/startpage-server/errorlog.php?proj="+selectedProject+"&other=" + other,
                data: {
                    'database': selectedProject,
                    'dataType': dataTypeserver3
                },
                dataType: dataTypeserver3
            }).done(function(msg) {
                $("#l2log").html(msg);
                addMousebindung("#l2log");
            });
        }


         if (selectedProject && selectedProject != '' && selectedProject != 'Nothing' && selectedServer == selectedUser + server4 ) {   
            $.ajax({
                type: "POST",
                url: "http://" + selectedUser + server4 + "/startpage-server/errorlog.php?proj="+selectedProject+"&other=" + other,
                data: {
                    'database': selectedProject,
                    'dataType': dataTypeserver4
                },
                dataType: dataTypeserver4
            }).done(function(msg) {
                $("#l3log").html(msg);
                addMousebindung("#l3log");
            });
        }

        if (selectedProject && selectedProject != '' && selectedProject != 'Nothing' && selectedServer == selectedUser + server2) {  
            $.ajax({
                type: "POST",
                url: "http://" + selectedUser + server2 + "/startpage-server/adodblogs.php?database=" + selectedProject + "&databasetype=" + databaseType,
                data: {
                    'dataType': dataTypeserveradodb
                },
                dataType: dataTypeserveradodb
            }).done(function(msg) {
                $("#adodbMysql").html(msg);
            });    
        }
    }else{
        if (selectedProject && selectedProject != '' && selectedProject != 'Nothing' && selectedServer ==  server1 ) {   
            $.ajax({
                type: "POST",
                url: "http://" + server1 + "/developertoolbox/errorlog.php?proj="+selectedProject+"&other=" + other,
                data: {
                    'database': selectedProject,
                    'dataType': dataTypeserver1
                },
                dataType: dataTypeserver1
            }).done(function(msg) {
                $("#vmlog").html(msg);
            });
        }

        if (selectedProject && selectedProject != '' && selectedProject != 'Nothing' && selectedServer == server2 ) {   
            $.ajax({
                type: "POST",
                url: "http://" + server2 + "/developertoolbox/errorlog.php?proj="+selectedProject+"&other=" + other,
                data: {
                    'database': selectedProject,
                    'dataType': dataTypeserver2
                },
                dataType: dataTypeserver2
            }).done(function(msg) {
                $("#l1log").html(msg);
            });
        }

        if (selectedProject && selectedProject != '' && selectedProject != 'Nothing'   && selectedServer == server3 ) {  
            $.ajax({
                type: "POST",
                url: "http://" + server3 + "/developertoolbox/errorlog.php?proj="+selectedProject+"&other=" + other,
                data: {
                    'database': selectedProject,
                    'dataType': dataTypeserver3
               },
                dataType: dataTypeserver3
            }).done(function(msg) {
                $("#l2log").html(msg);
            });
        }

        if (selectedProject && selectedProject != '' && selectedProject != 'Nothing'   && selectedServer == server4 ) {  
            $.ajax({
                type: "POST",
                url: "http://" + server4 + "/developertoolbox/errorlog.php?proj="+selectedProject+"&other=" + other,
                data: {
                    'database': selectedProject,
                    'dataType': dataTypeserver4
              },
                dataType: dataTypeserver4
            }).done(function(msg) {
                $("#l3log").html(msg);
            });
        }

        if (selectedProject && selectedProject != '' && selectedProject != 'Nothing' && selectedServer == server2) {  
            $.ajax({
                type: "POST",
                url: "http://" + server2 + "/developertoolbox/adodblogs.php?database=" + selectedProject + "&databasetype=" + databaseType,
                data: {
                    'dataType': dataTypeserveradodb
              },
                dataType: dataTypeserveradodb
            }).done(function(msg) {
                $("#adodbMysql").html(msg);
            });    
        }
    }
}

$(document).ready(function() {
    $(".staticButton").click(function() {
        window.open(pages[this.id]);
    });

    $("#databasetype_mysql").click(function() {
        if(this.checked=true){
            databaseType = 1;    
        }
        else{
            databaseType = 2;
        }        
    });

    $("#databasetype_mssql").click(function() {
        if(this.checked=true){
            databaseType = 2;    
        }
        else{
            databaseType = 1;
        }
    });

    $('#xmltranform').attr('action', "http://" + selectedUser + server2 + "/startpage-server/hide/tools/gettransformation.php");

    $('#userNameInDropdownA').html(user);
    $('#userSelector').html(user);

    $('#restartBtn').click(function(event) {
        statusRefresh = true;
        startRefresh();
    });

    $('#userNameInDropdown').click(function(event) {
        stopTimer(errortimer,false);        
        $('#userSelector').html(user);
        selectedUser = user + '.';
        getProjects();
        startRefresh();
    });

    $('#devInDropdown').click(function(event) {
        stopTimer(errortimer,false);
        $('#userSelector').html('Development');
        selectedUser = 'Development';
        getProjects();
        startRefresh();
    });

    $('#stopBtn').click(function(event) {
    	if(errortimer){
        	stopTimer(errortimer,false);
        }
    });    
});