@extends('templates.setup')
@section('content')
    <div class="min-h-[80vh] grid place-items-center">
        <div class="lg:w-[360px] w-[90%]">
            <div class="mb-5 p-1">
                <div class="flex items-center justify-center">
                    <img src="{{ url('images/icon.png') }}" alt="LOGO UPT" class="h-12 w-12">
                    <h2 class="ml-2 font-semibold text-xl">SIP POINT</h2>
                </div>
            </div>
            <div class="card border bg-white">
                <div class="card-header p-3">
                    <p class="text-center text-gray-500 text-sm">
                        Setup Admin SIP POINT
                    </p>
                </div>
                <form method="POST" action="{{ url('setup') }}">
                    @csrf
                    <div class="card-body p-3">
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text">Nama</span>
                            </label>
                            <input type="text" placeholder="Masukan username"
                                class="input input-bordered w-full  @error('nama') border-red-500 @enderror"
                                name="nama" value="{{ old('nama') }}" />
                            @error('nama')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text">Username</span>
                            </label>
                            <input type="text" placeholder="Masukan username"
                                class="input input-bordered w-full  @error('username') border-red-500 @enderror"
                                name="username" value="{{ old('username') }}" />
                            @error('username')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text">Password</span>
                            </label>
                            <input type="password" placeholder="Masukan password"
                                class="input input-bordered w-full  @error('password') border-red-500 @enderror"
                                name="password" value="{{ old('password') }}" />
                            @error('password')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text">Konfirmasi Password</span>
                            </label>
                            <input type="password" placeholder="Masukan password"
                                class="input input-bordered w-full  @error('confirm_password') border-red-500 @enderror"
                                name="confirm_password" value="{{ old('confirm_password') }}" />
                            @error('confirm_password')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer p-4 grid">
                        <button type="submit" class="btn btn-neutral">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

