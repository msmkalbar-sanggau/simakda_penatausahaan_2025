<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

var table= $('#sp2dverif').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [5, 10],
            ajax: {
                "url": "{{ route('verif_sp2d.load_data') }}",
                "type": "POST"
            },
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: "text-center"
                }, 
                {
                    data: 'nomor',
                    name: 'nomor',
                    className: "text-center",
                },
                {
                    data: 'tanggal',
                    name: 'tanggal',
                },
                {
                    data: 'keterangan',
                    name: 'keterangan',
                },
                {
                    data: 'nilai',
                    name: 'nilai',
                    className: "text-center",
                },
                {
                    data: 'user',
                    name: 'user',
                    className: "text-center",
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                    width: 200,
                    className: "text-center",
                },
            ],
        });
        
        
        $('#bverif').on('click', function() {
            table.ajax.url("{{ route('verif_sp2d.load_data') }}");
            table.ajax.reload();
        });
        $('#sverif').on('click', function() {
            table.ajax.url("{{ route('verif_sp2d.load_data_verif') }}");
            table.ajax.reload();
        });
        $('#salur').on('click', function() {
            table.ajax.url("{{ route('verif_sp2d.load_data_salur') }}");
            table.ajax.reload();
        });
        
        

        
    });



    function batal_sp2d(no_sp2d, beban, kd_skpd, no_spm, no_spp, status) {
        $('#no_sp2d_batal').val(no_sp2d);
        $('#beban_batal').val(beban);
        $('#no_spm_batal').val(no_spm);
        $('#no_spp_batal').val(no_spp);
        $('#status_bud').val(status);
        $('#sp2d_batal').modal('show');
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
