<?php
require 'ceklogin.php';

// hitung jumlah pesanan
$h1 = mysqli_query($c, "SELECT * FROM pesanan");
$h2 = mysqli_num_rows($h1); // jumlah pesanan
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Data Pesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="index.php">BMLeatherShop</a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Menu</div>
                        <a class="nav-link" href="index.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Order Barang
                        </a>
                        <a class="nav-link" href="stock.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Stok Barang
                        </a>
                        <a class="nav-link" href="masuk.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Barang Masuk
                        </a>
                        <a class="nav-link" href="pelanggan.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Kelola Pelanggan
                        </a>
                        <a class="nav-link" href="logout.php">
                            Logout
                        </a>
                    </div>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Data Pesanan</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Selamat Datang Di Toko BM Leather Shop</li>
                    </ol>
                    <div class="row">
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-primary text-white mb-4">
                                <div class="card-body">Jumlah Pesanan </div>
                            </div>
                        </div>
                    </div>

                    <!-- Button to Open the Modal -->
                    <button type="button" class="btn btn-info mb-4" data-bs-toggle="modal" data-bs-target="#myModal">
                        Tambah Pesanan Baru
                    </button>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Data Pesanan
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID Pesanan</th>
                                            <th>Tanggal Pesanan</th>
                                            <th>Nama Pelanggan</th>
                                            <th>Jumlah</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                            // Pastikan koneksi ke database sudah dilakukan sebelumnya dengan variabel $c
                                            $get = mysqli_query($c, "SELECT * FROM pesanan p, pelanggan pl WHERE p.IDPelanggan=pl.IDPelanggan");

                                            while ($p = mysqli_fetch_array($get)) {
                                                $IDPesanan = $p['IDPesanan'];
                                                $TanggalPesanan = $p['TanggalPesanan'];
                                                $NamaPelanggan = $p['NamaPelanggan'];
                                                $Alamat = $p['Alamat'];
                                                
                                                // Hitung jumlah
                                                $hitungjumlah = mysqli_query($c, "SELECT * FROM detailpesanan WHERE IDPesanan='$IDPesanan'");
                                                $jumlah = mysqli_num_rows($hitungjumlah);
                                                ?>

                                                <tr>
                                                    <td><?= $IDPesanan; ?></td>
                                                    <td><?= $TanggalPesanan; ?></td>
                                                    <td><?= $NamaPelanggan; ?> - <?= $Alamat; ?></td>
                                                    <td><?= $jumlah; ?></td>
                                                    <td>
                                                        <a href="view.php?idp=<?= $IDPesanan; ?>" class="btn btn-primary" target="_blank">Tampilkan</a> 
                                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#delete<?=$IDPesanan;?>">
                                                            Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                                <!-- Modal Delete -->
                                                <div class="modal fade" id="delete<?=$IDPesanan;?>" tabindex="-1" aria-labelledby="deleteLabel<?=$IDPesanan;?>" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <!-- Modal Header -->
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">Hapus Data Pesanan</h4>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form method="post">
                                                                <!-- Modal body -->
                                                                <div class="modal-body">
                                                                    Apakah Anda Yakin Ingin Menghapus Data Pesanan Ini?
                                                                    <input type="hidden" name="IDPesanan" value="<?=$IDPesanan;?>">
                                                                </div>
                                                                <!-- Modal footer -->
                                                                <div class="modal-footer">
                                                                    <button type="submit" class="btn btn-success" name="hapuspesanan">Submit</button>
                                                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php
                                            } // end of while
                                            ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Your Website 2023</div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/chart-area-demo.js"></script>
    <script src="assets/demo/chart-bar-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>
</body>
<!-- The Modal -->
<div class="modal" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Tambah Pesanan Baru</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form method="post">

                <!-- Modal body -->
                <div class="modal-body">
                    Pilih Pelanggan
                    <select name="IDPelanggan" class="form-control">
                        <?php
                        $getpelanggan = mysqli_query($c, "SELECT * FROM pelanggan");

                        while ($pl = mysqli_fetch_array($getpelanggan)) {
                            $NamaPelanggan = $pl['NamaPelanggan'];
                            $IDPelanggan = $pl['IDPelanggan'];
                            $Notelp = $pl['Notelp'];
                            $Alamat = $pl['Alamat'];
                            ?>
                            <option value="<?= $IDPelanggan; ?>"><?= $NamaPelanggan; ?> - <?= $Alamat; ?></option>
                            <?php
                        } // Akhir dari loop while
                        ?>
                    </select>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" name="tambahpesanan">Submit</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>

            </form>

        </div>
    </div>

    </div>
</html>
