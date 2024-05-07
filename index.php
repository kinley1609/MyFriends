<?php
// Including database connection
include_once("./functions/connect_database.php");

// SQL to populate friends table
$insert_friends = "
        INSERT INTO friends (friend_email, password, profile_name, date_started, num_of_friends) VALUES
        ('jsmith@example.com', 'password123', 'John Smith', '2023-02-15', 6),
        ('kdoe@example.com', 'doe123', 'Karen Doe', '2023-05-20', 5),
        ('mjohnson@example.com', 'johnsonpass', 'Mike Johnson', '2023-03-10', 7),
        ('aperez@example.com', 'perezpass', 'Ana Perez', '2023-01-05', 5),
        ('rlee@example.com', 'lee456', 'Rachel Lee', '2023-04-30', 2),
        ('bnguyen@example.com', 'nguyenpass', 'Brian Nguyen', '2023-06-12', 2),
        ('skumar@example.com', 'kumarpass', 'Sara Kumar', '2023-07-25', 2),
        ('wchoi@example.com', 'choipass', 'William Choi', '2023-08-18', 5),
        ('thernandez@example.com', 'hernandezpass', 'Tina Hernandez', '2023-09-22', 4),
        ('mjones@example.com', 'jonespass', 'Maria Jones', '2023-10-10', 2);
    ";

// SQL to populate myfriends table
$insert_myfriends = "
        INSERT INTO myfriends (friend_id1, friend_id2) VALUES 
        (10, 2),
        (3, 5),
        (2, 3),
        (4, 9),
        (1, 4),
        (8, 4),
        (8, 1),
        (6, 5),
        (3, 6),
        (4, 2),
        (1, 9),
        (3, 9),
        (2, 8),
        (1, 10),
        (4, 3),
        (2, 9),
        (3, 1),
        (1, 5),
        (7, 8),
        (7, 3);
    ";

// Execute populate friends SQL
$populate_data = "Populated data successfully";

$result = $conn->query("SELECT COUNT(*) AS count FROM friends;");
$result = mysqli_fetch_assoc($result);

if ($result["count"] == 0) {
    if (!$conn->query($insert_friends)) {
        $populate_data = "Populated data failed.";
    }
}

// Execute populate myfriends SQL
$result = $conn->query("SELECT COUNT(*) AS count FROM myfriends;");
$result = mysqli_fetch_assoc($result);

if ($result["count"] == 0) {
    if (!$conn->query($insert_myfriends)) {
        $populate_data = "Populated data failed.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<?php
$page = 0;
include_once("./functions/head.php");

// Check if the pop-up has been displayed
$popup_displayed = isset($_SESSION['popup_displayed']) && $_SESSION['popup_displayed'] === true;
?>

<body>
    <div class="container">
        <div class="content">
            <?php session_start();
            include_once("./functions/header.php"); ?>
            <div class="display">
                <div class="home">
                    <div class="branding">
                        <img src="assets/logo/png/logo-no-background.png" alt="">
                    </div>
                    <div>
                        ↓ made by
                    </div>
                    <div class="text">
                        <h1>Le Tran Bao Kien</h1>
                        <p>I declare that this assignment is my individual work. I have not worked collaboratively nor have I
                            copied from any other student’s work or from any other source.</p>
                        <h4>Student ID: 104223584</h4>
                        <h4>Contact: kienltbswh01234@fpt.edu.vn</h4>
                    </div>
                    <div class="text">
                        <p class="connection-status">
                            <?php echo htmlspecialchars($create_db); ?>
                            <br />
                            <?php echo htmlspecialchars($create_tables); ?>
                            <br />
                            <?php echo htmlspecialchars($populate_data); ?>
                        </p>
                    </div>
                    <h3><a href="about.php">More about this assignment.</a></h3>
                </div>
                <div class="userActions">
                    <a title="Sign in" class="login <?php echo ($page == 4 ? 'active' : ''); ?>" href="login.php">
                        <button>Log In</button>
                    </a>
                    <a title="Sign up" class="signup <?php echo ($page == 5 ? 'active' : ''); ?>" href="signup.php">
                        <button>Sign Up</button>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php include_once("./functions/footer.php"); ?>
</body>

</html>