@extends('template.app')
@section('title', 'Pengguna | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <a href="{{ route('user.create') }}" class="btn btn-primary" style="float: right;">Tambah</a>
                </div>

                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="user" class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">No.</th>
                                        <th style="text-align: center;">Username</th>
                                        <th style="text-align: center;">Nama</th>
                                        <th style="text-align: center;">SKPD</th>
                                        <th style="text-align: center;">JABATAN</th>
                                        <th style="text-align: center;">Aksi</th>
                                    </tr>
                                </thead>

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

            $('#user').DataTable({
                responsive: true,
                ordering: false,
                serverSide: true,
                processing: true,
                lengthMenu: [5, 10, 20, 50],
                ajax: {
                    "url": "{{ route('user.load_data') }}",
                    "type": "POST",
                },
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: "text-center",
                }, {
                    data: 'username',
                    name: 'username',
                }, {
                    data: 'nama',
                    name: 'nama',
                }, {
                    data: 'nm_skpd',
                    name: 'nm_skpd',
                }, {
                    data: 'jabatan',
                    name: 'jabatan',
                }, {
                    data: 'aksi',
                    name: 'aksi',
                    className: 'text-center'
                }, ],
            });
        });

        function deleteData(id, user_id) {
            if (id == user_id) {
                alert('Dilarang menghapus data diri sendiri!!!');
                return;
            }
            var r = confirm("Hapus?");
            if (r == true) {
                let url = '{{ route('user.destroy', ':id') }}';
                url = url.replace(':id', id);
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        id: id,
                    },
                    success: function(data) {
                        if (data.message == '1') {
                            alert('Data berhasil dihapus!');
                            window.location.reload();
                        } else if (data.message == '403') {
                            window.location.href = "{{ route('403') }}";
                        } else {
                            alert('Data gagal dihapus!');
                        }
                    }
                });
            } else {
                return false;
            }
        }
    </script>
@endsection
