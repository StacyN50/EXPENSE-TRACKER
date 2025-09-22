<?php
// Handle new expense POST
$errors = [];
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_expense'])) {
    $item = trim($_POST['item'] ?? '');
    $amount = $_POST['amount'] ?? '';
    $category = $_POST['category'] ?? '';
    $date = $_POST['date'] ?? date('Y-m-d');

    $allowed_categories = ['Food','Utilities','Transport','Other'];

    if ($item === '' || strlen($item) > 255) {
        $errors[] = 'Item is required (max 255 chars).';
    } elseif (!preg_match('/^[A-Za-z ]+$/', $item)) {
        $errors[] = 'Item must contain letters and spaces only.';
    }
    if (!is_numeric($amount) || $amount <= 0) {
        $errors[] = 'Amount must be a positive number.';
    }
    if (!in_array($category, $allowed_categories, true)) {
        $errors[] = 'Invalid category.';
    }
    // Date validation
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        $errors[] = 'Invalid date format (YYYY-MM-DD).';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO expenses (date,item,amount,category) VALUES (?,?,?,?)');
        $stmt->execute([$date, $item, $amount, $category]);
        echo '<div class="alert alert-success">Expense added.</div>';
    }
}
?>
<div class="row g-4">
  <div class="col-12 col-lg-6">
    <div class="card p-3">
      <h5 class="mb-3">Add Expense</h5>
      <?php if ($errors): ?>
        <div class="alert alert-danger">
          <ul class="mb-0">
            <?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
      <form method="post" class="needs-validation" novalidate>
        <div class="mb-3">
          <label class="form-label">Date</label>
          <input type="date" class="form-control" name="date" value="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Item (letters & spaces only)</label>
          <input type="text" class="form-control" name="item" maxlength="255" pattern="^[A-Za-z ]+$" required>
          <div class="form-text">Example: Groceries, Bus Ticket, Electricity</div>
        </div>
        <div class="mb-3">
          <label class="form-label">Amount (KSH)</label>
          <input type="number" class="form-control" name="amount" min="0.01" step="0.01" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Category</label>
          <select class="form-select" name="category" required>
            <option>Food</option>
            <option>Utilities</option>
            <option>Transport</option>
            <option>Other</option>
          </select>
        </div>
        <button class="btn btn-teal btn-success" name="add_expense">Add Expense</button>
      </form>
    </div>
  </div>

  <div class="col-12 col-lg-6">
    <div class="card p-3">
      <h5 class="mb-3">Today at a glance</h5>
      <?php
      $today = date('Y-m-d');
      $sum = $pdo->prepare('SELECT COALESCE(SUM(amount),0) AS t FROM expenses WHERE date=?');
      $sum->execute([$today]);
      $total = $sum->fetch()['t'];
      ?>
      <div class="display-6 mb-2">KSH <?= number_format($total,2) ?></div>
      <div class="text-muted">Total Expenses Today</div>
      <a class="fab" href="index.php?page=entry" title="Add"><span>+</span></a>
    </div>
  </div>
</div>
