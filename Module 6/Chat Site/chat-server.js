// Require the packages we will use:
var http = require("http"),
	socketio = require("socket.io"),
	fs = require("fs");

// Listen for HTTP connections.  This is essentially a miniature static file server that only serves our one file, client.html:
var app = http.createServer(function (req, resp) {
	// This callback runs when a new connection is made to our HTTP server.

	fs.readFile("client.html", function (err, data) {
		// This callback runs when the client.html file has been read from the filesystem.

		if (err) return resp.writeHead(500);
		resp.writeHead(200);
		resp.end(data);
	});
});
app.listen(3456);

var current_chat = null;
var id = null;
var rooms = ["main_room"];
var main = "main_room";
var owners = {};
var passwords = {};
var ban_user = {};
var chat_userids_map = {};
var io = socketio.listen(app);

io.sockets.on("connection", function (socket) {
	// This callback runs when a new Socket.IO connection is established.
	socket.on('message_to_server', function (data) {
		// This callback runs when the server receives a new message from the client.
		console.log("message from " + data.username + ": " + data.message);
		io.in(chat_userids_map[data.username]).emit("message_to_client", { message: data.message, username: data.username }); // broadcast the message to other users
	});
	//called by the create room button when permission allows (always in this case)
	socket.on("createRoom", function (data) {
		var room = data.roomName;
		var pass = data.roomPass;
		var own = data.owner;
		rooms.push(room);
		passwords[room] = pass;
		owners[room] = own;
		console.log("new room created called: " + room + " with password " + pass);
		io.in(chat_userids_map[own]).emit("createdRoom", { password: pass, roomname: room, owner: own });
	});
	//called when a new user logs on
	socket.on("new_user", function (data) {
		socket.username = data.username;
		socket.room = data.current;
		chat_userids_map[socket.username] = socket.room;
		socket.join(main);
		passwords[main] = "";
		console.log(socket.username + " has just entered the room " + socket.room);
		io.in(socket.room).emit('joinedChat', { username: socket.username });
		io.in(socket.room).emit("showMembers", { room: socket.room, users: chat_userids_map });

	});
	//called by the join room button when permission allows (always in this case)
	socket.on("joinChat", function (data) {
		if (data.password == passwords[data.name]) {
			var roomName = data.name;
			var previousRoom = socket.room;
			socket.leave(previousRoom);
			socket.room = roomName;
			socket.join(roomName);
			console.log(data.user + " has joined the room " + data.name);
			chat_userids_map[data.user] = roomName;
			io.in(roomName).emit('joinedChat', { username: socket.username, room: roomName });
			io.in(previousRoom).emit('leftChat', { username: socket.username, room: previousRoom });
			io.in(roomName).emit("showMembers", { room: roomName, users: chat_userids_map });
			io.in(previousRoom).emit("showMembers", { room: previousRoom, users: chat_userids_map });
		}
	});
	//called by the kick person button when permission allows (if not kicking themself)
	socket.on("kickHim", function (data) {
		var kicked = data.kicked;
		var kicker = data.kicker;
		var room = data.room;
		console.log(kicker + owners[room] + room);
		if (kicker == owners[room]) {
			if (room == chat_userids_map[kicked]) {
				chat_userids_map[kicked] = "main_room";
				console.log(kicked + " has been kicked from the chat " + room + " by " + kicker);
				io.in(room).emit('kickHim2', { kicked: kicked, kicker: kicker, room: room });
				io.in(room).emit("showMembers", { room: socket.room, users: chat_userids_map });
				io.in(main).emit("showMembers", { room: main, users: chat_userids_map });
			}
		}
	});
	//called by the ban person button when permission allows (if not kicking themself)
	socket.on("banHim", function (data) {
		var banned = data.banned;
		var banner = data.banner;
		var room = data.room;
		console.log(banner + owners[room] + room);
		if (banner == owners[room]) {
			if (room == chat_userids_map[banned]) {
				chat_userids_map[banned] = "main_room";
				console.log(banned + " has been banned from the chat " + room + " by " + banner + chat_userids_map[banned]);
				io.in(room).emit('banHim2', { banned: banned, banner: banner, room: room });
				io.in(room).emit("showMembers", { room: socket.room, users: chat_userids_map });
				io.in(main).emit("showMembers", { room: main, users: chat_userids_map });
			}
		}
	});
	//called by the change owner button when permission allows (if they are owner and not changing owner to themself)
	socket.on("changeOwner", function (data) {
		var room = data.room;
		var newOwner = data.newOwner;
		var oldOwner = data.oldOwner;
		console.log(oldOwner + " has given ownership of the room " + room + " to " + newOwner);
		owners[room] = "";
		owners[room] = newOwner;
		io.in(room).emit('changeOwner2', { room: room, newOwner: newOwner, oldOwner: oldOwner });
	});
	//called by the change password button when permission allows (if they are owner and not changing password to the same)
	socket.on("changePass", function (data) {
		var room = data.room;
		var owner = data.owner;
		var newPass = data.newPass;
		console.log(owner + " has changed the password of the room " + room + " to " + newPass);
		passwords[room] = "";
		passwords[room] = newPass;
		io.sockets.emit('changePass2', { room: room, owner: owner, newPass: newPass });
	});
	//called by the private message button when permission allows (always in this case)
	socket.on('message_to_one', function (data) {
		var user = data.username;
		var message = data.message;
		var receiver = data.receiver;
		if (chat_userids_map[user] == chat_userids_map[receiver]) {
			console.log("message from " + user + " to " + receiver + ": " + message);
			io.sockets.emit("message_to_one2", { username: user, message: message, receiver: receiver });
		}
	});
});