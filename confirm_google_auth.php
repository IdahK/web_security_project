<?php
session_start();
require __DIR__ . '/config/db_connection.php';
$db = DB();

// Application library  with DemoLib class  that does registration and login logic
require __DIR__ . '/library/library.php';
$app = new DemoLib($db);
$user = $app->UserDetails($_SESSION['user_id']);

require_once __DIR__ . '/GoogleAuthenticator/GoogleAuthenticator.php';
$pga = new PHPGangsta_GoogleAuthenticator();
$qr_code =  $pga->getQRCodeGoogleUrl($user->email, $user->google_secret_code, 'itechempires.com');

$error_message = '';

if (isset($_POST['btnValidate'])) {

    $code = $_POST['code'];

    if ($code == "") {
        $error_message = 'Please Scan above QR code to configure your application and enter genereated authentication code to validated!';
    }
    else
    {
        if($pga->verifyCode($user->google_secret_code, $code, 2))
        {
            // success
            header("Location: profile.php");
        }
        else
        {
            // fail
            $error_message = 'Invalid Authentication Code!';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Confirm User Device</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>

<div class="container">
        <div class="row">
        <div class="col-md-5 col-md-offset-3 well">
            <h4>Application Authentication</h4>

            <p>
                Please download and install Google authenticate app on your phone, and scan following QR code to configure your device.
            </p>

            <div class="form-group">
                <img src="<?php echo $qr_code; ?>">
            </div>

            <form method="post" action="confirm_google_auth.php">
                <?php
                if ($error_message != "") {
                    echo '<div class="alert alert-danger"><strong>Error: </strong> ' . $error_message . '</div>';
                }
                ?>
                <div class="form-group">
                    <label >Enter Authentication Code:</label>
                    <input type="text" name="code" placeholder="6 Digit Code" class="form-control">
                </div>
                <div class="form-group">
                    <button type="submit" name="btnValidate" class="btn btn-primary">Validate</button>
                </div>
            </form>

            <div class="form-group">
                Click here to <a href="index.php">Login</a> if you have already registered your account.
            </div>
        </div>
    </div>
</div>

</body>
</html>
