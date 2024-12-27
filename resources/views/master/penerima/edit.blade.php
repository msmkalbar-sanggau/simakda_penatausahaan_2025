@extends('template.app')
@section('title', 'Ubah Penerima | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            @if (session()->has('message'))
                <div class="alert {{ session('alert') ?? 'alert-info' }}">
                    {{ session('message') }}
                </div>
            @endif
            <div class="card">
                <div class="card-body">
                    <form
                        action="{{ route('penerima.update_penerima', ['rekening' => Crypt::encryptString($data_penerima->rekening), 'kd_skpd' => Crypt::encryptString($data_penerima->kd_skpd)]) }}"
                        method="post">
                        @method('PUT')
                        @csrf

                        {{-- @if ($cek > 0)
                            <div class="alert alert-warning alert-block">
                                <b style="font-size:16px">Sudah di Buat SPP!!Tidak dapat diedit!!</b>
                            </div>
                        @endif --}}

                        <!-- Bank -->
                        <div class="mb-3 row">
                            <label for="bank" class="col-md-2 col-form-label">Bank</label>
                            <div class="col-md-4">
                                <select class="form-control select2-multiple @error('bank') is-invalid @enderror"
                                    style="width: 100%;" id="bank" name="bank" data-placeholder="Silahkan Pilih">
                                    <optgroup label="Daftar Bank">
                                        <option value="" disabled selected>Silahkan Pilih Bank</option>
                                        @foreach ($daftar_bank as $bank)
                                            <option value="{{ $bank->kd_bank }}" data-bic="{{ $bank->bic }}"
                                                data-nama="{{ $bank->nama_bank }}"
                                                {{ $bank->kd_bank == $data_penerima->kd_bank && $bank->bic == $data_penerima->bic ? 'selected' : '' }}>
                                                {{ $bank->kd_bank }} | {{ $bank->nama_bank }}</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                                @error('bank')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <input class="form-control @error('nama_bank') is-invalid @enderror" type="text"
                                    id="nama_bank" value="{{ $nama_bank->nama_bank }}" name="nama_bank" readonly>
                                @error('nama_bank')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- BIC -->
                        <div class="mb-3 row">
                            <label for="bic" class="col-md-2 col-form-label">BIC</label>
                            <div class="col-md-10">
                                <input class="form-control @error('bic') is-invalid @enderror"
                                    value="{{ $data_penerima->bic }}" type="text" id="bic" name="bic"
                                    readonly>
                                @error('bic')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Cabang Pusat -->
                        <div class="mb-3 row">
                            <label for="cabang" class="col-md-2 col-form-label">Cabang Pusat</label>
                            <div class="col-md-4">
                                <select class="form-control select2-multiple @error('cabang') is-invalid @enderror"
                                    style="width: 100%;" id="cabang" name="cabang" data-placeholder="Silahkan Pilih">
                                </select>
                                @error('cabang')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <input type="hidden" id="cabang_sementara" value="{{ $data_penerima->bank }}">
                                <input class="form-control @error('nama_cabang') is-invalid @enderror" type="text"
                                    value="{{ $data_penerima->nm_bank }}" id="nama_cabang" name="nama_cabang" readonly>
                                @error('nama_cabang')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Jenis Rekening -->
                        <div class="mb-3 row">
                            <label for="jenis" class="col-md-2 col-form-label">Jenis Rekening</label>
                            <div class="col-md-10">
                                <select class="form-control select2-multiple @error('jenis') is-invalid @enderror"
                                    style="width: 100%;" id="jenis" name="jenis" data-placeholder="Silahkan Pilih">
                                    <optgroup label="Daftar Jenis Rekening">
                                        <option value="" disabled selected>Silahkan Pilih Jenis Rekening</option>
                                        <option value="1" {{ $data_penerima->jenis == '1' ? 'selected' : '' }}>
                                            Rekening
                                            Pegawai</option>
                                        <option value="2" {{ $data_penerima->jenis == '2' ? 'selected' : '' }}>
                                            Rekening
                                            Rekenan Pihak Ketiga</option>
                                        <option value="3" {{ $data_penerima->jenis == '3' ? 'selected' : '' }}>
                                            Rekening
                                            Penampung Pajak</option>
                                    </optgroup>
                                </select>
                                @error('jenis')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- KEPERLUAN -->
                        <div class="mb-3 row">
                            <label for="keperluan" class="col-md-2 col-form-label">Keperluan</label>
                            <div class="col-md-10">
                                <select class="form-control select2-multiple @error('keperluan') is-invalid @enderror"
                                    style="width: 100%;" id="keperluan" name="keperluan" data-placeholder="Silahkan Pilih">
                                    <option value="" disabled selected>Silahkan Pilih Jenis Rekening</option>
                                    <option value="1" {{ $data_penerima->keperluan == '1' ? 'selected' : '' }}>
                                        Transfer
                                        (Transaksi CMS, Transaksi Pemindahbukuan, Transaksi Tunai)
                                    </option>
                                    <option value="2" {{ $data_penerima->keperluan == '2' ? 'selected' : '' }}>
                                        Pengajuan
                                        Penagihan SPP SPM</option>
                                </select>
                                @error('keperluan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- No Rekening Bank -->
                        <div class="mb-3 row">
                            <label for="rekening" class="col-md-2 col-form-label">No Rekening Bank</label>
                            <div class="col-md-10">
                                <input class="form-control" value="{{ $data_penerima->rekening }}" type="text"
                                    placeholder="Silahkan isi dengan nomor rekening" id="rekening" name="rekening">
                                <input class="form-control" value="{{ $data_penerima->rekening }}" type="text"
                                    id="rekening_lama" name="rekening_lama" hidden>
                            </div>
                        </div>
                        <!-- Nama Pemilik/Penerima -->
                        <div class="mb-3 row">
                            <label for="nm_rekening" class="col-md-2 col-form-label">Nama Pemilik/Penerima</label>
                            <div class="col-md-10">
                                <input class="form-control" type="text" value="{{ $data_penerima->nm_rekening }}"
                                    placeholder="Silahkan isi dengan nama penerima" id="nm_rekening" name="nm_rekening">
                            </div>
                        </div>
                        <!-- Cek Rekening -->
                        <div class="mb-3 row">
                            <label for="cek_rekening" class="col-md-2 col-form-label"></label>
                            <div class="col-md-10">
                                <button type="button" id="cek_rekening" class="btn btn-primary btn-sm">Cek
                                    Rekening</button>
                            </div>
                        </div>
                        <!-- Kode Akun -->
                        <div class="mb-3 row">
                            <label for="kode_akun" class="col-md-2 col-form-label">Kode Akun</label>
                            <div class="col-md-10">
                                <select class="form-control select2-multiple @error('kode_akun') is-invalid @enderror"
                                    style="width: 100%;" id="kode_akun" name="kode_akun"
                                    data-placeholder="Silahkan Pilih">
                                    <optgroup label="Daftar Kode Akun">
                                        <option value="" disabled selected>Silahkan Pilih Kode Akun</option>
                                        @foreach ($daftar_kode_akun as $kode_akun)
                                            <option value="{{ $kode_akun->kd_map }}"
                                                {{ $kode_akun->kd_map == $data_penerima->kd_map ? 'selected' : '' }}>
                                                {{ $kode_akun->nm_map }}</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                                @error('kode_akun')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <input type="hidden" name="kode_akun_sementara" value="{{ $data_penerima->kd_map }}"
                                    id="kode_akun_sementara">
                            </div>
                        </div>
                        <!-- Kode Setor -->
                        <div class="mb-3 row">
                            <label for="kode_setor" class="col-md-2 col-form-label">Kode Setor</label>
                            <div class="col-md-10">
                                <select class="form-control select2-multiple @error('kode_setor') is-invalid @enderror"
                                    style="width: 100%;" id="kode_setor" name="kode_setor"
                                    data-placeholder="Silahkan Pilih">
                                    <optgroup label="Daftar Kode Setor">
                                        <option value="" disabled selected>Silahkan Pilih Kode Setor</option>
                                    </optgroup>
                                </select>
                                @error('kode_setor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <input type="hidden" name="kode_setor_sementara" value="{{ $data_penerima->kd_setor }}"
                                    id="kode_setor_sementara">
                            </div>
                        </div>
                        <!-- NPWP -->
                        <div class="mb-3 row">
                            <label for="npwp" class="col-md-2 col-form-label">NPWP</label>
                            <div class="col-md-10">
                                <input class="form-control" value="{{ $data_penerima->npwp }}" type="text"
                                    placeholder="Silahkan isi dengan npwp" id="npwp" name="npwp" maxlength="16">
                            </div>
                        </div>
                        <!-- Cek NPWP -->
                        <div class="mb-3 row">
                            <label for="cek_npwp" class="col-md-2 col-form-label"></label>
                            <div class="col-md-10">
                                <button type="button" id="cek_npwp" class="btn btn-primary btn-sm">Cek NPWP</button>
                            </div>
                        </div>
                        <!-- Keterangan Tambahan -->
                        <div class="mb-3 row">
                            <label for="keterangan" class="col-md-2 col-form-label">Keterangan Tambahan</label>
                            <div class="col-md-10">
                                <input class="form-control @error('keterangan') is-invalid @enderror" type="text"
                                    value="{{ $data_penerima->keterangan }}"
                                    placeholder="Silahkan isi dengan nama penerima" id="keterangan" name="keterangan">
                                @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        {{-- Rekanan --}}
                        <div class="mb-3 row">
                            <label for="rekanan" class="col-md-2 col-form-label">Rekanan</label>
                            <div class="col-md-10">
                                {{-- <select class="form-control select2-multiple" style="width: 100%;" id="rekanan"
                                    name="rekanan" data-placeholder="Silahkan Pilih">
                                    <option value="" disabled selected>Silahkan Pilih Rekanan</option>
                                    @foreach ($daftar_rekanan as $rekanan)
                                        <option value="{{ $rekanan->nmrekan }}" data-pimpinan="{{ $rekanan->pimpinan }}"
                                            data-alamat="{{ $rekanan->alamat }}"
                                            {{ $data_penerima->nmrekan == $rekanan->nmrekan ? 'selected' : '' }}>
                                            {{ $rekanan->nmrekan }}</option>
                                    @endforeach
                                </select> --}}
                                <input type="text" class="form-control" id="rekanan" name="rekanan"
                                    value="{{ $data_penerima->nmrekan }}">
                            </div>
                        </div>
                        {{-- pimpinan --}}
                        <div class="mb-3 row">
                            <label for="pimpinan" class="col-md-2 col-form-label">Pimpinan</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" id="pimpinan" name="pimpinan"
                                    value="{{ $data_penerima->pimpinan }}">
                            </div>
                        </div>
                        {{-- alamat --}}
                        <div class="mb-3 row">
                            <label for="alamat" class="col-md-2 col-form-label">Alamat</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" id="alamat" name="alamat"
                                    value="{{ $data_penerima->alamat }}">
                            </div>
                        </div>
                        <!-- Hasil Validasi Bank -->
                        <div class="mb-3 row">
                            <label for="hasil_validasi" class="col-md-12 col-form-label"
                                style="text-align: center;">Hasil Validasi Bank</label>
                            <div class="col-md-10">
                            </div>
                        </div>
                        <!-- No Rekening Bank -->
                        <div class="mb-3 row">
                            <label for="no_rekening_validasi" class="col-md-2 col-form-label">No Rekening Bank</label>
                            <div class="col-md-10">
                                <input class="form-control @error('no_rekening_validasi') is-invalid @enderror"
                                    value="{{ $data_penerima->rekening }}" readonly type="text"
                                    id="no_rekening_validasi" name="no_rekening_validasi">
                                @error('no_rekening_validasi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Nama Pemilik -->
                        <div class="mb-3 row">
                            <label for="nm_rekening_validasi" class="col-md-2 col-form-label">Nama
                                Pemilik</label>
                            <div class="col-md-10">
                                <input class="form-control @error('nm_rekening_validasi') is-invalid @enderror"
                                    value="{{ $data_penerima->nm_rekening }}" readonly type="text"
                                    id="nm_rekening_validasi" name="nm_rekening_validasi">
                                @error('nm_rekening_validasi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- NPWP -->
                        <div class="mb-3 row">
                            <label for="npwp_validasi" class="col-md-2 col-form-label">NPWP</label>
                            <div class="col-md-10">
                                <input class="form-control @error('npwp_validasi') is-invalid @enderror" readonly
                                    value="{{ $data_penerima->npwp }}" type="text" id="npwp_validasi"
                                    name="npwp_validasi">
                                @error('npwp_validasi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- NM WP -->
                        <div class="mb-3 row">
                            <label for="nm_npwp_validasi" class="col-md-2 col-form-label">Nama WP</label>
                            <div class="col-md-10">
                                <input class="form-control @error('nm_npwp_validasi') is-invalid @enderror" readonly
                                    value="{{ $data_penerima->nm_wp }}" type="text" id="nm_npwp_validasi"
                                    name="nm_npwp_validasi">
                                @error('nm_npwp_validasi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- SIMPAN -->
                        <div style="float: right;">
                            <button type="submit" id="save" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('penerima.index') }}" class="btn btn-warning btn-md">Kembali</a>
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
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            if (document.getElementById('jenis').value == '2') {

                $('#rekanan').prop('readonly', false);
                $('#pimpinan').prop('readonly', false);
                $('#alamat').prop('readonly', false);
            } else {
                $('#rekanan').prop('readonly', true);
                $('#pimpinan').prop('readonly', true);
                $('#alamat').prop('readonly', true);
            }

            $('#jenis').on('select2:select', function() {
                let bank = $('#bank').val();

                if (!bank) {
                    alert('Silahkan pilih Bank terlebih dahulu!');
                    $('#jenis').val(null).change();
                    return;
                }

                let jenis = this.value;

                if (jenis == '2') {
                    $('#pimpinan').prop('readonly', false);
                    $('#alamat').prop('readonly', false);
                    $('#rekanan').val(null);
                    $('#pimpinan').val(null);
                    $('#alamat').val(null);
                } else {
                    $('#pimpinan').prop('readonly', true);
                    $('#alamat').prop('readonly', true);
                    $('#rekanan').val(null);
                    $('#pimpinan').val(null);
                    $('#alamat').val(null);
                }
            });

            // $('#rekanan').on('change', function() {
            //     let rekanan = this.value;
            //     let pimpinan = $(this).find(':selected').data('pimpinan');
            //     let alamat = $(this).find(':selected').data('alamat');
            //     $("#nama_rekan").val(rekanan);
            //     $("#pimpinan").val(pimpinan);
            //     $("#alamat").val(alamat);
            // });

            let bank = document.getElementById('cabang_sementara').value;
            let bic = document.getElementById('bic').value;
            $.ajax({
                url: "{{ route('penerima.cabang') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    bic: bic,
                    "_token": "{{ csrf_token() }}",
                },
                dataType: "json",
                success: function(data) {
                    $('#cabang').empty();
                    $('#cabang').append(`<option value="0">Pilih Cabang Pusat</option>`);
                    $.each(data, function(index, data) {
                        if (data.kode == bank) {
                            $('#cabang').append(
                                `<option value="${data.kode}" data-nama="${data.nama}" selected>${data.kode} | ${data.nama}</option>`
                            );
                        } else {
                            $('#cabang').append(
                                `<option value="${data.kode}" data-nama="${data.nama}">${data.kode} | ${data.nama}</option>`
                            );
                        }
                    })
                }
            })
            let kode_setor_sementara = document.getElementById('kode_setor_sementara').value;
            let kode_akun = document.getElementById('kode_akun_sementara').value;
            $.ajax({
                url: "{{ route('penerima.kodeSetor') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_map: kode_akun,
                    "_token": "{{ csrf_token() }}",
                },
                dataType: "json",
                success: function(data) {
                    $('#kode_setor').empty();
                    $('#kode_setor').append(`<option value="0">Pilih Kode Setor</option>`);
                    $.each(data, function(index, data) {
                        if (data.kd_setor == kode_setor_sementara) {
                            $('#kode_setor').append(
                                `<option value="${data.kd_setor}" selected>${data.nm_setor}</option>`
                            );
                        } else {
                            $('#kode_setor').append(
                                `<option value="${data.kd_setor}">${data.nm_setor}</option>`
                            );
                        }
                    })
                }
            })
            $('#bank').on("change", function() {
                $("#nama_cabang").val("");
                let bic = $(this).find(':selected').data('bic');
                let nm_bank = $(this).find(':selected').data('nama');
                $("#nama_bank").val(nm_bank);
                $("#bic").val(bic);
                $.ajax({
                    url: "{{ route('penerima.cabang') }}",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        bic: bic,
                        "_token": "{{ csrf_token() }}",
                    },
                    dataType: "json",
                    success: function(data) {
                        $('#cabang').empty();
                        $('#cabang').append(`<option value="0">Pilih Cabang Pusat</option>`);
                        $.each(data, function(index, data) {

                            $('#cabang').append(
                                `<option value="${data.kode}" data-nama="${data.nama}">${data.kode} | ${data.nama}</option>`
                            );
                        })
                    }
                })
            });
            $('#cabang').on('change', function() {
                let selected = $(this).find('option:selected');
                let nama_cabang = selected.data('nama');
                $("#nama_cabang").val(nama_cabang);
            });
            $('#kode_akun').on("change", function() {
                let kd_map = this.value;
                $.ajax({
                    url: "{{ route('penerima.kodeSetor') }}",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        kd_map: kd_map,
                        "_token": "{{ csrf_token() }}",
                    },
                    dataType: "json",
                    success: function(data) {
                        $('#kode_setor').empty();
                        $('#kode_setor').append(`<option value="0">Pilih Kode Setor</option>`);
                        $.each(data, function(index, data) {
                            $('#kode_setor').append(
                                `<option value="${data.kd_setor}">${data.nm_setor}</option>`
                            );
                        })
                    }
                })
            });
            $('#cek_rekening').on("click", function() {
                let kode_bank = document.getElementById('bank').value;

                let no_rek = document.getElementById('rekening').value;
                let nm_rek = document.getElementById('nm_rekening').value;
                let jenis = document.getElementById('jenis').value;
                let digit = $('#bank').find('option:selected').data('digit');

                if (!kode_bank) {
                    alert('Bank harus dipilih!');
                }
                if (!no_rek) {
                    alert('No rekening harus diisi!');
                }
                if (!nm_rek) {
                    alert('Nama rekening harus diisi!');
                }

                // if (jenis == '4') {
                //     if (no_rek.length > digit) {
                //         alert('No Rekening melebihi digit inputan rekening, yaitu ' + digit + ' digit!');
                //         return;
                //     }

                //     Swal.fire({
                //         title: 'SUKSES!',
                //         text: 'Rekening bank ' + no_rek + '-' + nm_rek + ' tersimpan',
                //         icon: 'success',
                //         confirmButtonColor: '#5b73e8',
                //     })

                //     $("#no_rekening_validasi").val(no_rek);
                //     $("#nm_rekening_validasi").val(nm_rek);
                //     document.getElementById("save").disabled = false;
                // } else {
                //     if (kode_bank && no_rek && nm_rek) {
                //         swal.fire({
                //             allowOutsideClick: false,
                //             allowEscapeKey: false,
                //             title: 'Proses cek rekening bank',
                //             text: 'Silahkan tunggu !!!',
                //             onOpen: function() {
                //                 swal.showLoading()
                //             }
                //         })


                //         $.ajax({
                //             url: "{{ route('penerima.cekRekening') }}",
                //             type: "POST",
                //             dataType: 'json',
                //             data: {
                //                 kode_bank: kode_bank,
                //                 no_rek: no_rek,
                //                 nm_rek: nm_rek,
                //             },
                //             dataType: "json",
                //             success: function(data) {
                //                 let data1 = $.parseJSON(data);
                //                 if (data1.status) {
                //                     Swal.fire({
                //                         title: 'SUKSES!',
                //                         text: 'Rekening bank ' + data1.data[0].data
                //                             .nomorRekening + '-' + data1.data[0].data
                //                             .namaPemilikRekening + ' tersedia',
                //                         icon: 'success',
                //                         confirmButtonColor: '#5b73e8',
                //                     })

                //                     $("#no_rekening_validasi").val(data1.data[0].data
                //                         .nomorRekening);
                //                     $("#nm_rekening_validasi").val(data1.data[0].data
                //                         .namaPemilikRekening);
                //                     document.getElementById("save").disabled = false;
                //                     $("#loading").hide();
                //                 } else {
                //                     // alert(data1.message);
                //                     let pesan = data1.message.replaceAll(" ", "\u00A0");

                //                     Swal.fire({
                //                         type: "error",
                //                         icon: "error",
                //                         title: "Oops...",
                //                         text: pesan,
                //                         confirmButtonClass: "btn btn-confirm mt-2",
                //                     })

                //                     document.getElementById("save").disabled = true;
                //                     $("#no_rekening_validasi").attr("value", '');
                //                     $("#nm_rekening_validasi").attr("value", '');
                //                     $("#loading").hide();
                //                 }
                //             }
                //         })
                //     }
                // }

                if (kode_bank && no_rek && nm_rek) {
                    swal.fire({
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        title: 'Proses cek rekening bank',
                        text: 'Silahkan tunggu !!!',
                        onOpen: function() {
                            swal.showLoading()
                        }
                    })


                    $.ajax({
                        url: "{{ route('penerima.cekRekening') }}",
                        type: "POST",
                        dataType: 'json',
                        data: {
                            kode_bank: kode_bank,
                            no_rek: no_rek,
                            nm_rek: nm_rek,
                            "_token": "{{ csrf_token() }}",
                        },
                        dataType: "json",
                        success: function(data) {
                            let data1 = $.parseJSON(data);
                            if (data1.status) {
                                Swal.fire({
                                    title: 'SUKSES!',
                                    text: 'Rekening bank ' + data1.data[0].data
                                        .nomorRekening + '-' + data1.data[0].data
                                        .namaPemilikRekening + ' tersedia',
                                    icon: 'success',
                                    confirmButtonColor: '#5b73e8',
                                })

                                $("#no_rekening_validasi").val(data1.data[0].data
                                    .nomorRekening);
                                $("#nm_rekening_validasi").val(data1.data[0].data
                                    .namaPemilikRekening);
                                if (jenis == '1') {
                                    $("#rekanan").val(data1.data[0].data
                                        .namaPemilikRekening);
                                }
                                document.getElementById("save").disabled = false;
                                $("#loading").hide();
                            } else {
                                // alert(data1.message);
                                let pesan = data1.message.replaceAll(" ", "\u00A0");

                                Swal.fire({
                                    type: "error",
                                    icon: "error",
                                    title: "Oops...",
                                    text: pesan,
                                    confirmButtonClass: "btn btn-confirm mt-2",
                                })

                                document.getElementById("save").disabled = true;
                                $("#no_rekening_validasi").attr("value", '');
                                $("#nm_rekening_validasi").attr("value", '');
                                $("#loading").hide();
                            }
                        }
                    })
                }
            });

            $('#cek_npwp').on("click", function() {
                let npwp = document.getElementById('npwp').value;
                let kode_akun = document.getElementById('kode_akun').value;
                let kode_setor = document.getElementById('kode_setor').value;
                if (!npwp) {
                    alert('Bank harus dipilih!');
                    exit;
                }
                if (!kode_akun) {
                    alert('No rekening harus diisi!');
                    exit;
                }
                if (!kode_setor) {
                    alert('Nama rekening harus diisi!');
                    exit;
                }
                if (npwp == '000000000000000') {
                    $("#npwp_validasi").val(npwp);
                    $("#nm_npwp_validasi").val("-");
                    document.getElementById("save").disabled = false;
                } else {
                    if (npwp && kode_akun && kode_setor) {
                        swal.fire({
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            title: 'Proses cek NPWP',
                            text: 'Silahkan tunggu !!!',
                            onOpen: function() {
                                swal.showLoading()
                            }
                        })
                        $.ajax({
                            url: "{{ route('penerima.cekNpwp') }}",
                            type: "POST",
                            dataType: 'json',
                            data: {
                                npwp: npwp,
                                kode_akun: kode_akun,
                                kode_setor: kode_setor,
                                "_token": "{{ csrf_token() }}",
                            },
                            dataType: "json",
                            success: function(data) {
                                let data1 = $.parseJSON(data);
                                if (data1.status) {
                                    if (data1.data[0].response_code == 00) {
                                        Swal.fire({
                                            title: 'SUKSES!',
                                            text: 'NPWP ' + data1.data[0].data
                                                .nomorPokokWajibPajak + '-' + data1
                                                .data[0]
                                                .data.namaWajibPajak + ' tersedia',
                                            icon: 'success',
                                            confirmButtonColor: '#5b73e8',
                                        })

                                        $("#npwp_validasi").val(data1.data[0].data
                                            .nomorPokokWajibPajak);
                                        $("#nm_npwp_validasi").val(data1.data[0].data
                                            .namaWajibPajak);
                                        document.getElementById("save").disabled = false;
                                        // $("#loading").hide();
                                    } else {
                                        Swal.fire({
                                            type: "error",
                                            icon: "error",
                                            title: "Oops...",
                                            text: data1.data[0].message,
                                            confirmButtonClass: "btn btn-confirm mt-2",
                                        })
                                        document.getElementById("save").disabled = true;
                                        $("#npwp_validasi").attr("value", '');
                                        $("#nm_npwp_validasi").attr("value", '');
                                        // $("#loading").hide();
                                    }
                                } else {
                                    Swal.fire({
                                        type: "error",
                                        icon: "error",
                                        title: "Oops...",
                                        text: data1.message,
                                        confirmButtonClass: "btn btn-confirm mt-2",
                                    })
                                    document.getElementById("save").disabled = true;
                                    $("#npwp_validasi").attr("value", '');
                                    $("#nm_npwp_validasi").attr("value", '');
                                }
                            }
                        })
                    }
                }
            });
        });
    </script>
@endsection
