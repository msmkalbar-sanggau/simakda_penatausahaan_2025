@extends('template.app')
@section('title', 'Kontrak | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">{{ 'Penandatangan' }}</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Apps</a></li>
                                <li class="breadcrumb-item active">{{ 'Penandatangan' }}</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->
            <div class="card">
                <div class="card-header">
                    <a href="{{ route('tandatangan.create') }}" class="btn btn-primary" style="float: right;">Tambah</a>
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="tandatangan" class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>NIP</th>
                                        <th>Nama </th>
                                        <th>Jabatan</th>
                                        <th>Pangkat</th>
                                        <th>SKPD</th>
                                        <th>Kode</th>
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

            $('#tandatangan').DataTable({
                responsive: true,
                ordering: false,
                serverSide: true,
                processing: true,
                lengthMenu: [5, 10],
                ajax: {
                    "url": "{{ route('tandatangan.load_data') }}",
                    "type": "POST",
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: "text-center",
                    },
                    {
                        data: 'nip',
                        name: 'nip',
                    },
                    {
                        data: 'nama',
                        name: 'nama',
                    },
                    {
                        data: 'jabatan',
                        name: 'jabatan',
                    },
                    {
                        data: 'pangkat',
                        name: 'pangkat',
                    },
                    {
                        data: 'kd_skpd',
                        name: 'kd_skpd',
                    },
                    {
                        data: 'kode',
                        name: 'kode',
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

        function deleteData(id) {
            let tanya = confirm('Apakah anda yakin untuk menghapus data ini ?');
            if (tanya == true) {
                $.ajax({
                    url: "{{ route('tandatangan.hapus') }}",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        id: id,
                    },
                    success: function(data) {
                        if (data.message == '1') {
                            alert('Data Berhasil dihapus');
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
