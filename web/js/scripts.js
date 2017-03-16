var webSocket = WS.connect("ws://127.0.0.1:3000");
var date;
var time_string;

webSocket.on("socket/connect", function(session){

    session.subscribe("app/chat/chatroom", function(uri, payload){
        // Check for history messages
        if(payload.history){
            payload.history.forEach(function (item) {
                // Convert date and time
                date = new Date(item.postedAt.date);
                time_string = date.getHours() + ':' + date.getUTCMinutes() + ':' + date.getUTCSeconds();
                display_message(date.toDateString() + ' ' + time_string, item.message);
            })
        } else {
            display_message(payload.created_at, payload.msg);
        }
    });

    // Sent message on form submit
    $('#message').on('submit', function (e) {
        // Stop submitting form
        e.preventDefault();
        var selector = $('textarea');
        // Check if message field is not empty
        if(selector.val()){
            // Send message
            session.publish("app/chat/chatroom", selector.val());
            // Clear message field
            selector.val('');
        }

    });
});

webSocket.on("socket/disconnect", function(error){
    console.log("Disconnected for " + error.reason + " with code " + error.code);
});

function display_message(time_string, message) {
    $('.panel-body').append('<p class="msg"><span>[' + time_string +']: </span>' + message + '</p>')
}