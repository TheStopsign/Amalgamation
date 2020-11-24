const express = require('express')
const cors = require('cors')
const port = 3000

var app = express();

var corsOptions = {
    origin: '* null',
}

app.use(cors(corsOptions))

var socketio = require('socket.io');
var ioserver = require('http').createServer(app);
var allowedOrigins = "http://localhost:* http://127.0.0.1:*";

var io = socketio(ioserver, {
	origins: allowedOrigins
});

var roomData = new Map() //will be used to actually make changes to things

// This is an observer that logs user changes
io.on('connection', function (socket) {
	console.log("User connected")
	socket.on('joinsession', function (data) {
		console.log("\tjoining session")

		socket.join(data.room);
		var room = io.sockets.adapter.rooms.get(data.room);
		console.log("\t" + room.size + " user(s) connected")
		io.in(data.room).emit('usercount', room.size); //update clients' information

		if (room.length == 1) { //first to join room!
			roomData.set(data.room, data.document)
		}
        
        socket.on('draw', function () {
            console.log('User doodled')
            io.in(data.room).emit('draw')
		});

		socket.on('disconnect', function () {
			io.in(data.room).emit('usercount', room.size);
			console.log('User disconnected from ' + data.room)
			// if (room.length == 0) {
			// 	updateFromHistory(data.room)
			// }
		});
	})
})

ioserver.listen(port+1);
app.listen(port,function() {
    console.log("Websockets running on http://localhost:"+(port+1))
})

module.exports = app;