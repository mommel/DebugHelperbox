var warnings = true;
var fatal = true;
var other = true;
var errortimer = 0;
var selectedProject = 'Nothing';
var selectedUser = user + '.';
var pages = new Array();





pages['btnRegEx'] = 'http://www.phpliveregex.com/';
pages['btnJQueryLocal'] = 'http://www.sitepoint.com/building-list-jquery-local-storage/';

pages['btnBootPly'] = 'http://www.bootply.com/';
pages['btnHoodie'] = 'http://www.hood.ie/';


$.ajax({
    type: "POST",
    url: "http://" + selectedUser + "msdev.enterurl.loc/getProjects.php",
    data: {},
    dataType: "html"
}).done(function(msg) {
    $("#msdevProjectsContent").html(msg);
});

$.ajax({
    type: "POST",
    url: "http://" + selectedUser + "linux1.enterurl.loc/getProjects.php",
    data: {},
    dataType: "html"
}).done(function(msg) {
    $("#linux2ProjectsContent").html(msg);
});

$.ajax({
    type: "POST",
    url: "http://" + selectedUser + "linux2.enterurl.loc/getProjects.php",
    data: {},
    dataType: "html"
}).done(function(msg) {
    $("#linux2ProjectsContent").html(msg);
});

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
        var value = $(this).is(':checked');
        var c = $(this).is("checked");
    });

});

$('#decHourBtn').click(function(event) {
    var decHour = $('#decHour').val();
    var decHourMinutes = decHour * 60;
});

$(function() {
    startRefresh();
});

function startRefresh() {

    if (selectedProject && selectedProject != '') {
        $('#selectedProjectUnBtn').val(selectedProject);
    }
    errortimer = setTimeout(startRefresh, 1000);
    $.ajax({
        type: "POST",
        url: "http://" + selectedUser + "msdev.enterurl.loc/errorlog.php?other=" + other,
        data: {
            'database': selectedProject
        },
        dataType: "html"
    }).done(function(msg) {
        $("#vmlog").html(msg);
    });

    $.ajax({
        type: "POST",
        url: "http://" + selectedUser + "linux1.enterurl.loc/errorlog.php?other=" + other,
        data: {
            'database': selectedProject
        },
        dataType: "html"
    }).done(function(msg) {
        $("#e4log").html(msg);
    });

    $.ajax({
        type: "POST",
        url: "http://" + selectedUser + "linux2.enterurl.loc/errorlog.php?other=" + other,
        data: {},
        dataType: "html"
    }).done(function(msg) {
        $("#e3log").html(msg);
    });

    $.ajax({
        type: "POST",
        url: "http://" + selectedUser + "linux1.enterurl.loc/adodblogs.php?database=" + selectedProject,
        data: {},
        dataType: "html"
    }).done(function(msg) {
        $("#adodbMysql").html(msg);
    });
}

$(document).ready(function() {
    $(".staticButton").click(function() {
        window.open(pages[this.id]);
    });

    $('#userNameInDropdownA').html(user);
    $('#userSelector').html(user);

    $('#restartBtn').click(function(event) {
        startRefresh();
    });

    $('#userNameInDropdown').click(function(event) {
        clearTimeout(errortimer);
        $('#userSelector').html(user);
        selectedUser = user + '.';
        startRefresh();
    });

    $('#devInDropdown').click(function(event) {
        clearTimeout(errortimer);
        $('#userSelector').html('Development');
        selectedUser = '';
        startRefresh();
    });

    $('#stopBtn').click(function(event) {
        clearTimeout(errortimer);
    });
});