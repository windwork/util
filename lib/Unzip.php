<?php
/**
 * Windwork
 * 
 * 一个用于快速开发高并发Web应用的轻量级PHP框架
 * 
 * @copyright Copyright (c) 2008-2017 Windwork Team. (http://www.windwork.org)
 * @license   http://opensource.org/licenses/MIT
 */
namespace wf\util;

/**
 * 解压zip文件
 *
 * @package     wf.util
 * @author      来自网络，作者未知
 * @since       0.1.0
 */
class Unzip {
	public $Comment = '';

	public $Entries = array();

	public $Name = '';

	public $Size = 0;

	public $Time = 0;

	public function __construct($inFileName = '') {
		if($inFileName !== '') {
			$this->ReadFile($inFileName);
		}
	}

	function Count() {
		return count($this->Entries);
	}

	function GetData($inIndex) {
		return $this->Entries[$inIndex]->Data;
	}

	function GetEntry($inIndex) {
		return $this->Entries[$inIndex];
	}

	function GetError($inIndex) {
		return $this->Entries[$inIndex]->Error;
	}

	function GetErrorMsg($inIndex) {
		return $this->Entries[$inIndex]->ErrorMsg;
	}

	function GetName($inIndex) {
		return $this->Entries[$inIndex]->Name;
	}

	function GetPath($inIndex) {
		return $this->Entries[$inIndex]->Path;
	}

	function GetTime($inIndex) {
		return $this->Entries[$inIndex]->Time;
	}

	function ReadFile($inFileName) {
		$this->Entries = array();

		$this->Name = $inFileName;
		$this->Time = filemtime($inFileName);
		$this->Size = filesize($inFileName);

		$oF = fopen($inFileName, 'rb');
		$vZ = fread($oF, $this->Size);
		fclose($oF);

		$aE = explode("\x50\x4b\x05\x06", $vZ);


		$aP = unpack('x16/v1CL', $aE[1]);
		$this->Comment = substr($aE[1], 18, $aP['CL']);

		$this->Comment = strtr($this->Comment, array("\r\n" => "\n",
				"\r"   => "\n"));

		$aE = explode("\x50\x4b\x01\x02", $vZ);
		$aE = explode("\x50\x4b\x03\x04", $aE[0]);
		array_shift($aE);

		foreach($aE as $vZ) {
			$aI = array();
			$aI['E']  = 0;
			$aI['EM'] = '';
			$aP = unpack('v1VN/v1GPF/v1CM/v1FT/v1FD/V1CRC/V1CS/V1UCS/v1FNL', $vZ);
			$bE = ($aP['GPF'] && 0x0001) ? TRUE : FALSE;
			$nF = $aP['FNL'];

			if($aP['GPF'] & 0x0008) {
				$aP1 = unpack('V1CRC/V1CS/V1UCS', substr($vZ, -12));

				$aP['CRC'] = $aP1['CRC'];
				$aP['CS']  = $aP1['CS'];
				$aP['UCS'] = $aP1['UCS'];

				$vZ = substr($vZ, 0, -12);
			}

			$aI['N'] = substr($vZ, 26, $nF);

			if(substr($aI['N'], -1) == '/') {
				continue;
			}

			$aI['P'] = dirname($aI['N']);
			$aI['P'] = $aI['P'] == '.' ? '' : $aI['P'];
			$aI['N'] = basename($aI['N']);

			$vZ = substr($vZ, 26 + $nF);

			if(strlen($vZ) != $aP['CS']) {
				$aI['E']  = 1;
				$aI['EM'] = 'Compressed size is not equal with the value in header information.';
			} else {
				if($bE) {
					$aI['E']  = 5;
					$aI['EM'] = 'File is encrypted, which is not supported from this class.';
				} else {
					switch($aP['CM']) {
						case 0: // Stored
							break;

						case 8: // Deflated
							$vZ = gzinflate($vZ);
							break;

						case 12: // BZIP2
							if(! extension_loaded('bz2')) {
								if(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
									@dl('php_bz2.dll');
								} else {
									@dl('bz2.so');
								}
							}

							if(extension_loaded('bz2')) {
								$vZ = bzdecompress($vZ);
							} else {
								$aI['E']  = 7;
								$aI['EM'] = "PHP BZIP2 extension not available.";
							}

							break;

						default:
							$aI['E']  = 6;
							$aI['EM'] = "De-/Compression method {$aP['CM']} is not supported.";
					}

					if(! $aI['E']) {
						if($vZ === FALSE) {
							$aI['E']  = 2;
							$aI['EM'] = 'Decompression of data failed.';
						} else {
							if(strlen($vZ) != $aP['UCS']) {
								$aI['E']  = 3;
								$aI['EM'] = 'Uncompressed size is not equal with the value in header information.';
							} else {
								if(crc32($vZ) != $aP['CRC']) {
									$aI['E']  = 4;
									$aI['EM'] = 'CRC32 checksum is not equal with the value in header information.';
								}
							}
						}
					}
				}
			}

			$aI['D'] = $vZ;

			$aI['T'] = mktime(($aP['FT']  & 0xf800) >> 11,
					($aP['FT']  & 0x07e0) >>  5,
					($aP['FT']  & 0x001f) <<  1,
					($aP['FD']  & 0x01e0) >>  5,
					($aP['FD']  & 0x001f),
					(($aP['FD'] & 0xfe00) >>  9) + 1980);

			$this->Entries[] = new SimpleUnzipEntry($aI);
		}

		return $this->Entries;
	}
}

/**
 * zip解压实体
 *
 * @package     wf.util
 * @author      cm <cmpan@qq.com>
 * @since       0.1.0
 */
class SimpleUnzipEntry {
	public $Data = '';

	public $Error = 0;

	public $ErrorMsg = '';

	public $Name = '';

	public $Path = '';

	public $Time = 0;

	public function __construct($inEntry) {
		$this->Data     = $inEntry['D'];
		$this->Error    = $inEntry['E'];
		$this->ErrorMsg = $inEntry['EM'];
		$this->Name     = $inEntry['N'];
		$this->Path     = $inEntry['P'];
		$this->Time     = $inEntry['T'];
	}
}
