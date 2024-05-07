<!DOCTYPE html>
<html lang="en">
<?php
// Including head section with page number
$page = 3;
include_once("./functions/head.php");
?>

<body>
    <div class="container">
        <div class="content">
            <?php
            // Starting session and including header
            session_start();
            include_once("./functions/header.php");
            ?>
            <div class="display">
                <h1>About</h1>
                <ul>
                    <li>PHP version: <?php echo phpversion(); ?></li>
                    <li>Tasks not completed: None</li>
                    <li>Special features:
                        <ul>
                            <li>Improved design</li>
                        </ul>
                    </li>
                    <li>
                        Troubles:
                        <ul>
                            <li>
                                Having to get used to using SQL command was a bit difficult, <br /> but I'm glad that everything worked out.
                            </li>
                        </ul>
                    </li>
                    <li>
                        Future improvements:
                        <ul>
                            <li>Optimization of code.</li>
                            <li>Add more security measures.</li>
                        </ul>
                    </li>
                    <li>
                        Link to other pages:
                        <ul>
                            <li><a href="friendlist.php">Friend List</a></li>
                            <li><a href="friendadd.php">Add Friend</a></li>
                            <li><a href="index.php">Home Page</a></li>
                        </ul>
                    </li>
                    <li>
                        Discussion point: Even though I did not partake in any discussion, <br /> there were a few questions that had peaked my interest, <br /> but the one that caught my eyes was
                        <ul>
                            <li><img class="discussionImg" src="assets/discussion.png" alt="" /></li>
                        </ul>
                    </li>
                    <li>Credits: Pagination's svg and logo are inspired and made from internet sources.</li>
                </ul>
            </div>
        </div>
    </div>
    <?php
    // Including footer
    include_once("./functions/footer.php");
    ?>
</body>

</html>