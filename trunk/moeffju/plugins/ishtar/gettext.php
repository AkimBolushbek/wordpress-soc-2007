<?php

function _po_clean_helper($x) {
	if (is_array($x)) {
		foreach ($x as $k => $v) {
			$x[$k]= _po_clean_helper($v);
		}
	} else {
		if ($x[0] == '"')
			$x= substr($x, 1, -1);
		$x= str_replace("\"\n\"", '', $x);
		$x= str_replace('$', '\\$', $x);
		$x= @ eval ("return \"$x\";");
	}
	return $x;
}

/* Parse gettext .po files. */
/* @link http://www.gnu.org/software/gettext/manual/gettext.html#PO-Files */
function parse_po_file($in) {
	// read .po file
	$fc= file_get_contents($in);
	// normalize newlines
	$fc= str_replace(array (
		"\r\n",
		"\r"
	), array (
		"\n",
		"\n"
	), $fc);

	// results array
	$hash= array ();
	// temporary array
	$temp= array ();
	// state
	$state= null;
	$fuzzy= false;

	// iterate over lines
	foreach (explode("\n", $fc) as $line) {
		$line= trim($line);
		if ($line === '') {
			// start a new entry
			if (sizeof($temp) && array_key_exists('msgid', $temp) && array_key_exists('msgstr', $temp)) {
        if ($fuzzy) $temp['flags']['fuzzy']= true;
				//if (!$fuzzy)
					$hash[]= $temp;
				$temp= array ();
				$state= null;
				$fuzzy= false;
			}
			continue;
		}
    
    $key = $data = '';
		list ($key, $data)= explode(' ', $line, 2);

		switch ($key) {
			case '#,' : // flag...
				$fuzzy= in_array('fuzzy', preg_split('/,\s*/', $data));
			case '#' : // translator-comments
			case '#.' : // extracted-comments
			case '#|' : // msgid previous-untranslated-string
				break;
			case '#:' : // reference...
        $temp['reference']= $data;
        break;
			case 'msgctxt' :
				// context
			case 'msgid' :
				// untranslated-string
			case 'msgid_plural' :
				// untranslated-string-plural
				$state= $key;
				$temp[$state]= $data;
				break;
			case 'msgstr' :
				// translated-string
				$state= 'msgstr';
				$temp[$state][]= $data;
				break;
			default :
				if (strpos($key, 'msgstr[') === 0) {
					// translated-string-case-n
					$state= 'msgstr';
					$temp[$state][]= $data;
				} else {
					// continued lines
					switch ($state) {
						case 'msgctxt' :
						case 'msgid' :
						case 'msgid_plural' :
							$temp[$state] .= "\n" . $line;
							break;
						case 'msgstr' :
							$temp[$state][sizeof($temp[$state]) - 1] .= "\n" . $line;
							break;
						default :
							// parse error
							return FALSE;
					}
				}
				break;
		}
	}

	// add final entry
	if ($state == 'msgstr')
		$hash[]= $temp;

	// Cleanup data, merge multiline entries, reindex hash for ksort
	$temp= $hash;
	$hash= array ();
	foreach ($temp as $entry) {
		foreach ($entry as & $v) {
			$v= _po_clean_helper($v);
			if ($v === FALSE) {
				// parse error
				return FALSE;
			}
		}
		$hash[$entry['msgid']]= $entry;
	}

	return $hash;
}

/* Write a GNU gettext style po file. */
function write_po_file($hash, $out) {
	ksort($hash, SORT_STRING);
	
	
	foreach ($hash as $entry) {
		
	}
}

/* Write a GNU gettext style machine object. */
/* @link http://www.gnu.org/software/gettext/manual/gettext.html#MO-Files */
function write_mo_file($hash, $out) {
	// sort by msgid
	ksort($hash, SORT_STRING);
	// our mo file data
	$mo= '';
	// header data
	$offsets= array ();
	$ids= '';
	$strings= '';

	foreach ($hash as $entry) {
		$id= $entry['msgid'];
		if (isset ($entry['msgid_plural']))
			$id .= "\x00" . $entry['msgid_plural'];
		// context is merged into id, separated by EOT (\x04)
		if (array_key_exists('msgctxt', $entry))
			$id= $entry['msgctxt'] . "\x04" . $id;
		// plural msgstrs are NUL-separated
		$str= implode("\x00", $entry['msgstr']);
		// keep track of offsets
		$offsets[]= array (
			strlen($ids
		), strlen($id), strlen($strings), strlen($str));
		// plural msgids are not stored (?)
		$ids .= $id . "\x00";
		$strings .= $str . "\x00";
	}

	// keys start after the header (7 words) + index tables ($#hash * 4 words)
	$key_start= 7 * 4 + sizeof($hash) * 4 * 4;
	// values start right after the keys
	$value_start= $key_start +strlen($ids);
	// first all key offsets, then all value offsets
	$key_offsets= array ();
	$value_offsets= array ();
	// calculate
	foreach ($offsets as $v) {
		list ($o1, $l1, $o2, $l2)= $v;
		$key_offsets[]= $l1;
		$key_offsets[]= $o1 + $key_start;
		$value_offsets[]= $l2;
		$value_offsets[]= $o2 + $value_start;
	}
	$offsets= array_merge($key_offsets, $value_offsets);

	// write header
	$mo .= pack('Iiiiiii', 0x950412de, // magic number
	0, // version
	sizeof($hash), // number of entries in the catalog
	7 * 4, // key index offset
	7 * 4 + sizeof($hash) * 8, // value index offset,
	0, // hashtable size (unused, thus 0)
	$key_start // hashtable offset
	);
	// offsets
	foreach ($offsets as $offset)
		$mo .= pack('i', $offset);
	// ids
	$mo .= $ids;
	// strings
	$mo .= $strings;

	file_put_contents($out, $mo);
}

?>