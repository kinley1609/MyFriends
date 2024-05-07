<?php
// The function to count mutual friends with the friend list of current signed in user and friend_id of the target user
function count_mutual(mysqli $conn, $friend_id, $source)
{
    $list1 = array();

    // Get friend list of the target user and store their id in array $list1 and sort the array
    $list = $conn->query("SELECT friend_id1 , friend_id2 FROM myfriends WHERE friend_id1 = $friend_id OR friend_id2 = $friend_id;");
    while ($row = mysqli_fetch_assoc($list)) {
        if ($row["friend_id1"] == $friend_id) {
            $list1[] = $row["friend_id2"];
        } else {
            $list1[] = $row["friend_id1"];
        }
    }
    return count(array_intersect($list1, $source));
}

// Including database connection
include_once("./functions/connect_database.php");

// Error message variable
$err_msg = "";

session_start();
// Check if login session is set, if not, redirect to login page
if (
    isset($_SESSION["friend_id"]) && isset($_SESSION["friend_email"]) && isset($_SESSION["profile_name"])
) {
    // Add friend functionality
    if (isset($_GET["addfriend_id"])) {
        try {
            // Begin transaction
            $conn->query("START TRANSACTION;"); // make sure that either all database operations within the transaction are completed successfully or none of them are

            // Insert friend into myfriends table
            $conn->query("INSERT INTO myfriends (friend_id1, friend_id2) VALUES (" . $_SESSION["friend_id"] . ", " . $_GET["addfriend_id"] . ");");

            // Update number of friends for the signed in user and the added friend
            $conn->query("UPDATE friends SET num_of_friends = num_of_friends + 1 WHERE friend_id = " . $_SESSION["friend_id"] . ";");
            $conn->query("UPDATE friends SET num_of_friends = num_of_friends + 1 WHERE friend_id = " . $_GET["addfriend_id"] . ";");

            // Commit transaction
            $conn->query("COMMIT;"); // commits the transaction, making all the changes (insertion and updates) permanent in the database
        } catch (\Throwable $e) {
            // Rollback transaction if error occurs
            $conn->rollback();
            $err_msg .= $e->getMessage();
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

    // Select the not friend users and limit 5 for pagination
    $result = $conn->query("
            SELECT 
                friend_id, friend_email, profile_name 
            FROM 
                friends 
            WHERE 
                friend_id NOT IN (
                    SELECT 
                        friend_id2 
                    FROM 
                        myfriends 
                    WHERE 
                        friend_id1 = " . $_SESSION["friend_id"] . "
                ) 
            AND 
                friend_id NOT IN (
                    SELECT 
                        friend_id1 
                    FROM 
                        myfriends 
                    WHERE 
                        friend_id2 = " . $_SESSION["friend_id"] . "
                ) 
            AND 
                friend_id != " . $_SESSION["friend_id"] . "
            ORDER BY 
                profile_name
            LIMIT " . ($page_num * 5) . ",5;
        ");

    // Select friends of login user, store in an array, and sort to reduce calculation and use for mutual friends count
    $myfriends = array();
    $getmyfriends = $conn->query("SELECT friend_id2 AS friend_id 
                                    FROM myfriends 
                                    WHERE friend_id1 = " . $_SESSION["friend_id"] . " ORDER BY friend_id ASC;");
    while ($row = mysqli_fetch_assoc($getmyfriends)) {
        $myfriends[] = $row["friend_id"];
    }
    $getmyfriends = $conn->query("SELECT friend_id1 AS friend_id 
                                    FROM myfriends 
                                    WHERE friend_id2 = " . $_SESSION["friend_id"] . " ORDER BY friend_id ASC;");
    while ($row = mysqli_fetch_assoc($getmyfriends)) {
        $myfriends[] = $row["friend_id"];
    }
    sort($myfriends);
    } else {
        header("location: login.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<?php include_once("./functions/head.php"); ?>

<body>
    <div class="container">
        <div class="content">
            <?php $page = 2;
            include_once("./functions/header.php"); ?>
            <div class="display list">
                <h2><?php echo $_SESSION["profile_name"]; ?>’s Add Friend Page</h2>
                <div class='resultSummary'>
                    <div class="pagination">
                        <?php if ($page_num > 0) : // Render previous button
                        ?>
                            <a title="previous" class="page-btn" href="friendadd.php?page_num=<?php echo $page_num - 1; ?>">
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
                            <a title="next" class="page-btn" href="friendadd.php?page_num=<?php echo $page_num + 1; ?>">
                                <img src="assets\arrow.svg" alt="" class="nextBtn">
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="displayRes">
                    <?php while ($row = mysqli_fetch_assoc($result)) : // Call function mutual count for each user
                    ?>
                        <div class='friendDetails'>
                            <div class="brief">
                                <h3><?php echo $row["profile_name"]; ?></h3>
                                <p><?php echo $count_friends = count_mutual($conn, $row["friend_id"], $myfriends); ?> mutual friend<?php echo ($count_friends > 1 ? 's' : ''); ?></p>
                            </div>
                            <a href="friendadd.php?addfriend_id=<?php echo $row["friend_id"]; ?>">
                                <button>Add friend</button>
                            </a>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    include_once("./functions/footer.php");
    $conn->close();
    ?>
</body>

</html>