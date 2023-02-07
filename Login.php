<?php 
include("./common/header.php"); 

session_start();



 ?>
<html>
    <head>
        <title>Online Course Registration</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    </head>
    <body>
        <?php
        $error="";
        $studentiderror="";
        $passworderror="";
        
        function ValidateStudentId($studentid) {
            if(!isset($studentid) || $studentid=='')
            {
              $aerror='Student ID cannot be blank';
              return $aerror;
            
            }
            else{return $aerror="";}
        } 
        
        
        function ValidatePassword($password) {
            if(!isset($password) || $password=='')
            {
             $aerror='Password cannot be blank';
             return $aerror;
            }
        } 
        
        function clearerror(){
            
            $aerror='';
            return $aerror;
        }
            
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") 
        {
        $studentid = trim(filter_input(INPUT_POST, 'studentid'));
        $userpassword = filter_input(INPUT_POST, 'userpassword');
 
        
        $studentiderror=ValidateStudentId($studentid);
        $passworderror=ValidatePassword($userpassword);
        
        
        
        if(($studentiderror=="")&&($passworderror==""))
        {
            $dbConnection = parse_ini_file("Lab5.ini");
        	
	     extract($dbConnection);
        
	    $pdo = new PDO($dsn, $user, $password);
            if($pdo !=null){
                try{                   
                    $sql = "SELECT StudentId FROM student where StudentId=$studentid"; 
                    $id = $pdo->query($sql)->fetchColumn();
                    $sql1 = "SELECT Password FROM student where StudentId=$studentid";
                    $pass = $pdo->query($sql1)->fetchColumn();
                    $sql2 = "SELECT Name FROM student where StudentId=$studentid";
                    $username = $pdo->query($sql2)->fetchColumn();
                    if($id == $studentid){
                        if(password_verify($userpassword, $pass)){
                        $_SESSION['username'] = $username;
                        $_SESSION['studentId'] = $studentid;
                        header("Location: CourseSelection.php");
                        exit();
                    }}
                    else{
                      $error = "Incorrect Student ID and/or Password!";
                       
                    }
                } catch (PDOException $e) {
                     die($e->getMessage());
                }
            }
              
        }
        else
        {
            
            $_SESSION['login'] = '';
            session_destroy();
        }
        if(isset($_POST['clear']))
        {
            
            clear();
        }
        }
        
        
        ?>
        
        <div class="container">
          
            <form method="post" action="" id="depositform">
                
            <div class="container">
                <h1 class="text-primary">Log In</h1>
                <p>You need to <a href="NewUser.php">sign up</a> if you are a new user</p>
                <br/>
                <div class="text-danger"><?php echo $error;?></div>
                
                <div class="form-group row">
                    <label for="name" class="col-sm-2 col-form-label">Student ID:</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="studentid" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['studentid'], ENT_QUOTES) : ''; ?>">
                        <div class="text-danger"><?php echo $studentiderror;?></div>
                    </div>
               
                </div>
                
                <div class="form-group row">
                    <label for="emailid" class="col-sm-2 col-form-label">Password:</label>
                    <div class="col-sm-4">
                        <input type="password" class="form-control" name="userpassword" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['userpassword'], ENT_QUOTES) : ''; ?>">
                    <div class="text-danger"><?php echo $passworderror;?></div>
                    </div>
                    
                </div>
                
                 <br/>
                 <div class="form-group row col-sm-2">                                       
                    <button type="submit" class="btn btn-primary" value="Submit" name="submit">Submit</button>
                    <button type="reset" class="btn btn-primary" value="Reset" name="clear" >Clear</button>
                </div>
            </div>                     
        </form>
    
        </div>
    </body>

<?php include('./common/footer.php'); ?>

</html>