<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright Copyright (c) 2008-2017 Windwork Team. (http://www.windwork.org)
 * @license   http://opensource.org/licenses/MIT
 */
namespace wf\util;

/**
 * Zip压缩打包
 * 
 * @package     wf.util
 * @author      来自网络，作者未知
 * @since       0.1.0
 */
class Zip {
	var $datasec      = array();
	var $ctrl_dir     = array();
	var $eof_ctrl_dir = "\x50\x4b\x05\x06\x00\x00\x00\x00";
	var $old_offset   = 0;

	/**
	 * Unix时间戳转成datetime格式
	 *
	 * @param int $timestamp
	 */
	private function _unix2DosTime($timestamp = 0) {
		$timearray = ($timestamp == 0) ? getdate() : getdate($timestamp);

		if($timearray['year'] < 1980) {
			$timearray['year']    = 1980;
			$timearray['mon']     = 1;
			$timearray['mday']    = 1;
			$timearray['hours']   = 0;
			$timearray['minutes'] = 0;
			$timearray['seconds'] = 0;
		} // end if

		return (($timearray['year'] - 1980) << 25) | ($timearray['mon'] << 21) | ($timearray['mday'] << 16) |
		($timearray['hours'] << 11) | ($timearray['minutes'] << 5) | ($timearray['seconds'] >> 1);
	}

	/**
	 * 添加内容作为一个文件到压缩包中
	 *
	 * @param string $data
	 * @param string $name
	 * @param int $time
	 */
	public function addFile($data, $name, $time = 0) {
		$name     = str_replace('\\', '/', $name);

		$dtime    = dechex($this->_unix2DosTime($time));
		$hexdtime = '\x' . $dtime[6] . $dtime[7]
		. '\x' . $dtime[4] . $dtime[5]
		. '\x' . $dtime[2] . $dtime[3]
		. '\x' . $dtime[0] . $dtime[1];
		eval('$hexdtime = "' . $hexdtime . '";');

		$fr   = "\x50\x4b\x03\x04";
		$fr   .= "\x14\x00";            // ver needed to extract
		$fr   .= "\x00\x00";            // gen purpose bit flag
		$fr   .= "\x08\x00";            // compression method
		$fr   .= $hexdtime;             // last mod time and date

		$unc_len = strlen($data);
		$crc     = crc32($data);
		$zdata   = gzcompress($data);
		$zdata   = substr(substr($zdata, 0, strlen($zdata) - 4), 2); // fix crc bug
		$c_len   = strlen($zdata);
		$fr      .= pack('V', $crc);             // crc32
		$fr      .= pack('V', $c_len);           // compressed filesize
		$fr      .= pack('V', $unc_len);         // uncompressed filesize
		$fr      .= pack('v', strlen($name));    // length of filename
		$fr      .= pack('v', 0);                // extra field length
		$fr      .= $name;

		$fr .= $zdata;


		$this -> datasec[] = $fr;

		$cdrec = "\x50\x4b\x01\x02";
		$cdrec .= "\x00\x00";                // version made by
		$cdrec .= "\x14\x00";                // version needed to extract
		$cdrec .= "\x00\x00";                // gen purpose bit flag
		$cdrec .= "\x08\x00";                // compression method
		$cdrec .= $hexdtime;                 // last mod time & date
		$cdrec .= pack('V', $crc);           // crc32
		$cdrec .= pack('V', $c_len);         // compressed filesize
		$cdrec .= pack('V', $unc_len);       // uncompressed filesize
		$cdrec .= pack('v', strlen($name) ); // length of filename
		$cdrec .= pack('v', 0 );             // extra field length
		$cdrec .= pack('v', 0 );             // file comment length
		$cdrec .= pack('v', 0 );             // disk number start
		$cdrec .= pack('v', 0 );             // internal file attributes
		$cdrec .= pack('V', 32 );            // external file attributes - 'archive' bit set

		$cdrec .= pack('V', $this -> old_offset ); // relative offset of local header
		$this -> old_offset += strlen($fr);

		$cdrec .= $name;

		$this -> ctrl_dir[] = $cdrec;
	}

	/**
	 * 压缩打包
	 * @return string
	 */
	function file() {
		$data    = implode('', $this->datasec);
		$ctrldir = implode('', $this->ctrl_dir);

		return $data . 
		$ctrldir . $this->eof_ctrl_dir .
		pack('v', sizeof($this->ctrl_dir)) .    // total # of entries "on this disk"
		pack('v', sizeof($this->ctrl_dir)) .    // total # of entries overall
		pack('V', strlen($ctrldir)) .           // size of central dir
		pack('V', strlen($data)) .              // offset to start of central dir
		"\x00\x00";                             // .zip file comment length
	}

}

