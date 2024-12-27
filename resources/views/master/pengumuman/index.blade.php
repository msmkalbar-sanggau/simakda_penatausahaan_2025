@extends('template.app')
@section('title', 'Pengumuman | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">{{ 'Pengumuman' }}</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Apps</a></li>
                                <li class="breadcrumb-item active">{{ 'Pengumuman' }}</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->
            <div class="card">
                <div class="card-header">
                    <a href="{{ route('pengumuman.create') }}" class="btn btn-primary" style="float: right;">Tambah</a>
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="pengumuman" class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Tanggal</th>
                                        <th>Judul</th>
                                        <th>Isi</th>
                                        <th>File</th>
                                        <th>Status Aktif</th>
                                        <th>Status Beranda</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div>

                    </div>

                </div>
            </div>
        </div> <!-- end col -->
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#pengumuman').DataTable({
                responsive: true,
                ordering: false,
                serverSide: true,
                processing: true,
                lengthMenu: [5, 10],
                ajax: {
                    "url": "{{ route('pengumuman.load_data') }}",
                    "type": "POST",
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: "text-center",
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal',
                    },
                    {
                        data: 'judul',
                        name: 'judul',
                    },
                    {
                        data: 'isi',
                        name: 'isi',
                    },
                    {
                        data: 'file',
                        name: 'file',
                    },{
                        data: 'aktif',
                        className: "text-center",
                        render: function (data, type) {
                            if (type === 'display') {
                                let link = '<i class="fa fa-times text-danger"></i>';
                                if (data === '1') {
                                    link = '<i class="fa fa-check text-success"></i>';
                                }else{
                                    link = '<i class="fa fa-times text-danger"></i>';
                                }

                                return link;
                            }

                            return data;
                        },
                    },{
                        data: 'status',
                        className: "text-center",
                        render: function (data, type) {
                            if (type === 'display') {
                                let link = '<i class="fa fa-times text-danger"></i>';
                                if (data === '1') {
                                    link = '<i class="fa fa-check text-success"></i>';
                                }else{
                                    link = '<i class="fa fa-times text-danger"></i>';
                                }

                                return link;
                            }

                            return data;
                        },
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        className: 'text-center',
                        width: 150
                    },
                ],
            });
        });

        function deleteData(no_pengumuman,dokumen) {
            let tanya = confirm('Apakah anda yakin untuk menghapus data dengan id : ' + no_pengumuman+' dan dokumen: '+dokumen );
            if (tanya == true) {
                $.ajax({
                    url: "{{ route('pengumuman.hapus') }}",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        id: no_pengumuman,
                        file:dokumen
                    },
                    success: function(data) {
                        if (data.message == '1') {
                            alert('Proses Hapus Berhasil');
                            window.location.reload();
                        } else {
                            alert('Proses Hapus Gagal...!!!');
                        }
                    }
                })
            } else {
                return false;
            }
        }
    </script>
@endsection
