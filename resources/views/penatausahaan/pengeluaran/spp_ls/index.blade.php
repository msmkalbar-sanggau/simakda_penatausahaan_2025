@extends('template.app')
@section('title', 'SPP LS | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <a href="{{ route('sppls.create') }}" id="tambah_spp_ls"
                        class="btn btn-primary {{ $cek['selisih_angkas'] > 0 ? 'disabled' : '' }} {{ $cek['status_angkas'] == '0' ? 'disabled' : '' }}"
                        style="float: right;" {{ $kunci == 1 ? 'hidden' : '' }}>Tambah</a>
                    <input type="text" id="selisih_angkas" hidden readonly value="{{ $cek['selisih_angkas'] }}">
                    <input type="text" id="status_ang" hidden readonly value="{{ $cek['status_ang'] }}">
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="spp_ls" class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th style="width: 25px">No.</th>
                                        <th style="width: 150px">Nomor SPP</th>
                                        <th style="width: 100px">Tanggal</th>
                                        <th style="width: 100px">Keterangan</th>
                                        <th style="width: 200px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{--    @foreach ($data_spp as $data)
                                        <tr>
                                            <td style="width: 25px">{{ $loop->iteration }}</td>
                                            <td style="width: 150px">{{ $data->no_spp }}</td>
                                            <td style="width: 100px">
                                                {{ \Carbon\Carbon::parse($data->tgl_spp)->locale('id')->isoFormat('D MMMM Y') }}
                                            </td>
                                            <td style="width: 400px">{{ $data->keperluan }}</td>
                                            <td style="width: 200px">
                                                <a href="{{ route('sppls.show', $data->no_spp) }}"
                                                    class="btn btn-info btn-sm"><i class="fas fa-info-circle"></i></a>
                                                <a href="{{ route('sppls.edit', $data->no_spp) }}"
                                                    class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                                <button type="button"
                                                    onclick="cetak('{{ $data->no_spp }}', '{{ $data->jns_spp }}', '{{ $data->kd_skpd }}')"
                                                    class="btn btn-success btn-sm"><i class="uil-print"></i></button>
                                                @if ($data->status == 0)
                                                    <a href="javascript:void(0);"
                                                        onclick="deleteData('{{ $data->no_spp }}');"
                                                        class="btn btn-danger btn-sm" id="delete"><i
                                                            class="fas fa-trash-alt"></i></a>
                                                    <button type="button"
                                                        onclick="batal_spp('{{ $data->no_spp }}', '{{ $data->jns_spp }}', '{{ $data->kd_skpd }}')"
                                                        class="btn btn-success btn-sm"><i class="uil-ban"></i></button>
                                                @else
                                                @endif

                                            </td>
                                        </tr>
                                    @endforeach --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div>

    {{-- modal cetak sppls --}}
    <div id="modal_cetak" class="modal" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cetak SPP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- No SPP --}}
                    <div class="mb-3 row">
                        <label for="no_spp" class="col-md-2 col-form-label">No SPP</label>
                        <div class="col-md-6">
                            <input type="text" readonly class="form-control" id="no_spp" name="no_spp">
                            <input type="text" hidden class="form-control" id="beban" name="beban">
                            <input type="text" hidden class="form-control" id="kd_skpd" name="kd_skpd">
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch form-switch-lg">
                                <input type="checkbox" class="form-check-input" id="tanpa_tanggal">
                                <label class="form-check-label" for="tanpa_tanggal">Tanpa Tanggal</label>
                            </div>
                        </div>
                    </div>
                    {{-- Bendahara --}}
                    <div class="mb-3 row">
                        <label for="bendahara" class="col-md-2 col-form-label">Bendahara</label>
                        <div class="col-md-6">
                            <select name="bendahara" class="form-control" id="bendahara">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                @foreach ($bendahara as $ttd)
                                    <option value="{{ $ttd->nip }}" data-nama="{{ $ttd->nama }}">
                                        {{ $ttd->nip }} | {{ $ttd->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="nama_bendahara" id="nama_bendahara" class="form-control" readonly>
                        </div>
                    </div>
                    {{-- PPTK --}}
                    <div class="mb-3 row">
                        <label for="pptk" class="col-md-2 col-form-label">PPTK</label>
                        <div class="col-md-6">
                            <select name="pptk" class="form-control" id="pptk">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                @foreach ($pptk as $ttd)
                                    <option value="{{ $ttd->nip }}" data-nama="{{ $ttd->nama }}">
                                        {{ $ttd->nip }} | {{ $ttd->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="nama_pptk" id="nama_pptk" class="form-control" readonly>
                        </div>
                    </div>
                    {{-- PA/KPA --}}
                    <div class="mb-3 row">
                        <label for="pa_kpa" class="col-md-2 col-form-label">PA/KPA</label>
                        <div class="col-md-6">
                            <select name="pa_kpa" class="form-control" id="pa_kpa">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                @foreach ($pa_kpa as $ttd)
                                    <option value="{{ $ttd->nip }}" data-nama="{{ $ttd->nama }}">
                                        {{ $ttd->nip }} | {{ $ttd->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="nama_pa_kpa" id="nama_pa_kpa" class="form-control" readonly>
                        </div>
                    </div>
                    {{-- PPKD --}}
                    <div class="mb-3 row">
                        <label for="ppkd" class="col-md-2 col-form-label">PPKD</label>
                        <div class="col-md-6">
                            <select name="ppkd" class="form-control" id="ppkd">
                                <option value="" selected disabled>Silahkan Pilih</option>
                                @foreach ($ppkd as $ttd)
                                    <option value="{{ $ttd->nip }}" data-nama="{{ $ttd->nama }}">
                                        {{ $ttd->nip }} | {{ $ttd->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="nama_ppkd" id="nama_ppkd" class="form-control" readonly>
                        </div>
                    </div>
                    {{-- Spasi --}}
                    <div class="mb-3 row">
                        <label for="spasi" class="col-md-2 col-form-label">Spasi</label>
                        <div class="col-md-6">
                            <input type="number" value="1" min="1" class="form-control" id="spasi"
                                name="spasi">
                        </div>
                    </div>
                    {{-- Pengantar, Ringkasan dan Format Permandagri 77 --}}
                    <div class="mb-3 row">
                        {{--  <label for="pengantar" class="col-md-2 col-form-label">Pengantar</label>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-md pengantar_layar" data-jenis="pdf"
                                name="pengantar_pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md pengantar_layar" data-jenis="layar"
                                name="pengantar_layar">Layar</button>
                        </div>
                        <label for="ringkasan" class="col-md-2 col-form-label">Ringkasan</label>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-md ringkasan_layar" data-jenis="pdf"
                                name="ringkasan_pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md ringkasan_layar" data-jenis="layar"
                                name="ringkasan_layar">Layar</button>
                        </div>
                        <label for="ringkasan" style="text-align: center" class="col-md-4 col-form-label">Format
                            Permendagri 77</label>  --}}
                    </div>
                    {{-- Rincian, Pernyataan dan SPP --}}
                    <div class="mb-3 row">
                        <label for="spp" class="col-md-2 col-form-label">SPP</label>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-md spp_layar" data-jenis="pdf"
                                name="spp_pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md spp_layar" data-jenis="layar"
                                name="spp_layar">Layar</button>
                        </div>
                        <label for="rincian77" class="col-md-2 col-form-label">Rincian</label>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-md rincian77_layar" data-jenis="pdf"
                                name="rincian77_pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md rincian77_layar" data-jenis="layar"
                                name="rincian77_layar">Layar</button>
                        </div>
                        {{--  <label for="rincian" class="col-md-2 col-form-label">Rincian</label>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-md rincian_layar" data-jenis="pdf"
                                name="rincian_pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md rincian_layar" data-jenis="layar"
                                name="rincian_layar">Layar</button>
                        </div>  --}}
                        <label for="pernyataan" class="col-md-2 col-form-label">Pernyataan</label>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-md pernyataan_layar" data-jenis="pdf"
                                name="pernyataan_pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md pernyataan_layar" data-jenis="layar"
                                name="pernyataan_layar">Layar</button>
                        </div>

                    </div>
                    {{-- Permintaan, SPTB dan Rincian --}}
                    <div class="mb-3 row">
                        {{--  <label for="permintaan" class="col-md-2 col-form-label">Permintaan</label>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-md permintaan_layar" data-jenis="pdf"
                                name="permintaan_pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md permintaan_layar" data-jenis="layar"
                                name="permintaan_layar">Layar</button>
                        </div> --}}
                        <label for="sptb" class="col-md-2 col-form-label">SPTB</label>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-md sptb_layar" data-jenis="pdf"
                                name="sptb_pdf">PDF</button>
                            <button type="button" class="btn btn-dark btn-md sptb_layar" data-jenis="layar"
                                name="sptb_layar">Layar</button>
                        </div>

                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-md btn-secondary"
                                data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- modal batal spp --}}
    <div id="batal_spp" class="modal" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">KETERANGAN PEMBATALAN SPP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- No SPP --}}
                    <div class="mb-3 row">
                        <label for="no_spp_batal" class="col-md-2 col-form-label">No SPP</label>
                        <div class="col-md-12">
                            <input type="text" readonly class="form-control" id="no_spp_batal" name="no_spp_batal">
                            <input type="text" readonly class="form-control" id="beban_batal" name="beban_batal"
                                hidden>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="keterangan_batal" class="col-md-2 col-form-label">Keterangan</label>
                        <div class="col-md-12">
                            <textarea type="text" class="form-control" id="keterangan_batal" name="keterangan_batal"></textarea>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-md btn-danger" id="batal_sppls"><i
                                    class="uil-ban"></i>Batal SPP</button>
                            <button type="button" class="btn btn-md btn-warning" data-bs-dismiss="modal"><i
                                    class="fa fa-undo"></i>Keluar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            let status_ang = document.getElementById('status_ang').value;
            let selisih_angkas = document.getElementById('selisih_angkas').value;

            if (status_ang == 0 || status_ang == '0') {
                alert('DPA Belum Disahkan!');
            }
            if (selisih_angkas > 0) {
                alert('Masih ada ' + selisih_angkas +
                    ' Selisih antara Anggaran dan Anggaran Kas, Anda tidak bisa melanjutkan transaksi');
            }
            $('#spp_ls').DataTable({
                responsive: true,
                ordering: false,
                serverSide: true,
                processing: true,
                lengthMenu: [10, 20, 50],
                ajax: {
                    "url": "{{ route('sppls.load_data') }}",
                    "type": "POST",
                },
                createdRow: function(row, data, index) {
                    if (data.sp2d_batal == "1" && data.status == 1) {
                        $(row).css("background-color", "#ff0000");
                        $(row).css("color", "white");
                    }
                    if (data.sp2d_batal == "1" && data.status == 0) {
                        $(row).css("background-color", "#ff0000");
                        $(row).css("color", "white");
                    }
                    if (data.status == 1 && data.sp2d_batal != '1') {
                        $(row).css("background-color", "#4bbe68");
                        $(row).css("color", "white");
                    }

                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    }, {
                        data: 'no_spp',
                        name: 'no_spp'
                    },
                    {
                        data: 'tgl_spp',
                        name: 'tgl_spp'
                    },
                    {
                        data: null,
                        name: 'keperluan',
                        render: function(data, type, row, meta) {
                            return data.keperluan.substr(0, 10) + '.....';
                        }
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        width: '200px',
                        className: 'text-center'
                    },
                ],
            });

            $('#bendahara').select2({
                dropdownParent: $('#modal_cetak .modal-content'),
                theme: 'bootstrap-5'
            });

            $('#pptk').select2({
                dropdownParent: $('#modal_cetak .modal-content'),
                theme: 'bootstrap-5'
            });

            $('#pa_kpa').select2({
                dropdownParent: $('#modal_cetak .modal-content'),
                theme: 'bootstrap-5'
            });

            $('#ppkd').select2({
                dropdownParent: $('#modal_cetak .modal-content'),
                theme: 'bootstrap-5'
            });

            $('#bendahara').on('select2:select', function() {
                let nama = $(this).find(':selected').data('nama');
                $('#nama_bendahara').val(nama);
            });

            $('#pptk').on('select2:select', function() {
                let nama = $(this).find(':selected').data('nama');
                $('#nama_pptk').val(nama);
            });

            $('#pa_kpa').on('select2:select', function() {
                let nama = $(this).find(':selected').data('nama');
                $('#nama_pa_kpa').val(nama);
            });

            $('#ppkd').on('select2:select', function() {
                let nama = $(this).find(':selected').data('nama');
                $('#nama_ppkd').val(nama);
            });

            // cetak pengantar layar
            $('.pengantar_layar').on('click', function() {
                let spasi = document.getElementById('spasi').value;
                let no_spp = document.getElementById('no_spp').value;
                let beban = document.getElementById('beban').value;
                let bendahara = document.getElementById('bendahara').value;
                let pptk = document.getElementById('pptk').value;
                let pa_kpa = document.getElementById('pa_kpa').value;
                let ppkd = document.getElementById('ppkd').value;
                let kd_skpd = document.getElementById('kd_skpd').value;
                let tanpa_tanggal = document.getElementById('tanpa_tanggal').checked;
                let jenis_print = $(this).data("jenis");
                let tanpa;
                if (tanpa_tanggal == false) {
                    tanpa = 0;
                } else {
                    tanpa = 1;
                }
                if (!bendahara) {
                    alert('Bendahara Penghasilan tidak boleh kosong!');
                    return;
                }
                if (!pptk) {
                    alert("PPTK tidak boleh kosong!");
                    return;
                }
                if (!ppkd) {
                    alert("PPKD tidak boleh kosong!");
                    return;
                }
                let url = new URL("{{ route('sppls.cetak_pengantar_layar') }}");
                let searchParams = url.searchParams;
                searchParams.append("no_spp", no_spp);
                searchParams.append("beban", beban);
                searchParams.append("spasi", spasi);
                searchParams.append("bendahara", bendahara);
                searchParams.append("pptk", pptk);
                searchParams.append("pa_kpa", pa_kpa);
                searchParams.append("ppkd", ppkd);
                searchParams.append("kd_skpd", kd_skpd);
                searchParams.append("tanpa", tanpa);
                searchParams.append("jenis_print", jenis_print);
                window.open(url.toString(), "_blank");
            });

            // cetak rincian layar
            $('.rincian_layar').on('click', function() {
                let spasi = document.getElementById('spasi').value;
                let no_spp = document.getElementById('no_spp').value;
                let beban = document.getElementById('beban').value;
                let bendahara = document.getElementById('bendahara').value;
                let pptk = document.getElementById('pptk').value;
                let pa_kpa = document.getElementById('pa_kpa').value;
                let ppkd = document.getElementById('ppkd').value;
                let kd_skpd = document.getElementById('kd_skpd').value;
                let tanpa_tanggal = document.getElementById('tanpa_tanggal').checked;
                let jenis_print = $(this).data("jenis");
                let tanpa;
                if (tanpa_tanggal == false) {
                    tanpa = 0;
                } else {
                    tanpa = 1;
                }
                if (!bendahara) {
                    alert('Bendahara Penghasilan tidak boleh kosong!');
                    return;
                }
                if (!pptk) {
                    alert("PPTK tidak boleh kosong!");
                    return;
                }
                if (!ppkd) {
                    alert("PPKD tidak boleh kosong!");
                    return;
                }
                let url = new URL("{{ route('sppls.cetak_rincian_layar') }}");
                let searchParams = url.searchParams;
                searchParams.append("no_spp", no_spp);
                searchParams.append("beban", beban);
                searchParams.append("spasi", spasi);
                searchParams.append("bendahara", bendahara);
                searchParams.append("pptk", pptk);
                searchParams.append("pa_kpa", pa_kpa);
                searchParams.append("ppkd", ppkd);
                searchParams.append("kd_skpd", kd_skpd);
                searchParams.append("tanpa", tanpa);
                searchParams.append("jenis_print", jenis_print);
                window.open(url.toString(), "_blank");
            });

            // cetak permintaan layar
            $('.permintaan_layar').on('click', function() {
                let spasi = document.getElementById('spasi').value;
                let no_spp = document.getElementById('no_spp').value;
                let beban = document.getElementById('beban').value;
                let bendahara = document.getElementById('bendahara').value;
                let pptk = document.getElementById('pptk').value;
                let pa_kpa = document.getElementById('pa_kpa').value;
                let ppkd = document.getElementById('ppkd').value;
                let kd_skpd = document.getElementById('kd_skpd').value;
                let tanpa_tanggal = document.getElementById('tanpa_tanggal').checked;
                let jenis_print = $(this).data("jenis");
                let tanpa;
                if (tanpa_tanggal == false) {
                    tanpa = 0;
                } else {
                    tanpa = 1;
                }
                if (!bendahara) {
                    alert('Bendahara Penghasilan tidak boleh kosong!');
                    return;
                }
                if (!pptk) {
                    alert("PPTK tidak boleh kosong!");
                    return;
                }
                if (!ppkd) {
                    alert("PPKD tidak boleh kosong!");
                    return;
                }
                let url = new URL("{{ route('sppls.cetak_permintaan_layar') }}");
                let searchParams = url.searchParams;
                searchParams.append("no_spp", no_spp);
                searchParams.append("beban", beban);
                searchParams.append("spasi", spasi);
                searchParams.append("bendahara", bendahara);
                searchParams.append("pptk", pptk);
                searchParams.append("pa_kpa", pa_kpa);
                searchParams.append("ppkd", ppkd);
                searchParams.append("kd_skpd", kd_skpd);
                searchParams.append("tanpa", tanpa);
                searchParams.append("jenis_print", jenis_print);
                window.open(url.toString(), "_blank");
            });

            $('.ringkasan_layar').on('click', function() {
                let spasi = document.getElementById('spasi').value;
                let no_spp = document.getElementById('no_spp').value;
                let beban = document.getElementById('beban').value;
                let bendahara = document.getElementById('bendahara').value;
                let pptk = document.getElementById('pptk').value;
                let pa_kpa = document.getElementById('pa_kpa').value;
                let ppkd = document.getElementById('ppkd').value;
                let kd_skpd = document.getElementById('kd_skpd').value;
                let tanpa_tanggal = document.getElementById('tanpa_tanggal').checked;
                let jenis_print = $(this).data("jenis");
                let tanpa;
                if (tanpa_tanggal == false) {
                    tanpa = 0;
                } else {
                    tanpa = 1;
                }
                if (!bendahara) {
                    alert('Bendahara Penghasilan tidak boleh kosong!');
                    return;
                }
                if (!pptk) {
                    alert("PPTK tidak boleh kosong!");
                    return;
                }
                if (!ppkd) {
                    alert("PPKD tidak boleh kosong!");
                    return;
                }
                let url = new URL("{{ route('sppls.cetak_ringkasan_layar') }}");
                let searchParams = url.searchParams;
                searchParams.append("no_spp", no_spp);
                searchParams.append("beban", beban);
                searchParams.append("spasi", spasi);
                searchParams.append("bendahara", bendahara);
                searchParams.append("pptk", pptk);
                searchParams.append("pa_kpa", pa_kpa);
                searchParams.append("ppkd", ppkd);
                searchParams.append("kd_skpd", kd_skpd);
                searchParams.append("tanpa", tanpa);
                searchParams.append("jenis_print", jenis_print);
                window.open(url.toString(), "_blank");
            });

            $('.pernyataan_layar').on('click', function() {
                let spasi = document.getElementById('spasi').value;
                let no_spp = document.getElementById('no_spp').value;
                let beban = document.getElementById('beban').value;
                let bendahara = document.getElementById('bendahara').value;
                let pptk = document.getElementById('pptk').value;
                let pa_kpa = document.getElementById('pa_kpa').value;
                let ppkd = document.getElementById('ppkd').value;
                let kd_skpd = document.getElementById('kd_skpd').value;
                let tanpa_tanggal = document.getElementById('tanpa_tanggal').checked;
                let jenis_print = $(this).data("jenis");
                let tanpa;
                if (tanpa_tanggal == false) {
                    tanpa = 0;
                } else {
                    tanpa = 1;
                }
                if (!bendahara) {
                    alert('Bendahara Penghasilan tidak boleh kosong!');
                    return;
                }
                if (!pptk) {
                    alert("PPTK tidak boleh kosong!");
                    return;
                }
                if (!ppkd) {
                    alert("PPKD tidak boleh kosong!");
                    return;
                }
                let url = new URL("{{ route('sppls.cetak_pernyataan_layar') }}");
                let searchParams = url.searchParams;
                searchParams.append("no_spp", no_spp);
                searchParams.append("beban", beban);
                searchParams.append("spasi", spasi);
                searchParams.append("bendahara", bendahara);
                searchParams.append("pptk", pptk);
                searchParams.append("pa_kpa", pa_kpa);
                searchParams.append("ppkd", ppkd);
                searchParams.append("kd_skpd", kd_skpd);
                searchParams.append("tanpa", tanpa);
                searchParams.append("jenis_print", jenis_print);
                window.open(url.toString(), "_blank");
            });

            $('.sptb_layar').on('click', function() {
                let spasi = document.getElementById('spasi').value;
                let no_spp = document.getElementById('no_spp').value;
                let beban = document.getElementById('beban').value;
                let bendahara = document.getElementById('bendahara').value;
                let pptk = document.getElementById('pptk').value;
                let pa_kpa = document.getElementById('pa_kpa').value;
                let ppkd = document.getElementById('ppkd').value;
                let kd_skpd = document.getElementById('kd_skpd').value;
                let tanpa_tanggal = document.getElementById('tanpa_tanggal').checked;
                let jenis_print = $(this).data("jenis");
                let tanpa;
                if (tanpa_tanggal == false) {
                    tanpa = 0;
                } else {
                    tanpa = 1;
                }
                if (!bendahara) {
                    alert('Bendahara Penghasilan tidak boleh kosong!');
                    return;
                }
                if (!pptk) {
                    alert("PPTK tidak boleh kosong!");
                    return;
                }
                if (!ppkd) {
                    alert("PPKD tidak boleh kosong!");
                    return;
                }
                let url = new URL("{{ route('sppls.cetak_sptb_layar') }}");
                let searchParams = url.searchParams;
                searchParams.append("no_spp", no_spp);
                searchParams.append("beban", beban);
                searchParams.append("spasi", spasi);
                searchParams.append("bendahara", bendahara);
                searchParams.append("pptk", pptk);
                searchParams.append("pa_kpa", pa_kpa);
                searchParams.append("ppkd", ppkd);
                searchParams.append("kd_skpd", kd_skpd);
                searchParams.append("tanpa", tanpa);
                searchParams.append("jenis_print", jenis_print);
                window.open(url.toString(), "_blank");
            });

            $('.spp_layar').on('click', function() {
                let spasi = document.getElementById('spasi').value;
                let no_spp = document.getElementById('no_spp').value;
                let beban = document.getElementById('beban').value;
                let bendahara = document.getElementById('bendahara').value;
                let pptk = document.getElementById('pptk').value;
                let pa_kpa = document.getElementById('pa_kpa').value;
                let ppkd = document.getElementById('ppkd').value;
                let kd_skpd = document.getElementById('kd_skpd').value;
                let tanpa_tanggal = document.getElementById('tanpa_tanggal').checked;
                let jenis_print = $(this).data("jenis");
                let tanpa;
                if (tanpa_tanggal == false) {
                    tanpa = 0;
                } else {
                    tanpa = 1;
                }
                if (!bendahara) {
                    alert('Bendahara Penghasilan tidak boleh kosong!');
                    return;
                }
                if (!pptk) {
                    alert("PPTK tidak boleh kosong!");
                    return;
                }
                if (!ppkd) {
                    alert("PPKD tidak boleh kosong!");
                    return;
                }
                let url = new URL("{{ route('sppls.cetak_spp77_layar') }}");
                let searchParams = url.searchParams;
                searchParams.append("no_spp", no_spp);
                searchParams.append("beban", beban);
                searchParams.append("spasi", spasi);
                searchParams.append("bendahara", bendahara);
                searchParams.append("pptk", pptk);
                searchParams.append("pa_kpa", pa_kpa);
                searchParams.append("ppkd", ppkd);
                searchParams.append("kd_skpd", kd_skpd);
                searchParams.append("tanpa", tanpa);
                searchParams.append("jenis_print", jenis_print);
                window.open(url.toString(), "_blank");
            });

            $('.rincian77_layar').on('click', function() {
                let spasi = document.getElementById('spasi').value;
                let no_spp = document.getElementById('no_spp').value;
                let beban = document.getElementById('beban').value;
                let bendahara = document.getElementById('bendahara').value;
                let pptk = document.getElementById('pptk').value;
                let pa_kpa = document.getElementById('pa_kpa').value;
                let ppkd = document.getElementById('ppkd').value;
                let kd_skpd = document.getElementById('kd_skpd').value;
                let tanpa_tanggal = document.getElementById('tanpa_tanggal').checked;
                let jenis_print = $(this).data("jenis");
                let tanpa;
                if (tanpa_tanggal == false) {
                    tanpa = 0;
                } else {
                    tanpa = 1;
                }
                if (!bendahara) {
                    alert('Bendahara Penghasilan tidak boleh kosong!');
                    return;
                }
                if (!pptk) {
                    alert("PPTK tidak boleh kosong!");
                    return;
                }
                if (!ppkd) {
                    alert("PPKD tidak boleh kosong!");
                    return;
                }
                let url = new URL("{{ route('sppls.cetak_rincian77_layar') }}");
                let searchParams = url.searchParams;
                searchParams.append("no_spp", no_spp);
                searchParams.append("beban", beban);
                searchParams.append("spasi", spasi);
                searchParams.append("bendahara", bendahara);
                searchParams.append("pptk", pptk);
                searchParams.append("pa_kpa", pa_kpa);
                searchParams.append("ppkd", ppkd);
                searchParams.append("kd_skpd", kd_skpd);
                searchParams.append("tanpa", tanpa);
                searchParams.append("jenis_print", jenis_print);
                window.open(url.toString(), "_blank");
            });

            $('#batal_sppls').on('click', function() {
                let no_spp = document.getElementById('no_spp_batal').value;
                let keterangan = document.getElementById('keterangan_batal').value;
                let beban = document.getElementById('beban_batal').value;
                let tanya = confirm('Anda yakin akan Membatalkan SPP: ' + no_spp + '  ?');
                if (tanya == true) {
                    if (!keterangan) {
                        alert('Keterangan harus diisi!');
                        return;
                    }
                    $.ajax({
                        url: "{{ route('sppls.batal_sppls') }}",
                        type: "POST",
                        dataType: 'json',
                        data: {
                            no_spp: no_spp,
                            keterangan: keterangan,
                            beban: beban
                        },
                        success: function(data) {
                            if (data.message == '1') {
                                alert('SPP Berhasil Dibatalkan');
                                window.location.href = "{{ route('sppls.index') }}";
                            } else {
                                alert('SPP Berhasil Dibatalkan');
                                return;
                            }
                        }
                    })
                }
            });
        });

        function cetak(no_spp, beban, kd_skpd) {
            $('#no_spp').val(no_spp);
            $('#beban').val(beban);
            $('#kd_skpd').val(kd_skpd);
            $('#modal_cetak').modal('show');
        }

        function batal_spp(no_spp, beban, kd_skpd) {
            $('#no_spp_batal').val(no_spp);
            $('#beban_batal').val(beban);
            $('#batal_spp').modal('show');
        }

        function deleteData(no_spp) {
            let tanya = confirm('Apakah anda yakin untuk menghapus dengan Nomor SPP : ' + no_spp)
            if (tanya == true) {
                $.ajax({
                    url: "{{ route('sppls.hapus_sppls') }}",
                    type: "DELETE",
                    dataType: 'json',
                    data: {
                        no_spp: no_spp
                    },
                    success: function(data) {
                        if (data.message == '1') {
                            alert('Data berhasil dihapus!');
                            location.reload();
                        } else {
                            alert('Data gagal dihapus!');
                            location.reload();
                        }
                    }
                })
            } else {
                return false;
            }
        }
    </script>
@endsection
