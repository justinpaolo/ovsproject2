<?php
include("header.html");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}
.nav-bar
{
    background-image: linear-gradient(to right, #74b9ff,#74b9ff);
    padding: 0;

}

.btn-special-2 {
    padding: 12px 24px;
    background-color: white;
    color: hsl(243, 80%, 62%);
    border-radius: 6px;
    border: 2px hsl(243, 80%, 62%) solid;
    transition: transform 250ms ease-in-out;
}

.btn-special-2:hover {
    transform: scale(1.10);
}

.btn-special-2:active {
    transform: scale(.9);
}
#footersection{
    margin-top:80px;
}
.h2_3{
    margin-top:30px;
}
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="h2_3"> Select Result Year</h2>
            <a href="validation.php"><button style="margin-top:40px;" class="btn-special-2">1<sup>st</sup> Year </button></a>
            </div>
            <div class="col-md-12">
            <a href="validation_2.php"><button style="margin-top:60px;" class="btn-special-2">2<sup>nd</sup> Year </button></a>
            </div>
        </div>
    </div>

    
</body>
</html>
<?php
include("footer.html");
?>