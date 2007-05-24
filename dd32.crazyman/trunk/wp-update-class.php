<?php
class WP_Update{

	function search($item='themes',$tags=array(),$output='array',$offset=0,$limit=15){
		if('themes' == $item){
			if(0 == count($tags)) return false; //Must specify search terms.
			if('array' == $output) return $this->searchThemes($tags,$offset,$limit);
			if('html' == $output){ //Format it for HTML output.
				$items = $this->searchThemes($tags,$offset,$limit);
				$ret = '';
				foreach($items as $theme){
					$ret .="&nbsp;<div class='themeinfo'><span>
							<a href='{$theme['url']}' title='{$theme['name']}' target='_blank'>{$theme['name']}<br />
							<img src='{$theme['snapshot']['thumb']}' alt='{$theme['name']} - Downloaded {$theme['downloadcount']} times' 
							title='{$theme['name']} - Downloaded {$theme['downloadcount']} times' /></a><br/>
							<a href='{$theme['testrun']}' target='_blank'>Test Run</a> | <a href='#' onclick='return false;' target='_blank'>Install</a>
						</span></div>\n";
				}
				return $ret;
			}//end html
			//Format for.. JSSI? XML?
			return;
		} elseif('plugins' == $item){
			
		}
	}
	function searchThemes($tags, $offset, $limit){
		require_once( ABSPATH . 'wp-includes/class-snoopy.php' );
		$urlpart = array();
		foreach($_POST as $postname=>$postvalue){
			if(is_array($postvalue)){
				foreach($postvalue as $subpostvalue)
					$urlpart[] = urlencode($postname.'[]').'='.urlencode($subpostvalue);
			} else {
				$urlpart[] = urlencode($postname).'='.urlencode($postvalue);
			}
		}
		$urlpart = implode('&',$urlpart);
		$snoopy = new Snoopy();
		$snoopy->fetch('http://themes.wordpress.net/?'.$urlpart);
		return $this->parseThemeHTML($snoopy->results);
	}
	function parseThemeHTML($html){
		preg_match_all('#<a href="(.*?)" rel="bookmark" title="(.*?)"><img src="/snapshots/(\d+?)-thumb.jpg" alt="\2" /></a>#i',$html,$mat1);
		preg_match_all('#Download \((\d+)\)#i',$html,$mat2);
		$ret = array();
		
		$totalItems = count($mat2[1]);
		for($i=0;$i<$totalItems;$i++){
			$id = $mat1[3][$i];
			$ret[] = array(
							'name'=>$mat1[2][$i],
							'url'=>$mat1[1][$i],
							'id'=>$id,
							'download'=>'http://themes.wordpress.net/download.php?theme='.$id,
							'downloadcount'=>$mat2[1][$i],
							'snapshot'=> array(
										'thumb'=>'http://themes.wordpress.net/snapshots/'.$id.'-thumb.jpg',
										'medium'=>'http://themes.wordpress.net/snapshots/'.$id.'-medium.jpg',
										'big'=>'http://themes.wordpress.net/snapshots/'.$id.'-big.jpg'
										),
							'testrun'=>'http://themes.wordpress.net/testrun/?wptheme='.$id
							);
		}
		return $ret;
	}
}
?>