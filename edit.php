<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<?php include("databaseconnection.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php 
    $nameErr = $emailErr = $qualificationErr = $genderErr = $fileErr = "";
    $name = $email = $qualification = $gender = $filename = "";
    $errorMsg = "";

    if (isset($_GET['id'])) {
        $edit_id = $_GET['id'];
    
        $editsql = "SELECT * FROM studentsinfo WHERE srno = $edit_id";
        $editstmt = mysqli_query($dbconnection, $editsql);
    
        try {
          if (!$editstmt) {
            throw new Exception("Error Fetching Data" . mysqli_error($dbconnection));
          }
          if (mysqli_num_rows($editstmt) > 0) {
            $result = mysqli_fetch_assoc($editstmt);
            $name = $result['name'];
            $email = $result['email'];
            $qualification = $result['qualification'];
            $gender = $result['gender'];
            $filename = $result['filename'];
          } else {
            throw new Exception("No data found for editing with ID: $edit_id");
          }
        } catch (Exception $e) {
          echo "Error" . $e->getMessage();
        }
      }

    
  function validateInput($data)
  {
    $data = trim($data);
    $data = htmlspecialchars($data);
    return $data;
  }

  function emailExists($conn, $email)
  {
    $stmt = $conn->prepare("SELECT email FROM studentsinfo WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    $count = $stmt->num_rows;
    $stmt->close();

    return $count > 0;
  }

  function validateName($name)
  {
    if (empty($name)) {
      return "Name is required";
    } elseif (strlen($name) < 3) {
      return "Valid Name is required";
    } elseif (!preg_match("/^[a-zA-Z-']*$/", $name)) {
      return "Only letters are allowed";
    }
    return "";
  }

  function validateEmail($conn, $email)
  {
    if (empty($email)) {
      return "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return "Invalid email format";
    }
    return "";
  }

  function validateQualification($qualification)
  {
    if ($qualification === "select") {
      return "Qualification is required";
    }
    return "";
  }

  function validateGender($gender)
  {
    if (!isset($gender) || empty($gender)) {
      return "Gender is required";
    }
    return "";
  }

  function validateFile($filename, $fileSize)
  {
    if (empty($filename)) {
      return 'File is required';
    } elseif ($fileSize > 1000000) {
      return "Sorry, your file is too large.";
    } elseif (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) !== "pdf") {
      return "Sorry, only pdf files are allowed.";
    }

    return "";
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = validateInput($_POST['fname']);
    $email = validateInput($_POST['email']);
    $qualification = validateInput($_POST['education']);
    $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
    $fileErr = '';

    $nameErr = validateName($name);
    // if (!empty($_POST['id'])) {
    //   $emailErr = validateEmail($dbconnection, $email);
    // }
    // $emailErr = validateEmail($dbconnection, $email);
    $qualificationErr = validateQualification($qualification);
    $genderErr = validateGender($gender);

    if (!empty($_FILES['fileupload']['name'])) {
      $filename = validateInput($_FILES['fileupload']['name']);
      $fileSize = $_FILES['fileupload']['size'];
      $fileErr = validateFile($filename, $fileSize);
    } else {
      $fileErr = 'File is required';
    }

    if (isset($_POST['id']) && !empty($_POST['id'])) {
      $edit_id = mysqli_real_escape_string($dbconnection, $_POST['id']);

      if (empty($nameErr) && empty($emailErr) && empty($qualificationErr) && empty($genderErr) && empty($fileErr)) {
        try {
          $updatesql = "UPDATE studentsinfo SET name=?, email=?, qualification=?, gender=?, filename=? where srno = ?";
          $stmt = mysqli_prepare($dbconnection, $updatesql);
          if (!$stmt) {
            throw new Exception("Error in preparing statement" . mysqli_connect($dbconnection));
          }
          mysqli_stmt_bind_param($stmt, "sssssi", $name, $email, $qualification, $gender, $filename, $edit_id);

          $result = mysqli_stmt_execute($stmt);
          if ($result) {
            echo "<h2>Data Updated Successfully</h2>";

            header("Location: viewpage.php");
            exit();
          } else {
            echo "Error updating record: " . mysqli_error($conn);
          }
          mysqli_stmt_close($stmt);
        } catch (Exception $e) {
          echo "Error" . $e->getMessage();
        }
      }
    }
  }

  ?>

  <section class="form-section">
    <h2>Enter Student Detail :</h2>
    <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?php echo $edit_id; ?>">
      <div class="form-field">
        <label for="fname">Name :</label>
        <input type="text" name="fname" class="fname" value="<?php echo $name; ?>"><br>
        <span class="error"><?php echo $nameErr; ?></span>
      </div>
      <br>
      <div class="form-field">
        <label for="email">Email :</label>
        <input type="text" name="email" class="email" value="<?php echo $email; ?>"> <br>
        <span class="error"><?php echo $emailErr; ?></span>
      </div>
      <br>
      <div class="form-input">
        <label for="education">Highest Qualificaton :</label>
        <select name="education" id="education">
          <option value="select" <?php echo ($qualification == 'select') ? 'selected' : ''; ?>>select</option>
          <option value="12th" <?php echo ($qualification == '12th') ? 'selected' : ''; ?>>12th</option>
          <option value="diploma" <?php echo ($qualification == 'diploma') ? 'selected' : ''; ?>>Diploma</option>
          <option value="B.E" <?php echo ($qualification == 'B.E') ? 'selected' : ''; ?>>B.E/B.Tech</option>
        </select><br>
        <span class="error"><?php echo $qualificationErr; ?></span>
      </div>
      <br>
      <div class="form-field">
        <label for="gender">Gender :</label>
        <input type="radio" name="gender" value="male" <?php echo ($gender == 'male') ? 'checked' : ''; ?>> Male
        <input type="radio" name="gender" value="female" <?php echo ($gender == 'female') ? 'checked' : ''; ?>> Female
        <input type="radio" name="gender" value="other" <?php echo ($gender == 'other') ? 'checked' : ''; ?>> Other<br>
        <span class="error"><?php echo $genderErr; ?></span>
      </div>
      <br>
      <div class="file-uploads">
        <input type="file" name="fileupload"><br>
        <span class="error"><?php echo $fileErr; ?></span>
      </div>
      <br>
      <div class="submit-btn">
        <input type="submit" name="submit">
      </div>
    </form>
  </section>
  <p class="main-error"><?php echo $errorMsg; ?></p>
</body>

</html>
</body>
</html>