@include('layouts.header')
@include('layouts.nav')
@include('layouts.sidebar')

<div class="content-wrapper">

    <div class="content-header">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-check"></i> Berhasil!</h5>
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>

    @yield('content')

    </div>
@include('layouts.footer')