<?php $this->extend('template') ?>

<?php $this->section('content') ?>
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Tambah Barang</h4>
            </div>
            <div class="card-body">
                <form>
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">Nama Barang</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="namaBarang" name="namaBarang">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputPassword3" class="col-sm-2 col-form-label">Stok</label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control" id="stok" name="stok">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">Satuan</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="satuan" name="satuan">
                        </div>
                    </div>
                </form>
                <div class="form-group row">
                    <div class="col-sm-12 text-center">
                        <button class="btn btn-info" onclick="tambah()" id="tambah">Tambah</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Daftar Barang</h4>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead class=" text-info">
                        <th>
                            ID
                        </th>
                        <th>
                            Nama Barang
                        </th>
                        <th>
                            Stok
                        </th>
                        <th>
                            Satuan
                        </th>
                        <th>
                            Hapus
                        </th>
                    </thead>
                    <tbody id="tabelBarang">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalHapus" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus Barang</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" value="" id="idHapus" name="idHapus">
                <p>Apakah anda yakin ingin menghapus <b id="detailHapus">....</b> ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="hapus()" class="btn btn-info">Hapus</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>

<script>
    muatData()

    function muatData() {
        $("#tambah").html('<i class="fa fa-spinner fa-pulse"></i> Memproses...')
        $.ajax({
            url: '<?= base_url() ?>/barang/muatData',
            method: 'post',
            dataType: 'json',
            success: function(data) {
                var tabel = ''
                for (let i = 0; i < data.length; i++) {
                    tabel += "<tr><td>" + data[i].id + "</td><td>" + data[i].namaBarang + "</td><td>" + data[i].stok + "</td><td>" + data[i].satuan + "</td><td><a href='#' id='hapus" + data[i].id + "' onclick='tryHapus(" + data[i].id + ", \"" +data[i].namaBarang + "\")' ><i class='fa fa-trash'></i></a></td></tr>"

                }
                if (!tabel) {
                    tabel = '<td class="text-center" colspan="2">Data Masih kosong :)</td>'
                }
                $("#tabelBarang").html(tabel)

                $("#tambah").html('Tambah')
            }
        });
    }

    function tambah() {
        if ($("#namaBarang").val() == "") {
            $("#namaBarang").focus();
        } else if ($("#satuan").val() == "") {
            $("#satuan").focus();
        } else {
            $.ajax({
                type: 'POST',
                data: 'namaBarang=' + $("#namaBarang").val() + '&stok=' + $("#stok").val()+ '&satuan=' + $("#satuan").val(),
                url: '<?= base_url() ?>/barang/tambah',
                dataType: 'json',
                success: function(data) {
                    $("#namaBarang").val("");
                    $("#stok").val("");
                    $("#satuan").val("");
                    muatData();
                }
            });
        }
    }

    function tryHapus(id, nama) {
        $("#hapus" + id).html('<i class="fa fa-spinner fa-pulse"></i>')
        $("#idHapus").val(id)
        $("#detailHapus").html(nama + " (" + id + ") ")
        $("#hapus" + id).html('<i class="fa fa-trash"></i>')
        $("#modalHapus").modal('show')
    }

    function hapus() {
        $("#hapus").html('<i class="fa fa-spinner fa-pulse"></i> Memproses..')
        var id = $("#idHapus").val()
        $.ajax({
            url: '<?= base_url() ?>/barang/hapus',
            method: 'post',
            data: "id=" + id,
            dataType: 'json',
            success: function(data) {
                $("#idHapus").val("")
                $("#detailHapus").html("")
                $("#modalHapus").modal('hide')
                $("#hapus").html('Hapus')
                muatData()
            }
        });
    }
</script>
<?php $this->endSection() ?>