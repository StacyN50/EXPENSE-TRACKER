<?php // header and nav ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mita Budget</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{background:#f5f7fb}
    .brand-bar{background:#0f766e;color:#fff}
    .brand-bar .navbar-brand{font-weight:700;letter-spacing:.5px}
    .card{border:none;box-shadow:0 6px 20px rgba(0,0,0,.06);border-radius:16px}
    .table th{background:#eef6f9}
    .fab{
      position:fixed;right:18px;bottom:18px;z-index:1040;width:56px;height:56px;border-radius:50%;
      display:flex;align-items:center;justify-content:center;font-size:28px;color:#fff;background:#0f766e;
      box-shadow:0 6px 20px rgba(0,0,0,.2);text-decoration:none
    }
    .nav-dd .dropdown-menu{right:0;left:auto}
    @media (max-width:576px){.card{border-radius:12px}}
  </style>
</head>
<body>
  <nav class="navbar navbar-expand brand-bar">
    <div class="container-fluid px-3">
      <a class="navbar-brand" href="index.php">Mita Budget</a>
      <div class="ms-auto nav-dd dropdown">
        <button class="btn btn-teal btn-sm btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Menu</button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="index.php?page=entry">Entry of Data</a></li>
          <li><a class="dropdown-item" href="index.php?page=report">Table Report</a></li>
          <li><a class="dropdown-item" href="index.php?page=charts">Charts</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="export_csv.php">Export CSV</a></li>
          <li>
            <form class="px-3 py-2" action="import_csv.php" method="post" enctype="multipart/form-data">
              <label class="form-label small">Import CSV</label>
              <input class="form-control form-control-sm" type="file" name="csv" accept=".csv" required>
              <button class="btn btn-sm btn-primary mt-2">Upload</button>
            </form>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <div class="container my-4">
