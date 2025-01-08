<?php 
include 'includes/db.php';
$webInfo = getWebInfo(); 
?>

<footer>
    <div class="footer-container">
        <div class="footer-info">
            <h2><?php echo $webInfo['website_name']; ?></h2>
            <p><?php echo $webInfo['slogan']; ?></p>
            <p>Follow us on:</p>
            <ul>
                <li><a href="<?php echo $webInfo['social_twitter']; ?>" target="_blank">Twitter</a></li>
                <li><a href="<?php echo $webInfo['social_facebook']; ?>" target="_blank">Facebook</a></li>
                <li><a href="<?php echo $webInfo['social_instagram']; ?>" target="_blank">Instagram</a></li>
            </ul>
        </div>
    </div>
</footer>
</body>
</html>
