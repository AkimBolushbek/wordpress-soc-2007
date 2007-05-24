<?php
//TODO: - Split this into filesystem Direct; Filesystem PHP_FTP; Filesystem Socket_FTP; Fielsystem ...
//		- Determine the best layout of the file(ie. what functions WILL return what
//		- Perhaps $ = new WP_Filesystem('ftp'); .. if(extneions exists) return new WP_Filesystem_Ftp_ext
class WP_Filesystem{

	function WP_Filesystem($method='',$arg=''){
		$method = $this->bestOption($method);
		if( ! $method ) return false;

		@require('wp-update-filesystem-'.$method.'-class.php');
		return new "WP_Filesystem_$method"($arg);
	}
	function bestOption($preference='direct'){
		//No Breaks here, we want to go through each item.
		switch($preference){
			default:
			case 'direct':
				//Likely suPHP
				if( getmyuid() == fileowner(__FILE__) ) return 'direct';
			case 'ftp':
				if( extension_loaded('ftp') ) return 'ftp';
			case 'ftpsocket':
				if( function_exists('socket_create') ) return 'ftpsocket';
		}
		if( getmyuid() == fileowner(__FILE__) ) return 'direct';
		if( extension_loaded('ftp')) return 'ftp';
		if( function_exists('socket_create') ) return 'ftpsocket';
		return false;
	}
	
}


class WP_Filesystem_Combination{
	var $method = 'direct';
	var $ftp = array('host'=>false,'post'=>21,'username'=>false,'password'=>false,'base'=>'./');
	var $link;
	
	function WP_Filesystem_Combination($method = false, $info=''){
		if($method) $this->method = $method;
		if('ftp' == $method && !function_exists('ftp_connect'))
			$this->method = 'direct';
		if('ftp' == $this->method){
			if(isset($info['host']))
				$this->ftp['host'] = $info['host'];
			if(isset($info['port']))
				$this->ftp['port'] = (int)$info['port'];
			if(isset($info['username']))
				$this->ftp['username'] = $info['username'];
			if(isset($info['password']))
				$this->ftp['password'] = $info['password'];
			if(isset($info['base']))
				$this->ftp['base'] = $info['base'];
			if( !$this->ftp['host'] || !$this->ftp['username'] || !$this->ftp['password'] ){
				$this->method = 'direct';
			} else {
				if( false !== ($this->link = ftp_connect($this->ftp['host'], $this->ftp['port']) ) && 
								ftp_login($this->link,$this->ftp['username'], $this->ftp['password'] ) ){
					$this->ftp['password'] = false;
					if( './' == $this->ftp['base'] ) $this->ftp['base'] = ftp_pwd($this->link);
					return;
				} else {
					$this->link = false;
					$this->ftp['password'] = false;
					$this->method = 'direct';
				}
			}
		}
			
	}
	function get_contents($file){
		if('direct' == $this->method){
			return @file_get_contents($file);
		} else {
			
		}
	}
	function get_contents_array($file){
		if('direct' == $this->method){
			return @file($file);
		} else {
		
		}
	}
	function put_contents($file,$contents,$mode=''){
		if('direct' == $this->method){
			$fp=@fopen($file,'w'.$mode);
			if (!$fp)
				return false;
			@fwrite($fp,$contents);
			@fclose($fp);
			return true;
		} else {
			
		}
	}
	
	function chgrp($file,$group,$recursive=false){
		if('direct' == $this->method){
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
		} else {
			return false;
		}
	}
	function chmod($file,$mode,$recursive=false){
		if('direct' == $this->method){
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
		} else {
			if( ! $this->exists($file) )
				return false;
			if( ! $recursive )
				return ftp_chmod($this->link,$mode,$file);
			if( ! $this->is_dir($file) ){
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
	}
	function chown($file,$owner,$recursive=false){
		if('direct' == $this->method){
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
		} else {
			return false;
		}
	}
	function owner($file){
		if('direct' == $this->method){
			$owneruid=@fileowner($file);
			if( ! $owneruid )
				return false;
			if( !function_exists('posix_getpwuid') )
				return $owneruid;
			$ownerarray=posix_getpwuid($owneruid); 
			return $ownerarray['name'];
		} else {
			
		}
	}
	function getchmod($file){
		if('direct' == $this->method){
			return @fileperms($file);
		} else {
			
		}
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
		if('direct' == $this->method){
			$gid=@filegroup($file);
			if( ! $gid )
				return false;
			if( !function_exists('posix_getgrgid') )
				return $gid;
			$grouparray=posix_getgrgid($gid); 
			return $grouparray['name'];
		} else {
		
		}
	}
	
	function copy($source,$destination,$overwrite=false){
		if('direct' == $this->method){
			if( $overwrite && $this->exists($destination) )
				return false;
			return copy($source,$destination);
		} else {
		
		}
	}
	function move($source,$destination,$overwrite=false){
		if('direct' == $this->method){
			if( $this->copy($source,$destination,$overwrite) && $this->exists($destination) ){
				$this->delete($source);
				return true;
			} else {
				return false;
			}
		} else {
			return ftp_rename($this->link,$source,$destination);
		}
	}
	function delete($file,$recursive=false){
		if('direct' == $this->method){
			if( $this->is_file($file) )
				return @unlink($file);
			if( !$recursive )
				return @rmdir($file);
			$filelist = $this->dir($file.'/');
			foreach($filelist as $filename){
				$this->delete($file.'/'.$filename,$recursive);
			}
		} else {
			if( $this->is_file($file) )
				return ftp_delete($this->link,$file);
			if( !$recursive )
				return ftp_rmdir($this->link,$file);
			$filelist = $this->dir($file.'/');
			foreach($filelist as $filename){
				$this->delete($file.'/'.$filename,$recursive);
			}
			
		}
	}
	
	function exists($file){
		if('direct' == $this->method){
			return file_exists($file);
		} else {
			
		}
	}
	function is_file($file){
		if('direct' == $this->method){
			return is_file($file);
		} else {
		
		}
	}
	function is_dir($path){
		if('direct' == $this->method){
			return is_dir($path);
		} else {
		
		}
	}
	function is_readable($file){
		if('direct' == $this->method){
			return is_readable($file);
		} else {
		
		}
	}
	function is_writable($file){
		if('direct' == $this->method){
			return is_writable($file);
		} else {
		
		}
	}
	
	function atime($file){
		if('direct' == $this->method){
			return fileatime($file);
		} else {
		
		}
	}
	function mtime($file){
		if('direct' == $this->method){
			return filemtime($file);
		} else {
		
		}
	}
	function size($file){
		if('direct' == $this->method){
			return filesize($file);
		} else {
		
		}
	}
	function touch($file,$time=0,$atime=0){
		if($time==0)
			$time = time();
		if($atime==0)
			$atime = time();
			
		if('direct' == $this->method){
			return touch($file,$time,$atime);
		} else {
		
		}
	}
	
	function mkdir($path,$chmod=false,$chown=false,$chgrp=false){
		if( false == $chmod)
			$chmod = umask();
		if('direct' == $this->method){
			if( !mkdir($path,$chmod) )
				return false;
			if( $chown )
				$this->chown($path,$chown);
			if( $chgrp )
				$this->chgrp($path,$chgrp);
			return true;
		} else {
		
		}
	}
	function rmdir($path,$recursive=false){
		if('direct' == $this->method){
			if( ! $recursive )
				return rmdir($path);
			//recursive:
			$filelist = $this->dirlist($path);
			foreach($filelist as $filename=>$det){
				if ( '/' == substr($filename,-1,1) )
					$this->rmdir($path.'/'.$filename,$recursive);
				rmdir($entry);
			}
			return rmdir($path);
		} else {
		
		}
	}
	
	function dirlist($path,$incdot=false,$recursive=false){
	/* $f array ( 
	*			'file' => array ($f,$f),
	*			'owner' => 'test',
	*			'group' => 'group',
	*			'permissions' => array( 'human' =>,'mode' => 0000 ),
	*			'filezise' => Bytes,
	*			'modified' => Date
	*/
		if('direct' == $this->method){
			$ret = array();
			$dir = dir($path);
			while (false !== ($entry = $dir->read())) {
				if( ! $incdot && ($entry == '.' || $entry == '..') )
					continue;
				$struc = array();
				$struc['perms'] 	= $this->gethchmod($path.'/'.$entry);
				$struc['permsn']	= $this->getnumchmodfromh($struc['perms']);
				$struc['number'] 	= false;
				$struc['owner']    	= $this->owner($path.'/'.$entry);
				$struc['group']    	= $this->group($path.'/'.$entry);
				$struc['size']    	= $this->size($path.'/'.$entry);
				$struc['lastmodunix']= $this->mtime($path.'/'.$entry);
				$struc['lastmod']   = date('M j',$struc['lastmodunix']);
				$struc['time']    	= date('h:i:s',$struc['lastmodunix']);
				$struc['name'] 		= $entry;
				$struc['type']		= $this->is_dir($path.'/'.$entry) ? 'folder' : 'file';
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
		} else {
			$list = ftp_rawlist($this->link,'-a '.$path,false); //We'll do the recursive part ourseves...
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
	/*function (){
		if('direct' == $this->method){
		
		} else {
		
		}
	}*/
	function __destruct(){
		if($this->link) 
			@ftp_close($this->link);
	}
}
?>