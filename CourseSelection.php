<?php

session_start();
$error = "";
$studentId = $_SESSION['studentId'];
if (!isset($_SESSION['username'])) {
    header("Location: Login.php");
    exit();
}
$hours=0;


$balancehours=0;
include("./common/header.php"); ?>
<style><?php include ("./common/css/Site.css");

 ?></style>
<?php
$username = $_SESSION['username'];
$dbConnection = parse_ini_file("Lab5.ini");
extract($dbConnection);
$myPdo = new PDO($dsn, $user, $password);       

$query = $myPdo->query("SELECT SemesterCode as code, Term, Year FROM semester");

if (isset($_POST["semester"])) {
    $error="";
    $semester = $_POST["semester"];
    $query = $myPdo->query("SELECT SemesterCode as code, Term, Year FROM semester");
//for weeklyhours
    $myPdoh = new PDO($dsn, $user, $password);  
    $queryh = $myPdoh->prepare("Select WeeklyHours from course as co left join registration as r ON co.CourseCode=r.CourseCode where r.StudentId='$studentId' AND r.SemesterCode='$semester'");
    $queryh->execute();
    
    while ($result = $queryh->fetch(PDO::FETCH_ASSOC))
    {
        $hours = $hours+$result['WeeklyHours'];
    }
    $balancehours = 16 - $hours;
}


if (isset($_POST['neeha'])) {
    
    $whours=0;
    
    $selected[]="";
    
    if(!empty($_POST['time'])){
        // Loop to store and display values of individual checked checkbox.
        foreach($_POST['time'] as $selected){
        $selected=$_POST['time'];
        }
        $pdo = new PDO($dsn, $user, $password);
        foreach($selected as &$coursecode)
        {
        
           $sqlhr = $pdo->prepare("Select WeeklyHours from Course where CourseCode='$coursecode'");
           $queryh->execute();
           $whours = $whours+$queryh->fetchColumn();
        
        }
        if(($hours+$whours)>17)
        {
         $error = "Your Selection exceed the max weekly hours";
        }
    }else
        {
            $error="select atleast one course";
        }
        
    
    
    if($error=="")
    {
        
        $error="";
        $pdo3 = new PDO($dsn, $user, $password);
    
        foreach ($selected as &$courseCode) {
            $sql2 = "INSERT INTO Registration VALUES (:studentId,:courseCode,:semesterCode)";
            $pStmt = $pdo3 -> prepare($sql2);
            $pStmt -> bindParam(':studentId', $studentId);
            $pStmt -> bindParam(':courseCode', $courseCode);
            $pStmt -> bindParam(':semesterCode', $semester);
            $pStmt -> execute();
        }
        
        $hours=0;
        $myPdoh1 = new PDO($dsn, $user, $password);  
        $queryh = $myPdoh1->prepare("Select WeeklyHours from course as co left join registration as r ON co.CourseCode=r.CourseCode where r.StudentId='$studentId' AND r.SemesterCode='$semester'");
        $queryh->execute();
    
         while ($result = $queryh->fetch(PDO::FETCH_ASSOC))
        {
           $hours = $hours+$result['WeeklyHours'];
        }
        $balancehours = 16 - $hours;
    }
    
}

?>
<div class="container">
    
    <h1 style="text-align: center">Course Selection</h1>
    <p>Welcome <?php echo "$username" ?>!(not you? change user <a href="">here</a>)</p>
    <p>You have registered <?php echo"$hours"?> hours for the selected semester</p>
    <p>You can register <?php echo"$balancehours" ?> more hours of course(s) for the semester</p>
    <p>Please note that the courses you have registered will not be displayed in the list</p>
    <br />
    <div class="container">
        <form method="POST" action="" >
            <div class="text-danger"><?php echo $error;?></div>
            <div align="right">
                <select name="semester" onchange="this.form.submit()">
                    <?php
            
            print(empty($semester) ? "<option value='select'>Select your Preffered Semester</option>"  :  "<option value='" . $semester . "'>" . $semester . "</option>");
            while ($row = $query->fetch(PDO::FETCH_NUM)) {
                      
                      echo "<option value='".$row[0]."'>".$row[1]." ".$row[2]."</option>";
                        }
                        
            ?>
                    </select>
            </div>
              
            
        
         <?php
        if (empty($semester)) {
            echo"Please Select a Semester";
        } else {
            
            echo '<table>';
            echo "<tr><th>Course Code</th><th>Title</th><th>Hours</th><th>Select</th></tr> \n";
            
            $sql = <<<SQL_Query
                Select co.CourseCode, Title, WeeklyHours, co.SemesterCode from CourseOffer as co 
                left join Course as c on co.CourseCode = c.CourseCode 
                left join Semester as s on s.SemesterCode = co.SemesterCode
      SQL_Query;
                        $sql .= " where s.SemesterCode='";
                        
                        $sql .= "$semester' and co.courseCode NOT IN (select courseCode from registration ";
                        $sql .= "where studentId = '$studentId' and semesterCode = '$semester')";

                        $data = $myPdo->query($sql);
                        
            foreach ($data as $item) {
                echo "<tr> \n";
                echo '<td>' . $item['CourseCode'] . '</td>';
                echo '<td>' . $item['Title'] . '</td>';
                echo '<td>' . $item['WeeklyHours'] . '</td>';
                echo '<td><input type ="checkbox" name = "time[]" value=' . $item['CourseCode'] . '  /></td>';
                echo "</tr>";
            }
            
            echo '</table>';
            echo '<br />';
            echo '<br />';
            echo '<button type="submit" name="neeha" style="Text-align:right">Submit</button>';
            echo '<button type="clear">Clear</button>';
            echo '<br />';
            echo '<br />';
        }
        ?>
    </form>
        
</div>
</div>

    <?php include('./common/footer.php'); ?>