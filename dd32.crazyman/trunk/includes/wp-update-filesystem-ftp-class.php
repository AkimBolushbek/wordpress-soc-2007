<?php

class WP_Filesystem_FTP{
	var $link;
	var $timeout = 5;
	
	var $wp_base;
	
	var $filetypes = array(
							'php'=>FTP_ASCII,
							'css'=>FTP_ASCII,
							'txt'=>FTP_ASCII,
							'js'=>FTP_ASCII,
							'html'=>FTP_ASCII,
							'htm'=>FTP_ASCII,
							
							'jpg'=>FTP_BINARY,
							'png'=>FTP_BINARY,
							'gif'=>FTP_BINARY,
							'bmp'=>FTP_BINARY
							);
	
	function WP_Filesystem_FTP($opt=''){
		//Check if possible to use ftp functions.
		if( ! function_exists('ftp_connect') )
			return false;
		//Check if options provided
		//if( ! is_array($opt) );
		//	return false;

		//Set defaults:
		if( ! isset($opt['port']) || empty($opt['port']) )
			$opt['port'] = 21;
		if( ! isset($opt['host']) || empty($opt['host']) )
			$opt['host'] = isset($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : 'localhost';
		
		//Check if the options provided are OK.
		if( ! isset($opt['username']) || ! isset($opt['password']) ||
			 empty ($opt['username']) ||  empty ($opt['password']) ){
			 echo 'no auth details';
			 return false;
		}
		
		//All is A-OK.
		if( false == ($this->link = ftp_connect($opt['host'], $opt['post'],$this->timeout) ) ){
			echo 'no connect';
			return false;
		}
		
		if( false == (ftp_login($this->link,$opt['username'], $opt['password']) ) ){
			echo 'no login';
			return false;
		}
		
	}
	function find_base_dir($base = '.'){
		$this->wp_base = ABSPATH;
		return $this->wp_base;
	}
	function get_base_dir($base = '.'){
		return $this->wp_base;
	}
	function get_contents($file,$type='',$resumepos=0){
		if( empty($type) ){
			$extension = substr(strrchr($filename, "."), 1);
			$type = isset($this->filetypes[ $extension ]) ? $this->filetypes[ $extension ] : FTP_ASCII;
		}
		$temp = tmpfile();
		if( ! @ftp_fget($this->link,$temp,$file,$type,$resumepos) )
			return false;
		fseek($temp, 0); //Skip back to the start of the file being written to
		$contents = '';
		while (!feof($temp)) {
			$contents .= fread($temp, 8192);
		}
		fclose($temp);
		return $contents;
	}
	function get_contents_array($file){
		return explode("\n",$this->get_contents($file));
	}
	function put_contents($file,$contents,$type=''){
		if( empty($type) ){
			$extension = substr(strrchr($filename, "."), 1);
			$type = isset($this->filetypes[ $extension ]) ? $this->filetypes[ $extension ] : FTP_ASCII;
		}
		$temp = tmpfile();
		fwrite($temp,$contents);
		fseek($temp, 0); //Skip back to the start of the file being written to
		$ret = @ftp_fput($this->link,$file,$temp,$type);
		fclose($temp);
		return $ret;
	}
	function chgrp($file,$group,$recursive=false){
		return false;
	}
	function chmod($file,$mode,$recursive=false){
		if( ! $this->exists($file) )
			return false;
		if( ! $recursive || ! $this->is_dir($file) ){
			if (!function_exists('ftp_chmod'))
				return ftp_site($this->link, sprintf('CHMOD %o %s', $mode, $file));
			return ftp_chmod($this->link,$mode,$file);
		}
		//Is a directory, and we want recursive
		$filelist = $this->dirlist($file);
		foreach($filelist as $filename){
			$this->chmod($file.'/'.$filename,$mode,$recursive);
		}
		return true;
	}
	function chown($file,$owner,$recursive=false){
		return false;
	}
	function owner($file){
		$dir = $this->dirlist($file);
		return $dir[$file]['owner'];
	}
	function getchmod($file){
		$dir = $this->dirlist($file);
		return $dir[$file]['permsn'];
	}
	function gethchmod($file){
		//From the PHP.net page for ...?
		$perms = $this->getchmod($file);
		if (($perms & 0xC000) == 0xC000) {
			// Socket
			$info = 's';
		} elseif (($perms & 0xA000) == 0xA000) {
			// Symbolic Link
			$info = 'l';
		} elseif (($perms & 0x8000) == 0x8000) {
			// Regular
			$info = '-';
		} elseif (($perms & 0x6000) == 0x6000) {
			// Block special
			$info = 'b';
		} elseif (($perms & 0x4000) == 0x4000) {
			// Directory
			$info = 'd';
		} elseif (($perms & 0x2000) == 0x2000) {
			// Character special
			$info = 'c';
		} elseif (($perms & 0x1000) == 0x1000) {
			// FIFO pipe
			$info = 'p';
		} else {
			// Unknown
			$info = 'u';
		}
		
		// Owner
		$info .= (($perms & 0x0100) ? 'r' : '-');
		$info .= (($perms & 0x0080) ? 'w' : '-');
		$info .= (($perms & 0x0040) ?
					(($perms & 0x0800) ? 's' : 'x' ) :
					(($perms & 0x0800) ? 'S' : '-'));
		
		// Group
		$info .= (($perms & 0x0020) ? 'r' : '-');
		$info .= (($perms & 0x0010) ? 'w' : '-');
		$info .= (($perms & 0x0008) ?
					(($perms & 0x0400) ? 's' : 'x' ) :
					(($perms & 0x0400) ? 'S' : '-'));
		
		// World
		$info .= (($perms & 0x0004) ? 'r' : '-');
		$info .= (($perms & 0x0002) ? 'w' : '-');
		$info .= (($perms & 0x0001) ?
					(($perms & 0x0200) ? 't' : 'x' ) :
					(($perms & 0x0200) ? 'T' : '-'));
		return $info;
	}
	function getnumchmodfromh($mode) {
		$realmode = "";
		$legal =  array("","w","r","x","-");
		$attarray = preg_split("//",$mode);
		for($i=0;$i<count($attarray);$i++){
		   if($key = array_search($attarray[$i],$legal)){
			   $realmode .= $legal[$key];
		   }
		}
		$mode = str_pad($realmode,9,'-');
		$trans = array('-'=>'0','r'=>'4','w'=>'2','x'=>'1');
		$mode = strtr($mode,$trans);
		$newmode = '';
		$newmode .= $mode[0]+$mode[1]+$mode[2];
		$newmode .= $mode[3]+$mode[4]+$mode[5];
		$newmode .= $mode[6]+$mode[7]+$mode[8];
		return $newmode;
	}
	function group($file){
		$dir = $this->dirlist($file);
		return $dir[$file]['group'];
	}
	function copy($source,$destination,$overwrite=false){
		if( ! $overwrite && $this->exists($destination) )
			return false;
		$content = $this->get_content($source);
		$this->put_contents($destination,$content);
	}
	function move($source,$destination,$overwrite=false){
		return ftp_rename($this->link,$source,$destination);
	}
	function delete($file,$recursive=false){
		if( $this->is_file($file) )
			return ftp_delete($this->link,$file);
		if( !$recursive )
			return ftp_rmdir($this->link,$file);
		$filelist = $this->dirlist($file);
		foreach($filelist as $filename){
			$this->delete($file.'/'.$filename,$recursive);
		}
	}
	function exists($file){
		//TODO: Do not feel this is the best way.
		return (bool)$this->dirlist($file);
			
	}
	function is_file($file){
		return $this->exists($file);
	}
	function is_dir($path){
		return $this->exists($file);
	}
	function is_readable($file){
		//Get dir list, Check if the file is writable by the current user??
		return true;
	}
	function is_writable($file){
		//Get dir list, Check if the file is writable by the current user??
		return true;
	}
	function atime($file){
		return false;
	}
	function mtime($file){
		return ftp_mdtm($this->link, $file);
	}
	function size($file){
		return ftp_size($this->link, $file);
	}
	function touch($file,$time=0,$atime=0){
		return false;
	}
	function mkdir($path,$chmod=false,$chown=false,$chgrp=false){
		if( !ftp_mkdir($this->link, $path) )
			return false;
		if( $chmod )
			$this->chmod($chmod);
		if( $chown )
			$this->chown($chown);
		if( $chgrp )
			$this->chgrp($chgrp);
		return true;
	}
	function rmdir($path,$recursive=false){
		if( ! $recursive )
			return ftp_rmdir($this->link, $file);
		
		//TODO: Recursive Directory delete, Have to delete files from the folder first.
		//$dir = $this->dirlist($path);
		//foreach($dir as $file)
			
	}
	function dirlist($path='.',$incdot=false,$recursive=false){
		$list = ftp_rawlist($this->link,'-a '.$path,false); //We'll do the recursive part ourseves...
		if($list == false)
			return false;
		$ret = array();
		foreach($list as $line){
			$struc = array();
			$current = preg_split("/[\s]+/",$line,9);
			$struc['perms']    	= $current[0];
			$struc['permsn']	= $this->getnumchmodfromh($current[0]);
			$struc['number']	= $current[1];
			$struc['owner']    	= $current[2];
			$struc['group']    	= $current[3];
			$struc['size']    	= $current[4];
			$struc['lastmod']   = $current[5].' '.$current[6];
			$struc['time']    	= $current[7];
			$struc['name']    	= str_replace('//','',$current[8]);
			$struc['type']		= ('d' == substr($struc['perms'], 0, 1) || 'l' == substr($struc['perms'], 0, 1) ) ? 'folder' : 'file';
			if('folder' == $struc['type'] ){
				if( '.' == $struc['name'] || '..' == $struc['name']){
					//Dots
					if($incdot) {
						$struc['files'] = array();
						$ret[$struc['name']] = $struc;
					}
				} else {
					//No dots
					if ($recursive){
						$struc['files'] = $this->dirlist($path.'/'.$struc['name'],$incdot,$recursive);
					} else {
						$struc['files'] = array();
					}
					$ret[$struc['name']] = $struc;
				}
			} else {
				//File
				$ret[$struc['name']] = $struc;
			}
		}
		return $ret;
	}
}
?>