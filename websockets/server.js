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
	socket.on('joinsession', function () {
		console.log("\tjoining session")
        
        socket.on('draw', function () {
            console.log('User doodled')
            socket.emit('draw')
		});

		socket.on('leavesession', function () {
			console.log('User disconnected from session')
		});
	})
})

ioserver.listen(port+1);
app.listen(port,function() {
    console.log("Websockets running on http://localhost:"+(port+1))
})

module.exports = app;