var static = require('node-static');
var http = require('http');
var file = new(static.Server)();
var app = http.createServer(function (req, res) {
  console.log("Server is listening...");

  file.serve(req, res, function (err, result) {
    if (err) {
      if(req.url.substr(0, 6) === '/user=') {
	      file.serveFile('/users.html', 200, {}, req, res);
      } else if (req.url.indexOf('_') == -1) {  // caller opens video chat
        file.serveFile('/videochat.html', 200, {}, req, res);
      } else if (req.url.indexOf('_') > 0) {    // callee joins video chat
        file.serveFile('/videochat2.html', 200, {}, req, res);
      } else {
	      file.serveFile('/index.html', 200, {}, req, res); 
      }
    }
  });
}).listen(2013);


// Associative array on the server to store users' status, key is username, value is status
var clients = {};

var io = require('socket.io').listen(app, { log: false });

io.sockets.on('connection', function (socket){
  
  // When the server receives a message, it will broadcast the message to all the other users
  socket.on('message', function (message) {
    socket.broadcast.emit('message', message);
  });
  
  // Function to check if an entered username already exists or not
  socket.on('check uniqueness', function (username) {
    if (username in clients) {
      socket.emit('unique', false);
    } else {
      socket.emit('unique', true);
    }
  });
  
  // Called when a user opens the users.html page
  socket.on('enter', function (username) {
    console.log(username, 'has entered the public room.');
    socket.join('');	// Room '' is considered as the public room, where users are 'online'
    
    console.log(io.sockets.clients('').length, 'clients are in the public room.');
    
    socket.emit('list', clients);	// The array that lists users and their online status is sent as the response
    socket.broadcast.to('').emit('entered', username);	// Notify other users (in the public room) that this user has come online
    clients[username] = 'available';	// Add the new user to array
    console.log(clients);
  });
  
  
  // Entered when the client calls: socket.emit('available', username);
  socket.on('available', function (username) {
    clients[username] = 'available';
    socket.broadcast.to('').emit('available', username);
  });
  
  // Entered when the client calls: socket.emit('create', room);
  // Same rules apply below...
  socket.on('create', function (room) {
    var numClients = io.sockets.clients(room).length;
    if (numClients == 0){
      socket.join(room);
      socket.emit('created', room);
    } else {
      console.log('Room ', room, ' already exists.');
    }
  });
  
  socket.on('request', function (sender, receiver) {
    console.log(sender + ' requesting videochat with ' + receiver);
    io.sockets.emit('request', sender, receiver);
  });
  
  socket.on('confirm request', function (sender, receiver) {
    console.log('receiver:'+receiver+' has confirmed request from:'+sender);
    socket.broadcast.to('').emit('confirm request', sender, receiver);
  });
  
  socket.on('reject request', function (sender, receiver) {
    socket.broadcast.to('').emit('reject request', sender, receiver);
  });
  
  socket.on('join', function (room, username) {
    var numClients = io.sockets.clients(room).length;
    if (numClients == 1){
      io.sockets.in(room).emit('join', room, username);
      socket.join(room);
      socket.emit('joined', room);
      var room_owner = room;
      clients[room_owner] = 'busy';
      clients[username] = 'busy';
      io.sockets.in('').emit('busy', room_owner);
      io.sockets.in('').emit('busy', username);
    } else if(numClients > 1) {
      console.log('Room ', room, ' is full.');
      socket.emit('full', room);
    }
  });
  
  socket.on('left users page', function (username) {
    socket.leave('');
    console.log(io.sockets.clients('').length, 'clients are in public room');
    delete clients[username];
    io.sockets.in('').emit('left users page', username);
  });
  
  socket.on('left video page', function (room, username) {
    socket.leave(room);
    console.log(io.sockets.clients(room).length, 'clients are in room: ' + room);
    delete clients[username];
    socket.broadcast.to('').emit('left video page', username);
    socket.broadcast.to(room).emit('left room', username);
  });
});

