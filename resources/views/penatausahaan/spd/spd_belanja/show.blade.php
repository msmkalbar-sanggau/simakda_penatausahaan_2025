@extends('template.app')
@section('title', 'Tampil SPD Belanja | SIMAKDA')
@section('content')

<div class="row">
    {{-- Input form --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                SPD Belanja
            </div>
            <div class="card-body">
                @csrf
                <!-- Kode SKPD dan Nama SKPD -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="kd_skpd">Kode SKPD</label>
                            <input type="text" class="form-control" id="nm_skpd" name="nm_skpd" value="{{ $dataspd->kd_skpd }}" readonly />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="nm_skpd">Nama SKPD</label>
                            <input type="text" class="form-control" id="nm_skpd" name="nm_skpd" value="{{ $dataspd->nm_skpd }}" readonly />
                        </div>
                    </div>
                </div>

                <!-- nip dan nama skpd dan beban-->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="nip">NIP SKPD</label>
                            <input type="text" class="form-control" id="nip" name="nip" value="{{ $dataspd->kd_bkeluar }}" readonly />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="nama_bend">Nama Kepala SKPD</label>
                            <input type="text" class="form-control" id="nama_bend" name="nama_bend" value="{{ $nm_bend->nama }}" readonly />
                        </div>
                    </div>
                </div>

                <!-- no spd dan tgl spd -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="no_spd">No SPD
                                <small style="color: red;">*Format nomor SPD : 13.00/01.0/XXXXXX/KODE SKPD/M/1/2021</small>
                            </label>
                            <input type="text" class="form-control" id="nomor" name="nomor" value="{{ $dataspd->no_spd }}" readonly />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="tanggal">Tanggal SPD</label>
                            <input type="text" class="form-control" id="tanggal" name="tanggal" value="{{ \Carbon\Carbon::parse($dataspd->tgl_spd)->locale('id')->isoFormat('D MMMM Y')  }}" readonly />
                        </div>
                    </div>
                </div>

                <!-- periode bulan dan jenis beban -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label" for="bulan_awal">Periode Bulan Awal</label>
                            <input type="text" class="form-control" id="bulan_awal" name="bulan_awal" value="{{ MSbulan($dataspd->bulan_awal)  }}" readonly />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label" for="bulan_akhir">Periode Bulan Akhir</label>
                            <input type="text" class="form-control" id="bulan_akhir" name="bulan_akhir" value="{{ MSbulan($dataspd->bulan_akhir)  }}" readonly />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label" for="jenis">Beban</label>
                            <input type="text" class="form-control" id="jenis" name="jenis" value="{{ jsBbn($dataspd->jns_beban)  }}" readonly />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label" for="revisi">Jenis SPD</label>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" disabled id="revisi" name="revisi" {{ $dataspd->revisi_ke == '0' ? '' : 'checked' }}>
                                <label class="form-check-label" for="revisi">Revisi</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- jenis anggaran dan status angkas -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="jenis_anggaran">Jenis Anggaran</label>
                            <input type="text" class="form-control" id="jenis_anggaran" name="jenis_anggaran" value="{{ $dataspd->jns_ang  }}" readonly />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="status_angkas">Status Angkas</label>
                            <input type="text" class="form-control" id="status_angkas" name="status_angkas" value="{{ $dataspd->jns_angkas  }}" readonly />
                        </div>
                    </div>
                </div>
                <!-- keterangan -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label" for="keterangan">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" rows="2" class="form-control" readonly>{{ $dataspd->klain }}</textarea>
                        </div>
                    </div>
                </div>
                <!-- SIMPAN -->
                <div class="mb-3 row" style="float: right;">
                    <div class="col-md-12" style="text-align: center">
                        <a href="{{ route('spd_belanja.index') }}" class="btn btn-warning btn-md">Kembali</a>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                Rincian SPD
            </div>
            <div class="card-body">
                <div class="table-rep-plugin">
                    <div class="table-responsive mb-0" data-pattern="priority-columns">
                        <table id="spd_belanja" class="table" style="width: 100%">
                            <thead>
                                <tr>
                                    <th style="width: 100px;text-align:center">Kode Unit</th>
                                    <th style="width: 100px;text-align:center">Kode Sub Kegiatan</th>
                                    <th style="width: 100px;text-align:center">Kode Rekening</th>
                                    <th style="width: 100px;text-align:center">Nilai SPD Ini</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <div class="mb-2 mt-2 row">
                        <label for="total" class="col-md-8 col-form-label" style="text-align: right">Total
                            SPD</label>
                        <div class="col-md-4">
                            <input type="text" style="text-align: right" readonly class="form-control" id="total" name="total" value="{{ $dataspd->total }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('js')
@include('penatausahaan.spd.spd_belanja.js.show')
@endsection