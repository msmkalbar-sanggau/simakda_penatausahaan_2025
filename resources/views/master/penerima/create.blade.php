@extends('template.app')
@section('title', 'Tambah Penerima | SIMAKDA')
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
                    {{-- <form action="{{ route('penerima.store') }}" method="post"> --}}
                    @csrf
                    <!-- Bank -->
                    <div class="mb-3 row">
                        <label for="bank" class="col-md-2 col-form-label">Bank</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple @error('bank') is-invalid @enderror"
                                style=" width: 100%;" id="bank" name="bank" data-placeholder="Silahkan Pilih">
                                <optgroup label="Daftar Bank">
                                    <option value="" disabled selected>Silahkan Pilih Bank</option>
                                    @foreach ($daftar_bank as $bank)
                                        <option value="{{ $bank->kd_bank }}" data-bic="{{ $bank->bic }}"
                                            data-nama="{{ $bank->nama_bank }}"
                                            {{ old('bank') == $bank->kd_bank ? 'selected' : '' }}>{{ $bank->kd_bank }}
                                            |
                                            {{ $bank->nama_bank }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                            @error('bank')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <input class="form-control @error('nama_bank') is-invalid @enderror" type="text"
                                id="nama_bank" name="nama_bank" value="{{ old('nama_bank') }}" readonly>
                            @error('nama_bank')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- BIC -->
                    <div class="mb-3 row">
                        <label for="bic" class="col-md-2 col-form-label">BIC</label>
                        <div class="col-md-10">
                            <input class="form-control @error('bic') is-invalid @enderror" type="text" id="bic"
                                name="bic" readonly value="{{ old('bic') }}">
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
                            <input class="form-control @error('nama_cabang') is-invalid @enderror" type="text"
                                id="nama_cabang" name="nama_cabang" readonly value="{{ old('nama_cabang') }}">
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
                                    <option value="1" {{ old('jenis') == '1' ? 'selected' : '' }}>Rekening Pegawai
                                    </option>
                                    <option value="2" {{ old('jenis') == '2' ? 'selected' : '' }}>Rekening Rekenan
                                        Pihak Ketiga</option>
                                    <option value="3" {{ old('jenis') == '3' ? 'selected' : '' }}>Rekening
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
                                <option value="1" {{ old('keperluan') == '1' ? 'selected' : '' }}>Transfer
                                    (Transaksi CMS, Transaksi Pemindahbukuan, Transaksi Tunai)
                                </option>
                                <option value="2" {{ old('keperluan') == '2' ? 'selected' : '' }}>Pengajuan
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
                            <input class="form-control" type="text" placeholder="Silahkan isi dengan nomor rekening"
                                id="rekening" value="{{ old('rekening') }}" name="rekening">
                        </div>
                    </div>
                    <!-- Nama Pemilik -->
                    <div class="mb-3 row">
                        <label for="nm_rekening" class="col-md-2 col-form-label">Nama Pemilik</label>
                        <div class="col-md-10">
                            <input class="form-control" type="text" placeholder="Silahkan isi dengan nama penerima"
                                id="nm_rekening" name="nm_rekening" value="{{ old('nm_rekening') }}">
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
                                style="width: 100%;" id="kode_akun" name="kode_akun"data-placeholder="Silahkan Pilih">
                                <optgroup label="Daftar Kode Akun">
                                    <option value="" disabled selected>Silahkan Pilih Kode Akun</option>
                                    @foreach ($daftar_kode_akun as $kode_akun)
                                        <option value="{{ $kode_akun->kd_map }}"
                                            {{ old('kode_akun') == $kode_akun->kd_map ? 'selected' : '' }}>
                                            {{ $kode_akun->nm_map }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                            @error('kode_akun')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                        </div>
                    </div>
                    <!-- NPWP -->
                    <div class="mb-3 row">
                        <label for="npwp" class="col-md-2 col-form-label">NPWP</label>
                        <div class="col-md-10">
                            <input class="form-control" type="text" placeholder="Silahkan isi dengan npwp"
                                id="npwp" name="npwp" value="{{ old('npwp') }}" maxlength="16">
                        </div>
                    </div>
                    {{--  <!-- Nama NPWP -->
                        <div class="mb-3 row">
                            <label for="nm_npwp" class="col-md-2 col-form-label">Nama NPWP</label>
                            <div class="col-md-10">
                                <input class="form-control" type="text" placeholder="Silahkan isi nama npwp"
                                    id="nm_npwp" name="nm_npwp" value="{{ old('nm_npwp') }}">
                            </div>
                        </div>  --}}
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
                            <input class="form-control @error('keterangan') is-invalid @enderror"
                                value="{{ old('keterangan') }}" type="text"
                                placeholder="Silahkan isi dengan nama penerima" id="keterangan" name="keterangan">
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- Rekanan --}}
                    <div class="mb-3 row">
                        <label for="rekanan" class="col-md-2 col-form-label">Rekanan/Penerima</label>
                        <div class="col-md-10">
                            {{-- <select class="form-control select2-multiple" style="width: 100%;" id="rekanan"
                                    name="rekanan" data-placeholder="Silahkan Pilih">
                                    <option value="" disabled selected>Silahkan Pilih Rekanan</option>
                                    @foreach ($daftar_rekanan as $rekanan)
                                        <option value="{{ $rekanan->nmrekan }}" data-pimpinan="{{ $rekanan->pimpinan }}"
                                            data-alamat="{{ $rekanan->alamat }}"
                                            {{ old('rekanan') == $rekanan->nmrekan ? 'selected' : '' }}>
                                            {{ $rekanan->nmrekan }}</option>
                                    @endforeach
                                </select> --}}
                                <input placeholder="Silahkan isi rekanan/penerima" type="text" class="form-control" id="rekanan" name="rekanan">
                        </div>
                    </div>
                    {{-- pimpinan --}}
                    <div class="mb-3 row">
                        <label for="pimpinan" class="col-md-2 col-form-label">Pimpinan</label>
                        <div class="col-md-10">
                        <input placeholder="Silahkan isi pimpinan" type="text" class="form-control" id="pimpinan" name="pimpinan">
                        </div>
                    </div>
                    {{-- alamat --}}
                    <div class="mb-3 row">
                        <label for="alamat" class="col-md-2 col-form-label">Alamat</label>
                        <div class="col-md-10">
                        <input placeholder="Silahkan isi alamat" type="text" class="form-control" id="alamat" name="alamat">
                        </div>
                    </div>
                    <!-- Hasil Validasi Bank -->
                    <div class="mb-3 row">
                        <label for="hasil_validasi" class="col-md-12 col-form-label" style="text-align: center;">Hasil
                            Validasi Bank</label>
                        <div class="col-md-10">
                        </div>
                    </div>
                    <!-- No Rekening Bank -->
                    <div class="mb-3 row">
                        <label for="no_rekening_validasi" class="col-md-2 col-form-label">No Rekening Bank</label>
                        <div class="col-md-10">
                            <input class="form-control @error('no_rekening_validasi') is-invalid @enderror" readonly
                                type="text" value="{{ old('no_rekening_validasi') }}" id="no_rekening_validasi"
                                name="no_rekening_validasi">
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
                            <input class="form-control @error('nm_rekening_validasi') is-invalid @enderror" readonly
                                type="text" value="{{ old('nm_rekening_validasi') }}" id="nm_rekening_validasi"
                                name="nm_rekening_validasi">
                            @error('nm_rekening_validasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- NPWP -->
                    <div class="mb-3 row">
                        <label for="npwp_validasi" class="col-md-2 col-form-label">NPWP</label>
                        <div class="col-md-10">
                            <input class="form-control @error('npwp_validasi') is-invalid @enderror" type="text"
                                readonly value="{{ old('npwp_validasi') }}" id="npwp_validasi" name="npwp_validasi">
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
                                type="text" value="{{ old('nm_npwp_validasi') }}" id="nm_npwp_validasi"
                                name="nm_npwp_validasi">
                            @error('nm_npwp_validasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div style="float: right;">
                        <button id="save" class="btn btn-primary btn-md">Simpan</button>
                        <a href="{{ route('penerima.index') }}" class="btn btn-warning btn-md">Kembali</a>
                    </div>
                    {{-- </form> --}}
                </div>
            </div>
        </div> <!-- end col -->
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            $('.select2-multiple').select2({
                theme: 'bootstrap-5'
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
            $('#jenis').on('select2:select', function() {
                let bank = $('#bank').val();

                if (!bank) {
                    alert('Silahkan pilih Bank terlebih dahulu!');
                    $('#jenis').val(null).change();
                    return;
                }

                let jenis = this.value;

                $('#rekanan').val(null);
                $('#pimpinan').val(null);
                $('#alamat').val(null);
            });

            // $('#rekanan').on('change', function() {
            //     let rekanan = this.value;
            //     let pimpinan = $(this).find(':selected').data('pimpinan');
            //     let alamat = $(this).find(':selected').data('alamat');
            //     $("#nama_rekan").val(rekanan);
            //     $("#pimpinan").val(pimpinan);
            //     $("#alamat").val(alamat);
            // });

            let bank1 = document.getElementById('bank').value;
            if (bank1) {
                let bic = document.getElementById('bic').value;
                let cabang = "{{ old('cabang') }}";
                $.ajax({
                    url: "{{ route('penerima.cabang') }}",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        "bic": bic,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data) {
                        $('#cabang').empty();
                        $('#cabang').append(`<option value="0">Pilih Cabang Pusat</option>`);
                        $.each(data, function(index, data) {
                            if (data.kode == cabang) {
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
            }

            let kd_map = document.getElementById('kode_akun').value;
            if (kd_map) {
                let kode_setor = "{{ old('kode_setor') }}";
                $.ajax({
                    type: "POST",
                    url: "{{ route('penerima.kodeSetor') }}",
                    dataType: 'json',
                    data: {
                        "kd_map": kd_map,
                        "_token": "{{ csrf_token() }}",
                    },
                    dataType: "json",
                    success: function(data) {
                        $('#kode_setor').empty();
                        $('#kode_setor').append(`<option value="0">Pilih Kode Setor</option>`);
                        $.each(data, function(index, data) {
                            if (data.kd_setor == kode_setor) {
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
            }

            $('#bank').on("select2:select", function() {
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

            $('#cabang').on('select2:select', function() {
                let selected = $(this).find('option:selected');
                let nama_cabang = selected.data('nama');
                $("#nama_cabang").val(nama_cabang);
            });

            $('#kode_akun').on("select2:select", function() {
                let kd_map = this.value;
                $.ajax({
                    type: "POST",
                    url: "{{ route('penerima.kodeSetor') }}",
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
                let jenis = document.getElementById('jenis').value;
                let kode_bank = document.getElementById('bank').value;
                let no_rek = document.getElementById('rekening').value;
                let nm_rek = document.getElementById('nm_rekening').value;

                let digit = $('#bank').find('option:selected').data('digit');

                if (!kode_bank) {
                    alert('Bank harus dipilih!');
                    exit;
                }
                if (!no_rek) {
                    alert('No rekening harus diisi!');
                    exit;
                }
                if (!nm_rek) {
                    alert('Nama rekening harus diisi!');
                    exit;
                }

                 // if (kode_bank && no_rek && nm_rek) {
                //     Swal.fire({
                //         title: 'Sabar Ya!',
                //         html: 'Cek rekening dalam <strong></strong> detik.',
                //         timer: 3000,
                //         willOpen: function() {
                //             Swal.showLoading()
                //             timerInterval = setInterval(function() {
                //                 Swal.getContent().querySelector('strong')
                //                     .textContent = Swal.getTimerLeft()
                //             }, 100)
                //         },
                //         willClose: function() {
                //             clearInterval(timerInterval)
                //         }
                //     }).then(function(result) {
                //         if (
                //             // Read more about handling dismissals
                //             result.dismiss === Swal.DismissReason.timer
                //         ) {
                //             console.log('Loading');
                //         }
                //     })
                //     $.ajax({
                //         type: "POST",
                //         url: "{{ route('penerima.cekRekening') }}",
                //         dataType: 'json',
                //         data: {
                //             kode_bank: kode_bank,
                //             no_rek: no_rek,
                //             nm_rek: nm_rek,
                //         },
                //         success: function(data) {
                //             let data1 = $.parseJSON(data);
                //             if (data1.status) {
                //                 $("#no_rekening_validasi").val(data1.data[0].data
                //                     .nomorRekening);
                //                 $("#nm_rekening_validasi").val(data1.data[0].data
                //                     .namaPemilikRekening);
                //                 if (jenis == '1') {
                //                     $("#rekanan").val(data1.data[0].data
                //                         .namaPemilikRekening);
                //                 }
                //                 document.getElementById("save").disabled = false;
                //             } else {
                //                 alert(data1.message);
                //                 document.getElementById("save").disabled = true;
                //                 $("#no_rekening_validasi").attr("value", '');
                //                 $("#nm_rekening_validasi").attr("value", '');
                //                 $('#rekanan').val(null);
                //             }
                //         }
                //     })
                // }

                if (kode_bank && no_rek && nm_rek) {
                    swal.fire({
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        title: 'Proses cek Rekening Bank',
                        text: 'Silahkan tunggu !!!',
                        onOpen: function() {
                            swal.showLoading()
                        }
                    })
                    $.ajax({
                        type: "POST",
                        url: "{{ route('penerima.cekRekening') }}",
                        dataType: 'json',
                        data: {
                            kode_bank: kode_bank,
                            no_rek: no_rek,
                            nm_rek: nm_rek,
                            "_token": "{{ csrf_token() }}",
                        },
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
                            } else {
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
                                $('#rekanan').val(null);
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
                            type: "POST",
                            url: "{{ route('penerima.cekNpwp') }}",
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
                                                .data
                                                .namaWajibPajak + ' tersedia',
                                            icon: 'success',
                                            confirmButtonColor: '#5b73e8',
                                        })
                                        $("#npwp_validasi").val(data1.data[0].data
                                            .nomorPokokWajibPajak);
                                        $("#nm_npwp_validasi").val(data1.data[0].data
                                            .namaWajibPajak);
                                        document.getElementById("save").disabled = false;
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

            $('#save').on("click", function() {
                let bank = document.getElementById('bank').value;
                let nama_bank = document.getElementById('nama_bank').value;
                let bic = document.getElementById('bic').value;
                let cabang = document.getElementById('cabang').value;
                let nama_cabang = document.getElementById('nama_cabang').value;
                let jenis = document.getElementById('jenis').value;
                let keperluan = document.getElementById('keperluan').value;
                let no_rekening_validasi = document.getElementById('no_rekening_validasi').value;
                let nm_rekening_validasi = document.getElementById('nm_rekening_validasi').value;
                let kode_akun = document.getElementById('kode_akun').value;
                let kode_setor = document.getElementById('kode_setor').value;
                let npwp_validasi = document.getElementById('npwp_validasi').value;
                let nm_npwp_validasi = document.getElementById('nm_npwp_validasi').value;
                let keterangan = document.getElementById('keterangan').value;  
                let rekanan = document.getElementById('rekanan').value;
                let alamat = document.getElementById('alamat').value;
                let pimpinan = document.getElementById('pimpinan').value;
               
                if (!bank || !nama_bank) {
                    alert('Pilih Bank terlebih dahulu...!!!');
                    return
                }

                if (!bic) {
                    alert('BIC harus dipilih...!!!');
                    return
                }

                if (!cabang || !nama_cabang) {
                    alert('Pilih Nama Cabang terlebih dahulu...!!!');
                    return
                }

                if (!jenis) {
                    alert('Pilih Jenis Rekening terlebih dahulu...!!!');
                    return;
                }

                if (!kode_akun) {
                    alert('Kode Akun harus dipilih!');
                    return;
                }

                if (!kode_setor) {
                    alert('Kode Setor harus dipilih!');
                    return;
                }

                if (!keterangan) {
                    alert('Isi Keterangan Tambahan terlebih dahulu...!!!');
                    return;
                }

                if (!no_rekening_validasi) {
                    alert('No Rekening validasi harus ada!');
                    return;
                }

                if (isNaN(no_rekening_validasi)) {
                    alert('No Rekening validasi harus angka!');
                    return;
                }

                if (!nm_rekening_validasi) {
                    alert('Nama Rekening validasi harus ada!');
                    return;
                }

                if (!keperluan) {
                    alert('Pilih Keperluan terlebih dahulu...!!!');
                    return
                }

                if (keperluan != '1') {
                    if (!npwp_validasi) {
                        alert('NPWP Validasi wajib diisi!');
                        return
                    }

                    if (!nm_npwp_validasi) {
                        alert('Nama NPWP Validasi wajib diisi!');
                        return
                    }
                }

                if (!rekanan) {
                    alert("Isi Rekanan terlebih dahulu...!!!");
                    return;
                }

                if (!pimpinan) {
                    alert("Isi pimpinan terlebih dahulu...!!!");
                    return;
                }

                if (!alamat) {
                    alert("Isi pimpinan terlebih dahulu...!!!");
                    return;
                }

                let data = {
                    bank,
                    nama_bank,
                    bic,
                    cabang,
                    nama_cabang,
                    jenis,
                    keperluan,
                    no_rekening_validasi,
                    nm_rekening_validasi,
                    kode_akun,
                    kode_setor,
                    npwp_validasi,
                    nm_npwp_validasi,
                    keterangan,
                    rekanan,
                    alamat,
                    pimpinan,
                };
                // proses simpan
                $('#save').prop('disabled', true);
                $.ajax({
                    url: "{{ route('penerima.store') }}",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        data: data,
                        "_token": "{{ csrf_token() }}",
                    },
                    beforeSend: function() {
                        $("#overlay").fadeIn(100);
                    },
                    success: function(response) {
                        if (response.message == '1') {
                            alert('Data berhasil ditambahkan!');
                            window.location.href =
                                "{{ route('penerima.index') }}";
                        } else if (response.message == '2') {
                            alert('Rekening Telah Ada di SKPD!');
                            $('#save').prop('disabled', false);
                            return;
                        } else {
                            alert('Data tidak berhasil ditambahkan!');
                            $('#save').prop('disabled', false);
                            return;
                        }
                    },
                    complete: function(data) {
                        $("#overlay").fadeOut(100);
                    }
                })
            });
        });
    </script>
@endsection