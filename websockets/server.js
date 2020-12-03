const express = require('express')
const cors = require('cors')
var mysql = require('mysql');
var conn = mysql.createConnection ( {  
	host: "localhost",
	user: "root",
	password: "",
	database: "amalgamation"
} );
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

		socket.join(data.room);
		var room = io.sockets.adapter.rooms.get(data.room);
		
		let getStatement = "SELECT * FROM projects WHERE ProjectID = '"+data.room+"'"
		conn.query(getStatement, function (err, result) {
			if (err) throw err;
			
			if (room.size == 1) { //first to join room!
				let parsed = JSON.parse(result[0].history)
				if(parsed) {
					roomData.set(data.room,parsed.history)
				} else {
					roomData.set(data.room,[])
				}
				roomUsers.set(data.room, [data])
			} else {
				let usrs = roomUsers.get(data.room)
				usrs.push(data)
				roomUsers.set(data.room,usrs)
			}

			socket.emit("loadsessionchanges", roomData.get(data.room))
			io.in(data.room).emit('usersupdate', roomUsers.get(data.room)); //update clients' information
			
			socket.on('draw', function (data) {
				let history = roomData.get(myData.room)
				history.push(data)
				roomData.set(myData.room,history)
				socket.to(myData.room).emit('draw', data);
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

				if(room.size == 0) {

					console.log(roomData.get(myData.room))
					let updateStatement = "UPDATE projects SET history = '" + JSON.stringify({history:roomData.get(data.room)}) + "' WHERE ProjectID = " + data.room
					console.log(updateStatement)
					conn.query(updateStatement, function (err, result) {
						if (err) throw err;
						roomData.delete(data.room)
					});

					roomUsers.delete(data.room)
				}

				io.in(myData.room).emit('usersupdate', roomUsers.get(data.room));
			});

		});
	})
})

ioserver.listen(port+1);
app.listen(port,function() {
    console.log("Websockets running on http://localhost:"+(port+1))
})

module.exports = app;