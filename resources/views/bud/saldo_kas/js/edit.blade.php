<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.select2-multiple').select2({
            placeholder: "Silahkan Pilih",
            theme: 'bootstrap-5'
        });

        $("input[data-type='currency']").on({
            keyup: function() {
                formatCurrency($(this));
            },
            blur: function() {
                formatCurrency($(this), "blur");
            }
        });

        $('#simpan_edit').on('click', function() {
            let nomor = document.getElementById('nomor').value;
            let ket = document.getElementById('uraian').value;
            let nil = angka(document.getElementById('nilai').value);
            let rek = document.getElementById('rek_bank').value;
            // alert(nil);
            // return;
            if (!ket) {
                alert('Silahkan isi Uraian!');
                return;
            }
            if (!rek) {
                alert('Silahkan isi Rekening Bank!');
                return;
            }
            if (!nil) {
                alert('Silahkan isi Nilai!');
                return;
            }

            let data = {
                nomor,
                ket,
                nil,
                rek
            };

            $('#simpan_edit').prop("disabled", true);
            $.ajax({
                url: "{{ route('saldo_kas.simpan_edit') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    data: data,
                },
                success: function(data) {
                    if (data.message == '1') {
                        alert('Data berhasil diubah!');
                        window.location.href =
                            "{{ route('saldo_kas.index') }}";
                    } else {
                        alert('Data gagal diubah!');
                        $('#simpan').prop('disabled', false);
                        return;
                    }
                }
            })
        });

    });

    function formatNumber(n) {
        // format number 1000000 to 1,234,567
        return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
    }

    function formatCurrency(input, blur) {
        // appends $ to value, validates decimal side
        // and puts cursor back in right position.

        // get input value
        var input_val = input.val();

        // don't validate empty input
        if (input_val === "") {
            return;
        }

        // original length
        var original_len = input_val.length;

        // initial caret position
        var caret_pos = input.prop("selectionStart");

        // check for decimal
        if (input_val.indexOf(".") >= 0) {

            // get position of first decimal
            // this prevents multiple decimals from
            // being entered
            var decimal_pos = input_val.indexOf(".");

            // split number by decimal point
            var left_side = input_val.substring(0, decimal_pos);
            var right_side = input_val.substring(decimal_pos);

            // add commas to left side of number
            left_side = formatNumber(left_side);

            // validate right side
            right_side = formatNumber(right_side);

            // On blur make sure 2 numbers after decimal
            if (blur === "blur") {
                right_side += "00";
            }

            // Limit decimal to only 2 digits
            right_side = right_side.substring(0, 2);

            // join number by .
            input_val = left_side + "." + right_side;

        } else {
            // no decimal entered
            // add commas to number
            // remove all non-digits
            input_val = formatNumber(input_val);
            input_val = input_val;

            // final formatting
            if (blur === "blur") {
                input_val += ".00";
            }
        }

        // send updated string to input
        input.val(input_val);

        // put caret back in the right position
        var updated_len = input_val.length;
        caret_pos = updated_len - original_len + caret_pos;
        input[0].setSelectionRange(caret_pos, caret_pos);
    }

    function angka(n) {
        let nilai = n.split(',').join('');
        return parseFloat(nilai) || 0;
    }

    function rupiah(n) {
        let n1 = n.split('.').join('');
        let rupiah = n1.split(',').join('.');
        return parseFloat(rupiah) || 0;
    }
</script>
