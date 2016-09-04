<html>
    <head>
        <link rel="stylesheet" href="css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <!-- Optional theme -->
        <link rel="stylesheet" href="css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
        <!-- Latest compiled and minified JavaScript -->
        <script src="js/jquery-3.1.0.min.js"></script>
        <script src="js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
        <script src="js/tinymce/tinymce/js/tinymce/tinymce.min.js"></script>
        <script src="js/tinymce/tinymce/js/tinymce/jquery.tinymce.min.js"></script>
        <script>
            tinymce.init({
                selector:'textarea', 
                plugins: [
                    'link'            
                ],
            });
        </script>
    </head>
    <body>
        <div class="col-sm-12">
            <form role="form" action="index.php" method="POST">
                <div class="col-sm-6">
                    <div class="col-sm-4">
                        <label><input type="radio" name="mailService" value="gmail" checked>Gmail</label>
                    </div>
                    <div class="col-sm-4">
                        <label><input type="radio" name="mailService" value="yahoo">Yahoo</label>
                    </div>
                    <div class="col-sm-4">
                        <label><input type="radio" name="mailService" value="microsoft">Microsoft</label>
                    </div>
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input class="form-control" name="name" id="name">
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" name="email" id="email">
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" class="form-control" name="password" id="password">
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject:</label>
                        <input class="form-control" name="subject" id="subject">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="comment">Message:</label>
                        <textarea class="form-control" rows="5" id="comment" name="message"></textarea>
                    </div>
                    <button type="submit" class="btn btn-default">Send</button>
                </div>
            </form>
        </div>
    </body>
</html>
<?php
require 'phpmailer\PHPMailerAutoload.php';
$email = isset($_POST["email"])? $_POST["email"]:"";
$name = isset($_POST["name"])? $_POST["name"]:"";
$password = isset($_POST["password"])? $_POST["password"]:"";
$message = isset($_POST["message"])? $_POST["message"]:"";
$subject = isset($_POST["subject"])? $_POST["subject"]:"";
$mailService = isset($_POST["mailService"])? $_POST["mailService"]:"";
$mysql = mysqli_connect('localhost', 'root', '');
mysqli_select_db($mysql, 'mail_app');
$result = mysqli_query($mysql, 'SELECT * FROM client');

if($email!=""&&$password!=""&&$subject!=""&&$mailService!=""){
    switch($mailService){
        case "gmail":{
            foreach ($result as $row) {
                 send_mail($email,$password,$row['ClientEmail'],$message,$subject,$row['ClientName'],$name,"smtp.gmail.com");
            }      
        }break;
        case "yahoo":{
            foreach ($result as $row) { 
                send_mail($email,$password,$row['ClientEmail'],$message,$subject,$row['ClientName'],$name,"smtp.mail.yahoo.com");
        }
        }break;
        case "microsoft":{
            foreach ($result as $row) { 
                send_mail($email,$password,$row['ClientEmail'],$message,$subject,$row['ClientName'],$name,"smtp.live.com");
            }
        }break;           
    }
}
function send_mail($from,$password, $to, $message, $subject,$recipName,$sendName,$mailService){

    $mail = new PHPMailer(true);
    //Create a new PHPMailer instance
    try{
        //Tell PHPMailer to use SMTP
        $mail->isSMTP();
        //Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $mail->SMTPDebug = 0;
        //Ask for HTML-friendly debug output
        //$mail->Debugoutput = 'html';
        //Set the hostname of the mail server
        $mail->Host = $mailService;
        // use
        // $mail->Host = gethostbyname('smtp.gmail.com');
        // if your network does not support SMTP over IPv6
        //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $mail->Port = 587;
        //Set the encryption system to use - ssl (deprecated) or tls
        $mail->SMTPSecure = 'tls';
        //Whether to use SMTP authentication
        $mail->SMTPAuth = true;
        //Username to use for SMTP authentication - use full email address for gmail
        $mail->Username = $from;
        //Password to use for SMTP authentication
        $mail->Password = $password;
        //Set who the message is to be sent from
        $mail->setFrom($from, $sendName);
        //Set an alternative reply-to address
        // $mail->addReplyTo('replyto@example.com', 'First Last');
        //Set who the message is to be sent to
        $mail->addAddress($to, $recipName);
        //Set the subject line
        $mail->Subject = $subject;
        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        // $mail->msgHTML(file_get_contents('contents.html'), dirname(__FILE__));
        //Replace the plain text body with one created manually
        $mail->AltBody = $message;
        $mail->Body    = $message;
        $mail->IsHTML(true);
        //Attach an image file
        // $mail->addAttachment('images/phpmailer_mini.png');
        //send the message, check for errors
        $mail->send();
        echo '<p class=\"text-primary\"> Message has been sent to '.$to." </p>";
    }catch(phpmailerException $e){
        echo "<p class=\"text-danger\">Failed to send to " . $to . " " . $e->errorMessage(). "</p>";
    }
}
?>