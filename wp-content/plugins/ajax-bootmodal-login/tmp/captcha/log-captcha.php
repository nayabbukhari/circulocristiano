<?php
require_once('../../../../../wp-load.php');
// Captcha image size
$imageWidth = 320;
$imageHeight= 50;

// Number of characters in captcha image - captcha length
$charsNumber=6;
// Random characters array
$characters=array_merge(range(0,9),range('a','z'));
shuffle($characters);

// Create captcha image
$captchaImage = imageCreateTrueColor($imageWidth, $imageHeight);
for ( $pixelX=0; $pixelX < $imageWidth; $pixelX++) {
	for ( $pixelY=0; $pixelY < $imageHeight; $pixelY++) {
		$randomPixelColor = imageColorAllocate($captchaImage, 255, 255, 255);
		imageSetPixel( $captchaImage, $pixelX, $pixelY , $randomPixelColor);
	}
}

$captchaText = "";	// Full captcha text

$charImageStep = $imageWidth/($charsNumber+1);

$charWritePoint= $charImageStep;

// Write captcha characters to the image
for( $i=0; $i < $charsNumber; $i++) {
	$nextChar = $characters[mt_rand(0, count($characters)-1)];
	$captchaText .= $nextChar;
	
	// Font properties
	$randomFontSize = mt_rand(25, 30);	// Random character size to spice things a little bit :)
	$randomFontAngle = mt_rand(-25,25);	// Twist the character a little bit
	$fontType = select_captcha_font();	// This is the font we are using - we need to point to the ttf file here
	
	// Pixels
	$pixelX = $charWritePoint; // We will write a character at this X point
	$pixelY = 40; // We will write a character at this Y point
	
	// Random character color								  // R			  // G			  // B			  // Alpha
	$randomCharColor = imageColorAllocateAlpha($captchaImage, mt_rand(0,255), mt_rand(0,255), mt_rand(0,255), mt_rand(0,25));
	
	// Write a character to the image
	imageTtfText($captchaImage, $randomFontSize, $randomFontAngle, $pixelX, $pixelY, $randomCharColor, $fontType, $nextChar);
	
	// Increase captcha step
	$charWritePoint += $charImageStep;
}

// Add currently generated captcha text to the session
$_SESSION['login_captcha'] = $captchaText;

// Return the image
return imagePng($captchaImage);

// Destroy captcha image
imageDestroy($captchaImage);
?>