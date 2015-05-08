'use strict';

var room = '';
var me;
//var my_name;
var path = location.pathname.substring(1);

if (path.substring(0, 5) === 'user=') {
  // Assign what's after 'user=' to my_name variable
  //my_name = path.substring(5, path.length);
  me = parseUser(path.substring(5, path.length));
}

console.log("my_name is " + me.fullname);

$('#me').append('<tr><td>' + me.name + ' (me)</td>' +
                    '<td>' + me.lang1 + '</td>' +
                    '<td>' + me.lang2 + '</td>' +
                    //'<td id="' + my_name + '">online</td></tr>');
                    '<td id="' + me.fullname + '">online</td></tr>');

var socket = io.connect();

// Send variable my_name to the server, see 'server.js'
socket.emit('enter', me.fullname);

// Entered when the server calls socket.broadcast.to('').emit('entered', username);
// another user entered the room
socket.on('entered', function (username){
  console.log(username, 'just entered the public room');
  var user = parseUser(username);
  if (element_exists(username)) {
    $('#' + username).html('<button id="' + username + '_btn">Start video chat with ' + user.name + '</button>');
  }else{
    $('#users').append('<tr><td>' + user.name + '</td>' +
                            '<td>' + user.lang1 + '</td>' +
                            '<td>' + user.lang2 + '</td>' +
                            '<td id="' + username + '">' + 
                                '<button id="' + username + '_btn">Start video chat with ' + user.name + '</button>' + 
                            '</td></tr>');
    $('#' + username + '_btn').click(popup(username));
  }
});

// Entered when the server calls socket.emit('list', clients);
socket.on('list', function (list){
  for(var username in list) {
    var user = parseUser(username);
    if (list[username] === 'online') {
      var row = '<tr><td>' + user.name + '</td>' +
                    '<td>' + user.lang1 + '</td>' +
                    '<td>' + user.lang2 + '</td>' +
                    '<td id="' + username + '">' +
                        '<button id="' + username + '_btn">Start video chat with ' + user.name + '</button>' +
                    '</td></tr>';
      $('#users').append(row);
      $('#' + username + '_btn').click(popup(username));
    } else {  // list[username] is 'busy'
      $('#users').append('<tr><td>' + user.name + '</td>' +
                              '<td>' + user.lang1 + '</td>' +
                              '<td>' + user.lang2 + '</td>' +
                              '<td id="' + username + '">' + list[username] + '</td></tr>');
    }
  }
});

function popup(username) {
  var user = parseUser(username);
  return function() {
    //socket.emit('request', my_name, username);
    socket.emit('request', me.fullname, username);
    $('html').append('<div id="dialog-message" title="Calling...">Waiting for ' + user.name + '\'s response.');
    $("#dialog-message").dialog({
      modal: true,
      buttons: {
        CANCEL: function() {
          //socket.emit('cancel request', my_name, username);
          socket.emit('cancel request', me.fullname, username);
          $(this).dialog( "close" );
          $(this).remove();
        }
      }
    });
  }
}

socket.on('request', function(sender, receiver) {
  //if (receiver === my_name) {
  if (receiver === me.fullname) {
    $('html').append('<div id="dialog-confirm-' + sender + '" title="Accept Call?">' + sender.split('_')[0] + ' is video calling, Accept?');
    if($('audio').length == 0) {
      $('html').append('<audio autoplay loop><source src="apple.mp3" type="audio/mpeg">Your browser does not support the audio element.</audio>');
    }

    $( "#dialog-confirm-" + sender ).dialog({
      modal: true,
      buttons: {
        YES: function() {
          socket.emit('confirm request', receiver, sender);
          $(this).dialog( "close" );
          $(this).remove();
          $('audio').stop().remove();
        },
        NO: function() {
          socket.emit('reject request', receiver, sender);
          $(this).dialog( "close" );
          $(this).remove();
          $('audio').stop().remove();
        }
      }
    });
  }
});

socket.on('cancel request', function(sender, receiver) {
  //if (receiver === my_name) {
  if (receiver === me.fullname) {
    $("#dialog-confirm").dialog("close");
    $("#dialog-confirm").remove();
    $('html').append('<div id="dialog-call-cancelled" title="Call Cancelled">' + sender.split('_')[0] + ' cancelled the call.');
    $("#dialog-call-cancelled").dialog({
      modal: true,
      buttons: {
        OK: function() {
          $(this).dialog( "close" );
          $(this).remove();
          $('audio').stop().remove();
        }
      }
    });
  }
});

socket.on('confirm request', function(sender, receiver) {
  //if (receiver === my_name) {
  if (receiver === me.fullname) {
    $("#dialog-message").dialog("close");
    $("#dialog-message").remove();
    //socket.emit('caller entered room', my_name, sender);
    socket.emit('caller entered room', me.fullname, sender);
    //window.location.replace('/' + my_name);
    window.location.replace('/' + me.fullname);
  }
});

socket.on('caller entered room', function(sender, receiver) {
  //if(receiver === my_name) {
  if(receiver === me.fullname) {
    setTimeout(function() {
      window.location.replace('/' + sender + "." + receiver);
    }, 2000);
  }
});

socket.on('reject request', function(sender, receiver) {
  //if (receiver === my_name) {
  if (receiver === me.fullname) {
    $("#dialog-message").dialog("close");
    $("#dialog-message").remove();
    // alert(sender + ' rejected your request');
    $('html').append('<div id="dialog-reject" title="Call Rejected">' + sender.split('_')[0] + ' rejected your call.');
    $( "#dialog-reject" ).dialog({
      modal: true,
      buttons: {
        OK: function() {
          $(this).dialog( "close" );
          $(this).remove();
        }
      }
    });
    $('#' + sender + '_btn').prop("disabled", false);
  }
});

socket.on('busy', function (username){
  if (element_exists(username)) {
    $('#' + username).text('busy');
  }else{
    var user = parseUser(username);
    $('#users').append('<tr><td>' + user.name + '</td>' +
                            '<td>' + user.lang1 + '</td>' +
                            '<td>' + user.lang2 + '</td>' +
                            '<td id="' + username + '">busy</td></tr>');
  }
});

socket.on('left users page', function (username){
  $("#" + username).closest('tr').remove();
});

socket.on('left video page', function (username){
  $("#" + username).closest('tr').remove();
});

window.onunload = window.onbeforeunload = function(e){
    //socket.emit('left users page', my_name);
    socket.emit('left users page', me.fullname);
}

function element_exists(element_id) {
  return $('#' + element_id).length;
}

function parseUser(fullUsername) {
  // fullUsername format: Roger_1_2-3
  var userSplit = fullUsername.split('_');
  var langs = ["English","Spanish","French","Portuguese","German","Italian","Japanese","Chinese"];

  return {'name':userSplit[0], 
          'lang1':langs[userSplit[1]], 
          'lang2':langs[userSplit[2].split('-')[0]], 
          'uid':userSplit[2].split('-')[1], 
          'fullname':fullUsername};
}

