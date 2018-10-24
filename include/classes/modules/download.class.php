<?php
/**
 * Moduł pobierajacy plik lub cały folder
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright	Copyright (c) 2004-2011 ARTplus
 * @package		ARTdesign
 * @version		1.0
 */

class Download {
	public $Baza = null;

	function __construct(&$BazaParametry, $Baza = false) {
            if(!$Baza){
                $DBConnectionSettings = new DBConnectionSettings($BazaParametry);
                $this->Baza = new DBMySQL($DBConnectionSettings);
            }else{
                $this->Baza = $Baza;
            }
	}
	
        function GetFile($Values){
            $File = $this->Baza->GetValue("SELECT link FROM zalaczniki WHERE id = '{$Values['attach']}'");
            $Sciezka = "files/$File";
            $this->DownloadFile($Sciezka);
        }

        function GetFolder($Values){
            $UsefullBase = new UsefullBase($this->Baza);
            $Sciezka = $UsefullBase->GetRealPath($_GET);
            $LockedDirs = $UsefullBase->GetLockedDirs($_GET);
            $Packing = new Packing();
            $Name = $UsefullBase->GetProjectName($Values['project']);
            if(isset($Values['dir'])){
                $Name .= "_".$UsefullBase->GetDirName($Values['dir']);
            }
            if(isset($_GET['fll'])){
                $Name .= "_".str_replace("/", "-", $_GET['fll']);
            }
            $EndFile = "archive_tmp/$Name.zip";
            if($Packing->ZipFolder($Sciezka, $EndFile, $LockedDirs)){
                $this->DownloadFile($EndFile);
            }else{
                exit;
            }
        }

        function GetArchive($ID){
            if($_SESSION['poziom_uprawnien'] == 1){
                $UsefullBase = new UsefullBase($this->Baza);
                $FileName = $UsefullBase->GetBackupFile($ID);
                $this->DownloadFile("archiwum/$FileName");
            }
        }

        function DownloadFile($Sciezka, $filenme = null){
            $fsize = filesize($Sciezka);
            $path_parts = pathinfo($Sciezka);
            $ext = strtolower($path_parts["extension"]);
            switch ($ext) {
                case "pdf": $ctype="application/pdf"; break;
                case "exe": $ctype="application/octet-stream"; break;
                case "zip": $ctype="application/zip"; break;
                case "docx":
                case "doc": $ctype="application/msword"; break;
                case "xls":
                case "xlsx": $ctype="application/vnd.ms-excel"; break;
                case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
                case "gif": $ctype="image/gif"; break;
                case "png": $ctype="image/png"; break;
                case "jpeg":
                case "jpg": $ctype="image/jpg"; break;
                default: $ctype="application/force-download";
            }
            header("Pragma: public"); // required
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private",false); // required for certain browsers
            header("Content-Type: $ctype");
            header("Content-Disposition: attachment; filename=\"".(empty($filenme) ? basename($Sciezka) : $filenme)."\";" );
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: ".$fsize);
            ob_clean();
            flush();
            readfile( $Sciezka );
        }

        function MakeArchive(){
            
        }
}
?>
