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
    // Checking if email, profile name, password, and re-entered password are submitted
    if (
        isset($_POST["email"]) && isset($_POST["profile_name"]) && isset($_POST["password"]) && isset($_POST["re_password"])
    ) {
        $_POST["email"] = trim($_POST["email"]);
        $_POST["profile_name"] = trim($_POST["profile_name"]);
        $_POST["password"] = trim($_POST["password"]);
        $_POST["re_password"] = trim($_POST["re_password"]);
        if (!empty($_POST["email"]) && !empty($_POST["profile_name"]) && !empty($_POST["password"]) && !empty($_POST["re_password"])) {

            // Validate email
            if (!preg_match('/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/', $_POST["email"])) {
                $err_msg = $err_msg . "
                        Email is invalid. <br/>
                    ";
            } else {
                // Check email uniqueness
                if (mysqli_num_rows($conn->query("SELECT friend_email FROM friends WHERE friend_email = '" . $_POST["email"] . "';"))) {
                    $err_msg = $err_msg . "
                            Email already registered. <br/>
                        ";
                }
            }

            // Validate profile name 
            if (!preg_match('/^[a-zA-Z]+$/', $_POST["profile_name"])) {
                $err_msg = $err_msg . "
                        Profile name must contains only letters. <br/>
                    ";
            }

            // Validate password
            if (!preg_match('/^[a-zA-Z0-9]+$/', $_POST["password"])) {
                $err_msg = $err_msg . "
                        Password must contains only letters and numbers. <br/>
                    ";
            }

            // Check password match
            if ($_POST["password"] != $_POST["re_password"]) {
                $err_msg = $err_msg . "
                        Password does not match. <br/>
                    ";
            }

            // If no error, insert to database
            if ($err_msg == "") {
                if ($conn->query("INSERT INTO friends (friend_email, profile_name, password, date_started) VALUES ('" . $_POST["email"] . "','" . $_POST["profile_name"] . "','" . $_POST["password"] . "', CURRENT_TIMESTAMP());")) {

                    // Select again to make user created
                    $get_user = $conn->query("SELECT friend_id, friend_email, profile_name FROM friends WHERE friend_email = '" . $_POST["email"] . "' AND password = '" . $_POST["password"] . "';");
                    if ($get_user) {
                        $user = mysqli_fetch_assoc($get_user);

                        // Set user information to session to recognize signed in
                        $_SESSION["friend_id"] = $user["friend_id"];
                        $_SESSION["friend_email"] = $user["friend_email"];
                        $_SESSION["profile_name"] = $user["profile_name"];
                        header("location: friendadd.php");
                    } else {
                        $err_msg = $err_msg . "
                                Register failed, try again later. <br/>
                                Error: " . htmlspecialchars(addslashes($conn->error)) . " <br/>
                            ";
                    }
                } else {
                    $err_msg = $err_msg . "
                            Register failed, try again later. <br/>
                            Error: " . htmlspecialchars(addslashes($conn->error)) . " <br/>
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
        if (!(!isset($_POST["email"]) && !isset($_POST["profile_name"]) && !isset($_POST["password"]) && !isset($_POST["re_password"]))) {
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
            <?php $page = 5;
            include_once("./functions/header.php"); ?>
            <div class="display">
                <h2>
                    Registration Page
                </h2>
                <form id="postForm" action="signup.php" method="post">
                    <div class="group">
                        <label for="email"><strong>Email: </strong></label>
                        <input type="text" name="email" id="email" value="<?php if (isset($_POST["email"])) echo $_POST["email"]; ?>" />
                    </div>
                    <div class="group">
                        <label for="profile_name"><strong>Profile Name: </strong></label>
                        <input type="text" name="profile_name" id="profile_name" value="<?php if (isset($_POST["profile_name"])) echo $_POST["profile_name"]; ?>" />
                    </div>
                    <div class="group">
                        <label for="password"><strong>Password: </strong></label>
                        <input type="password" name="password" id="password" />
                    </div>
                    <div class="group">
                        <label for="re_password"><strong>Confirm Password: </strong></label>
                        <input type="password" name="re_password" id="re_password" />
                    </div>
                    <p class="info error">
                        <?php echo $err_msg; ?>
                    </p>
                    <div class="userInput">
                        <button type="submit">Register</button>
                        <button type="reset">Clear</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include_once("./functions/footer.php"); ?>
</body>

</html>