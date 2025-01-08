<?php 
include 'includes/db.php';
$webInfo = getWebInfo(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $webInfo['website_name']; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="header-container">
            <img src="assets/img/<?php echo $webInfo['logo']; ?>" alt="Logo" class="logo">
            <div class="header-text">
                <h1><?php echo $webInfo['website_name']; ?></h1>
                <p><?php echo $webInfo['slogan']; ?></p>
                <p><?php echo $webInfo['address']; ?></p>
            </div>
        </div>
    </header>
