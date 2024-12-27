<script>
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

        // skpd
        $('#kd_skpd').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5',
            ajax: {
                delay: 1000,
                url: "{{ route('spd.register_spd.skpd') }}",
                type: 'POST',
                dataType: 'json',
                data: function(params) {
                    var query = {
                        term: params.term,
                    }
                    return query
                },
            },
            dropdownAutoWidth: true,
            templateResult: function(result) {
                if (!result.id) return 'Searching';
                return `${result.id} | ${result.text}`;
            },
            escapeMarkup: (m) => m,
            templateSelection: function(result) {
                return result.id || result.text;
            },
        });

        $('#kd_skpd').on('select2:select', function() {
            var skpd = $(this).select2('data')[0];
            $('#nip').val(null).trigger('change').trigger('select2:select');
            if (skpd) {
                $('#nm_skpd').val(skpd.nm_skpd)
                $('#nip').prop('disabled', false)
            } else {
                $('#nm_skpd').val(null)
                $('#nip').prop('disabled', true)
            }
        }).trigger('select2:select');

        //nip
        $('#nip').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5',
            ajax: {
                delay: 1000,
                type: 'POST',
                url: "{{ route('spd.register_spd.nip') }}",
                dataType: 'json',
                data: function(params) {
                    var query = {
                        term: params.term,
                    }
                    var skpd = $('#kd_skpd').select2('data')[0]
                    if (skpd) query.kd_skpd = skpd.kd_skpd
                    return query
                },
            },
            dropdownAutoWidth: true,
            templateResult: function(result) {
                if (!result.id) return 'Searching';
                return `${result.id} | ${result.text}`;
            },
            escapeMarkup: (m) => m,
            templateSelection: function(result) {
                return result.id || result.text;
            },
        });

        $('#nip').on('select2:select', function() {
            var data = $(this).select2('data')[0]
            $('#nama_bend').val(data ? data.nama : null)
        });

        // cetak unit
        $('.unit').on('click', function() {
            let url = new URL("{{ route('spd.register_spd.cetak_urs') }}");
            let skpd = $('#kd_skpd').select2('data')[0];
            let nip = $('#nip').select2('data')[0];
            let tgl_ttd = $("#tanggal_ttd").val();
            let tgl_awal = $("#tanggal_awal").val();
            let tgl_akhir = $("#tanggal_akhir").val();

            if (!skpd) {
                return alert('SKPD Belum Dipilih')
            }

            if (!nip) {
                return alert('NIP Belum Dipilih')
            }

            if (!tgl_awal) {
                return alert('Tanggal Awal Belum Dipilih')
            }

            if (!tgl_akhir) {
                return alert('Tanggal Akhir Belum Dipilih')
            }

            let searchParams = url.searchParams;
            searchParams.append('kd_skpd', skpd.kd_skpd);
            searchParams.append('nip_ttd', nip.nip);
            searchParams.append('tgl_ttd', tgl_ttd);
            searchParams.append('tgl_awal', tgl_awal);
            searchParams.append('tgl_akhir', tgl_akhir);
            searchParams.append('jenis', $(this).data('jenis'));
            window.open(url.toString(), "_blank");
        });

        // cetak skpd
        $('.skpd').on('click', function() {
            let url = new URL("{{ route('spd.register_spd.cetak_srs') }}");
            let skpd = $('#kd_skpd').select2('data')[0];
            let nip = $('#nip').select2('data')[0];
            let tgl_ttd = $("#tanggal_ttd").val();
            let tgl_awal = $("#tanggal_awal").val();
            let tgl_akhir = $("#tanggal_akhir").val();

            if (!skpd) {
                return alert('SKPD Belum Dipilih')
            }

            if (!nip) {
                return alert('NIP Belum Dipilih')
            }

            if (!tgl_awal) {
                return alert('Tanggal Awal Belum Dipilih')
            }

            if (!tgl_akhir) {
                return alert('Tanggal Akhir Belum Dipilih')
            }

            let searchParams = url.searchParams;
            searchParams.append('kd_skpd', skpd.kd_skpd);
            searchParams.append('nip_ttd', nip.nip);
            searchParams.append('tgl_ttd', tgl_ttd);
            searchParams.append('tgl_awal', tgl_awal);
            searchParams.append('tgl_akhir', tgl_akhir);
            searchParams.append('jenis', $(this).data('jenis'));
            window.open(url.toString(), "_blank");
        });

        // cetak keseluruhan
        $('.keseluruhan').on('click', function() {
            let url = new URL("{{ route('spd.register_spd.cetak_krs') }}");
            let tgl_awal = $("#tanggal_awal").val();
            let tgl_akhir = $("#tanggal_akhir").val();

            if (!tgl_awal) {
                return alert('Tanggal Awal Belum Dipilih')
            }

            if (!tgl_akhir) {
                return alert('Tanggal Akhir Belum Dipilih')
            }

            let searchParams = url.searchParams;
            searchParams.append('tgl_awal', tgl_awal);
            searchParams.append('tgl_akhir', tgl_akhir);
            searchParams.append('jenis', $(this).data('jenis'));
            window.open(url.toString(), "_blank");
        });

         // cetak keseluruhan revisi
         $('.keseluruhan_revisi').on('click', function() {
            let url = new URL("{{ route('spd.register_spd.cetak_krrs') }}");
            let tgl_awal = $("#tanggal_awal").val();
            let tgl_akhir = $("#tanggal_akhir").val();

            if (!tgl_awal) {
                return alert('Tanggal Awal Belum Dipilih')
            }

            if (!tgl_akhir) {
                return alert('Tanggal Akhir Belum Dipilih')
            }

            let searchParams = url.searchParams;
            searchParams.append('tgl_awal', tgl_awal);
            searchParams.append('tgl_akhir', tgl_akhir);
            searchParams.append('jenis', $(this).data('jenis'));
            window.open(url.toString(), "_blank");
        });
    });
</script>