<?php
	include_once "config.php";
	include_once "functions.php";
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Krixus simplifies school data management by offering a comprehensive result portal and other digital tools to schools across Africa.">
	<meta name="keywords" content="Krixus, school management system, school result portal, Africa, data management, educational tools, innovation, technology">
	<meta name="author" content="Krixus">
	<meta name="robots" content="index, follow">
	<meta property="og:title" content="Krixus - Simplifying School Data Management">
	<meta property="og:description" content="Krixus empowers schools with innovative digital tools to manage student records, results, and more.">
	<meta property="og:image" content="https://krixus.kodin.ng/assets/img/logo.svg">
	<meta property="og:url" content="https://krixus.kodin.ng">
	<meta property="og:type" content="website">
	<meta property="og:site_name" content="Krixus">
	<meta property="og:locale" content="en_US">
	<title>Krixus | Modular platform for managing school data</title>
  <link rel="icon" type="image/svg+xml" href="assets/img/logo.svg">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
	<!-- Navigation Bar -->
	<nav class="navbar navbar-expand-lg navbar-light bg-light">
		<div class="container-fluid">
		  <a class="navbar-brand" href="index">
		    <img src="assets/img/logo.svg" alt="Krixus Logo" width="50px">
		  </a>
		  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
		    <span class="navbar-toggler-icon"></span>
		  </button>
		  <div class="collapse navbar-collapse" id="navbarNav">
		    <ul class="navbar-nav me-auto mb-md-0 mb-4">
		      <li class="nav-item"><a class="nav-link" href="docs">Docs</a></li>
		      <li class="nav-item"><a class="nav-link" href="features">Features</a></li>
		      <li class="nav-item"><a class="nav-link" href="subscription">Subscribe</a></li>
		    </ul>
		    <a href="index.php#demorequest"><button class="btn btn-primary btn-sm">Request a Demo</button></a>
		  </div>
		</div>
	</nav>

<?php include_once "notify.php" ?>	