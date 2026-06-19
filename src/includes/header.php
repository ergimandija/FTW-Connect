<head>
    <link rel="icon" type="image/png" href="./assets/img/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FTW-Connect</title>
    <?php 
    $configPath = __DIR__ . '/../../config/config.php';

    if (file_exists($configPath)) {
        require_once $configPath;
    }
    ?>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.typekit.net/jky4twh.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="./css/global.css">
    <link rel="stylesheet" href="./css/header.css">
    <link rel="stylesheet" href="./css/footer.css">
    <link rel="stylesheet" href="./css/chat.css">
</head>   
<body>
    <?php include '../src/includes/navbar.php'; ?>
</body>