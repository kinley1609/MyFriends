<?php
// Starting session and including database connection
session_start();
include_once("./functions/connect_database.php");
$err_msg = "";

// Check if already signed in, if yes, direct to friend list
if (
    isset($_SESSION["friend_id"]) && isset($_SESSION["friend_email"]) && isset($_SESSION["profile_name"])
) {
    header("location: friendlist.php");
} else {
    // Checking if email and password are submitted
    if (
        isset($_POST["email"]) && isset($_POST["password"])
    ) {
        $_POST["email"] = trim($_POST["email"]);
        $_POST["password"] = trim($_POST["password"]);
        if (!empty($_POST["email"]) && !empty($_POST["password"])) {

            // Validate email
            if (!preg_match('/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/', $_POST["email"])) {
                $err_msg = $err_msg . "
                        Email is invalid. <br/>
                    ";
            } else {
                if (mysqli_num_rows($conn->query("SELECT friend_email FROM friends WHERE friend_email = '" . $_POST["email"] . "';")) == 0) {
                    $err_msg = $err_msg . "
                            Email is not registered. <br/>
                        ";
                }
            }

            // Validate password
            if (!preg_match('/^[a-zA-Z0-9]+$/', $_POST["password"])) {
                $err_msg = $err_msg . "
                        Password must contains only letters and numbers. <br/>
                    ";
            }

            // If no error, try to select the user from database
            if ($err_msg == "") {
                $get_user = $conn->query("SELECT friend_id, friend_email, profile_name FROM friends WHERE friend_email = '" . $_POST["email"] . "' AND password = '" . $_POST["password"] . "';");
                if (mysqli_num_rows($get_user)) {
                    $user = mysqli_fetch_assoc($get_user);

                    // Set user information to session to recognize signed in
                    $_SESSION["friend_id"] = $user["friend_id"];
                    $_SESSION["friend_email"] = $user["friend_email"];
                    $_SESSION["profile_name"] = $user["profile_name"];
                    header("location: friendlist.php");
                } else {
                    $err_msg = $err_msg . "
                            Incorrect password. <br/>
                        ";
                }
            }
        } else {
            $err_msg = $err_msg . "
                    Fields must not be empty. <br/>
                ";
        }
    } else {
        // Avoid generating error message on first visit
        if (!(!isset($_POST["email"]) && !isset($_POST["password"]))) {
            $err_msg = $err_msg . "
                    Every field is required. <br/>
                ";
        }
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<?php include_once("./functions/head.php"); ?>

<body>
    <div class="container">
        <div class="content">
            <?php $page = 4;
            include_once("./functions/header.php"); ?>
            <div class="display">
                <h2>Log In Page</h2>
                <form id="postForm" action="login.php" method="post">
                    <div class="group">
                        <label for="email"><strong>Email: </strong></label>
                        <input type="text" name="email" id="email" value="<?php if (isset($_POST["email"])) echo $_POST["email"]; ?>" />
                    </div>
                    <div class="group">
                        <label for="password"><strong>Password: </strong></label>
                        <input type="password" name="password" id="password" />
                    </div>
                    <p class="info error">
                        <?php echo $err_msg; ?>
                    </p>
                    <div class="userInput">
                        <button type="submit">Log In</button>
                        <button type="reset">Clear</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include_once("./functions/footer.php"); ?>
</body>

</html>