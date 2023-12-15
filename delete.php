<?php require_once('databaseconnection.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script>
        function confirmDelete() {
          return confirm("Do you want to delete this record?");
        }
    </script>
</head>

<body>
    <?php
    $id = $_GET['id'];
    if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
        try {
            $sql = "Delete FROM studentsinfo where srno = $id";
            $stmt = mysqli_query($dbconnection, $sql);
            if (!$stmt) {
                throw new Exception("Error in query: " . mysqli_error($dbconnection));
            }
        } catch (Exception $e) {
            echo  "Error :" . $e->getMessage();
        } finally {
            mysqli_close($dbconnection);
            header("Location: viewpage.php");
        }
    }
    ?>
    <script>
        if (confirmDelete()) {
            window.location.href = 'delete.php?id=<?php echo $id; ?>&confirm=yes';
        } else {
            window.location.href = 'viewpage.php';
        }
    </script>
</body>

</html>
