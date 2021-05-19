<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<?php
		//sets the webroot var from server because php is weird
		$basepath = $_SERVER["DOCUMENT_ROOT"];
		//checks if the user is logged in
		include $basepath . "/functions/checklogin.php" ;
		checklogin();
		//gets the current css theme
		include $basepath . "/functions/setcss.php";
		$stylesheet = setcss();
		?>
		<link rel="stylesheet" type="text/css" href="/css/<?php echo $stylesheet; //sets the css stylesheet?>.css">
		<title>PCAPAnalyzer - Database created</title>
		<meta http-equiv="refresh" content="2; url=../index.php">
	</head>
	<body>
		<?php
		//avoids command injection
		$folder = escapeshellcmd($_POST["dbName"]);
		$basepath = $_SERVER["DOCUMENT_ROOT"];
		//sets the target folder for the pcaps
		$target_dir = $basepath . "/../backend/analysisGenerator/files/pcaps/analysis_" . $folder . "/";
		//creates the target pcap folder
		mkdir($target_dir, 0777, true);
		//saves the .pcap files into the newly created folder
		foreach (range(0, sizeof($_FILES['file']['name']) - 1) as $filenum) {
			$filename = $_FILES['file']['name'][$filenum];
			$filename = preg_replace("/[^A-Za-z0-9 \.\-_]/", '', $filename);
			$target_file = $target_dir . $filename;
			move_uploaded_file($_FILES['file']['tmp_name'][$filenum], $target_file);
			chmod($target_file, 0777);
			}
		//runs the backend script for analysis database generation
		$commandstring = "python3 " . $basepath . "/../backend/analysisGenerator/script/dbGenerator.py " . $_POST['dbName']; 
		$command = escapeshellcmd($commandstring);
		$output = shell_exec($command);
		?>
		<h1>Files uploaded and database created! returning to start page...</h1>
	</body>
</html>
