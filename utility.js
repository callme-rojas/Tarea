var client = null;
var connected = false;
topics = ['boton_bool','valor_analog'];

connect();


// called when the client connects
function onConnect(context) {
  // Once a connection has been made, make a subscription and send a message.
  var connectionString = context.invocationContext.host + ":" + context.invocationContext.port + context.invocationContext.path;
  logMessage("INFO", "Connection Success ", "[URI: ", connectionString, ", ID: ", context.invocationContext.clientId, "]");
  var statusSpan = document.getElementById("connectionStatus");
  statusSpan.innerHTML = "Conectado : " + connectionString + " como " + context.invocationContext.clientId;
  connected = true;
  setFormEnabledState(true);
}


function onConnected(reconnect, uri) {
  // Once a connection has been made, make a subscription and send a message.
  logMessage("INFO", "Client Has now connected: [Reconnected: ", reconnect, ", URI: ", uri, "]");
  connected = true;


  for (const topic of topics){
    subscribe(topic,0);
  }

  subscribe('world',0)
}

function onFail(context) {
  logMessage("ERROR", "Failed to connect. [Error Message: ", context.errorMessage, "]");
  var statusSpan = document.getElementById("connectionStatus");
  statusSpan.innerHTML = "Error al conectar: " + context.errorMessage;
  connected = false;
  setFormEnabledState(false);
}

// called when the client loses its connection
function onConnectionLost(responseObject) {
  if (responseObject.errorCode !== 0) {
    logMessage("INFO", "Connection Lost. [Error Message: ", responseObject.errorMessage, "]");
  }
  connected = false;
}

// called when a message arrives
function onMessageArrived(message) {
  logMessage("INFO", "Message Recieved: [Topic: ", message.destinationName, ", Payload: ", message.payloadString, ", QoS: ", message.qos, ", Retained: ", message.retained, ", Duplicate: ", message.duplicate, "]");
  var messageTime = new Date().toISOString();

  payload = safeTagsRegex(message.payloadString);
  if (message.payloadString==='0'){
    payload = false;
  } else{
    if (message.payloadString==='1'){
      payload = true;
    }
  }

  if(typeof payload === 'boolean'){
    document.getElementById(message.destinationName).checked = payload;
  }

  //document.getElementById(message.destinationName).innerHTML = payload;

  if(message.destinationName.includes("valor_analog"))
  {
    document.getElementById('valor_analog').innerHTML = (message.payloadString);
  }


  
}

function connect() {
  var hostname = 'localhost';
  var port = '9001';
  var clientId = "js-utility-" + makeid();
  var path = '/ws';
  var user = '';
  var pass = '';
  var keepAlive = 60;
  var timeout = 3;
  var tls = false;
  var automaticReconnect = true;
  var cleanSession = true;
  var lastWillTopic = '';
  var lastWillQos = 0;
  var lastWillRetain = false;
  var lastWillMessageVal = '';

  client = new Paho.Client(hostname, Number(port), path, clientId);

  logMessage("INFO", "Connecting to Server: [Host: ", hostname, ", Port: ", port, ", Path: ", client.path, ", ID: ", clientId, "]");

  // set callback handlers
  client.onConnectionLost = onConnectionLost;
  client.onMessageArrived = onMessageArrived;
  client.onConnected = onConnected;


  var options = {
    invocationContext: { host: hostname, port: port, path: client.path, clientId: clientId },
    timeout: timeout,
    keepAliveInterval: keepAlive,
    cleanSession: cleanSession,
    useSSL: tls,
    reconnect: automaticReconnect,
    onSuccess: onConnect,
    onFailure: onFail
  };



  if (user.length > 0) {
    options.userName = user;
  }

  if (pass.length > 0) {
    options.password = pass;
  }

  if (lastWillTopic.length > 0) {
    var lastWillMessage = new Paho.Message(lastWillMessageVal);
    lastWillMessage.destinationName = lastWillTopic;
    lastWillMessage.qos = lastWillQos;
    lastWillMessage.retained = lastWillRetain;
    options.willMessage = lastWillMessage;
  }

  // connect the client
  client.connect(options);
  var statusSpan = document.getElementById("connectionStatus");
  statusSpan.innerHTML = "Conectando...";
}

function disconnect() {
  logMessage("INFO", "Disconnecting from Server.");
  client.disconnect();
  var statusSpan = document.getElementById("connectionStatus");
  statusSpan.innerHTML = "Conexion - Desconectado.";
  connected = false;
  setFormEnabledState(false);

}

// Sets various form controls to either enabled or disabled
function setFormEnabledState(enabled) {

  // Connection Panel Elements
  if (enabled) {
    document.getElementById("clientConnectButton").innerHTML = "Desconectar";
  } else {
    document.getElementById("clientConnectButton").innerHTML = "Conectar";
  }

  // Publish Panel Elements
  document.getElementById("publishButton").disabled = !enabled;
  document.getElementById("publishButton2").disabled = !enabled;


}

function publish(topic, qos, message, retain) {

  logMessage("INFO", "Publishing Message: [Topic: ", topic, ", Payload: ", message, ", QoS: ", qos, ", Retain: ", retain, "]");
  message = new Paho.Message(message);
  message.destinationName = topic;
  message.qos = Number(qos);
  message.retained = retain;
  client.send(message);
}


function subscribe(topic, qos) {

  logMessage("INFO", "Subscribing to: [Topic: ", topic, ", QoS: ", qos, "]");
  client.subscribe(topic, { qos: Number(qos) });
}

function unsubscribe(topic) {
  logMessage("INFO", "Unsubscribing: [Topic: ", topic, "]");
  client.unsubscribe(topic, {
    onSuccess: unsubscribeSuccess,
    onFailure: unsubscribeFailure,
    invocationContext: { topic: topic }
  });
}


function unsubscribeSuccess(context) {
  logMessage("INFO", "Unsubscribed. [Topic: ", context.invocationContext.topic, "]");
}

function unsubscribeFailure(context) {
  logMessage("ERROR", "Failed to unsubscribe. [Topic: ", context.invocationContext.topic, ", Error: ", context.errorMessage, "]");
}


// Just in case someone sends html
function safeTagsRegex(str) {
  return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").
    replace(/>/g, "&gt;");
}

function makeid() {
  var text = "";
  var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

  for (var i = 0; i < 5; i++)
    text += possible.charAt(Math.floor(Math.random() * possible.length));

  return text;
}

function logMessage(type, ...content) {

  var date = new Date();
  var timeString = date.toUTCString();
  var logMessage = timeString + " - " + type + " - " + content.join("");
 
  if (type === "INFO") {
    console.info(logMessage);
  } else if (type === "ERROR") {
    console.error(logMessage);
  } else {
    console.log(logMessage);
  }
}

