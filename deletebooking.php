<?php
include "converted template/header.php";
include "converted template/menu.php";
echo '<div id="site_content">';
include "converted template/sidebar.php";
echo '<div id="content">';

include "config.php";
$DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

if (mysqli_connect_errno()) {
    echo "Error:Unable to connect to MySql." . mysqli_connect_error();
    exit; //stop processing the page further.
}

function cleanInput($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

    //check if id exists
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $id = $_GET['id'];
        if (empty($id) or !is_numeric($id)) {
            echo "<h2>Invalid booking ID</h2>"; //simple error feedback
            exit;
        }
    }

    //delete ticket
    if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Delete')) {
        $error = 0;
        $msg = "Error:";

        //we try to convert to number - intval function(return to the integer of a variable)
        if (isset($_POST['id']) and !empty($_POST['id']) and is_integer(intval($_POST['id']))) {
            //code here
            $id = cleanInput($_POST['id']);

        } else {
            //code here
            $error++; //bump the error flag
            $msg .= 'Invalid booking ID '; //append error message
            $id = 0;
        }

        if ($error == 0 and $id > 0) {
            //code here
            $query = "DELETE FROM booking WHERE bookingID=?";
            $stmt = mysqli_prepare($DBC, $query); //prepare the query
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            echo "<h2>Booking details deleted.</h2>";
        } else {
            echo "<h5>$msg</h5>" . PHP_EOL;
        }
    }

    $query = 'SELECT booking.bookingID, room.roomname, room.roomtype, room.beds, booking.checkindate, booking.checkoutdate, booking.contactnumber, booking.bookingextras, booking.roomreview
             FROM booking
             INNER JOIN room on booking.roomID = room.roomID
             WHERE bookingID =' . $id;



    $result = mysqli_query($DBC, $query);
    $rowcount = mysqli_num_rows($result);
    ?>

    <!-- We can add a menu bar here to go back -->
    <h1>Booking Preview Before Deletion</h1>
    <h2><a href="listbookings.php">[Return to the Booking listing]</a>
        <a href="index.php">[Return to the main page]</a>
    </h2>
    <?php
    if ($rowcount > 0) {

        echo "<fieldset><legend>Booking Detail #$id</legend><dl>";
        $row = mysqli_fetch_assoc($result);
        $id = $row['bookingID'];

        echo "<dt>Room name: </dt><dd>" . $row['roomname'] . "</dd>" . PHP_EOL;
        echo "<dt>checkindate: </dt><dd>" . $row['checkindate'] . "</dd>" . PHP_EOL;
        echo "<dt>Checkoutdate: </dt><dd>" . $row['checkoutdate'] . "</dd>" . PHP_EOL;
        echo '</dl></fieldset>' . PHP_EOL;


        ?>

        <form method="POST" action="deletebooking.php">

            <h4>Are you sure you want to delete this ticket?</h4>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="submit" name="submit" value="Delete">
            <a href="listbookings.php">Cancel</a>

        </form>

        <?php
    } else
        echo "<h5>No ticket found! Possbily deleted!</h5>";
    mysqli_free_result($result);
    mysqli_close($DBC);
    ?>
<?php
echo '</div></div>';
include "converted template/footer.php";
?>