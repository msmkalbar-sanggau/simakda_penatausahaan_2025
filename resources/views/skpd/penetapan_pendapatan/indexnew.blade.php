@extends('template.app')
@section('title', 'Penetapan Pendapatan | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    List Data Penetapan
                    <a href="{{ route('tambahpenetapantahunini') }}" class="btn btn-primary" style="float: right;">Tambah</a>
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="penetapan_pendapatan" class="table table-striped table-bordered" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th style="width: 25px;text-align:center">No</th>
                                        <th style="width: 90px;text-align:center">Nomor Tetap</th>
                                        <th style="width: 50px;text-align:center">Tanggal</th>
                                        <th style="width: 50px;text-align:center">SKPD</th>
                                        <th style="width: 50px;text-align:center">Rekening</th>
                                        <th style="width: 50px;text-align:center">Rekening</th>
                                        <th style="width: 100px;text-align:center">Nilai</th>
                                        <th style="width: 60px;text-align:center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript">
        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });

        $(document).ready(function() {
            $('#penetapan_pendapatan').DataTable({
                responsive: true,
                ordering: false,
                serverSide: true,
                processing: true,
                lengthMenu: [5, 10, 25, 50, 100],
                ajax: {
                    "url": "{{ route('listpenetapantahunini') }}",
                    "type": "POST",
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: "text-center",
                    }, {
                        data: 'no_tetap',
                        name: 'no_tetap',
                        // className: "text-center",
                    },
                    {
                        data: 'tgl_tetap',
                        name: 'tgl_tetap',
                        className: "text-center",
                    },
                    {
                        visible: false,
                        data: 'kd_skpd',
                        name: 'kd_skpd',
                        className: "text-center",
                    },
                    {
                        // visible: false,
                        data: 'kd_rek6',
                        name: 'Rekening',
                        className: "text-center",
                    },
                    {
                        visible: false,
                        data: 'kd_rek_lo',
                        name: 'kd_rek_lo',
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
                        data: 'aksi',
                        name: 'aksi',
                        width: '60px',
                        className: "text-center",
                    },
                ],
            });
        });

        function hapus(no_tetap, kd_skpd) {
            // let data = false;
            let data = {
                no_tetap,
                kd_skpd
            }
            Swal.fire({
                text: `Yakin menghapus data : ${no_tetap}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal!',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('hapuspenetapantahunini.hapus') }}",
                        type: 'POST',
                        data: {
                            data: data
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.sudahada) {
                                Swal.fire({
                                    icon: 'warning',
                                    text: response.sudahada,
                                })
                            } else if (response.berhasil) {
                                Swal.fire({
                                    icon: 'success',
                                    text: response.berhasil,
                                })
                            } else if (response.erorr) {
                                Swal.fire({
                                    icon: 'error',
                                    text: response.erorr,
                                })
                            }
                        },
                        error: function(message) {
                            console.log(message);
                        }
                    });
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    Swal.fire({
                        icon: 'info',
                        text: `Data : ${no_tetap} aman`
                    })
                }
            })
        }
    </script>
@endsection
