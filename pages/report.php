<?php
// Build dynamic filter SQL
$filters = [];
$args = [];

if (!empty($_GET['q'])) { $filters[] = 'item LIKE ?'; $args[] = '%'.$_GET['q'].'%'; }
if (!empty($_GET['category'])) { $filters[] = 'category = ?'; $args[] = $_GET['category']; }
if (!empty($_GET['min'])) { $filters[] = 'amount >= ?'; $args[] = (float)$_GET['min']; }
if (!empty($_GET['max'])) { $filters[] = 'amount <= ?'; $args[] = (float)$_GET['max']; }
if (!empty($_GET['start'])) { $filters[] = 'date >= ?'; $args[] = $_GET['start']; }
if (!empty($_GET['end'])) { $filters[] = 'date <= ?'; $args[] = $_GET['end']; }

$sql = 'SELECT * FROM expenses';
if ($filters) $sql .= ' WHERE ' . implode(' AND ', $filters);
$sql .= ' ORDER BY date DESC, id DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($args);
$rows = $stmt->fetchAll();
?>
<div class="card p-3 mb-3">
  <form class="row gy-2 gx-2 align-items-end" method="get">
    <input type="hidden" name="page" value="report">
    <div class="col-12 col-sm-4 col-lg-3">
      <label class="form-label">Search Item</label>
      <input class="form-control" name="q" placeholder="e.g. Groceries" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
    </div>
    <div class="col-6 col-sm-3 col-lg-2">
      <label class="form-label">Category</label>
      <select class="form-select" name="category">
        <option value="">All</option>
        <?php foreach(['Food','Utilities','Transport','Other'] as $c): ?>
          <option value="<?= $c ?>" <?= (($_GET['category']??'')===$c?'selected':'') ?>><?= $c ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-6 col-sm-3 col-lg-2">
      <label class="form-label">Min</label>
      <input class="form-control" type="number" step="0.01" name="min" value="<?= htmlspecialchars($_GET['min'] ?? '') ?>">
    </div>
    <div class="col-6 col-sm-3 col-lg-2">
      <label class="form-label">Max</label>
      <input class="form-control" type="number" step="0.01" name="max" value="<?= htmlspecialchars($_GET['max'] ?? '') ?>">
    </div>
    <div class="col-6 col-sm-3 col-lg-2">
      <label class="form-label">Start Date</label>
      <input class="form-control" type="date" name="start" value="<?= htmlspecialchars($_GET['start'] ?? '') ?>">
    </div>
    <div class="col-6 col-sm-3 col-lg-2">
      <label class="form-label">End Date</label>
      <input class="form-control" type="date" name="end" value="<?= htmlspecialchars($_GET['end'] ?? '') ?>">
    </div>
    <div class="col-12 col-sm-3 col-lg-2">
      <button class="btn btn-primary w-100">Filter</button>
    </div>
    <div class="col-12 col-sm-3 col-lg-2">
      <a class="btn btn-secondary w-100" href="index.php?page=report">Reset</a>
    </div>
    <div class="col-12 col-sm-3 col-lg-2">
      <a class="btn btn-success w-100" href="export_csv.php<?= $_SERVER['QUERY_STRING']? ('?'.$_SERVER['QUERY_STRING']) : '' ?>">Export CSV</a>
    </div>
  </form>
</div>

<div class="card p-0">
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead>
        <tr>
          <th>Date</th><th>Item</th><th>Amount (KSH)</th><th>Category</th><th></th>
        </tr>
      </thead>
      <tbody>
        <?php if ($rows): foreach ($rows as $r): ?>
        <tr>
          <td><?= htmlspecialchars($r['date']) ?></td>
          <td><?= htmlspecialchars($r['item']) ?></td>
          <td><?= number_format((float)$r['amount'],2) ?></td>
          <td><?= htmlspecialchars($r['category']) ?></td>
          <td>
            <form method="post" action="row_delete.php" onsubmit="return confirm('Delete this expense?')">
              <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <button class="btn btn-sm btn-outline-danger">Delete</button>
            </form>
          </td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="5" class="text-center p-4">No expenses found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
