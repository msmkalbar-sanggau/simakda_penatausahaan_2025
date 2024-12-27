@extends('template.app')
@section('title', 'Hak Akses | SIMAKDA')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="hak_akses" class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th style="width: 5px">No.</th>
                                        <th>Nama</th>
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

            $('#hak_akses').DataTable({
                responsive: true,
                ordering: false,
                serverSide: true,
                processing: true,
                lengthMenu: [5, 10],
                ajax: {
                    "url": "{{ route('hak_akses.load_data') }}",
                    "type": "POST",
                },
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: "text-center",
                }, {
                    data: 'display_name',
                    name: 'display_name',
                }, ],
            });
        });
    </script>
@endsection
