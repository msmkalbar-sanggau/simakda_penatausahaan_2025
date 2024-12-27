@extends('template.app')
@section('title', 'Tampil SP2D | SIMAKDA')
@section('content')
    <div class="row">
        {{-- Input form --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Data SP2D
                </div>
                <div class="card-body">
                    @csrf
                    @if ($sp2d->status_bud == '1')
                        <div class="alert alert-success alert-block">
                            <b style="font-size:16px">
                                SP2D SUDAH TERSALURKAN
                            </b>
                        </div>
                    @elseif ($sp2d->status_bud != '1' && $sp2d->is_verified == '1')
                        <div class="alert alert-warning alert-block">
                            <b style="font-size:16px">
                                SP2D SUDAH VERIFIKASI
                            </b>
                        </div>
                    @else
                        <div class="alert alert-danger alert-block">
                            <b style="font-size:16px">
                                SP2D BELUM VERIFIKASI
                            </b>
                        </div>
                    @endif

                    {{-- No SP2D --}}
                    <div class="mb-3 row">
                        <label for="no_sp2d" class="col-md-2 col-form-label">No SP2D</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" id="no_sp2d" name="no_sp2d"
                                value="{{ $sp2d->no_sp2d }}" readonly>
                            <input type="text" class="form-control" id="nocek" name="nocek"
                                value="{{ $sp2d->nocek }}" readonly hidden>
                            <input type="text" class="form-control" id="kd_skpd" name="kd_skpd"
                                value="{{ $sp2d->kd_skpd }}" readonly hidden>
                        </div>
                    </div>
                    {{-- No Cair --}}
                    <div class="mb-3 row">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('verif_sp2d.index') }}" class="btn btn-warning btn-md">Kembali</a>
                            @if ($sp2d->status_bud == '1')
                            @elseif ($sp2d->is_verified == '1')
                                {{-- sudah verif --}}
                                <button class="btn btn-md btn-primary" id="batal_verif"
                                    style="border: 1px solid black">BATAL VERIF</button>
                                {{-- <button class="btn btn-md btn-primary" id="verif_sp2d" style="border: 1px solid black" hidden="true">VERIF SP2D</button> --}}
                            @else
                                {{-- belum verif --}}
                                {{-- <button class="btn btn-md btn-primary" id="batal_verif" style="border: 1px solid black" hidden="true" >BATAL VERIF</button> --}}
                                <button class="btn btn-md btn-primary" id="verif_sp2d" style="border: 1px solid black">VERIF
                                    SP2D</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <ul class="nav nav-pills" role="tablist">
                <li class="nav-item waves-effect waves-light">
                    <a class="nav-link active" data-bs-toggle="tab" href="#navpills-home" role="tab">
                        <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                        <span class="d-none d-sm-block">BERKAS</span>
                    </a>
                </li>
                <li class="nav-item waves-effect waves-light">
                    <a class="nav-link" data-bs-toggle="tab" href="#navpills-profile" role="tab">
                        <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                        <span class="d-none d-sm-block">POTONGAN</span>
                    </a>
                </li>
                <li class="nav-item waves-effect waves-light">
                    <a class="nav-link" data-bs-toggle="tab" href="#navpills-lamp" role="tab">
                        <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                        <span class="d-none d-sm-block">LAMPIRAN</span>
                    </a>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content p-3 text-muted">
                <div class="tab-pane active" id="navpills-home" role="tabpanel">
                    <div class="card">
                        <div class="card-body table-responsive">
                            <table style="width: 100%" style="border: 1px solid black">
                                <tbody>
                                    <tr>
                                        <td class="border1" colspan="3" style="width: 50%">{{ $daerah->provinsi }}</td>
                                        <td class="border1" colspan="3" style="width: 50%">SURAT PERINTAH PENCAIRAN DANA
                                            (SP2D)<br>Nomor :
                                            {{ $no_sp2d }}</td>
                                    </tr>
                                    <tr>
                                        <td class="kiri">Nomor SPM</td>
                                        <td>:</td>
                                        <td class="kanan">{{ $data_sp2d->no_spm }}</td>
                                        <td>Dari</td>
                                        <td>:</td>
                                        <td class="kanan">KUASA BUD</td>
                                    </tr>
                                    <tr>
                                        <td class="kiri">Tanggal</td>
                                        <td>:</td>
                                        <td class="kanan">{{ tanggal($data_sp2d->tgl_spm) }}</td>
                                        <td>NPWP</td>
                                        <td>:</td>
                                        <td class="kanan"></td>
                                    </tr>
                                    <tr>
                                        <td class="kiri bawah">Nama SKPD</td>
                                        <td class="bawah">:</td>
                                        <td class="kanan bawah">{{ $data_sp2d->kd_skpd }} {{ $data_sp2d->nm_skpd }}</td>
                                        <td class="bawah">Tahun Anggaran</td>
                                        <td class="bawah">:</td>
                                        <td class="kanan bawah">{{ tahun_anggaran() }}</td>
                                    </tr>
                                    <tr>
                                        <td class="kiri">Bank Pengirim</td>
                                        <td>:</td>
                                        <td colspan="4" class="kanan">PT. Bank Kalbar Cabang Sanggau</td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="kiri kanan">Hendaklah mencairkan / memindahbukukan dari
                                            baki
                                            Rekening
                                            Nomor
                                            3001006966</td>
                                    </tr>
                                    <tr>
                                        <td class="kiri bawah">Uang sebesar</td>
                                        <td class="bawah">:</td>
                                        <td class="kanan bawah" colspan="4">Rp {{ rupiah($nilai) }}
                                            ({{ terbilang($nilai) }})
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="kiri">Kepada</td>
                                        <td>:</td>
                                        <td colspan="4" class="kanan">
                                            @if (($data_sp2d->jns_spp == '6' && $data_sp2d->jenis_beban == '6') || $data_sp2d->jns_spp == '5')
                                                <!-- {{ $data_sp2d->pimpinan }},
                                                            {{ $data_sp2d->nmrekan }}, {{ $data_sp2d->alamat }} -->
                                                {{ $bk->nm_rekening }}
                                            @else
                                                {{-- {{ $bk->nama ? $bk->nama : 'Belum Ada data Bendahara' }} -
                                                {{ $bk->jabatan ? $bk->jabatan : ' ' }} --}}
                                                {{ $bk->nm_rekening }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="kiri">NPWP</td>
                                        <td>:</td>
                                        <td colspan="4" class="kanan">
                                            @if (($data_sp2d->jns_spp == '6' && $data_sp2d->jenis_beban == '6') || $data_sp2d->jns_spp == '5')
                                                {{ $data_sp2d->npwp }}
                                            @else
                                                {{ $data_sp2d->npwp ? $data_sp2d->npwp : ' ' }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="kiri">No.Rekening Bank</td>
                                        <td>:</td>
                                        <td colspan="4" class="kanan">
                                            @if (($data_sp2d->jns_spp == '6' && $data_sp2d->jenis_beban == '6') || $data_sp2d->jns_spp == '5')
                                                {{ $data_sp2d->no_rek }}
                                            @else
                                                {{-- {{ $bank->rekening ? $bank->rekening : ' ' }} --}}
                                                {{ $bk->rekening }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="kiri">Bank Penerima</td>
                                        <td>:</td>
                                        <td colspan="4" class="kanan">
                                            @if (($data_sp2d->jns_spp == '6' && $data_sp2d->jenis_beban == '6') || $data_sp2d->jns_spp == '5')
                                                {{ $sp2d->bank ? bank($sp2d->bank) : 'Belum Pilih Bank' }}
                                            @else
                                                {{ $sp2d->bank ? bank($sp2d->bank) : 'Belum Pilih Bank' }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="kiri">Keperluan Untuk</td>
                                        <td>:</td>
                                        <td colspan="4" class="kanan">{{ $data_sp2d->keperluan }}
                                            <br>
                                            {{-- @if ($data_sp2d->jns_spp == '6')
                                                {{ right($kd_prog, 2) }} - {{ $nm_prog }}<br>
                                                {{ right($kd_kegi, 2) }} - {{ $nm_kegi }}
                                            @else
                                                {{ right($kd_prog, 2) }} {{ $nm_prog }}<br>
                                                {{ right($kd_kegi, 2) }} {{ $nm_kegi }}
                                            @endif --}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="kiri">Pagu Anggaran</td>
                                        <td>:</td>
                                        <td colspan="4" class="kanan">
                                            @if ($data_sp2d->jns_spp == '1')
                                            @else
                                                Rp {{ rupiah($pagu) }}
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table style="width: 100%" class="table table-bordered border-dark mb-0">
                                <tbody>
                                    <tr>
                                        <td class="center"><b>NO</b></td>
                                        <td class="center"><b>KODE KEGIATAN/SUB KEGIATAN</b></td>
                                        <td class="center"><b>URAIAN</b></td>
                                        <td class="center"><b>JUMLAH<br>(Rp)</b></td>
                                    </tr>
                                    <tr>
                                        <td class="center"><b>1</b></td>
                                        <td class="center"><b>2</b></td>
                                        <td class="center"><b>3</b></td>
                                        <td class="center"><b>4</b></td>
                                    </tr>
                                    @if (in_array($data_sp2d->jns_spp, ['1', '2']))
                                        <tr>
                                            <td style="text-align: center">1</td>
                                            <td>{{ $data_sp2d->kd_skpd }}</td>
                                            <td>{{ $data_sp2d->nm_skpd }}</td>
                                            <td style="text-align: right">{{ rupiah($total_kegiatan->nilai) }}</td>
                                        </tr>
                                    @else
                                        @foreach ($sub_kegiatan as $sp2d)
                                            <tr>
                                                <td style="text-align: center">{{ $loop->iteration }}</td>
                                                <td>{{ $sp2d->kd_rek }}</td>
                                                <td>{{ $sp2d->nm_rek }}</td>
                                                <td style="text-align: right">{{ rupiah($sp2d->nilai) }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    <tr>
                                        <td colspan="3" style="text-align: right"><b>JUMLAH</b></td>
                                        <td style="text-align: right"><b>{{ rupiah($total_kegiatan->nilai) }}</b></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">Potongan-potongan</td>
                                    </tr>
                                </tbody>
                            </table>
                            <table style="width: 100%" class="table table-bordered border-dark mb-0">
                                <tbody>
                                    <tr>
                                        <td class="center"><b>NO</b></td>
                                        <td class="center"><b>Uraian (No.Rekening)</b></td>
                                        <td class="center"><b>Jumlah (Rp)</b></td>
                                        <td class="center"><b>Keterangan</b></td>
                                    </tr>
                                    @php
                                        $jumlah_pot1 = 0;
                                    @endphp
                                    @foreach ($potongan1 as $potongan)
                                        @php
                                            $jumlah_pot1 += $potongan->nilai;
                                        @endphp
                                        <tr>
                                            <td style="text-align: center">{{ $loop->iteration }}</td>
                                            <td>{{ dotrek($potongan->kd_rek6) }} {{ $potongan->nm_rek6 }}</td>
                                            <td style="text-align: right">{{ rupiah($potongan->nilai) }}</td>
                                            <td></td>
                                        </tr>
                                    @endforeach
                                    @for ($i = count($potongan1); $i < 4; $i++)
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    @endfor
                                    <tr>
                                        <td colspan="2" style="text-align: right"><b>JUMLAH</b></td>
                                        <td style="text-align: right"><b>{{ rupiah($jumlah_pot1) }}</b></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">Informasi <i>(tidak mengurangi jumlah pembayaran SP2D)</i></td>
                                    </tr>
                                    <tr>
                                        <td class="center"><b>NO</b></td>
                                        <td class="center"><b>Uraian (No.Rekening)</b></td>
                                        <td class="center"><b>Jumlah (Rp)</b></td>
                                        <td class="center"><b>Keterangan</b></td>
                                    </tr>
                                    @php
                                        $jumlah_pot2 = 0;
                                    @endphp
                                    @foreach ($potongan2 as $potongan)
                                        @php
                                            $jumlah_pot2 += $potongan->nilai;
                                        @endphp
                                        <tr>
                                            <td style="text-align: center">{{ $loop->iteration }}</td>
                                            <td>{{ dotrek($potongan->kd_rek6) }} {{ $potongan->nm_rek6 }}</td>
                                            <td style="text-align: right">{{ rupiah($potongan->nilai) }}</td>
                                            <td></td>
                                        </tr>
                                    @endforeach
                                    @for ($i = count($potongan2); $i < 4; $i++)
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    @endfor
                                    <tr>
                                        <td colspan="2" style="text-align: right"><b>JUMLAH</b></td>
                                        <td style="text-align: right"><b>{{ rupiah($jumlah_pot2) }}</b></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"><b>SP2D yang Dibayarkan</b></td>
                                    </tr>
                                </tbody>
                            </table>
                            <table style="width: 100%">
                                <tbody>
                                    <tr>
                                        <td colspan="2" class="kiri bawah">Jumlah yang Diminta</td>
                                        <td class="bawah">Rp</td>
                                        <td style="text-align: right" class="kanan bawah">
                                            {{ rupiah($total_kegiatan->nilai) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="kiri bawah">Jumlah Potongan</td>
                                        <td class="bawah">Rp</td>
                                        <td style="text-align: right" class="kanan bawah">
                                            {{ rupiah($jumlah_pot1) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="kiri bawah"><b>Jumlah yang Dibayarkan</b></td>
                                        <td class="bawah"><b>Rp</b></td>
                                        <td style="text-align: right" class="kanan bawah">
                                            <b>{{ rupiah($total_kegiatan->nilai - $jumlah_pot1) }}</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="kanan kiri bawah"><b>Uang Sejumlah :
                                                ({{ terbilang($total_kegiatan->nilai - $jumlah_pot1) }})</b>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table style="width: 100%" id="ttd" style="border: 1px solid black">
                                <tr>
                                    <td colspan="6" class="kanan kiri" style="height: 20px"></td>
                                </tr>
                                <tr>
                                    <td class="kiri">Lembar 1</td>
                                    <td>:</td>
                                    <td>Bank Yang Ditunjuk</td>
                                    <td></td>
                                    <td style="text-align: center" class="kanan"><b>Sanggau,
                                            {{ tanggal($data_sp2d->tgl_sp2d) }}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="kiri">Lembar 2</td>
                                    <td>:</td>
                                    <td>Pengguna Anggaran/Kuasa Pengguna Anggaran</td>
                                    <td></td>
                                    <td style="text-align: center" class="kanan"><b></b></td>
                                </tr>
                                <tr>
                                    <td class="kiri">Lembar 3</td>
                                    <td>:</td>
                                    <td>Arsip Kuasa BUD</td>
                                    <td></td>
                                    <td style="text-align: center" class="kanan"><b>{{ $bud->jabatan }}</b></td>
                                </tr>
                                <tr>
                                    <td class="kiri">Lembar 4</td>
                                    <td>:</td>
                                    <td>Pihak Penerima</td>
                                    <td></td>
                                    <td style="text-align: center" class="kanan"></td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="kanan kiri" style="height: 50px"></td>
                                </tr>
                                <tr>
                                    <td class="kiri"></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td style="text-align: center" class="kanan"><b><u>{{ $bud->nama }}</u></b></td>
                                </tr>
                                <tr>
                                    <td class="kiri bawah"></td>
                                    <td class="bawah"></td>
                                    <td class="bawah"></td>
                                    <td class="bawah"></td>
                                    <td style="text-align: center" class="kanan bawah"><b>NIP. {{ $bud->nip }}</b>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="navpills-profile" role="tabpanel">
                    <div class="card">
                        <div class="card-body table-responsive">
                            <table style="width: 100%" class="table table-bordered border-dark mb-0">
                                <tbody>
                                    <tr>
                                        <td class="center"><b>NO</b></td>
                                        <td class="center"><b>Uraian (No.Rekening)</b></td>
                                        <td class="center"><b>ID Billing</b></td>
                                        <td class="center"><b>Jumlah (Rp)</b></td>
                                    </tr>
                                    @php
                                        $jumlah_pot1 = 0;
                                    @endphp
                                    @foreach ($potongan1 as $potongan)
                                        @php
                                            $jumlah_pot1 += $potongan->nilai;
                                        @endphp
                                        <tr>
                                            <td style="text-align: center">{{ $loop->iteration }}</td>
                                            <td>{{ dotrek($potongan->kd_rek6) }} {{ $potongan->nm_rek6 }}</td>
                                            <td style="text-align: right">{{ Billing($potongan->idBilling) }}</td>
                                            <td style="text-align: right">{{ rupiah($potongan->nilai) }}</td>

                                        </tr>
                                    @endforeach

                                    <tr>
                                        <td colspan="3" style="text-align: right"><b>JUMLAH</b></td>
                                        <td style="text-align: right"><b>{{ rupiah($jumlah_pot1) }}</b></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">Informasi <i>(tidak mengurangi jumlah pembayaran SP2D)</i></td>
                                    </tr>
                                    <tr>
                                        <td class="center"><b>NO</b></td>
                                        <td class="center"><b>Uraian (No.Rekening)</b></td>
                                        <td class="center"><b>ID Billing</b></td>
                                        <td class="center"><b>Jumlah (Rp)</b></td>
                                    </tr>
                                    @php
                                        $jumlah_pot2 = 0;
                                    @endphp
                                    @foreach ($potongan2 as $potongan)
                                        @php
                                            $jumlah_pot2 += $potongan->nilai;
                                        @endphp
                                        <tr>
                                            <td style="text-align: center">{{ $loop->iteration }}</td>
                                            <td>{{ dotrek($potongan->kd_rek6) }} {{ $potongan->nm_rek6 }}</td>
                                            <td style="text-align: right">{{ Billing($potongan->idBilling) }}</td>
                                            <td style="text-align: right">{{ rupiah($potongan->nilai) }}</td>
                                        </tr>
                                    @endforeach

                                    <tr>
                                        <td colspan="3" style="text-align: right"><b>JUMLAH</b></td>
                                        <td style="text-align: right"><b>{{ rupiah($jumlah_pot2) }}</b></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="text-align: right"><b>TOTAL POTONGAN</b></td>
                                        <td style="text-align: right"><b>{{ rupiah($jumlah_pot2 + $jumlah_pot1) }}</b>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="navpills-lamp" role="tabpanel">
                    <div class="card">
                        <div class="card-body table-responsive">
                            <table style="width: 100%" class="table table-bordered border-dark mb-0">
                                <tbody>
                                    <tr>
                                        <td class="center"><b>NO</b></td>
                                        <td class="center"><b>Kode Kegiatan / Kode Sub Kegiatan</b></td>
                                        <td class="center"><b>Uraian</b></td>
                                        <td class="center"><b>Jumlah (Rp)</b></td>
                                    </tr>
                                </tbody>
                                @php
                                    $jumlahlamp = 0;
                                @endphp
                                @foreach ($data_lamp as $lamp)
                                    @php
                                        $jumlahlamp += $lamp->nilai;
                                    @endphp
                                    @if ($lamp->no == '3')
                                        <tr>
                                            <td class="center">{{ $loop->iteration }}</td>
                                            <td>{{ dotrek($lamp->kd_rek) }}</td>
                                            <td>{{ $lamp->nm_rek }}</td>
                                            <td style="text-align: right">{{ rupiah($lamp->nilai) }}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td class="center"><b>{{ $loop->iteration }}</b></td>
                                            <td><b>{{ $lamp->kd_rek }}</b></td>
                                            <td><b>{{ $lamp->nm_rek }}</b></td>
                                            <td style="text-align: right"><b>{{ rupiah($lamp->nilai) }}</b></td>
                                        </tr>
                                    @endif
                                @endforeach
                                <tr>
                                    <td colspan="3" style="text-align: right"><b>JUMLAH</b></td>
                                    <td style="text-align: right"><b>{{ rupiah($total_kegiatan->nilai) }}</b></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            {{-- TAB --}}


        </div>
    </div>
@endsection
@section('js')
    <style>
        .center {
            text-align: center
        }

        .border1 {
            border: 1px solid black;
            text-align: center
        }

        .kanan {
            border-right: 1px solid black;
        }

        .kiri {
            border-left: 1px solid black;
        }

        .bawah {
            border-bottom: 1px solid black;
        }
    </style>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#verif_sp2d').on('click', function() {
                let no_sp2d = document.getElementById('no_sp2d').value;

                $('#verif_sp2d').prop('disabled', true);
                swal.fire({
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Proses validasi',
                    text: 'Silahkan tunggu !!!',
                    onOpen: function() {
                        swal.showLoading()
                    }
                })
                $.ajax({
                    url: "{{ route('verif_sp2d.verif_sp2d') }}",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        no_sp2d: no_sp2d,
                    },
                    success: function(response) {
                        if (response.message == '1') {
                            Swal.fire({
                                title: 'SUKSES!',
                                text: 'SP2D berhasil diverifikasi',
                                icon: 'success',
                                confirmButtonColor: '#5b73e8',
                            })

                            // alert('SP2D berhasil diverifikasi');
                            window.location.reload();
                        } else {
                            Swal.fire({
                                type: "error",
                                icon: "error",
                                title: "Oops...",
                                text: 'Sp2D Gagal diverifikasi!',
                                confirmButtonClass: "btn btn-confirm mt-2",
                            })
                            // alert('Sp2D Gagal diverifikasi!');
                            $('#verif_sp2d').prop('disabled', false);
                        }
                    }
                })

            });

            $('#batal_verif').on('click', function() {
                let no_sp2d = document.getElementById('no_sp2d').value;

                $('#batal_verif').prop('disabled', true);
                swal.fire({
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    title: 'Proses validas',
                    text: 'Silahkan tunggu !!!',
                    onOpen: function() {
                        swal.showLoading()
                    }
                })
                $.ajax({
                    url: "{{ route('verif_sp2d.batal_verif') }}",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        no_sp2d: no_sp2d,
                    },
                    success: function(response) {
                        if (response.message == '1') {
                            Swal.fire({
                                title: 'SUKSES!',
                                text: 'SP2D berhasil Dibatal verifikasi',
                                icon: 'success',
                                confirmButtonColor: '#5b73e8',
                            })
                            window.location.reload();
                        } else {
                            Swal.fire({
                                type: "error",
                                icon: "error",
                                title: "Oops...",
                                text: 'SP2D Gagal batal verifikasi!',
                                confirmButtonClass: "btn btn-confirm mt-2",
                            })
                            $('#batal_verif').prop('disabled', false);
                        }
                    }
                })

            });
        });
    </script>
    {{-- @include('penatausahaan.pengeluaran.pencairan_sp2d.js.show'); --}}
@endsection
