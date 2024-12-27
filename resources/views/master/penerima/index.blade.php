@extends('template.app')
@section('title', 'Penerima | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <a href="{{ route('penerima.create') }}" class="btn btn-primary" style="float: right;">Tambah</a>
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="penerima" class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>No Rekening</th>
                                        <th>Nama Rekening</th>
                                        <th>Bank</th>
                                        <th>NPWP</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @foreach ($data_penerima as $key => $data)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $data->rekening }}</td>
                                            <td>{{ $data->nm_rekening }}</td>
                                            <td>{{ $data->nm_bank }}</td>
                                            <td>{{ $data->npwp }}</td>
                                            <td>{{ $data->keterangan }}</td>
                                            <td>
                                                <a href="{{ route('penerima.show', $data->id) }}"
                                                    class="btn btn-info btn-sm"><i class="fas fa-info-circle"></i></a>
                                                <a href="{{ route('penerima.edit', $data->id) }}"
                                                    class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                                <a href="javascript:void(0);" onclick="deleteData({{ $data->id }})"
                                                    class="btn btn-danger btn-sm" id="delete"><i
                                                        class="fas fa-trash-alt"></i></a>
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

            $('#penerima').DataTable({
                responsive: true,
                ordering: false,
                serverSide: true,
                processing: true,
                lengthMenu: [5, 10],
                ajax: {
                    "url": "{{ route('penerima.load_data') }}",
                    "type": "POST",
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: "text-center",
                    },
                    {
                        data: 'rekening',
                        name: 'rekening',
                    },
                    {
                        data: 'nm_rekening',
                        name: 'nm_rekening',
                    },
                    {
                        data: 'nm_bank',
                        name: 'nm_bank',
                    },
                    {
                        data: 'npwp',
                        name: 'npwp',
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan',
                        // render: function(data, type, row, meta) {
                        //     return data.keterangan.substr(0, 10) + '.....';
                        // }
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
            var r = confirm("Hapus?");
            if (r == true) {
                $.ajax({
                    url: "{{ route('penerima.cekPenerima') }}",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        id: id,
                    },
                    success: function(data) {
                        if (data > 0) {
                            Swal.fire({
                                position: 'top-end',
                                icon: 'warning',
                                title: 'Data Penerima telah digunakan di kontrak',
                                width: 300,
                                height: 300,
                                showConfirmButton: false,
                                timer: 1500
                            })
                        } else {
                            let url = '{{ route('penerima.destroy', ':id') }}';
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
                        }
                    }
                })
            } else {
                return false;
            }
        }
    </script>
@endsection
