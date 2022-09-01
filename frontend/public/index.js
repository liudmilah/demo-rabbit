(function() {
    initWS();
    initButtons();
})();

function initWS() {
    const socket = io('ws://rabbit-frontend.localhost', { path: '/ws/socket.io/' });

    socket.on('connect', () => {
        console.log('WS::connected ', socket.id);
        socket. emit('join', '1');
    });

    socket.on('disconnect', (reason) => console.log('WS::disconnected', reason));

    socket.on('PUBLIC_NOTIFICATION_CREATED', (payload) => {
        renderFormattedJson(document.getElementById('ws-broadcast'), payload);
    });

    socket.on('ROOM_NOTIFICATION_CREATED', (payload) => {
        renderFormattedJson(document.getElementById('ws-room'), payload);
    });
}

function initButtons() {
    const items = [
        { id: 'btn-notify-room', url: '/api/notify-room', method: 'POST' },
        { id: 'btn-notify-all', url: '/api/notify-all', method: 'POST' },
        { id: 'btn-job-email', url: '/api/job-email', method: 'POST' },
        { id: 'btn-job-convert', url: '/api/job-convert', method: 'POST' },
        { id: 'btn-rpc', url: '/api/user-info', method: 'GET' },
    ];

    items.forEach((item) =>
        document.getElementById(item.id).addEventListener('click', sendRequest(item.url, item.method))
    );
}

function sendRequest(url, method) {
    return () => fetch(url, { method: method })
        .then((res) => res.json())
        .then((json) => renderFormattedJson(document.getElementById('server-response'), json));
}

function renderFormattedJson(el, data) {
    const pre = document.createElement('pre');
    pre.innerHTML = JSON.stringify(data, undefined, 2);

    el.append(pre);
    el.scrollTop = el.scrollHeight;
}
