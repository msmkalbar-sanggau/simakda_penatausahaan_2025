@extends('template.app')
@section('title', 'Peran | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <a href="{{ route('peran.create') }}" class="btn btn-primary" style="float: right;">Tambah</a>
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="peran" class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Kode</th>
                                        <th>Nama Peran</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @foreach ($daftar_peran as $key => $hak_akses)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $hak_akses->role }}</td>
                                            <td>{{ $hak_akses->nama_role }}</td>
                                            <td>
                                                <a href="{{ route('peran.show', $hak_akses->id) }}"
                                                    class="btn btn-primary btn-sm"><i class="fas fa-info-circle"></i></a>
                                                <a href="{{ route('peran.edit', $hak_akses->id) }}"
                                                    class="btn btn-warning btn-sm"><i class="fas fa-info-circle"></i></a>
                                                <a href="javascript:void(0);" onclick="deleteData({{ $hak_akses->id }});"
                                                    class="btn btn-danger btn-sm" id="delete"
                                                    data-id={{ $hak_akses->id }}><i class="fas fa-trash-alt"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach --}}
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
                    "url": "{{ route('peran.load_data') }}",
                    "type": "POST",
                },
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: "text-center",
                }, {
                    data: 'role',
                    name: 'role',
                }, {
                    data: 'nama_role',
                    name: 'nama_role',
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
