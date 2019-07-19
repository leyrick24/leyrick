/*
* <System Name> iBMS
* <Program Name> socket.js
*
* <Create> 2018.06.22 TP Bryan
* <Update>
*
*/

/**
 * Instantiate socket.io server
 *
 * Make sure socket.io is installed
 */
var app = require('express')();
var http = require('https').Server(app);
http.globalAgent.options.rejectUnauthorized = false;
var io = require('socket.io')(http);

/**
 * Instantiate redis client
 *
 * Check .env file BROADCAST_DRIVER=redis
 */
var redis = require("redis");
var client = redis.createClient();

/**
 * Subscribe to channels
 *
 * '*' refers to wildcard (connect to any channel)
 */
client.psubscribe('*', function(pattern, count) {
    console.log("Subscription on: " + pattern);
});

/**
 * Receive message from 'channel'
 *
 * Broadcasted from Laravel
 */
client.on('pmessage', function(pattern, channel, message) {
    console.log("Message Received: " + message + ", From: " + channel + " With: " + pattern);
    io.emit(channel, message);
});

/**
 * Listen to port
 */
var port = 3000;
http.listen(port);
console.log("Listening to Port " + port);