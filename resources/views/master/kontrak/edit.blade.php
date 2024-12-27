@extends('template.app')
@section('title', 'Kontrak | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">{{ 'Edit Kontrak' }}</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Apps</a></li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">{{ 'Kontrak' }}</a></li>
                                <li class="breadcrumb-item active">{{ 'Edit Kontrak' }}</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('kontrak.update', Crypt::encryptString($data_kontrak->no_kontrak)) }}"
                        id="kontrak" method="post">
                        @method('PUT')
                        @csrf
                        <!-- Kode SKPD -->
                        <div class="mb-3 row">
                            <label for="kd_skpd" class="col-md-2 col-form-label">Kode SKPD/Unit</label>
                            <div class="col-md-10">
                                <input type="text" readonly class="form-control @error('kd_skpd') is-invalid @enderror"
                                    name="kd_skpd" id="kd_skpd" value="{{ $data_kontrak->kd_skpd }}">
                                @error('kd_skpd')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Nama SKPD -->
                        <div class="mb-3 row">
                            <label for="nm_skpd" class="col-md-2 col-form-label">Nama SKPD/Unit</label>
                            <div class="col-md-10">
                                <input class="form-control @error('nm_skpd') is-invalid @enderror" type="text"
                                    id="nm_skpd" name="nm_skpd" readonly required value="{{ $skpd->nm_skpd }}">
                                @error('nm_skpd')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- No Kontrak -->
                        <div class="mb-3 row">
                            <label for="no_kontrak" class="col-md-2 col-form-label">No Kontrak</label>
                            <div class="col-md-10">
                                <input type="text" placeholder="Isi Nomor Kontrak Tanpa Spasi"
                                    class="form-control @error('no_kontrak') is-invalid @enderror"
                                    value="{{ $data_kontrak->no_kontrak }}" id="no_kontrak" name="no_kontrak">
                                @error('no_kontrak')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Tanggal Kontrak -->
                        <div class="mb-3 row">
                            <label for="tgl_kerja" class="col-md-2 col-form-label">Tanggal Kontrak</label>
                            <div class="col-md-10">
                                <input type="date" name="tgl_kerja" id="tgl_kerja"
                                    value="{{ date('Y-m-d', strtotime($data_kontrak->tgl_kerja)) }}"
                                    class="form-control @error('tgl_kerja') is-invalid @enderror">
                                @error('tgl_kerja')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Pelaksana Pekerjaan/Rekanan -->
                        <div class="mb-3 row">
                            <label for="nmpel" class="col-md-2 col-form-label">Pelaksana Pekerjaan/Rekanan</label>
                            <div class="col-md-10">
                                <input class="form-control @error('nmpel') is-invalid @enderror"
                                    value="{{ $data_kontrak->nmpel }}" type="text"
                                    placeholder="Silahkan isi dengan nama pelaksana pekerjaan" id="nmpel"
                                    name="nmpel">
                                @error('nmpel')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Pimpinan -->
                        <div class="mb-3 row">
                            <label for="pimpinan" class="col-md-2 col-form-label">Pimpinan</label>
                            <div class="col-md-10">
                                <input class="form-control @error('pimpinan') is-invalid @enderror" type="text"
                                    placeholder="Silahkan isi dengan nama pimpinan" value="{{ $data_kontrak->pimpinan }}"
                                    id="pimpinan" name="pimpinan">
                                @error('pimpinan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Nama Pekerjaan -->
                        <div class="mb-3 row">
                            <label for="nm_kerja" class="col-md-2 col-form-label">Nama Pekerjaan</label>
                            <div class="col-md-10">
                                <input class="form-control @error('nm_kerja') is-invalid @enderror" type="text"
                                    placeholder="Silahkan isi dengan nama pekerjaan" value="{{ $data_kontrak->nm_kerja }}"
                                    id="nm_kerja" name="nm_kerja">
                                @error('nm_kerja')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Nomor Referensi Bank -->
                        <div class="mb-3 row">
                            <label for="no_ref" class="col-md-2 col-form-label">No Referensi Bank</label>
                            <div class="col-md-10">
                                <input class="form-control" type="text"
                                    placeholder="Silahkan isi dengan nomor referensi bank"
                                    value="{{ $data_kontrak->no_ref }}" id="no_ref" name="no_ref">
                                <div class="invalid-feedback">No Referensi Bank harus diisi jika nilai kontrak lebih
                                    dari
                                    100 juta!</div>
                                @error('no_ref')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>


                        <!-- Rekanan -->
                        <div class="mb-3 row">
                            <label for="nm_rekening" class="col-md-2 col-form-label">(Rekanan) Nama Pemilik
                                Rekening</label>
                            <div class="col-md-10">
                                <select class="form-control @error('nm_rekening') is-invalid @enderror select2-multiple"
                                    style="width: 100%;" id="nm_rekening" name="nm_rekening" required>
                                    <optgroup label="Nama Rekening | No Rekening | NPWP">
                                        <option value="" disabled selected>Silahkan Pilih Rekening</option>
                                        @foreach ($daftar_rekening as $data_rekening)
                                            <option value="{{ $data_rekening->nm_rekening }}"
                                                data-npwp="{{ $data_rekening->npwp }}"
                                                data-rekening="{{ $data_rekening->rekening }}"
                                                {{ $data_kontrak->rekening == $data_rekening->rekening ? 'selected' : '' }}>
                                                {{ $data_rekening->nm_rekening }} | {{ $data_rekening->rekening }} |
                                                {{ $data_rekening->npwp }}</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                                @error('nm_rekening')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- No Rekening -->
                        <div class="mb-3 row">
                            <label for="no_rekening" class="col-md-2 col-form-label">No Rekening</label>
                            <div class="col-md-10">
                                <input type="text" readonly name="no_rekening"
                                    value="{{ $data_kontrak->no_rekening }}" id="no_rekening"
                                    class="form-control @error('no_rekening') is-invalid @enderror">
                                @error('no_rekening')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- NPWP -->
                        <div class="mb-3 row">
                            <label for="npwp" class="col-md-2 col-form-label">NPWP</label>
                            <div class="col-md-10">
                                <input type="text" readonly name="npwp" value="{{ $data_kontrak->npwp }}"
                                    id="npwp" class="form-control @error('npwp') is-invalid @enderror">
                                @error('npwp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Nilai Kontrak -->
                        <div class="mb-3 row">
                            <label for="nilai" class="col-md-2 col-form-label">Nilai Kontrak</label>
                            <div class="col-md-10">
                                <input type="text" min="0" value="{{ $data_kontrak->nilai }}"
                                    placeholder="Silahkan isi dengan nilai kontrak" name="nilai" id="nilai"
                                    class="form-control @error('nilai') is-invalid @enderror">
                                @error('nilai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- SIMPAN -->
                        <div style="float: right;">
                            <button type="submit" id="save" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('kontrak.index') }}" class="btn btn-warning btn-md">Kembali</a>
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
        $('#nm_rekening').on("change", function() {
            let rekening = $(this).find(':selected').data('rekening');
            let npwp = $(this).find(':selected').data('npwp');
            $("#no_rekening").val(rekening);
            $("#npwp").val(npwp);
        });

        document.getElementById('kontrak').addEventListener('submit', function(event) {
            // Ambil nilai dari inputan
            var nilaiKontrak = document.getElementById('nilai').value;
            var noRef = document.getElementById('no_ref').value;

            nilaiKontrak.value = nilaiKontrak.value.replace(',', '.');

            // Validasi nilai kontrak
            if (nilaiKontrak > 100000000) {
                if (!noRef) {
                    // Jika no_ref kosong dan nilai_kontrak lebih dari 100 juta
                    event.preventDefault(); // Mencegah submit form
                    var noRefInput = document.getElementById('no_ref');
                    noRefInput.classList.add('is-invalid'); // Tambahkan kelas is-invalid
                } else {
                    // Jika no_ref terisi
                    document.getElementById('no_ref').classList.remove('is-invalid');
                }
            } else {
                // Jika nilai_kontrak kurang dari 100 juta, hapus kelas is-invalid jika ada
                document.getElementById('no_ref').classList.remove('is-invalid');
            }
        });
    </script>
@endsection
