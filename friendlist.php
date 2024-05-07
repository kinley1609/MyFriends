<?php
// Including database connection
include_once("./functions/connect_database.php");

// Error message variable
$err_msg = "";

session_start();

// The function to count mutual friends with the friend list of current signed in user and friend_id of the target user
if (
    isset($_SESSION["friend_id"]) && isset($_SESSION["friend_email"]) && isset($_SESSION["profile_name"])
) {
    // Unfriend functionality
    if (isset($_GET["unfriend_id"])) {
        // Check if friend exists
        if ((int)(mysqli_fetch_assoc($conn->query("
                SELECT 
                    COUNT(*) 
                AS 
                    count_check 
                FROM 
                    myfriends 
                WHERE (friend_id1 = " . $_SESSION["friend_id"] . " 
                    AND 
                        friend_id2 = " . $_GET["unfriend_id"] . ") 
                OR (friend_id2 = " . $_SESSION["friend_id"] . " 
                    AND 
                        friend_id1 = " . $_GET["unfriend_id"] . ");
            "))["count_check"]) >= 1) {
            try {
                // Begin transaction
                $conn->query("START TRANSACTION;");

                // Delete friend connection from myfriends table
                $conn->query("
                        DELETE FROM myfriends 
                        WHERE (friend_id1 = " . $_SESSION["friend_id"] . " AND friend_id2 = " . $_GET["unfriend_id"] . ") 
                        OR (friend_id2 = " . $_SESSION["friend_id"] . " AND friend_id1 = " . $_GET["unfriend_id"] . ");
                    ");

                // Decrease the number of friends for both users
                $conn->query("
                        UPDATE friends 
                        SET num_of_friends = num_of_friends - 1 
                        WHERE friend_id = " . $_SESSION["friend_id"] . " 
                        AND num_of_friends > 0;
                    ");
                $conn->query("
                        UPDATE friends 
                        SET num_of_friends = num_of_friends - 1 
                        WHERE friend_id = " . $_GET["unfriend_id"] . " 
                        AND num_of_friends > 0;
                    ");

                // Commit transaction
                $conn->query("COMMIT;");
            } catch (\Throwable $e) {
                // Rollback transaction if error occurs
                $conn->rollback();
                $err_msg .= $e->getMessage();
            }
        }
    }

    // Count number of friends
    $res_count = (int)mysqli_fetch_assoc($conn->query("
            SELECT num_of_friends FROM friends 
            WHERE friend_id = " . $_SESSION["friend_id"] . ";
        "))["num_of_friends"];

    // Variable for pagination
    $page_num = 0;
    $max_page = ceil($res_count * 1.0 / 5) - 1;
    if (isset($_GET["page_num"]) && is_numeric($_GET["page_num"])) {
        $page_num = (int)$_GET["page_num"];
    }
    if ($page_num > $max_page) $page_num = $max_page;
    if ($page_num < 0) $page_num = 0;

    // Select friends and allow pagination
    $result = $conn->query("
            SELECT 
                f.friend_id, f.friend_email, f.profile_name, mf.friend_id1, mf.friend_id2 
            FROM 
                myfriends AS mf 
            JOIN 
                friends AS f 
            ON (mf.friend_id2 = f.friend_id 
                AND mf.friend_id1 = " . $_SESSION["friend_id"] . ") 
            OR (mf.friend_id1 = f.friend_id 
                AND mf.friend_id2 = " . $_SESSION["friend_id"] . ") 
            WHERE 
                mf.friend_id1 = " . $_SESSION["friend_id"] . " 
            OR 
                mf.friend_id2 = " . $_SESSION["friend_id"] . "
            ORDER BY f.profile_name
            LIMIT " . ($page_num * 5) . ",5;
        ");
} else {
    // Redirect to login page if session is not set
    header("location: login.php");
}
// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<?php include_once("./functions/head.php"); ?>

<body>
    <div class="container">
        <div class="content">
            <?php $page = 1;
            include_once("./functions/header.php"); ?>
            <div class="display list">
                <h2><?php echo $_SESSION["profile_name"]; ?>’s Friend List Page</h2>
                <div class='resultSummary'>
                    <div class="pagination">
                        <?php if ($page_num > 0) : // Render previous button
                        ?>
                            <a title="previous" class="page-btn" href="friendlist.php?page_num=<?php echo $page_num - 1; ?>">
                                <img src="assets\arrow.svg" alt="" class="prevBtn">
                            </a>
                        <?php endif; ?>
                    </div>
                    <p>
                        <strong>
                            <span class='resultCount'>Total number of friends is <?php echo $res_count; ?></span>
                        </strong>
                    </p>
                    <div class="pagination">
                        <?php if ($page_num < $max_page) : // Render next button
                        ?>
                            <a title="next" class="page-btn" href="friendlist.php?page_num=<?php echo $page_num + 1; ?>">
                                <img src="assets\arrow.svg" alt="" class="nextBtn">
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="displayRes">
                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                        <div class='friendDetails'>
                            <h3><?php echo $row["profile_name"]; ?></h3>
                            <h3><?php echo $row["email"]; ?></h3>
                            <a href="friendlist.php?unfriend_id=<?php echo $row["friend_id"]; ?>">
                                <button>Unfriend</button>
                            </a>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
    <?php include_once("./functions/footer.php"); ?>
</body>

</html>