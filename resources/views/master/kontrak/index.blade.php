@extends('template.app')
@section('title', 'Kontrak | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">{{ 'Kontrak' }}</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Apps</a></li>
                                <li class="breadcrumb-item active">{{ 'Kontrak' }}</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->
            <div class="card">
                <div class="card-header">
                    <a href="{{ route('kontrak.create') }}" class="btn btn-primary" style="float: right;"
                        {{ $kunci == 1 ? 'hidden' : '' }}>Tambah</a>
                </div>
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="kontrak" class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>No Kontrak</th>
                                        <th>Nilai Kontrak</th>
                                        <th>Nama Pekerjaan</th>
                                        <th>Pelaksana Pekerjaan</th>
                                        <th>Tanggal Kontrak</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @foreach ($data_kontrak as $data)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $data->no_kontrak }}</td>
                                            <td>{{ $data->nilai }}</td>
                                            <td>{{ $data->nm_kerja }}</td>
                                            <td>{{ $data->nmpel }}</td>
                                            <td>{{ \Carbon\Carbon::parse($data->tgl_kerja)->locale('id')->isoFormat('D MMMM Y') }}
                                            </td>
                                            <td>
                                                <a href="{{ route('kontrak.show', $data->id) }}"
                                                    class="btn btn-info btn-sm"><i class="fas fa-info-circle"></i></a>
                                                <a href="{{ route('kontrak.edit', $data->id) }}"
                                                    class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                                <a href="javascript:void(0);" onclick="deleteData({{ $data->id }});"
                                                    class="btn btn-danger btn-sm" id="delete"
                                                    data-id={{ $data->id }}><i class="fas fa-trash-alt"></i></a>
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

            $('#kontrak').DataTable({
                responsive: true,
                ordering: false,
                serverSide: true,
                processing: true,
                lengthMenu: [5, 10],
                ajax: {
                    "url": "{{ route('kontrak.load_data') }}",
                    "type": "POST",
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: "text-center",
                    },
                    {
                        data: 'no_kontrak',
                        name: 'no_kontrak',
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
                        data: null,
                        name: 'nm_kerja',
                        render: function(data, type, row, meta) {
                            return data.nm_kerja.substr(0, 10) + '.....';
                        }
                    },
                    {
                        data: 'nmpel',
                        name: 'nmpel',
                    },
                    {
                        data: 'tgl_kerja',
                        name: 'tgl_kerja',
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

        function deleteData(no_kontrak) {
            let tanya = confirm('Apakah anda yakin untuk menghapus data dengan Nomor : ' + no_kontrak);
            if (tanya == true) {
                $.ajax({
                    url: "{{ route('kontrak.hapus') }}",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        no_kontrak: no_kontrak,
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
