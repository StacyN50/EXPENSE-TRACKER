<?php
require __DIR__.'/db.php';

if($_SERVER['REQUEST_METHOD']==='POST' && isset($_FILES['csv'])){
  if($_FILES['csv']['error']===UPLOAD_ERR_OK){
    $tmp = $_FILES['csv']['tmp_name'];
    if(($h = fopen($tmp,'r')) !== false){
      // Expect header: date,item,amount,category
      $header = fgetcsv($h);
      $allowed = ['Food','Utilities','Transport','Other'];
      $ins = $pdo->prepare('INSERT INTO expenses (date,item,amount,category) VALUES (?,?,?,?)');
      while(($row = fgetcsv($h)) !== false){
        if(count($row) < 4) continue;
        list($date,$item,$amount,$category) = $row;

        if(!preg_match('/^\d{4}-\d{2}-\d{2}$/',$date)) continue;
        $item = trim($item);
        if($item==='' || strlen($item)>255 || !preg_match('/^[A-Za-z ]+$/',$item)) continue;
        if(!is_numeric($amount) || $amount<=0) continue;
        if(!in_array($category,$allowed,true)) continue;

        $ins->execute([$date,$item,(float)$amount,$category]);
      }
      fclose($h);
    }
  }
}
header('Location: index.php?page=report');
exit;
?>