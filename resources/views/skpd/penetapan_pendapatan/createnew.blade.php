@extends('template.app')
@section('title', 'Input Data Penetapan | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Input Data Penetapan
                </div>
                <div class="card-body">
                    @csrf
                    <div class="mb-3 row jns_penetapan">
                        <label for="jns_penetapan" class="col-md-2 col-form-label">Jenis Penetapan</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple @error('jns_penetapan') is-invalid @enderror"
                                style="width: 100%;" id="jns_penetapan" name="jns_penetapan"
                                data-placeholder="Silahkan Pilih">
                                {{-- <optgroup label="Jenis Penetapan"> --}}
                                <option value="1">BPHTB</option>
                                <option value="2">SKP BAPENDA</option>
                                <option value="3">PBB BAPENDA</option>
                                <option value="4">PBB KASDA</option>
                                </option>
                                {{-- </optgroup> --}}
                            </select>
                            @error('nomor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <p style="color:red">*Jika tidak ada jenis penetapan tidak perlu dipilih (Opsional)</p>
                        </div>
                    </div>
                    {{-- No Penetapan dan Tanggal Penetapan --}}
                    <div class="mb-3 row">
                        <label for="no_tetap" class="col-md-2 col-form-label">No. Penetapan</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="no_tetap" name="no_tetap"
                                placeholder="Silahkan Diisi" required>
                        </div>
                        <label for="tgl_tetap" class="col-md-2 col-form-label">Tanggal Penetapan</label>
                        <div class="col-md-4">
                            <input class="form-control" type="date" id="tgl_tetap" name="tgl_tetap" required>
                            <input class="form-control" type="text" id="tahun_anggaran" name="tahun_anggaran" required
                                readonly hidden value="{{ tahun_anggaran() }}">
                        </div>
                    </div>
                    {{-- Kode dan Nama SKPD --}}
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">Kode SKPD</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="kd_skpd" name="kd_skpd" required readonly
                                value="{{ $skpd->kd_skpd }}">
                        </div>
                        <label for="nm_skpd" class="col-md-2 col-form-label">Nama SKPD</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="nm_skpd" name="nm_skpd" required readonly
                                value="{{ $skpd->nm_skpd }}">
                        </div>
                    </div>
                    {{-- Kode dan Nama Akun --}}
                    <div class="mb-3 row">
                        <label for="kode_akun" class="col-md-2 col-form-label">Kode Rekening</label>
                        <div class="col-md-4">
                            <select class="form-control select2-multiple" style="width: 100%" id="kode_akun"
                                name="kode_akun">
                                <option value="" disabled selected>Silahkan Pilih</option>
                                @foreach ($daftar_akun as $akun)
                                    <option value="{{ $akun->kd_rek6 }}" data-kd_sub_kegiatan="{{ $akun->kd_sub_kegiatan }}"
                                        data-nm_rek6="{{ $akun->nm_rek6 }}" data-kd_rek="{{ $akun->kd_rek }}">
                                        {{ $akun->kd_rek6 }} - {{ $akun->nm_rek6 }}
                                    </option>
                                @endforeach
                                </option>
                            </select>
                            <input class="form-control" type="text" id="kode_rek_lo" name="kode_rek_lo" readonly hidden>
                        </div>
                        <label for="jns_pajak" class="col-md-2 col-form-label">Jenis
                            Pajak</label>
                        <div class="col-md-4">
                            <select id="jns_pajak_tambah" name="jns_pajak_tambah">
                                <option value="">-- Pilih -- </option>
                                @foreach ($ms_pajak as $pajak)
                                    <option value="{{ $pajak->kode_pajak }}">
                                        {{ $pajak->kode_pajak }} | {{ $pajak->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- Sub Kegiatan dan Nilai --}}
                    <div class="mb-3 row">
                        <label for="kd_sub_kegiatan" class="col-md-2 col-form-label">Sub Kegiatan</label>
                        <div class="col-md-4">
                            <input class="form-control" type="text" id="kd_sub_kegiatan" name="kd_sub_kegiatan" required
                                readonly>
                        </div>
                        <label for="nilai" class="col-md-2 col-form-label">Nilai</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="nilai" id="nilai"
                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency" style="text-align: right">
                        </div>
                    </div>
                    {{-- Keterangan --}}
                    <div class="mb-3 row">
                        <label for="keterangan" class="col-md-2 col-form-label">Keterangan</label>
                        <div class="col-md-10">
                            <textarea class="form-control" style="width: 100%" id="keterangan" name="keterangan"></textarea>
                        </div>
                    </div>
                    <!-- SIMPAN -->
                    <div class="mb-3 row" style="float: right;">
                        <div class="col-md-12" style="text-align: center">
                            <button id="simpan" class="btn btn-primary btn-md">Simpan</button>
                            <a href="{{ route('penetapantahunini.index') }}" class="btn btn-warning btn-md">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
        $(document).ready(function() {
            $("#jns_pajak_tambah").attr("disabled", true);
            $('#kode_akun').select2({
                placeholder: "Silahkan Pilih",
                theme: 'bootstrap-5'
            }).on('select2:select', function() {
                let kd_rek6 = $('#kode_akun option:selected').val();
                let kd_sub_kegiatan = $('#kode_akun option:selected').data('kd_sub_kegiatan');
                let kd_rek_lo = $('#kode_akun option:selected').data('kd_rek');
                $('#kode_rek_lo').val(kd_rek_lo);
                $('#kd_sub_kegiatan').val(kd_sub_kegiatan);
                if (kd_rek6 == '410106010001' || kd_rek6 == '410114370001') {
                    $("#jns_pajak_tambah").attr("disabled", false);
                } else {
                    $("#jns_pajak_tambah").attr("disabled", true);
                }
            });
            $('#jns_pajak_tambah').select2({
                placeholder: "Silahkan Pilih",
                theme: 'bootstrap-5'
            });
            $('#jns_penetapan').select2({
                placeholder: "Silahkan Pilih",
                theme: 'bootstrap-5'
            }).on('select2:select', function() {
                let jns_tetap = $('#jns_penetapan option:selected').text();
                let tahun = {{ tahun_anggaran() }};
                $("#no_tetap").val('/' + jns_tetap + '/' + tahun);
            });

            $("input[data-type='currency']").on({
                keyup: function() {
                    formatCurrency($(this));
                },
                blur: function() {
                    formatCurrency($(this), "blur");
                }
            });
        });

        $(document).ready(function() {
            $('#simpan').on('click', function() {
                let no_penetapan = $("#no_tetap").val();
                let tgl_penetapan = $("#tgl_tetap").val();
                let kd_skpd = $("#kd_skpd").val();
                let kd_rek6 = $("#kode_akun").val();
                let kd_rek_lo = $("#kode_rek_lo").val();
                let jns_pajak = $("#jns_pajak_tambah").val();
                let keterangan = $("#keterangan").val();
                let kd_sub_kegiatan = $("#kd_sub_kegiatan").val();
                let nilai = angka($("#nilai").val());
                let thn_input = tgl_penetapan.substr(0, 4);
                let tahun = $("#tahun_anggaran").val();
                if (no_penetapan == '') {
                    alert("Nomor Penetapan tidak boleh kosong");
                    return;
                }
                if (tgl_penetapan == '') {
                    alert("Tanggal Penetapan tidak boleh kosong");
                    return;
                }
                if (kd_rek6 == null || kd_rek6 == '') {
                    alert("Rekening Penetapan tidak boleh kosong");
                    return;
                }
                if ((kd_rek6 == '410106010001' || kd_rek6 == '410114370001') && jns_pajak == '') {
                    alert("Jenis Pajak tidak boleh kosong");
                    return;
                }
                if (thn_input != tahun) {
                    alert("Tahun tidak sama dengan tahun anggaran");
                    return;
                }
                if (nilai == 0) {
                    alert("Nilai 0! Silahkan cek kembali");
                    return;
                }
                if (keterangan == '') {
                    alert("Keterangan tidak boleh kosong");
                    return;
                }
                let data = {
                    no_penetapan,
                    tgl_penetapan,
                    kd_skpd,
                    kd_rek6,
                    kd_rek_lo,
                    kd_sub_kegiatan,
                    jns_pajak,
                    keterangan,
                    nilai,
                }
                $.ajax({
                    url: "{{ route('simpanpenetapantahunini') }}",
                    type: 'POST',
                    data: {
                        data: data
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        $("#simpan").attr("disabled", "disabled");
                        $("#simpan").html('<i class="fa fa-spin fa-spinner"></i>');
                    },
                    success: function(response) {
                        if (response.sudahada) {
                            Swal.fire({
                                icon: 'warning',
                                text: response.sudahada,
                            })
                        } else if (response.berhasil) {
                            Swal.fire({
                                icon: 'success',
                                text: response.berhasil,
                            })
                        } else if (response.erorr) {
                            Swal.fire({
                                icon: 'error',
                                text: response.erorr,
                            })
                        }
                    },
                    complete: function(response) {
                        $("#simpan").removeAttr('disabled');
                        $("#simpan").html('Simpan');
                    },
                    error: function(message) {
                        console.log(message);
                    }
                });

            });
        });



        function formatNumber(n) {
            // format number 1000000 to 1,234,567
            return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
        }

        function formatCurrency(input, blur) {
            // appends $ to value, validates decimal side
            // and puts cursor back in right position.

            // get input value
            var input_val = input.val();

            // don't validate empty input
            if (input_val === "") {
                return;
            }

            // original length
            var original_len = input_val.length;

            // initial caret position
            var caret_pos = input.prop("selectionStart");

            // check for decimal
            if (input_val.indexOf(".") >= 0) {

                // get position of first decimal
                // this prevents multiple decimals from
                // being entered
                var decimal_pos = input_val.indexOf(".");

                // split number by decimal point
                var left_side = input_val.substring(0, decimal_pos);
                var right_side = input_val.substring(decimal_pos);

                // add commas to left side of number
                left_side = formatNumber(left_side);

                // validate right side
                right_side = formatNumber(right_side);

                // On blur make sure 2 numbers after decimal
                if (blur === "blur") {
                    right_side += "00";
                }

                // Limit decimal to only 2 digits
                right_side = right_side.substring(0, 2);

                // join number by .
                input_val = left_side + "." + right_side;

            } else {
                // no decimal entered
                // add commas to number
                // remove all non-digits
                input_val = formatNumber(input_val);
                input_val = input_val;

                // final formatting
                if (blur === "blur") {
                    input_val += ".00";
                }
            }

            // send updated string to input
            input.val(input_val);

            // put caret back in the right position
            var updated_len = input_val.length;
            caret_pos = updated_len - original_len + caret_pos;
            input[0].setSelectionRange(caret_pos, caret_pos);
        }

        function angka(n) {
            let nilai = n.split(',').join('');
            return parseFloat(nilai) || 0;
        }

        function rupiah(n) {
            let n1 = n.split('.').join('');
            let rupiah = n1.split(',').join('.');
            return parseFloat(rupiah) || 0;
        }
    </script>
@endsection
