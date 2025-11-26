<?php
include "converted template/header.php";
include "converted template/menu.php";
echo '<div id="site_content">';
include "converted template/sidebar.php";
echo '<div id="content">';

// include jQuery UI scripts in the content area
?>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script>
        $(document).ready(function () {
            $.datepicker.setDefaults({
                dateFormat: 'yy-mm-dd'
            });
            $(function () {
                $("#depa").datepicker();
                $("#arr").datepicker();
            });
        });
    </script>
<?php
include "config.php";
$DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);
    if (mysqli_connect_errno()) {
        echo "Error: Unable to connect to MySQL." . mysqli_connect_error();
        exit;
    }

    function cleanInput($data)
    {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    // Check that id exists and is still valid from the URL
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = $_GET['id'];
    } else {
        echo "<h2>Invalid or missing booking ID</h2>";
        exit;
    }

    // Handle form submission for update
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit']) && $_POST['submit'] == 'Update') {
        $room = cleanInput($_POST['room']);
        $depa = $_POST['depa'];
        $arr = $_POST['arr'];
        $contact = cleanInput($_POST['contact']);
        $booking = cleanInput($_POST['booking']);
        $review = cleanInput($_POST['review']);

        // Update booking query using prepared statement
        $upd = "UPDATE booking SET roomID=?, checkindate=?, checkoutdate=?, contactnumber=?, bookingextras=?, roomreview=? WHERE bookingID=?";
        $stmt = mysqli_prepare($DBC, $upd);

        // Check for query preparation failure
        if (!$stmt) {
            echo "<h2>Error preparing SQL query: " . mysqli_error($DBC) . "</h2>";
            exit;
        }

        // Bind the parameters (roomID, checkin, checkout, contact, booking extras, review, bookingID)
        mysqli_stmt_bind_param($stmt, 'isssssi', $room, $depa, $arr, $contact, $booking, $review, $id);

        // Execute the query and check if it succeeds
        if (mysqli_stmt_execute($stmt)) {
            echo "<h2>Booking updated successfully.</h2>";
        } else {
            echo "<h2>Error updating booking: " . mysqli_error($DBC) . "</h2>";
        }

        mysqli_stmt_close($stmt);
    }

    //  the current booking data from the database is fetched here
    $query = "SELECT booking.bookingID, room.roomID, booking.checkindate, booking.checkoutdate, room.roomname, booking.contactnumber, booking.bookingextras, booking.roomreview, room.roomtype, room.beds
              FROM booking
              INNER JOIN room ON booking.roomID = room.roomID
              WHERE booking.bookingID = ?";
    $stmt = mysqli_prepare($DBC, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    // Check if booking was found
    if (!$row) {
        echo "<h2>Booking not found</h2>";
        exit;
    }
    ?>

    <h1>Edit a booking</h1>
    <h2>
        <a href="listbookings.php">[Return to the tickets listing]</a>
        <a href="index.php">[Return to main page]</a>
    </h2>

    <form action="editbooking.php" method="POST">
        <p>
            <label for="room">Room:</label>
            <select name="room" id="room" required>
                <option value="<?php echo $row['roomID']; ?>" selected>
                    <?php echo $row['roomname'] . " " . $row['roomtype'] . " " . $row['beds']; ?>
                </option>
                <!-- We can populate more room options here if needed -->
            </select>
        </p>
        <p>
            <label for="arr">Arrival Date:</label>
            <input type="text" id="arr" name="arr" required placeholder="yy-mm-dd" value="<?php echo $row['checkindate']; ?>">
        </p>

        <p>
            <label for="depa">Departure Date:</label>
            <input type="text" id="depa" name="depa" required placeholder="yy-mm-dd" value="<?php echo $row['checkoutdate']; ?>">
        </p>

       

        <p>
            <label for="contact">Contact number:</label>
            <input type="text" id="contact" name="contact" placeholder="(###) ### ####" value="<?php echo $row['contactnumber']; ?>">
        </p>

        <p>
            <label for="booking">Booking extras:</label>
            <input type="text" id="booking" name="booking" value="<?php echo $row['bookingextras']; ?>">
        </p>

        <p>
            <label for="review">Room review:</label>
            <input type="text" id="review" name="review" value="<?php echo $row['roomreview']; ?>">
        </p>

        <p>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="submit" name="submit" value="Update">
            <a href="listbookings.php">Cancel</a>
        </p>
    </form>

    <?php
    // Free result and close connection
    mysqli_free_result($result);
    mysqli_close($DBC);
    ?>

<?php
echo '</div></div>';
include "converted template/footer.php";
?>
