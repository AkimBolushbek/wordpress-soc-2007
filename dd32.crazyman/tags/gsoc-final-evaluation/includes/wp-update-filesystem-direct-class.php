<?php

class WP_Filesystem_Direct{
	var $permission = null;
	function WP_Filesystem_Direct($arg){
		$this->permission = umask();
	}
	function connect(){
		return;
	}
	function setDefaultPermissions($perm){
		$this->permission = $perm;
	}
	function find_base_dir($base = '.'){
		return str_replace('\\','/',ABSPATH);
	}
	function get_base_dir($base = '.'){
		return str_replace('\\','/',ABSPATH);
	}
	function get_contents($file){
		return @file_get_contents($file);
	}
	function get_contents_array($file){
		return @file($file);
	}
	function put_contents($file,$contents,$mode=false,$type=''){
		$fp=@fopen($file,'w'.$type);
		if (!$fp)
			return false;
		@fwrite($fp,$contents);
		@fclose($fp);
		$this->chmod($file,$mode);
		return true;
	}
	function cwd(){
		return @getcwd();
	}
	function chgrp($file,$group,$recursive=false){
		if( ! $this->exists($file) )
			return false;
		if( ! $recursive )
			return @chgrp($file,$group);
		if( ! $this->is_dir($file) )
			return @chgrp($file,$group);
		//Is a directory, and we want recursive
		$filelist = $this->dirlist($file);
		foreach($filelist as $filename){
			$this->chgrp($file.'/'.$filename,$group,$recursive);
		}
		return true;
	}
	function chmod($file,$mode=false,$recursive=false){
		if( ! $mode )
			$mode = $this->permission;
		if( ! $this->exists($file) )
			return false;
		if( ! $recursive )
			return @chmod($file,$mode);
		if( ! $this->is_dir($file) )
			return @chmod($file,$mode);
		//Is a directory, and we want recursive
		$filelist = $this->dirlist($file);
		foreach($filelist as $filename){
			$this->chmod($file.'/'.$filename,$mode,$recursive);
		}
		return true;
	}
	function chown($file,$owner,$recursive=false){
		if( ! $this->exists($file) )
			return false;
		if( ! $recursive )
			return @chown($file,$owner);
		if( ! $this->is_dir($file) )
			return @chown($file,$owner);
		//Is a directory, and we want recursive
		$filelist = $this->dirlist($file);
		foreach($filelist as $filename){
			$this->chown($file.'/'.$filename,$owner,$recursive);
		}
		return true;
	}
	function owner($file){
		$owneruid=@fileowner($file);
		if( ! $owneruid )
			return false;
		if( !function_exists('posix_getpwuid') )
			return $owneruid;
		$ownerarray=posix_getpwuid($owneruid); 
		return $ownerarray['name'];
	}
	function getchmod($file){
		return @fileperms($file);
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
		$gid=@filegroup($file);
		if( ! $gid )
			return false;
		if( !function_exists('posix_getgrgid') )
			return $gid;
		$grouparray=posix_getgrgid($gid); 
		return $grouparray['name'];
	}
	
	function copy($source,$destination,$overwrite=false){
		if( $overwrite && $this->exists($destination) )
			return false;
		return copy($source,$destination);
	}
	function move($source,$destination,$overwrite=false){
		//Possible to use rename()
		if( $this->copy($source,$destination,$overwrite) && $this->exists($destination) ){
			$this->delete($source);
			return true;
		} else {
			return false;
		}
	}
	function delete($file,$recursive=false){
		$file = str_replace('\\','/',$file); //for win32, occasional problems deleteing files otherwise
		if( $this->is_file($file) )
			return @unlink($file);
		if( !$recursive )
			return @rmdir($file);
		$filelist = $this->dirlist($file);

		$reval = true;
		foreach($filelist as $filename=>$fileinfo){
			if( ! $this->delete($file.'/'.$filename,$recursive) )
				$retval = false;
		}
		if( ! @rmdir($file) )
			return false;
		return $retval;
	}
	
	function exists($file){
		return @file_exists($file);
	}
	function is_file($file){
		return @is_file($file);
	}
	function is_dir($path){
		return @is_dir($path);
	}
	function is_readable($file){
			return @is_readable($file);
	}
	function is_writable($file){
		return @is_writable($file);
	}
	
	function atime($file){
		return @fileatime($file);
	}
	function mtime($file){
		return @filemtime($file);
	}
	function size($file){
		return @filesize($file);
	}
	function touch($file,$time=0,$atime=0){
		if($time==0)
			$time = time();
		if($atime==0)
			$atime = time();
		return @touch($file,$time,$atime);
	}
	
	function mkdir($path,$chmod=false,$chown=false,$chgrp=false){
		if( ! $chmod)
			$chmod = $this->permission;
			
		if( !@mkdir($path,$chmod) )
			return false;
		if( $chown )
			$this->chown($path,$chown);
		if( $chgrp )
			$this->chgrp($path,$chgrp);
		return true;
	}
	function rmdir($path,$recursive=false){
		if( ! $recursive )
			return @rmdir($path);
		//recursive:
		$filelist = $this->dirlist($path);
		foreach($filelist as $filename=>$det){
			if ( '/' == substr($filename,-1,1) )
				$this->rmdir($path.'/'.$filename,$recursive);
			@rmdir($entry);
		}
		return @rmdir($path);
	}
	
	function dirlist($path,$incdot=false,$recursive=false){
		if( $this->is_file($path) ){
			$limitFile = basename($path);
			$path = dirname($path);
		} else {
			$limitFile = false;
		}
		if( ! $this->is_dir($path) )
			return false;

		$ret = array();
		$dir = dir($path);
		while (false !== ($entry = $dir->read())) {
			$struc = array();
			$struc['name'] 		= $entry;
			
			if( '.' == $struc['name'][0] && !$incdot)
				continue;
			if( $limitFile && $struc['name'] != $limitFile)
				continue;
			
			$struc['perms'] 	= $this->gethchmod($path.'/'.$entry);
			$struc['permsn']	= $this->getnumchmodfromh($struc['perms']);
			$struc['number'] 	= false;
			$struc['owner']    	= $this->owner($path.'/'.$entry);
			$struc['group']    	= $this->group($path.'/'.$entry);
			$struc['size']    	= $this->size($path.'/'.$entry);
			$struc['lastmodunix']= $this->mtime($path.'/'.$entry);
			$struc['lastmod']   = date('M j',$struc['lastmodunix']);
			$struc['time']    	= date('h:i:s',$struc['lastmodunix']);
			$struc['type']		= $this->is_dir($path.'/'.$entry) ? 'folder' : 'file';
			if('folder' == $struc['type'] ){
				$struc['files'] = array();
				
				if( $incdot ){
					//We're including the doted starts
					if( '.' != $struc['name'] && '..' != $struc['name'] ){ //Ok, It isnt a special folder
						if ($recursive)
							$struc['files'] = $this->dirlist($path.'/'.$struc['name'],$incdot,$recursive);
					}
				} else { //No dots
					if ($recursive)
						$struc['files'] = $this->dirlist($path.'/'.$struc['name'],$incdot,$recursive);
				}
			}
			//File
			$ret[$struc['name']] = $struc;
		}
		$dir->close();
		unset($dir);
		return $ret;
	}
	function __destruct(){
		return;
	}
}
?>