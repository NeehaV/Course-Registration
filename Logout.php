<?php 

include("./common/header.php");
session_start();
session_destroy();
header("Location: Login.php");
exit();
 ?>

<div class="container">
   
    
</div>

<?php include('./common/footer.php'); ?>

</html>