<?php
//create database object
$db = new PDO('****', '****', '****', array(PDO::ATTR_EMULATE_PREPARES => false, 
                                                                                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
//set errors variable
$errors = '';
//set server variables
$ip = $_SERVER['REMOTE_ADDR'];
$browser = $_SERVER['HTTP_USER_AGENT'];
$server_data = $ip.', '.$browser;

//sanitize post variables
if (isset($_POST['submit'])) {
    if ($_POST['email'] != "") {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors .= "<p class='text-danger'>$email is not valid email silly</p>.";
        }
    } else{ 
            $errors .= "<p class='text-danger'>Please enter your email address.</p>";
    }
    if ($_POST['message'] != ""){
        $_POST['message'] = filter_var($_POST['message'], FILTER_SANITIZE_STRING);
        $message = $_POST['message'];
    }else{
            $errors .= "<p class='text-danger'>Write a message dude!</p>";
        } 
    //prepare checkbox data for db    
    if (isset($_POST['check'])){
        $check = implode(', ',$_POST['check']);
    }
    // if form passes validation, submit data
    if($errors == ""){
        $stmt = $db->prepare("INSERT INTO test_table (data_a, data_b, data_c, data_d) VALUES(:checkb, :email, :message, :serverdata)");
        $stmt->bindParam(':checkb', $check);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':serverdata', $server_data);
        $stmt->execute(); 
    } 
}

if(isset($_POST['delete'])){
    $stmt = $db->prepare("DELETE FROM `test_table` WHERE 1");
    $stmt->execute();
}

?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container">
        <h3>Form practice</h3>
            <div class="col-md-6">
            <p>The inputs are sanitized and the SQL insert is a prepared statement with all the input variables entered into parameters.</p>
            <?php if(isset($errors)){echo $errors;}?>
            <?php if(isset($test)){echo $test;}?>
                <form role="form" method="post">
                    <div class="form-group">
                        <span class="label label-primary">Email</span>
                        <input type="text" class="form-control" name="email">
                    </div>
                    <div class="form-group">
                        <span class="label label-primary">Message</span>    
                        <input type="text" class="form-control" name="message">
                    </div>    
                    <div class="checkbox">
                        <label><input type="checkbox" name="check[]" value="Checkbox A">Option 1</label>
                    </div>
                    <div class="checkbox">
                        <label><input type="checkbox" name="check[]" value="Checkbox B">Option 2</label>
                    </div>
                    <div class="checkbox">
                        <label><input type="checkbox" name="check[]" value="Checkbox C">Option 3</label>
                    </div>    
                        <input type="submit" class="btn btn-primary" name="submit">

                </form>
            </div>
            <div class="col-md-6">
            <h4>Sanitized inputs</h4>
                <ul class="list-group">
                    <li class="list-group-item">Your Sanitized email:<span class="text-success"><?php if(isset($email)) echo $email;?></span></li>
                    <li class="list-group-item">Your Sanitized message: <?php if(isset($message))echo $message;?></li>
                </ul>
            </div>
            <div class="col-md-12">
                <table class="table table-striped">
                <tr>
                    <td>The Data</td>
                    <td>Email</td>
                    <td>Message</td>
                    <td>Server Data</td> 
                </tr>
                    <?php foreach($db->query('SELECT * FROM test_table') as $row) {
                        echo '<tr><td>'.$row[0].'</td><td>'.$row[1].'</td><td>'.$row[2].'</td><td>'.$row[3].'</td></tr>';
                    }?>                 
                </table>
                <form method="post">
                <button type="submit" name="delete" class="btn btn-primary">Start Fresh</button>
                </form>
            </div>
        </div>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    </body>
</html>
