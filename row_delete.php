<?php
require __DIR__.'/db.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $id = (int)($_POST['id'] ?? 0);
  if($id>0){
    $stm = $pdo->prepare('DELETE FROM expenses WHERE id=?');
    $stm->execute([$id]);
  }
}
header('Location: index.php?page=report');
exit;
?>