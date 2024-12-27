@extends('template.app')
@section('title', 'Tampil Penerima | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Data Penerima
                </div>
                <div class="card-body">
                    @csrf
                    @if ($data_penerima->totspp > '0' )
                        <div class="alert alert-danger" role="alert">
                            Data Penerima Sudah dibuat di SPP!
                        </div>
                    @elseif ($data_penerima->totkon > '0')
                    <div class="alert alert-danger" role="alert">
                        Data Penerima Sudah dibuat di Kontrak!
                    </div>
                    @endif
                    <!-- Bank -->
                    <div class="mb-3 row">
                        <label for="bank" class="col-md-2 col-form-label">Bank</label>
                        <div class="col-md-4">
                            <input type="text" value="{{ $data_penerima->kd_bank }}" readonly class="form-control">
                        </div>
                        <div class="col-md-6">
                            <input type="text" value="{{ $bank->nama_bank }}" readonly class="form-control">
                        </div>
                    </div>
                    <!-- BIC -->
                    <div class="mb-3 row">
                        <label for="bic" class="col-md-2 col-form-label">BIC</label>
                        <div class="col-md-10">
                            <input type="text" value="{{ $data_penerima->bic }}" readonly class="form-control">
                        </div>
                    </div>
                    <!-- Cabang Pusat -->
                    <div class="mb-3 row">
                        <label for="cabang" class="col-md-2 col-form-label">Cabang Pusat</label>
                        <div class="col-md-4">
                            <input type="text" value="{{ $data_penerima->bank }}" readonly class="form-control">
                        </div>
                        <div class="col-md-6">
                            <input type="text" value="{{ $data_penerima->nm_bank }}" readonly class="form-control">
                        </div>
                    </div>
                    <!-- Jenis Rekening -->
                    <div class="mb-3 row">
                        <label for="jenis" class="col-md-2 col-form-label">Jenis Rekening</label>
                        <div class="col-md-10">
                            @if ($data_penerima->jenis == '1')
                                <input type="text" value="Rekening Pegawai" readonly class="form-control">
                            @elseif ($data_penerima->jenis == '2')
                                <input type="text" value="Rekening Rekanan" readonly class="form-control">
                            @elseif ($data_penerima->jenis == '3')
                                <input type="text" value="Rekening Penampung Pajak" readonly class="form-control">
                            @endif
                        </div>
                    </div>
                    <!-- No Rekening Bank -->
                    <div class="mb-3 row">
                        <label for="rekening" class="col-md-2 col-form-label">No Rekening Bank</label>
                        <div class="col-md-10">
                            <input type="text" value="{{ $data_penerima->rekening }}" readonly class="form-control">
                        </div>
                    </div>
                    <!-- Nama Pemilik/Penerima -->
                    <div class="mb-3 row">
                        <label for="nm_rekening" class="col-md-2 col-form-label">Nama Pemilik/Penerima</label>
                        <div class="col-md-10">
                            <input type="text" value="{{ $data_penerima->nm_rekening }}" readonly class="form-control">
                        </div>
                    </div>
                    {{--  <!-- Kode Akun -->
                    <div class="mb-3 row">
                        <label for="kode_akun" class="col-md-2 col-form-label">Kode Akun</label>
                        <div class="col-md-10">
                            <input type="text" value="{{ isset($billing->nm_map) ? $billing->nm_map : '' }}" readonly
                                class="form-control">
                        </div>
                    </div>
                    <!-- Kode Setor -->
                    <div class="mb-3 row">
                        <label for="kode_setor" class="col-md-2 col-form-label">Kode Setor</label>
                        <div class="col-md-10">
                            <input type="text" value="{{ isset($billing->nm_setor) ? $billing->nm_setor : '' }}" readonly
                                class="form-control">
                        </div>
                    </div>  --}}
                    <!-- NPWP -->
                    <div class="mb-3 row">
                        <label for="npwp" class="col-md-2 col-form-label">NPWP</label>
                        <div class="col-md-10">
                            <input type="text" value="{{ $data_penerima->npwp }}" readonly class="form-control">
                        </div>
                    </div>
                    <!-- NM WP -->
                    <div class="mb-3 row">
                        <label for="nm_npwp_validasi" class="col-md-2 col-form-label">Nama WP</label>
                        <div class="col-md-10">
                            <input type="text" value="{{ $data_penerima->nm_wp }}" readonly class="form-control">
                        </div>
                    </div>
                    <!-- Keterangan Tambahan -->
                    <div class="mb-3 row">
                        <label for="keterangan" class="col-md-2 col-form-label">Keterangan Tambahan</label>
                        <div class="col-md-10">
                            <input type="text" value="{{ $data_penerima->keterangan }}" readonly class="form-control">
                        </div>
                    </div>
                    <!-- Rekanan -->
                    <div class="mb-3 row">
                        <label for="keterangan" class="col-md-2 col-form-label">Rekanan</label>
                        <div class="col-md-10">
                            <input type="text" value="{{ $data_penerima->nmrekan }}" readonly class="form-control">
                        </div>
                    </div>
                    <!-- Pimpinan -->
                    <div class="mb-3 row">
                        <label for="keterangan" class="col-md-2 col-form-label">Pimpinan</label>
                        <div class="col-md-10">
                            <input type="text" value="{{ $data_penerima->pimpinan }}" readonly class="form-control">
                        </div>
                    </div>
                    <!-- Alamat -->
                    <div class="mb-3 row">
                        <label for="keterangan" class="col-md-2 col-form-label">Alamat</label>
                        <div class="col-md-10">
                            <input type="text" value="{{ $data_penerima->alamat }}" readonly class="form-control">
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('penerima.index') }}" class="btn btn-warning btn-md" style="float: right;">Kembali</a>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div>
@endsection
