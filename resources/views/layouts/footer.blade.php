<footer class="main-footer">
    <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <b>Version</b> 3.2.0
    </div>
</footer>

<aside class="control-sidebar control-sidebar-dark"></aside>
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<!-- jQuery UI -->
<script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<script>$.widget.bridge('uibutton', $.ui.button)</script>
<!-- Bootstrap 4 -->
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- ChartJS -->
<script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>
<!-- Sparkline -->
<script src="{{ asset('plugins/sparklines/sparkline.js') }}"></script>
<!-- JQVMap -->
<script src="{{ asset('plugins/jqvmap/jquery.vmap.min.js') }}"></script>
<script src="{{ asset('plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
<!-- jQuery Knob -->
<script src="{{ asset('plugins/jquery-knob/jquery.knob.min.js') }}"></script>
<!-- Moment + Daterangepicker -->
<script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
<!-- Tempusdominus -->
<script src="{{ asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
<!-- Summernote -->
<script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
<!-- overlayScrollbars -->
<script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
<!-- AdminLTE -->
<script src="{{ asset('dist/js/adminlte.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
<!-- DataTables -->
 <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<!-- SweetAlert2 -->
<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- App Global JS -->
<script src="{{ asset('js/app.js') }}"></script>

{{-- Notifikasi untuk Pelajar --}}
@auth
    <style>
        .toast-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            width: 350px;
            background: #fff;
            border-left: 4px solid #3490dc;
            border-radius: 6px;
            padding: 16px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }
        .toast-notification.success { border-left-color: #28a745; }
        .toast-notification.info { border-left-color: #17a2b8; }
        .toast-notification.warning { border-left-color: #ffc107; }
        .toast-notification.danger { border-left-color: #dc3545; }
    </style>
    
    <script>
        // Track seen notifications to prevent duplicate toasts
        let seenNotifications = JSON.parse(localStorage.getItem('seen_notifications') || '[]');
        
        function formatRelativeTime(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMins / 60);
            
            if (diffMins < 1) return 'Baru saja';
            if (diffMins < 60) return `${diffMins} menit yang lalu`;
            if (diffHours < 24) return `${diffHours} jam yang lalu`;
            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
        }
        
        function getNotificationIcon(type, data) {
            if (type === 'App\\Notifications\\NewContentNotification') {
                if (data.content_type === 'assignment') return 'fas fa-file-alt text-primary';
                if (data.content_type === 'announcement') return 'fas fa-bullhorn text-info';
                if (data.content_type === 'quiz') return 'fas fa-question-circle text-warning';
            }
            if (type === 'App\\Notifications\\AssignmentReminder') return 'fas fa-clock text-danger';
            if (type === 'App\\Notifications\\AssignmentGradedNotification') return 'fas fa-star text-success';
            return 'fas fa-bell text-secondary';
        }
        
        function getNotificationColorClass(type, data) {
            if (type === 'App\\Notifications\\NewContentNotification') {
                if (data.content_type === 'assignment') return 'primary';
                if (data.content_type === 'announcement') return 'info';
                if (data.content_type === 'quiz') return 'warning';
            }
            if (type === 'App\\Notifications\\AssignmentReminder') return 'danger';
            if (type === 'App\\Notifications\\AssignmentGradedNotification') return 'success';
            return 'info';
        }

        function showDynamicToast(notif) {
            const id = notif.id;
            const data = notif.data;
            const icon = getNotificationIcon(notif.type, data);
            const colorClass = getNotificationColorClass(notif.type, data);
            
            let title = 'Notifikasi Baru';
            if (notif.type === 'App\\Notifications\\NewContentNotification') {
                if (data.content_type === 'assignment') title = 'Tugas Baru 📝';
                if (data.content_type === 'announcement') title = 'Pengumuman Baru 📢';
                if (data.content_type === 'quiz') title = 'Kuis Baru ⚡';
            } else if (notif.type === 'App\\Notifications\\AssignmentReminder') {
                title = 'Batas Waktu ⏰';
            } else if (notif.type === 'App\\Notifications\\AssignmentGradedNotification') {
                title = 'Tugas Dinilai ⭐';
            }
            
            const toastHtml = `
                <div class="toast-notification shadow ${colorClass}" id="toast-${id}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="font-weight-bold text-${colorClass} mb-1">
                                <i class="${icon} mr-1"></i> ${title}
                            </div>
                            <div class="text-sm text-dark mb-2">
                                ${data.message}
                            </div>
                            <a href="${data.url || '#'}" class="btn btn-${colorClass} btn-xs" onclick="markReadAndRedirect('${id}', '${data.url || '#'}', event)">
                                <i class="fas fa-eye mr-1"></i> Lihat Detail
                            </a>
                        </div>
                        <button onclick="dismissToast('${id}')" style="background:none; border:none; font-size:18px; cursor:pointer; color:#aaa; margin-left:10px;">
                            &times;
                        </button>
                    </div>
                </div>
            `;
            
            $('body').append(toastHtml);
            arrangeToasts();
        }

        function arrangeToasts() {
            let bottom = 20;
            $('.toast-notification').each(function () {
                $(this).css('bottom', bottom + 'px');
                bottom += $(this).outerHeight() + 10;
            });
        }

        function dismissToast(id) {
            $(`#toast-${id}`).fadeOut(300, function () {
                $(this).remove();
                arrangeToasts();
            });
            markNotificationRead(id);
        }

        function markReadAndRedirect(id, url, event) {
            if (event) event.preventDefault();
            markNotificationRead(id).always(() => {
                window.location.href = url;
            });
        }

        function markNotificationRead(id) {
            $(`#toast-${id}`).remove();
            arrangeToasts();
            return $.ajax({
                url: '/notifications/' + id + '/read',
                type: 'POST',
                data: { _token: $('meta[name="csrf-token"]').attr('content') }
            });
        }

        function loadNotifications() {
            $.ajax({
                url: '/notifications',
                type: 'GET',
                success: function (response) {
                    const unreadCount = response.unread_count;
                    const notifications = response.notifications;
                    
                    // Update dropdown header and badge
                    $('#notification-dropdown-header').text(`${unreadCount} Notifikasi Baru`);
                    if (unreadCount > 0) {
                        $('#notification-badge').text(unreadCount).show();
                    } else {
                        $('#notification-badge').hide();
                    }
                    
                    // Populate dropdown list
                    const container = $('#notification-items-container');
                    container.empty();
                    
                    if (notifications.length === 0) {
                        container.append(`
                            <div class="text-center p-3 text-muted" id="notification-empty-state">
                                <i class="far fa-bell-slash mb-2" style="font-size: 1.5rem;"></i>
                                <p class="mb-0 text-sm">Tidak ada notifikasi baru</p>
                            </div>
                        `);
                    } else {
                        notifications.forEach(notif => {
                            const icon = getNotificationIcon(notif.type, notif.data);
                            const relativeTime = formatRelativeTime(notif.created_at);
                            const unreadStyle = notif.read_at ? '' : 'background-color: #f8f9fa; font-weight: 500;';
                            
                            const itemHtml = `
                                <a href="${notif.data.url || '#'}" 
                                   class="dropdown-item d-flex align-items-start py-2 border-bottom read-notification-item" 
                                   data-id="${notif.id}" 
                                   style="white-space: normal; gap: 10px; ${unreadStyle}">
                                    <div class="mt-1">
                                        <i class="${icon}"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <p class="mb-1 text-sm text-wrap text-dark">${notif.data.message}</p>
                                        <span class="text-muted text-xs d-block">${relativeTime}</span>
                                    </div>
                                </a>
                            `;
                            
                            container.append(itemHtml);
                            
                            // If this notification is unread and not yet seen in this session, show toast
                            if (!notif.read_at && !seenNotifications.includes(notif.id)) {
                                showDynamicToast(notif);
                                seenNotifications.push(notif.id);
                            }
                        });
                        
                        // Save seen notifications IDs to prevent repeating toasts
                        localStorage.setItem('seen_notifications', JSON.stringify(seenNotifications));
                    }
                }
            });
        }

        $(document).ready(function () {
            // Load on init
            loadNotifications();
            
            // Poll for notifications every 30 seconds
            setInterval(loadNotifications, 30000);
            
            // Mark individual as read on click
            $(document).on('click', '.read-notification-item', function (e) {
                e.preventDefault();
                const id = $(this).data('id');
                const url = $(this).attr('href');
                markReadAndRedirect(id, url, e);
            });
            
            // Mark all read
            $('#mark-all-read-btn').on('click', function (e) {
                e.preventDefault();
                $.ajax({
                    url: '/notifications/mark-all-read',
                    type: 'POST',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function() {
                        $('.toast-notification').fadeOut(300, function() { $(this).remove(); });
                        loadNotifications();
                    }
                });
            });
            
            // Web Push Subscription handling
            const vapidPublicKey = '{{ env("VAPID_PUBLIC_KEY") }}';
            if (vapidPublicKey && 'serviceWorker' in navigator && 'PushManager' in window) {
                navigator.serviceWorker.register('/sw.js')
                    .then(function (registration) {
                        return registration.pushManager.getSubscription()
                            .then(async function (subscription) {
                                if (subscription) {
                                    return subscription;
                                }
                                
                                // Request permission and subscribe if not already done
                                if (Notification.permission === 'granted') {
                                    return subscribeUser(registration, vapidPublicKey);
                                } else if (Notification.permission !== 'denied') {
                                    // User can grant permission via browser prompt
                                    const permission = await Notification.requestPermission();
                                    if (permission === 'granted') {
                                        return subscribeUser(registration, vapidPublicKey);
                                    }
                                }
                            });
                    })
                    .then(function (subscription) {
                        if (subscription) {
                            sendSubscriptionToServer(subscription);
                        }
                    })
                    .catch(function (e) {
                        console.warn('Web Push SW Registration / subscription failed:', e);
                    });
            }
        });

        function subscribeUser(registration, publicKey) {
            const serverKey = urlB64ToUint8Array(publicKey);
            return registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: serverKey
            });
        }

        function sendSubscriptionToServer(subscription) {
            const key = subscription.getKey ? subscription.getKey('p256dh') : null;
            const token = subscription.getKey ? subscription.getKey('auth') : null;
            
            const payload = {
                endpoint: subscription.endpoint,
                keys: {
                    p256dh: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null,
                    auth: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null
                },
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            $.ajax({
                url: '/web-push/subscribe',
                type: 'POST',
                data: JSON.stringify(payload),
                contentType: 'application/json',
                dataType: 'json'
            });
        }

        function urlB64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding)
                .replace(/\-/g, '+')
                .replace(/_/g, '/');
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);
            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }
    </script>
@endauth

@stack('scripts')
</body>
</html>
