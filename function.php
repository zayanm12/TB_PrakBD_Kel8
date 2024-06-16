
<?php
session_start();

// Buat Koneksi
$c = mysqli_connect('localhost', 'root', '', 'bmleathershop2');

// Cek Koneksi
if (!$c) {
    die("Connection failed: " . mysqli_connect_error());
}

// Login
if (isset($_POST['login'])) {
    // Inisialisasi variabel
    $username = mysqli_real_escape_string($c, $_POST['username']);
    $password = mysqli_real_escape_string($c, $_POST['password']);

    $check = mysqli_query($c, "SELECT * FROM user WHERE username='$username' AND password='$password'");
    $hitung = mysqli_num_rows($check);

    if ($hitung > 0) {
        // Jika datanya ditemukan
        // Berhasil login
        $_SESSION['login'] = 'True';
        header('Location: index.php');
        exit();
    } else {
        // Data tidak ditemukan
        // Gagal login
        echo '
        <script>
            alert("Username atau Password salah");
            window.location.href = "login.php";
        </script>';
    }
};

// tambah barang di stok

if (isset($_POST['tambahbarang'])) {
    $NamaProduk = $_POST['NamaProduk'];
    $Deskripsi = $_POST['Deskripsi'];
    $Harga = $_POST['Harga'];
    $Stok = $_POST['Stok'];

    $insert = mysqli_query($c, "INSERT INTO produk (NamaProduk, Deskripsi, Harga, Stok) VALUES ('$NamaProduk', '$Deskripsi', '$Harga', '$Stok')");

    if ($insert) {
        header('location:stock.php');
    } else {
        echo '
        <script>alert("Gagal Menambah Barang Baru");
        window.location.href="stock.php"
        </script>
        ';
    }
}

// tambah pelanggan

if (isset($_POST['tambahpelanggan'])) {
    $NamaPelanggan = $_POST['NamaPelanggan'];
    $Notelp = $_POST['Notelp'];
    $Alamat = $_POST['Alamat'];
    
    // Query INSERT dengan tanda kurung yang benar
    $insert = mysqli_query($c, "INSERT INTO pelanggan (NamaPelanggan, Notelp, Alamat) VALUES ('$NamaPelanggan', '$Notelp', '$Alamat')");

    if ($insert) {
        header('location:pelanggan.php');
    } else {
        echo '
        <script>alert("Gagal Menambah Pelanggan Baru");
        window.location.href="pelanggan.php"
        </script>
        ';
    }
}

// tambah pesanan

if (isset($_POST['tambahpesanan'])) {
    $IDPelanggan = $_POST['IDPelanggan'];
    $TanggalPesanan = date("Y-m-d H:i:s"); // Menyertakan tanggal dan jam saat ini
    
    // Query INSERT dengan tanda kurung yang benar dan menyertakan tanggal pesanan
    $insert = mysqli_query($c, "INSERT INTO pesanan (IDPelanggan, TanggalPesanan) VALUES ('$IDPelanggan', '$TanggalPesanan')");

    if ($insert) {
        header('location:index.php');
    } else {
        echo '
        <script>alert("Gagal Menambah Pesanan Baru");
        window.location.href="index.php"
        </script>
        ';
    }
}

// produk dipilih di pesanan
if (isset($_POST['tambahproduk'])) {
    $IDProduk = $_POST['IDProduk'];
    $idp = $_POST['idp'];
    $Kuantitas = $_POST['Kuantitas']; //jumlah
    
    //hitung jumlah stok sekarang ada berapa
    $hitung1 = mysqli_query($c, "select * from produk where IDProduk='$IDProduk'");
    $hitung2 = mysqli_fetch_array($hitung1);
    $stoksekarang = $hitung2['Stok']; //stok barang saat ini

    if ($stoksekarang >= $Kuantitas) {
        //kurangi stoknya dengan jumlah yang akan dikeluarkan
        $selisih = $stoksekarang - $Kuantitas;

        // stok nya cukup
        $insert = mysqli_query($c, "INSERT INTO detailpesanan (IDPesanan, IDProduk, Kuantitas) VALUES ('$idp', '$IDProduk', '$Kuantitas')");
        $update = mysqli_query($c, "update produk set stok='$selisih' where IDProduk='$IDProduk'");

        if ($insert && $update) {
            header('location:view.php?idp=' . $idp);
        } else {
            echo '
            <script>alert("Gagal Menambah Pesanan Baru");
            window.location.href="view.php?idp=' . $idp . '";
            </script>
            ';
        }
    } else {
        //stok ga cukup
        echo '
        <script>alert("Stok Barang Tidak Cukup");
        window.location.href="view.php?idp=' . $idp . '";
        </script>
        ';
    }
}

//menambah barang masuk
if (isset($_POST['barangmasuk'])) {
    $IDProduk = $_POST['IDProduk'];
    $Kuantitas = $_POST['Kuantitas'];

    // Cari tahu stok sekarang ada berapa
    $caristok = mysqli_query($c, "select * from produk where IDProduk='$IDProduk'");
    $caristok2 = mysqli_fetch_array($caristok);
    $stoksekarang = $caristok2['Stok'];

    //hitung
    $newstok = $stoksekarang+$Kuantitas;

    $insertb = mysqli_query($c, " insert into masuk (IDProduk,Kuantitas) values('$IDProduk','$Kuantitas')");
    $updateb = mysqli_query($c, " update produk set Stok='$newstok' where IDProduk='$IDProduk'");

    if ($insertb&&$updateb) {
        header('location:masuk.php');
    } else {
        echo '
        <script>alert("Gagal");
        window.location.href="masuk.php"
        </script>
        ';
    }
}  

//hapus produk pesanan
if (isset($_POST['hapusprodukpesanan'])) {
    $iddp = $_POST['iddp']; 
    $idpr = $_POST['idpr'];
    $idp = $_POST['idp'];

    //cek qty sekarang
    $cek1 = mysqli_query($c, "SELECT * FROM detailpesanan WHERE IDDetail='$iddp'");
    $cek2 = mysqli_fetch_array($cek1);
    $qtysekarang = $cek2['Kuantitas'];

    //cek stok sekarang
    $cek3 = mysqli_query($c, "SELECT * FROM produk WHERE IDProduk='$idpr'");
    $cek4 = mysqli_fetch_array($cek3);
    $stoksekarang = $cek4['Stok'];

    // hitung stok setelah penghapusan barang
    $stokbaru = $stoksekarang + $qtysekarang;

    // update stok
    $update = mysqli_query($c, "UPDATE produk SET Stok='$stokbaru' WHERE IDProduk='$idpr'");

    // hapus detail pesanan
    $hapus = mysqli_query($c, "DELETE FROM detailpesanan WHERE IDDetail='$iddp' AND IDProduk='$idpr'");

    if ($update && $hapus) {
        header('location:view.php?idp=' . $idp);
    } else {
        echo '
            <script>alert("Gagal Menghapus Barang");
            window.location.href="view.php?idp=' . $idp . '";
            </script>
        ';
    }
}

//edit barang
if (isset($_POST['editbarang'])) {
    $NamaProduk = $_POST['NamaProduk'];
    $Deskripsi= $_POST['Deskripsi'];
    $Harga = $_POST['Harga'];
    $idp = $_POST['idp']; // idproduk

    $query = mysqli_query($c, "update produk set NamaProduk='$NamaProduk', Deskripsi='$Deskripsi', harga='$Harga' where IDProduk='$idp'");

    if($query) {
        header('location:stock.php');
    } else {
        echo '
        <script>alert("Gagal");
        window.location.href="stock.php"
        </script>
        ';
    }
}

// hapus barang 
if (isset($_POST['hapusbarang'])) {
    $idp = $_POST['idp'];

    // Koneksi ke database
    $query = mysqli_query($c, "DELETE FROM produk WHERE IDProduk='$idp'");
    
    if($query){
        header('location:stock.php');
    } else {
        echo '
        <script>alert("Gagal Menghapus Barang");
        window.location.href="stock.php";
        </script>
        ';
    }
}

// edit pelanggan
if (isset($_POST['editpelanggan'])) {
    $np = $_POST['NamaPelanggan'];
    $nt = $_POST['Notelp'];
    $a = $_POST['Alamat'];
    $id = $_POST['idpl'];

    $query = mysqli_query($c, "update pelanggan set NamaPelanggan='$np', Notelp='$nt', Alamat='$a' where IDPelanggan='$id'");

    if($query) {
        header('location:pelanggan.php');
    } else {
        echo '
        <script>alert("Gagal");
        window.location.href="pelanggan.php"
        </script>
        ';
    }
}   

// hapus pelanggan
if (isset($_POST['hapuspelanggan'])) {
    $idpl = $_POST['idpl'];

    // Koneksi ke database
    $query = mysqli_query($c, "DELETE FROM pelanggan WHERE IDPelanggan='$idpl'");
    
    if($query){
        header('Location: pelanggan.php');
    } else {
        echo '
        <script>
            alert("Gagal Menghapus Pelanggan: ' . mysqli_error($c) . '");
            window.location.href="pelanggan.php";
        </script>
        ';
    }
}

// Edit barang masuk
if (isset($_POST['editbarangmasuk'])) {
    $Kuantitas = $_POST['Kuantitas'];
    $idmasuk = $_POST['IDMasuk'];
    $IDProduk = $_POST['IDProduk'];

    // Cari tau kuantitas sekarang berapa
    $caritahu = mysqli_query($c, "select * from masuk where IDMasuk='$idmasuk'");
    $caritahu2 = mysqli_fetch_array($caritahu);
    $qtysekarang = $caritahu2['Kuantitas'];

    // Cari tahu stok sekarang ada berapa
    $caristok = mysqli_query($c, "select * from produk where IDProduk='$IDProduk'");
    $caristok2 = mysqli_fetch_array($caristok);
    $stoksekarang = $caristok2['Stok'];

    if ($Kuantitas >= $qtysekarang) {
        // Kalau inputan user lebih besar daripada kuantitas yang tercatat
        // Hitung selisih
        $selisih = $Kuantitas - $qtysekarang;
        $newstok = $stoksekarang + $selisih;  // Menambah stok yang ada

        $query1 = mysqli_query($c, "update masuk set Kuantitas='$Kuantitas' where IDMasuk='$idmasuk'");
        $query2 = mysqli_query($c, "update produk set Stok='$newstok' where IDProduk='$IDProduk'");

        if ($query1 && $query2) {
            header('location:masuk.php');
        } else {
            echo '
            <script>alert("Gagal");
            window.location.href="masuk.php"
            </script>
            ';
        }
    } else {
        // Kalau lebih kecil
        // Hitung selisih
        $selisih = $qtysekarang - $Kuantitas;
        $newstok = $stoksekarang - $selisih;  // Mengurangi stok yang ada

        $query1 = mysqli_query($c, "update masuk set Kuantitas='$Kuantitas' where IDMasuk='$idmasuk'");
        $query2 = mysqli_query($c, "update produk set Stok='$newstok' where IDProduk='$IDProduk'");

        if ($query1 && $query2) {
            header('location:masuk.php');
        } else {
            echo '
            <script>alert("Gagal");
            window.location.href="masuk.php"
            </script>
            ';
        }
    }
}

// hapus barang masuk 
if (isset($_POST['hapusdatabarangmasuk'])) {
    $idmasuk = $_POST['IDMasuk'];
    $IDProduk = $_POST['IDProduk'];

    // Cari tau kuantitas sekarang berapa
    $caritahu = mysqli_query($c, "select * from masuk where IDMasuk='$idmasuk'");
    $caritahu2 = mysqli_fetch_array($caritahu);
    $qtysekarang = $caritahu2['Kuantitas'];

    // Cari tahu stok sekarang ada berapa
    $caristok = mysqli_query($c, "select * from produk where IDProduk='$IDProduk'");
    $caristok2 = mysqli_fetch_array($caristok);
    $stoksekarang = $caristok2['Stok'];

    //hitung selisih
    $newstok = $stoksekarang-$qtysekarang;

    $query1 = mysqli_query($c, "delete from masuk where IDMasuk='$idmasuk'");
    $query2 = mysqli_query($c, "update produk set stok='$newstok' where IDProduk='$IDProduk'");
 
    
    if($query1 && $query2){
        header('Location: masuk.php');
    } else {
        echo '
        <script>alert("Gagal");
        window.location.href="masuk.php";
        </script>
        ';
    }
}

if (isset($_POST['hapuspesanan'])) {
    $IDPesanan = $_POST['IDPesanan'];
    echo "IDPesanan: " . $IDPesanan . "<br>";

    // Mulai transaksi
    mysqli_begin_transaction($c);

    try {
        // Ambil data detail pesanan
        $cekdata = mysqli_query($c, "SELECT * FROM detailpesanan WHERE IDPesanan='$IDPesanan'");
        if (!$cekdata) {
            throw new Exception('Error query detail pesanan: ' . mysqli_error($c));
        }

        while ($ok = mysqli_fetch_array($cekdata)) {
            // Debug: Tampilkan data detail pesanan
            echo "Detail: " . json_encode($ok) . "<br>";

            // Kembalikan stok
            $Kuantitas = $ok['Kuantitas'];
            $IDProduk = $ok['IDProduk'];
            $iddp = $ok['IDDetail'];

            // Cari stok sekarang
            $caristok = mysqli_query($c, "SELECT * FROM produk WHERE IDProduk='$IDProduk'");
            if (!$caristok) {
                throw new Exception('Error query produk: ' . mysqli_error($c));
            }
            $caristok2 = mysqli_fetch_array($caristok);
            $stoksekarang = $caristok2['Stok'];

            // Debug: Tampilkan stok sekarang
            echo "Stok Sekarang: " . $stoksekarang . "<br>";

            // Hitung stok baru
            $newstok = $stoksekarang + $Kuantitas;

            // Update stok
            $queryupdate = mysqli_query($c, "UPDATE produk SET stok='$newstok' WHERE IDProduk='$IDProduk'");
            if (!$queryupdate) {
                throw new Exception('Gagal update stok produk: ' . mysqli_error($c));
            }

            // Hapus detail pesanan
            $querydelete = mysqli_query($c, "DELETE FROM detailpesanan WHERE IDDetail='$iddp'");
            if (!$querydelete) {
                throw new Exception('Gagal hapus detail pesanan: ' . mysqli_error($c));
            }
        }

        // Hapus pesanan
        $query = mysqli_query($c, "DELETE FROM pesanan WHERE IDPesanan='$IDPesanan'");
        if (!$query) {
            throw new Exception('Gagal hapus pesanan: ' . mysqli_error($c));
        }

        // Commit transaksi
        mysqli_commit($c);

        header('Location: index.php');
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        mysqli_rollback($c);

        echo '
        <script>
            alert("'.$e->getMessage().'");
            window.location.href="index.php";
        </script>
        ';
    }
}


// mengubah data detail pesanan
if (isset($_POST['editdetailpesanan'])) {
    $Kuantitas = $_POST['Kuantitas'];
    $iddp = $_POST['IDDetail'];
    $idpr = $_POST['IDProduk'];
    $idp = $_POST['IDPesanan'];

    // Cari tau kuantitas sekarang berapa
    $caritahu = mysqli_query($c, "select * from detailpesanan where IDDetail='$iddp'");
    $caritahu2 = mysqli_fetch_array($caritahu);
    $qtysekarang = $caritahu2['Kuantitas'];

    // Cari tahu stok sekarang ada berapa
    $caristok = mysqli_query($c, "select * from produk where IDProduk='$idpr'");
    $caristok2 = mysqli_fetch_array($caristok);
    $stoksekarang = $caristok2['Stok'];

    if ($Kuantitas >= $qtysekarang) {
        // Kalau inputan user lebih besar daripada kuantitas yang tercatat
        // Hitung selisih
        $selisih = $Kuantitas - $qtysekarang;
        $newstok = $stoksekarang + $selisih;  // Menambah stok yang ada

        $query1 = mysqli_query($c, "update detailpesanan set Kuantitas='$Kuantitas' where IDDetail='$iddp'");
        $query2 = mysqli_query($c, "update produk set Stok='$newstok' where IDProduk='$idpr'");

        if ($query1 && $query2) {
            header('location:view.php?idp='.$idp);
        } else {
            echo '
            <script>alert("Gagal");
            window.location.href="view.php?idp='.$idp.'";
            </script>
            ';
        }
    } else {
        // Kalau lebih kecil
        // Hitung selisih
        $selisih = $qtysekarang - $Kuantitas;
        $newstok = $stoksekarang + $selisih;  // Mengurangi stok yang ada

        $query1 = mysqli_query($c, "update detailpesanan set Kuantitas='$Kuantitas' where IDDetail='$iddp'");
        $query2 = mysqli_query($c, "update produk set Stok='$newstok' where IDProduk='$idpr'");

        if ($query1 && $query2) {
            header('location:view.php?idp='.$idp);
        } else {
            echo '
            <script>alert("Gagal");
            window.location.href="view.php?idp='.$idp.'"
            </script>
            ';
        }
    }
}





?>


