self.addEventListener('push', function (event) {
    if (!event.data) {
        return;
    }

    try {
        const payload = event.data.json();
        const title = payload.title || 'Notifikasi Baru';
        const options = {
            body: payload.body || '',
            icon: payload.icon || '/dist/img/AdminLTELogo.png',
            badge: payload.badge || '/dist/img/AdminLTELogo.png',
            data: {
                url: payload.url || '/'
            }
        };

        event.waitUntil(
            self.registration.showNotification(title, options)
        );
    } catch (e) {
        console.warn('Failed to parse push data as JSON. Using fallback.', e);
        
        // Fallback for text payloads
        const text = event.data.text();
        event.waitUntil(
            self.registration.showNotification('Notifikasi Baru', {
                body: text,
                icon: '/dist/img/AdminLTELogo.png',
                data: {
                    url: '/'
                }
            })
        );
    }
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    const targetUrl = event.notification.data ? event.notification.data.url : '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then(function (clientList) {
                // If a window is already open on this URL, focus it
                for (let i = 0; i < clientList.length; i++) {
                    const client = clientList[i];
                    if (client.url === targetUrl && 'focus' in client) {
                        return client.focus();
                    }
                }
                // Otherwise open a new window
                if (clients.openWindow) {
                    return clients.openWindow(targetUrl);
                }
            })
    );
});
