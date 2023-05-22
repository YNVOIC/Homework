<?php
include "includes/header.php";
include "includes/db.php";
include "includes/config.php";
// Ellenőrzés, hogy a bejelentkezett felhasználó rendelkezik-e a szükséges jogosultságokkal
session_start();
if (!isset($_SESSION['username'])) {
    // A felhasználó nincs bejelentkezve, visszairányítjuk a bejelentkezési oldalra
    header("Location: index.php");
    exit;
}

// Ellenőrzés, hogy a poszt azonosítója érvényes-e és át lett-e adva
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $post_id = $_GET['id'];

    // Ellenőrzés, hogy a poszt létezik-e és a bejelentkezett felhasználó hozta-e létre
    $query = "SELECT * FROM posts WHERE post_id = $post_id AND post_author = '{$_SESSION['username']}'";
    $select_post_query = mysqli_query($connection, $query);

    if ($select_post_query && mysqli_num_rows($select_post_query) > 0) {
        // A poszt megtalálható és a bejelentkezett felhasználó hozta létre, töröljük a hozzászólásokat és a posztot
        $delete_comments_query = "DELETE FROM comments WHERE post_id = $post_id";
        $delete_comments = mysqli_query($connection, $delete_comments_query);

        $delete_query = "DELETE FROM posts WHERE post_id = $post_id";
        $delete_post = mysqli_query($connection, $delete_query);

        // Törölni kell a poszt képét is, ha van
        $select_image_query = "SELECT post_image FROM posts WHERE post_id = $post_id";
        $select_image = mysqli_query($connection, $select_image_query);
        $row = mysqli_fetch_assoc($select_image);
        $post_image = $row['post_image'];
        if (!empty($post_image)) {
            $image_path = "images/" . $post_image;
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        if ($delete_comments && $delete_post) {
            // A poszt és a hozzátartozó információk sikeresen törölve, átirányítás a főoldalra vagy egy másik releváns oldalra
            header("Location: index.php");
            exit;
        } else {
            // Hiba történt a poszt törlése közben
            echo "Hiba történt a poszt törlése közben.";
        }
    } else {
        echo "A poszt nem találhatóvagy nincs jogosultsága a törléshez.";
}
 }
?>