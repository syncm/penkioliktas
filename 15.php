<?php
//error_reporting(E_ERROR | E_WARNING | E_PARSE);
//prisijungimas prie Duomenų bazės
    $servername = 'localhost';
    $dbname = 'Auto';
    $username = 'root';
    $password = '';
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die('Nepavyko prisjungti: ' . $conn->connect_error);
    }

$results_per_page = 10;                     // įrašų skaičius puslapyje
$values = [];
// Ar gauname id per GET komandą
    if (array_key_exists('id', $_GET) && $_GET['id'] > 0) {
        $sql = "SELECT * FROM radars WHERE id = " . $_GET["id"];  //WHERE id=" . $_GET["id"];
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $values = $result->fetch_assoc();
        }
    }
// Į formą įvestų naujų duomenų išsaugojimas Duomenų bazėje
if ((isset($_POST['id'])) && ($_POST['id']) === ""){
    $stmt = $conn->prepare("INSERT INTO radars(data, number, distance, time) VALUES(?, ?, ?, ?)");

    $date = mysqli_real_escape_string($conn, $_POST['data']);
    $number = mysqli_real_escape_string($conn,  $_POST['number']);
    $distance = mysqli_real_escape_string($conn, $_POST['distance']);
    $time = mysqli_real_escape_string($conn, $_POST['time']);
    $stmt->bind_param("ssdd", $date, $number, $distance, $time);
    $stmt->execute();
    header("Location: " . $_SERVER['PHP_SELF']);        /* Redirect browser */
        exit();

}else if ((isset($_POST['id'])) && $_POST['id'] > 0 ){

    $stmt = $conn->prepare("UPDATE radars SET date = ?, number = ?, distance = ?, time = ? WHERE id = ?");

    $id = mysqli_real_escape_string($conn,$_REQUEST['id']);
    $date = mysqli_real_escape_string($conn, $_REQUEST['data']);
    $number = mysqli_real_escape_string($conn,  $_REQUEST['number']);
    $distance = mysqli_real_escape_string($conn, $_REQUEST['distance']);
    $time = mysqli_real_escape_string($conn, $_REQUEST['time']);
    $stmt->bind_param("ssddi", $date, $number, $distance, $time, $id);
    $stmt->execute();
    header("Location: " . $_SERVER['PHP_SELF']);        /* Redirect browser */
        exit();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>15 Uzduotis</title>
        <meta charset="UTF-8">
    </head>
<body>
<!-- Duomenų įvedimo Forma -->
<h1>Įveskite duomenis:</h1>
<form action="" method="post">
    Date: <br>
    <input type="text" name="date" value = "<?php if (isset($_GET['id'])): echo $values['data']; endif; ?>"><br>
    Auto number: <br>
    <input type="text" name="number" value = "<?php if (isset($_GET['id'])): echo $values['number']; endif;  ?>"><br>
    Distance: <br>
    <input type="text" name="distance"value = "<?php if (isset($_GET['id'])): echo $values['distance']; endif;  ?>"><br>
    Time: <br>
    <input type="text" name="time"value = "<?php if (isset($_GET['id'])): echo $values['time']; endif;  ?>"><br>
    <input type="hidden" name="id" value="<?php if (isset($_GET['id'])): echo $values['id']; endif;  ?>"><br>
    <input type="submit" name ="send" value="Send"><br><br>
</form>

<?php
if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 1;
}
$start_from = ($page-1) * $results_per_page;
?>

<!-- Lentelė -->
    <?php
    $sql = "SELECT *, `distance`/`time`*3.6 as `speed` FROM radars ORDER BY `id`, `number` DESC LIMIT $start_from , $results_per_page" ;
    $result = $conn->query($sql);
    //$row = $result->fetch_assoc();
    if ($result->num_rows > 0): ?>
        <h3>Rūšiuojama pagal greitį, mažėjimo tvarka: </h3>
        <table border="1">
            <tr>
            <th bgcolor="#CCCCCC">Nr.</th>
            <th bgcolor="#CCCCCC">Date</th>
            <th bgcolor="#CCCCCC">Number</th>
            <th bgcolor="#CCCCCC">Distance,m</th>
            <th bgcolor="#CCCCCC">Time,s</th>
            <th bgcolor="#CCCCCC">Speed, km/h</th>
            <th bgcolor="#CCCCCC"></th>
            </tr>
            <?php while($row = $result->fetch_assoc()) :    // output data of each row?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['data']; ?></td>
                    <td><?php echo $row['number']; ?></td>
                    <td><?php echo $row['distance']; ?></td>
                    <td><?php echo $row['time']; ?></td>
                    <td align="right"><?php echo round($row['speed'], 2); ?></td>
                    <td><a href = "?id=<?php echo $row['id']; ?>">Update</a></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else:
      echo "Nėra duomenų.";
    endif;
// Puslapiavimas
    ?>
<style>
.curPage {
    font-size: 22px;
    color: blue;
}
</style>

<?php
$sql = "SELECT COUNT(ID) AS total FROM radars";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results
echo "Puslapiai: ";
for ($i=1; $i<=$total_pages; $i++) {                    // print links for all pages
            echo "<a href='15.php?page=".$i."'";
            if ($i==$page)  echo " class='curPage'";
                echo ">" .$i ."</a> ... ";
};
$conn->close();
?>
</body>
</html>
