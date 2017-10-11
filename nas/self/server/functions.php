<?php
function escapeAll($ji){
	$needles  = array("/",".",",","<",">","?","/",":",";","@","'","~","#","[","]","{","}","¬","`","!",'"',"£","$","%","^","&","*","(",")","_","=","|","\\");
	$newJ = str_replace($needles,"",$ji);
	$newJ = str_replace(" ","-",$newJ);
	return $newJ;
}
function Mcrypt($str){
	$outstr = "";
	$st = sha1($str);
	$len = strlen($st);
	$asciMap = array($len);
	$start = 0;
	$genC = 0;
	while ($start < $len){
		$asciMap[$start] = tener(ord(substr($st,$start,1)));
		//echo $asciMap[$start]."<br/>";
		$start++;
	}
	$xi = array($len/4);
	$start = 0;
	while($start < $len){
		$a = $asciMap[$start];
		$b = $asciMap[$start + 1];
		$c = $asciMap[$start + 2];
		$d = $asciMap[$start + 3];
		$sumer = 0;
		$sub = 0;
		while($sumer < 4){
			$t1 = 0;
			$t2 = 0;
			$t3 = 0;
			$t4 = 0;
			while($sub < 4){
				if($sumer == 0){
					if($sub == 0){
						$t1 = $t1 + substr($a,0,1);
					}
					elseif($sub == 1){
						$t1 = $t1 + substr($b,1,1);
					}
					elseif($sub == 2){
						$t1 = $t1 + substr($c,2,1);
					}
					else{
						$t1 = $t1 + substr($d,3,1);
					}
				}
				elseif($sumer == 1){
					if($sub == 0){
						$t2 = $t2 + substr($b,0,1);
					}
					elseif($sub == 1){
						$t2 = $t2 + substr($c,1,1);
					}
					elseif($sub == 2){
						$t2 = $t2 + substr($d,2,1);
					}
					else{
						$t2 = $t2 + substr($a,3,1);
					}
				}
				elseif($sumer == 2){
					if($sub == 0){
						$t3 = $t3 + substr($c,0,1);
					}
					elseif($sub == 1){
						$t3 = $t3 + substr($d,1,1);
					}
					elseif($sub == 2){
						$t3 = $t3 + substr($a,2,1);
					}
					else{
						$t3 = $t3 + substr($a,3,1);
					}
				}
				elseif($sumer == 3){
					if($sub == 0){
						$t4 = $t4 + substr($d,0,1);
					}
					elseif($sub == 1){
						$t4 = $t4 + substr($a,1,1);
					}
					elseif($sub == 2){
						$t4 = $t4 + substr($b,2,1);
					}
					else{
						$t4 = $t4 + substr($c,3,1);
					}
				}
					$sub++;
			}
				$sub = 0;
				$xi[$genC] = $t1 + $t2 + $t3 + $t4;
				$outstr = $outstr."".$xi[$genC];
				$genC++;
				$sumer++;
			}
			//echo $xi[$start] +$xi[$start+1] +$xi[$start+2] + $xi[start+3]."<br/>";
		$start = $start + 4;
	}
	return $outstr;	
}
function tener($binInt)
{
	$Quad = "";
	$binstack = array(8);
	$start = 128;
	$counter = 0;
	while ($start >= 1){
		if($start <= $binInt){
			$binInt = $binInt - $start;
			$binstack[$counter] = 1;
		}
		else{
			$binstack[$counter] = 0;
		}
		//echo $binstack[$counter];
		$start = $start/2;
		$counter++;
	}
	$start = 0;
	$binstack = array_reverse($binstack);
	while($start < 8){
		if($binstack[$start] == 1 && $binstack[$start+1] == 0){
			$Quad = $Quad."1";
		}
		elseif($binstack[$start] == 0  && $binstack[$start+1] == 1){
			$Quad = $Quad."3";
		}
		elseif($binstack[$start] == 0 && $binstack[$start + 1] == 0){
			$Quad = $Quad."0";
		}
		else{
			$Quad = $Quad."4";
		}
		//echo $binstack[$start]."and".$binstack[$start+1]."<br/>";
		$start = $start + 2;
	}
	return $Quad;
}
function validatestr($str,$type){
	$right = true;
	//$message = "";

	//$responses = array($right, $message);
	if ($type == "str"){
		$right = preg_match("/^[a-zA-Z]+$/",$str);
	}
	elseif($type == "anum"){
		$right = preg_match("/^[0-9a-zA-Z]+$/",$str);
	}
	elseif($type == "num"){
		$right = preg_match("/^[0-9]+$/",$str);
	}
	elseif($type == "mail"){
		list($userName, $mailDomain) = explode("@", $str);
		$right = checkdnsrr($mailDomain, "MX");
		if($right){
			
		}else{
			$message = em("email is invalid");
			$right = true;
		}
	}
	return $right;
}
function validatestrstr($str,$typed,$jr){
	//$right = true;
	$message = "";
	if ($typed == "str"){
		$right = preg_match("/^[0-9]+$/",$str);
	}else if($typed == "anum"){
		$right = preg_match("/^[0-9a-zA-Z]+$/",$str);
	}else if($typed == "num"){
		$right = preg_match("/^[0-9]+$/",$str);
	}elseif($typed == "mail"){
		//echo "kkk";
		list($userName, $mailDomain) = explode("@", $str);
		$right = checkdnsrr($mailDomain, "MX");
		if($right){
			$q = redef("query","select*from kins where email = '$str'",$jr,0);
			if(redef("mCount",$q,$jr,0) > 0){
				$right = false;
				$message = eM("email address exists");
			}else{
				$right = true;
			}
		}else{
			$message = em("email is invalid");
			$right = false;
		}
	}
	$responses = array($right, $message);
	return $responses;
}
function pricer($pri){
	$r = strrev($pri);
	$len = strlen($r);
	$start = 0;
	$nstr  = "";
	$l = 0;
	$iS = false;
	while ($start < $len){
		if($l == 2 && ($start+1) < $len){
			$nstr = ",".substr($r,$start,1).$nstr;
			$iS = true;
		}else{
			$nstr = substr($r,$start,1).$nstr;
		}
		$start++;
		if (!($iS)){
			$l++;
		}else{
			$l = 0;
			$iS = false;
		}
	}
return "&#8358;".$nstr;
}

function wM($str){
	return '<div style="padding:20px;"><div style="color:#F48622; font-size:15px; background:#FBE9BD; border-left:#F48622 thick solid; padding:15px;"><i class="fa fa-exclamation-circle"></i> '.$str.'</div></div>';
}
function eM($str)
{
	$nstr = '<div style="padding:20px;"><div style="color:#ED050B; font-size:15px; background:#F9B4B0; border-left:#ED050B thick solid; padding:15px;" align="left"><i class="fa fa-warning"></i> '.$str.'</div></div>';
	return $nstr;
}
function thumber($jstr){
	$c = explode("/",$jstr);
	if(in_array("avatar",$c)){
		return $jstr;
	}
	else{
		$v = count($c);
		$c[$v-1] = 'thumb_'.$c[$v-1];
		return(implode("/",$c));
	}
	return $jstr;
}
function timer($tstr)
{
	$ret = "";
	$pass_time = time() - $tstr;
	if($pass_time < 2){
		$ret = "<i class='fa fa-clock'></i> about a second ago";
	}
	else if($pass_time > 1 and $pass_time < 60){
		$ret = "<i class='fa fa-clock'></i> less than a minute ago";
	}
	else{
		$r = ceil($pass_time/60);
		if ($r >= 1 and $r < 60)
		{
			// within a minute;
			if ($r > 1)
			{
				$ret = "<i class='fa fa-clock'></i> about ".$r." minutes ago";
			}
			else
			{
				$ret = "<i class='fa fa-clock'></i> about a minute ago";
			}
		}
		elseif($r >= 60 and $r < 1440)
		{
			// within an hour
			if(($r/60) > 1)
			{
				$ret = "<i class='fa fa-clock'></i> about ".ceil($r/60)." hours ago";
			}
			else
			{
				$ret = "<i class='fa fa-clock'></i> about a hour ago";
			}
		}
		elseif($r >= 1440 and $r < 10080)
		{
			//within a day
			if(($r/1440) > 1)
			{
				$ret = "<i class='fa fa-clock'></i> about ".ceil($r/1440)." days ago";
			}
			else
			{
				$ret = "<i class='fa fa-clock'></i> about a day ago";
			}
		}
		elseif($r >= 10080 and $r < 40320)
		{
			//within a week
			if(($r/10080) > 1)
			{
				$ret = "<i class='fa fa-clock'></i> about ".ceil($r/10080)." weeks ago";
			}
			else
			{
				$ret = "<i class='fa fa-clock'></i> about a week ago";
			}
		}
		elseif ($r >= 40320 and $r < 483840)
		{
			//within a month
			if(($r/40320) > 1)
			{
				$ret = "<i class='fa fa-clock'></i> about ".ceil($r/40320)." months ago";
			}
			else
			{
				$ret = "<i class='fa fa-clock'></i> about a month ago";
			}
		}
		else
		{
			//within a year;
			if(($r/483840) > 1)
			{
				$ret = "<i class='fa fa-clock'></i> about ".ceil($r/483840)." years ago";
			}
			else
			{
				$ret = "<i class='fa fa-clock'></i> about a year ago";
			}
		}
	}
	return $ret;
}
function sM($str){
	//$nstr = '<div style="background:#5cb85c; color:#fff; padding:12px; font-size:14px; width:70%; border-radius:8px;" align="center">'.$str.'</div>';
	$nstr = '<div style="padding:20px;"><div style="color:#2B8E11; font-size:15px; background:#BCF8AD; border-left:#2B8E11 thick solid; padding:15px;" align="left"><i class="fa fa-check-square-o"></i> '.$str.'</div></div>';
	return $nstr;
}
function striphtml($str){
$search = array ("'<script[^>]*?>.*?</script>'si", // Strip out javascript
				"'<[\/\!]*?[^<>]*?>'si", // Strip out html tags 
				"'([\r\n])[\s]+'", // Strip out white space 
				"'&(quot|#34);'i", // Replace html entities 
				"'&(amp|#38);'i", 
				"'&(lt|#60);'i", 
				"'&(gt|#62);'i", 
				"'&(nbsp|#160);'i", 
				"'&(iexcl|#161);'i", 
				"'&(cent|#162);'i", 
				"'&(pound|#163);'i", 
				"'&(copy|#169);'i", 
				"'&#(\d+);'e"); // evaluate as php
$replace = array ("", "", "\\1", "\"", "&", "<", ">", " ", chr(161), chr(162), chr(163), chr(169), "chr(\\1)");
return preg_replace($search, $replace, $str);
}
function scrambler($str){
	$randa = rand(11111,99999);
	$randb = rand(11111,99999);
	$str = $randa.$str.$randb;
	return $str;
}
function unScramble($str){
	$str = substr($str,5,strlen($str)-10);
	return $str;
}

function thumbnail($nna, $img, $source, $dest, $maxw, $maxh ) {
    $jpg = $source.$img;
	$x = explode('.',$img);
	$pio = sizeof($x) -1;
	$format = $x[$pio];
	$cont = true;
    if( $jpg ) {
        list( $width, $height  ) = getimagesize( $jpg );
		if ( strtoupper($format) == "JPEG" || strtoupper($format) == "JPG")
		{
        $source = imagecreatefromjpeg( $jpg );
		}
		else if (strtoupper($format) == "PNG")
		{
			$source = imagecreatefrompng($jpg);
		}
		else if (strtoupper($format == "GIF"))
		{
			$source = imagecreatefromgif($jpg);
		}
		else if (strtoupper($format == "BMP"))
		{
			$source = imagecreatefromwbmp($jpg);
		}
		else
		{
			//echo 'shiiiii';
			$cont = false;
		}
        if( $maxw >= $width && $maxh >= $height ) {
            $ratio = 1;
        }elseif( $width > $height ) {
            $ratio = $maxw / $width;
        }else {
            $ratio = $maxh / $height;
        }
		if ($cont)
		{
			$thumb_width = round( $width * $ratio );
			$thumb_height = round( $height * $ratio );
			$thumb = imagecreatetruecolor( 50, 50 );
			imagecopyresampled( $thumb, $source, 0, 0, 0, 0, 50, 50, $width, $height );
			$path = $dest.$nna;
			imagejpeg( $thumb, $path, 75 );
		}
    }
	if ($cont)
	{
		imagedestroy( $thumb );
		imagedestroy( $source );
		//unlink($jpg);
	}
}
function watermarker($tmp_f, $name, $new_name, $new_dir)
{
	ob_start();
    $disp_width_max=150;                    // used when displaying watermark choices
    $disp_height_max=80;                    // used when displaying watermark choices
    $edgePadding=15;                        // used when placing the watermark near an edge
    $quality=100;                           // used when generating the final image
    $default_watermark='wm.fw.png';  // the default image to use if no watermark was chosen
        
            // be sure that the other options we need have some kind of value
            /*if(!isset($_POST['save_as']))*/ $_POST['save_as']='jpeg';
            /*if(!isset($_POST['v_position']))*/ $_POST['v_position']='center';
            /*if(!isset($_POST['h_position']))*/ $_POST['h_position']='center';
            /*if(!isset($_POST['wm_size']))*/ $_POST['wm_size']='.5';
            /*if(!isset($_POST['watermark']))*/ $_POST['watermark']=$default_watermark;
        
            // file upload success
            $size=getimagesize($tmp_f); //$size=getimagesize($_FILES['watermarkee']['tmp_name']);
            if($size[2]==2 || $size[2]==3){
                // it was a JPEG or PNG image, so we're OK so far
                
                $original=$tmp_f; //$original=$_FILES['watermarkee']['tmp_name'];
                /*$target_name=date('YmdHis').'_'.
                    // if you change this regex, be sure to change it in generated-images.php:26
                    preg_replace('`[^a-z0-9-_.]`i','',$_FILES['watermarkee']['name']);*/
					$target_name= $new_name;
               
                $target=$new_dir."".$new_name; //$target=dirname(__FILE__).'/results/'.$target_name;
                $watermark='bins/'.$_POST['watermark'];
				//$watermark=dirname(__FILE__).'/watermarks/'.$_POST['watermark'];
                $wmTarget=$watermark.'.tmp';

                $origInfo = getimagesize($original); 
                $origWidth = $origInfo[0]; 
                $origHeight = $origInfo[1]; 

                $waterMarkInfo = getimagesize($watermark);
                $waterMarkWidth = $waterMarkInfo[0];
                $waterMarkHeight = $waterMarkInfo[1];
        
                // watermark sizing info
                if($_POST['wm_size']=='larger'){
                    $placementX=0;
                    $placementY=0;
                    $_POST['h_position']='center';
                    $_POST['v_position']='center';
                	$waterMarkDestWidth=$waterMarkWidth;
                	$waterMarkDestHeight=$waterMarkHeight;
                    
                    // both of the watermark dimensions need to be 5% more than the original image...
                    // adjust width first.
                    if($waterMarkWidth > $origWidth*1.05 && $waterMarkHeight > $origHeight*1.05){
                    	// both are already larger than the original by at least 5%...
                    	// we need to make the watermark *smaller* for this one.
                    	
                    	// where is the largest difference?
                    	$wdiff=$waterMarkDestWidth - $origWidth;
                    	$hdiff=$waterMarkDestHeight - $origHeight;
                    	if($wdiff > $hdiff){
                    		// the width has the largest difference - get percentage
                    		$sizer=($wdiff/$waterMarkDestWidth)-0.05;
                    	}else{
                    		$sizer=($hdiff/$waterMarkDestHeight)-0.05;
                    	}
                    	$waterMarkDestWidth-=$waterMarkDestWidth * $sizer;
                    	$waterMarkDestHeight-=$waterMarkDestHeight * $sizer;
                    }else{
                    	// the watermark will need to be enlarged for this one
                    	
                    	// where is the largest difference?
                    	$wdiff=$origWidth - $waterMarkDestWidth;
                    	$hdiff=$origHeight - $waterMarkDestHeight;
                    	if($wdiff > $hdiff){
                    		// the width has the largest difference - get percentage
                    		$sizer=($wdiff/$waterMarkDestWidth)+0.05;
                    	}else{
                    		$sizer=($hdiff/$waterMarkDestHeight)+0.05;
                    	}
                    	$waterMarkDestWidth+=$waterMarkDestWidth * $sizer;
                    	$waterMarkDestHeight+=$waterMarkDestHeight * $sizer;
                    }
                }else{
	                $waterMarkDestWidth=round($origWidth * floatval($_POST['wm_size']));
	                $waterMarkDestHeight=round($origHeight * floatval($_POST['wm_size']));
	                if($_POST['wm_size']==1){
	                    $waterMarkDestWidth-=2*$edgePadding;
	                    $waterMarkDestHeight-=2*$edgePadding;
	                }
                }

                // OK, we have what size we want the watermark to be, time to scale the watermark image
                resize_png_image($watermark,$waterMarkDestWidth,$waterMarkDestHeight,$wmTarget);
                
                // get the size info for this watermark.
                $wmInfo=getimagesize($wmTarget);
                $waterMarkDestWidth=$wmInfo[0];
                $waterMarkDestHeight=$wmInfo[1];

                $differenceX = $origWidth - $waterMarkDestWidth;
                $differenceY = $origHeight - $waterMarkDestHeight;

                // where to place the watermark?
                switch($_POST['h_position']){
                    // find the X coord for placement
                    case 'left':
                        $placementX = $edgePadding;
                        break;
                    case 'center':
                        $placementX =  round($differenceX / 2);
                        break;
                    case 'right':
                        $placementX = $origWidth - $waterMarkDestWidth - $edgePadding;
                        break;
                }

                switch($_POST['v_position']){
                    // find the Y coord for placement
                    case 'top':
                        $placementY = $edgePadding;
                        break;
                    case 'center':
                        $placementY =  round($differenceY / 2);
                        break;
                    case 'bottom':
                        $placementY = $origHeight - $waterMarkDestHeight - $edgePadding;
                        break;
                }
       
                if($size[2]==3)
                    $resultImage = imagecreatefrompng($original);
                else
                    $resultImage = imagecreatefromjpeg($original);
                imagealphablending($resultImage, TRUE);
        
                $finalWaterMarkImage = imagecreatefrompng($wmTarget);
                $finalWaterMarkWidth = imagesx($finalWaterMarkImage);
                $finalWaterMarkHeight = imagesy($finalWaterMarkImage);
        
                imagecopy($resultImage,
                          $finalWaterMarkImage,
                          $placementX,
                          $placementY,
                          0,
                          0,
                          $finalWaterMarkWidth,
                          $finalWaterMarkHeight
                );
                
                if($size[2]==3){
                    imagealphablending($resultImage,FALSE);
                    imagesavealpha($resultImage,TRUE);
                    imagepng($resultImage,$target,$quality);
                }else{
                    imagejpeg($resultImage,$target,$quality); 
                }

                imagedestroy($resultImage);
                imagedestroy($finalWaterMarkImage);
				return true;
			}
}
function resize_png_image($img,$newWidth,$newHeight,$target){
    $srcImage=imagecreatefrompng($img);
    if($srcImage==''){
        return FALSE;
    }
    $srcWidth=imagesx($srcImage);
    $srcHeight=imagesy($srcImage);
    $percentage=(double)$newWidth/$srcWidth;
    $destHeight=round($srcHeight*$percentage)+1;
    $destWidth=round($srcWidth*$percentage)+1;
    if($destHeight > $newHeight){
        // if the width produces a height bigger than we want, calculate based on height
        $percentage=(double)$newHeight/$srcHeight;
        $destHeight=round($srcHeight*$percentage)+1;
        $destWidth=round($srcWidth*$percentage)+1;
    }
    $destImage=imagecreatetruecolor($destWidth-1,$destHeight-1);
    if(!imagealphablending($destImage,FALSE)){
        return FALSE;
    }
    if(!imagesavealpha($destImage,TRUE)){
        return FALSE;
    }
    if(!imagecopyresampled($destImage,$srcImage,0,0,0,0,$destWidth,$destHeight,$srcWidth,$srcHeight)){
        return FALSE;
    }
    if(!imagepng($destImage,$target)){
        return FALSE;
    }
    imagedestroy($destImage);
    imagedestroy($srcImage);
    return TRUE;
}

function resize_crop_image($max_width, $max_height, $source_file, $dst_dir, $quality = 30){
 $imgsize = getimagesize($source_file);
 $width = $imgsize[0];
 $height = $imgsize[1];
 $mime = $imgsize['mime'];
 switch($mime){
  case 'image/gif':
   $image_create = "imagecreatefromgif";
   $image = "imagegif";
   break;
  case 'image/png':
   $image_create = "imagecreatefrompng";
   $image = "imagepng";
   $quality = 3;
   break;
  case 'image/jpeg':
   $image_create = "imagecreatefromjpeg";
   $image = "imagejpeg";
   $quality = 30;
   break;
  default:
   return false;
   break;
 }
 $dst_img = imagecreatetruecolor($max_width, $max_height);
 $src_img = $image_create($source_file);
 $width_new = $height * $max_width / $max_height;
 $height_new = $width * $max_height / $max_width;
 //if the new width is greater than the actual width of the image, then the height is too large and the rest cut off, or vice versa
 if($width_new > $width){
  //cut point by height
  $h_point = (($height - $height_new) / 2);
  //copy image
  imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
 }else{
  //cut point by width
  $w_point = (($width - $width_new) / 2);
  imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
 }
 $image($dst_img, $dst_dir, $quality);
 if($dst_img)imagedestroy($dst_img);
 if($src_img)imagedestroy($src_img);
}

?>