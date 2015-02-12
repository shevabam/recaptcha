# reCAPTCHA

- [Installation](#installation)
- [Initialization](#initialization)
- [Usage](#usage)
- [Customization](#customization)
	- [Theme](#theme)
	- [Language](#language)
	- [Type](#type)
- [Full example](#full-example)


## Installation

With Composer, add this line to your *require* section :

	"phelium/recaptcha": "dev-master"

Then run `composer update`.


## Initilization

	require 'vendor/autoload.php';
	
	use Phelium\Component\reCAPTCHA;
	

To initialize reCAPTCHA, you must provide your site key and your secret key.  
There is two possible ways :

	$reCAPTCHA = new reCAPTCHA('your site key', 'your secret key');

or

	$reCAPTCHA = new reCAPTCHA();
	$reCAPTCHA->setSiteKey('your site key');
	$reCAPTCHA->setSecretKey('your secret key');

## Usage

To generate the *script* tag, use :

	$reCAPTCHA->getScript();

To generate the HTML block, use in your form :

	$reCAPTCHA->getHtml();

Checking the server side, in your form validation script :

	if ($reCAPTCHA->isValid($_POST['g-recaptcha-response']))
	{
		// do whatever you want, the captcha is valid
	}

## Customization

### Theme

Several themes are available : light (default) or dark.
	
	$reCAPTCHA->setTheme('dark');

### Language

You can change the language of reCAPTCHA. Check [https://developers.google.com/recaptcha/docs/language](https://developers.google.com/recaptcha/docs/language) for more information.  
By default, the language is automatically detected.

	$reCAPTCHA->setLanguage('it');

### Type

Several types are available : image (default) or audio.

	$reCAPTCHA->setType('audio');


## Full example

Here is an example :

	<?php
	require 'vendor/autoload.php';
	use Phelium\Component\reCAPTCHA;
	
	$reCAPTCHA = new reCAPTCHA('your site key', 'your secret key');
	?>
	
	<html>
	<head>
	    <title>reCAPTCHA example</title>
	    <?php echo $reCAPTCHA->getScript(); ?>
	</head>
	
	<body>
	
	<?php
	if (isset($_POST['name']))
	{
	    var_dump($_POST);
	
	    if ($reCAPTCHA->isValid($_POST['g-recaptcha-response']))
	    {
	        echo '<br>-- Captcha OK ! --<br>';
	    }
	}
	?>
	
	<form action="#" method="POST">
	    <input type="text" name="name" placeholder="name">
	
	    <?php echo $reCAPTCHA->getHtml(); ?>
	
	    <input type="submit">
	</form>
	
	</body>
	</html>