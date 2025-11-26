<?php
include "converted template/header.php";
include "converted template/menu.php";
echo '<div id="site_content">';
include "converted template/sidebar.php";
echo '<div id="content">';

function cleanInput($data) {
  return htmlspecialchars(stripslashes(trim($data)));
}


if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Add')) {
    
    include "config.php"; 
    $DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

    if (mysqli_connect_errno()) {
        echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
        exit; 
    };


//roomname
    $error = 0; 
    $msg = 'Error: ';
    if (isset($_POST['roomname']) and !empty($_POST['roomname']) and is_string($_POST['roomname'])) {
       $fn = cleanInput($_POST['roomname']); 
       $roomname = (strlen($fn)>50)?substr($fn,1,50):$fn; //check length and clip if too big
       //we would also do context checking here for contents, etc       
    } else {
       $error++; //bump the error flag
       $msg .= 'Invalid roomname '; //append eror message
       $roomname = '';  
    } 
 
//description
       $description = cleanInput($_POST['description']);        
//roomtype
       $roomtype = cleanInput($_POST['roomtype']);            
//beds    
       $beds = cleanInput($_POST['beds']);        
       
//save the room data if the error flag is still clear
    if ($error == 0) {
        $query = "INSERT INTO room (roomname,description,roomtype,beds) VALUES (?,?,?,?)";
        $stmt = mysqli_prepare($DBC,$query); //prepare the query
        $beds_i = intval($beds);
        mysqli_stmt_bind_param($stmt,'sssi', $roomname, $description, $roomtype, $beds_i);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);    
        echo "<h2>New room added to the list</h2>";        
    } else { 
      echo "<h2>$msg</h2>".PHP_EOL;
    }      
    mysqli_close($DBC); //close the connection once done
}
?>
<h1>Add a new room</h1>
<h2><a href='listrooms.php'>[Return to the room listing]</a><a href='/bnb_2/'>[Return to the main page]</a></h2>

<form method="POST" action="addroom.php">
  <p>
    <label for="roomname">Room name: </label>
    <input type="text" id="roomname" name="roomname" minlength="5" maxlength="50" required> 
  </p> 
  <p>
    <label for="description">Description: </label>
    <input type="text" id="description" size="100" name="description" minlength="5" maxlength="200" required> 
  </p>  
  <p>  
    <label for="roomtype">Room type: </label>
    <input type="radio" id="roomtype" name="roomtype" value="S"> Single 
    <input type="radio" id="roomtype" name="roomtype" value="D" Checked> Double 
   </p>
  <p>
    <label for="beds">Beds (1-5): </label>
    <input type="number" id="beds" name="beds" min="1" max="5" value="1" required> 
  </p> 
  
   <input type="submit" name="submit" value="Add">
 </form>
<?php
echo '</div></div>';
include "converted template/footer.php";
?>
  