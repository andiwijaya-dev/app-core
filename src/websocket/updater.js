#!/usr/bin/env node

var fs = require('fs');
var privateKey  = fs.readFileSync('./sslcert/private_key.pem');
var certificate = fs.readFileSync('./sslcert/server.pem');
var credentials = {key: privateKey, cert: certificate};

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
  host:"andiwijaya.me",
  port:8443,
}
http.listen(options, function(){
  console.log('listening...');
});

var channels = [];

io.on('connection', function (socket) {

  console.log("New connection: " + socket.id);

  socket.emit('connected', "Connected with id: " + socket.id);

  socket.on('join', function(channel){

    var redisClient = redis.createClient();

    redisClient.subscribe(channel);

    channels[channel] = redisClient;

    console.log("Connected to redis: " + channel);

    redisClient.on("message", function(channel, message) {

      console.log('[' + (new Date().toISOString()) + '] ' + channel + ': ' + message);

      socket.emit('notify', channel, message);

    });

    socket.on('disconnect',function(){
      redisClient.quit();
    });

  });

  socket.on('leave', function(channel){

    var redisClient = channels[channel];

    redisClient.unsubscribe(channel);

    channels.splice(channels.indexOf(channel), 1);

    console.log("Disconnected from redis: " + channel);

  })

});

