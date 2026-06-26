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
<!-- SweetAlert2 -->
<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- App Global JS -->
<script src="{{ asset('js/app.js') }}"></script>

{{-- Notifikasi untuk Pelajar --}}
@auth
    @if(Auth::user()->hasRole('pelajar'))
        @php
            $unreadNotifications = Auth::user()->unreadNotifications
                ->where('type', 'App\Notifications\AssignmentGradedNotification');
        @endphp

        @foreach($unreadNotifications as $notif)
            <div class="toast-notification shadow"
                id="notif-{{ $notif->id }}"
                style="
                    position: fixed;
                    bottom: 20px;
                    right: 20px;
                    z-index: 9999;
                    width: 350px;
                    background: #fff;
                    border-left: 4px solid #28a745;
                    border-radius: 6px;
                    padding: 16px;
                    margin-bottom: 10px;
                ">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="font-weight-bold text-success mb-1">
                            <i class="fas fa-star mr-1"></i> Tugas Dinilai
                        </div>
                        <div class="text-sm text-muted mb-2">
                            {{ $notif->data['message'] }}
                        </div>
                        <a href="{{ route('assignments.show', $notif->data['assignment_id']) }}"
                            class="btn btn-success btn-xs"
                            onclick="markRead('{{ $notif->id }}')">
                            <i class="fas fa-eye mr-1"></i> Lihat Tugas
                        </a>
                    </div>
                    <button onclick="markRead('{{ $notif->id }}')"
                        style="background:none; border:none; font-size:18px; cursor:pointer; color:#aaa; margin-left:10px;">
                        &times;
                    </button>
                </div>
            </div>
        @endforeach

        <script>
            function markRead(id) {
                $('#notif-' + id).fadeOut(300, function () {
                    $(this).remove();
                });

                $.ajax({
                    url: '/notifications/' + id + '/read',
                    type: 'POST',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') }
                });
            }

            // Geser notif ke atas jika ada lebih dari satu
            $(document).ready(function () {
                let bottom = 20;
                $('.toast-notification').each(function () {
                    $(this).css('bottom', bottom + 'px');
                    bottom += $(this).outerHeight() + 10;
                });
            });
        </script>
    @endif
@endauth

@stack('scripts')
</body>
</html>
