#!/usr/bin/env node

var fs = require('fs');
/*var privateKey  = fs.readFileSync('./sslcert/private_key.pem');
var certificate = fs.readFileSync('./sslcert/server.pem');
var credentials = {key: privateKey, cert: certificate};*/

var app = require('express')();
//var http = require('https').Server(credentials, app);
var http = require('http').Server(app);
var io = require('socket.io')(http, {
  pingTimeout: 60000,
  pingInterval: 5000
});
var redis = require('redis');


app.get('/', function(req, res){
  res.sendFile(__dirname + '/index.html');
});

var options = {
  port:8443,
}
http.listen(options, function(){
  console.log('listening...');
});

var channels = [];

io.on('connection', function (socket) {

  console.log('[' + (new Date().toISOString()) + '] New connection with ID: ' + socket.id);

  socket.emit('connected', "Connected with id: " + socket.id);


  /**
   * Join event
   */
  socket.on('join', function(channel){

    var redisClient = redis.createClient();

    redisClient.subscribe(channel);

    channels[channel] = redisClient;

    console.log('[' + (new Date().toISOString()) + '] Joined to channel: ' + channel);

    redisClient.on("message", function(channel, message) {

      //console.log('[' + (new Date().toISOString()) + '] ' + channel + ': ' + message);
      console.log('[' + (new Date().toISOString()) + '] New message for channel: ' + channel);

      socket.emit('notify', channel, message);

    });

    socket.on('disconnect',function(){

      console.log('[' + (new Date().toISOString()) + '] Socket ' + socket.id + ' disconnected.')

      redisClient.quit();

    });

  });

  /**
   * On leave event
   */
  socket.on('leave', function(channel){

    var redisClient = channels[channel];

    if(typeof redisClient == 'undefined') return;

    redisClient.unsubscribe(channel);

    channels.splice(channels.indexOf(channel), 1);

    console.log('[' + (new Date().toISOString()) + '] Disconnected from channel: ' + channel);

  })

});
