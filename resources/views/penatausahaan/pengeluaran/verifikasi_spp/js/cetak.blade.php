<script>
    let table_kelengkapan_spm;
    let no_spp_modal = '';
    let kd_skpd_modal = "";

    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        $('.select2-multiple').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5'
        });

        $('#verifikasi_spp').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [10, 20, 50],
            ajax: {
                "url": "{{ route('verifikasi_spp.load_data') }}",
                "type": "POST",
            },
            createdRow: function(row, data, index) {
                if (data.status == 1 && data.sp2d_batal != '1') {

                } else if (data.status == 2 && data.sp2d_batal != '1') {
                    $(row).css("background-color", "#4bbe68");
                    $(row).css("color", "white");
                } else if (data.status == 3 && data.sp2d_batal != '1') {
                    $(row).css("background-color", "#e28743");
                    $(row).css("color", "black");
                } else if (data.sp2d_batal == '1') {
                    $(row).css("background-color", "#ff0000");
                    $(row).css("color", "white");
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: "text-center"
                }, {
                    data: 'no_spp',
                    name: 'no_spp',
                    className: "text-left",
                },
                {
                    data: 'tgl_spp',
                    name: 'tgl_spp',
                    // className: "text-center",
                },
                {
                    data: 'kd_skpd',
                    name: 'kd_skpd',
                    className: "text-center",
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
                    width: 200,
                    className: "text-center",
                },
            ],
            drawCallback: function(settings) {
                console.log('drawCallback');
                $('[data-bs-toggle="tooltip"]').tooltip();
            }
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

        $('#jenis_ls').select2({
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

        // cetak kelengkapan
        $('.kelengkapan').on('click', function() {
            let pptk = document.getElementById('pptk').value;
            let baris_spm = document.getElementById('baris_spm').value;
            let jenis_print = $(this).data("jenis");

            if (!pptk) {
                alert("Pilih PPTK Terlebih Dahulu!");
                return;
            }

            let url = new URL("{{ route('verifikasi_spp.cetak_kelengkapan') }}");
            let searchParams = url.searchParams;
            searchParams.append("no_spp", no_spp_modal);
            searchParams.append("pptk", pptk);
            searchParams.append("jenis_print", jenis_print);
            searchParams.append("baris_spm", baris_spm);
            searchParams.append("kd_skpd", kd_skpd_modal);
            window.open(url.toString(), "_blank");
        });

        $('.pernyataan').on('click', function() {
            let pptk = document.getElementById('pptk').value;
            let baris_spm = document.getElementById('baris_spm').value;
            let jenis_print = $(this).data("jenis");

            if (!pptk) {
                alert("Pilih PPTK Terlebih Dahulu!");
                return;
            }

            let url = new URL("{{ route('verifikasi_spp.pernyataan') }}");
            let searchParams = url.searchParams;
            searchParams.append("no_spp", no_spp_modal);
            searchParams.append("pptk", pptk);
            searchParams.append("jenis_print", jenis_print);
            searchParams.append("baris_spm", baris_spm);
            searchParams.append("kd_skpd", kd_skpd_modal);
            window.open(url.toString(), "_blank");
        });
    });

    function cetak(no_spp, beban, kd_skpd) {
        document.getElementById("no_spp_modal").value = no_spp;
        no_spp_modal = no_spp;
        kd_skpd_modal = kd_skpd;
        $('#modal_cetak').modal('show');
    }
</script>
