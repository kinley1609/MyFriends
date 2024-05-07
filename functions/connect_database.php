<?php
    require_once ("settings.php");
    
    // Create connection
    $conn = new mysqli($host, $user, $pwd);
    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

    $create_db = "";
    $create_tables = "";

    $create_db_sql = "CREATE DATABASE IF NOT EXISTS $db;"; // SQL for creating database
    
    //  SQL for creating friends table
    $create_friends_sql = "
        CREATE TABLE IF NOT EXISTS friends (
            friend_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            friend_email VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(20) NOT NULL,
            profile_name VARCHAR(30) NOT NULL,
            date_started DATE NOT NULL,
            num_of_friends INT UNSIGNED DEFAULT 0,
            INDEX index_profile_name (profile_name)
        );
    ";

    // SQL for creating myfriends table
    $create_myfriends_sql = "
        CREATE TABLE IF NOT EXISTS myfriends (
            friend_id1 INT NOT NULL,
            friend_id2 INT NOT NULL,
            PRIMARY KEY (friend_id1, friend_id2),
            FOREIGN KEY (friend_id1) REFERENCES friends(friend_id),
            FOREIGN KEY (friend_id2) REFERENCES friends(friend_id),
            CONSTRAINT `check_not_same` CHECK (friend_id1 != friend_id2)
        );
    ";
    
    // Execute create database SQL
    if ($conn->query($create_db_sql) === TRUE) {
        $create_db = "Database created successfully";
    } else {
        $create_db = "Error creating database: " . $conn->error;
    }
    
    mysqli_select_db($conn, $db); // Select the database
    
    // Execute create table SQL
    if ($conn->query($create_friends_sql) === TRUE && $conn->query($create_myfriends_sql) === TRUE) {
        $create_tables = "Tables created successfully";
    } else {
        $create_tables = "Error creating tables: " . $conn->error;
    }
?>