<?php
require 'ceklogin.php';

if (isset($_GET['idp'])){
    $idp = $_GET['idp'];
    $ambilnamapelanggan = mysqli_query($c, "select * from pesanan p, pelanggan pl where p.IDPelanggan=pl.IDPelanggan and p.IDPesanan='$idp'");
    $np = mysqli_fetch_array($ambilnamapelanggan);
    $namapel = $np['NamaPelanggan'];

} else {
    header('location:index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Data Pesanan </title>
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
                            <a class="nav-link" href="pelanggan.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Kelola Pelanggan
                            </a>
                            </a><a class="nav-link" href="logout.php">
                                Logout
                            </a>
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid">
                    <h1 class="mt-4">Data Pesanan: <?php echo $idp; ?></h1>
                    <h4 class="mt-4">Nama Pelanggan: <?php echo $namapel; ?></h4>

                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Selamat Datang Di Toko BM Leather Shop</li>
                        </ol>

                        <!-- Button to Open the Modal -->
                        <button type="button" class="btn btn-info mb-4" data-bs-toggle="modal" data-bs-target="#myModal">
                            Tambah Barang
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
                                                <th>No</th>
                                                <th>Nama Produk</th>
                                                <th>Harga Satuan</th>
                                                <th>Jumlah Barang Dipesan</th>
                                                <th>Sub-total</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                            // Pastikan koneksi ke database sudah dilakukan sebelumnya dengan variabel $c
                                            $get = mysqli_query($c, "SELECT * FROM detailpesanan p, produk pr WHERE p.IDProduk = pr.IDProduk AND p.IDPesanan = '$idp'");

                                            $i = 1;

                                            while ($p = mysqli_fetch_array($get)) {
                                                $idpr = $p['IDProduk'];
                                                $iddp = $p['IDDetail'];
                                                $idp = $p['IDPesanan'];
                                                $Kuantitas = $p['Kuantitas'];
                                                $Harga = $p['Harga'];
                                                $NamaProduk = $p['NamaProduk'];
                                                $Deskripsi = $p['Deskripsi'];
                                                $SubTotal = $Kuantitas * $Harga;
                                                ?>

                                                <tr>
                                                    <td><?= $i++; ?></td>
                                                    <td><?= $NamaProduk; ?> (<?=$Deskripsi;?>)</td>
                                                    <td>Rp<?= number_format($Harga); ?></td>
                                                    <td><?= number_format($Kuantitas); ?></td>
                                                    <td>Rp<?= number_format($SubTotal); ?></td>
                                                    <td>
                                                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#edit<?=$idpr;?>">
                                                            Edit
                                                        </button>
                                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#delete<?= $iddp; ?>">
                                                            Delete
                                                        </button>

                                                    </td>
                                                </tr>

                                                <!-- Modal Edit -->
                                                <div class="modal fade" id="edit<?=$idpr;?>" tabindex="-1" aria-labelledby="editLabel<?=$idpr;?>" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <!-- Modal Header -->
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">Ubah Data Detail Pesanan</h4>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>

                                                            <form method="post">
                                                                <!-- Modal body -->
                                                                <div class="modal-body">
                                                                    <input type="text" name="NamaProduk" class="form-control" placeholder="Nama Produk" value="<?=$NamaProduk;?> : <?=$Deskripsi;?>" disabled>
                                                                    <input type="number" name="Kuantitas" class="form-control mt-2" placeholder="Jumlah" value="<?=$Kuantitas;?>">
                                                                    <input type="hidden" name="IDDetail" value="<?=$iddp;?>">
                                                                    <input type="hidden" name="IDPesanan" value="<?=$idp;?>">
                                                                    <input type="hidden" name="IDProduk" value="<?=$idpr;?>">
                                                                </div>
                                                                <!-- Modal footer -->
                                                                <div class="modal-footer">
                                                                    <button type="submit" class="btn btn-success" name="editdetailpesanan">Submit</button>
                                                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- The Modal Delete -->
                                                <div class="modal fade" id="delete<?= $iddp; ?>" tabindex="-1" aria-labelledby="deleteLabel<?= $iddp; ?>" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <!-- Modal Header -->
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">Apakah Anda Yakin Ingin Menghapus Barang Ini?</h4>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form method="post">
                                                                <!-- Modal body -->
                                                                <div class="modal-body">
                                                                    Apakah Anda Yakin Ingin Menghapus Barang Ini?
                                                                    <input type="hidden" name="iddp" value="<?= $iddp; ?>">
                                                                    <input type="hidden" name="idpr" value="<?= $idpr; ?>">
                                                                    <input type="hidden" name="idp" value="<?= $idp; ?>">
                                                                </div>
                                                                <!-- Modal footer -->
                                                                <div class="modal-footer">
                                                                    <button type="submit" class="btn btn-success" name="hapusprodukpesanan">Ya</button>
                                                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                <?php
                                            }
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
                    <h4 class="modal-title">Tambah Barang

                <form method="post">
            
                <!-- Modal body -->
                <div class="modal-body">
                    Pilih Barang
                    <select name="IDProduk" class="form-control">
                        <?php
                        $getproduk = mysqli_query($c, "SELECT * FROM produk where IDProduk not in (select IDProduk from detailpesanan where IDPesanan='$idp')");

                        while ($pl = mysqli_fetch_array($getproduk)) {
                            $NamaProduk = $pl['NamaProduk'];
                            $Deskripsi = $pl['Deskripsi'];
                            $Stok = $pl['Stok'];
                            $IDProduk = $pl['IDProduk'];
                        ?>
                            <option value="<?=$IDProduk;?>"><?=$NamaProduk;?> - <?=$Deskripsi;?> (Stok: <?=$Stok;?>)</option>
                        <?php
                        } // Akhir dari loop while
                        ?>
                    </select>

                    <input type="number" name="Kuantitas" class="form-control mt-4" placeholder="Jumlah">
                    <input type="hidden" name="idp" value="<?=$idp;?>">
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" name="tambahproduk">Submit</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>

                </form>

            </div>
        </div>
    </div>
</html>
