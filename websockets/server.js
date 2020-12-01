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
var roomUsers = new Map()
// This is an observer that logs user changes
io.on('connection', function (socket) {

	let myData

	socket.on('joinsession', (data) => {
		myData = data
		console.log(data.rcs + " joining session " + data.room)

		socket.join(data.room);
		var room = io.sockets.adapter.rooms.get(data.room);
		console.log("\t" + room.size + " user(s) connected")

		if (room.size == 1) { //first to join room!
			roomData.set(data.room, data.document)
			roomUsers.set(data.room, [data])
		} else {
			let usrs = roomUsers.get(data.room)
			usrs.push(data)
			roomUsers.set(data.room,usrs)
		}

		io.in(data.room).emit('usersupdate', roomUsers.get(data.room)); //update clients' information
        
        socket.on('draw', function (data) {
            console.log(data.rcs + ' doodled')
            io.in(myData.room).emit('draw', data);	
		});

		socket.on('disconnect', function () {
			let usrs = roomUsers.get(data.room)
			for(let i=0;i<usrs.length;i++) {
				if(usrs[i].rcs == data.rcs) {
					usrs.splice(i,1)
					break
				}
			}
			roomUsers.set(data.room,usrs)
			console.log(data.rcs + ' disconnected from ' + data.room)
			io.in(myData.room).emit('usersupdate', roomUsers.get(data.room));
		});
	})
})

ioserver.listen(port+1);
app.listen(port,function() {
    console.log("Websockets running on http://localhost:"+(port+1))
})

module.exports = app;