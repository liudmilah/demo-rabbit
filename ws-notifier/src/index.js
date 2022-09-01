const express = require('express');
const app = express();
const http = require('http');
const server = http.createServer(app);
const { Server } = require('socket.io');
const io = new Server(server);
const amqp = require('amqplib/callback_api');

server.listen(3000, () => {
    console.log('server listening on 3000');
});

app.get('/', (req, res) => {
    res.send('<h1>Hi!</h1>');
});

io.on('connection', (socket) => {
    console.log('a user connected');

    socket.on('disconnect', () => {
        console.log('user disconnected');
    });

    socket.on('join', (roomId) => {
        socket.join(`room.${roomId}`);
    });
});

amqp.connect('amqp://user:secret@rabbit-mq:5672', function(error0, connection) {
    if (error0) {
        throw error0;
    }

    connection.createChannel(function(error1, channel) {
        if (error1) {
            throw error1;
        }

        const exchange = 'notifications';

        channel.assertExchange(exchange, 'topic', { durable: false });

        channel.assertQueue('', { exclusive: true }, function(error2, q) {
            if (error2) {
                throw error2;
            }

            channel.bindQueue(q.queue, exchange, '#');

            channel.consume(q.queue, handleReceivedMessage, { noAck: true });
        });
    });
});

function handleReceivedMessage(msg) {
    const { event, data } = JSON.parse(msg.content.toString());

    console.log('received message, key: %s, event: %s, data: %s', msg.fields.routingKey, event, data);

    if (msg.fields.routingKey === 'all') {
        io.emit(event, data);
    } else {
        io.to(msg.fields.routingKey).emit(event, data);
    }
}
