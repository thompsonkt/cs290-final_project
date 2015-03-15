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

function listMyMovies($modal)
{
    $mysqli = connectDB();

    $selectStmt = "SELECT MOVIE_ID, MOVIE_TITLE, MOVIE_RELEASE_DATE, MOVIE_DESCRIPTION, MOVIE_POSTER, MOVIE_ADDED_BY_USERNAME FROM MOVIEDB_USER_MOVIES WHERE FAVORITE = 1 LIMIT 8";

    if (!($stmt = $mysqli->prepare($selectStmt))) {
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error . $selectStmt;
    }

    if (isset($_POST['filter']) && $_POST['filter'] != 'all') {
        $filter = $_POST['filter'];
        if (!$stmt->bind_param("s", $filter)) {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        }
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
    $movie_user = NULL;
    if (!$stmt->bind_result($movie_id, $movie_name, $movie_date, $movie_desc, $movie_img, $movie_user)) {
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
            echo '                            <li>Release Date: ';
            echo '                                <strong>' . $movie_date ;
            echo '                                </strong>';
            echo '                            </li>';
            echo '                            <li>Favorite Of: ';
            echo '                                <strong>' . $movie_user ;
            echo '                                </strong>';
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
                    <a href="#recommended">Recommended Movies</a>
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
<section id="recommended">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h2>Top Recommended Movies</h2>
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

<!-- About Section -->
<section class="success" id="about">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h2>About</h2>
                <hr class="star-light">
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4 col-lg-offset-2">
                <p>Shareable Movies is a place dedicated to fans of movies. Create an account and begin loading your movies.</p>
            </div>
            <div class="col-lg-4">
                <p>Use shareable movies to create a database of movies you have seen or want to see.  Favorite them to share with the broader shareable community.</p>
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