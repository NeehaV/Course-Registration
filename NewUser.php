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
 
        $studentiderror=$passworderror=$passwordseconderror=$nameerror=$phonenumbererror="";
        
        function ValidateStudentId($studentid) {
            if(!isset($studentid) || $studentid=='')
            {
              $aerror='Student ID cannot be blank';
              return $aerror;
            
            }
            else{return $aerror="";}
        } 
        
        function ValidateName($name) {
            if(!isset($name) || $name=='')
            {
              $aerror='Name cannot be blank';
              return $aerror;
            
            }
            else{return $aerror="";}
        } 
        
        function ValidatePhone($phone) {
            if(!isset($phone) || $phone=='')
            {
             $aerror='Phone Number cannot be blank';
             return $aerror;
            }
            elseif(!preg_match("/^[2-9][0-9][0-9][\s-]?[2-9][0-9][0-9][\s-]?([0-9]{4,4})$/",$phone))
            {
                $aerror='Incorrect phone number';
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
            elseif(!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9A-Za-z@#\-_$%^&+=§!\?]{6,20}$/',$password))
            {
                $aerror='Incorrect password';
              return $aerror;
            }
            else{return $aerror="";}
        } 
        
        function clearerror(){
            
            $aerror='';
            return $aerror;
        }
            
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") 
        {
        $studentid = trim(filter_input(INPUT_POST, 'studentid'));
        $name = trim(filter_input(INPUT_POST, 'name'));
        $phonenumber = filter_input(INPUT_POST, 'phonenumber');
        $userpassword = filter_input(INPUT_POST, 'userpassword');
        $passwordsecond = filter_input(INPUT_POST, 'passwordsecond');
        
        $studentiderror=ValidateStudentId($studentid);
        $nameerror=ValidateName($name);
        $phonenumbererror=ValidatePhone($phonenumber);
        $passworderror=ValidatePassword($userpassword);
        
        if(!isset($passwordsecond) || $passwordsecond=='')
        {
            $passwordseconderror="Password again is not blank";
        }elseif ($userpassword!=$passwordsecond) {
            $passwordseconderror="Password Again is not same as password";
        
        }else {
        
            $dbConnection = parse_ini_file("Lab5.ini");
        	
	     extract($dbConnection);
        
	    $pdo = new PDO($dsn, $user, $password);

            if($pdo !=null){
                $sameStudent;
                try{
                    $hashedpassword=password_hash($userpassword, PASSWORD_BCRYPT);
                    $sql = "SELECT count(*) FROM student where StudentId=$studentid"; 
                    $sameStudent = $pdo->query($sql)->fetchColumn();

                    if($sameStudent > 0)
                    {
                        $studentiderror="A student with this ID has already signed up";
                    }
                    else{
                      $sql ="insert into  student (StudentId,Name,Phone,Password) values ('$studentid','$name','$phonenumber','$hashedpassword') " ;
                      $result = $pdo->prepare($sql);
                      $result->execute();
                      $pdo = null;
                      $_SESSION['username'] = $name;
                      $_SESSION['studentId'] = $studentid;
                      header("Location: CourseSelection.php");
                      exit(); 
                    }
                } catch (PDOException $e) {
                     die($e->getMessage());
                }
            }
                
        }
        
        }
        
        
        ?>
        
        <div class="container">
          
            <form method="post" action="" id="depositform">
                
            <div class="container">
                <h1 class="text-primary">Sign Up</h1>
                <p>All fields are required</p>
                <br/>
                
                <div class="form-group row">
                    <label for="name" class="col-sm-2 col-form-label">Student ID:</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="studentid" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['studentid'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="text-danger"><?php echo $studentiderror;?></div>
                </div>

                <div class="form-group row">
                    <label for="name" class="col-sm-2 col-form-label">Name:</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="text-danger"><?php echo $nameerror;?></div>
                </div>
                
                <div class="form-group row">
                    <label for="phonenumber" class="col-sm-2 col-form-label">Phone Number:</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="phonenumber" placeholder="nnn-nnn-nnnn" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['phonenumber'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="text-danger"><?php echo $phonenumbererror;?></div>
                </div>
                
                <div class="form-group row">
                    <label for="emailid" class="col-sm-2 col-form-label">Password:</label>
                    <div class="col-sm-4">
                        <input type="password" class="form-control" name="userpassword" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['userpassword'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="text-danger"><?php echo $passworderror;?></div>
                </div>
                
                <div class="form-group row">
                    <label for="emailid" class="col-sm-2 col-form-label">Password Again:</label>
                    <div class="col-sm-4">
                        <input type="password" class="form-control" name="passwordsecond" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['passwordsecond'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="text-danger"><?php echo $passwordseconderror;?></div>
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