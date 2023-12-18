<?php

namespace App\Controllers;

use App\Models\BarangMasukModel;
use App\Models\BarangKeluarModel;
use App\Models\BarangModel;

class Laporan extends BaseController
{
    public function __construct()
    {
        $this->barangMasukModel = new BarangMasukModel();
        $this->barangKeluarModel = new BarangKeluarModel();
        $this->barangModel = new BarangModel();
    }

    public function index()
    {
        if (!session()->get('nama')) {
            return redirect()->to(base_url() . "/login");
        }
        return view('laporan');
    }

    public function dataBarangMasuk()
    {
        $tanggalMulai = $this->request->getPost('tanggalMulai') . " 00:00:00";
        $tanggalSelesai = $this->request->getPost('tanggalSelesai') . " 23:59:59";
        $ringkasan = $this->request->getPost('ringkas');
        $barangMasuk = $this->barangMasukModel->where(['tanggal >=' => $tanggalMulai, 'tanggal <=' => $tanggalSelesai])->findAll();
        $barangKeluar = $this->barangKeluarModel->where(['tanggal >=' => $tanggalMulai, 'tanggal <=' => $tanggalSelesai])->findAll();

        $dataTransaksi = [];
        if ($ringkasan) {
            $barang = $this->barangModel->findAll();
            for ($i = 0; $i < count($barang); $i++) {
                $barang[$i]["barangMasuk"] = 0;
                $barang[$i]["barangKeluar"] = 0;
            
                for ($j=0; $j < count($barangMasuk); $j++) { 
                    if ($barangMasuk[$j]["idBarang"]==$barang[$i]["id"]) {
                        $barang[$i]["barangMasuk"] += $barangMasuk[$j]["penambahanStok"];
                    }
                }
                for ($k=0; $k < count($barangKeluar); $k++) {
                    if ($barangKeluar[$k]["idBarang"]==$barang[$i]["id"]) {
                        $barang[$i]["barangKeluar"] += $barangKeluar[$k]["penguranganStok"];
                    }
                }
            }
            $dataTransaksi = $barang;
        }else{
            $tanggal = [];
            for ($i=0; $i < count($barangMasuk); $i++) { 
                $barangMasuk[$i]["status"] = "Masuk";
                $barangMasuk[$i]["penyesuaian"] = $barangMasuk[$i]["penambahanStok"];
                array_push($tanggal, $barangMasuk[$i]["tanggal"]);
            }
            for ($j=0; $j < count($barangKeluar); $j++) {
                $barangKeluar[$j]["status"] = "Keluar";
                $barangKeluar[$j]["penyesuaian"] = $barangKeluar[$j]["penguranganStok"];
                array_push($tanggal, $barangKeluar[$j]["tanggal"]);
            }

            sort($tanggal);
            $gabungan = array_merge($barangMasuk, $barangKeluar);

            for ($k=0; $k < count($tanggal); $k++) {
                for ($l=0; $l < count($gabungan); $l++) { 
                    if ($tanggal[$k]==$gabungan[$l]["tanggal"]) {
                        array_push($dataTransaksi, $gabungan[$l]);
                        break;
                    }
                }
            }
        }
        echo json_encode($dataTransaksi);
    }
}
