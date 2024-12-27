@extends('template.app')
@section('title', 'Peran | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    {{-- <a href="{{ route('peran.create') }}" class="btn btn-primary" style="float: right;">Tambah</a> --}}
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="peran" class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>User</th>
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

            $('#peran').DataTable({
                responsive: true,
                ordering: false,
                serverSide: true,
                processing: true,
                lengthMenu: [5, 10],
                ajax: {
                    "url": "{{ route('skpd_pengguna.load_data') }}",
                    "type": "POST",
                },
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: "text-center",
                }, {
                    data: 'nama',
                    name: 'nama',
                }, {
                    data: 'aksi',
                    name: 'aksi',
                    className: 'text-center'
                }, ],
            });
        });

        function deleteData(id, role) {
            if (id == role) {
                alert('Dilarang Hapus Data Sesuai Role Sendiri!!');
                return;
            }
            var r = confirm("Hapus?");
            if (r == true) {
                let url = '{{ route('peran.destroy', ':id') }}';
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
