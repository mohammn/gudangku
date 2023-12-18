<?php

namespace App\Controllers;

use App\Models\BarangKeluarModel;
use App\Models\BarangModel;

class BarangKeluar extends BaseController
{
    public function __construct()
    {
        $this->BarangKeluarModel = new BarangKeluarModel();
        $this->BarangModel = new BarangModel();
    }
    public function index()
    {
        if (!session()->get('nama')) {
            return redirect()->to(base_url() . "/login");
        }
        $data["barang"] = $this->BarangModel->where('hapus', 0)->findAll();
        return view('barangKeluar', $data);
    }
    public function muatData()
    {
        echo json_encode($this->BarangKeluarModel->findAll());
    }

    public function barangById(){
        echo json_encode($this->BarangModel->where('id', $this->request->getPost("id"))->first());
    }

    public function tambah()
    {
        $stokTerikini = $this->request->getPost("stokTerkini") - $this->request->getPost("penguranganStok");
        $data = [
            "idBarang" => $this->request->getPost("idBarang"),
            "namaBarang" => $this->request->getPost("namaBarang"),
            "penguranganStok" => $this->request->getPost("penguranganStok"),
            "stokTerkini" => $stokTerikini,
            "satuan" => $this->request->getPost("satuan"),
            "keterangan" => $this->request->getPost("keterangan"),
            "karyawan" => session()->get('nama')
        ];

        $this->BarangKeluarModel->save($data);

        $data = [
            "stok" => $stokTerikini
        ];
        $this->BarangModel->update($this->request->getPost("idBarang"), $data);

        echo json_encode("");
    }
}
