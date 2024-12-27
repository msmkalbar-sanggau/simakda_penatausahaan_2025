@extends('template.app')
@section('title', 'Penerimaan Tahun Ini | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    List Data Penerimaan
                    <button id="tambahpenerimaan" class="btn btn-outline-info tomboltambah" style="float: right;"><i
                            class="bx bx-plus-circle"></i> Tambah Data</button>
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        {{-- <div class="table-responsive mb-0" data-pattern="priority-columns"> --}}
                        <table id="penerimaan_tahun_ini" class="table table-striped table-bordered" style="width: 100%">
                            <thead>
                                <tr>
                                    <th style="text-align:center">No.</th>
                                    <th style="text-align:center">Nomor Terima</th>
                                    <th style="text-align:center">Nomor Tetap</th>
                                    <th style="text-align:center">Tanggal Tetap</th>
                                    <th style="text-align:center">Tanggal</th>
                                    <th style="text-align:center">SKPD</th>
                                    <th style="text-align:center">Rekening</th>
                                    <th style="text-align:center">Nilai</th>
                                    <th style="text-align:center">SPJ</th>
                                    <th style="text-align:center">Status Tetap</th>
                                    <th style="text-align:center">Kode Sub Kegiatan</th>
                                    <th style="text-align:center">Jenis Setor</th>
                                    <th style="text-align:center">Jenis Bayar</th>
                                    <th style="text-align:center">Jenis Pajak</th>
                                    <th style="text-align:center">Kode LO</th>
                                    <th style="text-align:center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        {{-- </div> --}}
                    </div>
                </div>
            </div>
            <!-- Tambah -->
            <div id="tambah-dialog" class="modal fade" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-secondary">
                            <h5 class="modal-title text-white">Penerimaan</h5>
                        </div>
                        <div class="modal-body">
                            <form id="tambah-form" method="POST">
                                <div class="form-group mb-2">
                                    <div class="form-check form-switch form-switch-m">
                                        <input type="checkbox" class="form-check-input" id="pilihan">
                                        <label class="form-check-label" for="pilihan">
                                            Dengan Penetapan</label>
                                    </div>
                                </div>
                                <div class="form-group mb-2" id="dengan_penetapan1">
                                    <div class="mb-2 row">
                                        <label for="no_tetap_tambah" class="col-md-2 col-form-label-sm">No.
                                            Penetapan</label>
                                        <div class="col-md-4">
                                            <select class="form-control form-control-sm" style="width: 100%"
                                                id="no_tetap_tambah" name="no_tetap_tambah">
                                                <option value="">-- Pilih -- </option>
                                                @foreach ($daftar_penetapan as $tetap)
                                                    <option value="{{ $tetap->no_tetap }}"
                                                        data-tgl="{{ $tetap->tgl_tetap }}" data-nilai="{{ $tetap->nilai }}"
                                                        data-kd_rek6="{{ $tetap->kd_rek6 }}"
                                                        data-kd_rek_lo="{{ $tetap->kd_rek_lo }}"
                                                        data-ket="{{ $tetap->keterangan }}"
                                                        data-kd_sub_kegiatan="{{ $tetap->kd_sub_kegiatan }}">
                                                        {{ $tetap->no_tetap }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <label for="tgl_tetap_tambah" class="col-md-2 col-form-label-sm">Tanggal
                                            Penetapan</label>
                                        <div class="col-md-2">
                                            <input class="form-control form-control-sm" type="date" id="tgl_tetap_tambah"
                                                name="tgl_tetap_tambah">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-2" id="dengan_penetapan2">
                                    <div class="mb-2 row">
                                        <label for="nilai_tetap_tambah" class="col-md-2 col-form-label-sm">Nilai
                                            Penetapan</label>
                                        <div class="col-md-4">
                                            <input id="nilai_tetap_tambah" type="text"
                                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency"
                                                class="form-control form-control-sm" name="nilai_tetap_tambah"
                                                placeholder="Nilai" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-2">
                                    <div class="mb-2 row">
                                        <label for="no_terima_tambah" class="col-md-2 col-form-label-sm">No Terima</label>
                                        <div class="col-md-4">
                                            <input id="no_terima_tambah" type="text" class="form-control form-control-sm"
                                                name="no_terima_tambah" placeholder="No Terima">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-2">
                                    <div class="mb-2 row">
                                        <label for="jns_tambah" class="col-md-2 col-form-label-sm">Jenis Setor</label>
                                        <div class="col-md-2">
                                            <div class="form-check form-switch form-switch-m">
                                                <input type="checkbox" class="form-check-input" name="status_setor"
                                                    id="tanpa_setor" value="Tanpa Setor" onclick="opt(this.value)">
                                                <label class="form-check-label col-form-label-sm" for="tanpa_setor">
                                                    Tanpa Setor
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-check form-switch form-switch-m">
                                                <input type="checkbox" class="form-check-input" name="status_setor"
                                                    id="dengan_setor" value="Dengan Setor" onclick="opt(this.value)">
                                                <label class="form-check-label col-form-label-sm" for="dengan_setor">
                                                    Dengan Setor
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-2" id="jns_bayar">
                                    <div class="mb-2 row">
                                        <label for="jenis_pembayaran_tambah" class="col-md-2 col-form-label-sm">Jenis
                                            Pembayaran</label>
                                        <div class="col-md-4">
                                            <select id="jenis_pembayaran_tambah" name="jenis_pembayaran_tambah"
                                                class="form-control form-control-sm">
                                                <option value="TUNAI"> Tunai</option>
                                                <option value="BANK"> Bank</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-2">
                                    <div class="mb-2 row">
                                        <label for="tanggal_tambah" class="col-md-2 col-form-label-sm">Tanggal</label>
                                        <div class="col-md-4">
                                            <input id="tanggal_tambah" type="date"
                                                class="form-control form-control-sm" name="tanggal_tambah" placeholder=""
                                                required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-2">
                                    <div class="mb-2 row">
                                        <label for="rekening_tambah" class="col-md-2 col-form-label-sm">Rekening</label>
                                        <div class="col-md-4">
                                            <select id="rekening_tambah" name="rekening_tambah"
                                                class="form-control form-control-sm">
                                                <option value="">-- Pilih -- </option>
                                                @foreach ($daftar_akun as $akun)
                                                    <option value="{{ $akun->kd_rek6 }}"
                                                        data-kd_sub_kegiatan="{{ $akun->kd_sub_kegiatan }}"
                                                        data-nm_rek="{{ $akun->nm_rek }}"
                                                        data-kd_rek_lo="{{ $akun->kd_rek_lo }}">
                                                        {{ $akun->kd_rek6 }} | {{ $akun->nm_rek }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input id="kd_rek_lo_tambah" type="text"
                                                class="form-control form-control-sm" name="kd_rek_lo_tambah"
                                                placeholder="" required hidden>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-2" id="jns_pajak_tambah">
                                    <div class="mb-2 row">
                                        <label for="jenispajak_tambah" class="col-md-2 col-form-label-sm">Jenis
                                            Pajak</label>
                                        <div class="col-md-4">
                                            <select id="jenispajak_tambah" name="jenispajak_tambah"
                                                class="form-control form-control-sm">
                                                <option value="">-- Pilih -- </option>
                                                @foreach ($ms_pajak as $pajak)
                                                    <option value="{{ $pajak->kode_pajak }}">
                                                        {{ $pajak->kode_pajak }} | {{ $pajak->nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-2">
                                    <div class="mb-2 row">
                                        <label for="nilai_terima_tambah" class="col-md-2 col-form-label-sm">Nilai
                                            Penerimaan</label>
                                        <div class="col-md-4">
                                            <input type="text" id="nilai_terima_tambah" name="nilai_terima_tambah"
                                                class="form-control form-control-sm"
                                                pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency"
                                                placeholder="Nilai" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-2">
                                    <div class="mb-2 row">
                                        <label for="ket_tambah" class="col-md-2 col-form-label-sm">Keterangan</label>
                                        <div class="col-md-4">
                                            <textarea class="form-control" id="ket_tambah" name="ket_tambah"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-2">
                                    <div class="mb-2 row">
                                        <label for="kdkegiatan_tambah" class="col-md-2 col-form-label-sm">Sub
                                            Kegiatan</label>
                                        <div class="col-md-4">
                                            <input id="kdkegiatan_tambah" type="text"
                                                class="form-control form-control-sm" name="kdkegiatan_tambah"
                                                placeholder="Kode Kegiatan" required disabled>
                                        </div>
                                        {{-- <div class="col-md-4">
                                            <input id="nmkegiatan_tambah" type="text"
                                                class="form-control form-control-sm" name="nmkegiatan_tambah"
                                                placeholder="Nama Sub Kegiatan" required disabled>
                                        </div> --}}
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class=" modal-footer">
                            <button id="simpan-btn" type="submit" onclick="simpanpenerimaan();"
                                class="btn btn-sm btn-primary">Simpan</button>
                            <button id="close-btn" type="button" class="btn btn-sm btn-secondary"
                                data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End tambah -->

            <!-- edit -->
            <div id="edit-dialog" class="modal fade" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-secondary">
                            <h5 class="modal-title text-white">Penerimaan</h5>
                        </div>
                        <div class="modal-body">
                            <form id="edit-form" action="" method="POST">
                                @csrf
                                <div class="form-group mb-2">
                                    <div class="form-check form-switch form-switch-m">
                                        <input type="checkbox" class="form-check-input" id="pilihan_edit">
                                        <label class="form-check-label" for="pilihan_edit">
                                            Dengan Penetapan</label>
                                    </div>
                                </div>
                                <div class="form-group mb-2" id="dengan_penetapan1_edit">
                                    <div class="mb-2 row">
                                        <label for="no_tetap_edit" class="col-md-2 col-form-label-sm">No.
                                            Penetapan</label>
                                        <div class="col-md-4">
                                            <select class="form-control form-control-sm" style="width: 100%"
                                                id="no_tetap_edit" name="no_tetap_edit">
                                                <option value="">-- Pilih -- </option>
                                                @foreach ($no_penetapan as $tetap)
                                                    <option value="{{ $tetap->no_tetap }}"
                                                        data-tgl="{{ $tetap->tgl_tetap }}"
                                                        data-nilai="{{ $tetap->nilai }}"
                                                        data-kd_rek6="{{ $tetap->kd_rek6 }}"
                                                        data-kd_rek_lo="{{ $tetap->kd_rek_lo }}"
                                                        data-ket="{{ $tetap->keterangan }}"
                                                        data-kd_sub_kegiatan="{{ $tetap->kd_sub_kegiatan }}">
                                                        {{ $tetap->no_tetap }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <label for="tgl_tetap_edit" class="col-md-2 col-form-label-sm">Tanggal
                                            Penetapan</label>
                                        <div class="col-md-2">
                                            <input class="form-control form-control-sm" type="date"
                                                id="tgl_tetap_edit" name="tgl_tetap_edit">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-2" id="dengan_penetapan2_edit">
                                    <div class="mb-2 row">
                                        <label for="nilai_tetap_edit" class="col-md-2 col-form-label-sm">Nilai
                                            Penetapan</label>
                                        <div class="col-md-4">
                                            <input id="nilai_tetap_edit" type="text"
                                                class="form-control form-control-sm" name="nilai_tetap_edit"
                                                placeholder="Nilai" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-2">
                                    <div class="mb-2 row">
                                        <label for="no_terima_edit" class="col-md-2 col-form-label-sm">No Terima</label>
                                        <div class="col-md-4">
                                            <input id="no_terima_edit" type="text"
                                                class="form-control form-control-sm" name="no_terima_edit"
                                                placeholder="No Terima">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-2">
                                    <div class="mb-2 row">
                                        <label for="jns_edit" class="col-md-2 col-form-label-sm">Jenis Setor</label>
                                        <div class="col-md-2">
                                            <div class="form-check form-switch form-switch-m">
                                                <input type="checkbox" class="form-check-input" name="status_setor_edit"
                                                    id="tanpa_setor_edit" value="Tanpa Setor"
                                                    onclick="opt_edit(this.value)">
                                                <label class="form-check-label col-form-label-sm" for="tanpa_setor_edit">
                                                    Tanpa Setor
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-check form-switch form-switch-m">
                                                <input type="checkbox" class="form-check-input" name="status_setor_edit"
                                                    id="dengan_setor_edit" value="Dengan Setor"
                                                    onclick="opt_edit(this.value)">
                                                <label class="form-check-label col-form-label-sm" for="dengan_setor_edit">
                                                    Dengan Setor
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-2" id="jns_bayar_edit">
                                    <div class="mb-2 row">
                                        <label for="jenis_pembayaran_edit" class="col-md-2 col-form-label-sm">Jenis
                                            Pembayaran</label>
                                        <div class="col-md-4">
                                            <select id="jenis_pembayaran_edit" name="jenis_pembayaran_edit"
                                                class="form-control form-control-sm">
                                                <option value="TUNAI"> Tunai</option>
                                                <option value="BANK"> Bank</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-2">
                                    <div class="mb-2 row">
                                        <label for="tanggal_edit" class="col-md-2 col-form-label-sm">Tanggal</label>
                                        <div class="col-md-4">
                                            <input id="tanggal_edit" type="date" class="form-control form-control-sm"
                                                name="tanggal_edit" placeholder="" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-2">
                                    <div class="mb-2 row">
                                        <label for="rekening_edit" class="col-md-2 col-form-label-sm">Rekening</label>
                                        <div class="col-md-4">
                                            <select id="rekening_edit" name="rekening_edit"
                                                class="form-control form-control-sm">
                                                <option value="">-- Pilih -- </option>
                                                @foreach ($daftar_akun as $akun)
                                                    <option value="{{ $akun->kd_rek6 }}"
                                                        data-kd_sub_kegiatan="{{ $akun->kd_sub_kegiatan }}"
                                                        data-nm_rek="{{ $akun->nm_rek }}"
                                                        data-kd_rek_lo="{{ $akun->kd_rek_lo }}">
                                                        {{ $akun->kd_rek6 }} | {{ $akun->nm_rek }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input id="kd_rek_lo_edit" type="text"
                                                class="form-control form-control-sm" name="kd_rek_lo_edit" placeholder=""
                                                hidden>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-2" id="jns_pajak_edit">
                                    <div class="mb-2 row">
                                        <label for="jenispajak_edit" class="col-md-2 col-form-label-sm">Jenis
                                            Pajak</label>
                                        <div class="col-md-4">
                                            <select id="jenispajak_edit" name="jenispajak_edit"
                                                class="form-control form-control-sm">
                                                <option value="">-- Pilih -- </option>
                                                @foreach ($ms_pajak as $pajak)
                                                    <option value="{{ $pajak->kode_pajak }}">
                                                        {{ $pajak->kode_pajak }} | {{ $pajak->nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-2">
                                    <div class="mb-2 row">
                                        <label for="nilai_terima_edit" class="col-md-2 col-form-label-sm">Nilai
                                            Penerimaan</label>
                                        <div class="col-md-4">
                                            <input id="nilai_terima_edit" type="text"
                                                class="form-control form-control-sm" name="nilai_terima_edit"
                                                placeholder="Nilai">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-2">
                                    <div class="mb-2 row">
                                        <label for="ket_edit" class="col-md-2 col-form-label-sm">Keterangan</label>
                                        <div class="col-md-4">
                                            <textarea class="form-control" id="ket_edit" name="ket_edit"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-2">
                                    <div class="mb-2 row">
                                        <label for="kdkegiatan_edit" class="col-md-2 col-form-label-sm">Sub
                                            Kegiatan</label>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control form-control-sm"
                                                id="kdkegiatan_edit" name="kdkegiatan_edit">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class=" modal-footer">
                            <button id="edit-btn" class="btn btn-sm btn-primary">Update</button>
                            <button id="keluar-edit" type="button" class="btn btn-sm btn-secondary"
                                data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End edit -->
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript">
        var table = '';
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });

        $(document).ready(function() {
            table = $('#penerimaan_tahun_ini').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                lengthMenu: [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, 'All'],
                ],
                ajax: {
                    "url": "{{ route('listpenerimaantahunini') }}",
                    "type": "POST",
                },
                columns: [{
                        orderable: false,
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: "text-center",
                    },
                    {
                        data: 'no_terima',
                        name: 'no_terima',
                    },
                    {
                        data: 'no_tetap',
                        name: 'No Tetap',
                    },
                    {
                        data: 'tgl_tetap',
                        name: 'Tanggal Tetap',
                    },
                    {
                        data: 'tgl_terima',
                        name: 'tgl_terima',
                        className: "text-center",
                    },
                    {
                        visible: false,
                        data: 'kd_skpd',
                        name: 'kd_skpd',
                        className: "text-center",
                    },
                    {
                        data: 'kd_rek6',
                        name: 'kd_rek6',
                        className: "text-center",
                    },
                    {
                        data: null,
                        name: 'nilai',
                        className: 'text-right',
                        render: function(data, type, row, meta) {
                            return new Intl.NumberFormat('id-ID', {
                                minimumFractionDigits: 2
                            }).format(data.nilai)
                        }
                    },
                    {
                        data: null,
                        name: 'simbol',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            if (data.ketspj == '1') {
                                return '&#10004';
                            } else {
                                return '&#10008';
                            }
                        }
                    },
                    {
                        visible: false,
                        data: 'sts_tetap',
                        name: 'Status Tetap',
                        className: "text-center",
                    },
                    {
                        visible: false,
                        data: 'kd_sub_kegiatan',
                        name: 'Kode Sub Kegiatan',
                        className: "text-center",
                    },
                    {
                        visible: false,
                        data: 'status_setor',
                        name: 'Status Setor',
                        className: "text-center",
                    },
                    {
                        visible: false,
                        data: 'jns_pembayaran',
                        name: 'Jenis Bayar',
                        className: "text-center",
                    },
                    {
                        visible: false,
                        data: 'jns_pajak',
                        name: 'Jenis Pajak',
                        className: "text-center",
                    },
                    {
                        visible: false,
                        data: 'kd_rek_lo',
                        name: 'Kode LO',
                        className: "text-center",
                    },
                    {
                        orderable: false,
                        data: 'aksi',
                        name: 'aksi',
                        width: '120px',
                    },
                ],
            });
        });

        $(document).ready(function() {
            $("#dengan_penetapan1").hide();
            $("#dengan_penetapan2").hide();
            $("#jns_bayar").hide();
            $("#jns_pajak_tambah").hide();
            // $("#dengan_setor").prop("checked", true);
            //Select2
            // No Tetap
            $('#no_tetap_tambah').select2({
                closeOnSelect: true,
                width: '100%',
                dropdownParent: $("#tambah-dialog"),
                // allowClear: true,
            }).on('select2:select', function() {
                let no_tetap = $('#no_tetap_tambah option:selected').val();
                let tgl = $('#no_tetap_tambah option:selected').data('tgl');
                let nilai = $('#no_tetap_tambah option:selected').data('nilai');
                let kd_rek6 = $('#no_tetap_tambah option:selected').data('kd_rek6');
                let kd_rek_lo = $('#no_tetap_tambah option:selected').data('kd_rek_lo');
                let keterangan = $('#no_tetap_tambah option:selected').data('ket');
                let kd_sub_kegiatan = $('#no_tetap_tambah option:selected').data('kd_sub_kegiatan');
                $('#kdkegiatan_tambah').val(kd_sub_kegiatan);
                $('#no_terima_tambah').val(no_tetap);
                $('#ket_tambah').val(keterangan);
                $('#kd_rek_lo_tambah').val(kd_rek_lo);
                $('#rekening_tambah').val(kd_rek6).change();
                $('#tgl_tetap_tambah').val(tgl);
                $('#tanggal_tambah').val(tgl);
                $('#nilai_tetap_tambah').val(nilai);
                $('#nilai_terima_tambah').val(nilai);
                // Readonly
                $('#ket_tambah').prop('readonly', true);
                $('#no_terima_tambah').prop('readonly', true);
                $('#tanggal_tambah').prop('readonly', true);
                $("#nilai_terima_tambah").prop('readonly', true);
                $("#tgl_tetap_tambah").prop('readonly', true);
            });
            $('#no_tetap_edit').select2({
                closeOnSelect: true,
                width: '100%',
                dropdownParent: $("#edit-dialog"),
                // allowClear: true,
            }).on('select2:select', function() {
                let no_tetap = $('#no_tetap_edit option:selected').val();
                let tgl = $('#no_tetap_edit option:selected').data('tgl');
                let nilai = $('#no_tetap_edit option:selected').data('nilai');
                let kd_rek6 = $('#no_tetap_edit option:selected').data('kd_rek6');
                let kd_rek_lo = $('#no_tetap_edit option:selected').data('kd_rek_lo');
                let keterangan = $('#no_tetap_edit option:selected').data('ket');
                let kd_sub_kegiatan = $('#no_tetap_edit option:selected').data('kd_sub_kegiatan');
                $('#kdkegiatan_edit').val(kd_sub_kegiatan);
                $('#no_terima_edit').val(no_tetap);
                $('#ket_edit').val(keterangan);
                $('#kd_rek_lo_edit').val(kd_rek_lo);
                $('#rekening_edit').val(kd_rek6).change();
                $('#tgl_tetap_edit').val(tgl);
                $('#tanggal_edit').val(tgl);
                $('#nilai_tetap_edit').val(nilai);
                $('#nilai_terima_edit').val(nilai);
                // Readonly
                $('#ket_edit').prop('readonly', true);
                $('#no_terima_edit').prop('readonly', true);
                $('#tanggal_edit').prop('readonly', true);
                $("#nilai_terima_edit").prop('readonly', true);
                $("#tgl_tetap_edit").prop('readonly', true);
            });

            // Jenis Pajak
            $('#jenispajak_tambah').select2({
                dropdownParent: $("#tambah-dialog")
            });
            $('#jenispajak_edit').select2({
                dropdownParent: $("#edit-dialog")
            });
            $('#jenis_pembayaran_edit').select2({
                dropdownParent: $("#edit-dialog")
            });
            // Rekening
            $('#rekening_tambah').select2({
                // closeOnSelect: true,
                width: '100%',
                dropdownParent: $("#tambah-dialog"),
                // allowClear: true,
            }).on('select2:select', function() {
                let kd_rek = $('#rekening_tambah option:selected').val();
                if (kd_rek == '410106010001' || kd_rek == '410114370001') {
                    $("#jns_pajak_tambah").show();
                } else {
                    $("#jns_pajak_tambah").hide();
                }
                let kd_sub_kegiatan = $('#rekening_tambah option:selected').data('kd_sub_kegiatan');
                let kd_rek_lo = $('#rekening_tambah option:selected').data('kd_rek_lo');
                $('#kdkegiatan_tambah').val(kd_sub_kegiatan);
                $('#kd_rek_lo_tambah').val(kd_rek_lo);
            });
            //   Jenis Bayar
            $('#jenis_pembayaran_tambah').select2({
                // closeOnSelect: true,
                width: '100%',
                dropdownParent: $("#tambah-dialog"),
                // allowClear: true,
            })
            $('#tambahpenerimaan').click(function() {
                $('#tambah-dialog').modal('show');
            });
            $('#rekening_edit').select2({
                width: '100%',
                dropdownParent: $("#edit-dialog"),
            }).on('select2:select', function() {
                let kd_sub_kegiatan = $('#rekening_edit option:selected').data('kd_sub_kegiatan');
                let kd_rek_lo = $('#rekening_edit option:selected').data('kd_rek_lo');
                let kd_rek = $('#rekening_edit option:selected').val();
                if (kd_rek == '410106010001' || kd_rek == '410114370001') {
                    $("#jns_pajak_edit").show();
                } else {
                    $("#jns_pajak_edit").hide();
                    $("#jenispajak_edit").val(null);
                }
                $('#rekening_edit').val(kd_rek);
                $('#kdkegiatan_edit').val(kd_sub_kegiatan);
                $('#kd_rek_lo_edit').val(kd_rek_lo);
            });



            // Pop Up Modal
            $('#tambah-dialog').modal({
                backdrop: "static"
            });
            $('#edit-dialog').modal({
                backdrop: "static"
            });
            $("#close-btn").on("click", function() {
                $('#tambah-dialog').modal('hide');
                kosongmodal();
            });
            $("#keluar-edit").on("click", function() {
                $('#edit-dialog').modal('hide');
                kosongmodal();
            })

            // CheckBox Dengan Penetapan
            let data = false;
            $('#pilihan').on('click', function() {
                if ($(this).is(':checked')) {
                    data = $(this).is(':checked');
                    if (data == true) {
                        $('#no_tetap_tambah').val(null).change();
                        $('#tgl_tetap_tambah').val(null);
                        $('#nilai_tetap_tambah').val(null);
                        $('#tanggal_tambah').val(null);
                        $('#nilai_terima_tambah').val(null);
                        $('#no_terima_tambah').val(null);
                        $('#ket_tambah').val(null);
                        $('#rekening_tambah').val(null).change();
                        $('#dengan_penetapan1').show();
                        $('#dengan_penetapan2').show();
                    }
                } else {
                    data = $(this).is(':checked');
                    if (data == false) {
                        $('#no_tetap_tambah').val(null).change();
                        $('#tgl_tetap_tambah').val(null);
                        $('#nilai_tetap_tambah').val(null);
                        $('#tanggal_tambah').val(null);
                        $('#nilai_terima_tambah').val(null);
                        $('#no_terima_tambah').val(null);
                        $('#ket_tambah').val(null);
                        $('#rekening_tambah').val(null).change();
                        $('#ket_tambah').prop("disabled", false);
                        $('#tanggal_tambah').prop("disabled", false);
                        $('#no_terima_tambah').prop("disabled", false);
                        $("#nilai_terima_tambah").prop("disabled", false);
                        $("#rekening_tambah").prop("disabled", false);
                        $('#dengan_penetapan1').hide();
                        $('#dengan_penetapan2').hide();
                    }
                }
            });
        });

        // Che  ckBox
        function opt(val) {
            ctk = val;
            if (ctk == "Dengan Setor") {
                $("#tanpa_setor").prop("checked", false);
                $("#jenis_pembayaran_tambah").val("TUNAI").change();
                $("#jenis_pembayaran_tambah").prop("disabled", true);

                $("#jns_bayar").show();
            } else if (ctk == "Tanpa Setor") {
                $("#dengan_setor").prop("checked", false);
                $("#jenis_pembayaran_tambah").val("BANK").change();
                $("#jenis_pembayaran_tambah").prop("disabled", true);

                $("#jns_bayar").show();
            }
        }

        function opt_edit(val) {
            ctk = val;
            if (ctk == "Dengan Setor") {
                $("#tanpa_setor_edit").prop("checked", false);
                $("#jenis_pembayaran_edit").val("TUNAI").change();
                $("#jns_bayar_edit").show();
            } else if (ctk == "Tanpa Setor") {
                $("#dengan_setor_edit").prop("checked", false);
                $("#jenis_pembayaran_edit").val("BANK").change();
                $("#jns_bayar_edit").show();
            }
        }

        function simpanpenerimaan() {
            let no_tetap = $('#no_tetap_tambah').val();
            let tgl_tetap = $('#tgl_tetap_tambah').val();
            let nilai_tetap = $('#nilai_tetap_tambah').val();
            let no_terima = $('#no_terima_tambah').val();
            let statusSetor = $('input[name="status_setor"]:checked').val();
            let jns_pembayaran = $('#jenis_pembayaran_tambah').val();
            let tgl_terima = $('#tanggal_tambah').val();
            let rekening = $('#rekening_tambah').val();
            let nilai_terima = angka($('#nilai_terima_tambah').val());
            // alert(nilai_terima);
            // return;
            let keterangan = $('#ket_tambah').val();
            let kd_sub_kegiatan = $('#kdkegiatan_tambah').val();
            let kd_rek_lo = $('#kd_rek_lo_tambah').val();
            let jenispajak = $('#jenispajak_tambah').val();
            let pilihan = document.getElementById('pilihan').checked;
            let dengan_penetapan;
            if (pilihan == false) {
                dengan_penetapan = 0;
            } else {
                dengan_penetapan = 1;
            }
            if (no_terima == '') {
                alert('Nomor tidak boleh kosong');
                return;
            } else if (statusSetor == undefined) {
                alert('Jenis Setor tidak boleh kosong');
                return;
            } else if (tgl_terima == '') {
                alert('Tanggal tidak boleh kosong');
                return;
            } else if (rekening == '') {
                alert('Rekening tidak boleh kosong');
                return;
            } else if (nilai_terima == '') {
                alert('Nilai 0! Cek lagi');
                return;
            } else if (keterangan == '') {
                alert('Keterangan tidak boleh kosong');
                return;
            }

            $(document).ready(function() {
                $.ajax({
                    url: "{{ route('simpanpenerimaantahunini') }}",
                    type: 'POST',
                    data: {
                        cdengan_penetapan: dengan_penetapan,
                        cno_tetap: no_tetap,
                        ctgl_tetap: tgl_tetap,
                        cno_terima: no_terima,
                        cstatusSetor: statusSetor,
                        cjns_pembayaran: jns_pembayaran,
                        ctgl_terima: tgl_terima,
                        crekening: rekening,
                        cnilai_terima: nilai_terima,
                        cketerangan: keterangan,
                        ckd_sub_kegiatan: kd_sub_kegiatan,
                        ckd_rek_lo: kd_rek_lo,
                        cjenispajak: jenispajak
                    },
                    dataType: 'json',
                    // beforeSend: function() {
                    //     $("#simpan-btn").attr("disabled", "disabled");
                    // },
                    success: function(data) {
                        if (data.message == 'berhasil') {
                            alert('Data berhasil disimpan');
                            $('#tambah-dialog').modal('hide');
                            location.reload();
                            // $('#penerimaan_tahun_ini').DataTable().ajax.reload();
                            kosongmodal();
                        }
                        if (data.message == 'sudah ada') {
                            alert('Nomor Penerimaan sudah ada');
                        }
                    },
                    error: function(message) {
                        console.log(message);
                    }
                });
            });
        }

        function kosongmodal() {
            $('#no_tetap_tambah').val(null).change();
            $('#tgl_tetap_tambah').val(null);
            $('#nilai_tetap_tambah').val(null);
            $('#tanggal_tambah').val(null);
            $('#nilai_terima_tambah').val(null);
            $('#no_terima_tambah').val(null);
            $('#kdkegiatan_tambah').val(null);
            $('#ket_tambah').val(null);
            $('#jns_bayar').hide();
            $('#jenis_pembayaran_tambah').val(null).change();
            $('#rekening_tambah').val(null).change();
            $('#ket_tambah').prop("disabled", false);
            $('#tanggal_tambah').prop("disabled", false);
            $('#no_terima_tambah').prop("disabled", false);
            $("#nilai_terima_tambah").prop("disabled", false);
            $("#rekening_tambah").prop("disabled", false);
            $('#dengan_penetapan1').hide();
            $('#dengan_penetapan2').hide();
            $('#simpan-btn').prop('disabled', false);
            $("#pilihan").prop("checked", false);
            $("#dengan_setor").prop("checked", false);
            $("#tanpa_setor").prop("checked", false);
            // Edit
            $('#no_tetap_edit').val(null).change();
            $('#tgl_tetap_edit').val(null);
            $('#nilai_tetap_edit').val(null);
            $('#tanggal_edit').val(null);
            $('#nilai_terima_edit').val(null);
            $('#no_terima_edit').val(null);
            $('#kdkegiatan_edit').val(null);
            $('#ket_edit').val(null);
            $('#jns_bayar').hide();
            $('#jenis_pembayaran_edit').val(null).change();
            $('#rekening_edit').val(null).change();
            $("#dengan_setor_edit").prop("checked", false);
            $("#tanpa_setor_edit").prop("checked", false);
        }

        $(document).ready(function() {
            $('#penerimaan_tahun_ini').on('click', '#edit', function() {
                // var data = table.row(this).data();
                var data = table.row($(this).parents('tr')).data();
                let status_setor = data['status_setor'];
                let status_tetap = data['sts_tetap'];
                let jns_pajak = data['jns_pajak'];
                if (jns_pajak == '' || jns_pajak == null) {
                    $('#jns_pajak_edit').hide();
                    $('#jenispajak_edit').val(null);
                }
                if (status_setor == "Dengan Setor") {
                    $("#dengan_setor_edit").prop("checked", true);
                } else if (status_setor == "Tanpa Setor") {
                    $("#tanpa_setor_edit").prop("checked", true);
                }
                if (status_tetap == "1") {
                    $("#pilihan_edit").prop("checked", true);
                    $('#no_tetap_edit').val(data['no_tetap']).change();
                    $('#tgl_tetap_edit').val(data['tgl_tetap']);
                    $('#nilai_tetap_edit').val(data['nilai']);
                    $('#tgl_tetap_edit').prop("readonly", true);
                    $('#nilai_tetap_edit').prop("readonly", true);
                    $('#ket_edit').prop("readonly", true);
                    $('#kd_rek_lo_edit').prop("readonly", true);
                    $('#kdkegiatan_edit').prop("readonly", true);
                    $("#dengan_penetapan1").show();
                    $("#dengan_penetapan2").show();
                } else if (status_tetap == "0") {
                    $("#pilihan_edit").prop("checked", false);
                    $('#no_tetap_edit').val(null).change();
                    $('#tgl_tetap_edit').val(null);
                    $('#nilai_tetap_edit').val(null);
                    $("#dengan_penetapan1_edit").hide();
                    $("#dengan_penetapan2_edit").hide();
                }
                $('#no_terima_edit').val(data['no_terima']);
                $('#tanggal_edit').val(data['tgl_terima']);
                $('#rekening_edit').val(data['kd_rek6']).change();
                $('#jenispajak_edit').val(data['jns_pajak']).change();
                $('#nilai_terima_edit').val(data['nilai']);
                $('#jenis_pembayaran_edit').val(data['jns_pembayaran']).change();
                $('#ket_edit').val(data['ket']);
                $('#kd_rek_lo_edit').val(data['kd_rek_lo']);
                $('#kdkegiatan_edit').val(data['kd_sub_kegiatan']);
                $('#edit-form').attr('action',
                    `{{ route('updatepenerimaantahunini') }}/${data['no_terima']}`)
                $('#edit-dialog').modal('show');
            });
            $('#edit-btn').click(function() {
                $('#edit-form').submit()
            })
        });

        function hapusdata(no_terima) {
            let hapus = confirm('Yakin Ingin Menghapus Penerimaan ' + no_terima + ' ?');
            if (hapus == true) {
                $(document).ready(function() {
                    $.ajax({
                        url: "{{ route('hapuspenerimaantahunini') }}",
                        type: 'POST',
                        data: {
                            cno_terima: no_terima
                        },
                        dataType: 'json',
                        success: function(data) {
                            if (data.message == '0') {
                                alert('Data sudah disetorkan');
                            } else if (data.message == '1') {
                                alert('Data berhasil dihapus');
                                $('#penerimaan_tahun_ini').DataTable().ajax.reload();
                            }
                        },
                        error: function(message) {
                            console.log(message);
                        }
                    });
                });
            }
        }



        $(document).ready(function() {
            $("input[data-type='currency']").on({
                keyup: function() {
                    formatCurrency($(this));
                },
                blur: function() {
                    formatCurrency($(this), "blur");
                }
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
