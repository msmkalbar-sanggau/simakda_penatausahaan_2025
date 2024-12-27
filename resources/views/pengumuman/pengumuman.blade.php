@extends('template.app')
@section('title', 'Pengumuman | SIMAKDA')

@section('content')

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">{{'Pengumuman'}}</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">{{'App'}}</a></li>
                    <li class="breadcrumb-item active">{{'Pengumuman'}}</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-xl-12">
                @php
                    $nomor = 0;
                @endphp
                @foreach ($data_pengumuman as $pemgumuman)
                    <div class="card">
                        <div class="card-body">
                            <b>{!!$pemgumuman->judul!!}</b><br />
                            {!!$pemgumuman->isi!!}
                            @if ($pemgumuman->file!='')
                            <br />
                            <a href="{{asset('template/assets/download/'.$pemgumuman->file)}}">Download</a>
                            @endif
                        </div>
                    </div>
                @endforeach
    </div>
</div>

<!-- apexcharts -->
<script src="{{asset('template/assets/libs/apexcharts/apexcharts.min.js')}}"></script>

<script src="{{asset('template/assets/js/pages/dashboard.init.js')}}"></script>
        <!-- apexcharts init -->
{{-- <script src="{{asset('template/assets/js/pages/apexcharts.init.js')}}"></script> --}}

@endsection
