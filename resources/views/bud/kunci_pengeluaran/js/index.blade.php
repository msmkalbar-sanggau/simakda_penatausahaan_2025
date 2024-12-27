<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#kunci_pengeluaran').DataTable({
            responsive: true,
            ordering: false,
            serverSide: true,
            processing: true,
            lengthMenu: [
                [-1],
                ["All"]
            ],
            ajax: {
                "url": "{{ route('kunci_pengeluaran.load') }}",
                "type": "POST",
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: "text-center",
                },
                {
                    data: 'nm_skpd',
                    name: 'nm_skpd',
                },
                {
                    data: null,
                    name: 'kontrak',
                    className: "text-center",
                    render: function(data, type, row, meta) {
                        if (data.kd_skpd == '-') {
                            return `<a href="javascript:void(0);" onclick="kunci('${data.kd_skpd}','${data.kunci_kontrak}','kontrak')" class="btn btn-dark btn-md"><i class="fa fa-lock"></i> / <i class="fas fa-lock-open"></i></a>`
                        } else {
                            if (data.kunci_kontrak == 1) {
                                return `<a href="javascript:void(0);" onclick="kunci('${data.kd_skpd}','${data.kunci_kontrak}','kontrak')" class="btn btn-info btn-md"><i class="fa fa-lock" aria-hidden="true"></i></a>`
                            } else {
                                return `<a href="javascript:void(0);" onclick="kunci('${data.kd_skpd}','${data.kunci_kontrak}','kontrak')" class="btn btn-danger btn-md"><i class="fas fa-lock-open"></i></a>`
                            }
                        }
                    }
                },
                {
                    data: null,
                    name: 'penagihan',
                    className: "text-center",
                    render: function(data, type, row, meta) {
                        if (data.kd_skpd == '-') {
                            return `<a href="javascript:void(0);" onclick="kunci('${data.kd_skpd}','${data.kunci_tagih}','tagih')" class="btn btn-dark btn-md"><i class="fa fa-lock"></i> / <i class="fas fa-lock-open"></i></a>`
                        } else {
                            if (data.kunci_tagih == 1) {
                                return `<a href="javascript:void(0);" onclick="kunci('${data.kd_skpd}','${data.kunci_tagih}','tagih')" class="btn btn-info btn-md"><i class="fa fa-lock" aria-hidden="true"></i></a>`
                            } else {
                                return `<a href="javascript:void(0);" onclick="kunci('${data.kd_skpd}','${data.kunci_tagih}','tagih')" class="btn btn-danger btn-md"><i class="fas fa-lock-open"></i></a>`
                            }
                        }
                    }
                },
                {
                    data: null,
                    name: 'spp',
                    className: "text-center",
                    render: function(data, type, row, meta) {
                        if (data.kd_skpd == '-') {
                            return `<a href="javascript:void(0);" onclick="kunci('${data.kd_skpd}','${data.kunci_spp}','spp')" class="btn btn-dark btn-md"><i class="fa fa-lock"></i> / <i class="fas fa-lock-open"></i></a>`
                        } else {
                            if (data.kunci_spp == 1) {
                                return `<a href="javascript:void(0);" onclick="kunci('${data.kd_skpd}','${data.kunci_spp}','spp')" class="btn btn-info btn-md"><i class="fa fa-lock" aria-hidden="true"></i></a>`
                            } else {
                                return `<a href="javascript:void(0);" onclick="kunci('${data.kd_skpd}','${data.kunci_spp}','spp')" class="btn btn-danger btn-md"><i class="fas fa-lock-open"></i></a>`
                            }
                        }
                    }
                },
                {
                    data: null,
                    name: 'spp_gu',
                    className: "text-center",
                    render: function(data, type, row, meta) {
                        if (data.kd_skpd == '-') {
                            return `<a href="javascript:void(0);" onclick="kunci('${data.kd_skpd}','${data.kunci_spp_gu}','spp_gu')" class="btn btn-dark btn-md"><i class="fa fa-lock"></i> / <i class="fas fa-lock-open"></i></a>`
                        } else {
                            if (data.kunci_spp_gu == 1) {
                                return `<a href="javascript:void(0);" onclick="kunci('${data.kd_skpd}','${data.kunci_spp_gu}','spp_gu')" class="btn btn-info btn-md"><i class="fa fa-lock" aria-hidden="true"></i></a>`
                            } else {
                                return `<a href="javascript:void(0);" onclick="kunci('${data.kd_skpd}','${data.kunci_spp_gu}','spp_gu')" class="btn btn-danger btn-md"><i class="fas fa-lock-open"></i></a>`
                            }
                        }
                    }
                },
                {
                    data: null,
                    name: 'spp_ls',
                    className: "text-center",
                    render: function(data, type, row, meta) {
                        if (data.kd_skpd == '-') {
                            return `<a href="javascript:void(0);" onclick="kunci('${data.kd_skpd}','${data.kunci_spp_ls}','spp_ls')" class="btn btn-dark btn-md"><i class="fa fa-lock"></i> / <i class="fas fa-lock-open"></i></a>`
                        } else {
                            if (data.kunci_spp_ls == 1) {
                                return `<a href="javascript:void(0);" onclick="kunci('${data.kd_skpd}','${data.kunci_spp_ls}','spp_ls')" class="btn btn-info btn-md"><i class="fa fa-lock" aria-hidden="true"></i></a>`
                            } else {
                                return `<a href="javascript:void(0);" onclick="kunci('${data.kd_skpd}','${data.kunci_spp_ls}','spp_ls')" class="btn btn-danger btn-md"><i class="fas fa-lock-open"></i></a>`
                            }
                        }
                    }
                },
                {
                    data: null,
                    name: 'spp_gu_nihil',
                    className: "text-center",
                    render: function(data, type, row, meta) {
                        if (data.kd_skpd == '-') {
                            return `<a href="javascript:void(0);" onclick="kunci('${data.kd_skpd}','${data.kunci_spp_gu_nihil}','spp_gu_nihil')" class="btn btn-dark btn-md"><i class="fa fa-lock"></i> / <i class="fas fa-lock-open"></i></a>`
                        } else {
                            if (data.kunci_spp_gu_nihil == 1) {
                                return `<a href="javascript:void(0);" onclick="kunci('${data.kd_skpd}','${data.kunci_spp_gu_nihil}','spp_gu_nihil')" class="btn btn-info btn-md"><i class="fa fa-lock" aria-hidden="true"></i></a>`
                            } else {
                                return `<a href="javascript:void(0);" onclick="kunci('${data.kd_skpd}','${data.kunci_spp_gu_nihil}','spp_gu_nihil')" class="btn btn-danger btn-md"><i class="fas fa-lock-open"></i></a>`
                            }
                        }
                    }
                },
                {
                    data: null,
                    name: 'spm',
                    className: "text-center",
                    render: function(data, type, row, meta) {
                        if (data.kd_skpd == '-') {
                            return `<a href="javascript:void(0);" onclick="kunci('${data.kd_skpd}','${data.kunci_spm}','spm')" class="btn btn-dark btn-md"><i class="fa fa-lock"></i> / <i class="fas fa-lock-open"></i></a>`
                        } else {
                            if (data.kunci_spm == 1) {
                                return `<a href="javascript:void(0);" onclick="kunci('${data.kd_skpd}','${data.kunci_spm}','spm')" class="btn btn-info btn-md"><i class="fa fa-lock" aria-hidden="true"></i></a>`
                            } else {
                                return `<a href="javascript:void(0);" onclick="kunci('${data.kd_skpd}','${data.kunci_spm}','spm')" class="btn btn-danger btn-md"><i class="fas fa-lock-open"></i></a>`
                            }
                        }
                    }
                },
                {
                    data: null,
                    name: 'jurnal',
                    className: "text-center",
                    render: function(data, type, row, meta) {
                        if (data.kd_skpd == '-') {
                            return `<a href="javascript:void(0);" onclick="kunci('${data.kd_skpd}','${data.kunci_jurnal}','spm')" class="btn btn-dark btn-md"><i class="fa fa-lock"></i> / <i class="fas fa-lock-open"></i></a>`
                        } else {
                            if (data.kunci_jurnal == 1) {
                                return `<a href="javascript:void(0);" onclick="kunci('${data.kd_skpd}','${data.kunci_jurnal}','spm')" class="btn btn-info btn-md"><i class="fa fa-lock" aria-hidden="true"></i></a>`
                            } else {
                                return `<a href="javascript:void(0);" onclick="kunci('${data.kd_skpd}','${data.kunci_jurnal}','spm')" class="btn btn-danger btn-md"><i class="fas fa-lock-open"></i></a>`
                            }
                        }
                    }
                },
            ],
        });

        $('.kunci').on('click', function() {
            let tabel = $('#kunci_pengeluaran').DataTable();
            let nilai = $(this).data("nilai");
            let kd_skpd = $('#kd_skpd').val();
            let jenis = $('#jenis').val();


            $.ajax({
                url: "{{ route('kunci_pengeluaran.kunci') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: kd_skpd,
                    jenis: jenis,
                    kunci: nilai
                },
                success: function(data) {
                    if (data.message == 1) {
                        tabel.ajax.reload();
                        $('#kunci_semua').modal('hide');
                    }
                }
            });
        });
    });

    function cetak(no_kas, no_sts, kd_skpd) {
        let url = new URL("{{ route('penerimaan_kas.cetak') }}");
        let searchParams = url.searchParams;
        searchParams.append("no_kas", no_kas);
        searchParams.append("no_sts", no_sts);
        searchParams.append("kd_skpd", kd_skpd);
        window.open(url.toString(), "_blank");
    }

    function kunci(kd_skpd, kunci, jenis) {
        if (kd_skpd == '-') {
            $('#kd_skpd').val(kd_skpd);
            $('#jenis').val(jenis);
            $('#kunci_semua').modal('show');
        } else {
            let tabel = $('#kunci_pengeluaran').DataTable();
            $.ajax({
                url: "{{ route('kunci_pengeluaran.kunci') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_skpd: kd_skpd,
                    kunci: kunci,
                    jenis: jenis,
                },
                success: function(data) {
                    if (data.message == 1) {
                        tabel.ajax.reload();
                    }
                }
            });
        }

    }
</script>
