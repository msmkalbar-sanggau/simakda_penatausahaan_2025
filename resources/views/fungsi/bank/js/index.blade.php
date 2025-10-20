<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.select2-modal').select2({
            dropdownParent: $('#modal_bank .modal-content'),
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5'
        });

        let tabel = $('#bank').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [5, 10],
            ajax: {
                "url": "{{ route('bank.load') }}",
                "type": "POST",
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: "text-center",
                },
                {
                    data: 'kode',
                    name: 'kode',
                    className: 'text-center'
                },
                {
                    data: 'nama',
                    name: 'nama',
                },
                {
                    data: 'bic',
                    name: 'bic',
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                    width: 100,
                    className: "text-center",
                },
            ],
        });

        $('#tambah').on('click', function() {
            $('#nomor').val(null);
            nomor();
            $('#modal_bank').modal('show');
        });

        $('#simpan').on('click', function() {
            let kode = document.getElementById('kode').value;
            let nama = document.getElementById('nama').value;
            let bic = document.getElementById('bic').value;

            if (!kode) {
                alert('Silahkan refresh!Kode tidak ada!');
                return
            }

            if (!nama) {
                alert('Nama bank tidak boleh kosong!');
                return;
            }

            if (nama.length > 500) {
                alert('Maksimal panjang nama 500 karakter');
                return;
            }

            if (!bic) {
                alert('BIC tidak boleh kosong!');
                return;
            }

            if (bic.length > 100) {
                alert('Maksimal panjang bic 100 karakter');
                return;
            }

            $.ajax({
                url: "{{ route('bank.simpan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kode: kode,
                    bic: bic,
                    nama: nama
                },
                beforeSend: function() {
                    $("#overlay").fadeIn(100);
                },
                success: function(data) {
                    tabel.ajax.reload();
                    if (data.message == '1') {
                        alert('Data berhasil tersimpan!');
                    } else if (data.message == '2') {
                        alert('Kode telah digunakan!Silahkan refresh!');
                    } else if (data.message == '3') {
                        alert('Nama telah digunakan!!');
                    } else {
                        alert('Data gagal disimpan!');
                    }
                    $('#kode').val(null);
                    $('#nama').val(null);
                    $('#bic').val(null);
                    $('#modal_bank').modal('hide');
                },
                complete: function(data) {
                    $("#overlay").fadeOut(100);
                }
            })
        });

        function nomor() {
            $.ajax({
                url: "{{ route('bank.nomor') }}",
                type: "POST",
                dataType: 'json',
                beforeSend: function() {
                    $("#overlay").fadeIn(100);
                },
                success: function(data) {
                    $('#kode').val(data);
                },
                complete: function(data) {
                    $("#overlay").fadeOut(100);
                }
            })
        }
    });
</script>
