# tnkb-detail


TNKB is an acronym for Tanda Nomor Kendaraan Bermotor, which translates to "motor vehicle sign" or "registration number" in Indonesian. It's a package that can decode and validate Indonesian vehicle registration numbers. 
In Indonesia, all motorized vehicles are required to have registration plates on the front and back of the vehicle.


Motorized vehicles in Indonesia are required to have registration plates, which must be displayed both at the front and back of the vehicles. The issuing of number plates is regulated and administered by SAMSAT (Indonesian: Sistem Administrasi Manunggal Satu Atap, lit. 'One-stop Administration Services Office'), which is a collaboration between the Indonesian National Police, provincial offices of regional revenue, and the national mandatory vehicle insurance operator Jasa Raharja.


Cek Nopol Kendaraan

The lettering convention denoting the area of registration is a legacy of the Dutch colonial era and does not reflect the current regional divisions of the country into provinces. They follow the old system of Dutch Karesidenan or residencies lettering systems, which were adopted in the 1920s,[1] and the Territorial Police system which was abolished in 2010.




For Search Jabar Nopol

````
POST /tnkb HTTP/1.1
X-Rapidapi-Key: rapid-key
X-Rapidapi-Host: cek-nopol-kendaraan.p.rapidapi.com
Content-Type: application/x-www-form-urlencoded
Host: cek-nopol-kendaraan.p.rapidapi.com
Content-Length: 14

stnk=D-AA-1234
````
Result 

````
{
  "code": "0000",
  "success": true,
  "data": {
    "data_wp": {
      "no_ktp": "nomerktp ",
      "nm_pemilik": "NAMA LENGKAP PEMILIK ",
      "nm_kota_lhr": "",
      "tgl_lahir": "",
      "no_kk": "",
      "al_pemilik": "ALAMAT LENGKAP ",
      "rt": " ",
      "rw": " ",
      "nm_kelurahan": "",
      "kd_kec": "",
      "nm_kecamatan": "",
      "kd_kota": "",
      "nm_kota": "BANDUNG I PDJDJRAN ",
      "email": "",
      "no_hp": " "
    },
    "list_kbm": [
      {
        "no_ktp": "NO KTP ",
        "rt": " ",
        "rw": " ",
        "data_tnkb": {
          "tnkb_daerah": "D",
          "tnkb_subdaerah": "AA",
          "tnkb_nomor": "1234"
        },
        "no_polisi": "D-1234-AA",
        "no_polisi2": "D AA 1234",
        "kd_plat": "1",
        "nm_pemilik": "NAMA LENGKAP ",
        "al_pemilik": "ALAMAT",
        "nm_merek": "JENIS KENDARAAN ",
        "nm_model_kb": "KETERANGAN ",
        "tg_kepemilikan": "1991-02-02",
        "tg_akhir_pajak": "2018-02-02",
        "tg_akhir_pajak2": "02-02-2018",
        "th_rakitan": "1991",
        "jumlah_cc": "1795 ",
        "milik_ke": "1",
        "kd_blockir": " ",
        "kd_blockir_baru": "0",
        "jenis_kb": "R4",
        "kd_wil": "12000",
        "nm_wil": "BANDUNG I PDJDJRAN ",
        "tg_akhir_stnkb": "2019-02-02",
        "no_hp": " ",
        "email": ""
      }
    ]
  },
  "message": "Sukses",
  "param": {
    "no_polisi": "D AA 1234"
  }
}
````
