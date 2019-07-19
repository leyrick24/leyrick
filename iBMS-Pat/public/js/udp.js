/*
* <System Name> iBMS
* <Program Name>
*
* <Create> 2018.06.21 TP Bryan
* <Update>
*
*/

//Get .env File Data
require('dotenv').config({path: '/usr/share/nginx/html/iBMS/.env'});
var request = require('request');

var port = process.env.PORT_GATEWAY;
var host = process.env.IP_PUSH;

var dgram = require('dgram');
var server = dgram.createSocket('udp4');

server.on('listening', function () {
    var address = server.address();
    console.log('UDP Server listening on ' + address.address + ":" + address.port);
});

server.on('message', function(data, remote) {
    console.log(remote.address + ':' + remote.port +' - ' + data);

    var data = JSON.parse(data.toString());
    var url = data['url'];
    console.log(data);
    sendArr = {
        "MESSAGE" : data['message'],
        "IV" :      data['iv_key'],
        "GATEWAY_IP" : remote.address,
        "URL" :      data['url'],
    }

    console.log(url);
    console.log(sendArr);
    sendRequest(url, sendArr);

});

server.bind(port, host);

function sendRequest(url, data) {
    var address = "http://" + host + "/api/" + url;
    request.post({url: address, formData: data}, function optionalCallback(err, httpResponse, body) {
        if (err) {
            return console.error('upload failed:', httpResponse);
        }else{
        console.log('Server responded with:', body);
        }
    });
}