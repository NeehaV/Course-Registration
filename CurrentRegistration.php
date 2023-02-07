<?php 
session_start();
$error="";
$studentId = $_SESSION['studentId'];
if (!isset($_SESSION['username'])) {
    header("Location: Login.php");
    exit();
}
$username = $_SESSION['username'];
include("./common/header.php"); 
//include("./common/delete.js"); 
$dbConnection = parse_ini_file("Lab5.ini");
extract($dbConnection);
 ?>
<style><?php include ("./common/css/Site.css");

 ?></style>

     <?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //saving checkbox values
    if(!empty($_POST['time'])){
        // Loop to store and display values of individual checked checkbox.
        foreach($_POST['time'] as $selected){
          $selected=$_POST['time'];
        }
        
        //delete course details
    $pdo3 = new PDO($dsn, $user, $password);
    foreach($selected as &$coursecode)
    {
        
        $query4 = $pdo3->prepare("Delete From Registration where CourseCode='$coursecode'");
        $query4->execute();
        
        
    }
    }
    else {
        $error="please select atleast one";
    }
    
        
}
?>

<div class="container">
    <h1 style="text-align: center">Course Registration</h1>
    <p>Welcome <?php echo "$username" ?>!(not you? change user <a href="">here</a>), the followings are your current registrations </p>
    <div class="text-danger"><?php echo $error;?></div>
    <form method="POST" action="" >
        <table>
            <tr>
                <th>Year</th>
                <th>Term</th>
                <th>Course Code</th>
                <th>Course Title</th>
                <th>Hours</th>
                <th>Select</th>
            </tr>
            <?php
            //getting registered semestercode
            $pdo1 = new PDO($dsn, $user, $password);
            $query = $pdo1->prepare("SELECT DISTINCT SemesterCode FROM Registration");
            $query->execute();
            while ($result = $query->fetch(PDO::FETCH_ASSOC))
            {
                //getting course details for each semestercode
               $hours=0;
               $RegisteredSemester = $result['SemesterCode'];
               $pdo2 = new PDO($dsn, $user, $password);
               $query2 = $pdo2->prepare("Select sem.Year,sem.Term,co.CourseCode,co.Title,co.WeeklyHours from Semester as sem left join Registration as r ON sem.SemesterCode=r.SemesterCode left join Course as co ON co.CourseCode=r.CourseCode where r.SemesterCode='$RegisteredSemester' AND StudentId='$studentId'");
               $query2->execute();
               while($row = $query2->fetch()) {
                   $hours=$hours+$row['WeeklyHours'];
                   echo "<tr> \n";
                   echo '<td>' . $row['Year'] . '</td>';
                   echo '<td>' . $row['Term'] . '</td>';
                   echo '<td>' . $row['CourseCode'] . '</td>';
                   echo '<td>' . $row['Title'] . '</td>';
                   echo '<td>' . $row['WeeklyHours'] . '</td>';
                   echo '<td><input type ="checkbox" name = "time[]" value=' . $row['CourseCode'] . '  /></td>';
                   echo "</tr>";
               }
               if($hours!=0)
               {
                   echo"<tr>";
                   echo"<td></td>";
                   echo"<td></td>";
                   echo"<td></td>";
                   echo"<td style='text-align:right; font-weight: bold;'>Total Weekly Hours</td>";
                   echo"<td style='font-weight: bold;'>".$hours.'</td>';
                   echo"<td></td>";
                   echo"</tr>";
               }
               
            }
            
            ?>
        </table>
        <div class="form-group row col-sm-2"> 
            <button class="btn btn-danger" type="submit" onClick="return confirm('Are you sure want to Delete ?')?this.form.action='<?php echo $_SERVER['PHP_SELF'] ?>': false">Delete</button>
            <button type="reset" class="btn btn-primary" value="Reset" name="clear" >Clear</button>
        </div>
        
    </form> 
    
</div>

<?php include('./common/footer.php'); ?>

</html>