<?php
session_start();
include "includes/header.php";
include "includes/db.php";
include "includes/config.php";

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
// Login process
if (!isset($_SESSION['username'])) {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $username = $_POST["username"];
        $password = $_POST["password"];

        // Check if all fields are filled
        if (empty($username) || empty($password)) {
            echo '<div class="alert alert-danger text-center">Please fill in all fields.</div>';
        } else {
            // Check username and password in the database
            $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
            $result = mysqli_query($connection, $query);

            if (!$result) {
                die("Database query failed.");
            }

            $row_count = mysqli_num_rows($result);

            if ($row_count == 1) {
                // Successful login
                echo '<div class="alert alert-success text-center">Login successful!</div>';

                // Retrieve user data
                $row = mysqli_fetch_assoc($result);
                $first_name = $row['first_name'];
                $last_name = $row['last_name'];
                $username = $row['username'];

                // Set session variable for logged-in user
                $_SESSION['username'] = $username;
            } else {
                // Failed login
                echo '<div class="alert alert-danger text-center">Invalid username or password.</div>';
            }
        }
    }
}
?>
/
<body>

<!-- Navigation -->
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse"
                    data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">Homework Blog</a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <?php if (!isset($_SESSION['username'])) : ?>
                        <li>
                            <a href="register.php">Register</a>
                        </li>
                    <?php endif; ?>
                    <li>
                        <a href="contact.php">Contact</a>
                        <?php if (isset($_SESSION['username'])) : ?>
                            <li>
                                <a href="new_post.php">New Post</a>
                            </li>
                        <?php endif; ?>
                    </li>
                    <li>
                        <a href="about_us.php">About Us</a>
                    </li>
                </ul>
                <?php if (isset($_SESSION['username'])) : ?>
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
            <div class="col-md-8 col-md-offset-2">
                <?php
                // Check if the post ID is provided in the URL
                if (isset($_GET['id'])) {
                    $post_id = $_GET['id'];
                }
                // Retrieve the post from the database based on the provided ID
                $query = "SELECT * FROM posts WHERE post_id = $post_id";
                $select_post_query = mysqli_query($connection, $query);

                // Check if the query was successful and if the post exists
                if ($select_post_query && mysqli_num_rows($select_post_query) > 0) {
                    $row = mysqli_fetch_assoc($select_post_query);
                    $post_title = $row['post_title'];
                    $post_author = $row['post_author'];
                    $post_date = $row['post_date'];
                    $post_content = $row['post_content'];
                    $post_image = $row['post_image'];
                }
                ?>

                <div class="row">
                    <div class="col-md-8">
                        <!-- Post Content -->
                        <h2><?php echo $post_title; ?></h2>
                        <p class="lead">by <?php echo $post_author; ?></p>
                        <p><span class="glyphicon glyphicon-time"></span> <?php echo $post_date . ' '; ?></p>
                        <hr>
                        <?php if (!empty($post_image)) : ?>
                            <img class="img-responsive" src="<?php echo $post_image; ?>" alt="" width="300" height="150">
                        <?php endif; ?>
                        <hr>
                        <p><?php echo $post_content; ?></p>
                        <hr>
                         <!-- Delete Button -->
                         <?php if (isset($_SESSION['username']) && $_SESSION['username'] === $post_author) : ?>
                            <a href="post_delete.php?id=<?php echo $post_id; ?>" class="btn btn-danger" style="position: absolute; top: 20px; right: 20px;" onclick="return confirm('Are you sure you want to delete this post?');">X</a>
                        <?php endif; ?>
                        <!-- Comment Section -->
                        <?php if (isset($_SESSION['username'])) : ?>
    <div class="well">
        <h4>Leave a Comment:</h4>
        <form role="form" method="post" action="add_comment.php?id=<?php echo $post_id; ?>">
            <div class="form-group">
                <textarea class="form-control" rows="3" name="comment_content" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
<?php endif; ?>
                        
                  <!-- Display Comments -->
<?php
// Check if post ID is valid and passed
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $post_id = $_GET['id'];

    // Retrieve comments for the post from the database
    $query = "SELECT * FROM comments WHERE post_id = $post_id ORDER BY comment_id DESC";
    $select_comments_query = mysqli_query($connection, $query);

    if ($select_comments_query && mysqli_num_rows($select_comments_query) > 0) {
        while ($row = mysqli_fetch_assoc($select_comments_query)) {
            $comment_id = $row['comment_id'];
            $comment_content = $row['comment_content'];
            $comment_author = $row['comment_author'];
            $comment_date = $row['comment_date'];

            // Check if logged-in user is the author of the comment
            $is_author = isset($_SESSION['username']) && $_SESSION['username'] === $comment_author;
            ?>
            <div class="media mb-4">
                <div class="media-body">
                    <div class="comment-header">
                        <h5 class="comment-author"><?php echo $comment_author; ?></h5>
                        <small class="comment-date text-muted"><?php echo $comment_date; ?></small>
                    </div>
                    <p class="comment-content"><?php echo $comment_content; ?></p>
                    <?php if ($is_author) : ?>
                        <!-- Additional actions for the comment author -->
                    <?php endif; ?>
                </div>
            </div>
            <?php
        }
    } else {
        echo "<p>No comments yet.</p>";
    }
}
?>

</div>

<div class="col-md-4">
    <?php if (!isset($_SESSION['username'])) : ?>
        <div class="well">
            <form action="index.php" method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
                <div class="text-center">
                    <p class="small">Don't have an account? <a href="register.php">Register</a></p>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>
</div>
</div>
</div>
</div>
</body>

</html>