@extends('template.app')
@section('title', 'Setting | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">{{ 'Profile SKPD' }}</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Apps</a></li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">{{ 'Profile SKPD' }}</a></li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            @if ($message = Session::get('errors'))
                <div class="alert alert-warning alert-block">
                    <strong>{{ $message }}</strong>
                </div>
            @endif
            <!-- end page title -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="post" enctype="multipart/form-data">
                        @method('patch')
                        @csrf
                        <div class="mb-3 row">
                            <div class="col-md-6">
                                <label for="kd_skpd" class="form-label">Kode SKPD</label>
                                <input class="form-control" placeholder="Isi dengan nama pemerintah" type="text"
                                    id="kd_skpd" name="kd_skpd" required value="{{ $data_setting->kd_skpd }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="npwp" class="form-label">NPWP</label>
                                <input class="form-control" placeholder="Isi dengan NPWP" type="text"
                                    id="npwp" name="npwp" required value="{{ $data_setting->npwp }}">
                            </div>    
                        </div>

                        <div class="mb-3 row">
                            <div class="col-md-6">
                                <label for="nm_skpd" class="form-label">Nama SKPD</label>
                                <input class="form-control" placeholder="Isi dengan nama pemerintah" type="text"
                                    id="nm_skpd" name="nm_skpd" required value="{{ $data_setting->nm_skpd }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="obskpd" class="form-label">KODE CMS/OB</label>
                                <input class="form-control" placeholder="Isi dengan KODE CMS/OBSKPD" type="text"
                                    id="obskpd" name="obskpd" required value="{{ $data_setting->obskpd }}">
                            </div>
                        </div>
                        
                        <div class="mb-3 row">
                            <div class="col-md-6">
                                <label for="alamat" class="form-label">Alamat</label>
                                <input class="form-control" placeholder="Isi dengan Alamat Kantor SKPD" type="text"
                                    id="alamat" name="alamat"  value="{{ $data_setting->alamat }}">
                            </div>
                            <div class="col-md-6">
                                <label for="bank" class="form-label">Bank</label>
                                <select class="form-control select2-multiple @error('bank') is-invalid @enderror"
                                    style="width: 100%;" id="bank" name="bank" data-placeholder="Silahkan Pilih">
                                    <optgroup label="Daftar Bank">
                                        <option value="" disabled selected>Silahkan Pilih Bank</option>
                                        @foreach ($daftar_bank as $bank)
                                            <option value="{{ $bank->kode }}" data-nama="{{ $bank->nama }}"
                                                {{ $data_setting->bank == $bank->kode ? 'selected' : '' }}>
                                                {{ $bank->kode }} | {{ $bank->nama }}</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                                @error('bank')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <div class="col-md-6">
                                <label for="kodepos" class="form-label">Kode POS</label>
                                <input class="form-control" placeholder="Isi dengan kode pos" type="text"
                                    id="kodepos" name="kodepos"  value="{{ $data_setting->kodepos }}">
                            </div>
                            <div class="col-md-6">
                                <label for="rekening_pend" class="form-label">Rekening Penerimaan</label>
                                <input class="form-control" placeholder="Isi dengan rekening penerimaan" type="text"
                                    id="rekening_pend" name="rekening_pend"  value="{{ $data_setting->rekening_pend }}">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input class="form-control" placeholder="Isi dengan email" type="text"
                                    id="email" name="email"  value="{{ $data_setting->email }}">
                            </div>
                            <div class="col-md-6">
                                <label for="rekening" class="form-label">Rekening Pengeluaran</label>
                                <input class="form-control" placeholder="Isi dengan rekeing pengeluaran" type="text"
                                    id="rekening" name="rekening" required value="{{ $data_setting->rekening }}">
                            </div>
                        </div>
                        <!-- SIMPAN -->
                        <div style="float: right;">
                            <button type="submit" id="save" class="btn btn-primary btn-md">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div> <!-- end col -->
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            $('.select2-multiple').select2({
                theme: 'bootstrap-5',
            });
        });
    </script>
@endsection
