<?php

use App\Http\Controllers\Anggaran\PengesahanAngkasController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SkpdPenggunaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PenerimaController;
use App\Http\Controllers\KontrakController;
use App\Http\Controllers\PenagihanController;
use App\Http\Controllers\BankKalbarController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BendaharaUmumDaerahController;
use App\Http\Controllers\BUD\PengesahanController;
use App\Http\Controllers\SppLsController;
use App\Http\Controllers\SppUpController;
use App\Http\Controllers\SpmController;
use App\Http\Controllers\VerifikasiSpmController;
use App\Http\Controllers\Sp2dController;
use App\Http\Controllers\DaftarPengujiController;
use App\Http\Controllers\JurnalKoreksiController;
use App\Http\Controllers\PencairanSp2dController;
use App\Http\Controllers\Skpd\VerifikasiSPPController;
use App\Http\Controllers\Skpd\PencairanSp2dController as CairSp2dController;
use App\Http\Controllers\BUD\VerifSp2dController;
use App\Http\Controllers\Skpd\TerimaSp2dController;
use App\Http\Controllers\Skpd\TransaksiCmsController;
use App\Http\Controllers\Skpd\UploadCmsController;
use App\Http\Controllers\Skpd\ValidasiCmsController;
use App\Http\Controllers\Skpd\PotonganPajakCmsController;
use App\Http\Controllers\Skpd\PotonganPajakController;
use App\Http\Controllers\Skpd\SetorPotonganController;
use App\Http\Controllers\Skpd\TransaksiPemindahbukuanController;
use App\Http\Controllers\Skpd\TransaksiTunaiController;
use App\Http\Controllers\SettingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as FacadesRequest;
use App\Http\Controllers\skpd\LaporanBendaharaController;
use App\Http\Controllers\skpd\SpjFungsionalController;
use App\Http\Controllers\Skpd\PelimpahanController;
use App\Http\Controllers\Skpd\SimpananBankController;
use App\Http\Controllers\Skpd\BpKasBankController;
use App\Http\Controllers\Skpd\BpKasTunaiController;
use App\Http\Controllers\Skpd\PelimpahanKegiatanController;
use App\Http\Controllers\Skpd\BpPajakController;
use App\Http\Controllers\Skpd\BpPanjarController;
use App\Http\Controllers\Skpd\RealisasiFisikController;
use App\Http\Controllers\Skpd\LaporanPenutupanKasBulananController;
use App\Http\Controllers\Skpd\LaporanDthController;
use App\Http\Controllers\Skpd\LaporanRegPajakController;
use App\Http\Controllers\Skpd\RegisterCpController;
use App\Http\Controllers\Skpd\SubRincianObjekController;
use App\Http\Controllers\Skpd\RegisterSppSpmSp2dController;
use App\Http\Controllers\Skpd\KartuKendaliSubkegiatanController;
use App\Http\Controllers\Skpd\PenerimaanLainController;
use App\Http\Controllers\Skpd\PengeluaranLainController;
use App\Http\Controllers\Skpd\SetorKasController;
use App\Http\Controllers\Skpd\SetorSisaController;
use App\Http\Controllers\PanjarPengembalianController;
use App\Http\Controllers\PanjarTambahController;
use App\Http\Controllers\Skpd\UyhdController;
use App\Http\Controllers\Skpd\UyhdPajakController;
use App\Http\Controllers\Skpd\Anggaran\RakController;
use App\Http\Controllers\Skpd\Pendapatan\PenetapanController;
use App\Http\Controllers\Skpd\TransaksiKKPDController;
use App\Http\Controllers\PenandatanganController;
use App\Http\Controllers\Skpd\Pendapatan\PenerimaanController;
//spd belanaja
use App\Http\Controllers\Spd\SPDBelanjaController;
use App\Http\Controllers\Spd\PembatalanSPDController;
use App\Http\Controllers\Spd\RegisterSPDController;
use App\Http\Controllers\Spd\KonfigurasiSPDController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Skpd\Pendapatan\PenyetoranController;
use App\Http\Controllers\Skpd\Transaksi\LPJController;
use App\Http\Controllers\skpd\LaporanBendaharaPenerimaanController;
use App\Http\Controllers\skpd\BukuPenerimaanPenyetoranController;
use App\Http\Controllers\skpd\SpjPendapatanController;
use App\Http\Controllers\Skpd\SppGuController;
use App\Http\Controllers\Skpd\SppGuNihilController;
use App\Http\Controllers\Skpd\BukuSetoranPenerimaanController;
use App\Http\Controllers\Akuntansi\LaporanAkuntansiController;
use App\Http\Controllers\Akuntansi\LraController;
use App\Http\Controllers\Skpd\BOS\PenerimaanBosController;
use App\Http\Controllers\Skpd\BOS\PengembalianBosController;
// use App\Http\Controllers\Skpd\BOS\Sp2bController;
use App\Http\Controllers\Sp2bController;
use App\Http\Controllers\SaldoKasController;
use App\Http\Controllers\Skpd\BOS\TransaksiBosController;
use App\Http\Controllers\Skpd\Panjar\PembayaranPanjarController;
use App\Http\Controllers\Skpd\Panjar\PengembalianPanjarController;
use App\Http\Controllers\Skpd\Panjar\PertanggungjawabanPanjarController;
use App\Http\Controllers\Skpd\Panjar\TambahPanjarController;
use App\Http\Controllers\Skpd\Panjar\TransaksiPanjarController;
use App\Http\Controllers\Skpd\PanjarCMS\PemberianPanjarController;
use App\Http\Controllers\Skpd\PanjarCMS\TambahPanjarCMSController;
use App\Http\Controllers\Skpd\PanjarCMS\UploadPanjarCMSController;
use App\Http\Controllers\Skpd\PanjarCMS\ValidasiPanjarCMSController;
use App\Http\Controllers\SppTU1Controller;
use App\Http\Controllers\Skpd\SppTuController;
use App\Http\Controllers\Skpd\TransaksiLaluController;
use App\Http\Controllers\Utility\KunciBelanjaController;
use App\Http\Controllers\Utility\KunciKasdaController;
use App\Http\Controllers\Utility\KunciPengeluaranController;
use App\Http\Controllers\PengumumanController;
//akuntansi
use App\Http\Controllers\Akuntansi\LraperdaController;
use App\Http\Controllers\Akuntansi\RekalController;
use App\Http\Controllers\Akuntansi\pengesahan_spj\PengesahanSPJController;
use App\Http\Controllers\KoreksiDataController;

// Route::get('/simakda_2023', function () {
//     return view('auth.login');
// });
Route::get('', [LoginController::class, 'index'])->name('login');
// Route::get('coba', [PenerimaController::class, 'coba'])->name('penerima.coba');

// Auth::routes();
Route::group(['middleware' => 'auth', 'auth.session'], function () {
    Route::group(['prefix' => 'kelola-akses'], function () {
        Route::resource('hak-akses', PermissionController::class);
        Route::post('data_hak_akses', [PermissionController::class, 'loadData'])->name('hak_akses.load_data');
        Route::resource('peran', RoleController::class);
        Route::post('data_peran', [RoleController::class, 'loadData'])->name('peran.load_data');
        Route::resource('user', UserController::class);
        Route::post('data_pengguna', [UserController::class, 'loadData'])->name('user.load_data');
        Route::resource('skpd_pengguna', SkpdPenggunaController::class);
        Route::post('data_skpd_pengguna', [SkpdPenggunaController::class, 'loadData'])->name('skpd_pengguna.load_data');
    });

    // index, create, store, update, show, destroy

    Route::group(['prefix' => 'master'], function () {
        Route::resource('penerima', PenerimaController::class);
        Route::get('penerima/show/{rekening?}/{kd_skpd?}/{npwp?}', [PenerimaController::class, 'showPenerima'])->name('penerima.show_penerima');
        Route::get('penerima/edit/{rekening?}/{kd_skpd?}/{npwp?}', [PenerimaController::class, 'editPenerima'])->name('penerima.edit_penerima');
        Route::put('penerima/update/{rekening?}/{kd_skpd?}/{npwp?}', [PenerimaController::class, 'updatePenerima'])->name('penerima.update_penerima');
        Route::post('load_penerima', [PenerimaController::class, 'loadData'])->name('penerima.load_data');
        Route::post('simpan_penerima', [PenerimaController::class, 'simpanPenerima'])->name('penerima.simpan_penerima');
        Route::post('simpan_edit_penerima', [PenerimaController::class, 'simpanEditPenerima'])->name('penerima.simpan_edit_penerima');
        Route::resource('kontrak', KontrakController::class);
        Route::post('hapus_kontrak', [KontrakController::class, 'hapus'])->name('kontrak.hapus');
        Route::post('load_kontrak', [KontrakController::class, 'loadData'])->name('kontrak.load_data');
        // pengumuman
        Route::resource('pengumuman', PengumumanController::class);
        Route::post('hapus_pengumuman', [PengumumanController::class, 'hapus'])->name('pengumuman.hapus');
        Route::post('load_pengumuman', [PengumumanController::class, 'loadData'])->name('pengumuman.load_data');
        // penandatangan
        Route::resource('tandatangan', PenandatanganController::class);
        Route::post('hapus_tandatangan', [PenandatanganController::class, 'hapus'])->name('tandatangan.hapus');
        Route::post('load_tandatangan', [PenandatanganController::class, 'loadData'])->name('tandatangan.load_data');
        Route::post('skpd_tandatangan', [PenandatanganController::class, 'cariSkpd'])->name('tandatangan.skpd');
        // setting
        Route::get('setting', [SettingController::class, 'edit'])->name('setting.edit');
        Route::patch('setting/update', [SettingController::class, 'update'])->name('setting.update');
        // profile SKPD
        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('profile/update', [ProfileController::class, 'update'])->name('profile.update');

        Route::post('cabang', [PenerimaController::class, 'cabang'])->name('penerima.cabang');
        Route::post('kode-setor', [PenerimaController::class, 'kode_setor'])->name('penerima.kodeSetor');
        Route::post('kode-seto-pot', [PenerimaController::class, 'kode_setor_pot'])->name('penerima.kodeSetorPot');
        Route::post('cek-rekening', [BankKalbarController::class, 'cek_rekening'])->name('penerima.cekRekening');
        Route::post('cek-npwp', [BankKalbarController::class, 'cek_npwp'])->name('penerima.cekNpwp');
        Route::post('cek-penerima', [PenerimaController::class, 'cekPenerima'])->name('penerima.cekPenerima');
    });

    Route::group(['prefix' => 'penatausahaan/pengeluaran'], function () {
        // Penagihan
        Route::group(['prefix' => 'penagihan'], function () {
            Route::get('', [PenagihanController::class, 'index'])->name('penagihan.index');
            Route::post('load_data', [PenagihanController::class, 'loadData'])->name('penagihan.load_data');
            Route::get('create', [PenagihanController::class, 'create'])->name('penagihan.create');
            Route::get('show/{no_bukti?}', [PenagihanController::class, 'show'])->where('no_bukti', '(.*)')->name('penagihan.show');
            Route::get('edit/{no_bukti?}', [PenagihanController::class, 'edit'])->where('no_bukti', '(.*)')->name('penagihan.edit');
            Route::post('hapus_penagihan', [PenagihanController::class, 'hapusPenagihan'])->name('penagihan.hapus_penagihan');
            Route::post('realisasi_sumber_dana', [PenagihanController::class, 'realisasiSumber'])->name('penagihan.realisasi_sumber_dana');
            Route::post('cek_status_ang_new', [PenagihanController::class, 'cekStatusAngNew'])->name('penagihan.cek_status_ang_new');
            Route::post('cek_status_ang', [PenagihanController::class, 'cekStatusAng'])->name('penagihan.cek_status_ang');
            Route::post('cari_rekening', [PenagihanController::class, 'cariRekening'])->name('penagihan.cari_rekening');
            Route::post('load_total_spd', [PenagihanController::class, 'totalSpd'])->name('penagihan.load_total_spd');
            Route::post('load_total_angkas', [PenagihanController::class, 'totalAngkas'])->name('penagihan.load_total_angkas');
            Route::post('cari_sumber_dana', [PenagihanController::class, 'cariSumberDana'])->name('penagihan.cari_sumber_dana');
            Route::post('cari_nama_sumber', [PenagihanController::class, 'cariNamaSumber'])->name('penagihan.cari_nama_sumber');
            Route::post('cari_nama_sumber2', [PenagihanController::class, 'cariNamaSumber2'])->name('penagihan.cari_nama_sumber2');
            Route::post('cari_total_kontrak', [PenagihanController::class, 'cariTotalKontrak'])->name('penagihan.cari_total_kontrak');
            Route::post('simpan_tampungan', [PenagihanController::class, 'simpanTampungan'])->name('penagihan.simpan_tampungan');
            Route::post('cek_nilai_kontrak', [PenagihanController::class, 'cekNilaiKontrak'])->name('penagihan.cek_nilai_kontrak');
            Route::post('cek_nilai_kontrak2', [PenagihanController::class, 'cekNilaiKontrak2'])->name('penagihan.cek_nilai_kontrak2');
            Route::post('cek_simpan_penagihan', [PenagihanController::class, 'cekSimpanPenagihan'])->name('penagihan.cek_simpan_penagihan');
            Route::post('simpan_penagihan', [PenagihanController::class, 'simpanPenagihan'])->name('penagihan.simpan_penagihan');
            Route::post('simpan_detail_penagihan', [PenagihanController::class, 'simpanDetailPenagihan'])->name('penagihan.simpan_detail_penagihan');
            Route::post('hapus_detail_tampungan_penagihan', [PenagihanController::class, 'hapusTampunganPenagihan'])->name('penagihan.hapus_detail_tampungan_penagihan');
            Route::post('hapus_semua_tampungan', [PenagihanController::class, 'hapusSemuaTampungan'])->name('penagihan.hapus_semua_tampungan');
            Route::post('hapus_detail_edit_penagihan', [PenagihanController::class, 'hapusDetailEditPenagihan'])->name(
                'penagihan.hapus_detail_edit_penagihan'
            );
            Route::post('update_penagihan', [PenagihanController::class, 'updatePenagihan'])->name('penagihan.update_penagihan');
            Route::post('update_detail_penagihan', [PenagihanController::class, 'updateDetailPenagihan'])->name('penagihan.update_detail_penagihan');
            Route::post('simpan_edit_tampungan', [PenagihanController::class, 'simpanEditTampungan'])->name('penagihan.simpan_edit_tampungan');
        });
        // SPP LS
        Route::group(['prefix' => 'spp_ls'], function () {
            Route::get('', [SppLsController::class, 'index'])->name('sppls.index');
            Route::post('load_data', [SppLsController::class, 'loadData'])->name('sppls.load_data');
            Route::get('create', [SppLsController::class, 'create'])->name('sppls.create');
            Route::post('cari_jenis', [SppLsController::class, 'cariJenis'])->name('sppls.cari_jenis');
            Route::post('cari_nomor_spd', [SppLsController::class, 'cariNomorSpd'])->name('sppls.cari_nomor_spd');
            Route::post('cari_sub_kegiatan', [SppLsController::class, 'cariSubKegiatan'])->name('sppls.cari_sub_kegiatan');
            Route::post('cari_rekening', [SppLsController::class, 'cariRekening'])->name('sppls.cari_rekening');
            Route::post('jumlah_anggaran_penyusunan', [SppLsController::class, 'jumlahAnggaranPenyusunan'])->name('sppls.jumlah_anggaran_penyusunan');
            Route::post('total_spd', [SppLsController::class, 'totalSpd'])->name('sppls.total_spd');
            Route::post('total_angkas', [SppLsController::class, 'totalAngkas'])->name('sppls.total_angkas');
            Route::post('realisasi_spd', [SppLsController::class, 'realisasiSpd'])->name('sppls.realisasi_spd');
            Route::post('sumber_dana', [SppLsController::class, 'sumberDana'])->name('sppls.sumber_dana');
            Route::post('cari_nospp', [SppLsController::class, 'cariNoSpp'])->name('sppls.cari_nospp');
            Route::post('cek_simpan', [SppLsController::class, 'cekSimpan'])->name('sppls.cek_simpan');
            Route::post('simpan_sppls', [SppLsController::class, 'simpanSppLs'])->name('sppls.simpan_sppls');
            Route::post('simpan_detail_sppls', [SppLsController::class, 'simpanDetailSppLs'])->name('sppls.simpan_detail_sppls');
            Route::get('tampil/{no_spp}', [SppLsController::class, 'tampilSppLs'])->where('no_spp', '(.*)')->name('sppls.show');
            Route::post('hapus_sppls', [SppLsController::class, 'hapusSppLs'])->name('sppls.hapus_sppls');
            Route::post('cari_penagihan_sppls', [SppLsController::class, 'cariPenagihanSpp'])->name('sppls.cari_penagihan_sppls');
            Route::get('edit/{no_spp}', [SppLsController::class, 'editSppLs'])->where('no_spp', '(.*)')->name('sppls.edit');
            Route::post('simpan_sppls_edit', [SppLsController::class, 'simpanEditSppLs'])->name('sppls.simpan_sppls_edit');
            Route::post('batal_sppls', [SppLsController::class, 'batalSppLs'])->name('sppls.batal_sppls');
            // Cetakan SPPLS
            Route::get('cetak_pengantar', [SppLsController::class, 'cetakPengantarLayar'])->name('sppls.cetak_pengantar_layar');
            Route::get('cetak_rincian', [SppLsController::class, 'cetakRincianLayar'])->name('sppls.cetak_rincian_layar');
            Route::get('cetak_permintaan', [SppLsController::class, 'cetakPermintaanLayar'])->name('sppls.cetak_permintaan_layar');
            Route::get('cetak_ringkasan', [SppLsController::class, 'cetakRingkasanLayar'])->name('sppls.cetak_ringkasan_layar');
            Route::get('cetak_pernyataan', [SppLsController::class, 'cetakPernyataanLayar'])->name('sppls.cetak_pernyataan_layar');
            Route::get('cetak_sptb', [SppLsController::class, 'cetakSptbLayar'])->name('sppls.cetak_sptb_layar');
            Route::get('cetak_spp77', [SppLsController::class, 'cetakSpp77Layar'])->name('sppls.cetak_spp77_layar');
            Route::get('cetak_rincian77', [SppLsController::class, 'cetakRincian77Layar'])->name('sppls.cetak_rincian77_layar');
        });

        // Verifikasi SPP
        Route::group(['prefix' => 'verifikasi_spp', 'as' => 'verifikasi_spp.'], function () {
            Route::get('', [VerifikasiSPPController::class, 'index'])->name('index');
            Route::get('create', [VerifikasiSPPController::class, 'create'])->name('create');
            Route::get('edit/{no_spp?}', [VerifikasiSPPController::class, 'edit'])->name('edit');
            Route::post('load_data', [VerifikasiSPPController::class, 'loadData'])->name('load_data');
            Route::get('load_kelengkapan_spm', [VerifikasiSPPController::class, 'loadKelengkapanSPM'])->name('load_kelengkapan_spm');
            Route::post('simpan', [VerifikasiSPPController::class, 'simpan'])->name('simpan');
            Route::post('update', [VerifikasiSPPController::class, 'update'])->name('update');
            Route::post('setuju', [VerifikasiSPPController::class, 'setuju'])->name('setuju');
            Route::post('kembali', [VerifikasiSPPController::class, 'kembali'])->name('kembali');
            Route::get('cetak_kelengkapan', [VerifikasiSPPController::class, 'cetakKelengkapan'])->name('cetak_kelengkapan');
            Route::get('pernyataan', [VerifikasiSPPController::class, 'pernyataan'])->name('pernyataan');
        });

        // SPM LAMA (1 billing 1 akun pajak)
        Route::group(['prefix' => 'spm'], function () {
            Route::get('', [SpmController::class, 'index'])->name('spm.index');
            Route::post('load_data', [SpmController::class, 'loadData'])->name('spm.load_data');
            // created by elvara
            Route::post('cek_kelengkapan', [SpmController::class, 'cekKelengkapan'])->name('spm.cek_kelengkapan');
            // Cetak Persyaratan SPM
            Route::get('persyaratan', [SpmController::class, 'cetakPersyaratan'])->name('spm.persyaratan');
            Route::get('create', [SpmController::class, 'create'])->name('spm.create');
            Route::post('cari_jenis', [SpmController::class, 'cariJenis'])->name('spm.cari_jenis');
            Route::post('cari_bank', [SpmController::class, 'cariBank'])->name('spm.cari_bank');
            Route::post('detail_spm', [SpmController::class, 'detailSpm'])->name('spm.detail_spm');
            Route::post('cari_nospd', [SpmController::class, 'cariNoSpd'])->name('spm.cari_nospd');
            Route::post('cari_nospm', [SpmController::class, 'cariNoSpm'])->name('spm.cari_nospm');
            Route::post('tgl_spm_lalu', [SpmController::class, 'tglSpmLalu'])->name('spm.tgl_spm_lalu');
            Route::post('simpan_spm', [SpmController::class, 'simpanSpm'])->name('spm.simpan_spm');
            Route::post('cari_rek_pot', [SpmController::class, 'cariRekPot'])->name('spm.cari_rek_pot');
            Route::post('tambah_list_potongan', [SpmController::class, 'tambahListPotongan'])->name('spm.tambah_list_potongan');
            Route::post('isi_list_pot', [BankKalbarController::class, 'isiListPot'])->name('spm.isi_list_pot');
            Route::post('isi_tampungan', [SpmController::class, 'isiTampungan'])->name('spm.isi_tampungan');
            Route::post('hapus_tampungan', [SpmController::class, 'hapusTampungan'])->name('spm.hapus_tampungan');
            Route::post('rekening_tampungan', [SpmController::class, 'rekeningTampungan'])->name('spm.rekening_tampungan');
            // kalau udah sp2d online ditukar
            Route::post('simpan_billing', [BankKalbarController::class, 'simpanBilling'])->name('spm.simpan_billing');
            // Route::post('simpan_billing', [BankKalbarController::class, 'simpanBilling'])->name('spm.simpan_billing');
            Route::post('create_id_billing', [BankKalbarController::class, 'createBilling'])->name('spm.create_id_billing');
            Route::post('create_report', [BankKalbarController::class, 'createReport'])->name('spm.create_report');
            Route::post('load_rincian', [SpmController::class, 'loadRincian'])->name('spm.load_rincian');
            Route::post('load_rincian_tampungan', [SpmController::class, 'loadRincianTampungan'])->name('spm.load_rincian_tampungan');
            Route::post('simpan_tampungan', [SpmController::class, 'simpanTampungan'])->name('spm.simpan_tampungan');
            Route::post('load_rincian_show', [SpmController::class, 'loadRincianShow'])->name('spm.load_rincian_show');
            Route::post('hapus_rincian_pajak', [SpmController::class, 'hapusRincianPajak'])->name('spm.hapus_rincian_pajak');
            Route::post('billing_cetak', [SpmController::class, 'billingCetak'])->name('spm.billing_cetak');
            Route::post('total_show', [SpmController::class, 'totalShow'])->name('spm.total_show');
            Route::get('tambah_potongan/{no_spm?}', [SpmController::class, 'tambahPotongan'])->where('no_spm', '(.*)')->name('spm.tambah_potongan');
            Route::get('tampil/{no_spm?}', [SpmController::class, 'tampilSpm'])->where('no_spm', '(.*)')->name('spm.tampil');
            // Cetakan
            Route::get('cetak_kelengkapan', [SpmController::class, 'cetakKelengkapan'])->name('spm.cetak_kelengkapan');
            Route::get('berkas_spm', [SpmController::class, 'cetakBerkas'])->name('spm.berkas_spm');
            Route::get('pengantar', [SpmController::class, 'cetakPengantar'])->name('spm.pengantar');
            Route::get('lampiran', [SpmController::class, 'cetakLampiran'])->name('spm.lampiran');
            Route::get('tanggung', [SpmController::class, 'cetakTanggung'])->name('spm.tanggung');
            Route::get('berkas_kelengkapan', [SpmController::class, 'cetakBerkasKelengkapan'])->name('spm.berkas_kelengkapan');
            Route::get('pilihSPM', [SpmController::class, 'pilihSPM'])->name('spm.pilih_spm');
            Route::get('kelengkapan_spm', [SpmController::class, 'cetakKelengkapanSpm'])->name('spm.kelengkapan_spm');
            Route::get('pernyataan', [SpmController::class, 'cetakPernyataan'])->name('spm.pernyataan');
            Route::get('ringkasan_up', [SpmController::class, 'cetakRingkasanUp'])->name('spm.ringkasan_up');
            Route::get('ringkasan_gu', [SpmController::class, 'cetakRingkasanGu'])->name('spm.ringkasan_gu');
            Route::get('ringkasan_tu', [SpmController::class, 'cetakRingkasanTu'])->name('spm.ringkasan_tu');
            Route::post('batal_spm', [SpmController::class, 'batalSpmSpp'])->name('spm.batal_spm');
        });

        // SPM BARU (1 billing bisa multi akun pajak)
        // Route::group(['prefix' => 'spm'], function () {
        //     Route::get('', [SpmController::class, 'index'])->name('spm.index');
        //     Route::post('load_data', [SpmController::class, 'loadData'])->name('spm.load_data');
        //     Route::get('create', [SpmController::class, 'create'])->name('spm.create');
        //     Route::post('cari_jenis', [SpmController::class, 'cariJenis'])->name('spm.cari_jenis');
        //     Route::post('cari_bank', [SpmController::class, 'cariBank'])->name('spm.cari_bank');
        //     Route::post('detail_spm', [SpmController::class, 'detailSpm'])->name('spm.detail_spm');
        //     Route::post('cari_nospd', [SpmController::class, 'cariNoSpd'])->name('spm.cari_nospd');
        //     Route::post('cari_nospm', [SpmController::class, 'cariNoSpm'])->name('spm.cari_nospm');
        //     Route::post('tgl_spm_lalu', [SpmController::class, 'tglSpmLalu'])->name('spm.tgl_spm_lalu');
        //     Route::post('simpan_spm', [SpmController::class, 'simpanSpm'])->name('spm.simpan_spm');
        //     Route::post('cari_rek_pot', [SpmController::class, 'cariRekPot'])->name('spm.cari_rek_pot');
        //     Route::post('tambah_list_potongan', [SpmController::class, 'tambahListPotongan'])->name('spm.tambah_list_potongan');
        //     Route::post('isi_list_pot', [BankKalbarController::class, 'isiListPot'])->name('spm.isi_list_pot');
        //     Route::post('isi_tampungan', [SpmController::class, 'isiTampungan'])->name('spm.isi_tampungan');
        //     Route::post('hapus_tampungan', [SpmController::class, 'hapusTampungan'])->name('spm.hapus_tampungan');
        //     Route::post('rekening_tampungan', [SpmController::class, 'rekeningTampungan'])->name('spm.rekening_tampungan');
        //     Route::post('simpan_billing', [BankKalbarController::class, 'simpanBilling'])->name('spm.simpan_billing');
        //     Route::post('create_id_billing', [BankKalbarController::class, 'createBilling'])->name('spm.create_id_billing');
        //     Route::post('create_report', [BankKalbarController::class, 'createReport'])->name('spm.create_report');
        //     Route::post('load_rincian', [SpmController::class, 'loadRincian'])->name('spm.load_rincian');
        //     Route::post('load_rincian_tampungan', [SpmController::class, 'loadRincianTampungan'])->name('spm.load_rincian_tampungan');
        //     Route::post('simpan_tampungan', [SpmController::class, 'simpanTampungan'])->name('spm.simpan_tampungan');
        //     Route::post('load_rincian_show', [SpmController::class, 'loadRincianShow'])->name('spm.load_rincian_show');
        //     Route::post('hapus_rincian_pajak', [SpmController::class, 'hapusRincianPajak'])->name('spm.hapus_rincian_pajak');
        //     Route::post('billing_cetak', [SpmController::class, 'billingCetak'])->name('spm.billing_cetak');
        //     Route::post('total_show', [SpmController::class, 'totalShow'])->name('spm.total_show');
        //     Route::get('tambah_potongan/{no_spm?}', [SpmController::class, 'tambahPotongan'])->where('no_spm', '(.*)')->name('spm.tambah_potongan');
        //     Route::get('tampil/{no_spm?}', [SpmController::class, 'tampilSpm'])->where('no_spm', '(.*)')->name('spm.tampil');
        //     // Cetakan
        //     Route::get('cetak_kelengkapan', [SpmController::class, 'cetakKelengkapan'])->name('spm.cetak_kelengkapan');
        //     Route::get('berkas_spm', [SpmController::class, 'cetakBerkas'])->name('spm.berkas_spm');
        //     Route::get('pengantar', [SpmController::class, 'cetakPengantar'])->name('spm.pengantar');
        //     Route::get('lampiran', [SpmController::class, 'cetakLampiran'])->name('spm.lampiran');
        //     Route::get('tanggung', [SpmController::class, 'cetakTanggung'])->name('spm.tanggung');
        //     Route::get('pernyataan', [SpmController::class, 'cetakPernyataan'])->name('spm.pernyataan');
        //     Route::get('ringkasan_up', [SpmController::class, 'cetakRingkasanUp'])->name('spm.ringkasan_up');
        //     Route::get('ringkasan_gu', [SpmController::class, 'cetakRingkasanGu'])->name('spm.ringkasan_gu');
        //     Route::get('ringkasan_tu', [SpmController::class, 'cetakRingkasanTu'])->name('spm.ringkasan_tu');
        //     Route::post('batal_spm', [SpmController::class, 'batalSpmSpp'])->name('spm.batal_spm');
        // });

        //verifikasi SPM
        Route::group(['prefix' => 'verifikasi_spm'], function () {
            Route::get('', [VerifikasiSpmController::class, 'index'])->name('verifikasi_spm.index');
            Route::post('load_data', [VerifikasiSpmController::class, 'loadData'])->name('verifikasi_spm.load_data');
            Route::post('cari_skpd', [VerifikasiSpmController::class, 'cariSkpd'])->name('verifikasi_spm.cari_skpd');
            Route::post('cari_spm', [VerifikasiSpmController::class, 'cariSpm'])->name('verifikasi_spm.cari_spm');
            Route::post('detail_spm', [VerifikasispmController::class, 'detailSpm'])->name('verifikasi_spm.detail_spm');
            Route::get('cek_list', [VerifikasispmController::class, 'cekListSpm'])->name('verifikasi_spm.cek_list');
            Route::post('setuju_spm', [VerifikasispmController::class, 'setujuSpm'])->name('verifikasi_spm.setuju_spm');
            Route::post('update_cek', [VerifikasiSPMController::class, 'updateCek'])->name('verifikasi_spm.update_cek');
        });
        // SP2D
        Route::group(['prefix' => 'sp2d'], function () {
            Route::get('', [Sp2dController::class, 'index'])->name('sp2d.index');
            Route::post('load_data', [Sp2dController::class, 'loadData'])->name('sp2d.load_data');
            Route::get('tambah', [Sp2dController::class, 'create'])->name('sp2d.create');
            Route::post('cari_spm', [Sp2dController::class, 'cariSpm'])->name('sp2d.cari_spm');
            Route::post('cari_jenis', [Sp2dController::class, 'cariJenis'])->name('sp2d.cari_jenis');
            Route::post('cari_bulan', [Sp2dController::class, 'cariBulan'])->name('sp2d.cari_bulan');
            Route::post('load_rincian_spm', [Sp2dController::class, 'loadRincianSpm'])->name('sp2d.load_rincian_spm');
            Route::post('load_rincian_potongan', [Sp2dController::class, 'loadRincianPotongan'])->name('sp2d.load_rincian_potongan');
            Route::post('cari_total', [Sp2dController::class, 'cariTotal'])->name('sp2d.cari_total');
            Route::post('cari_nomor', [Sp2dController::class, 'cariNomor'])->name('sp2d.cari_nomor');
            Route::post('simpan_sp2d', [Sp2dController::class, 'simpanSp2d'])->name('sp2d.simpan_sp2d');
            Route::post('batal_sp2d', [Sp2dController::class, 'batalSp2d'])->name('sp2d.batal_sp2d');
            Route::get('tampil/{no_sp2d?}', [Sp2dController::class, 'tampilSp2d'])->where('no_sp2d', '(.*)')->name('sp2d.tampil');
            // cetakan sp2d
            Route::get('cetak_sp2d', [Sp2dController::class, 'cetakSp2d'])->name('sp2d.cetak_sp2d');
            Route::get('cetak_lampiran', [Sp2dController::class, 'cetakLampiran'])->name('sp2d.cetak_lampiran');
            Route::get('cetak_lampiran_lama', [Sp2dController::class, 'cetakLampiranLama'])->name('sp2d.cetak_lampiran_lama');
            Route::get('cetak_kelengkapan', [Sp2dController::class, 'cetakKelengkapan'])->name('sp2d.cetak_kelengkapan');
        });
        // Daftar Penguji
        Route::group(['prefix' => 'daftar_penguji'], function () {
            Route::get('', [DaftarPengujiController::class, 'index'])->name('daftar_penguji.index');
            Route::post('load_data', [DaftarPengujiController::class, 'loadData'])->name('daftar_penguji.load_data');
            Route::get('tambah', [DaftarPengujiController::class, 'create'])->name('daftar_penguji.create');
            Route::post('simpan_penguji', [DaftarPengujiController::class, 'simpanPenguji'])->name('daftar_penguji.simpan_penguji');
            Route::post('hapus_detail_penguji', [DaftarPengujiController::class, 'hapusDetailPenguji'])->name('daftar_penguji.hapus_detail_penguji');
            Route::post('load_detail', [DaftarPengujiController::class, 'detailPenguji'])->name('daftar_penguji.load_detail');
            Route::post('simpan_detail_penguji', [DaftarPengujiController::class, 'simpanDetailPenguji'])->name('daftar_penguji.simpan_detail_penguji');
            Route::get('tampil/{no_uji?}', [DaftarPengujiController::class, 'editPenguji'])->where('no_uji', '(.*)')->name('daftar_penguji.tampil');
            Route::post('load_rincian_penguji', [DaftarPengujiController::class, 'loadRincianPenguji'])->name('daftar_penguji.load_rincian_penguji');
            Route::post('hapus_rincian_penguji', [DaftarPengujiController::class, 'hapusRincianPenguji'])->name('daftar_penguji.hapus_rincian_penguji');
            Route::post('load_sp2d', [DaftarPengujiController::class, 'loadSp2d'])->name('daftar_penguji.load_sp2d');
            Route::post('tambah_rincian', [DaftarPengujiController::class, 'tambahRincian'])->name('daftar_penguji.tambah_rincian');
            Route::post('simpan_edit_penguji', [DaftarPengujiController::class, 'simpanEditPenguji'])->name('daftar_penguji.simpan_edit_penguji');
            Route::post('hapus_penguji', [DaftarPengujiController::class, 'hapusPenguji'])->name('daftar_penguji.hapus_penguji');
            Route::post('status_bank', [DaftarPengujiController::class, 'statusBank'])->name('daftar_penguji.status_bank');

            // cetakan
            Route::get('cetak_penguji', [DaftarPengujiController::class, 'cetakPenguji'])->name('daftar_penguji.cetak_penguji');
        });
        // Pencairan SP2D
        Route::group(['prefix' => 'pencairan_sp2d'], function () {
            Route::get('', [PencairanSp2dController::class, 'index'])->name('pencairan_sp2d.index');
            Route::post('load_data', [PencairanSp2dController::class, 'loadData'])->name('pencairan_sp2d.load_data');
            Route::get('tampil/{no_sp2d?}', [PencairanSp2dController::class, 'tampilCair'])->where('no_sp2d', '(.*)')->name('pencairan_sp2d.tampil');
            Route::post('load_rincian_spm', [PencairanSp2dController::class, 'loadRincianSpm'])->name('pencairan_sp2d.load_rincian_spm');
            Route::post('load_rincian_potongan', [PencairanSp2dController::class, 'loadRincianPotongan'])->name('pencairan_sp2d.load_rincian_potongan');
            Route::post('cek_simpan', [PencairanSp2dController::class, 'cekSimpan'])->name('pencairan_sp2d.cek_simpan');
            Route::post('simpan_cair', [PencairanSp2dController::class, 'simpanCair'])->name('pencairan_sp2d.simpan_cair');
            Route::post('batal_cair', [PencairanSp2dController::class, 'batalCair'])->name('pencairan_sp2d.batal_cair');
        });
        //Verifikasi SP2D
        Route::group(['prefix' => 'verif_sp2d'], function () {
            Route::get('', [VerifSp2dController::class, 'index'])->name('verif_sp2d.index');
            Route::post('load_data', [VerifSp2dController::class, 'loadData'])->name('verif_sp2d.load_data');
            Route::post('load_data_verif', [VerifSp2dController::class, 'loadDataVerif'])->name('verif_sp2d.load_data_verif');
            Route::post('load_data_salur', [VerifSp2dController::class, 'loadDataSalur'])->name('verif_sp2d.load_data_salur');
            Route::get('tampil_sp2d/{no_sp2d?}', [VerifSp2dController::class, 'tampilSp2d'])->where('no_sp2d', '(.*)')->name('verif_sp2d.tampil_sp2d');
            Route::post('verif_sp2d', [VerifSp2dController::class, 'verifSp2d'])->name('verif_sp2d.verif_sp2d');
            Route::post('batal_verif', [VerifSp2dController::class, 'batalVerif'])->name('verif_sp2d.batal_verif');
        });
        // SPP UP
        Route::group(['prefix' => 'spp_up'], function () {
            Route::get('', [SppUpController::class, 'index'])->name('sppup.index');
            Route::post('load_data', [SppUpController::class, 'loadData'])->name('sppup.load_data');
            Route::get('tambah', [SppUpController::class, 'create'])->name('sppup.create');
            Route::get('edit/{no_spp?}', [SppUpController::class, 'edit'])->where('no_spp', '(.*)')->name('sppup.edit');
            Route::post('simpan_spp', [SppUpController::class, 'simpanSpp'])->name('sppup.simpan_spp');
            Route::post('cari_nospp_up', [SppUpController::class, 'cariNoSppUp'])->name('sppls.cari_nospp_up');
            Route::post('simpan_detail_spp', [SppUpController::class, 'simpanDetailSpp'])->name('sppup.simpan_detail_spp');
            Route::post('edit_spp', [SppUpController::class, 'editSpp'])->name('sppup.edit_spp');
            Route::post('hapus', [SppUpController::class, 'hapus'])->name('sppup.hapus');
            Route::get('pengantar_up', [SppUpController::class, 'pengantarUp'])->name('sppup.pengantar_up');
            Route::get('ringkasan_up', [SppUpController::class, 'ringkasanUp'])->name('sppup.ringkasan_up');
            Route::get('rincian_up', [SppUpController::class, 'rincianUp'])->name('sppup.rincian_up');
            Route::get('pernyataan_up', [SppUpController::class, 'pernyataanUp'])->name('sppup.pernyataan_up');
            Route::get('spp_up', [SppUpController::class, 'sppUp'])->name('sppup.spp_up');
            Route::get('rincian77_up', [SppUpController::class, 'rincian77Up'])->name('sppup.rincian77_up');
            Route::post('batal_spp', [SppUpController::class, 'batalSpp'])->name('sppup.batal_spp');
        });
        // Pengesahan Angkas
        Route::group(['prefix' => 'pengesahan_angkas'], function () {
            Route::get('', [PengesahanAngkasController::class, 'index'])->name('pengesahan_angkas.index');
            Route::post('load_data', [PengesahanAngkasController::class, 'loadData'])->name('pengesahan_angkas.load_data');
            Route::post('simpan', [PengesahanAngkasController::class, 'simpan'])->name('pengesahan_angkas.simpan');
        });

        Route::group(['prefix' => 'spd'], function () {
            // spd belanja
            Route::group(['prefix' => 'spd_belanja'], function () {
                Route::get('', [SPDBelanjaController::class, 'index'])->name('spd_belanja.index');
                Route::post('load_data', [SPDBelanjaController::class, 'loadData'])->name('spd.spd_belanja.load_data');
                Route::post('cek_status_angkas', [SPDBelanjaController::class, 'cekStatusAngkas'])->name('spd.spd_belanja.cek_status_angkas');
                Route::post('cek_status_anggaran', [SPDBelanjaController::class, 'cekStatusAnggaran'])->name('spd.spd_belanja.cek_status_anggaran');
                Route::post('daftar-skpd', [SPDBelanjaController::class, 'getSKPD'])->name('spd.spd_belanja.skpd');
                Route::post('daftar-nip-skpd', [SPDBelanjaController::class, 'getNipSKPD'])->name('spd.spd_belanja.nip');
                Route::post('daftar-jenis-anggaran', [SPDBelanjaController::class, 'getJenisAng'])->name('spd.spd_belanja.jns_ang');
                Route::post('daftar-status-angkas', [SPDBelanjaController::class, 'getStatusAng'])->name('spd.spd_belanja.status_angkas');
                Route::post('daftar-spd-belanja', [SPDBelanjaController::class, 'getSpdBelanja'])->name('spd.spd_belanja.load_spd_belanja');
                Route::post('insert-spd-belanja', [SPDBelanjaController::class, 'getInsertSpd'])->name('spd.spd_belanja.insert_spd');
                Route::post('delete-spd-belanja-temp', [SPDBelanjaController::class, 'getDeleteSpdTemp'])->name('spd.spd_belanja.delete_spd_temp');
                Route::post('insert-all-spd-belanja', [SPDBelanjaController::class, 'getInsertAllSpdTemp'])->name('spd.spd_belanja.insert_all_spd');
                Route::post('delete-all-spd-belanja', [SPDBelanjaController::class, 'getDeleteAllSpdTemp'])->name('spd.spd_belanja.delete_all_spd');
                Route::post('daftar-spd-belanja-temp', [SPDBelanjaController::class, 'getSpdBelanjaTemp'])->name('spd.spd_belanja.load_spd_belanja_temp');
                Route::get('tambah', [SPDBelanjaController::class, 'create'])->name('spd.spd_belanja.create');
                Route::post('simpan-spp', [SPDBelanjaController::class, 'simpanSPP'])->name('spd.spd_belanja.simpanSpp');
                Route::get('cetak-otorisasi', [SPDBelanjaController::class, 'cetakOto'])->name('spd.spd_belanja.cetak_otorisasi');
                Route::get('cetak-lampiran', [SPDBelanjaController::class, 'cetakLamp'])->name('spd.spd_belanja.cetak_lampiran');
                Route::post('/hapus_data_spd', [SPDBelanjaController::class, 'destroy'])->name('spd.spd_belanja.hapus_data_spd');
                Route::get('tampil/{no_spd}', [SPDBelanjaController::class, 'tampilspdBP'])->where('no_spd', '(.*)')->name('spdBP.show');
                Route::post('show_load_data', [SPDBelanjaController::class, 'ShowloadData'])->name('spd.spd_belanja.show_load_data');
                Route::post('cek_skpd', [SPDBelanjaController::class, 'cekSkpd'])->name('spd.spd_belanja.cek_skpd');
                Route::post('cek_angkas', [SPDBelanjaController::class, 'cekAngkas'])->name('spd.spd_belanja.cek_angkas');
            });

            // register spd
            Route::group(['prefix' => 'register_spd'], function () {
                Route::get('', [RegisterSPDController::class, 'index'])->name('register_spd.index');
                Route::post('daftar-skpd', [RegisterSPDController::class, 'getSKPD'])->name('spd.register_spd.skpd');
                Route::post('daftar-nip-skpd', [RegisterSPDController::class, 'getNipSKPD'])->name('spd.register_spd.nip');
                Route::get('cetak-unit-register-spd', [RegisterSPDController::class, 'CetakURS'])->name('spd.register_spd.cetak_urs');
                Route::get('cetak-skpd-register-spd', [RegisterSPDController::class, 'CetakSRS'])->name('spd.register_spd.cetak_srs');
                Route::get('cetak-kseluruhan-register-spd', [RegisterSPDController::class, 'CetakKRS'])->name('spd.register_spd.cetak_krs');
                Route::get('cetak-kseluruhan-revisi-register-spd', [RegisterSPDController::class, 'CetakKRRS'])->name('spd.register_spd.cetak_krrs');
            });

            //konfigurasi spd
            Route::group(['prefix' => 'konfigurasi_spd'], function () {
                Route::get('', [KonfigurasiSPDController::class, 'index'])->name('konfigurasi_spd.index');
                Route::patch('update', [KonfigurasiSPDController::class, 'update'])->name('spd.konfigurasi_spd.update');
            });
            //pembatalan spd
            Route::group(['prefix' => 'pembatalan_spd'], function () {
                Route::get('', [PembatalanSPDController::class, 'index'])->name('pembatalan_spd.index');
                Route::get('load_data', [PembatalanSPDController::class, 'loadData'])->name('spd.pembatalan_spd.load_data');
                Route::patch('update-status', [PembatalanSPDController::class, 'updateStatus'])->name('spd.spd_belanja.update_status');
            });
        });
    });

    Route::group(['prefix' => 'skpd'], function () {
        // Terima SP2D
        Route::group(['prefix' => 'terima_sp2d'], function () {
            Route::get('', [TerimaSp2dController::class, 'index'])->name('terima_sp2d.index');
            Route::post('load_data', [TerimaSp2dController::class, 'loadData'])->name('terima_sp2d.load_data');
            Route::get('tampil_sp2d/{no_sp2d?}', [TerimaSp2dController::class, 'tampilSp2d'])->where('no_sp2d', '(.*)')->name('terima_sp2d.tampil_sp2d');
            Route::post('terima_sp2d', [TerimaSp2dController::class, 'terimaSp2d'])->name('terima_sp2d.terima_sp2d');
            Route::post('batal_terima', [TerimaSp2dController::class, 'batalTerima'])->name('terima_sp2d.batal_terima');
            Route::get('cek_terima', [TerimaSp2dController::class, 'cekTerima'])->name('terima_sp2d.cek_terima');
        });
        // Pencairan SP2D
        Route::group(['prefix' => 'pencairan_sp2d'], function () {
            Route::get('', [CairSp2dController::class, 'index'])->name('skpd.pencairan_sp2d.index');
            Route::post('load_data', [CairSp2dController::class, 'loadData'])->name('skpd.pencairan_sp2d.load_data');
            Route::get('tampil_sp2d/{no_sp2d?}', [CairSp2dController::class, 'tampilSp2d'])->where('no_sp2d', '(.*)')->name('skpd.pencairan_sp2d.tampil_sp2d');
            Route::post('batal_cair', [CairSp2dController::class, 'batalCair'])->where('no_sp2d', '(.*)')->name('skpd.pencairan_sp2d.batal_cair');
            Route::post('simpan_cair', [CairSp2dController::class, 'simpanCair'])->where('no_sp2d', '(.*)')->name('skpd.pencairan_sp2d.simpan_cair');
            Route::get('cek_cair', [CairSp2dController::class, 'cekCair'])->name('skpd.pencairan_sp2d.cek_cair');
        });
        // Transaksi CMS
        Route::group(['prefix' => 'transaksi_cms'], function () {
            Route::get('', [TransaksiCmsController::class, 'index'])->name('skpd.transaksi_cms.index');
            Route::post('load_data', [TransaksiCmsController::class, 'loadData'])->name('skpd.transaksi_cms.load_data');
            Route::get('tambah', [TransaksiCmsController::class, 'create'])->name('skpd.transaksi_cms.create');
            Route::post('no_urut', [TransaksiCmsController::class, 'no_urut'])->name('skpd.transaksi_cms.no_urut');
            Route::post('skpd', [TransaksiCmsController::class, 'skpd'])->name('skpd.transaksi_cms.skpd');
            Route::post('cariKegiatan', [TransaksiCmsController::class, 'cariKegiatan'])->name('skpd.transaksi_cms.kegiatan');
            Route::post('cariSp2d', [TransaksiCmsController::class, 'cariSp2d'])->name('skpd.transaksi_cms.nomor_sp2d');
            Route::post('cariRekening', [TransaksiCmsController::class, 'cariRekening'])->name('skpd.transaksi_cms.rekening');
            Route::post('cariSumber', [TransaksiCmsController::class, 'cariSumber'])->name('skpd.transaksi_cms.sumber');
            Route::post('sisaBank', [TransaksiCmsController::class, 'sisaBank'])->name('skpd.transaksi_cms.sisa_bank');
            Route::post('potonganLs', [TransaksiCmsController::class, 'potonganLs'])->name('skpd.transaksi_cms.potongan_ls');
            Route::post('loadDana', [TransaksiCmsController::class, 'loadDana'])->name('skpd.transaksi_cms.load_dana');
            Route::post('statusAng', [TransaksiCmsController::class, 'statusAng'])->name('skpd.transaksi_cms.status_ang');
            Route::post('loadAngkas', [TransaksiCmsController::class, 'loadAngkas'])->name('skpd.transaksi_cms.load_angkas');
            Route::post('loadAngkasLalu', [TransaksiCmsController::class, 'loadAngkasLalu'])->name('skpd.transaksi_cms.load_angkas_lalu');
            Route::post('loadSpd', [TransaksiCmsController::class, 'loadSpd'])->name('skpd.transaksi_cms.load_spd');
            Route::post('cekSimpan', [TransaksiCmsController::class, 'cekSimpan'])->name('skpd.transaksi_cms.cek_simpan');
            Route::post('simpanCms', [TransaksiCmsController::class, 'simpanCms'])->name('skpd.transaksi_cms.simpan_cms');
            Route::post('simpanDetailCms', [TransaksiCmsController::class, 'simpanDetailCms'])->name('skpd.transaksi_cms.simpan_detail_cms');
            // EDIT
            Route::get('edit/{no_voucher?}', [TransaksiCmsController::class, 'edit'])->where('no_voucher', '(.*)')->name('skpd.transaksi_cms.edit');
            // HAPUS
            Route::post('hapusCms', [TransaksiCmsController::class, 'hapusCms'])->name('skpd.transaksi_cms.hapus_cms');
            // Cetak List
            Route::get('cetak_list', [TransaksiCmsController::class, 'cetakList'])->name('skpd.transaksi_cms.cetak_list');
        });
        // UPLOAD TRANSAKSI CMS
        Route::group(['prefix' => 'upload_cms'], function () {
            Route::get('', [UploadCmsController::class, 'index'])->name('skpd.upload_cms.index');
            Route::post('load_upload', [UploadCmsController::class, 'loadUpload'])->name('skpd.upload_cms.load_data');
            Route::post('draft_upload', [UploadCmsController::class, 'draftUpload'])->name('skpd.upload_cms.draft_upload');
            Route::post('data_upload', [UploadCmsController::class, 'dataUpload'])->name('skpd.upload_cms.data_upload');
            Route::get('tambah', [UploadCmsController::class, 'create'])->name('skpd.upload_cms.create');
            Route::post('load_transaksi', [UploadCmsController::class, 'loadTransaksi'])->name('skpd.upload_cms.load_transaksi');
            Route::post('proses_upload', [UploadCmsController::class, 'prosesUpload'])->name('skpd.upload_cms.proses_upload');
            Route::post('batal_upload', [UploadCmsController::class, 'batalUpload'])->name('skpd.upload_cms.batal_upload');
            Route::get('cetak_csv_kalbar', [UploadCmsController::class, 'cetakCsvKalbar'])->name('skpd.upload_cms.cetak_csv_kalbar');
            Route::get('cetak_csv_luar_kalbar', [UploadCmsController::class, 'cetakCsvLuarKalbar'])->name('skpd.upload_cms.cetak_csv_luar_kalbar');
            Route::post('rekening_transaksi', [UploadCmsController::class, 'rekeningTransaksi'])->name('skpd.upload_cms.rekening_transaksi');
            Route::post('rekening_potongan', [UploadCmsController::class, 'rekeningPotongan'])->name('skpd.upload_cms.rekening_potongan');
            Route::post('rekening_tujuan', [UploadCmsController::class, 'rekeningTujuan'])->name('skpd.upload_cms.rekening_tujuan');
        });
        // VALIDASI TRANSAKSI CMS
        Route::group(['prefix' => 'validasi_cms'], function () {
            Route::get('', [ValidasiCmsController::class, 'index'])->name('skpd.validasi_cms.index');
            Route::post('load_data', [ValidasiCmsController::class, 'loadData'])->name('skpd.validasi_cms.load_data');
            Route::post('draft_validasi', [ValidasiCmsController::class, 'draftValidasi'])->name('skpd.validasi_cms.draft_validasi');
            Route::post('data_upload', [ValidasiCmsController::class, 'dataUpload'])->name('skpd.validasi_cms.data_upload');
            Route::get('tambah', [ValidasiCmsController::class, 'create'])->name('skpd.validasi_cms.create');
            Route::post('load_transaksi', [ValidasiCmsController::class, 'loadTransaksi'])->name('skpd.validasi_cms.load_transaksi');
            Route::post('proses_validasi', [ValidasiCmsController::class, 'prosesValidasi'])->name('skpd.validasi_cms.proses_validasi');
            Route::post('batal_validasi', [ValidasiCmsController::class, 'batalValidasi'])->name('skpd.validasi_cms.batal_validasi');
        });
        // Terima Potongan Pajak CMS
        Route::group(['prefix' => 'potongan_pajak_cms'], function () {
            Route::get('', [PotonganPajakCmsController::class, 'index'])->name('skpd.potongan_pajak_cms.index');
            Route::post('load_data', [PotonganPajakCmsController::class, 'loadData'])->name('skpd.potongan_pajak_cms.load_data');
            Route::get('tambah', [PotonganPajakCmsController::class, 'create'])->name('skpd.potongan_pajak_cms.create');
            Route::get('edit/{no_bukti?}', [PotonganPajakCmsController::class, 'edit'])->where('no_bukti', '(.*)')->name('skpd.potongan_pajak_cms.edit');
            Route::post('cari_kegiatan', [PotonganPajakCmsController::class, 'cariKegiatan'])->name('skpd.potongan_pajak_cms.cari_kegiatan');
            Route::post('simpan_potongan', [PotonganPajakCmsController::class, 'simpanPotongan'])->name('skpd.potongan_pajak_cms.simpan_potongan');
            Route::post('edit_potongan', [PotonganPajakCmsController::class, 'editPotongan'])->name('skpd.potongan_pajak_cms.edit_potongan');
            Route::post('hapus_potongan', [PotonganPajakCmsController::class, 'hapusPotongan'])->name('skpd.potongan_pajak_cms.hapus_potongan');
        });
        // Transaksi PemindahBukuan
        Route::group(['prefix' => 'transaksi_pemindahbukuan'], function () {
            Route::get('', [TransaksiPemindahbukuanController::class, 'index'])->name('skpd.transaksi_pemindahbukuan.index');
            Route::post('load_data', [TransaksiPemindahbukuanController::class, 'loadData'])->name('skpd.transaksi_pemindahbukuan.load_data');
            Route::get('tambah', [TransaksiPemindahbukuanController::class, 'create'])->name('skpd.transaksi_pemindahbukuan.create');
            Route::post('simpan_transaksi', [TransaksiPemindahbukuanController::class, 'simpanTransaksi'])->name('skpd.transaksi_pemindahbukuan.simpan_transaksi');
            Route::post('hapus_transaksi', [TransaksiPemindahbukuanController::class, 'hapusTransaksi'])->name('skpd.transaksi_pemindahbukuan.hapus_transaksi');
            Route::get('edit/{no_bukti?}', [TransaksiPemindahbukuanController::class, 'edit'])->where('no_bukti', '(.*)')->name('skpd.transaksi_pemindahbukuan.edit');
            Route::post('edit_transaksi', [TransaksiPemindahbukuanController::class, 'editTransaksi'])->name('skpd.transaksi_pemindahbukuan.edit_transaksi');
        });
        // Transaksi Tunai
        Route::group(['prefix' => 'transaksi_tunai'], function () {
            Route::get('', [TransaksiTunaiController::class, 'index'])->name('skpd.transaksi_tunai.index');
            Route::post('load_data', [TransaksiTunaiController::class, 'loadData'])->name('skpd.transaksi_tunai.load_data');
            Route::get('tambah', [TransaksiTunaiController::class, 'create'])->name('skpd.transaksi_tunai.create');
            Route::post('nomor_sp2d', [TransaksiTunaiController::class, 'nomorSp2d'])->name('skpd.transaksi_tunai.nomor_sp2d');
            Route::post('cari_rekening', [TransaksiTunaiController::class, 'cariRekening'])->name('skpd.transaksi_tunai.cari_rekening');
            Route::post('cari_sumber', [TransaksiTunaiController::class, 'cariSumber'])->name('skpd.transaksi_tunai.cari_sumber');
            Route::post('sisa_tunai', [TransaksiTunaiController::class, 'sisaTunai'])->name('skpd.transaksi_tunai.sisa_tunai');
            Route::post('cek_simpan', [TransaksiTunaiController::class, 'cekSimpan'])->name('skpd.transaksi_tunai.cek_simpan');
            Route::post('simpan_transaksi', [TransaksiTunaiController::class, 'simpanTransaksi'])->name('skpd.transaksi_tunai.simpan_transaksi');
            Route::post('hapus_transaksi', [TransaksiTunaiController::class, 'hapusTransaksi'])->name('skpd.transaksi_tunai.hapus_transaksi');
            Route::get('edit/{no_bukti?}', [TransaksiTunaiController::class, 'edit'])->where('no_bukti', '(.*)')->name('skpd.transaksi_tunai.edit');
            Route::post('edit_transaksi', [TransaksiTunaiController::class, 'editTransaksi'])->name('skpd.transaksi_tunai.edit_transaksi');
        });
        // Terima Potongan Pajak
        Route::group(['prefix' => 'potongan_pajak'], function () {
            Route::get('', [PotonganPajakController::class, 'index'])->name('skpd.potongan_pajak.index');
            Route::post('load_data', [PotonganPajakController::class, 'loadData'])->name('skpd.potongan_pajak.load_data');
            Route::get('tambah', [PotonganPajakController::class, 'create'])->name('skpd.potongan_pajak.create');
            Route::get('edit/{no_bukti?}', [PotonganPajakController::class, 'edit'])->where('no_bukti', '(.*)')->name('skpd.potongan_pajak.edit');
            Route::post('cari_kegiatan', [PotonganPajakController::class, 'cariKegiatan'])->name('skpd.potongan_pajak.cari_kegiatan');
            Route::post('cari_rekening', [PotonganPajakController::class, 'cariRekening'])->name('skpd.potongan_pajak.cari_rekening');
            Route::post('simpan_potongan', [PotonganPajakController::class, 'simpanPotongan'])->name('skpd.potongan_pajak.simpan_potongan');
            Route::post('edit_potongan', [PotonganPajakController::class, 'editPotongan'])->name('skpd.potongan_pajak.edit_potongan');
            Route::post('hapus_potongan', [PotonganPajakController::class, 'hapusPotongan'])->name('skpd.potongan_pajak.hapus_potongan');
        });
        // Setor Potongan Pajak
        Route::group(['prefix' => 'setor_potongan'], function () {
            Route::get('', [SetorPotonganController::class, 'index'])->name('skpd.setor_potongan.index');
            Route::post('load_data', [SetorPotonganController::class, 'loadData'])->name('skpd.setor_potongan.load_data');
            Route::post('list_potongan', [SetorPotonganController::class, 'loadPotongan'])->name('skpd.setor_potongan.list_potongan');
            Route::post('total_potongan', [SetorPotonganController::class, 'totalPotongan'])->name('skpd.setor_potongan.total_potongan');
            Route::post('cek_billing', [BankKalbarController::class, 'cekBilling'])->name('skpd.setor_potongan.cek_billing');
            Route::post('simpan_ntpn', [SetorPotonganController::class, 'simpanNtpn'])->name('skpd.setor_potongan.simpan_ntpn');
            Route::post('edit_ntpn', [SetorPotonganController::class, 'editNtpn'])->name('skpd.setor_potongan.edit_ntpn');
            Route::get('tambah', [SetorPotonganController::class, 'create'])->name('skpd.setor_potongan.create');
            Route::post('simpan_potongan', [SetorPotonganController::class, 'simpanPotongan'])->name('skpd.setor_potongan.simpan_potongan');
            Route::get('edit/{no_bukti?}', [SetorPotonganController::class, 'edit'])->where('no_bukti', '(.*)')->name('skpd.setor_potongan.edit');
            Route::post('edit_potongan', [SetorPotonganController::class, 'editPotongan'])->name('skpd.setor_potongan.edit_potongan');
            Route::post('hapus_potongan', [SetorPotonganController::class, 'hapusPotongan'])->name('skpd.setor_potongan.hapus_potongan');
        });
        // Pelimpahan
        Route::group(['prefix' => 'pelimpahan'], function () {
            // Pelimpahan UP
            Route::get('up', [PelimpahanController::class, 'indexUp'])->name('skpd.pelimpahan.up_index');
            Route::post('load_data_up', [PelimpahanController::class, 'loadDataUp'])->name('skpd.pelimpahan.load_data_up');
            Route::get('tambah_up', [PelimpahanController::class, 'tambahUp'])->name('skpd.pelimpahan.tambah_up');
            Route::post('simpan_up', [PelimpahanController::class, 'simpanUp'])->name('skpd.pelimpahan.simpan_up');
            Route::get('edit_up/{no_kas?}', [PelimpahanController::class, 'editUp'])->where('no_kas', '(.*)')->name('skpd.pelimpahan.edit_up');
            Route::post('update_up', [PelimpahanController::class, 'updateUp'])->name('skpd.pelimpahan.update_up');
            Route::post('hapus_up', [PelimpahanController::class, 'hapusUp'])->name('skpd.pelimpahan.hapus_up');
            // Pelimpahan GU
            Route::get('gu', [PelimpahanController::class, 'indexGu'])->name('skpd.pelimpahan.gu_index');
            Route::post('load_data_gu', [PelimpahanController::class, 'loadDataGu'])->name('skpd.pelimpahan.load_data_gu');
            Route::get('tambah_gu', [PelimpahanController::class, 'tambahGu'])->name('skpd.pelimpahan.tambah_gu');
            Route::post('no_lpj', [PelimpahanController::class, 'noLpj'])->name('skpd.pelimpahan.no_lpj');
            Route::post('simpan_gu', [PelimpahanController::class, 'simpanGu'])->name('skpd.pelimpahan.simpan_gu');
            Route::get('edit_gu/{no_kas?}', [PelimpahanController::class, 'editGu'])->where('no_kas', '(.*)')->name('skpd.pelimpahan.edit_gu');
            Route::post('update_gu', [PelimpahanController::class, 'updateGu'])->name('skpd.pelimpahan.update_gu');
            Route::post('hapus_gu', [PelimpahanController::class, 'hapusGu'])->name('skpd.pelimpahan.hapus_gu');
            // Upload Pelimpahan
            Route::get('upload', [PelimpahanController::class, 'upload'])->name('skpd.pelimpahan.upload');
            Route::post('load_upload', [PelimpahanController::class, 'loadUpload'])->name('skpd.pelimpahan.load_upload');
            Route::post('draft_upload', [PelimpahanController::class, 'draftUpload'])->name('skpd.pelimpahan.draft_upload');
            Route::post('data_upload', [PelimpahanController::class, 'dataUpload'])->name('skpd.pelimpahan.data_upload');
            Route::get('tambah_upload', [PelimpahanController::class, 'tambahUpload'])->name('skpd.pelimpahan.tambah_upload');
            Route::post('proses_upload', [PelimpahanController::class, 'prosesUpload'])->name('skpd.pelimpahan.proses_upload');
            Route::post('batal_upload', [PelimpahanController::class, 'batalUpload'])->name('skpd.pelimpahan.batal_upload');
            Route::get('cetak_csv', [PelimpahanController::class, 'cetakCsv'])->name('skpd.pelimpahan.cetak_csv');
            Route::post('rekening_transaksi', [PelimpahanController::class, 'rekeningTransaksi'])->name('skpd.pelimpahan.rekening_transaksi');
            Route::post('rekening_potongan', [PelimpahanController::class, 'rekeningPotongan'])->name('skpd.pelimpahan.rekening_potongan');
            Route::post('rekening_tujuan', [PelimpahanController::class, 'rekeningTujuan'])->name('skpd.pelimpahan.rekening_tujuan');
            // Validasi Pelimpahan
            Route::get('validasi', [PelimpahanController::class, 'validasi'])->name('skpd.pelimpahan.validasi');
            Route::post('load_validasi', [PelimpahanController::class, 'loadValidasi'])->name('skpd.pelimpahan.load_validasi');
            Route::post('draft_validasi', [PelimpahanController::class, 'draftValidasi'])->name('skpd.pelimpahan.draft_validasi');
            Route::post('data_validasi', [PelimpahanController::class, 'dataValidasi'])->name('skpd.pelimpahan.data_validasi');
            Route::get('tambah_validasi', [PelimpahanController::class, 'tambahValidasi'])->name('skpd.pelimpahan.tambah_validasi');
            Route::post('proses_validasi', [PelimpahanController::class, 'prosesValidasi'])->name('skpd.pelimpahan.proses_validasi');
            Route::post('batal_validasi', [PelimpahanController::class, 'batalValidasi'])->name('skpd.pelimpahan.batal_validasi');
        });
        // Ambil Uang Simpanan
        Route::group(['prefix' => 'simpanan_bank'], function () {
            // Ambil Simpanan Bank ke Kasben
            Route::get('kasben', [SimpananBankController::class, 'kasben'])->name('skpd.simpanan_bank.kasben');
            Route::post('load_kasben', [SimpananBankController::class, 'loadKasben'])->name('skpd.simpanan_bank.load_kasben');
            Route::get('tambah_kasben', [SimpananBankController::class, 'tambahKasben'])->name('skpd.simpanan_bank.tambah_kasben');
            Route::post('simpan_kasben', [SimpananBankController::class, 'simpanKasben'])->name('skpd.simpanan_bank.simpan_kasben');
            Route::get('editKasben/{no_bukti?}', [SimpananBankController::class, 'editKasben'])->where('no_bukti', '(.*)')->name('skpd.simpanan_bank.edit_kasben');
            Route::post('update_kasben', [SimpananBankController::class, 'updateKasben'])->name('skpd.simpanan_bank.update_kasben');
            Route::post('hapus_kasben', [SimpananBankController::class, 'hapusKasben'])->name('skpd.simpanan_bank.hapus_kasben');
            // Ambil Simpanan Bank ke Tunai
            Route::get('tunai', [SimpananBankController::class, 'tunai'])->name('skpd.simpanan_bank.tunai');
            Route::post('load_tunai', [SimpananBankController::class, 'loadTunai'])->name('skpd.simpanan_bank.load_tunai');
            Route::get('tambah_tunai', [SimpananBankController::class, 'tambahTunai'])->name('skpd.simpanan_bank.tambah_tunai');
            Route::post('simpan_tunai', [SimpananBankController::class, 'simpanTunai'])->name('skpd.simpanan_bank.simpan_tunai');
            Route::get('editTunai/{no_bukti?}', [SimpananBankController::class, 'editTunai'])->where('no_bukti', '(.*)')->name('skpd.simpanan_bank.edit_tunai');
            Route::post('update_tunai', [SimpananBankController::class, 'updateTunai'])->name('skpd.simpanan_bank.update_tunai');
            Route::post('hapus_tunai', [SimpananBankController::class, 'hapusTunai'])->name('skpd.simpanan_bank.hapus_tunai');
            // Setor Simpanan Bank
            Route::get('setor', [SimpananBankController::class, 'setor'])->name('skpd.simpanan_bank.setor');
            Route::post('load_setor', [SimpananBankController::class, 'loadSetor'])->name('skpd.simpanan_bank.load_setor');
            Route::get('tambah_setor', [SimpananBankController::class, 'tambahSetor'])->name('skpd.simpanan_bank.tambah_setor');
            Route::post('simpan_setor', [SimpananBankController::class, 'simpanSetor'])->name('skpd.simpanan_bank.simpan_setor');
            Route::get('editSetor/{no_bukti?}', [SimpananBankController::class, 'editSetor'])->where('no_bukti', '(.*)')->name('skpd.simpanan_bank.edit_setor');
            Route::post('update_setor', [SimpananBankController::class, 'updateSetor'])->name('skpd.simpanan_bank.update_setor');
            Route::post('hapus_setor', [SimpananBankController::class, 'hapusSetor'])->name('skpd.simpanan_bank.hapus_setor');
        });
        // Pelimpahan Kegiatan
        Route::group(['prefix' => 'pelimpahan_kegiatan'], function () {
            // Pelimpahan Sub Kegiatan
            Route::get('index', [PelimpahanKegiatanController::class, 'index'])->name('skpd.pelimpahan_kegiatan.index');
            Route::post('load_data', [PelimpahanKegiatanController::class, 'loadData'])->name('skpd.pelimpahan_kegiatan.load_data');
            Route::get('create', [PelimpahanKegiatanController::class, 'create'])->name('skpd.pelimpahan_kegiatan.create');
            Route::post('simpan', [PelimpahanKegiatanController::class, 'simpan'])->name('skpd.pelimpahan_kegiatan.simpan');
            Route::get('edit/{id_user?}/{kd_bpp}', [PelimpahanKegiatanController::class, 'edit'])->where('id_user', '(.*)')->where('kd_bpp', '(.*)')->name('skpd.pelimpahan_kegiatan.edit');
            Route::post('update', [PelimpahanKegiatanController::class, 'update'])->name('skpd.pelimpahan_kegiatan.update');
            Route::post('hapus', [PelimpahanKegiatanController::class, 'hapus'])->name('skpd.pelimpahan_kegiatan.hapus');
        });
        // Transaksi Kas
        Route::group(['prefix' => 'transaksi_kas'], function () {
            // Setor Sisa Kas/CP
            Route::group(['prefix' => 'setor_sisa'], function () {
                Route::get('index', [SetorSisaController::class, 'index'])->name('skpd.setor_sisa.index');
                Route::post('load_data', [SetorSisaController::class, 'loadData'])->name('skpd.setor_sisa.load_data');
                Route::post('no_sp2d', [SetorSisaController::class, 'noSp2d'])->name('skpd.setor_sisa.no_sp2d');
                Route::post('kegiatan', [SetorSisaController::class, 'kegiatan'])->name('skpd.setor_sisa.kegiatan');
                Route::post('rekening', [SetorSisaController::class, 'rekening'])->name('skpd.setor_sisa.rekening');
                Route::get('create', [SetorSisaController::class, 'create'])->name('skpd.setor_sisa.create');
                Route::post('cek_simpan', [SetorSisaController::class, 'cekSimpan'])->name('skpd.setor_sisa.cek_simpan');
                Route::post('simpan', [SetorSisaController::class, 'simpan'])->name('skpd.setor_sisa.simpan');
                // TODO:: EDIT BELUM BENAR, CEK LAGI (FEDY-23092025)
                Route::get('edit/{no_sts?}', [SetorSisaController::class, 'edit'])->where('no_sts', '(.*)')->name('skpd.setor_sisa.edit');
                Route::post('update', [SetorSisaController::class, 'update'])->name('skpd.setor_sisa.update');
                Route::post('hapus', [SetorSisaController::class, 'hapus'])->name('skpd.setor_sisa.hapus');
                Route::get('cetak_registerCP', [SetorSisaController::class, 'cetakPermintaanLayar'])->name('skpd.cetak_registerCP');
            });
            // Penerimaan Lain-Lain Pengurang Belanja
            Route::group(['prefix' => 'penerimaan_lain'], function () {
                Route::get('index', [PenerimaanLainController::class, 'index'])->name('skpd.penerimaan_lain.index');
                Route::post('load_data', [PenerimaanLainController::class, 'loadData'])->name('skpd.penerimaan_lain.load_data');
                Route::get('create', [PenerimaanLainController::class, 'create'])->name('skpd.penerimaan_lain.create');
                Route::post('simpan', [PenerimaanLainController::class, 'simpan'])->name('skpd.penerimaan_lain.simpan');
                Route::get('edit/{id_user?}/{kd_bpp}', [PenerimaanLainController::class, 'edit'])->where('id_user', '(.*)')->where('kd_bpp', '(.*)')->name('skpd.penerimaan_lain.edit');
                Route::post('update', [PenerimaanLainController::class, 'update'])->name('skpd.penerimaan_lain.update');
                Route::post('hapus', [PenerimaanLainController::class, 'hapus'])->name('skpd.penerimaan_lain.hapus');
            });
            // Pengeluaran Lain-Lain
            Route::group(['prefix' => 'pengeluaran_lain'], function () {
                Route::get('index', [PengeluaranLainController::class, 'index'])->name('skpd.pengeluaran_lain.index');
                Route::post('load_data', [PengeluaranLainController::class, 'loadData'])->name('skpd.pengeluaran_lain.load_data');
                Route::get('create', [PengeluaranLainController::class, 'create'])->name('skpd.pengeluaran_lain.create');
                Route::post('simpan', [PengeluaranLainController::class, 'simpan'])->name('skpd.pengeluaran_lain.simpan');
                Route::get('edit/{no_bukti?}', [PengeluaranLainController::class, 'edit'])->where('no_bukti', '(.*)')->name('skpd.pengeluaran_lain.edit');
                Route::post('update', [PengeluaranLainController::class, 'update'])->name('skpd.pengeluaran_lain.update');
                Route::post('hapus', [PengeluaranLainController::class, 'hapus'])->name('skpd.pengeluaran_lain.hapus');
            });
            // Setor Kas Unit Ke SKPD
            Route::group(['prefix' => 'setor_kas'], function () {
                // List Setor (CMS)
                Route::group(['prefix' => 'setor'], function () {
                    Route::get('index', [SetorKasController::class, 'index'])->name('skpd.setor.index');
                    Route::post('load_data', [SetorKasController::class, 'loadData'])->name('skpd.setor.load_data');
                    Route::get('create', [SetorKasController::class, 'create'])->name('skpd.setor.create');
                    Route::post('simpan', [SetorKasController::class, 'simpan'])->name('skpd.setor.simpan');
                    Route::get('edit/{no_kas?}', [SetorKasController::class, 'edit'])->where('no_kas', '(.*)')->name('skpd.setor.edit');
                    Route::post('update', [SetorKasController::class, 'update'])->name('skpd.setor.update');
                    Route::post('hapus', [SetorKasController::class, 'hapus'])->name('skpd.setor.hapus');
                });
                // List Upload Setor (CMS)
                Route::group(['prefix' => 'upload_setor'], function () {
                    Route::get('index', [SetorKasController::class, 'indexUpload'])->name('skpd.upload_setor.index');
                    Route::post('load_data', [SetorKasController::class, 'loadDataUpload'])->name('skpd.upload_setor.load_data');
                    Route::post('draft_upload', [SetorKasController::class, 'draftUpload'])->name('skpd.upload_setor.draft_upload');
                    Route::post('data_upload', [SetorKasController::class, 'dataUpload'])->name('skpd.upload_setor.data_upload');
                    Route::get('create', [SetorKasController::class, 'createUpload'])->name('skpd.upload_setor.create');
                    Route::post('simpan', [SetorKasController::class, 'simpanUpload'])->name('skpd.upload_setor.simpan');
                    Route::post('hapus', [SetorKasController::class, 'hapusUpload'])->name('skpd.upload_setor.hapus');
                    Route::get('cetak_csv', [SetorKasController::class, 'cetakCsv'])->name('skpd.upload_setor.cetak_csv');
                });
                // List Validasi Setor (CMS)
                Route::group(['prefix' => 'validasi_setor'], function () {
                    Route::get('index', [SetorKasController::class, 'indexValidasi'])->name('skpd.validasi_setor.index');
                    Route::post('load_data', [SetorKasController::class, 'loadDataValidasi'])->name('skpd.validasi_setor.load_data');
                    Route::get('create', [SetorKasController::class, 'createValidasi'])->name('skpd.validasi_setor.create');
                    Route::post('simpan', [SetorKasController::class, 'simpanValidasi'])->name('skpd.validasi_setor.simpan');
                    Route::post('draft_validasi', [SetorKasController::class, 'draftValidasi'])->name('skpd.validasi_setor.draft_validasi');
                    Route::post('hapus', [SetorKasController::class, 'hapusValidasi'])->name('skpd.validasi_setor.hapus');
                });
                // List Setor (Tunai Ke Bank)
                Route::group(['prefix' => 'setor_tunai'], function () {
                    Route::get('index', [SetorKasController::class, 'indexTunai'])->name('skpd.setor_tunai.index');
                    Route::post('load_data', [SetorKasController::class, 'loadDataTunai'])->name('skpd.setor_tunai.load_data');
                    Route::get('create', [SetorKasController::class, 'createTunai'])->name('skpd.setor_tunai.create');
                    Route::post('simpan', [SetorKasController::class, 'simpanTunai'])->name('skpd.setor_tunai.simpan');
                    Route::post('bank', [SetorKasController::class, 'bank'])->name('skpd.setor_tunai.bank');
                    Route::get('edit/{no_kas?}', [SetorKasController::class, 'editTunai'])->where('no_kas', '(.*)')->name('skpd.setor_tunai.edit');
                    Route::post('update', [SetorKasController::class, 'updateTunai'])->name('skpd.setor_tunai.update');
                    Route::post('hapus', [SetorKasController::class, 'hapusTunai'])->name('skpd.setor_tunai.hapus');
                });
            });
            // UYHD
            Route::group(['prefix' => 'uyhd'], function () {
                Route::get('index', [UyhdController::class, 'index'])->name('skpd.uyhd.index');
                Route::post('load_data', [UyhdController::class, 'loadData'])->name('skpd.uyhd.load_data');
                Route::get('create', [UyhdController::class, 'create'])->name('skpd.uyhd.create');
                Route::post('simpan', [UyhdController::class, 'simpan'])->name('skpd.uyhd.simpan');
                Route::get('edit/{no_bukti?}', [UyhdController::class, 'edit'])->where('no_bukti', '(.*)')->name('skpd.uyhd.edit');
                Route::post('update', [UyhdController::class, 'update'])->name('skpd.uyhd.update');
                Route::post('hapus', [UyhdController::class, 'hapus'])->name('skpd.uyhd.hapus');
            });
            // UYHD Pajak
            Route::group(['prefix' => 'uyhd_pajak'], function () {
                Route::get('index', [UyhdPajakController::class, 'index'])->name('skpd.uyhd_pajak.index');
                Route::post('load_data', [UyhdPajakController::class, 'loadData'])->name('skpd.uyhd_pajak.load_data');
                Route::get('create', [UyhdPajakController::class, 'create'])->name('skpd.uyhd_pajak.create');
                Route::post('simpan', [UyhdPajakController::class, 'simpan'])->name('skpd.uyhd_pajak.simpan');
                Route::get('edit/{no_bukti?}', [UyhdPajakController::class, 'edit'])->where('no_bukti', '(.*)')->name('skpd.uyhd_pajak.edit');
                Route::post('update', [UyhdPajakController::class, 'update'])->name('skpd.uyhd_pajak.update');
                Route::post('hapus', [UyhdPajakController::class, 'hapus'])->name('skpd.uyhd_pajak.hapus');
            });
        });

        // PANJAR CMS
        Route::group(['prefix' => 'panjar_cms'], function () {
            // PEMBERIAN PANJAR CMS
            Route::group(['prefix' => 'pemberian_panjar'], function () {
                Route::get('', [PemberianPanjarController::class, 'index'])->name('pemberian_panjarcms.index');
                Route::post('load', [PemberianPanjarController::class, 'load'])->name('pemberian_panjarcms.load');
                Route::get('tambah', [PemberianPanjarController::class, 'tambah'])->name('pemberian_panjarcms.tambah');
                Route::post('simpan', [PemberianPanjarController::class, 'simpan'])->name('pemberian_panjarcms.simpan');
                Route::get('edit/{no_kas?}/{kd_skpd?}', [PemberianPanjarController::class, 'edit'])->name('pemberian_panjarcms.edit');
                Route::post('update', [PemberianPanjarController::class, 'update'])->name('pemberian_panjarcms.update');
                Route::post('hapus', [PemberianPanjarController::class, 'hapus'])->name('pemberian_panjarcms.hapus');
            });
            // TAMBAH PANJAR CMS
            Route::group(['prefix' => 'tambah_panjar'], function () {
                Route::get('', [TambahPanjarCMSController::class, 'index'])->name('tambah_panjarcms.index');
                Route::post('load', [TambahPanjarCMSController::class, 'load'])->name('tambah_panjarcms.load');
                Route::get('tambah', [TambahPanjarCMSController::class, 'tambah'])->name('tambah_panjarcms.tambah');
                Route::post('kegiatan', [TambahPanjarCMSController::class, 'kegiatan'])->name('tambah_panjarcms.kegiatan');
                Route::post('simpan', [TambahPanjarCMSController::class, 'simpan'])->name('tambah_panjarcms.simpan');
                Route::get('edit/{no_kas?}/{kd_skpd?}', [TambahPanjarCMSController::class, 'edit'])->name('tambah_panjarcms.edit');
                Route::post('update', [TambahPanjarCMSController::class, 'update'])->name('tambah_panjarcms.update');
                Route::post('hapus', [TambahPanjarCMSController::class, 'hapus'])->name('tambah_panjarcms.hapus');
            });
            // UPLOAD PANJAR CMS
            Route::group(['prefix' => 'upload_panjar'], function () {
                Route::get('', [UploadPanjarCMSController::class, 'index'])->name('upload_panjarcms.index');
                Route::post('load_upload', [UploadPanjarCMSController::class, 'loadUpload'])->name('upload_panjarcms.load_data');
                Route::post('draft_upload', [UploadPanjarCMSController::class, 'draftUpload'])->name('upload_panjarcms.draft_upload');
                Route::post('data_upload', [UploadPanjarCMSController::class, 'dataUpload'])->name('upload_panjarcms.data_upload');
                Route::post('data_transaksi', [UploadPanjarCMSController::class, 'dataTransaksi'])->name('upload_panjarcms.data_transaksi');
                Route::get('tambah', [UploadPanjarCMSController::class, 'create'])->name('upload_panjarcms.create');
                Route::post('proses_upload', [UploadPanjarCMSController::class, 'prosesUpload'])->name('upload_panjarcms.proses_upload');
                Route::post('batal_upload', [UploadPanjarCMSController::class, 'batalUpload'])->name('upload_panjarcms.batal_upload');
                Route::get('cetak_csv_kalbar', [UploadPanjarCMSController::class, 'cetakCsvKalbar'])->name('upload_panjarcms.cetak_csv_kalbar');
                Route::get('cetak_csv_luar_kalbar', [UploadPanjarCMSController::class, 'cetakCsvLuarKalbar'])->name('upload_panjarcms.cetak_csv_luar_kalbar');
                Route::post('rekening_transaksi', [UploadPanjarCMSController::class, 'rekeningTransaksi'])->name('upload_panjarcms.rekening_transaksi');
                Route::post('rekening_potongan', [UploadPanjarCMSController::class, 'rekeningPotongan'])->name('upload_panjarcms.rekening_potongan');
                Route::post('rekening_tujuan', [UploadPanjarCMSController::class, 'rekeningTujuan'])->name('upload_panjarcms.rekening_tujuan');
            });
            // VALIDASI PANJAR CMS
            Route::group(['prefix' => 'validasi_panjar'], function () {
                Route::get('', [ValidasiPanjarCMSController::class, 'index'])->name('validasi_panjarcms.index');
                Route::post('load_data', [ValidasiPanjarCMSController::class, 'loadData'])->name('validasi_panjarcms.load_data');
                Route::post('draft_validasi', [ValidasiPanjarCMSController::class, 'draftValidasi'])->name('validasi_panjarcms.draft_validasi');
                Route::post('data_upload', [ValidasiPanjarCMSController::class, 'dataUpload'])->name('validasi_panjarcms.data_upload');
                Route::post('data_transaksi', [ValidasiPanjarCMSController::class, 'dataTransaksi'])->name('validasi_panjarcms.data_transaksi');
                Route::get('tambah', [ValidasiPanjarCMSController::class, 'create'])->name('validasi_panjarcms.create');
                Route::post('proses_validasi', [ValidasiPanjarCMSController::class, 'prosesValidasi'])->name('validasi_panjarcms.proses_validasi');
                Route::post('batal_validasi', [ValidasiPanjarCMSController::class, 'batalValidasi'])->name('validasi_panjarcms.batal_validasi');
            });
        });

        // PERTANGGUNGJAWABAN PANJAR
        Route::group(['prefix' => 'pertanggungjawaban_panjar'], function () {
            Route::get('', [PertanggungjawabanPanjarController::class, 'index'])->name('jawabpanjar.index');
            Route::post('load', [PertanggungjawabanPanjarController::class, 'load'])->name('jawabpanjar.load');
            Route::get('tambah', [PertanggungjawabanPanjarController::class, 'tambah'])->name('jawabpanjar.tambah');
            Route::post('simpan', [PertanggungjawabanPanjarController::class, 'simpan'])->name('jawabpanjar.simpan');
            Route::get('edit/{no_kas?}/{kd_skpd?}', [PertanggungjawabanPanjarController::class, 'edit'])->name('jawabpanjar.edit');
            Route::post('update', [PertanggungjawabanPanjarController::class, 'update'])->name('jawabpanjar.update');
            Route::post('hapus', [PertanggungjawabanPanjarController::class, 'hapus'])->name('jawabpanjar.hapus');
        });

        // PEMBAYARAN PANJAR
        Route::group(['prefix' => 'pembayaran_panjar'], function () {
            Route::get('', [PembayaranPanjarController::class, 'index'])->name('bayarpanjar.index');
            Route::post('load', [PembayaranPanjarController::class, 'load'])->name('bayarpanjar.load');
            Route::get('tambah', [PembayaranPanjarController::class, 'tambah'])->name('bayarpanjar.tambah');
            Route::post('simpan', [PembayaranPanjarController::class, 'simpan'])->name('bayarpanjar.simpan');
            Route::get('edit/{no_panjar?}/{kd_skpd?}', [PembayaranPanjarController::class, 'edit'])->name('bayarpanjar.edit');
            Route::post('update', [PembayaranPanjarController::class, 'update'])->name('bayarpanjar.update');
            Route::post('hapus', [PembayaranPanjarController::class, 'hapus'])->name('bayarpanjar.hapus');
        });

        // TAMBAH PANJAR
        Route::group(['prefix' => 'tambah_panjar'], function () {
            Route::get('', [TambahPanjarController::class, 'index'])->name('tambahpanjar.index');
            Route::post('load', [TambahPanjarController::class, 'load'])->name('tambahpanjar.load');
            Route::get('tambah', [TambahPanjarController::class, 'tambah'])->name('tambahpanjar.tambah');
            Route::post('sub_kegiatan', [TambahPanjarController::class, 'subKegiatan'])->name('tambahpanjar.sub_kegiatan');
            Route::post('simpan', [TambahPanjarController::class, 'simpan'])->name('tambahpanjar.simpan');
            Route::get('edit/{no_panjar?}/{kd_skpd?}', [TambahPanjarController::class, 'edit'])->name('tambahpanjar.edit');
            Route::post('update', [TambahPanjarController::class, 'update'])->name('tambahpanjar.update');
            Route::post('hapus', [TambahPanjarController::class, 'hapus'])->name('tambahpanjar.hapus');
        });

        // TRANSAKSI PANJAR
        Route::group(['prefix' => 'transaksi_panjar'], function () {
            Route::get('', [TransaksiPanjarController::class, 'index'])->name('transaksipanjar.index');
            Route::post('load', [TransaksiPanjarController::class, 'load'])->name('transaksipanjar.load');
            Route::get('tambah', [TransaksiPanjarController::class, 'tambah'])->name('transaksipanjar.tambah');
            Route::post('kegiatan', [TransaksiPanjarController::class, 'kegiatan'])->name('transaksipanjar.kegiatan');
            Route::post('sp2d', [TransaksiPanjarController::class, 'sp2d'])->name('transaksipanjar.sp2d');
            Route::post('rekening', [TransaksiPanjarController::class, 'rekening'])->name('transaksipanjar.rekening');
            Route::post('angkas_spd', [TransaksiPanjarController::class, 'angkasSpd'])->name('transaksipanjar.angkas_spd');
            Route::post('sumber', [TransaksiPanjarController::class, 'sumber'])->name('transaksipanjar.sumber');
            Route::post('sumber_dana', [TransaksiPanjarController::class, 'sumberDana'])->name('transaksipanjar.sumber_dana');
            Route::post('load_data', [TransaksiPanjarController::class, 'loadData'])->name('transaksipanjar.load_data');
            Route::post('simpan', [TransaksiPanjarController::class, 'simpan'])->name('transaksipanjar.simpan');
            Route::get('edit/{no_bukti?}/{kd_skpd?}', [TransaksiPanjarController::class, 'edit'])->name('transaksipanjar.edit');
            Route::post('update', [TransaksiPanjarController::class, 'update'])->name('transaksipanjar.update');
            Route::post('hapus', [TransaksiPanjarController::class, 'hapus'])->name('transaksipanjar.hapus');
        });

        // PENGEMBALIAN SISA PANJAR
        Route::group(['prefix' => 'pengembalian_panjar'], function () {
            Route::get('', [PengembalianPanjarController::class, 'index'])->name('kembalipanjar.index');
            Route::post('load', [PengembalianPanjarController::class, 'load'])->name('kembalipanjar.load');
            Route::get('tambah', [PengembalianPanjarController::class, 'tambah'])->name('kembalipanjar.tambah');
            Route::post('load_data', [PengembalianPanjarController::class, 'loadData'])->name('kembalipanjar.load_data');
            Route::post('simpan', [PengembalianPanjarController::class, 'simpan'])->name('kembalipanjar.simpan');
            Route::get('edit/{no_kas?}/{kd_skpd?}', [PengembalianPanjarController::class, 'edit'])->name('kembalipanjar.edit');
            Route::post('update', [PengembalianPanjarController::class, 'update'])->name('kembalipanjar.update');
            Route::post('hapus', [PengembalianPanjarController::class, 'hapus'])->name('kembalipanjar.hapus');
        });

        //PEMBEMBALIAN PANJAR CMS
        Route::group(['prefix' => 'panjar_cms'], function () {
            Route::get('index', [PanjarPengembalianController::class, 'index'])->name('panjar_cms.index');
            Route::post('load_data', [PanjarPengembalianController::class, 'loadData'])->name('panjar_cms.load_data');
            Route::get('create', [PanjarPengembalianController::class, 'create'])->name('panjar_cms.create');
            Route::post('simpan', [PanjarPengembalianController::class, 'simpan'])->name('panjar_cms.simpan');
            Route::post('kegiatan', [PanjarPengembalianController::class, 'getSubKegiatan'])->name('panjar_cms.kegiatan');
            Route::post('sisaBank', [PanjarPengembalianController::class, 'sisaBank'])->name('panjar_cms.sisaBank');
            Route::post('sisa_ang', [PanjarPengembalianController::class, 'sisaAng'])->name('panjar_cms.sisa_ang');
            Route::post('no_urut', [PanjarPengembalianController::class, 'no_urut'])->name('panjar_cms.no_urut');
            Route::post('cekSimpan', [PanjarPengembalianController::class, 'cekSimpan'])->name('panjar_cms.cek_simpan');
            Route::post('simpanPanjar', [PanjarPengembalianController::class, 'simpanPanjar'])->name('panjar_cms.simpan_panjar');
            Route::post('simpanDetailPanjar', [PanjarPengembalianController::class, 'simpanDetailPanjar'])->name('panjar_cms.simpan_detail_panjar');
            Route::get('edit/{no_panjar?}', [PanjarPengembalianController::class, 'edit'])->where('no_panjar', '(.*)')->name('panjar_cms.edit');
            Route::post('hapus', [PanjarPengembalianController::class, 'hapus'])->name('panjar_cms.hapus');
            Route::post('update', [PanjarPengembalianController::class, 'update'])->name('panjar_cms.update');
            Route::get('cetak_list', [PanjarPengembalianController::class, 'cetakList'])->name('panjar_cms.cetak_list');
        });

        //TAMBAH PANJAR CMS
        Route::group(['prefix' => 'tambah_panjar_cms'], function () {
            Route::get('index', [PanjarTambahController::class, 'index'])->name('tpanjar_cms.index');
            Route::post('load_data', [PanjarTambahController::class, 'loadData'])->name('tpanjar_cms.load_data');
            Route::get('create', [PanjarTambahController::class, 'create'])->name('tpanjar_cms.create');
            Route::post('simpan', [PanjarTambahController::class, 'simpan'])->name('tpanjar_cms.simpan');
            Route::post('kegiatan', [PanjarTambahController::class, 'getSubKegiatan'])->name('tpanjar_cms.kegiatan');
            Route::post('no_panjar', [PanjarTambahController::class, 'noPanjar'])->name('tpanjar_cms.no_panjar');
            Route::post('sisaBank', [PanjarTambahController::class, 'sisaBank'])->name('tpanjar_cms.sisaBank');
            Route::post('sisa_ang', [PanjarTambahController::class, 'sisaAng'])->name('tpanjar_cms.sisa_ang');
            Route::post('no_urut', [PanjarTambahController::class, 'no_urut'])->name('tpanjar_cms.no_urut');
            Route::post('cekSimpan', [PanjarTambahController::class, 'cekSimpan'])->name('tpanjar_cms.cek_simpan');
            Route::post('simpanPanjar', [PanjarTambahController::class, 'simpanPanjar'])->name('tpanjar_cms.simpan_panjar');
            Route::post('simpanDetailPanjar', [PanjarTambahController::class, 'simpanDetailPanjar'])->name('tpanjar_cms.simpan_detail_panjar');
            Route::get('edit/{no_panjar?}', [PanjarTambahController::class, 'edit'])->where('no_panjar', '(.*)')->name('tpanjar_cms.edit');
            Route::post('hapus', [PanjarTambahController::class, 'hapus'])->name('tpanjar_cms.hapus');
            Route::post('update', [PanjarTambahController::class, 'update'])->name('tpanjar_cms.update');
            Route::get('cetak_list', [PanjarTambahController::class, 'cetakList'])->name('tpanjar_cms.cetak_list');
        });

        // Anggaran (RAK)
        Route::group(['prefix' => 'anggaran'], function () {
            // Input RAK
            Route::group(['prefix' => 'input_rak'], function () {
                Route::get('index', [RakController::class, 'index'])->name('skpd.input_rak.index');
                Route::post('load_data', [RakController::class, 'loadData'])->name('skpd.input_rak.load_data');
                Route::post('jenis_anggaran', [RakController::class, 'jenisAnggaran'])->name('skpd.input_rak.jenis_anggaran');
                Route::post('jenis_rak', [RakController::class, 'jenisRak'])->name('skpd.input_rak.jenis_rak');
                Route::post('sub_kegiatan', [RakController::class, 'subKegiatan'])->name('skpd.input_rak.sub_kegiatan');
                Route::post('rekening_rak', [RakController::class, 'rekeningRak'])->name('skpd.input_rak.rekening_rak');
                Route::post('nilai_triwulan', [RakController::class, 'nilaiTriwulan'])->name('skpd.input_rak.nilai_triwulan');
                Route::post('nilai_realisasi', [RakController::class, 'nilaiRealisasi'])->name('skpd.input_rak.nilai_realisasi');
                Route::post('nilai_realisasi_bulan', [RakController::class, 'nilaiRealisasiBulan'])->name('skpd.input_rak.nilai_realisasi_bulan');
                Route::post('status_kunci', [RakController::class, 'statusKunci'])->name('skpd.input_rak.status_kunci');
                Route::post('simpan_rak', [RakController::class, 'simpanRak'])->name('skpd.input_rak.simpan_rak');
            });
            // Cetak RAK
            Route::group(['prefix' => 'cetak_rak'], function () {
                Route::post('cari_ttd_skpd', [RakController::class, 'cariTtdSkpd'])->name('skpd.cetak_rak.ttdskpd');
                // PER SUB KEGIATAN
                Route::group(['prefix' => 'per_sub_kegiatan'], function () {
                    Route::get('cetak_anggaran_per_sub_kegiatan', [RakController::class, 'cetakPerSubKegiatanIndex'])->name('skpd.cetak_rak.per_sub_kegiatan');
                    Route::get('cetak_angkas_giat_preview', [RakController::class, 'cetakRakPerKegiatan'])->name('skpd.cetak_rak.cetak');
                });
                // PER SUB RINCIAN OBJEK
                Route::group(['prefix' => 'per_sub_rincian_objek'], function () {
                    Route::get('cetak_anggaran_per_sub_rincian_objek', [RakController::class, 'rincianObjekIndex'])->name('skpd.cetak_rak.per_sub_rincian_objek');
                    Route::get('cetak_angkas_giat_preview', [RakController::class, 'cetakRakPerObjek'])->name('skpd.cetak_rak.cetak_objek');
                    // keseluruhan seperti SPD
                    Route::get('cetak_angkas_seluruh_preview', [RakController::class, 'cetakRakPerObjekSkpd'])->name('skpd.cetak_rak.cetak_objek_seluruh');
                });
                // PER SKPD
                Route::group(['prefix' => 'per_skpd'], function () {
                    Route::get('per_skpd', [RakController::class, 'rincianPerSkpd'])->name('skpd.cetak_rak.per_skpd');
                    Route::get('per_skpd_preview', [RakController::class, 'cetakPerSkpd'])->name('skpd.cetak_rak.per_skpd_preview');
                });
                Route::group(['prefix' => 'pemda'], function () {
                    Route::get('pemda', [RakController::class, 'RakPemda'])->name('skpd.cetak_rak.pemda');
                    Route::get('pemda_preview', [RakController::class, 'cetakPemda'])->name('skpd.cetak_rak.pemda_preview');
                });
            });
            // CEK RAK
            Route::group(['prefix' => 'cek_rak'], function () {
                Route::get('cek_anggaran', [RakController::class, 'cekAnggaranIndex'])->name('skpd.cek_rak.cek_anggaran');
                Route::get('cetakan_cek_anggaran', [RakController::class, 'cetakCekAnggaran'])->name('skpd.cek_rak.cetakan_cek_anggaran');
                Route::post('cari_skpd', [RakController::class, 'cariSkpd'])->name('skpd.cek_rak.skpd');
            });
        });
        // Transaksi KKPD
        Route::group(['prefix' => 'transaksi_kkpd'], function () {
            // INPUT TRANSAKSI KKPD
            Route::get('', [TransaksiKKPDController::class, 'index'])->name('skpd.transaksi_kkpd.index');
            Route::post('load_data', [TransaksiKKPDController::class, 'loadData'])->name('skpd.transaksi_kkpd.load_data');
            Route::get('tambah', [TransaksiKKPDController::class, 'create'])->name('skpd.transaksi_kkpd.create');
            Route::post('no_urut', [TransaksiKKPDController::class, 'no_urut'])->name('skpd.transaksi_kkpd.no_urut');
            Route::post('skpd', [TransaksiKKPDController::class, 'skpd'])->name('skpd.transaksi_kkpd.skpd');
            Route::post('cariKegiatan', [TransaksiKKPDController::class, 'cariKegiatan'])->name('skpd.transaksi_kkpd.kegiatan');
            Route::post('cariSp2d', [TransaksiKKPDController::class, 'cariSp2d'])->name('skpd.transaksi_kkpd.nomor_sp2d');
            Route::post('cariRekening', [TransaksiKKPDController::class, 'cariRekening'])->name('skpd.transaksi_kkpd.rekening');
            Route::post('cariSumber', [TransaksiKKPDController::class, 'cariSumber'])->name('skpd.transaksi_kkpd.sumber');
            Route::post('sisaBank', [TransaksiKKPDController::class, 'sisaBank'])->name('skpd.transaksi_kkpd.sisa_bank');
            Route::post('potonganLs', [TransaksiKKPDController::class, 'potonganLs'])->name('skpd.transaksi_kkpd.potongan_ls');
            Route::post('loadDana', [TransaksiKKPDController::class, 'loadDana'])->name('skpd.transaksi_kkpd.load_dana');
            Route::post('statusAng', [TransaksiKKPDController::class, 'statusAng'])->name('skpd.transaksi_kkpd.status_ang');
            Route::post('loadAngkas', [TransaksiKKPDController::class, 'loadAngkas'])->name('skpd.transaksi_kkpd.load_angkas');
            Route::post('loadAngkasLalu', [TransaksiKKPDController::class, 'loadAngkasLalu'])->name('skpd.transaksi_kkpd.load_angkas_lalu');
            Route::post('loadSpd', [TransaksiKKPDController::class, 'loadSpd'])->name('skpd.transaksi_kkpd.load_spd');
            Route::post('cekSimpan', [TransaksiKKPDController::class, 'cekSimpan'])->name('skpd.transaksi_kkpd.cek_simpan');
            Route::post('simpanCms', [TransaksiKKPDController::class, 'simpanCms'])->name('skpd.transaksi_kkpd.simpan_cms');
            Route::post('simpanDetailCms', [TransaksiKKPDController::class, 'simpanDetailCms'])->name('skpd.transaksi_kkpd.simpan_detail_cms');
            Route::get('edit/{no_voucher?}', [TransaksiKKPDController::class, 'edit'])->where('no_voucher', '(.*)')->name('skpd.transaksi_kkpd.edit');
            Route::post('hapusCms', [TransaksiKKPDController::class, 'hapusCms'])->name('skpd.transaksi_kkpd.hapus_cms');
            Route::get('cetak_list', [TransaksiKKPDController::class, 'cetakList'])->name('skpd.transaksi_kkpd.cetak_list');

            // Validasi Transaksi KKPD
            Route::get('verifikasi', [TransaksiKKPDController::class, 'indexValidasi'])->name('skpd.transaksi_kkpd.index_validasi');
            Route::post('load_data_validasi', [TransaksiKKPDController::class, 'loadDataValidasi'])->name('skpd.transaksi_kkpd.load_data_validasi');
            Route::post('draft_validasi', [TransaksiKKPDController::class, 'draftValidasi'])->name('skpd.transaksi_kkpd.draft_validasi');
            Route::post('data_validasi', [TransaksiKKPDController::class, 'dataValidasi'])->name('skpd.transaksi_kkpd.data_validasi');
            Route::get('tambah_verifikasi', [TransaksiKKPDController::class, 'createValidasi'])->name('skpd.transaksi_kkpd.create_validasi');
            Route::post('proses_validasi', [TransaksiKKPDController::class, 'prosesValidasi'])->name('skpd.transaksi_kkpd.proses_validasi');
            Route::post('batal_validasi', [TransaksiKKPDController::class, 'batalValidasi'])->name('skpd.transaksi_kkpd.batal_validasi');

            // TERIMA POTONGAN PAJAK KKPD
            Route::get('terima_potongan_kkpd', [TransaksiKKPDController::class, 'indexPotongan'])->name('skpd.transaksi_kkpd.index_potongan');
            Route::post('load_data_potongan', [TransaksiKKPDController::class, 'loadDataPotongan'])->name('skpd.transaksi_kkpd.load_data_potongan');
            Route::get('tambah_potongan', [TransaksiKKPDController::class, 'createPotongan'])->name('skpd.transaksi_kkpd.create_potongan');
            Route::get('edit_potongan/{no_bukti?}', [TransaksiKKPDController::class, 'editPotongan'])->where('no_bukti', '(.*)')->name('skpd.transaksi_kkpd.edit_potongan');
            Route::post('cari_kegiatan_potongan', [TransaksiKKPDController::class, 'cariKegiatanPotongan'])->name('skpd.transaksi_kkpd.cari_kegiatan_potongan');
            Route::post('simpan_potongan', [TransaksiKKPDController::class, 'simpanPotongan'])->name('skpd.transaksi_kkpd.simpan_potongan');
            Route::post('simpan_edit_potongan', [TransaksiKKPDController::class, 'simpanEditPotongan'])->name('skpd.transaksi_kkpd.simpan_edit_potongan');
            Route::post('hapus_potongan', [TransaksiKKPDController::class, 'hapusPotongan'])->name('skpd.transaksi_kkpd.hapus_potongan');
        });
        // Laporan Bendahara
        Route::group(['prefix' => 'laporan_bendahara'], function () {
            Route::get('', [LaporanBendaharaController::class, 'index'])->name('skpd.laporan_bendahara_keluar.index');
            Route::post('cari_skpd', [LaporanBendaharaController::class, 'cariSkpd'])->name('skpd.laporan_bendahara.skpd');
            Route::post('cari_bendahara', [LaporanBendaharaController::class, 'cariBendahara'])->name('skpd.laporan_bendahara.bendahara');
            Route::post('cari_pakpa', [LaporanBendaharaController::class, 'cariPaKpa'])->name('skpd.laporan_bendahara.pakpa');
            Route::post('cari_subkegiatan', [LaporanBendaharaController::class, 'cariSubkegiatan'])->name('skpd.laporan_bendahara.subkegiatan');
            Route::post('cari_akunbelanja', [LaporanBendaharaController::class, 'cariAkunBelanja'])->name('skpd.laporan_bendahara.akunbelanja');
            // Cetak BKU
            Route::get('cetak_bku', [LaporanBendaharaController::class, 'cetakbku'])->name('skpd.laporan_bendahara.cetak_bku');
            // Cetak SPJ Fungsional
            Route::get('cetak_spj_fungsional', [SpjFungsionalController::class, 'cetakSpjFungsional'])->name('skpd.laporan_bendahara.cetak_spj_fungsional');
            // Cetak SP3B
            Route::get('cetak_sp3b', [LaporanBendaharaController::class, 'cetakSp3b'])->name('skpd.laporan_bendahara.cetak_sp3b');
            //Cetak SP3B
            Route::get('cetak_permintaan', [LaporanBendaharaController::class, 'cetakPermintaanLayar'])->name('skpd.laporan_bendahara.cetak_permintaan');
            // BP Kas Bank
            Route::get('cetak_bp_kasbank', [BpKasBankController::class, 'cetakBpkasBank'])->name('skpd.laporan_bendahara.cetak_bp_kasbank');
            // BP Kas Tunai
            Route::get('cetak_bp_kastunai', [BpKasTunaiController::class, 'cetakBpkasTunai'])->name('skpd.laporan_bendahara.cetak_bp_kastunai');
            // BP Pajak
            Route::get('cetak_bp_pajak1', [BpPajakController::class, 'cetakBpPajak'])->name('skpd.laporan_bendahara.cetak_bp_pajak1');
            Route::get('cetak_bp_pajak2', [BpPajakController::class, 'cetakBpPajak2'])->name('skpd.laporan_bendahara.cetak_bp_pajak2');
            Route::get('cetak_bp_pajak3', [BpPajakController::class, 'cetakBpPajak3'])->name('skpd.laporan_bendahara.cetak_bp_pajak3');
            Route::get('cetak_bp_pajak4', [BpPajakController::class, 'cetakBpPajak4'])->name('skpd.laporan_bendahara.cetak_bp_pajak4');
            Route::get('cetak_bp_pajak5', [BpPajakController::class, 'cetakBpPajak5'])->name('skpd.laporan_bendahara.cetak_bp_pajak5');
            Route::post('cari_jenis', [BpPajakController::class, 'cariJenis'])->name('cetak_bppajak.cari_jenis');
            Route::post('cari_pasal', [BpPajakController::class, 'cariPasal'])->name('cetak_bppajak.cari_pasal');
            // BP Panjar
            Route::get('cetak_bp_panjar', [BpPanjarController::class, 'cetakBpPanjar'])->name('skpd.laporan_bendahara.cetak_bp_panjar');
            // Realisasi Fisik
            Route::get('cetak_realisasi_fisik', [RealisasiFisikController::class, 'cetakRealisasiFisik'])->name('skpd.laporan_bendahara.cetak_realisasi_fisik');
            // cetak_laporan_penutupan_kas_bulanan
            Route::get('cetak_laporan_penutupan_kas_bulanan', [LaporanPenutupanKasBulananController::class, 'cetakLaporanPenutupanKasBulanan'])->name('skpd.laporan_bendahara.cetak_laporan_penutupan_kas_bulanan');
            // LAPORAN PENUTUPAN KAS BULANAN (SIPD)
            Route::get('cetak_laporan_penutupan_kas_bulanan_sipd', [LaporanPenutupanKasBulananController::class, 'cetakLaporanPenutupanKasBulananSIPD'])->name('skpd.laporan_bendahara.cetak_laporan_penutupan_kas_bulanan_sipd');
            // cetak_dth
            Route::get('cetak_dth', [LaporanDthController::class, 'cetakLaporanDth'])->name('skpd.laporan_bendahara.cetak_dth');
            // cetak register pajak
            Route::get('cetak_reg_pajak', [LaporanRegPajakController::class, 'cetakRegPajak'])->name('skpd.laporan_bendahara.cetak_reg_pajak');
            Route::get('cetak_reg_pajakpl', [LaporanRegPajakController::class, 'cetakRegPajakPl'])->name('skpd.laporan_bendahara.cetak_reg_pajakpl');
            Route::get('cetak_reg_pajak_rekap', [LaporanRegPajakController::class, 'cetakRekapPajakPotongan'])->name('skpd.laporan_bendahara.cetak_reg_pajak_rekap');
            // cetak_register_cp
            Route::get('cetak_register_cp', [RegisterCpController::class, 'cetakRegisterCp'])->name('skpd.laporan_bendahara.cetak_register_cp');
            // BP Kas Bank
            Route::get('cetak_regsppspm', [RegisterSppSpmSp2dController::class, 'cetakregisterSppSpmSp2d'])->name('skpd.laporan_bendahara.cetak_regsppspm');
            // cetak buku pembantu sub rincian objek
            Route::get('cetak_sub_rincian_objek', [SubRincianObjekController::class, 'cetakSubRincianObjek77'])->name('skpd.laporan_bendahara.cetak_sub_rincian_objek');
            Route::get('cetak_sub_rincian_objek2', [SubRincianObjekController::class, 'cetakSubRincianObjekRekening'])->name('skpd.laporan_bendahara.cetak_sub_rincian_objek2');
            Route::get('cetak_sub_rincian_objek3', [SubRincianObjekController::class, 'cetakSubRincianObjekSubkegiatan'])->name('skpd.laporan_bendahara.cetak_sub_rincian_objek3');
            Route::get('cetak_sub_rincian_objek4', [SubRincianObjekController::class, 'cetakSubRincianObjekSkpd'])->name('skpd.laporan_bendahara.cetak_sub_rincian_objek4');
            Route::get('cetak_sub_rincian_objek5', [SubRincianObjekController::class, 'cetakSubRincianObjekPemakaian'])->name('skpd.laporan_bendahara.cetak_sub_rincian_objek5');
            // cetak Kartu Kendali Sub Kegiatan
            Route::get('cetak_kk_pengajuan', [KartuKendaliSubkegiatanController::class, 'cetakKkpengajuan'])->name('skpd.laporan_bendahara.cetak_kk_pengajuan');
            Route::get('cetak_kk_spj', [KartuKendaliSubkegiatanController::class, 'cetakKkSpj'])->name('skpd.laporan_bendahara.cetak_kk_spj');
            // Cetak BKU Permendagri 13
            Route::get('cetak_bku13', [LaporanBendaharaController::class, 'cetakbku13'])->name('skpd.laporan_bendahara.cetak_bku13');
        });

        // Laporan Bendahara Penerimaan
        Route::group(['prefix' => 'laporan_bendahara_penerimaan'], function () {
            Route::get('', [LaporanBendaharaPenerimaanController::class, 'index'])->name('skpd.laporan_bendahara_penerimaan.index');
            Route::post('cari_skpd', [LaporanBendaharaPenerimaanController::class, 'cariSkpd'])->name('skpd.laporan_bendahara_penerimaan.skpd');
            Route::post('cari_bendahara', [LaporanBendaharaPenerimaanController::class, 'cariBendahara'])->name('skpd.laporan_bendahara_penerimaan.bendahara');
            Route::post('cari_pakpa', [LaporanBendaharaPenerimaanController::class, 'cariPaKpa'])->name('skpd.laporan_bendahara_penerimaan.pakpa');
            Route::post('cari_rekening', [LaporanBendaharaPenerimaanController::class, 'cariRekening'])->name('skpd.laporan_bendahara_penerimaan.rekening');

            // buku terima setor
            Route::get('cetak_buku_penerimaan_penyetoran', [BukuPenerimaanPenyetoranController::class, 'cetakBukuPenerimaanPenyetoran'])->name('skpd.laporan_bendahara_penerimaan.cetak_buku_penerimaan_penyetoran');
            // buku SPJ Pendapatan
            Route::get('cetak_spj_pendapatan', [SpjPendapatanController::class, 'cetakSpjPendapatan'])->name('skpd.laporan_bendahara_penerimaan.cetak_spj_pendapatan');
            // Cek Buku Setoran
            Route::get('cetak_buku_setoran', [BukuSetoranPenerimaanController::class, 'cetakBukuSetoran'])->name('skpd.laporan_bendahara_penerimaan.cetak_buku_setoran');
            Route::get('cetak_bp_sub_rincian_objek', [BukuSetoranPenerimaanController::class, 'cetakRincianObjek'])->name('skpd.laporan_bendahara_penerimaan.cetak_bp_sub_rincian_objek');
            Route::get('berdasarkan_skpd', [LaporanBendaharaPenerimaanController::class, 'berdasarkanSkpd'])->name('skpd.laporan_bendahara_penerimaan.berdasarkan_skpd');
            Route::get('berdasarkan_kasda', [LaporanBendaharaPenerimaanController::class, 'berdasarkanKasda'])->name('skpd.laporan_bendahara_penerimaan.berdasarkan_kasda');
            Route::get('detail_penerimaan', [LaporanBendaharaPenerimaanController::class, 'detailPenerimaan'])->name('skpd.laporan_bendahara_penerimaan.detail_penerimaan');
            Route::get('daiana', [LaporanBendaharaPenerimaanController::class, 'Lapdaiana'])->name('skpd.laporan_bendahara_penerimaan.daiana');
        });
        // Jurnal Koreksi
        Route::group(['prefix' => 'jurnal_koreksi'], function () {
            // Koreksi Atas Kegiatan atau Rekening
            Route::group(['prefix' => 'koreksi_rekening'], function () {
                Route::get('', [JurnalKoreksiController::class, 'indexRekening'])->name('koreksi_rekening.index');
                Route::post('load_data_rekening', [JurnalKoreksiController::class, 'loadDataRekening'])->name('koreksi_rekening.load_data');
                Route::get('tambah', [JurnalKoreksiController::class, 'tambahRekening'])->name('koreksi_rekening.create');
                Route::post('no_sp2d', [JurnalKoreksiController::class, 'nomorSp2d'])->name('koreksi_rekening.no_sp2d');
                Route::post('no_sp2d2', [JurnalKoreksiController::class, 'nomorSp2d2'])->name('koreksi_rekening.no_sp2d2');
                Route::post('rekening', [JurnalKoreksiController::class, 'rekening'])->name('koreksi_rekening.rekening');
                Route::post('sumber', [JurnalKoreksiController::class, 'sumber'])->name('koreksi_rekening.sumber');
                Route::post('sumber2', [JurnalKoreksiController::class, 'sumber2'])->name('koreksi_rekening.sumber2');
                Route::post('rekening_koreksi', [JurnalKoreksiController::class, 'rekeningKoreksi'])->name('koreksi_rekening.rekening_koreksi');
                Route::post('rekening_koreksi2', [JurnalKoreksiController::class, 'rekeningKoreksi2'])->name('koreksi_rekening.rekening_koreksi2');
                Route::post('sumber_koreksi', [JurnalKoreksiController::class, 'sumberKoreksi'])->name('koreksi_rekening.sumber_koreksi');
                Route::post('sumber_koreksi2', [JurnalKoreksiController::class, 'sumberKoreksi2'])->name('koreksi_rekening.sumber_koreksi2');
                Route::post('simpan_koreksi', [JurnalKoreksiController::class, 'simpanKoreksi'])->name('koreksi_rekening.simpan_koreksi');
                Route::get('edit_koreksi_rekening/{no_bukti?}', [JurnalKoreksiController::class, 'editRekening'])->where('no_bukti', '(.*)')->name('koreksi_rekening.edit');
                Route::post('simpan_edit_rekening', [JurnalKoreksiController::class, 'updateRekening'])->name('koreksi_rekening.update');
                Route::post('hapus_rekening', [JurnalKoreksiController::class, 'hapusRekening'])->name('koreksi_rekening.hapus');
            });
            // Koreksi Atas Nominal
            Route::group(['prefix' => 'koreksi_nominal'], function () {
                Route::get('', [JurnalKoreksiController::class, 'indexNominal'])->name('koreksi_nominal.index');
                Route::post('load_data_rekening', [JurnalKoreksiController::class, 'loadDataNominal'])->name('koreksi_nominal.load_data');
                Route::get('tambah', [JurnalKoreksiController::class, 'tambahNominal'])->name('koreksi_nominal.create');
                Route::post('simpan_koreksi', [JurnalKoreksiController::class, 'simpanNominal'])->name('koreksi_nominal.simpan_koreksi');
                Route::get('edit_koreksi_nominal/{no_bukti?}', [JurnalKoreksiController::class, 'editNominal'])->where('no_bukti', '(.*)')->name('koreksi_nominal.edit');
                Route::post('simpan_edit_rekening', [JurnalKoreksiController::class, 'updateNominal'])->name('koreksi_nominal.update');
                Route::post('hapus_rekening', [JurnalKoreksiController::class, 'hapusNominal'])->name('koreksi_nominal.hapus');
            });
        });
        // Pendapatan
        Route::group(['prefix' => 'pendapatan'], function () {
            // Penetapan Pendapatan
            Route::group(['prefix' => 'penetapan'], function () {
                Route::get('', [PenetapanController::class, 'indexPenetapanPendapatan'])->name('penetapan_pendapatan.index');
                Route::post('load_data', [PenetapanController::class, 'loadDataPenetapanPendapatan'])->name('penetapan_pendapatan.load_data');
                Route::get('tambah', [PenetapanController::class, 'tambahPenetapanPendapatan'])->name('penetapan_pendapatan.tambah');
                Route::post('simpan', [PenetapanController::class, 'simpanPenetapanPendapatan'])->name('penetapan_pendapatan.simpan');
                Route::get('edit/{no_tetap?}', [PenetapanController::class, 'editPenetapanPendapatan'])->where('no_tetap', '(.*)')->name('penetapan_pendapatan.edit');
                Route::post('simpan_edit', [PenetapanController::class, 'simpanEditPenetapanPendapatan'])->name('penetapan_pendapatan.simpan_edit');
                Route::post('hapus', [PenetapanController::class, 'hapusPenetapanPendapatan'])->name('penetapan_pendapatan.hapus');
            });
            // Penetapan Penerimaan
            Route::group(['prefix' => 'penerimaan'], function () {
                Route::get('', [PenetapanController::class, 'indexPenetapanPenerimaan'])->name('penetapan_penerimaan.index');
                Route::post('load_data', [PenetapanController::class, 'loadDataPenetapanPenerimaan'])->name('penetapan_penerimaan.load_data');
                Route::get('tambah', [PenetapanController::class, 'tambahPenetapanPenerimaan'])->name('penetapan_penerimaan.tambah');
                Route::post('simpan', [PenetapanController::class, 'simpanPenetapanPenerimaan'])->name('penetapan_penerimaan.simpan');
                Route::get('edit/{no_tetap?}', [PenetapanController::class, 'editPenetapanPenerimaan'])->where('no_tetap', '(.*)')->name('penetapan_penerimaan.edit');
                Route::post('simpan_edit', [PenetapanController::class, 'simpanEditPenetapanPenerimaan'])->name('penetapan_penerimaan.simpan_edit');
                Route::post('hapus', [PenetapanController::class, 'hapusPenetapanPenerimaan'])->name('penetapan_penerimaan.hapus');
            });
            // Penerimaan Tahun Lalu
            Route::group(['prefix' => 'penerimaan_tahun_lalu'], function () {
                Route::get('', [PenerimaanController::class, 'indexPenerimaanLalu'])->name('penerimaan_lalu.index');
                Route::post('load_data', [PenerimaanController::class, 'loadDataPenerimaanLalu'])->name('penerimaan_lalu.load_data');
                Route::get('tambah', [PenerimaanController::class, 'tambahPenerimaanLalu'])->name('penerimaan_lalu.tambah');
                Route::post('simpan', [PenerimaanController::class, 'simpanPenerimaanLalu'])->name('penerimaan_lalu.simpan');
                Route::get('edit/{no_terima?}', [PenerimaanController::class, 'editPenerimaanLalu'])->where('no_terima', '(.*)')->name('penerimaan_lalu.edit');
                Route::post('simpan_edit', [PenerimaanController::class, 'simpanEditPenerimaanLalu'])->name('penerimaan_lalu.simpan_edit');
                Route::post('hapus', [PenerimaanController::class, 'hapusPenerimaanLalu'])->name('penerimaan_lalu.hapus');
            });
            // Penerimaan Tahun Ini
            Route::group(['prefix' => 'penerimaan_tahun_ini'], function () {
                Route::get('', [PenerimaanController::class, 'indexPenerimaanIni'])->name('penerimaan_ini.index');
                Route::post('load_data', [PenerimaanController::class, 'loadDataPenerimaanIni'])->name('penerimaan_ini.load_data');
                Route::get('tambah', [PenerimaanController::class, 'tambahPenerimaanIni'])->name('penerimaan_ini.tambah');
                Route::post('simpan', [PenerimaanController::class, 'simpanPenerimaanIni'])->name('penerimaan_ini.simpan');
                Route::post('cek_simpan', [PenerimaanController::class, 'cekSimpan'])->name('penerimaan_ini.cek_simpan');
                Route::get('edit/{no_terima?}', [PenerimaanController::class, 'editPenerimaanIni'])->where('no_terima', '(.*)')->name('penerimaan_ini.edit');
                Route::post('simpan_edit', [PenerimaanController::class, 'simpanEditPenerimaanIni'])->name('penerimaan_ini.simpan_edit');
                Route::post('hapus', [PenerimaanController::class, 'hapusPenerimaanIni'])->name('penerimaan_ini.hapus');
            });
            // Penyetoran Atas Penerimaan Tahun Lalu
            Route::group(['prefix' => 'penyetoran_tahun_lalu'], function () {
                Route::get('', [PenyetoranController::class, 'indexPenyetoranLalu'])->name('penyetoran_lalu.index');
                Route::post('load_data', [PenyetoranController::class, 'loadDataPenyetoranLalu'])->name('penyetoran_lalu.load_data');
                Route::get('tambah', [PenyetoranController::class, 'tambahPenyetoranLalu'])->name('penyetoran_lalu.tambah');
                Route::post('simpan', [PenyetoranController::class, 'simpanPenyetoranLalu'])->name('penyetoran_lalu.simpan');
                Route::post('rekening', [PenyetoranController::class, 'rekeningPenyetoranLalu'])->name('penyetoran_lalu.rekening');
                Route::get('edit/{no_sts?}', [PenyetoranController::class, 'editPenyetoranLalu'])->where('no_sts', '(.*)')->name('penyetoran_lalu.edit');
                Route::post('simpan_edit', [PenyetoranController::class, 'simpanEditPenyetoranLalu'])->name('penyetoran_lalu.simpan_edit');
                Route::post('hapus', [PenyetoranController::class, 'hapusPenyetoranLalu'])->name('penyetoran_lalu.hapus');
                Route::get('cek', [PenyetoranController::class, 'cekPenyetoranLalu'])->name('penyetoran_lalu.cek');
                Route::post('validasi', [PenyetoranController::class, 'validasiPenyetoranLalu'])->name('penyetoran_lalu.validasi');
            });
            // Penyetoran Atas Penerimaan Tahun Ini
            Route::group(['prefix' => 'penyetoran_tahun_ini'], function () {
                Route::get('', [PenyetoranController::class, 'indexPenyetoranIni'])->name('penyetoran_ini.index');
                Route::post('load_data', [PenyetoranController::class, 'loadDataPenyetoranIni'])->name('penyetoran_ini.load_data');
                Route::get('tambah', [PenyetoranController::class, 'tambahPenyetoranIni'])->name('penyetoran_ini.tambah');
                Route::post('no_terima', [PenyetoranController::class, 'nomorPenyetoranIni'])->name('penyetoran_ini.no_terima');
                Route::post('simpan', [PenyetoranController::class, 'simpanPenyetoranIni'])->name('penyetoran_ini.simpan');
                Route::get('edit/{no_sts?}/{kd_skpd?}', [PenyetoranController::class, 'editPenyetoranIni'])->name('penyetoran_ini.edit');
                Route::post('update', [PenyetoranController::class, 'updatePenyetoranIni'])->name('penyetoran_ini.update');
                Route::post('hapus', [PenyetoranController::class, 'hapusPenyetoranIni'])->name('penyetoran_ini.hapus');
                Route::get('cek', [PenyetoranController::class, 'cekPenyetoranIni'])->name('penyetoran_ini.cek');
                Route::post('validasi', [PenyetoranController::class, 'validasiPenyetoranIni'])->name('penyetoran_ini.validasi');
            });
        });
        // LPJ
        Route::group(['prefix' => 'lpj'], function () {
            // Input LPJ UP/GU
            Route::group(['prefix' => 'up_gu'], function () {
                // LPJ UP/GU (SKPD TANPA UNIT)
                Route::group(['prefix' => 'skpd_tanpa_unit'], function () {
                    Route::get('', [LPJController::class, 'indexSkpdTanpaUnit'])->name('lpj.skpd_tanpa_unit.index');
                    Route::post('load_data', [LPJController::class, 'loadSkpdTanpaUnit'])->name('lpj.skpd_tanpa_unit.load_data');
                    Route::get('tambah', [LPJController::class, 'tambahSkpdTanpaUnit'])->name('lpj.skpd_tanpa_unit.tambah');
                    Route::post('cek', [LPJController::class, 'cekSkpdTanpaUnit'])->name('lpj.skpd_tanpa_unit.cek_kendali');
                    Route::post('detail', [LPJController::class, 'detailSkpdTanpaUnit'])->name('lpj.skpd_tanpa_unit.detail');
                    Route::post('total_spd', [LPJController::class, 'totalspdSkpdTanpaUnit'])->name('lpj.skpd_tanpa_unit.total_spd');
                    Route::post('simpan', [LPJController::class, 'simpanSkpdTanpaUnit'])->name('lpj.skpd_tanpa_unit.simpan');
                    Route::get('edit/{no_lpj?}', [LPJController::class, 'editSkpdTanpaUnit'])->where('no_lpj', '(.*)')->name('lpj.skpd_tanpa_unit.edit');
                    Route::post('update', [LPJController::class, 'updateSkpdTanpaUnit'])->name('lpj.skpd_tanpa_unit.update');
                    Route::post('hapus', [LPJController::class, 'hapusSkpdTanpaUnit'])->name('lpj.skpd_tanpa_unit.hapus');

                    // CETAKAN
                    Route::post('sub_kegiatan', [LPJController::class, 'subKegiatanSkpdTanpaUnit'])->name('lpj.skpd_tanpa_unit.sub_kegiatan');
                    Route::get('cetak_sptb', [LPJController::class, 'sptbSkpdTanpaUnit'])->name('lpj.skpd_tanpa_unit.cetak_sptb');
                    Route::get('rincian', [LPJController::class, 'rincianSkpdTanpaUnit'])->name('lpj.skpd_tanpa_unit.rincian');
                    Route::get('rekap', [LPJController::class, 'rekapSkpdTanpaUnit'])->name('lpj.skpd_tanpa_unit.rekap');
                });
                // LPJ UP/GU (SKPD + UNIT)
                Route::group(['prefix' => 'skpd_dan_unit'], function () {
                    Route::get('', [LPJController::class, 'indexSkpdDanUnit'])->name('lpj.skpd_dan_unit.index');
                    Route::post('load_data', [LPJController::class, 'loadSkpdDanUnit'])->name('lpj.skpd_dan_unit.load_data');
                    Route::get('tambah', [LPJController::class, 'tambahSkpdDanUnit'])->name('lpj.skpd_dan_unit.tambah');
                    Route::post('load_lpj', [LPJController::class, 'loadLpjSkpdDanUnit'])->name('lpj.skpd_dan_unit.load_lpj');
                    Route::post('simpan', [LPJController::class, 'simpanSkpdDanUnit'])->name('lpj.skpd_dan_unit.simpan');
                    Route::get('edit/{no_lpj?}', [LPJController::class, 'editSkpdDanUnit'])->where('no_lpj', '(.*)')->name('lpj.skpd_dan_unit.edit');
                    Route::post('update', [LPJController::class, 'updateSkpdDanUnit'])->name('lpj.skpd_dan_unit.update');
                    Route::post('hapus', [LPJController::class, 'hapusSkpdDanUnit'])->name('lpj.skpd_dan_unit.hapus');

                    // CETAKAN
                    Route::get('rincian', [LPJController::class, 'rincianSkpdDanUnit'])->name('lpj.skpd_dan_unit.rincian');
                });
                // LPJ UP/GU (SKPD/UNIT)
                Route::group(['prefix' => 'skpd_atau_unit'], function () {
                    Route::get('', [LPJController::class, 'indexSkpdAtauUnit'])->name('lpj.skpd_atau_unit.index');
                    Route::post('load_data', [LPJController::class, 'loadSkpdAtauUnit'])->name('lpj.skpd_atau_unit.load_data');
                    Route::get('tambah', [LPJController::class, 'tambahSkpdAtauUnit'])->name('lpj.skpd_atau_unit.tambah');
                    Route::post('detail', [LPJController::class, 'detailSkpdAtauUnit'])->name('lpj.skpd_atau_unit.detail');
                    Route::post('simpan', [LPJController::class, 'simpanSkpdAtauUnit'])->name('lpj.skpd_atau_unit.simpan');
                    Route::get('edit/{no_lpj?}', [LPJController::class, 'editSkpdAtauUnit'])->where('no_lpj', '(.*)')->name('lpj.skpd_atau_unit.edit');
                    Route::post('update', [LPJController::class, 'updateSkpdAtauUnit'])->name('lpj.skpd_atau_unit.update');
                    Route::post('hapus', [LPJController::class, 'hapusSkpdAtauUnit'])->name('lpj.skpd_atau_unit.hapus');

                    // CETAKAN
                    Route::post('sub_kegiatan', [LPJController::class, 'subKegiatanSkpdAtauUnit'])->name('lpj.skpd_atau_unit.sub_kegiatan');
                    Route::get('cetak_sptb', [LPJController::class, 'sptbSkpdAtauUnit'])->name('lpj.skpd_atau_unit.cetak_sptb');
                    Route::get('rincian', [LPJController::class, 'rincianSkpdAtauUnit'])->name('lpj.skpd_atau_unit.rincian');
                });
                // Validasi LPJ UP/GU UNIT
                Route::group(['prefix' => 'validasi_lpj_unit'], function () {
                    Route::get('', [LPJController::class, 'indexValidasiLpj'])->name('lpj.validasi.index');
                    Route::post('load_data', [LPJController::class, 'loadValidasiLpj'])->name('lpj.validasi.load_data');
                    Route::get('edit/{no_lpj?}/{kd_skpd?}', [LPJController::class, 'editValidasiLpj'])->name('lpj.validasi.edit');
                    Route::post('setuju', [LPJController::class, 'setujuValidasiLpj'])->name('lpj.validasi.setuju');
                    Route::post('batal_setuju', [LPJController::class, 'batalSetujuValidasiLpj'])->name('lpj.validasi.batal_setuju');
                });
            });
            // Input LPJ TU
            Route::group(['prefix' => 'tu'], function () {
                // LPJ TU
                Route::get('', [LPJController::class, 'indexLpjTu'])->name('lpj_tu.index');
                Route::post('load', [LPJController::class, 'loadLpjTu'])->name('lpj_tu.load');
                Route::get('tambah', [LPJController::class, 'tambahLpjTu'])->name('lpj_tu.tambah');
                Route::post('detail', [LPJController::class, 'detailLpjTu'])->name('lpj_tu.detail');
                Route::post('simpan', [LPJController::class, 'simpanLpjTu'])->name('lpj_tu.simpan');
                Route::get('edit/{no_lpj?}/{kd_skpd?}', [LPJController::class, 'editLpjTu'])->name('lpj_tu.edit');
                Route::post('update', [LPJController::class, 'updateLpjTu'])->name('lpj_tu.update');
                Route::post('hapus', [LPJController::class, 'hapusLpjTu'])->name('lpj_tu.hapus');

                // CETAKAN
                Route::get('sptb', [LPJController::class, 'sptbLpjTu'])->name('lpj_tu.sptb');
                Route::get('rincian', [LPJController::class, 'rincianLpjTu'])->name('lpj_tu.rincian');
            });
        });
        // SPP GU
        Route::group(['prefix' => 'spp_gu'], function () {
            Route::get('', [SppGuController::class, 'index'])->name('spp_gu.index');
            Route::post('load', [SppGuController::class, 'load'])->name('spp_gu.load');
            Route::get('tambah', [SppGuController::class, 'tambah'])->name('spp_gu.tambah');
            Route::post('detail', [SppGuController::class, 'detail'])->name('spp_gu.detail');
            Route::post('nomor', [SppGuController::class, 'nomor'])->name('spp_gu.nomor');
            Route::post('simpan', [SppGuController::class, 'simpan'])->name('spp_gu.simpan');
            Route::get('edit/{no_spp?}/{kd_skpd?}', [SppGuController::class, 'edit'])->name('spp_gu.edit');
            Route::post('update', [SppGuController::class, 'update'])->name('spp_gu.update');
            Route::post('hapus', [SppGuController::class, 'hapus'])->name('spp_gu.hapus');
            Route::post('batal', [SppGuController::class, 'batal'])->name('spp_gu.batal');

            // CETAKAN
            Route::get('pengantar', [SppGuController::class, 'pengantar'])->name('spp_gu.pengantar');
            Route::get('rincian', [SppGuController::class, 'rincian'])->name('spp_gu.rincian');
            Route::get('ringkasan', [SppGuController::class, 'ringkasan'])->name('spp_gu.ringkasan');
            Route::get('pernyataan', [SppGuController::class, 'pernyataan'])->name('spp_gu.pernyataan');
            Route::get('permintaan', [SppGuController::class, 'permintaan'])->name('spp_gu.permintaan');
            Route::get('sptb', [SppGuController::class, 'sptb'])->name('spp_gu.sptb');
            Route::get('spp', [SppGuController::class, 'spp'])->name('spp_gu.spp');
            Route::get('rincian77', [SppGuController::class, 'rincian77'])->name('spp_gu.rincian77');
        });
        // SPP GU Nihil (created by elvara)
        Route::group(['prefix' => 'spp_gu_nihil'], function () {
            Route::get('', [SppGuNihilController::class, 'index'])->name('spp_gu_nihil.index');
            Route::post('load', [SppGuNihilController::class, 'load'])->name('spp_gu_nihil.load');
            Route::get('tambah', [SppGuNihilController::class, 'tambah'])->name('spp_gu_nihil.tambah');
            Route::post('detail', [SppGuNihilController::class, 'detail'])->name('spp_gu_nihil.detail');
            Route::post('nomor', [SppGuNihilController::class, 'nomor'])->name('spp_gu_nihil.nomor');
            Route::post('simpan', [SppGuNihilController::class, 'simpan'])->name('spp_gu_nihil.simpan');
            Route::get('edit/{no_spp?}/{kd_skpd?}', [SppGuNihilController::class, 'edit'])->name('spp_gu_nihil.edit');
            Route::post('update', [SppGuNihilController::class, 'update'])->name('spp_gu_nihil.update');
            Route::post('hapus', [SppGuNihilController::class, 'hapus'])->name('spp_gu_nihil.hapus');

            // CETAKAN
            Route::get('pengantar', [SppGuNihilController::class, 'pengantar'])->name('spp_gu_nihil.pengantar');
            Route::get('rincian', [SppGuNihilController::class, 'rincian'])->name('spp_gu_nihil.rincian');
            Route::get('ringkasan', [SppGuNihilController::class, 'ringkasan'])->name('spp_gu_nihil.ringkasan');
            Route::get('pernyataan', [SppGuNihilController::class, 'pernyataan'])->name('spp_gu_nihil.pernyataan');
            Route::get('permintaan', [SppGuNihilController::class, 'permintaan'])->name('spp_gu_nihil.permintaan');
            Route::get('sptb', [SppGuNihilController::class, 'sptb'])->name('spp_gu_nihil.sptb');
            Route::get('spp', [SppGuNihilController::class, 'spp'])->name('spp_gu_nihil.spp');
            Route::get('rincian77', [SppGuNihilController::class, 'rincian77'])->name('spp_gu_nihil.rincian77');
        });
        // SPP TU
        Route::group(['prefix' => 'spp_tu'], function () {
            Route::get('', [SppTuController::class, 'index'])->name('spp_tu.index');
            Route::post('load', [SppTuController::class, 'load'])->name('spp_tu.load');
            Route::get('tambah', [SppTuController::class, 'tambah'])->name('spp_tu.tambah');
            Route::post('kegiatan', [SppTuController::class, 'kegiatan'])->name('spp_tu.kegiatan');
            Route::post('rekening', [SppTuController::class, 'rekening'])->name('spp_tu.rekening');
            Route::post('ang_spd_angkas', [SppTuController::class, 'angSpdAngkas'])->name('spp_tu.ang_spd_angkas');
            Route::post('nomor', [SppTuController::class, 'nomor'])->name('spp_tu.nomor');
            Route::post('simpan', [SppTuController::class, 'simpan'])->name('spp_tu.simpan');
            Route::get('edit/{no_spp?}/{kd_skpd?}', [SppTuController::class, 'edit'])->name('spp_tu.edit');
            Route::post('update', [SppTuController::class, 'update'])->name('spp_tu.update');
            Route::post('hapus', [SppTuController::class, 'hapus'])->name('spp_tu.hapus');

            // CETAKAN
            Route::get('pengantar', [SppTuController::class, 'pengantar'])->name('spp_tu.pengantar');
            Route::get('rincian', [SppTuController::class, 'rincian'])->name('spp_tu.rincian');
            Route::get('ringkasan', [SppTuController::class, 'ringkasan'])->name('spp_tu.ringkasan');
            Route::get('pernyataan', [SppTuController::class, 'pernyataan'])->name('spp_tu.pernyataan');
            Route::get('permintaan', [SppTuController::class, 'permintaan'])->name('spp_tu.permintaan');
            Route::get('sptb', [SppTuController::class, 'sptb'])->name('spp_tu.sptb');
            Route::get('spp', [SppTuController::class, 'spp'])->name('spp_tu.spp');
            Route::get('rincian77', [SppTuController::class, 'rincian77'])->name('spp_tu.rincian77');

            // CTTN
            // Route::get('tambah1', [SppTUController::class, 'tambah1'])->name('spp_tu.create');
            // Route::get('tambah2', [SppTUController::class, 'tambah2'])->name('spp_tu.list');
            // Route::get('tambah3', [SppTUController::class, 'tambah3'])->name('spptu.hapusdata');
        });

        // TRANSAKSI TAHUN LALU
        Route::group(['prefix' => 'transaksi_tahun_lalu'], function () {
            Route::get('', [TransaksiLaluController::class, 'index'])->name('transaksi_lalu.index');
            Route::post('load', [TransaksiLaluController::class, 'load'])->name('transaksi_lalu.load');
            Route::get('tambah', [TransaksiLaluController::class, 'tambah'])->name('transaksi_lalu.tambah');
            Route::post('kegiatan', [TransaksiLaluController::class, 'kegiatan'])->name('transaksi_lalu.kegiatan');
            Route::post('sp2d', [TransaksiLaluController::class, 'sp2d'])->name('transaksi_lalu.sp2d');
            Route::post('rekening', [TransaksiLaluController::class, 'rekening'])->name('transaksi_lalu.rekening');
            Route::post('angkas_spd', [TransaksiLaluController::class, 'angkasSpd'])->name('transaksi_lalu.angkas_spd');
            Route::post('potongan', [TransaksiLaluController::class, 'potongan'])->name('transaksi_lalu.potongan');
            Route::post('sumber', [TransaksiLaluController::class, 'sumber'])->name('transaksi_lalu.sumber');
            Route::post('sumber_dana', [TransaksiLaluController::class, 'sumberDana'])->name('transaksi_lalu.sumber_dana');
            Route::post('load_data', [TransaksiLaluController::class, 'loadData'])->name('transaksi_lalu.load_data');
            Route::post('simpan', [TransaksiLaluController::class, 'simpan'])->name('transaksi_lalu.simpan');
            Route::get('edit/{no_bukti?}/{kd_skpd?}', [TransaksiLaluController::class, 'edit'])->name('transaksi_lalu.edit');
            Route::post('update', [TransaksiLaluController::class, 'update'])->name('transaksi_lalu.update');
            Route::post('hapus', [TransaksiLaluController::class, 'hapus'])->name('transaksi_lalu.hapus');
        });
    });

    Route::group(['prefix' => 'pendapatan'], function () {
        // Penerimaan Lain PPKD
        Route::group(['prefix' => 'penerimaan_lain_ppkd'], function () {
            Route::get('', [PenerimaanController::class, 'indexPenerimaanPpkd'])->name('penerimaan_ppkd.index');
            Route::post('load_data', [PenerimaanController::class, 'loadDataPenerimaanPpkd'])->name('penerimaan_ppkd.load_data');
            Route::get('tambah', [PenerimaanController::class, 'tambahPenerimaanPpkd'])->name('penerimaan_ppkd.tambah');
            Route::post('urut', [PenerimaanController::class, 'urutPenerimaanPpkd'])->name('penerimaan_ppkd.urut');
            Route::post('simpan', [PenerimaanController::class, 'simpanPenerimaanPpkd'])->name('penerimaan_ppkd.simpan');
            Route::get('edit/{no_kas?}', [PenerimaanController::class, 'editPenerimaanPpkd'])->where('no_kas', '(.*)')->name('penerimaan_ppkd.edit');
            Route::post('simpan_edit', [PenerimaanController::class, 'simpanEditPenerimaanPpkd'])->name('penerimaan_ppkd.simpan_edit');
            Route::post('hapus', [PenerimaanController::class, 'hapusPenerimaanPpkd'])->name('penerimaan_ppkd.hapus');
        });
        // Penerimaan Kas
        Route::group(['prefix' => 'penerimaan_kas'], function () {
            Route::get('', [PenerimaanController::class, 'indexPenerimaanKas'])->name('penerimaan_kas.index');
            Route::post('load_data', [PenerimaanController::class, 'loadDataPenerimaanKas'])->name('penerimaan_kas.load_data');
            Route::get('tambah', [PenerimaanController::class, 'tambahPenerimaanKas'])->name('penerimaan_kas.tambah');
            Route::post('cari_no_bukti', [PenerimaanController::class, 'noBuktiPenerimaanKas'])->name('penerimaan_kas.no_bukti');
            Route::post('detail_sts', [PenerimaanController::class, 'detailPenerimaanKas'])->name('penerimaan_kas.detail_sts');
            Route::post('nm_sub_kegiatan', [PenerimaanController::class, 'kegiatanPenerimaanKas'])->name('penerimaan_kas.nm_sub_kegiatan');
            Route::post('kunci_kasda', [PenerimaanController::class, 'kunciPenerimaanKas'])->name('penerimaan_kas.kunci_kasda');
            Route::post('simpan', [PenerimaanController::class, 'simpanPenerimaanKas'])->name('penerimaan_kas.simpan');
            Route::post('batal', [PenerimaanController::class, 'batalPenerimaanKas'])->name('penerimaan_kas.batal');
            Route::get('edit/{no_kas?}/{kd_skpd?}', [PenerimaanController::class, 'editPenerimaanKas'])->name('penerimaan_kas.edit');
            Route::post('simpan_edit', [PenerimaanController::class, 'simpanEditPenerimaanKas'])->name('penerimaan_kas.simpan_edit');
            Route::post('hapus', [PenerimaanController::class, 'hapusPenerimaanKas'])->name('penerimaan_kas.hapus');
            Route::get('cetak', [PenerimaanController::class, 'cetakPenerimaanKas'])->name('penerimaan_kas.cetak');
            Route::get('cekvalidasi', [PenerimaanController::class, 'cekDataValidasi'])->name('penerimaan_kas.cekvalidasi');
        });

        // Koreksi Pendapatan
        Route::group(['prefix' => 'koreksi_pendapatan'], function () {
            Route::get('', [PenerimaanController::class, 'indexKoreksi'])->name('koreksi_pendapatan.index');
            Route::post('load_data', [PenerimaanController::class, 'loadDataKoreksi'])->name('koreksi_pendapatan.load_data');
            Route::get('tambah', [PenerimaanController::class, 'tambahKoreksi'])->name('koreksi_pendapatan.tambah');
            Route::post('jenis', [PenerimaanController::class, 'jenisKoreksi'])->name('koreksi_pendapatan.jenis');
            Route::post('simpan', [PenerimaanController::class, 'simpanKoreksi'])->name('koreksi_pendapatan.simpan');
            Route::get('edit/{no?}', [PenerimaanController::class, 'editKoreksi'])->name('koreksi_pendapatan.edit');
            Route::post('simpan_edit', [PenerimaanController::class, 'simpanEditKoreksi'])->name('koreksi_pendapatan.simpan_edit');
            Route::post('hapus', [PenerimaanController::class, 'hapusKoreksi'])->name('koreksi_pendapatan.hapus');
        });
        // Penerimaan Non Pendapatan
        Route::group(['prefix' => 'non_pendapatan'], function () {
            Route::get('', [PenerimaanController::class, 'indexPenerimaanNonPendapatan'])->name('non_pendapatan.index');
            Route::post('load_data', [PenerimaanController::class, 'loadDataPenerimaanNonPendapatan'])->name('non_pendapatan.load_data');
            Route::get('tambah', [PenerimaanController::class, 'tambahPenerimaanNonPendapatan'])->name('non_pendapatan.tambah');
            Route::post('jenis', [PenerimaanController::class, 'jenisPenerimaanNonPendapatan'])->name('non_pendapatan.jenis');
            Route::post('simpan', [PenerimaanController::class, 'simpanPenerimaanNonPendapatan'])->name('non_pendapatan.simpan');
            Route::get('edit/{nomor?}', [PenerimaanController::class, 'editPenerimaanNonPendapatan'])->name('non_pendapatan.edit');
            Route::post('simpan_edit', [PenerimaanController::class, 'simpanEditPenerimaanNonPendapatan'])->name('non_pendapatan.simpan_edit');
            Route::post('hapus', [PenerimaanController::class, 'hapusPenerimaanNonPendapatan'])->name('non_pendapatan.hapus');
        });
    });

    Route::group(['prefix' => 'kartu_kendali'], function () {
        Route::get('', [BendaharaUmumDaerahController::class, 'kartuKendali'])->name('kartu_kendali.index');
        Route::post('kegiatan', [BendaharaUmumDaerahController::class, 'kegiatanKartuKendali'])->name('kartu_kendali.kegiatan');
        Route::post('rekening', [BendaharaUmumDaerahController::class, 'rekeningKartuKendali'])->name('kartu_kendali.rekening');
        Route::get('cetak_per_sub_kegiatan', [BendaharaUmumDaerahController::class, 'cetakKegiatanKartuKendali'])->name('kartu_kendali.cetak_kegiatan');
        Route::get('cetak_per_rekening', [BendaharaUmumDaerahController::class, 'cetakRekeningKartuKendali'])->name('kartu_kendali.cetak_rekening');
    });

    Route::group(['prefix' => 'laporan_bendahara_umum_daerah'], function () {
        Route::get('laporan_bendahara_umum', [BendaharaUmumDaerahController::class, 'index'])->name('laporan_bendahara_umum.index');
        // CETAK REALISASI PENDAPATAN
        Route::get('realisasi_pendapatan', [BendaharaUmumDaerahController::class, 'realisasiPendapatan'])->name('laporan_bendahara_umum.realisasi_pendapatan');
        // CETAK BUKU KAS PEMBANTU PENERIMAAN
        Route::get('buku_kas_pembantu_penerimaan', [BendaharaUmumDaerahController::class, 'pembantuPenerimaan'])->name('laporan_bendahara_umum.buku_kas_pembantu_penerimaan');
        // BKU (B IX)
        Route::group(['prefix' => 'buku_kas_penerimaan_pengeluaran'], function () {
            // CETAK BKU (B IX) TANPA TANGGAL
            Route::get('tanpa_tanggal', [BendaharaUmumDaerahController::class, 'bkuTanpaTanggal'])->name('laporan_bendahara_umum.bku_tanpa_tanggal');
            // CETAK BKU (B IX) TANPA TANGGAL by Calvin
            Route::get('tanpa_tanggal1', [BendaharaUmumDaerahController::class, 'bkuTanpaTanggal1'])->name('laporan_bendahara_umum.bku_tanpa_tanggal1');
            // CETAK BKU (B IX) DENGAN TANGGAL
            Route::get('dengan_tanggal', [BendaharaUmumDaerahController::class, 'bkuDenganTanggal'])->name('laporan_bendahara_umum.bku_dengan_tanggal');
            // CETAK BKU (B IX) DENGAN TANGGAL by calvin
            Route::get('dengan_tanggal1', [BendaharaUmumDaerahController::class, 'bkuDenganTanggal1'])->name('laporan_bendahara_umum.bku_dengan_tanggal1');
            // CETAK BKU (B IX) DENGAN TANGGAL (TANPA BLUD DAN JASPEL)
            Route::get('tanpa_blud', [BendaharaUmumDaerahController::class, 'bkuTanpaBlud'])->name('laporan_bendahara_umum.bku_tanpa_blud');
            // CETAK BKU (B IX) RINCIAN (SEMENTARA HANYA PERTANGGAL)
            Route::get('rincian', [BendaharaUmumDaerahController::class, 'bkuRincian'])->name('laporan_bendahara_umum.bku_rincian');
            // CETAK BKU (B IX) RINCIAN (SEMENTARA HANYA PERTANGGAL) by calvin
            Route::get('rincian1', [BendaharaUmumDaerahController::class, 'bkuRincian1'])->name('laporan_bendahara_umum.bku_rincian1');
            // CETAK BKU (B IX) RINCIAN (PERIODE) by calvin
            Route::get('rincianperiode', [BendaharaUmumDaerahController::class, 'bkuRincianPeriode'])->name('laporan_bendahara_umum.bku_rincianperiode');
        });
        // PENERIMAAN PAJAK DAERAH
        Route::get('penerimaan_pajak_daerah', [BendaharaUmumDaerahController::class, 'pajakDaerah'])->name('laporan_bendahara_umum.penerimaan_pajak_daerah');
        // REKAP GAJI
        Route::get('rekap_gaji', [BendaharaUmumDaerahController::class, 'rekapGaji'])->name('laporan_bendahara_umum.rekap_gaji');
        // BUKU BESAR KASDA
        Route::get('buku_besar_kasda', [BendaharaUmumDaerahController::class, 'rekapBBKasda'])->name('laporan_bendahara_umum.buku_besar_kasda');
        // CETAK BUKU KAS PEMBANTU PENGELUARAN
        Route::get('buku_kas_pembantu_pengeluaran', [BendaharaUmumDaerahController::class, 'pembantuPengeluaran'])->name('laporan_bendahara_umum.buku_kas_pembantu_pengeluaran');
        // RETRIBUSI
        Route::get('retribusi', [BendaharaUmumDaerahController::class, 'retribusi'])->name('laporan_bendahara_umum.retribusi');
        // REGISTER CP
        Route::get('register_cp', [BendaharaUmumDaerahController::class, 'registerCp'])->name('laporan_bendahara_umum.register_cp');
        // REGISTER CP RINCI
        Route::get('register_cp_rinci', [BendaharaUmumDaerahController::class, 'registerCpRinci'])->name('laporan_bendahara_umum.register_cp_rinci');
        // DAFTAR POTONGAN PAJAK
        Route::get('potongan_pajak2', [BendaharaUmumDaerahController::class, 'potonganPajakk'])->name('laporan_bendahara_umum.potongan_pajak2');
        // created by el
        Route::get('potongan_pajak', [BendaharaUmumDaerahController::class, 'potonganPajak'])->name('laporan_bendahara_umum.potongan_pajak');
        // DAFTAR PENGELUARAN
        Route::get('daftar_pengeluaran', [BendaharaUmumDaerahController::class, 'daftarPengeluaran'])->name('laporan_bendahara_umum.daftar_pengeluaran');
        // DAFTAR PENERIMAAN
        Route::get('daftar_penerimaan', [BendaharaUmumDaerahController::class, 'daftarPenerimaan'])->name('laporan_bendahara_umum.daftar_penerimaan');
        // PENERIMAAN NON PENDAPATAN
        Route::get('penerimaan_non_pendapatan', [BendaharaUmumDaerahController::class, 'penerimaanNonPendapatan'])->name('laporan_bendahara_umum.penerimaan_non_pendapatan');
        // TRANSFER DANA
        Route::get('transfer_dana', [BendaharaUmumDaerahController::class, 'transferDana'])->name('laporan_bendahara_umum.transfer_dana');
        // RESTITUSI
        Route::get('restitusi', [BendaharaUmumDaerahController::class, 'restitusi'])->name('laporan_bendahara_umum.restitusi');
        // RTH
        Route::get('rth', [BendaharaUmumDaerahController::class, 'rth'])->name('laporan_bendahara_umum.rth');
        // BUKU PEMBANTU PENGELUARAN NON SP2D
        Route::get('pengeluaran_non_sp2d', [BendaharaUmumDaerahController::class, 'pengeluaranNonSp2d'])->name('laporan_bendahara_umum.pengeluaran_non_sp2d');
        // DTH
        Route::get('dth', [BendaharaUmumDaerahController::class, 'dth'])->name('laporan_bendahara_umum.dth');
        // REGISTER KOREKSI PENERIMAAN
        Route::get('koreksi_penerimaan', [BendaharaUmumDaerahController::class, 'koreksiPenerimaan'])->name('laporan_bendahara_umum.koreksi_penerimaan');
        // KAS HARIAN KASDA
        Route::get('harian_kasda', [BendaharaUmumDaerahController::class, 'harianKasda'])->name('laporan_bendahara_umum.harian_kasda');
        // UYHD
        Route::get('uyhd', [BendaharaUmumDaerahController::class, 'uyhd'])->name('laporan_bendahara_umum.uyhd');
        // KOREKSI PENGELUARAN
        Route::get('koreksi_pengeluaran', [BendaharaUmumDaerahController::class, 'koreksiPengeluaran'])->name('laporan_bendahara_umum.koreksi_pengeluaran');
        // KOREKSI PENERIMAAN
        Route::get('koreksi_penerimaan2', [BendaharaUmumDaerahController::class, 'koreksiPenerimaan2'])->name('laporan_bendahara_umum.koreksi_penerimaan2');
        // REGISTER SP2D (REGISTER)
        Route::get('register_sp2d', [BendaharaUmumDaerahController::class, 'registerSp2d'])->name('laporan_bendahara_umum.register_sp2d');
        // REGISTER SP2D (REALISASI)
        Route::get('realisasi_sp2d', [BendaharaUmumDaerahController::class, 'realisasiSp2d'])->name('laporan_bendahara_umum.realisasi_sp2d');
        // REGISTER SP2D (REALISASI PER SKPD)
        Route::get('realisasiskpd_sp2d', [BendaharaUmumDaerahController::class, 'realisasiSkpdSp2d'])->name('laporan_bendahara_umum.realisasiskpd_sp2d');
        // CEK DATA AKUNTANSI (BELANJA)
        Route::get('cekdataakuntansib', [BendaharaUmumDaerahController::class, 'cekDataAkuntansiB'])->name('laporan_bendahara_umum.cekdataakuntansib');
        // CEK DATA AKUNTANSI (PENDAPATAN)
        Route::get('cekdataakuntansip', [BendaharaUmumDaerahController::class, 'cekDataAkuntansiP'])->name('laporan_bendahara_umum.cekdataakuntansip');
    });

    Route::group(['prefix' => 'bendahara_umum_daerah'], function () {
        // Pengesahan LPJ UP/GU
        Route::group(['prefix' => 'pengesahan_lpj_upgu'], function () {
            Route::get('', [PengesahanController::class, 'indexPengesahanLpjUp'])->name('pengesahan_lpj_upgu.index');
            Route::post('load', [PengesahanController::class, 'loadPengesahanLpjUp'])->name('pengesahan_lpj_upgu.load');
            Route::get('edit/{no_lpj?}/{kd_skpd?}', [PengesahanController::class, 'editPengesahanLpjUp'])->name('pengesahan_lpj_upgu.edit');
            Route::post('detail', [PengesahanController::class, 'detailPengesahanLpjUp'])->name('pengesahan_lpj_upgu.detail');
            Route::post('kegiatan', [PengesahanController::class, 'kegiatanPengesahanLpjUp'])->name('pengesahan_lpj_upgu.kegiatan');
            Route::post('setuju', [PengesahanController::class, 'setujuPengesahanLpjUp'])->name('pengesahan_lpj_upgu.setuju');
            Route::post('batal_setuju', [PengesahanController::class, 'batalSetujuPengesahanLpjUp'])->name('pengesahan_lpj_upgu.batal_setuju');

            // CETAKAN
            Route::get('cetak', [PengesahanController::class, 'cetakPengesahanLpjUp'])->name('pengesahan_lpj_upgu.cetak');
        });
        // Pengesahan LPJ TU
        Route::group(['prefix' => 'pengesahan_lpj_tu'], function () {
            Route::get('', [PengesahanController::class, 'indexPengesahanLpjTu'])->name('pengesahan_lpj_tu.index');
            Route::post('load', [PengesahanController::class, 'loadPengesahanLpjTu'])->name('pengesahan_lpj_tu.load');
            Route::get('edit/{no_lpj?}/{kd_skpd?}', [PengesahanController::class, 'editPengesahanLpjTu'])->name('pengesahan_lpj_tu.edit');
            Route::post('detail', [PengesahanController::class, 'detailPengesahanLpjTu'])->name('pengesahan_lpj_tu.detail');
            Route::post('setuju', [PengesahanController::class, 'setujuPengesahanLpjTu'])->name('pengesahan_lpj_tu.setuju');
            Route::post('batal_setuju', [PengesahanController::class, 'batalSetujuPengesahanLpjTu'])->name('pengesahan_lpj_tu.batal_setuju');

            // CETAKAN
            Route::get('cetak', [PengesahanController::class, 'cetakPengesahanLpjTu'])->name('pengesahan_lpj_tu.cetak');
        });
        // Pengesahan SPM TU
        Route::group(['prefix' => 'pengesahan_spm_tu'], function () {
            Route::get('', [PengesahanController::class, 'indexPengesahanSpmTu'])->name('pengesahan_spm_tu.index');
            Route::post('load', [PengesahanController::class, 'loadPengesahanSpmTu'])->name('pengesahan_spm_tu.load');
            Route::get('edit/{no_spm?}/{kd_skpd?}', [PengesahanController::class, 'editPengesahanSpmTu'])->name('pengesahan_spm_tu.edit');
            Route::post('detail', [PengesahanController::class, 'detailPengesahanSpmTu'])->name('pengesahan_spm_tu.detail');
            Route::post('setuju', [PengesahanController::class, 'setujuPengesahanSpmTu'])->name('pengesahan_spm_tu.setuju');
            Route::post('batal_setuju', [PengesahanController::class, 'batalSetujuPengesahanSpmTu'])->name('pengesahan_spm_tu.batal_setuju');
        });
        // Kendali Proteksi LPJ
        Route::group(['prefix' => 'kendali_proteksi_lpj'], function () {
            Route::get('', [PengesahanController::class, 'indexKendaliProteksi'])->name('kendali_proteksi_lpj.index');
            Route::post('load', [PengesahanController::class, 'loadKendaliProteksi'])->name('kendali_proteksi_lpj.load');
            Route::post('simpan', [PengesahanController::class, 'simpanKendaliProteksi'])->name('kendali_proteksi_lpj.simpan');
        });
    });

    Route::group(['prefix' => 'transaksi_bos'], function () {
        // Input Transaksi BOS
        Route::group(['prefix' => 'input_transaksi_bos'], function () {
            Route::get('', [TransaksiBosController::class, 'index'])->name('transaksi_bos.index');
            Route::post('load', [TransaksiBosController::class, 'load'])->name('transaksi_bos.load');
            Route::get('create', [TransaksiBosController::class, 'create'])->name('transaksi_bos.create');
            Route::post('simpan', [TransaksiBosController::class, 'simpan'])->name('transaksi_bos.simpan');
            Route::post('kegiatan', [TransaksiBosController::class, 'kegiatan'])->name('transaksi_bos.kegiatan');
            Route::post('rekening', [TransaksiBosController::class, 'rekening'])->name('transaksi_bos.rekening');
            Route::post('spd', [TransaksiBosController::class, 'spd'])->name('transaksi_bos.spd');
            Route::post('sumber', [TransaksiBosController::class, 'sumber'])->name('transaksi_bos.sumber');
            Route::post('status', [TransaksiBosController::class, 'status'])->name('transaksi_bos.status');
            Route::get('edit/{no_kas?}/{kd_skpd?}', [TransaksiBosController::class, 'edit'])->name('transaksi_bos.edit');
            Route::post('update', [TransaksiBosController::class, 'update'])->name('transaksi_bos.update');
            Route::post('hapus', [TransaksiBosController::class, 'hapus'])->name('transaksi_bos.hapus');
        });
        // // SP2B
        // Route::group(['prefix' => 'sp2b'], function () {
        //     Route::get('', [Sp2bController::class, 'index'])->name('sp2b.index');
        //     Route::post('load', [Sp2bController::class, 'load'])->name('sp2b.load');
        //     Route::get('create', [Sp2bController::class, 'create'])->name('sp2b.create');
        //     Route::post('simpan', [Sp2bController::class, 'simpan'])->name('sp2b.simpan');
        //     Route::get('edit/{no_lpj?}/{kd_skpd?}', [Sp2bController::class, 'edit'])->name('sp2b.edit');
        //     Route::post('update', [Sp2bController::class, 'update'])->name('sp2b.update');
        //     Route::post('hapus', [Sp2bController::class, 'hapus'])->name('sp2b.hapus');
        // });

        //Created by elvara
        //SP2B
        Route::group(['prefix' => 'sp2b'], function () {
            Route::get('', [Sp2bController::class, 'indexSp2b'])->name('sp2b.index');
            Route::post('load_data', [Sp2bController::class, 'loadData'])->name('sp2b.load_data');
            Route::get('tambah', [Sp2bController::class, 'tambahSp2b'])->name('sp2b.tambah');
            Route::post('detail', [Sp2bController::class, 'detailSp2b'])->name('sp2b.detail');
            Route::post('total_sp2b', [Sp2bController::class, 'totalSp2b'])->name('sp2b.total_sp2b');
            Route::post('simpan', [Sp2bController::class, 'simpanSp2b'])->name('sp2b.simpan');
            Route::post('simpan_edit', [Sp2bController::class, 'simpanEditSp2b'])->name('sp2b.simpan_edit');
            Route::get('edit/{no_sp2b?}', [Sp2bController::class, 'editSp2b'])->where('no_sp2b', '(.*)')->name('sp2b.edit');
            Route::post('hapus', [Sp2bController::class, 'hapusSp2b'])->name('sp2b.hapus');
            // // CETAKAN
            Route::get('cetak_permintaan', [Sp2bController::class, 'cetakPermintaanLayar'])->name('sp2b.cetak_permintaan');
        });
        //End

        //Saldo Kas
        Route::group(['prefix' => 'saldo_kas'], function () {
            Route::get('', [SaldoKasController::class, 'index'])->name('saldo_kas.index');
            Route::post('load', [SaldoKasController::class, 'loadData'])->name('saldo_kas.load');
            Route::get('tambah', [SaldoKasController::class, 'tambah'])->name('saldo_kas.tambah');
            Route::post('simpan', [SaldoKasController::class, 'simpan'])->name('saldo_kas.simpan');
            Route::post('simpan_edit', [SaldoKasController::class, 'simpanEdit'])->name('saldo_kas.simpan_edit');
            Route::get('edit/{nomor?}', [SaldoKasController::class, 'edit'])->name('saldo_kas.edit');
            Route::post('hapus', [SaldoKasController::class, 'hapus'])->name('saldo_kas.hapus');
        });
        //End

        // Penerimaan BOS
        Route::group(['prefix' => 'penerimaan_bos'], function () {
            Route::get('', [PenerimaanBosController::class, 'index'])->name('penerimaan_bos.index');
            Route::post('load', [PenerimaanBosController::class, 'load'])->name('penerimaan_bos.load');
            Route::get('create', [PenerimaanBosController::class, 'create'])->name('penerimaan_bos.create');
            Route::post('simpan', [PenerimaanBosController::class, 'simpan'])->name('penerimaan_bos.simpan');
            Route::get('edit/{no_terima?}/{kd_skpd?}', [PenerimaanBosController::class, 'edit'])->name('penerimaan_bos.edit');
            Route::post('update', [PenerimaanBosController::class, 'update'])->name('penerimaan_bos.update');
            Route::post('hapus', [PenerimaanBosController::class, 'hapus'])->name('penerimaan_bos.hapus');
        });
        // Pengembalian BOS
        Route::group(['prefix' => 'pengembalian_bos'], function () {
            Route::get('', [PengembalianBosController::class, 'index'])->name('pengembalian_bos.index');
            Route::post('load', [PengembalianBosController::class, 'load'])->name('pengembalian_bos.load');
            Route::get('create', [PengembalianBosController::class, 'create'])->name('pengembalian_bos.create');
            Route::post('kegiatan', [PengembalianBosController::class, 'kegiatan'])->name('pengembalian_bos.kegiatan');
            Route::post('sisa_bos', [PengembalianBosController::class, 'sisaBos'])->name('pengembalian_bos.sisa_bos');
            Route::post('simpan', [PengembalianBosController::class, 'simpan'])->name('pengembalian_bos.simpan');
            Route::get('edit/{no_sts?}/{kd_skpd?}', [PengembalianBosController::class, 'edit'])->name('pengembalian_bos.edit');
            Route::post('update', [PengembalianBosController::class, 'update'])->name('pengembalian_bos.update');
            Route::post('hapus', [PengembalianBosController::class, 'hapus'])->name('pengembalian_bos.hapus');
        });
    });

    Route::group(['prefix' => 'utility'], function () {
        // MANAJEMEN RENJA
        Route::group(['prefix' => 'manajemen_renja'], function () {
            // BUKA KUNCI PENAGIHAN/SPP/SPM
            Route::group(['prefix' => 'kunci_penagihan_spp_spm'], function () {
                Route::get('', [KunciPengeluaranController::class, 'index'])->name('kunci_pengeluaran.index');
                Route::post('load', [KunciPengeluaranController::class, 'load'])->name('kunci_pengeluaran.load');
                Route::post('kunci', [KunciPengeluaranController::class, 'kunci'])->name('kunci_pengeluaran.kunci');
            });
            // BUKA KUNCI REKENING BELANJA
            Route::group(['prefix' => 'kunci_belanja'], function () {
                Route::get('', [KunciBelanjaController::class, 'index'])->name('kunci_belanja.index');
                Route::post('load', [KunciBelanjaController::class, 'load'])->name('kunci_belanja.load');
                Route::post('kegiatan', [KunciBelanjaController::class, 'kegiatan'])->name('kunci_belanja.kegiatan');
                Route::post('rekening', [KunciBelanjaController::class, 'rekening'])->name('kunci_belanja.rekening');
                Route::post('kunci', [KunciBelanjaController::class, 'kunci'])->name('kunci_belanja.kunci');
            });
        });
        // KUNCI DATA KASDA
        Route::group(['prefix' => 'kunci_kasda'], function () {
            Route::get('', [KunciKasdaController::class, 'index'])->name('kunci_kasda.index');
            Route::post('kunci', [KunciKasdaController::class, 'kunci'])->name('kunci_kasda.kunci');
        });
    });

    // AKUNTANSI
    // laporan Keuangan
    Route::group(['prefix' => 'laporan_akuntansi'], function () {
        Route::get('', [LaporanAkuntansiController::class, 'index'])->name('laporan_akuntansi.index');
        Route::post('cari_skpd', [LaporanAkuntansiController::class, 'cariSkpd'])->name('laporan_akuntansi.skpd');
        Route::post('cari_skpd2', [LaporanAkuntansiController::class, 'cariSkpd2'])->name('laporan_akuntansi.skpd2');
        Route::post('cari_ttd', [LaporanAkuntansiController::class, 'cariTtd'])->name('laporan_akuntansi.ttd');
        Route::post('cari_rek6', [LaporanAkuntansiController::class, 'carirek6'])->name('laporan_akuntansi.rek6');
        Route::post('cari_skpdbb', [LaporanAkuntansiController::class, 'cariskpdbb'])->name('laporan_akuntansi.skpdbb');
        Route::get('cetak_bb', [LaporanAkuntansiController::class, 'cetak_bb'])->name('laporan_akuntansi.cbb');

        Route::group(['prefix' => 'konsolidasi'], function () {
            Route::get('', [LaporanAkuntansiController::class, 'konsolidasi'])->name('laporan_akuntansi.konsolidasi.konsolidasi');
            //perda
            Route::get('perda', [LaporanAkuntansiController::class, 'perda'])->name('laporan_akuntansi.perda');
            // LRA
            Route::get('cetak_i4_urusan', [LraperdaController::class, 'cetak_i4_urusan'])->name('laporan_akuntansi.perda.cetak_i4_urusan');
            // LRA
            Route::get('cetak_lra', [LraController::class, 'cetakLra'])->name('laporan_akuntansi.konsolidasi.cetak_lra');
        });
    });
    // Pengesahan SPJ
    Route::group(['prefix' => 'pengesahan_spj'], function () {
        Route::get('', [PengesahanSPJController::class, 'index'])->name('pengesahan_spj.index');
        Route::post('cari_skpd', [PengesahanSPJController::class, 'cariSkpd'])->name('pengesahan_spj.skpd');
        Route::post('cari_ttd', [PengesahanSPJController::class, 'cariTtd'])->name('pengesahan_spj.ttd');
        //penerimaan
        Route::get('penerimaan_spj', [PengesahanSPJController::class, 'penerimaan'])->name('pengesahan_spj.penerimaan');
        Route::post('load_penerimaan_spj', [PengesahanSPJController::class, 'load_penerimaan'])->name('pengesahan_spj.load_penerimaan');
        Route::get('cetak_penerimaan_spj', [PengesahanSPJController::class, 'cetak_penerimaan_spj'])->name('pengesahan_spj.cetak_penerimaan_spj');
        Route::post('simpan_penerimaan_spj', [PengesahanSPJController::class, 'simpan_penerimaan_spj'])->name('pengesahan_spj.simpan_penerimaan_spj');
        //pengeluaran
        Route::get('pengeluaran_spj', [PengesahanSPJController::class, 'pengeluaran'])->name('pengesahan_spj.pengeluaran');
        Route::post('load_pengeluaran_spj', [PengesahanSPJController::class, 'load_pengeluaran'])->name('pengesahan_spj.load_pengeluaran');
        Route::get('cetak_pengeluaran_spj', [PengesahanSPJController::class, 'cetak_pengeluaran_spj'])->name('pengesahan_spj.cetak_pengeluaran_spj');
        Route::post('simpan_pengeluaran_spj', [PengesahanSPJController::class, 'simpan_pengeluaran_spj'])->name('pengesahan_spj.simpan_pengeluaran_spj');
    });
    // Rekal Jurnal
    Route::group(['prefix' => 'rekal'], function () {
        Route::get('', [RekalController::class, 'index'])->name('rekal.index');
    });

    // Koreksi SP2D/SPM/SPP/PENAGIHAN
    Route::group(['prefix' => 'koreksi_data'], function () {
        Route::get('', [KoreksiDataController::class, 'index'])->name('koreksi_data.index');
        Route::post('simpan', [KoreksiDataController::class, 'simpan'])->name('koreksi_data.simpan');
        Route::post('sp2d', [KoreksiDataController::class, 'sp2d'])->name('koreksi_data.sp2d');
    });
});



Route::get('dashboard', [HomeController::class, 'index'])->name('home')->middleware(['auth']);
Route::get('pengumuman_list', [HomeController::class, 'pengumuman'])->name('notification');
Route::get('pengumuman/{id?}', [HomeController::class, 'pengumumanid'])->where('id', '(.*)')->name('pengumuman');
Route::get('ubah_skpd/{id?}', [HomeController::class, 'ubahSkpd'])->where('id', '(.*)')->name('ubah_skpd');
Route::post('ubah_skpd/simpan', [HomeController::class, 'simpanUbahSkpd'])->name('ubah_skpd.simpan');
Route::get('ubah_password/{id?}', [HomeController::class, 'ubahPassword'])->where('id', '(.*)')->name('ubah_password');
Route::post('ubah_password/simpan', [HomeController::class, 'simpanUbahPassword'])->name('ubah_password.simpan');
// Route::get('setting', [SettingController::class, 'index'])->name('setting');
Route::post('backup_database', [SettingController::class, 'BackupDatabase'])->name('backup_database');
// Route::get('coba', [HomeController::class, 'coba'])->name('coba');

Route::post('login', [LoginController::class, 'authenticate'])->name('login.index');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('403', function () {
    return abort(401);
})->name('403');

Route::get('/{any}', function () {
    return abort(404);
})->where('any', '.*');
