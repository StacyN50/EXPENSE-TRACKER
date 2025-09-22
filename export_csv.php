<?php
require __DIR__.'/db.php';

$filters=[]; $args=[];
if (!empty($_GET['q'])) { $filters[] = 'item LIKE ?'; $args[] = '%'.$_GET['q'].'%'; }
if (!empty($_GET['category'])) { $filters[] = 'category = ?'; $args[] = $_GET['category']; }
if (!empty($_GET['min'])) { $filters[] = 'amount >= ?'; $args[] = (float)$_GET['min']; }
if (!empty($_GET['max'])) { $filters[] = 'amount <= ?'; $args[] = (float)$_GET['max']; }
if (!empty($_GET['start'])) { $filters[] = 'date >= ?'; $args[] = $_GET['start']; }
if (!empty($_GET['end'])) { $filters[] = 'date <= ?'; $args[] = $_GET['end']; }

$sql='SELECT date,item,amount,category FROM expenses';
if($filters) $sql .= ' WHERE ' . implode(' AND ', $filters);
$sql .= ' ORDER BY date, id';

$stm = $pdo->prepare($sql);
$stm->execute($args);

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="expenses_export_'.date('Ymd_His').'.csv"');

$out = fopen('php://output','w');
fputcsv($out, ['date','item','amount','category']);
while($r = $stm->fetch()){
  fputcsv($out, [$r['date'], $r['item'], $r['amount'], $r['category']]);
}
fclose($out);
exit;
?>