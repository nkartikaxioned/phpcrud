<?php require_once('databaseconnection.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>

<body>
  <?php
  try {
    $sql = 'SELECT * FROM studentsinfo';
    $stmt = mysqli_query($dbconnection, $sql);

    if (!$stmt) {
      throw new Exception("Error in query: " . mysqli_error($dbconnection));
    }
    $total = mysqli_num_rows($stmt);
    if ($total != 0) {
  ?>
      <section class="display-section">
        <h2 align="center">View Page</h2>
        <table align="center" border="1px" cellpadding="8px" cellspacing="5px">
          <tr>
            <th>First Name</th>
            <th>Email</th>
            <th>Qualification</th>
            <th>Gender</th>
            <th>File Name</th>
            <th>Edit/Delete</th>
          </tr>
        <?php
        while ($result = mysqli_fetch_assoc($stmt)) {
          echo "
          <tr>
              <td>" . $result['name'] . "</td>
              <td>" . $result['email'] . "</td>
              <td>" . $result['qualification'] . "</td>
              <td>" . $result['gender'] . "</td>
              <td>" . $result['filename'] . "</td>
              <td><a href='index.php?userid={$result['srno']}'>Edit</a> | <a href='delete.php?id={$res['srno']}'>Delete</a></td>
           </tr>
          ";
        }
      } else {
        echo "No records found";
      }
        ?>
        </table>
      </section>
    <?php
  } catch (Exception $e) {
    echo "Error: " . $e->getMessage();
  } finally {
    mysqli_close($dbconnection);
  }
    ?>
</body>

</html>