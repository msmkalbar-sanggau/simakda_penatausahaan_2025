<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let data_sub_kegiatan = $('#data_sub_kegiatan').DataTable({
            responsive: true,
            ordering: false,
            processing: true,
            lengthMenu: [5, 10],
            columns: [{
                    data: 'kd_sub_kegiatan',
                    name: 'kd_sub_kegiatan',
                    width: '50px'
                },
                {
                    data: 'nm_sub_kegiatan',
                    name: 'nm_sub_kegiatan',
                    width: '200px'
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                    width: '100px',
                    className: 'text-center'
                },
            ],
        });

        $('.select2-multiple').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5'
        });

        $('#kd_bpp').on('select2:select', function() {
            let id_user = $(this).find(':selected').data('id_user');
            $('#id_user').val(id_user);
        });

        $('#kd_sub_kegiatan').on('select2:select', function() {
            let kd_sub_kegiatan = this.value;
            let nm_sub_kegiatan = $(this).find(':selected').data('nama');

            let tampungan = data_sub_kegiatan.rows().data().toArray().map((value) => {
                let result = {
                    kd_sub_kegiatan: value.kd_sub_kegiatan,
                };
                return result;
            });
            let kondisi = tampungan.map(function(data) {
                if (data.kd_sub_kegiatan == kd_sub_kegiatan) {
                    return '1';
                }
            });
            if (kondisi.includes("1")) {
                alert('Sub Kegiatan ini sudah ada di LIST!');
                $("#kd_sub_kegiatan").val(null).change();
                return;
            }

            data_sub_kegiatan.row.add({
                'kd_sub_kegiatan': kd_sub_kegiatan,
                'nm_sub_kegiatan': nm_sub_kegiatan,
                'aksi': `<a href="javascript:void(0);" onclick="deleteData('${kd_sub_kegiatan}','${nm_sub_kegiatan}')" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>`,
            }).draw();
            $("#kd_sub_kegiatan").val(null).change();
        });

        $('#simpan_pelimpahan').on('click', function() {
            let kd_bpp = document.getElementById('kd_bpp').value;
            let id_user = document.getElementById('id_user').value;

            let rincian = data_sub_kegiatan.rows().data().toArray();
            if (rincian.length == 0) {
                alert('List Data belum dipilih');
                return;
            }

            if (!kd_bpp) {
                alert('Tujuan harus dipilih!');
                return;
            }

            let rincian_data = data_sub_kegiatan.rows().data().toArray().map((value) => {
                let data = {
                    kd_sub_kegiatan: value.kd_sub_kegiatan,
                    nm_sub_kegiatan: value.nm_sub_kegiatan,
                };
                return data;
            });

            let data = {
                kd_bpp,
                id_user,
                rincian_data
            };

            $('#simpan_pelimpahan').prop('disabled', true);
            $.ajax({
                url: "{{ route('skpd.pelimpahan_kegiatan.update') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: data
                },
                success: function(response) {
                    if (response.message == '1') {
                        alert('Data berhasil ditambahkan');
                        window.location.href =
                            "{{ route('skpd.pelimpahan_kegiatan.index') }}";
                    } else {
                        alert('Data tidak berhasil ditambahkan!');
                        $('#simpan_pelimpahan').prop('disabled', false);
                        return;
                    }
                }
            })
        });
    });

    function deleteData(kd_sub_kegiatan, nm_sub_kegiatan) {
        let tanya = confirm('Apakah anda yakin untuk menghapus dengan Sub Kegiatan : ' + kd_sub_kegiatan);
        let tabel = $('#data_sub_kegiatan').DataTable();
        if (tanya == true) {
            tabel.rows(function(idx, data, node) {
                return data.kd_sub_kegiatan == kd_sub_kegiatan && data.nm_sub_kegiatan == nm_sub_kegiatan
            }).remove().draw();
        } else {
            return false;
        }
    }
</script>
