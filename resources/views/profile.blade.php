@extends('main')

@section('content')

<div class="content-wrapper ml-0">

    <div class="content-header">
        <h1>Profile</h1>
    </div>

    <section class="content">

        <div class="container-fluid px-3">

            <div class="row">

                {{-- LEFT PROFILE --}}
                <div class="col-md-4">

                    <div class="card card-primary card-outline">

                        <div class="card-body box-profile text-center">

                            <img
                                class="profile-user-img img-fluid img-circle"
                                src="https://i.pravatar.cc/150"
                            >

                            <h3 class="profile-username mt-3">
                                {{ auth()->user()->name }}
                            </h3>

                            <p class="text-muted">
                                {{ auth()->user()->roles->first()->name ?? 'User' }}
                            </p>

                        </div>

                    </div>

                </div>

                {{-- RIGHT FORM --}}
                <div class="col-md-8">

                    <div class="card">

                        <div class="card-header bg-primary">
                            <h3 class="card-title">
                                Setting Form
                            </h3>
                        </div>

                        <div class="card-body">

                            {{-- SUCCESS MESSAGE --}}
                            @if(session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            {{-- ERROR MESSAGE --}}
                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form
                                action="/profile/update"
                                method="POST"
                                enctype="multipart/form-data"
                            >

                                @csrf

                                {{-- NAME --}}
                                <div class="form-group mb-3">
                                    <label>Nama Lengkap</label>

                                    <input
                                        type="text"
                                        name="name"
                                        class="form-control"
                                        value="{{ auth()->user()->name }}"
                                        required
                                    >
                                </div>

                                {{-- EMAIL --}}
                                <div class="form-group mb-3">
                                    <label>Email</label>

                                    <input
                                        type="email"
                                        name="email"
                                        class="form-control"
                                        value="{{ auth()->user()->email }}"
                                        required
                                    >
                                </div>

                                {{-- PHONE --}}
                                <div class="form-group mb-3">
                                    <label>No HP</label>

                                    <input
                                        type="text"
                                        name="phone"
                                        class="form-control"
                                        value="{{ auth()->user()->phone }}"
                                        placeholder="08xxxxxxxxxx"
                                    >
                                </div>

                                {{-- BIO --}}
                                <div class="form-group mb-3">
                                    <label>Bio</label>

                                    <textarea
                                        name="bio"
                                        class="form-control"
                                        rows="4"
                                        placeholder="Tulis bio singkat..."
                                    >{{ auth()->user()->bio }}</textarea>
                                </div>

                                {{-- AVATAR --}}
                                <div class="form-group mb-3">
                                    <label>Avatar</label>

                                    <input
                                        type="file"
                                        name="avatar"
                                        class="form-control"
                                    >
                                </div>

                                {{-- PASSWORD --}}
                                <div class="form-group mb-3">
                                    <label>Password Baru</label>

                                    <input
                                        type="password"
                                        name="password"
                                        class="form-control"
                                        placeholder="Kosongkan jika tidak diubah"
                                    >
                                </div>

                                {{-- PASSWORD CONFIRM --}}
                                <div class="form-group mb-3">
                                    <label>Konfirmasi Password</label>

                                    <input
                                        type="password"
                                        name="password_confirmation"
                                        class="form-control"
                                    >
                                </div>

                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                >
                                    <i class="fas fa-save"></i>
                                    Update Profile
                                </button>

                            </form>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

</div>

@endsection