<?php $this->extend('template') ?>

<?php $this->section('content') ?>
<div class="row mt--2">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <div class="d-flex col-sm-2">
                        <h2 class="card-title">Laporan</h2>
                        <div id="pesanError" class="badge badge-danger"></div>
                    </div>
                    <div class="d-flex col-sm-2 text-center">
                        <input type="checkbox" class="form-check-input" id="ringkasan" name="ringkasan" onChange="tampilkan()">
                        <label for="ringkasan" class="form-check-label badge badge-info">Ringkasan</label>
                    </div>
                    <div class="d-flex col-sm-4">
                        <div class="form-group">
                            <label for="pillInput" class="badge badge-info">Dari tgl</label>
                            <input type="date" class="form-control input-pill" id="tanggalMulai" onChange="tampilkan()" placeholder="Rp">
                        </div>
                    </div>
                    <div class="d-flex col-sm-4">
                        <div class="form-group">
                            <label for="pillInput" class="badge badge-info">Sampai tgl</label>
                            <input type="date" class="form-control input-pill" onChange="tampilkan()" id="tanggalSelesai" placeholder="Rp">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive" id="tempatTabel">

            </div>
        </div>
    </div>
</div>

<script>
    settanggal()
    tampilkan()

    function settanggal() {
        var now = new Date();
        var day = ("0" + now.getDate()).slice(-2);
        var month = ("0" + (now.getMonth() + 1)).slice(-2);
        var today = now.getFullYear() + "-" + (month) + "-" + (day);

        $("#tanggalMulai").val(today)
        $("#tanggalSelesai").val(today)
    }

    function tampilkan() {
        // tampilkanChart()
        var tanggalMulai = $("#tanggalMulai").val()
        var tanggalSelesai = $("#tanggalSelesai").val()

        if (tanggalMulai > tanggalSelesai) {
            $("#pesanError").html("Tanggal Mulai tidak Boleh Melebihi tanggal Selesai")
        } else {
            $("#pesanError").html("")
            $("#tombolProses").html('<i class="fa fa-spinner fa-pulse"></i> Memproses...')

            var keuntungan = 0;
            var totalKeuntungan = 0;
            var ringkas = 0;
            if ($("#ringkasan").is(":checked")) {
                ringkas = 1
                var tabel = '<table id="tabelLaporan" class="display table table-striped table-hover" ><thead><tr><th>NO</th><th>NAMA</th><th>PENAMBAHAN</th><th>PENGURANGAN</th><th>STOK</th><th>SATUAN</th></tr></thead><tbody>'
            } else {
                ringkas = 0
                var tabel = '<table id="tabelLaporan" class="display table table-striped table-hover" ><thead><tr><th>NO</th><th>STATUS</th><th>TANGGAL</th><th>NAMA</th><th>PENYESUAIAN</th><th>TERKINI</th><th>SATUAN</th><th>KETERANGAN</th><th>KARYAWAN</th></tr></thead><tbody>'
            }

            $.ajax({
                url: '<?= base_url() ?>/laporan/dataBarangMasuk',
                method: 'post',
                data: "tanggalMulai=" + tanggalMulai + "&tanggalSelesai=" + tanggalSelesai + "&ringkas=" + ringkas,
                dataType: 'json',
                success: function(data) {
                    if ($("#ringkasan").is(":checked")) {
                        for (let i = 0; i < data.length; i++) {
                            if (data[i].barangMasuk != 0 || data[i].barangKeluar != 0) {
                                tabel += '<tr>'
                                tabel += '<td>' + (i + 1) + '</td>'
                                tabel += '<td>' + data[i].namaBarang + '</td>'
                                tabel += '<td>' + data[i].barangMasuk + '</td>'
                                tabel += '<td>' + data[i].barangKeluar + '</td>'
                                tabel += '<td>' + data[i].stok + '</td>'
                                tabel += '<td>' + data[i].satuan + '</td>'
                                tabel += '</tr>'
                            }
                        }
                    } else {
                        for (let i = 0; i < data.length; i++) {
                            tabel += '<tr>'
                            tabel += '<td>' + (i+1) + '</td>'
                            tabel += '<td>' + data[i].status + '</td>'
                            tabel += '<td>' + data[i].tanggal + '</td>'
                            tabel += '<td>' + data[i].namaBarang + '</td>'
                            tabel += '<td>' + data[i].penyesuaian + '</td>'
                            tabel += '<td>' + data[i].stokTerkini + '</td>'
                            tabel += '<td>' + data[i].satuan + '</td>'
                            tabel += '<td>' + data[i].keterangan + '</td>'
                            tabel += '<td>' + data[i].karyawan + '</td>'
                            tabel += '</tr>'
                        }
                    }
                    tabel += '</tbody></table>'
                    $("#tempatTabel").html(tabel)
                    
                    $('#tabelLaporan').DataTable({
                        "pageLength": 10,
                    });
                    $("#tombolProses").html('Proses')
                }
            });

        }
    }

    function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
    }
</script>
<?php $this->endSection() ?>