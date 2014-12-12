<?php
/*
Classe resize
Jerome Combes (jeromecombes@yahoo.fr)
12 decembre 2014

Classe permettant de redimensionner des images jpg et png.
Cree une copie de l'image donnees aux dimensions souhaitees en gardant les proportions (on donne la hauteur ou la largeur souhaitee).
La copie est cree dans un sous-dossier nomme mini/xxx (ou xxx est la taille demandee). Si le dossier n'existe pas, il sera cree
(donner les droits a Apache).

Utilisation :
$r=new resize();
$r->folder= "/folder/images";
$r->file="photo.jpg";
$r->width=500;		// ou largeur : $->height=500;
$r->resizePicture();

Resultat : fichier /folder/images/mini/500/photo.jpg = copie de 500px de large


Example avec boucle :

$folder="/usr/local/www/events/images";
chdir($folder);
foreach(glob("{*.jpg,*.png,*.jpeg}",GLOB_BRACE) as $file){

	// Copie 35px de haut pour vignette dans tableaux
	$r=new resize();
	$r->folder=$folder;
	$r->file=$file;
	$r->height=35;
	$r->resizePicture();

	// Copie 125px de large pour apercus
	$r->height=null;
	$r->width=125;
	$r->resizePicture();
	unset($r);
}

*/



class resize{

	public $file=null;
	public $folder=null;
	public $height=null;
	public $width=null;


	public function resizePicture(){
		$f=new finfo(FILEINFO_MIME);

		switch($f->file($this->file)){
			case "image/jpeg" : $this->resizeJPG();	break;
			case "image/jpg" : $this->resizeJPG();	break;
			case "image/png" : $this->resizePNG();	break;
		}

		unset($f);
	}

	public function resizeJPG(){
		$file=$this->file;
		$folder=$this->folder;
		$newHeight=$this->height;
		$newWidth=$this->width;

		$newSize=$newHeight?$newHeight:$newWidth;

		if(!is_dir("$folder/mini/$newSize")){
			mkdir("$folder/mini/$newSize",0777,true);
		}

		$currentFile="$folder/$file";
		$newFile="$folder/mini/$newSize/$file";

		if(!file_exists($newFile)){
			$size=getimagesize($currentFile);
			$ratio=$size[0]/$size[1];             //      width/height
			$currentPicture=imagecreatefromjpeg($currentFile);

			if($newHeight){
				if($size[1]>$newHeight){
					$height=$newHeight;
					$width=$height*$ratio;
				}else{
					$width=$size[0];
					$height=$size[1];
				}
			}elseif($newWidth){
				if($size[0]>$newWidth){
					$width=$newWidth;
					$height=$width/$ratio;
				}else{
					$width=$size[0];
					$height=$size[1];
				}
			}

			$newPicture=imagecreatetruecolor($width,$height) or die("Error");
			imagecopyresampled($newPicture , $currentPicture, 0, 0, 0, 0, $width, $height, $size[0],$size[1]);
			imagejpeg($newPicture , $newFile, 100);
		}
	}


	public function resizePNG(){
		$file=$this->file;
		$folder=$this->folder;
		$newHeight=$this->height;
		$newWidth=$this->width;

		$newSize=$newHeight?$newHeight:$newWidth;

		if(!is_dir("$folder/mini/$newSize")){
			mkdir("$folder/mini/$newSize",0777,true);
		}

		$currentFile="$folder/$file";
		$newFile="$folder/mini/$newSize/$file";

		if(!file_exists($newFile)){
			$size=getimagesize($currentFile);
			$ratio=$size[0]/$size[1];             //      width/height
			$currentPicture=imagecreatefrompng($currentFile);

			if($newHeight){
				if($size[1]>$newHeight){
					$height=$newHeight;
					$width=$height*$ratio;
				}else{
					$width=$size[0];
					$height=$size[1];
				}
			}elseif($newWidth){
				if($size[0]>$newWidth){
					$width=$newWidth;
					$height=$width/$ratio;
				}else{
					$width=$size[0];
					$height=$size[1];
				}
			}

			$newPicture=imagecreatetruecolor($width,$height) or die("Error");
			imagealphablending($newPicture, false);
			imagesavealpha($newPicture,true);
			$transparent = imagecolorallocatealpha($newPicture, 255, 255, 255, 127);
			imagefilledrectangle($newPicture, 0, 0, $width, $height, $transparent);
			imagecopyresampled($newPicture , $currentPicture, 0, 0, 0, 0, $width, $height, $size[0],$size[1]);
			imagepng($newPicture , $newFile, 9);
		}
	}


}
?>
