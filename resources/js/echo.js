import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

// Create Pusher instance with debug mode
Pusher.logToConsole = true;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '0renst1fsvqti4xpuh6j',  // Your REVERB_APP_KEY
    cluster: 'mt1',  // Required by Pusher client
    wsHost: '127.0.0.1',
    wsPort: 8084,
    wssPort: 8084,
    forceTLS: false,
    disableStats: true,
    enabledTransports: ['ws'],
    encrypted: false,
    scheme: 'http',
    authorizer: (channel, options) => {
        return {
            authorize: (socketId, callback) => {
                console.log('Authorizing channel:', channel.name);
                axios.post('/broadcasting/auth', {
                    socket_id: socketId,
                    channel_name: channel.name
                })
                .then(response => {
                    console.log('Authorization successful:', response.data);
                    callback(null, response.data);
                })
                .catch(error => {
                    console.error('Authorization failed:', error);
                    callback(error);
                });
            }
        };
    }
});

// Debug connection status
window.Echo.connector.pusher.connection.bind('connecting', () => {
    console.log('Connecting to Reverb...');
});

window.Echo.connector.pusher.connection.bind('connected', () => {
    console.log('Successfully connected to Reverb!');
});

window.Echo.connector.pusher.connection.bind('disconnected', () => {
    console.log('Disconnected from Reverb');
});

window.Echo.connector.pusher.connection.bind('error', (error) => {
    console.error('Reverb connection error:', error);
});
