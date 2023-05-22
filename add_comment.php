<?php

session_start();
include "includes/db.php";
include "includes/header.php";
include "includes/config.php";
// Ellenőrzés, hogy a felhasználó be van-e jelentkezve
if (isset($_SESSION['username'])) {
    // Ellenőrzés, hogy a komment tartalma meg lett-e adva
    if (isset($_POST['comment_content'])) {
        $comment_content = $_POST['comment_content'];

        // Ellenőrzés, hogy az "id" érték létezik-e és nem üres
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $post_id = $_GET['id'];

            // Ellenőrzés, hogy a post_id érvényes-e
            $query = "SELECT * FROM posts WHERE post_id = $post_id";
            $result = mysqli_query($connection, $query);

            if (!$result || mysqli_num_rows($result) == 0) {
                die("Invalid post ID.");
            }

            // Felhasználó adatainak lekérdezése az adatbázisból
            $username = $_SESSION['username'];
            $query = "SELECT * FROM users WHERE username = '$username'";
            $result = mysqli_query($connection, $query);

            if (!$result || mysqli_num_rows($result) == 0) {
                die("User not found.");
            }

            $user = mysqli_fetch_assoc($result);

            if (isset($user['user_id'])) {
                $user_id = $user['user_id'];

                // Komment hozzáadása az adatbázishoz
                $query = "INSERT INTO comments (post_id, user_id, comment_content) VALUES ($post_id, $user_id, '$comment_content')";
                $result = mysqli_query($connection, $query);
                if ($result) {
                    // Sikeres hozzászólás hozzáadása
                    // Visszairányítás a poszt oldalra
                    header("Location: post.php?id=$post_id");
                    exit();
                      } 
                if ($result) {
                    // Sikeres komment hozzáadás
                    echo '<div class="alert alert-success text-center">Comment added successfully!</div>';
                } else {
                    // Sikertelen komment hozzáadás
                    echo '<div class="alert alert-danger text-center">Failed to add comment.</div>';
                }
            } else {
                // Hiba, ha a user_id nem található a $user tömbben
                echo '<div class="alert alert-danger text-center">User ID not found.</div>';
            }
        } else {
            // Hiba, ha az "id" érték hiányzik vagy üres
            echo '<div class="alert alert-danger text-center">Invalid post ID.</div>';
        }
    } else {
        // Hiba, ha a komment tartalma nincs megadva
        echo '<div class="alert alert-danger text-center">Comment content is required.</div>';
    }
} else {
    // Hiba, ha a felhasználó nincs bejelentkezve
    echo '<div class="alert alert-danger text-center">You must be logged in to add a comment.</div>';
}
?>
