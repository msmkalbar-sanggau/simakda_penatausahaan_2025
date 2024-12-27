@extends('template.app')
@section('title', 'SPP TU | SIMAKDA')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                Input SPP TU
            </div>
            <div class="card-body">
                <div class="container-fluid">
                    <!-- <div class="mb-3 row">
                        <label for="no_tersimpan" class="col-md-2 col-form-label">No Tersimpan</label>
                        <div class="col-md-4">
                            <input type="text" readonly class="form-control js-example-responsive" id="no_tersimpan" name="no_tersimpan" placeholder="Tidak Perlu diisi atau di Edit">
                            <input type="hidden" readonly class="form-control js-example-responsive" id="tahun" name="tahun" value="{{tahun_anggaran()}}">
                        </div>
                    </div> -->
                    <div class="mb-3 row">
                        <label for="kd_skpd" class="col-md-2 col-form-label">Kode OPD</label>
                        <div class="col-md-4">
                            <input type="text" value="{{ $data_skpd->kd_skpd }}" class="form-control js-example-responsive" id="kd_skpd" name="kd_skpd" readonly>
                            <input type="hidden" readonly class="form-control js-example-responsive" id="tahun" name="tahun" value="{{tahun_anggaran()}}">
                        </div>
                        <label for="nm_opd" class="col-md-2 col-form-label">Nama OPD</label>
                        <div class="col-md-4">
                            <input type="text" value="{{ $data_skpd->nm_skpd }}" class="form-control js-example-responsive" id="nm_opd" name="nm_opd" readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="tgl_spp" class="col-md-2 col-form-label">Tanggal SPP</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control js-example-responsive" id="tgl_spp" name="tgl_spp">
                            <!-- <input type="hidden" class="form-control" id="blnspp" name="blnspp" value=""> -->
                        </div>
                        <label for="bulan" class="col-md-2 col-form-label">Bulan</label>
                        <div class="col-md-4">
                            <select name="kebutuhan_bulan" class="form-control js-example-responsive" id="kebutuhan_bulan" disabled>
                                <option value="">Pilih Kebutuhan Bulan</option>
                                <option value="1">1 | Januari</option>
                                <option value="2">2 | Februari</option>
                                <option value="3">3 | Maret</option>
                                <option value="4">4 | April</option>
                                <option value="5">5 | Mei</option>
                                <option value="6">6 | Juni</option>
                                <option value="7">7 | Juli</option>
                                <option value="8">8 | Agustus</option>
                                <option value="9">9 | September</option>
                                <option value="10">10 | Oktober</option>
                                <option value="11">11 | November</option>
                                <option value="12">12 | Desember</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="jenis_beban" class="col-md-2 col-form-label">Jenis Beban</label>
                        <div class="col-md-4">
                            <select class="form-control js-example-responsive" id="jenis_beban" name="jenis_beban">
                                <option value="3" selected>TU</option>
                            </select>
                        </div>
                        <label for="no_spp" class="col-md-2 col-form-label">No SPP</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control js-example-responsive" style="background-color:powderblue;" id="no_spp" name="no_spp" readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="no_spd" class="col-md-2 col-form-label">No SPD</label>
                        <div class="col-md-4">
                            <select class="form-control js-example-responsive" id="no_spd" name="no_spd">
                            </select>
                        </div>
                        <label for="tgl_spd" class="col-md-2 col-form-label">Tanggal SPD</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control" id="tgl_spd" name="tgl_spd" readonly>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="kd_subkeg" class="col-md-2 col-form-label">Kode Sub Kegiatan</label>
                        <div class="col-md-4">
                            <select class="form-control js-example-responsive" id="kd_subkeg" name="kd_subkeg">
                            </select>
                        </div>
                        <label for="nm_sub" class="col-md-2 col-form-label">Nama Sub Kegiatan</label>
                        <div class="col-md-4">
                            <input type="text" readonly class="form-control" id="nm_sub" name="nm_sub">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="bank" class="col-md-2 col-form-label">Bank</label>
                        <div class="col-md-4">
                            <select class="form-control js-example-responsive" id="bank" name="bank">
                                <option value="">Pilih Rekening</option>
                                @foreach ($master_bank as $bank)
                                <option value="{{ $bank->kode }}">
                                    Nama Bank : {{ $bank->nama }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <label for="rek_bank" class="col-md-2 col-form-label">Rekening Bank</label>
                        <div class="col-md-4">
                            <select class="form-control js-example-responsive" id="rek_bank" name="rek_bank">
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="keperluan" class="col-md-2 col-form-label">Keperluan</label>
                        <div class="col-md-4">
                            <textarea name="keperluan" id="keperluan" class="form-control js-example-responsive" style="height:100px;"></textarea>
                        </div>
                        <label for="npwp" class="col-md-2 col-form-label">NPWP</label>
                        <div class="col-md-4">
                            <input type="text" readonly class="form-control js-example-responsive" id="npwp" name="npwp">
                        </div>
                    </div>
                    <div style="float: right;">
                        <button id="simpan" class="btn btn-primary btn-md" type="submit" onclick="SimpanData()">Simpan</button>
                        <a href="{{ route('spptu.index') }}" class="btn btn-warning btn-md">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Detail Rekening SPP -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                Input Detail SPP TU
                <button type="button" style="float: right" id="tambah_rincian" data-toggle="modal" class="btn btn-primary"><i class="bx bx-plus-circle"></i> Tambah Rekening</button>
            </div>
            <div class="card-body table-responsive">
                <table id="rincian_spptu" class="table" style="width: 100%">
                    <thead>
                        <tr>
                            <th>Rekening</th>
                            <th>Nama Rekening</th>
                            <th>Nilai</th>
                            <th>Sumber</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <div class="mb-2 mt-2 row">
                    <label for="total_nilai" class="col-md-8 col-form-label" style="text-align: right">Total</label>
                    <div class="col-md-4">
                        <input type="text" style="text-align: right" readonly class="form-control @error('total_nilai') is-invalid @enderror" id="total_nilai" name="total_nilai">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade bd-example-modal-xl js-example-responsive" tabindex="-1" role="dialog" id="myModal">
    <div class="modal-dialog modal-xl modal-dialog-scrollable js-example-responsive">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header bg-secondary">
                <h5 class="modal-title text-white">Rincian Rekening SPP</h5>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <div class="mb-3 row">
                    <label for="no_spdd" class="col-md-3 col-form-label js-example-responsive">No SPD</label>
                    <div class="col-md-6">
                        <input type="text" readonly class="form-control js-example-responsive" id="no_spdd" name="no_spdd">
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="kd_sub_keg" class="col-md-3 col-form-label js-example-responsive">Kode Sub Kegiatan</label>
                    <div class="col-md-6">
                        <input type="text" readonly class="form-control js-example-responsive" id="kd_sub_keg" name="kd_sub_keg">
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="nm_sub_keg" class="col-md-3 col-form-label js-example-responsive">Nama Sub Kegiatan</label>
                    <div class="col-md-6">
                        <input type="text" readonly class="form-control js-example-responsive" id="nm_sub_keg" name="nm_sub_keg">
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="kd_rek" class="col-md-3 col-form-label js-example-responsive">Kode Rekening</label>
                    <div class="col-md-6">
                        <select class="form-control js-example-responsive" id="kd_rek" name="kd_rek">
                        </select>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="kd_sumberdana" class="col-md-3 col-form-label js-example-responsive">Sumber Dana</label>
                    <div class="col-md-6">
                        <select class="form-control js-example-responsive" id="kd_sumberdana" name="kd_sumberdana">
                        </select>
                    </div>
                </div>


                <!-- Total SPD -->
                <div class="mb-3 row">
                    <label for="total_spd" class="col-md-2 col-form-label js-example-responsive">Total SPD</label>
                    <div class="col-md-2">
                        <input type="text" readonly class="form-control js-example-responsive" name="total_spd" id="total_spd">
                    </div>
                    <label for="realisasi_angkas" class="col-md-1 col-form-label js-example-responsive">Realisasi</label>
                    <div class="col-md-3">
                        <input type="text" readonly class="form-control js-example-responsive" name="realisasi_spd" id="realisasi_spd">
                    </div>
                    <label for="sisa_angkas" class="col-md-1 col-form-label js-example-responsive">Sisa</label>
                    <div class="col-md-3">
                        <input type="text" readonly class="form-control js-example-responsive" style="background-color:#B0E0E6;" name="sisa_spd" id="sisa_spd">
                    </div>
                </div>

                <!-- Total Angkas -->
                <div class="mb-3 row">
                    <label for="total_angkas" class="col-md-2 col-form-label js-example-responsive">Total Anggaran Kas</label>
                    <div class="col-md-2">
                        <input type="text" readonly class="form-control js-example-responsive" name="total_angkas" id="total_angkas">
                    </div>
                    <label for="realisasi_angkas" class="col-md-1 col-form-label js-example-responsive">Realisasi</label>
                    <div class="col-md-3">
                        <input type="text" readonly class="form-control js-example-responsive" name="realisasi_angkas" id="realisasi_angkas">
                    </div>
                    <label for="sisa_angkas" class="col-md-1 col-form-label js-example-responsive">Sisa</label>
                    <div class="col-md-3">
                        <input type="text" readonly class="form-control js-example-responsive" style="background-color:#B0E0E6;" name="sisa_angkas" id="sisa_angkas">
                    </div>
                </div>

                <!-- Anggaran -->
                <div class="mb-3 row">
                    <label for="total_anggaran" class="col-md-2 col-form-label js-example-responsive">Anggaran</label>
                    <div class="col-md-2">
                        <input type="text" readonly class="form-control js-example-responsive" name="total_anggaran" id="total_anggaran">
                    </div>
                    <label for="realisasi_anggaran" class="col-md-1 col-form-label js-example-responsive">Realisasi</label>
                    <div class="col-md-3">
                        <input type="text" readonly class="form-control js-example-responsive" name="realisasi_anggaran" id="realisasi_anggaran">
                    </div>
                    <label for="sisa_anggaran" class="col-md-1 col-form-label js-example-responsive">Sisa</label>
                    <div class="col-md-3">
                        <input type="text" readonly class="form-control js-example-responsive" style="background-color:#B0E0E6;" name="sisa_anggaran" id="sisa_anggaran">
                    </div>
                </div>

                <!-- Nilai Sumber Dana -->
                <div class="mb-3 row">
                    <label for="total_sumber" class="col-md-2 col-form-label js-example-responsive">Sumber Dana</label>
                    <div class="col-md-2">
                        <input type="text" readonly class="form-control js-example-responsive" name="total_sumber" id="total_sumber">
                    </div>
                    <label for="realisasi_sumber" class="col-md-1 col-form-label js-example-responsive">Realisasi</label>
                    <div class="col-md-3">
                        <input type="text" readonly class="form-control js-example-responsive" name="realisasi_sumber" id="realisasi_sumber">
                    </div>
                    <label for="sisa_sumber" class="col-md-1 col-form-label js-example-responsive">Sisa</label>
                    <div class="col-md-3">
                        <input type="text" readonly class="form-control js-example-responsive" style="background-color:#B0E0E6;" name="sisa_sumber" id="sisa_sumber">
                    </div>
                </div>

                <!-- Status Anggaran dan Anggaran Kas -->
                <div class="mb-3 row">
                    <label for="status_anggaran" class="col-md-2 col-form-label js-example-responsive">Status Anggaran</label>
                    <div class="col-md-2">
                        <input type="text" readonly class="form-control js-example-responsive" name="status_anggaran" id="status_anggaran">
                    </div>
                    <label for="status_angkas" class="col-md-2 col-form-label js-example-responsive">Status Anggaran Kas</label>
                    <div class="col-md-2">
                        <input type="text" readonly class="form-control js-example-responsive" name="status_angkas" id="status_angkas">
                    </div>
                </div>

                <!-- Inputan nilai -->
                <div class="mb-3 row">
                    <label for="input_nilai" class="col-md-2 col-form-label js-example-responsive">Nilai</label>
                    <div class="col-md-3">
                        <input type="text" class="form-control js-example-responsive" id="input_nilai" name="input_nilai" data-type="currency">
                    </div>
                </div>
                <div class=" mb-4 row text-center">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-success" id="tambahrincianrekening" style="margin-right:4px"><i class="bx bx-plus-circle"></i> Tambah Rincian Rekening
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <!-- <div class="card"> -->
                        <div class="card-body table-responsive">
                            <table id="detailrincian_spptu" class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>Rekening</th>
                                        <th>Nama Rekening</th>
                                        <th>Nilai</th>
                                        <th>Sumber</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <!-- </div> -->
                    </div>
                </div>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btnbtn_close btn-danger" data-dismiss="modal">Kembali</button>
            </div>

        </div>
    </div>
</div>
<!-- End -->

@endsection
@section('js')
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        // Panggilan Function
        getnomorspp();
        // Status Anggaran
        $.ajax({
            url: "{{ route('statusanggaran.cek_status_ang') }}",
            type: "POST",
            dataType: 'json',
            success: function(data) {
                $('#status_anggaran').val(data.nama);
            }
        });
        // Status Anggaran Kas
        $.ajax({
            url: "{{ route('statusanggaran.cek_status_angkas') }}",
            type: "POST",
            dataType: 'json',
            success: function(data) {
                $('#status_angkas').val(data.status);
            }
        });

    });



    $(document).ready(function() {
        var bulanspp = '';
        var tgl_spp = '';
        var bln = '';

        // Datatables rincian SPP 
        let rincian = $('#detailrincian_spptu').DataTable({
            responsive: true,
            ordering: false,
            scrollY: "200px",
            lengthMenu: [5, 10, 25, 50],
            columns: [{
                    data: 'kd_rek6'
                },
                {
                    data: 'nm_rek6'
                },
                {
                    data: 'nilai'
                },
                {
                    data: 'sumber'
                }
            ]
        });
        // End
        // Datatables SPP
        let spptu = $('#rincian_spptu').DataTable({
            responsive: true,
            ordering: false,
            scrollY: "200px",
            lengthMenu: [5, 10, 25, 50],
            columns: [{
                    data: 'kd_rek6'
                },
                {
                    data: 'nm_rek6'
                },
                {
                    data: 'nilai'
                },
                {
                    data: 'sumber'
                },
                {
                    data: 'aksi'

                }
            ]
        });
        // End

        // Oclick RincianRekening
        $('#tambahrincianrekening').on('click', function() {
            let kd_rek6 = $('#kd_rek').val();
            let nm_rek6 = $('#kd_rek option:selected').data('nm_rek6');
            let kd_sumber = $('#kd_sumberdana').val();
            let nilai_rincian = angka($("#input_nilai").val());
            let nilai_sementara = rupiah(document.getElementById('total_nilai').value);
            let total = nilai_rincian + nilai_sementara;

            if (kd_rek6 == null || kd_rek6 == '') {
                alert("Kode Rekening tidak boleh kosong");
                return;
            } else if (kd_sumber == null || kd_sumber == '') {
                alert("Sumber Dana tidak boleh kosong");
                return;
            } else if (nilai_rincian == 0) {
                alert("Nilai Masih 0");
                return;
            }
            let tampungan = rincian.rows().data().toArray().map((value) => {
                let result = {
                    kd_rek6: value.kd_rek6,
                    sumber: value.sumber,
                };
                return result;
            });

            let kondisi = tampungan.map(function(data) {
                if (data.kd_rek6 == kd_rek6 && data.sumber == kd_sumber) {
                    return '2';
                }
            });
            if (kondisi == '2') {
                alert("Tidak boleh memilih rekening dengan sumber dana yang sama dalam 1 SPP");
                return;
            }

            // 1
            rincian.row.add({
                'kd_rek6': kd_rek6,
                'nm_rek6': nm_rek6,
                'nilai': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(nilai_rincian),
                'sumber': kd_sumber,
            }).draw();
            // 2
            spptu.row.add({
                'kd_rek6': kd_rek6,
                'nm_rek6': nm_rek6,
                'nilai': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(nilai_rincian),
                'sumber': kd_sumber,
                'aksi': `<a href="javascript:void(0);" onclick="deleteData('${kd_rek6}','${nm_rek6}','${kd_sumber}','${nilai_rincian}')" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>`
            }).draw();

            $('#total_nilai').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total));

        });

        $('#kd_subkeg').select2({
            placeholder: 'Pilih Sub Kegiatan',
            theme: 'bootstrap-5'
        });
        $('#no_spd').select2({
            placeholder: 'Pilih No SPD',
            theme: 'bootstrap-5'
        });
        // Bank
        $('#bank').select2({
            placeholder: 'Pilih Bank',
            theme: 'bootstrap-5'
        }).on('change', function() {
            $.ajax({
                url: "{{ route('spptu.rekeningbank') }}",
                dataType: 'json',
                type: "POST",
                // delay: 100,
                success: function(data) {
                    $('#rek_bank').append(
                        `<option value="">Pilih Rekening Bank</option>`);
                    $.each(data, function(index, data) {
                        $('#rek_bank').append(
                            `<option value="${data.rekening}" data-npwp="${data.npwp}" data-nm_rekening="${data.nm_rekening}">Nama : ${data.nm_rekening} | No Rekening : ${data.rekening} | No NPWP : ${data.npwp} </option>`
                        );
                    });
                },
            });

        });

        $('#rek_bank').select2({
            theme: 'bootstrap-5'
        });

        $('#kd_sumberdana').select2({
            placeholder: 'Pilih Sumber Dana',
            theme: 'bootstrap-5'
        });


        // Onchange Rekening Bank
        $('#rek_bank').on('change', function() {
            let npwp = $('#rek_bank option:selected').data('npwp');
            $("#npwp").val(npwp);

        });

        // Onchange tgl_spp
        $('#tgl_spp').on('change', function() {
            var tgl_spp = this.value;
            const d = new Date(tgl_spp);
            var blnspp = (d.getMonth() + 1);
            $("#kebutuhan_bulan").val(blnspp).change();
            // Ajax
            $.ajax({
                url: "{{ route('spptu.spd') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    tglspp: tgl_spp
                },
                success: function(data) {
                    $('#no_spd').append(
                        `<option value="">Pilih SPD</option>`);
                    $.each(data, function(index, data) {
                        $('#no_spd').append(
                            `<option value="${data.no_spd}" data-tglspd="${data.tgl_spd}" >${data.no_spd} | ${new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR" }).format(data.nilai)} | ${data.tgl_spd} </option>`
                        );
                    });
                }
            });
        });

        // onchange no_spd
        $('#no_spd').on('change', function() {
            let no_spd = this.value;
            let tgl_spd = $('#no_spd option:selected').data('tglspd');
            $("#tgl_spd").val(tgl_spd).trigger('change');

            $.ajax({
                url: "{{ route('spptu.subkegiatan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    cspd: no_spd
                },
                success: function(data) {
                    $('#kd_subkeg').append(
                        `<option value="">Pilih Sub Kegiatan</option>`);
                    $.each(data, function(index, data) {
                        $('#kd_subkeg').append(
                            `<option value="${data.kd_sub_kegiatan}" data-nm_sub_kegiatan="${data.nm_sub_kegiatan}" data-kd_program="${data.kd_program}" data-nm_program="${data.nm_program}" >${data.kd_sub_kegiatan} | ${data.nm_sub_kegiatan} </option>`
                        );
                    });
                }
            });
        });

        // onchange kd_subkeg
        $('#kd_subkeg').on('change', function() {
            let kd_subkeg = this.value;
            let nm_subkeg = $('#kd_subkeg option:selected').data('nm_sub_kegiatan');
            $("#nm_sub").val(nm_subkeg).trigger('change');

        });

        // Onclick tambah rincian Spp
        $('#tambah_rincian').on('click', function() {
            let no_spd = $("#no_spd").val();
            let kd_subkeg = $("#kd_subkeg").val();
            let nm_subkeg = $("#nm_sub").val();
            let nilai_rincian = $("#input_nilai").val();
            $('#kd_rek').empty();
            $('#kd_sumberdana').empty();
            kosong_rincian();
            $('#no_spdd').val(no_spd);
            $('#kd_sub_keg').val(kd_subkeg);
            $('#nm_sub_keg').val(nm_subkeg);
            // Kondisi
            if (no_spd == null || no_spd == '') {
                alert("No SPD tidak ada");
                return;
            }
            if (kd_subkeg == null || kd_subkeg == '') {
                alert("Kode Sub Kegiatan Kosong");
                return;
            }
            // End
            // Modal Pop Up
            $('#myModal').modal('show');

            $('#kd_rek').select2({
                placeholder: 'Pilih Kode Rekening',
                theme: 'bootstrap-5',
                dropdownParent: $("#myModal")
            });

            $.ajax({
                url: "{{ route('spptu.rekeningkegiatan') }}",
                dataType: 'json',
                type: "POST",
                data: {
                    ckd_subkeg: kd_subkeg
                },
                success: function(data) {
                    var kd_rek = data.kd_rek6;
                    $('#kd_rek').append(
                        `<option value="">Pilih Kode Rekening</option>`);
                    $.each(data, function(index, data) {
                        $('#kd_rek').append(
                            `<option value="${data.kd_rek6}" data-nm_rek6="${data.nm_rek6}">${data.kd_rek6} | ${data.nm_rek6} </option>`
                        );
                    });
                }
            });
        });

        // onchange kode rekening
        $('#kd_rek').on('change', function() {
            let kd_rek6 = this.value;
            let kd_subkeg = $("#kd_subkeg").val();
            // alert(kd_subkeg);
            $.ajax({
                url: "{{ route('spptu.sumberdana') }}",
                dataType: 'json',
                type: "POST",
                data: {
                    ckd_rek: kd_rek6,
                    ckd_subkeg: kd_subkeg
                },
                success: function(data) {
                    $('#kd_sumberdana').empty();
                    $('#kd_sumberdana').append(
                        `<option value="">Pilih Sumberdana</option>`);
                    $.each(data, function(index, data) {
                        $('#kd_sumberdana').append(
                            `<option value="${data.sumber}" data-nilaisumber="${new Intl.NumberFormat("id-ID",{
                        minimumFractionDigits: 2}).format(data.nilai)}">${data.sumber} | Rp. ${new Intl.NumberFormat("id-ID",{
                        minimumFractionDigits: 2}).format(data.nilai)} </option>`
                        );
                    }); // alert(no_spd);
                }
            });
        });

        // ochange sumber dana
        $('#kd_sumberdana').on('change', function() {
            let kd_sumberdana = this.value;
            let nilaisumber = $('#kd_sumberdana option:selected').data('nilaisumber');
            $("#total_sumber").val(nilaisumber).trigger('change');
            let no_spd = $("#no_spd").val();
            let kd_subkeg = $("#kd_subkeg").val();
            let kd_rek = $("#kd_rek").val();
            let tgl_spp = $("#tgl_spp").val();
            let status_angkas = $("#status_angkas").val();
            let beban = $("#jenis_beban").val();
            // Total SPD
            $.ajax({
                url: "{{ route('spptu.totalspd') }}",
                dataType: 'json',
                type: "POST",
                data: {
                    cno_spd: no_spd,
                    ctgl_spp: tgl_spp,
                    ckd_rek: kd_rek,
                    ckd_subkeg: kd_subkeg
                },
                success: function(data) {
                    let total_spd = parseFloat(data.total_spd) || 0;
                    $("#total_spd").val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(total_spd));
                }
            });
            // Total Angkas
            $.ajax({
                url: "{{ route('spptu.totalangkas') }}",
                dataType: 'json',
                type: "POST",
                data: {
                    ctgl_spp: tgl_spp,
                    ckd_rek: kd_rek,
                    ckd_subkeg: kd_subkeg,
                    cbeban: beban,
                    cstatus_angkas: status_angkas,
                },
                success: function(data) {
                    let total_angkas = parseFloat(data.nilai) || 0;
                    $("#total_angkas").val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(total_angkas));
                }
            });
            // Total Anggaran 
            $.ajax({
                url: "{{ route('spptu.totalanggaran') }}",
                dataType: 'json',
                type: "POST",
                data: {
                    ckd_rek: kd_rek,
                    ckd_subkeg: kd_subkeg
                },
                success: function(data) {
                    let total_anggaran = parseFloat(data.totalanggaran) || 0;
                    $("#total_anggaran").val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(total_anggaran));
                }
            });
            // Realisasi Sumber Dana
            $.ajax({
                url: "{{ route('spptu.realisasi') }}",
                dataType: 'json',
                type: "POST",
                data: {
                    ckd_rek: kd_rek,
                    ckd_subkeg: kd_subkeg,
                    ckd_sumberdana: kd_sumberdana
                },
                success: function(data) {
                    let realisasi = parseFloat(data.totalrealisasi) || 0;
                    $("#realisasi_spd").val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(realisasi));
                    $("#realisasi_angkas").val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(realisasi));
                    $("#realisasi_anggaran").val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(realisasi));
                    $("#realisasi_sumber").val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(realisasi));

                    // Sisa Hasil SPD
                    let realisasi_spd = $('#realisasi_spd').val();
                    let total_spd = $('#total_spd').val();
                    let sisa_spd = rupiah(total_spd) - rupiah(realisasi_spd);
                    $('#sisa_spd').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(sisa_spd));
                    // Sisa Hasil Angkas
                    let realisasi_angkas = $('#realisasi_angkas').val();
                    let total_angkas = $('#total_angkas').val();
                    let sisa_angkas = rupiah(total_angkas) - rupiah(realisasi_angkas);
                    $('#sisa_angkas').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(sisa_angkas));
                    // Sisa Hasil Anggaran
                    let realisasi_anggaran = $('#realisasi_anggaran').val();
                    let total_anggaran = $('#total_anggaran').val();
                    let sisa_anggaran = rupiah(total_anggaran) - rupiah(realisasi_anggaran);
                    $('#sisa_anggaran').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(sisa_anggaran));
                    // Sisa Hasil Sumber Dana
                    let realisasi_sumber = $('#realisasi_sumber').val();
                    let total_sumber = $('#total_sumber').val();
                    let sisa_sumber = rupiah(total_sumber) - rupiah(realisasi_sumber);
                    $('#sisa_sumber').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(sisa_sumber));
                }
            });

        });

        // Onclick Kembali rincian Spp
        $('.btnbtn_close').on('click', function() {
            // kosong_input_detail();
            $('#myModal').modal('hide');
        });

        // Dialog static
        $('#myModal').modal({
            backdrop: "static"
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

    function deleteData(kd_rek6, nm_rek6, kd_sumber, nilai_rincian) {
        let nilai = rupiah(nilai_rincian);
        alert(nilai);
        let nilai_sementara = rupiah(document.getElementById('total_nilai').value);
        alert(nilai_sementara);
        let akumulasi = nilai_sementara - nilai;
        let hapus = confirm('Yakin Ingin Menghapus Data, Rekening : ' + kd_rek6 + '  Nilai :  ' + new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(nilai_rincian) +
            ' ?');
        let tabel = $('#rincian_spptu').DataTable();
        let tabel1 = $('#detailrincian_spptu').DataTable();
        if (hapus == true) {
            tabel.row(function(idx, data, node) {
                return data.kd_rek6 == kd_rek6 && data.nm_rek6 == nm_rek6
            }).remove().draw();
            tabel1.row(function(idx, data, node) {
                return data.kd_rek6 == kd_rek6 && data.nm_rek6 == nm_rek6
            }).remove().draw();
        } else {
            return false;
        }
        $('#total_nilai').val(new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2
        }).format(akumulasi));
    }

    function formatNumber(n) {
        // format number 1000000 to 1,234,567
        return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
    }

    function formatCurrency(input, blur) {
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


    function getnomorspp() {
        var beban = "TU";
        kd_skpd = $('#kd_skpd').val();
        tahun_anggaran = $('#tahun').val();
        $.ajax({
            url: "{{ route('spptu.cari_nospptu') }}",
            type: "POST",
            dataType: 'json',
            success: function(data) {
                let no_spp = data.nilai + "/SPP/" + beban + "/" + kd_skpd + "/" + tahun_anggaran;
                $('#no_spp').val(no_spp);
            }
        });
    }

    function SimpanData() {
        // kiri
        let kd_skpd = $("#kd_skpd").val();
        let tgl_spp = $("#tgl_spp").val();
        let beban = $("#jenis_beban").val();
        let no_spd = $("#no_spd").val();
        let kd_subkeg = $("#kd_subkeg").val();
        let bank = $("#bank").val();
        let keperluan = $("#keperluan").val();
        // Kanan
        let nm_skpd = $("#nm_opd").val();
        let kebutuhan_bulan = $("#kebutuhan_bulan").val();
        let no_spp = $("#no_spp").val();
        let nm_subkeg = $("#nm_sub").val();
        let rek_bank = $("#rek_bank").val();
        let npwp = $("#npwp").val();
        let nm_rekening = $('#rek_bank option:selected').data('nm_rekening');

        // Total Nilai
        let total_nilai = rupiah($("#total_nilai").val());
        // End
        let tabel = $('#detailrincian_spptu').DataTable();
        let datarincian_rekening = tabel.rows().data().toArray().map((value) => {
            let data = {
                kd_rek6: value.kd_rek6,
                nm_rek6: value.nm_rek6,
                nilai: rupiah(value.nilai),
                sumber: value.sumber,
            };
            return data;
        });
        let data = {
            kd_skpd,
            tgl_spp,
            beban,
            no_spd,
            kd_subkeg,
            bank,
            keperluan,
            nm_skpd,
            kebutuhan_bulan,
            no_spp,
            nm_subkeg,
            rek_bank,
            nm_rekening,
            npwp,
            total_nilai
        }

        if (no_spd == null || no_spd == '') {
            alert('SPD Kosong');
            return;
        }
        if (kd_subkeg == null || kd_subkeg == '') {
            alert('Sub Kegiatan Kosong');
            return;
        }
        if (bank == null || bank == '') {
            alert('Isian Bank Kosong');
            return;
        }
        if (rek_bank == null || rek_bank == '') {
            alert('Isian Rekening Bank Kosong');
            return;
        }
        if (keperluan == null || keperluan == '') {
            alert('Keperluan Kosong');
            return;
        }

        // cek simpan dan simpan data
        $.ajax({
            url: "{{ route('spptu.cek_simpan') }}",
            type: "POST",
            dataType: 'json',
            data: {
                cno_spp: no_spp,
            },
            success: function(response) {
                if (response == 1) {
                    alert("Data Sudah ada !");
                    return;
                } else if (response == 0) {
                    $.ajax({
                        url: "{{ route('spptu.simpandata') }}",
                        type: "POST",
                        dataType: 'json',
                        data: {
                            cdata1: datarincian_rekening,
                            cdata2: data
                        },
                        success: function(response) {
                            let message = response.message;
                            if (message == 2) {
                                alert('data berhasil disimpan');
                            }
                            location.reload();
                        }
                    });

                }
            }
        })

    }

    function kosong_rincian() {
        $("#total_sumber").val('');
        $('#total_angkas').val('');
        $('#total_spd').val('');
        $('#total_anggaran').val('');
        // Realiasi
        $('#realisasi_spd').val('');
        $('#realisasi_angkas').val('');
        $('#realisasi_anggaran').val('');
        $('#realisasi_sumber').val('');
        // Sisa
        $('#sisa_spd').val('');
        $('#sisa_angkas').val('');
        $('#sisa_anggaran').val('');
        $('#sisa_sumber').val('');
        // Inputan nilai
        $('#input_nilai').val('');
    }

    function nilai(n) {
        let nilai = n.split(',').join('');
        return parseFloat(nilai) || 0;
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