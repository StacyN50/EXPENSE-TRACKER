<?php
require_once __DIR__ . '/../db.php';

/*
|--------------------------------------------------------------------------
| Safe Date Range Handling
|--------------------------------------------------------------------------
*/

$start = $_GET['start'] ?? date('Y-m-01');
$end   = $_GET['end'] ?? date('Y-m-t');

/* Validate date format */
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $start)) {
    $start = date('Y-m-01');
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $end)) {
    $end = date('Y-m-t');
}

/* Ensure start is not greater than end */
if ($start > $end) {
    $temp = $start;
    $start = $end;
    $end = $temp;
}

/*
|--------------------------------------------------------------------------
| Fetch Expenses by Category
|--------------------------------------------------------------------------
*/

$cat = [];

try {
    $byCat = $pdo->prepare("
        SELECT 
            category, 
            SUM(amount) AS total 
        FROM expenses 
        WHERE `date` BETWEEN ? AND ?
        GROUP BY category
        ORDER BY total DESC
    ");

    $byCat->execute([$start, $end]);
    $cat = $byCat->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>
            Category Chart Error: " . htmlspecialchars($e->getMessage()) . "
          </div>";
}

/*
|--------------------------------------------------------------------------
| Fetch Daily Spending
|--------------------------------------------------------------------------
*/

$day = [];

try {
    $byDay = $pdo->prepare("
        SELECT 
            `date`, 
            SUM(amount) AS total 
        FROM expenses 
        WHERE `date` BETWEEN ? AND ?
        GROUP BY `date`
        ORDER BY `date` ASC
    ");

    $byDay->execute([$start, $end]);
    $day = $byDay->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>
            Daily Chart Error: " . htmlspecialchars($e->getMessage()) . "
          </div>";
}
?>

<!-- Load Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Filter Form -->
<div class="card p-3 mb-3">
    <form class="row g-2" method="GET">

        <input type="hidden" name="page" value="charts">

        <div class="col-6 col-sm-3">
            <label class="form-label">Start Date</label>
            <input
                class="form-control"
                type="date"
                name="start"
                value="<?= htmlspecialchars($start) ?>"
                required
            >
        </div>

        <div class="col-6 col-sm-3">
            <label class="form-label">End Date</label>
            <input
                class="form-control"
                type="date"
                name="end"
                value="<?= htmlspecialchars($end) ?>"
                required
            >
        </div>

        <div class="col-12 col-sm-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">
                Update Charts
            </button>
        </div>

    </form>
</div>

<!-- Charts Section -->
<div class="row g-4">

    <!-- Pie Chart -->
    <div class="col-12 col-lg-6">
        <div class="card p-3">
            <h5 class="mb-3">Expenses by Category</h5>

            <?php if (!empty($cat)): ?>
                <div style="height: 350px;">
                    <canvas id="pieChart"></canvas>
                </div>
            <?php else: ?>
                <div class="alert alert-info mb-0">
                    No expense data found for selected range.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Line Chart -->
    <div class="col-12 col-lg-6">
        <div class="card p-3">
            <h5 class="mb-3">Daily Spending Trend</h5>

            <?php if (!empty($day)): ?>
                <div style="height: 350px;">
                    <canvas id="lineChart"></canvas>
                </div>
            <?php else: ?>
                <div class="alert alert-info mb-0">
                    No daily spending data found.
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<script>
/*
|--------------------------------------------------------------------------
| Prevent Infinite Chart Re-rendering
|--------------------------------------------------------------------------
| The looping happens because Chart.js keeps creating new chart
| instances on the same canvas.
|
| FIX:
| Destroy old chart before creating a new one
| Disable animation loop
|
*/

const catLabels = <?= json_encode(array_column($cat, 'category')) ?>;
const catData   = <?= json_encode(array_map(fn($row) => (float)$row['total'], $cat)) ?>;

const dayLabels = <?= json_encode(array_column($day, 'date')) ?>;
const dayData   = <?= json_encode(array_map(fn($row) => (float)$row['total'], $day)) ?>;

/* Global chart references */
let pieChartInstance = null;
let lineChartInstance = null;

/*
|--------------------------------------------------------------------------
| Create Pie Chart Safely
|--------------------------------------------------------------------------
*/

const pieCanvas = document.getElementById('pieChart');

if (
    pieCanvas &&
    catLabels.length > 0 &&
    catData.length > 0
) {
    /* Destroy old instance first */
    if (Chart.getChart(pieCanvas)) {
        Chart.getChart(pieCanvas).destroy();
    }

    pieChartInstance = new Chart(pieCanvas, {
        type: 'doughnut',
        data: {
            labels: catLabels,
            datasets: [{
                label: 'Expenses by Category',
                data: catData,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false, // stops repeated animation loop
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

/*
|--------------------------------------------------------------------------
| Create Line Chart Safely
|--------------------------------------------------------------------------
*/

const lineCanvas = document.getElementById('lineChart');

if (
    lineCanvas &&
    dayLabels.length > 0 &&
    dayData.length > 0
) {
    /* Destroy old instance first */
    if (Chart.getChart(lineCanvas)) {
        Chart.getChart(lineCanvas).destroy();
    }

    lineChartInstance = new Chart(lineCanvas, {
        type: 'line',
        data: {
            labels: dayLabels,
            datasets: [{
                label: 'Daily Spending',
                data: dayData,
                tension: 0.25,
                fill: false,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false, // stops looping
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}
</script>
