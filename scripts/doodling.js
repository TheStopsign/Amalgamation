// import "../node_modules/socket.io-client/dist/socket.io"

function joinSession() {

    sock.on('draw', (drawing_data) => {
        console.log("someone drew on the canvas!")
    });

    sock.on('connect', () => {
        console.log("connected to editing session")
        sock.emit("joinsession");
    });

    window.addEventListener("beforeunload", function() {
        sock.emit("disconnect");
    });
}