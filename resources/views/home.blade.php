@extends('template.app')
@section('title', 'Dashboard | SIMAKDA')

@section('content')

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">{{ 'Dashboard' }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{ 'App' }}</a></li>
                        <li class="breadcrumb-item active">{{ 'Dashboard' }}</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="float-end mt-2">
                        <div id="total-revenue-chart" data-colors='["--bs-primary"]'></div>
                    </div>
                    <div>
                        <h6 class="mb-1 mt-1 ">Rp<span>{{ nilai($data_pendapatan->pendapatan) }}</span></h6>
                        <p class="text-mute mb-0">Pendapatan</p>
                    </div>

                </div>
            </div>
        </div> <!-- end col-->

        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body ">
                    <div class="float-end mt-2">
                        <div id="growth-chart" data-colors='["--bs-warning"]'></div>

                    </div>
                    <div>
                        <h6 class="mb-1 mt-1 ">Rp<span>{{ rupiah($data_belanja->belanja) }}</span></h6>
                        <p class="text-mute mb-0">Belanja</p>
                    </div>

                </div>
            </div>
        </div> <!-- end col-->

        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body ">
                    <div class="float-end mt-2">
                        <div id="total-revenue-chart2" data-colors='["--bs-primary"]'></div>
                    </div>
                    <div>
                        <h6 class="mb-1 mt-1 ">Rp<span>{{ rupiah($data_pem_terima->pem_terima) }}</span></h6>
                        <p class="text-mute mb-0">Pembiayaan Penerimaan</p>
                    </div>

                </div>
            </div>
        </div> <!-- end col-->

        <div class="col-md-6 col-xl-3">

            <div class="card">
                <div class="card-body">
                    <div class="float-end mt-2 ">
                        <div id="total-revenue-chart4" data-colors='["--bs-warning"]'> </div>

                    </div>
                    <div>
                        <h6 class="mb-1 mt-1 ">Rp<span>{{ rupiah($data_pem_keluar->pem_keluar) }}</span></h6>
                        <p class="text-mute mb-0">Pembiayaan Pengeluaran</p>
                    </div>

                </div>
            </div>
        </div> <!-- end col-->
    </div> <!-- end row-->

    {{-- ALERT START --}}
    @php
        $pengumuman1 = pengumuman_top1();
    @endphp
    @if ($cekpengumuman->aktif >='1')
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa fa-info-circle"> {{ $pengumuman1->judul }}</i>

        <p>{!! html_entity_decode($pengumuman1->isi, ENT_QUOTES, 'UTF-8') !!}</p>
        @isset($pengumuman1->file)
            <p><a href="{{ asset('pengumuman/' . $pengumuman1->file) }}" target="_blank">Download</a></p>
        @endisset
        <button type="button" class="btn btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @else
    <div>
    </div>
    @endif

    {{-- ALERT END --}}
    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">{{ 'Pagu Anggaran' }}</h4>
                    <div id="chartdiv"></div>
                    {{-- <div id="pie_chart" class="apex-charts" dir="ltr"></div> --}}
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">{{ 'Pengeluaran' }}</h4>
                    <div id="chartdiv2"></div>
                    {{-- <div id="donut_chart" class="apex-charts"  dir="ltr"></div> --}}
                </div>
            </div>
        </div>
    </div>

    <!-- apexcharts -->
    <script src="{{ asset('template/assets/libs/apexcharts/apexcharts.min.js') }}"></script>

    <script src="{{ asset('template/assets/js/pages/dashboard.init.js') }}"></script>
    <!-- apexcharts init -->

    {{-- AMCHART --}}
    <style>
        #chartdiv {
            width: 100%;
            height: 300px;
        }
    </style>

    <!-- Resources -->
    <script src="{{ asset('js/amchart/index.js') }}"></script>
    <script src="{{ asset('js/amchart/percent.js') }}"></script>
    <script src="{{ asset('js/amchart/xy.js') }}"></script>
    <script src="{{ asset('js/amchart/Animated.js') }}"></script>

    <!-- Chart code -->
    <script>
        am5.ready(function() {
            // Data
            let pendapatan = parseFloat("{{ $data_pendapatan->pendapatan }}");
            let belanja = parseFloat("{{ $data_belanja->belanja }}");
            let pem_terima = parseFloat("{{ $data_pem_terima->pem_terima }}");
            let pem_keluar = parseFloat("{{ $data_pem_keluar->pem_keluar }}");


            // Define data for each year
            var chartData = {
                "2024": [{
                        sector: "Pendapatan",
                        size: pendapatan
                    },
                    {
                        sector: "Belanja",
                        size: belanja
                    },
                    {
                        sector: "Penerimaan Pembiayaan",
                        size: pem_terima
                    },
                    {
                        sector: "Pengeluaran Pembiayaan",
                        size: pem_keluar
                    }
                ]
            };

            // Create root element
            // https://www.amcharts.com/docs/v5/getting-started/#Root_element
            var root = am5.Root.new("chartdiv");


            // Set themes
            // https://www.amcharts.com/docs/v5/concepts/themes/
            root.setThemes([
                am5themes_Animated.new(root)
            ]);


            // Create chart
            // https://www.amcharts.com/docs/v5/charts/percent-charts/pie-chart/
            var chart = root.container.children.push(am5percent.PieChart.new(root, {
                innerRadius: 70,
                //   layout: root.verticalLayout
            }));


            // Create series
            // https://www.amcharts.com/docs/v5/charts/percent-charts/pie-chart/#Series
            var series = chart.series.push(am5percent.PieSeries.new(root, {
                valueField: "size",
                categoryField: "sector"
            }));


            // Set data
            // https://www.amcharts.com/docs/v5/charts/percent-charts/pie-chart/#Setting_data

            series.data.setAll([{
                    sector: "Pendapatan",
                    size: pendapatan
                },
                {
                    sector: "Belanja",
                    size: belanja
                },
                {
                    sector: "Penerimaan Pembiayaan",
                    size: pem_terima
                },
                {
                    sector: "Pengeluaran Pembiayaan",
                    size: pem_keluar
                }
            ]);


            // Play initial series animation
            // https://www.amcharts.com/docs/v5/concepts/animations/#Animation_of_series
            series.appear(1000, 100);


            // Add label
            var label = root.tooltipContainer.children.push(am5.Label.new(root, {
                x: am5.p50,
                y: am5.p50,
                centerX: am5.p50,
                centerY: am5.p50,
                fill: am5.color(0x000000),
                fontSize: 20
            }));


            // Animate chart data
            var currentYear = '2024';

            function getCurrentData() {
                var data = chartData[currentYear];
                //   currentYear++;
                //   if (currentYear > 2014)
                // currentYear = 1995;
                return data;
            }

            function loop() {
                label.set("text", currentYear);
                var data = getCurrentData();
                for (var i = 0; i < data.length; i++) {
                    series.data.setIndex(i, data[i]);
                }
                chart.setTimeout(loop, 4000);
            }

            loop();

        }); // end am5.ready()
    </script>

    <!-- HTML -->


    {{-- AMCHART --}}

    <style>
        #chartdiv2 {
            width: 100%;
            height: 300px;
        }
    </style>


    <!-- Chart code -->
    <script>
        am5.ready(function() {
            let penagihan = parseFloat("{{ $data_penagihan->penagihan }}");
            let spp = parseFloat("{{ $data_spp->spp }}");
            let spm = parseFloat("{{ $data_spm->spm }}");
            let sp2d = parseFloat("{{ $data_sp2d->sp2d }}");
            // Create root element
            // https://www.amcharts.com/docs/v5/getting-started/#Root_element
            var root = am5.Root.new("chartdiv2");


            // Set themes
            // https://www.amcharts.com/docs/v5/concepts/themes/
            root.setThemes([
                am5themes_Animated.new(root)
            ]);


            // Create chart
            // https://www.amcharts.com/docs/v5/charts/xy-chart/
            var chart = root.container.children.push(am5xy.XYChart.new(root, {
                panX: false,
                panY: false,
                wheelX: "none",
                wheelY: "none"
            }));

            // We don't want zoom-out button to appear while animating, so we hide it
            chart.zoomOutButton.set("forceHidden", true);


            // Create axes
            // https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
            var yRenderer = am5xy.AxisRendererY.new(root, {
                minGridDistance: 30
            });

            var yAxis = chart.yAxes.push(am5xy.CategoryAxis.new(root, {
                maxDeviation: 0,
                categoryField: "network",
                renderer: yRenderer,
                tooltip: am5.Tooltip.new(root, {
                    themeTags: ["axis"]
                })
            }));

            var xAxis = chart.xAxes.push(am5xy.ValueAxis.new(root, {
                maxDeviation: 0,
                min: 0,
                extraMax: 0.1,
                renderer: am5xy.AxisRendererX.new(root, {})
            }));


            // Add series
            // https://www.amcharts.com/docs/v5/charts/xy-chart/series/
            var series = chart.series.push(am5xy.ColumnSeries.new(root, {
                name: "Series 1",
                xAxis: xAxis,
                yAxis: yAxis,
                valueXField: "value",
                categoryYField: "network",
                tooltip: am5.Tooltip.new(root, {
                    pointerOrientation: "left",
                    labelText: "{valueX}"
                })
            }));


            // Rounded corners for columns
            series.columns.template.setAll({
                cornerRadiusTR: 5,
                cornerRadiusBR: 5
            });

            // Make each column to be of a different color
            series.columns.template.adapters.add("fill", function(fill, target) {
                return chart.get("colors").getIndex(series.columns.indexOf(target));
            });

            series.columns.template.adapters.add("stroke", function(stroke, target) {
                return chart.get("colors").getIndex(series.columns.indexOf(target));
            });

            // Set data
            var data = [{
                    "network": "Penagihan",
                    "value": penagihan
                },
                {
                    "network": "SPP",
                    "value": spp
                },
                {
                    "network": "SPM",
                    "value": spm
                },
                {
                    "network": "SP2D",
                    "value": sp2d
                }
            ];

            yAxis.data.setAll(data);
            series.data.setAll(data);
            sortCategoryAxis();

            // Get series item by category
            function getSeriesItem(category) {
                for (var i = 0; i < series.dataItems.length; i++) {
                    var dataItem = series.dataItems[i];
                    if (dataItem.get("categoryY") == category) {
                        return dataItem;
                    }
                }
            }

            chart.set("cursor", am5xy.XYCursor.new(root, {
                behavior: "none",
                xAxis: xAxis,
                yAxis: yAxis
            }));


            // Axis sorting
            function sortCategoryAxis() {

                // Sort by value
                series.dataItems.sort(function(x, y) {
                    return x.get("valueX") - y.get("valueX"); // descending
                    //return y.get("valueY") - x.get("valueX"); // ascending
                })

                // Go through each axis item
                am5.array.each(yAxis.dataItems, function(dataItem) {
                    // get corresponding series item
                    var seriesDataItem = getSeriesItem(dataItem.get("category"));

                    if (seriesDataItem) {
                        // get index of series data item
                        var index = series.dataItems.indexOf(seriesDataItem);
                        // calculate delta position
                        var deltaPosition = (index - dataItem.get("index", 0)) / series.dataItems.length;
                        // set index to be the same as series data item index
                        dataItem.set("index", index);
                        // set deltaPosition instanlty
                        dataItem.set("deltaPosition", -deltaPosition);
                        // animate delta position to 0
                        dataItem.animate({
                            key: "deltaPosition",
                            to: 0,
                            duration: 1000,
                            easing: am5.ease.out(am5.ease.cubic)
                        })
                    }
                });

                // Sort axis items by index.
                // This changes the order instantly, but as deltaPosition is set,
                // they keep in the same places and then animate to true positions.
                yAxis.dataItems.sort(function(x, y) {
                    return x.get("index") - y.get("index");
                });
            }


            // update data with random values each 1.5 sec
            // setInterval(function () {
            //   updateData();
            // }, 1500)


            // Make stuff animate on load
            // https://www.amcharts.com/docs/v5/concepts/animations/
            series.appear(1000);
            chart.appear(1000, 100);

        }); // end am5.ready()
    </script>

@endsection
