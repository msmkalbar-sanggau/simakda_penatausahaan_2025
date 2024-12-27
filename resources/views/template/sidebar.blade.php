<div data-simplebar class="sidebar-menu-scroll">

    <!--- Sidemenu -->
    <div id="sidebar-menu">
        <!-- Left Menu Start -->
        <ul class="metismenu list-unstyled" id="side-menu">
            <li class="menu-title">Menu</li>
            <li>
                <a href="{{ route('home') }}">
                    <i class="uil-home-alt"></i><span class="badge rounded-pill bg-primary float-end"></span>
                    <span>Dashboard</span>
                </a>
            </li>
            {{-- Kelola Akses --}}
            {{-- <li>
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="uil-window-section"></i>
                    <span>Kelola Akses</span>
                </a>
                <ul class="sub-menu" aria-expanded="true">
                    <li><a href="{{ route('user.index') }}">Pengguna</a></li>
                    <li><a href="{{ route('peran.index') }}">Peran</a></li>
                    <li><a href="{{ route('hak-akses.index') }}">Hak Akses</a></li>
                </ul>
            </li> --}}
            {{-- Master --}}
            {{-- <li>
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="uil-window-section"></i>
                    <span>Master</span>
                </a>
                <ul class="sub-menu" aria-expanded="true">
                    <li><a href="{{ route('penerima.index') }}">Penerima</a></li>
                    <li><a href="{{ route('kontrak.index') }}">Kontrak</a></li>
                    <li><a href="{{ route('skpd.pelimpahan_kegiatan.index') }}">Pelimpahan Sub Kegiatan</a></li>
                </ul>
            </li> --}}
            {{-- Anggaran --}}
            {{-- <li>
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="uil-window-section"></i>
                    <span>Anggaran</span>
                </a>
                <ul class="sub-menu" aria-expanded="true">
                    <li><a href="{{ route('skpd.input_rak.index') }}">Input RAK</a></li>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">Cetak RAK</a>
                        <ul class="sub-menu" aria-expanded="true">
                            <li><a href="{{ route('skpd.cetak_rak.per_sub_kegiatan') }}">Per Sub Kegiatan</a></li>
                            <li><a href="{{ route('skpd.cetak_rak.per_sub_rincian_objek') }}">Per Sub Rincian Objek</a>
                            </li>
                        </ul>
                    </li>
                    <li><a href="{{ route('skpd.cek_rak.cek_anggaran') }}">Cek RAK</a></li>
                </ul>
            </li> --}}
            {{-- Pengeluaran --}}
            {{-- <li>
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="uil-window-section"></i>
                    <span>Penatausahaan</span>
                </a>
                <ul class="sub-menu" aria-expanded="true">
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">Pengeluaran</a>
                        <ul class="sub-menu" aria-expanded="true">
                            <li><a href="{{ route('penagihan.index') }}">Penagihan</a></li>
                            <li><a href="{{ route('sppls.index') }}">SPP LS</a></li>
                            <li><a href="{{ route('spm.index') }}">SPM</a></li>
                            <li><a href="{{ route('sp2d.index') }}">SP2D</a></li>
                            <li><a href="{{ route('daftar_penguji.index') }}">Daftar Penguji</a></li>
                            <li><a href="{{ route('pencairan_sp2d.index') }}">Pencairan SP2D</a></li>
                            <li><a href="{{ route('sppup.index') }}">SPP UP</a></li>
                        </ul>
                    </li>
                </ul>
            </li> --}}
            {{-- Transaksi Bendahara --}}
            {{-- <li>
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="uil-window-section"></i>
                    <span>Transaksi Bendahara</span>
                </a>
                <ul class="sub-menu" aria-expanded="true">
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">SP2D</a>
                        <ul class="sub-menu" aria-expanded="true">
                            <li><a href="{{ route('terima_sp2d.index') }}">Terima SP2D</a></li>
                            <li><a href="{{ route('skpd.pencairan_sp2d.index') }}">Pencairan SP2D</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">Pelimpahan</a>
                        <ul class="sub-menu" aria-expanded="true">
                            <li><a href="{{ route('skpd.pelimpahan.up_index') }}">Pelimpahan UP</a></li>
                            <li><a href="{{ route('skpd.pelimpahan.gu_index') }}">Pelimpahan GU</a></li>
                            <li><a href="{{ route('skpd.pelimpahan.upload') }}">Upload Pelimpahan</a></li>
                            <li><a href="{{ route('skpd.pelimpahan.validasi') }}">Validasi Pelimpahan</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">Ambil Uang Simpanan</a>
                        <ul class="sub-menu" aria-expanded="true">
                            <li><a href="{{ route('skpd.simpanan_bank.kasben') }}">Ambil Pelimpahan Ke Kas Bank</a>
                            </li>
                            <li><a href="{{ route('skpd.simpanan_bank.tunai') }}">Ambil Uang Bank Ke Tunai</a></li>
                            <li><a href="{{ route('skpd.simpanan_bank.setor') }}">Setor Simpanan</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">Transaksi</a>
                        <ul class="sub-menu" aria-expanded="true">
                            <li><a href="{{ route('skpd.transaksi_cms.index') }}">Transaksi CMS</a></li>
                            <li><a href="{{ route('skpd.upload_cms.index') }}">Upload Transaksi CMS</a></li>
                            <li><a href="{{ route('skpd.validasi_cms.index') }}">Validasi Transaksi CMS</a></li>
                            <li><a href="{{ route('skpd.potongan_pajak_cms.index') }}">Terima Potongan Pajak (CMS)</a>
                            </li>
                            <li><a href="{{ route('skpd.transaksi_tunai.index') }}">Transaksi Tunai</a></li>
                            <li><a href="{{ route('skpd.transaksi_pemindahbukuan.index') }}">Transaksi
                                    Pemindahbukuan</a></li>
                            <li><a href="{{ route('skpd.potongan_pajak.index') }}">Terima Potongan Pajak</a>
                            <li><a href="{{ route('skpd.setor_potongan.index') }}">Setor Potongan Pajak</a>
                            <li><a href="{{ route('skpd.transaksi_kkpd.index') }}">Transaksi KKPD</a>
                            <li><a href="{{ route('skpd.transaksi_kkpd.index_potongan') }}">Terima Potongan Pajak
                                    (KKPD)</a>
                            <li><a href="{{ route('skpd.transaksi_kkpd.index_validasi') }}">Verifikasi Transaksi
                                    KKPD</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">Transaksi Kas</a>
                        <ul class="sub-menu" aria-expanded="true">
                            <li><a href="{{ route('skpd.setor_sisa.index') }}">Setor Sisa Kas/CP</a></li>
                            <li><a href="{{ route('skpd.penerimaan_lain.index') }}">Penerimaan Lain Pengurang
                                    Belanja</a>
                            </li>
                            <li><a href="{{ route('skpd.pengeluaran_lain.index') }}">Pengeluaran Lain-Lain</a></li>
                            <li><a href="{{ route('skpd.setor.index') }}">List Setor (CMS)</a>
                            </li>
                            <li><a href="{{ route('skpd.upload_setor.index') }}">List Upload Setor (CMS)</a></li>
                            <li><a href="{{ route('skpd.validasi_setor.index') }}">List Validasi Setor
                                    (CMS)</a></li>
                            <li><a href="{{ route('skpd.setor_tunai.index') }}">List Setor (Tunai Ke Bank)</a>
                            <li><a href="{{ route('skpd.uyhd.index') }}">UYHD</a>
                            <li><a href="{{ route('skpd.uyhd_pajak.index') }}">UYHD Pajak</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li>
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="uil-window-section"></i>
                    <span>Laporan bendahara</span>
                </a>
                <ul class="sub-menu" aria-expanded="true">
                    <li><a href="{{ route('penerima.index') }}">Penerimaan x</a></li>
                    <li><a href="{{ route('skpd.laporan_bendahara.index') }}">pengeluaran</a></li>
                </ul>
            </li>
            <li>
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="uil-window-section"></i>
                    <span>Bendahara Umum Daerah</span>
                </a>
                <ul class="sub-menu" aria-expanded="true">
                    <li><a href="{{ route('laporan_bendahara_umum.index') }}">Laporan Bendahara Umum Daerah</a></li>
                </ul>
            </li>
            <li>
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="uil-window-section"></i>
                    <span>Jurnal Koreksi Transaksi</span>
                </a>
                <ul class="sub-menu" aria-expanded="true">
                    <li><a href="{{ route('koreksi_rekening.index') }}">Koreksi Atas Kegiatan Atau Rekening</a></li>
                    <li><a href="{{ route('koreksi_nominal.index') }}">Koreksi Atas Jumlah Nominal</a></li>
                </ul>
            </li>
            <li>
                <a href="{{ route('setting.edit') }}">
                    <i class="uil-cog"></i><span class="badge rounded-pill bg-primary float-end"></span>
                    <span>Setting</span>
                </a>
            </li> --}}

            {{-- <li>
                    @if ($menu->urutan_menu == '1')
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="uil-window-section"></i>
                            <span>{{ $menu->display_name }}</span>
                        </a>
                    @endif
                    @foreach ($daftar_menu as $sub)
                        @php
                            $menu1 = $sub->name;
                        @endphp
                        @if ($sub->parent_id == $menu->id)
                            <ul class="sub-menu" aria-expanded="true">
                                <li>
                                    <a href="{{ route($menu1) }}">{{ $sub->display_name }}</a>
                                </li>
                            </ul>
                        @endif
                    @endforeach
                </li> --}}
            @php
                $daftar_menu = filter_menu();
                $daftar_menu1 = sub_menu();
                $daftar_menu2 = sub_menu1();
            @endphp
            @foreach ($daftar_menu as $menu)
                @if ($menu->name !='' && $menu->name!= null)
                    <li>
                        <a href="{{ route($menu->name) }}">
                            <i class="uil-home-alt"></i><span class="badge rounded-pill bg-primary float-end"></span>
                            <span>{{ $menu->display_name }}</span>
                        </a>
                    </li>
                @else
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="uil-window-section"></i>
                        <span>{{ $menu->display_name }}</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">
                        @foreach ($daftar_menu1 as $submenu) {{-- level2 --}}
                            @php
                                $menu1 = $submenu->name;
                            @endphp
                            @if ($menu->id == $submenu->parent_id)
                                @if ($submenu->name)
                                    <li><a href="{{ route($menu1) }}">{{ $submenu->display_name }}</a></li>
                                @else
                                    <li>
                                        <a href="javascript: void(0);"
                                            class="has-arrow">{{ $submenu->display_name }}</a>
                                        <ul class="sub-menu" aria-expanded="true">
                                            @foreach ($daftar_menu2 as $submenu1) {{-- level3 --}}
                                                @php
                                                    $menu2 = $submenu1->name;
                                                @endphp
                                                @if ($submenu->id == $submenu1->parent_id)
                                                    <li><a href="{{ route($menu2) }}">{{ $submenu1->display_name }}</a>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </li>
                                @endif
                            @endif
                        @endforeach
                    </ul>
                </li>
                @endif
                
            @endforeach
        </ul>
    </div>
    <!-- Sidebar -->
</div>
