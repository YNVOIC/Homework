<?php
session_start();
include "includes/header.php";
include "includes/db.php";
include "includes/config.php";

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // User is not logged in, redirect to the login page or handle it differently
    header("Location: index.php"); // Replace "login.php" with the actual path to the login page
    exit(); // Immediately exit the script after the redirect
}

// User is logged in, continue displaying the page

// Check if the user is logged in
if (isset($_SESSION['username'])) {
    // Retrieve user data from the database
    $username = $_SESSION['username'];
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($connection, $query);

    if (!$result) {
        die("Database query failed.");
    }

    $row_count = mysqli_num_rows($result);

    if ($row_count == 1) {
        // Successful query
        $row = mysqli_fetch_assoc($result);
        $first_name = $row['first_name'];
        $last_name = $row['last_name'];
    }
}

// Handling form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $post_content = $_POST['post_content'];
    $date = date('Y-m-d'); // Get the current date
    $author = $username; // Set the author to the logged-in user

    // Uploading an image
    $image_directory = 'images/'; // Path to the images folder

    if (!empty($_FILES['post_image'])) {
        $image_file = $image_directory . basename($_FILES['post_image']['name']);
        $image_extension = strtolower(pathinfo($image_file, PATHINFO_EXTENSION));

        // Checking allowed image extensions
        $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array($image_extension, $allowed_extensions)) {
            if (move_uploaded_file($_FILES['post_image']['tmp_name'], $image_file)) {
                // Successful image upload
                $image_path = $image_file;
            } else {
                // Error during image upload
                echo "Error uploading image.";
            }
        } else {
            // Disallowed image extension
            echo '<div class="alert alert-danger text-center">Only JPG, JPEG, PNG, and GIF images are allowed.</div>';
        }
    } else {
        // No image selected
        echo "Please select an image.";
    }
}

    // Saving data to the database if there is no error in image upload
if (isset($image_path)) {
    $query = "INSERT INTO posts (post_title, post_content, post_date, post_author, post_image) VALUES ('$title', '$post_content', '$date', '$author', '$image_path')";
    $result = mysqli_query($connection, $query);

    if ($result) {
        // The post is successfully saved in the database
        header("Location: index.php"); // Redirect to the index.php page
        exit();
    } else {
        // Error occurred while saving the data
        echo "Error saving data.";
    }
}
?>

<body>

<!-- Navigation -->
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <a class="navbar-brand" href="index.php">Homework Blog</a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li>
                    <a href="contact.php">Contact</a>
                </li>
                <li>
                    <a href="about_us.php">About Us</a>
                </li>
            </ul>
            <?php if (isset($_SESSION['username'])) : ?>
                <!-- Display user account information for logged in users -->
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-user"></i>
                            <?php echo $last_name . ' ' . $first_name . ' (' . $username . ')' ?>
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="logout.php"><i class="fa fa-fw fa-power-off"></i> Log Out</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            <?php else : ?>
                <!-- Code for non-logged in users -->
            <?php endif; ?>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container -->
</nav>

<div class="container">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="well">
                <h3 class="text-center">Create a New Post</h3>
                <form action="" method="post" enctype="multipart/form-data">
                    <!-- Post Title -->
                    <div class="form-group">
                        <label for="title">Post Title</label>
                        <input type="text" class="form-control" name="title" placeholder="Enter post title" required>
                    </div>
                    <!-- File Input for Post Image -->
                    <div class="form-group">
                        <label for="post_image">Select File</label>
                        <input type="file" name="post_image" required>
                    </div>
                    <!-- Post Content -->
                    <div class="form-group">
                        <label for="post_content">Post Content</label>
                        <textarea class="form-control" name="post_content" rows="6" placeholder="Enter post content" required></textarea>
                    </div>
                    <!-- Submit Button -->
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Publish Post</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

