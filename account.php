<?php

include 'session.php';

function connectDB()
{
    include 'storedInfo.php';
    $mysqli = new mysqli("oniddb.cws.oregonstate.edu","thomkevi-db", $myPassword, "thomkevi-db");
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    } else {
        /* echo "Connection worked!<br>"; */
    }
    return $mysqli;
}

if (isset($_POST['action']) && $_POST['action'] == 'add_video' ) {

    $uploaddir = '/nfs/stak/students/t/thomkevi/public_html/cs290-final_project/user_images/';
    $uploadfile = $uploaddir . basename($_FILES['userfile']['name']);

    if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {

        $mysqli = connectDB();
        /* Prepared statement, stage 1: prepare */
        if (!($stmt = $mysqli->prepare("INSERT INTO MOVIEDB_USER_MOVIES (MOVIE_TITLE, MOVIE_RELEASE_DATE, MOVIE_DESCRIPTION, MOVIE_POSTER, MOVIE_ADDED_BY_USERNAME) VALUES (?, ?, ?, ?, ?)"))) {
            echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }

        /* Prepared statement, stage 2: bind and execute */
        $movie_name = $_POST['movie_name'];
        $movie_date = $_POST['movie_date'];
        $movie_desc = $_POST['movie_desc'];
        $movie_img = basename($_FILES['userfile']['name']);
        $movie_user = $_SESSION['username'];

        if (!$stmt->bind_param("sssss", $movie_name, $movie_date, $movie_desc, $movie_img, $movie_user)) {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        }

        /* Execute Statement */
        if (!$stmt->execute()) {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }

        /* explicit close recommended */
        $stmt->close();

    }

}

function listMyMovies($modal)
{
    $mysqli = connectDB();

    $selectStmt = "SELECT MOVIE_ID, MOVIE_TITLE, MOVIE_RELEASE_DATE, MOVIE_DESCRIPTION, MOVIE_POSTER, FAVORITE FROM MOVIEDB_USER_MOVIES WHERE MOVIE_ADDED_BY_USERNAME = ?";

    if (!($stmt = $mysqli->prepare($selectStmt))) {
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error . $selectStmt;
    }

    $movie_user = $_SESSION['username'];

    if (!$stmt->bind_param("s", $movie_user)) {
        echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    }

    /* Execute Statement */
    if (!$stmt->execute()) {
        echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }

    $movie_id = NULL;
    $movie_name = NULL;
    $movie_date = NULL;
    $movie_desc = NULL;
    $movie_img = NULL;
    $movie_fav = NULL;
    if (!$stmt->bind_result($movie_id, $movie_name, $movie_date, $movie_desc, $movie_img, $movie_fav)) {
        echo "Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    }

    while ($stmt->fetch()) {
        if ($modal === false)
        {
            echo '        <div class="col-sm-4 portfolio-item">';
            echo '                <a href="#portfolioModal' . $movie_id . '" class="portfolio-link" data-toggle="modal">';
            echo '                    <div class="caption">';
            echo '                        <div class="caption-content">';
            echo '                            <i class="fa fa-search-plus fa-3x"></i>';
            echo '                        </div>';
            echo '                    </div>';
            echo '                    <img src="user_images/' . $movie_img . '" class="img-responsive" alt="">';
            echo '                </a>';
            echo '        </div>';
        }
        else
        {
            echo '<div class="portfolio-modal modal fade" id="portfolioModal' . $movie_id . '" tabindex="-1" role="dialog" aria-hidden="true">';
            echo '    <div class="modal-content">';
            echo '        <div class="close-modal" data-dismiss="modal">';
            echo '            <div class="lr">';
            echo '                <div class="rl">';
            echo '                </div>';
            echo '            </div>';
            echo '        </div>';
            echo '        <div class="container">';
            echo '            <div class="row">';
            echo '                <div class="col-lg-8 col-lg-offset-2">';
            echo '                    <div class="modal-body">';
            echo '                        <h2>' . $movie_name . '</h2>';
            echo '                        <hr class="star-primary">';
            echo '                        <img src="user_images/' . $movie_img . '" class="img-responsive img-centered" alt="">';
            echo '                        <p>' . $movie_desc . '</p>';
            echo '                        <ul class="list-inline item-details">';
            echo '                            <li>Release Date:';
            echo '                                <strong>' . $movie_date ;
            echo '                                </strong>';
            echo '                            </li>';
            echo '                            <li>Favorite: ';
            echo '<a href="#" onclick="updateFavorite(' . $movie_id . ')"><span id="span' . $movie_id . '" class="glyphicon glyphicon-star" ';
            if ($movie_fav === 1)
            {
                echo 'style="color: gold" ';
            }
            else {
                echo 'style="color: black" ';
            }
            echo '></span></a>';
            echo '                            </li>';
            echo '                        </ul>';
            echo '                        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>';
            echo '                    </div>';
            echo '                </div>';
            echo '            </div>';
            echo '        </div>';
            echo '    </div>';
            echo '</div>';


        }


    }

    /* explicit close recommended */
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">

<title>Your Movie Database</title>

<!-- Bootstrap Core CSS - Uses Bootswatch Flatly Theme: http://bootswatch.com/flatly/ -->
    <link href="startbootstrap-freelancer-1.0.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
<link href="startbootstrap-freelancer-1.0.2/css/freelancer.css" rel="stylesheet">

    <!-- Custom Fonts -->
<link href="startbootstrap-freelancer-1.0.2/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
<link href="http://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
<link href="http://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
<script>
    function validateVideo(form) {
        if (form.movie_name.value.length < 1 || form.movie_name.value.length > 100) {
            alert("Movie Name Must Be Between 1 and 100 Characters");
            return false;
        }
        if (form.movie_desc.value.length < 1 || form.movie_desc.value.length > 1000) {
            alert("Movie Description Must Be Between 1 and 1000 Characters");
            return false;
        }
    }
    function updateFavorite(movie_id) {
        var favSpan;
        var httpRequest;
        if (document.getElementById) {
            if (favSpan=document.getElementById("span" + movie_id)) {
                if (favSpan.style.color==="black") {
                    favSpan.style.color="gold";
                } else {
                    favSpan.style.color="black"
                }
            }
        }
/*
        var funcOnReadyStateChange = function () {
            if (httpRequest.readyState === 4) {
                if (httpRequest.status === 200) {
                    response = JSON.parse(httpRequest.responseText);
                    if (response["parameters"]["Account"] === 'Available')
                    {
                        funcReturn = true;
                    } else {
                        document.getElementById("create_error").innerHTML = "Username Not Available";
                        funcReturn = false;
                    }
                } else {
                    alert('There was a problem with the request.');
                }
            }
        };
*/
        if (window.XMLHttpRequest) {
            httpRequest = new XMLHttpRequest();
        } else if (window.ActiveXObject) {
            httpRequest = new window.ActiveXObject("Microsoft.XMLHTTP");
        }
        if (!httpRequest) {
            alert('Could not create httpRequest');
        }
        /* httpRequest.onreadystatechange = funcOnReadyStateChange; */
        httpRequest.open('POST', 'http://web.engr.oregonstate.edu/~thomkevi/cs290-final_project/ajaxUpdateFavorite.php', false);
        httpRequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        httpRequest.send("movieid=" + movie_id);
    }
</script>

</head>
<body id="page-top" class="index">

<!-- Navigation -->
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header page-scroll">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#page-top">Shareable Movies</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
                <li class="hidden">
                    <a href="#page-top"></a>
                </li>
                <li class="page-scroll">
                    <a href="main.php">Home</a>
                </li>
                <li class="page-scroll">
                    <a href="#mymovies">My Movies</a>
                </li>
                <li class="page-scroll">
                    <a href="#add">Add Movie</a>
                </li>
                <li class="page-scroll">
                    <?php returnLoginOrAccount(); ?>
                </li>
                <li class="page-scroll">
                    <?php showLogOut(); ?>
                </li>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container-fluid -->
</nav>

<!-- Header -->
<header>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <img class="img-responsive" src="img/film-161204_1280.png" alt="movie reel" height="200" width="200">
                <div class="intro-text">
                    <span class="name">Shareable Movies</span>
                    <hr class="star-light">
                    <span class="skills">Social - Movie - Database</span>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Portfolio Grid Section -->
<section id="mymovies">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h2>My Movies</h2>
                <hr class="star-primary">
            </div>
        </div>
        <div class="row">
            <?php
                listMyMovies(false);
            ?>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="add">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h2>Add Movie</h2>
                <hr class="star-primary">
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2">
                <!-- To configure the contact form email address, go to mail/contact_me.php and update the email address in the PHP file on line 19. -->
                <!-- The form should work on most web servers, but if the form is not working you may need to configure your web server differently. -->
                <form action="account.php" enctype="multipart/form-data"  method="POST" onsubmit="return validateAddMovie(this)">
                    <div class="row control-group">
                        <div class="form-group col-xs-12 controls">
                            <label>Name</label>
                            <input type="text" class="form-control" name="movie_name" placeholder="Movie Name" required data-validation-required-message="Please enter movie name.">
                            <p class="help-block text-danger"></p>
                        </div>
                    </div>
                    <div class="row control-group">
                        <div class="form-group col-xs-12 controls">
                            <label>Release Date</label>
                            <input type="date" class="form-control" name="movie_date" required data-validation-required-message="Please enter movie release date.">
                            <p class="help-block text-danger"></p>
                        </div>
                    </div>
                    <div class="row control-group">
                        <div class="form-group col-xs-12 controls">
                            <label>Description</label>
                            <textarea rows="5" class="form-control" name="movie_desc" placeholder="Movie Description" required data-validation-required-message="Please enter movie description."></textarea>
                            <p class="help-block text-danger"></p>
                        </div>
                    </div>
                    <div class="row control-group">
                        <div class="form-group col-xs-12  controls">
                            <label>Movie Poster Image</label>
                            <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
                            <input type="file" class="form-control" name="userfile" required data-validation-required-message="Please browse to image file.">
                            <p class="help-block text-danger"></p>
                        </div>
                    </div>
                    <div id="success"></div>
                    <div class="row">
                        <div class="form-group col-xs-12">
                            <input type="hidden" name="action" value="add_video">
                            <button type="submit" class="btn btn-success btn-lg">Add</button>
                        </div>
                    </div>
                </form>
                <div id="add_video_error_message"></div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="text-center">
    <div class="footer-above">
        <div class="container">
            <div class="row">
                <div class="footer-col col-md-4">
                    <h3>Location</h3>
                    <p>E-campus<br>Oregon State University</p>
                </div>
                <div class="footer-col col-md-4">
                    <h3>Around the Web</h3>
                    <ul class="list-inline">
                        <li>
                            <a href="#" class="btn-social btn-outline"><i class="fa fa-fw fa-facebook"></i></a>
                        </li>
                        <li>
                            <a href="#" class="btn-social btn-outline"><i class="fa fa-fw fa-google-plus"></i></a>
                        </li>
                        <li>
                            <a href="#" class="btn-social btn-outline"><i class="fa fa-fw fa-twitter"></i></a>
                        </li>
                        <li>
                            <a href="#" class="btn-social btn-outline"><i class="fa fa-fw fa-linkedin"></i></a>
                        </li>
                        <li>
                            <a href="#" class="btn-social btn-outline"><i class="fa fa-fw fa-dribbble"></i></a>
                        </li>
                    </ul>
                </div>
                <div class="footer-col col-md-4">
                    <h3>About Freelancer</h3>
                    <p>Freelance is a free to use, open source Bootstrap theme created by <a href="http://startbootstrap.com">Start Bootstrap</a>.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-below">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    Not Copyrighted &copy; 2015
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Scroll to Top Button (Only visible on small and extra-small screen sizes) -->
<div class="scroll-top page-scroll visible-xs visble-sm">
    <a class="btn btn-primary" href="#page-top">
        <i class="fa fa-chevron-up"></i>
    </a>
</div>

<!-- Portfolio Modals -->
<?php
listMyMovies(true);
?>

<!-- jQuery -->
<script src="startbootstrap-freelancer-1.0.2/js/jquery.js"></script>

<!-- Bootstrap Core JavaScript -->
<script src="startbootstrap-freelancer-1.0.2/js/bootstrap.min.js"></script>

<!-- Plugin JavaScript -->
<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
<script src="startbootstrap-freelancer-1.0.2/js/classie.js"></script>
<script src="startbootstrap-freelancer-1.0.2/js/cbpAnimatedHeader.js"></script>

<!-- Contact Form JavaScript -->
<script src="startbootstrap-freelancer-1.0.2/js/jqBootstrapValidation.js"></script>
<script src="startbootstrap-freelancer-1.0.2/js/contact_me.js"></script>

<!-- Custom Theme JavaScript -->
<script src="startbootstrap-freelancer-1.0.2/js/freelancer.js"></script>

</body>

</html>

