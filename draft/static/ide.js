var host = 'ws://127.0.0.1:44321/websockets.php';
var socket = new WebSocket(host);

socket.addEventListener("message", (event) => {
    document.getElementById('root').innerHTML = "Message from server "+ event.data;
});

socket.addEventListener("open", (event) => {
    var message = {'a':'a1','b':'b2'};
    socket.send(JSON.stringify(message));
    document.getElementById('root').innerHTML = JSON.stringify(message);
});

socket.addEventListener("error", (event) => {
    console.log("WebSocket error: ", event);
});
