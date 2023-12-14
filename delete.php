<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<?php include('databaseconnection.php') ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php 

$id = $_GET['id'];
try{
   $sql = "Delete FROM studentsinfo where srno = $id";
   $stmt = mysqli_query($dbconnection, $sql);
   if (!$stmt) {
    throw new Exception("Error in query: " . mysqli_error($dbconnection));
  }
}catch(Exception $e){
 echo  "Error :". $e->getMessage();
}finally {
    mysqli_close($dbconnection);
    header("Location: viewpage.php");
}
    ?>
</body>
</html>