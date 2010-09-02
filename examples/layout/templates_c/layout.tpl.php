<<?php echo '?'; ?>xml version="<?php echo '1.0'; ?>" standalone="<?php echo 'no'; ?>" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

	
	
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>OPT Layout example</title>

	
	<?php $_sectcss_vals = &$ctx->_data['css']; if(is_array($_sectcss_vals) && ($_sectcss_cnt = sizeof($_sectcss_vals)) > 0){  for($_sect1_i = 0; $_sect1_i < $_sectcss_cnt; $_sect1_i++){  switch($_sectcss_vals[$_sect1_i]['item']){    case 'equals':   ?>
			<link rel="stylesheet" href="main.css" type="text/css" media="screen" />
			<link rel="stylesheet" href="text.css" type="text/css" media="screen" />
		<?php  break;     case 'equals':   ?>
			<link rel="stylesheet" href="printable.css" type="text/css" media="print" />
		<?php  break;     default:   ?>
			<link rel="stylesheet" href="<?php echo htmlspecialchars($_sectcss_vals[$_sect1_i]['file']);   ?>" type="text/css" />
		<?php  break;     }   }   }   ?>
</head>
<body>
<div id="header">
	<h1>Layout example</h1>
	<p>This example file shows, how to manage the more advanced layouts with OPT views
	and sections.</p>
	<p>Select a module:</p>
	<ol>
		<li><a href="index.php?cmd=1">Module A</a></li>
		<li><a href="index.php?cmd=2">Module B</a></li>
		<li><a href="index.php?cmd=3">Module C</a></li>
		<li><a href="index.php?cmd=4">Module A + C</a></li>
		<li><a href="index.php?cmd=5">Module A + B + C</a></li>
	</ol>
</div>
<div id="content">
	<?php $_sectmodules_vals = &$ctx->_data['modules']; if(is_array($_sectmodules_vals) && ($_sectmodules_cnt = sizeof($_sectmodules_vals)) > 0){  for($_sect1_i = 0; $_sect1_i < $_sectmodules_cnt; $_sect1_i++){   ?>
		<?php  if(!$_sectmodules_vals[$_sect1_i]['view'] instanceof Opt_View ||!$_sectmodules_vals[$_sect1_i]['view']->_parse($output, false)){   ?>
			<p>We are sorry, but the requested view has not been found.</p>
		<?php  }   ?>
	<?php  }   }   ?>
</div>
<div id="footer">
	<p>Copyright <?php echo Opt_Function::entity('copy');   ?> Invenzzia Group 2009</p>
</div>
</body>
</html>
