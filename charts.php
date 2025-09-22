<?php
require_once __DIR__ . '/../db.php'; 
// Compute totals for selected range (default: current month)
$start = $_GET['start'] ?? date('Y-m-01');
$end = $_GET['end'] ?? date('Y-m-t');

$byCat = $pdo->prepare('SELECT category, SUM(amount) AS total FROM expenses WHERE date BETWEEN ? AND ? GROUP BY category');
$byCat->execute([$start,$end]);
$cat = $byCat->fetchAll();

$byDay = $pdo->prepare('SELECT date, SUM(amount) AS total FROM expenses WHERE date BETWEEN ? AND ? GROUP BY date ORDER BY date');
$byDay->execute([$start,$end]);
$day = $byDay->fetchAll();

?>
<div class="card p-3 mb-3">
  <form class="row g-2" method="get">
    <input type="hidden" name="page" value="charts">
    <div class="col-6 col-sm-3">
      <label class="form-label">Start</label>
      <input class="form-control" type="date" name="start" value="<?= htmlspecialchars($start) ?>">
    </div>
    <div class="col-6 col-sm-3">
      <label class="form-label">End</label>
      <input class="form-control" type="date" name="end" value="<?= htmlspecialchars($end) ?>">
    </div>
    <div class="col-12 col-sm-3 d-flex align-items-end">
      <button class="btn btn-primary w-100">Update</button>
    </div>
  </form>
</div>

<div class="row g-4">
  <div class="col-12 col-lg-6">
    <div class="card p-3">
      <h6 class="mb-3">Expenses by Category</h6>
      <canvas id="pieChart"></canvas>
    </div>
  </div>
  <div class="col-12 col-lg-6">
    <div class="card p-3">
      <h6 class="mb-3">Daily Spend</h6>
      <canvas id="lineChart"></canvas>
    </div>
  </div>
</div>

<script>
const catLabels = <?= json_encode(array_column($cat,'category')) ?>;
const catData = <?= json_encode(array_map(fn($r)=> (float)$r['total'],$cat)) ?>;
const dayLabels = <?= json_encode(array_column($day,'date')) ?>;
const dayData = <?= json_encode(array_map(fn($r)=> (float)$r['total'],$day)) ?>;

new Chart(document.getElementById('pieChart'), {
  type: 'doughnut',
  data: { labels: catLabels, datasets: [{ data: catData }] },
  options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});

new Chart(document.getElementById('lineChart'), {
  type: 'line',
  data: { labels: dayLabels, datasets: [{ data: dayData, tension:.25, fill:false }] },
  options: { responsive: true, scales: { y: { beginAtZero: true } } }
});
</script>
