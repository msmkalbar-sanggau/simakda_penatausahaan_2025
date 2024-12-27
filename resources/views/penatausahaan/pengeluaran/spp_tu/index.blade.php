@extends('template.app')
@section('title', 'SPP TU | SIMAKDA')
@section('content')
    <!-- style="float: right;" -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    {{-- <a href="{{ route('spp_tu.create') }}" id="tambah_spp_tu" class="btn btn-outline-info tomboltambah"
                        style="float: right;"><i class="bx bx-plus-circle"></i> Tambah Data</a> --}}
                </div>
                <div class="card-body">
                    <!-- <div class="col-12"> -->
                    <!-- <div class="row"> -->
                    <div class="card-header">
                        <!-- <div class="card-header"> -->
                        List SPP Tambah Uang (TU)
                        <!-- </div> -->
                    </div>
                    <!-- </div> -->
                    <!-- </div> -->
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="spp_tu" class="table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No SPP</th>
                                        <th>Tanggal</th>
                                        <th>Keterangan</th>
                                        <th style="width: 120px;text-align:center">Aksi</th>
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
            var table = '';
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            // Datatable
            // table = $('#spp_tu').DataTable({
            //     responsive: true,
            //     ordering: false,
            //     serverSide: true,
            //     processing: true,
            //     scrollY: "300px",
            //     scrollCollapse: true,
            //     lengthMenu: [5, 10, 25, 50, 100],
            //     ajax: {
            //         "url": "{{ route('spp_tu.list') }}",
            //         "type": "POST",
            //     },
            //     columns: [{
            //             data: 'DT_RowIndex',
            //             name: 'DT_RowIndex'
            //         },
            //         {
            //             data: 'no_spp',
            //             name: 'no_spp'
            //         },
            //         {
            //             data: 'tgl_spp',
            //             name: 'tgl_spp'
            //         },
            //         {
            //             data: 'keterangan',
            //             name: 'keterangan'
            //         },
            //         {
            //             data: 'action',
            //             name: 'action'
            //         },
            //     ]
            // });

            // Show modal
            $('.tomboltambah').click(function() {
                $('#myModal').modal('show');
            })
            // Button close
            $("#close-btn").on("click", function() {
                $('#myModal').modal('hide');
            })
        });

        // function DeleteData(no_spp) {
        //     var aksi = confirm("Yakin menghapus data " + no_spp + " ?");
        //     if (aksi == true) {
        //         $.ajax({
        //             url: "{{ route('spptu.hapusdata') }}",
        //             type: "POST",
        //             dataType: 'json',
        //             data: {
        //                 cno_spp: no_spp,
        //             },
        //             success: function(response) {
        //                 var response = response.message;
        //                 if (response == 1) {
        //                     alert("Data " + no_spp + " Berhasil dihapus !");
        //                 }
        //                 $('#spp_tu').DataTable().ajax.reload();
        //             }
        //         });
        //     }

        // }
    </script>
@endsection
