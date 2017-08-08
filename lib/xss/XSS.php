<?php
/**
 * Windwork
 * 
 * 一个用于快速开发高并发Web应用的轻量级PHP框架
 * 
 * @copyright Copyright (c) 2008-2017 Windwork Team. (http://www.windwork.org)
 * @license   http://opensource.org/licenses/MIT
 */
namespace wf\util\xss;

/**
 * XSS过滤
 *
 * @package     wf.util
 * @author      cm <cmpan@qq.com>
 * @since       0.1.0
 */
class XSS {
	
	/**
	 * 过滤掉不在允许范围内的标签
	 * @param string $string
	 * @return string
	 */
	public static function strip($string) {
		$allowPostTags = array(
			'address' => array(),
			'a' => array(
				'class' => true,
				'href' => true,
				'id' => true,
				'title' => true,
				'rel' => true,
				'rev' => true,
				'name' => true,
				'target' => true,
			),
			'abbr' => array(
				'class' => true,
				'title' => true,
			),
			'acronym' => array(
				'title' => true,
			),
			'article' => array(
				'align' => true,
				'class' => true,
				'dir' => true,
				'lang' => true,
				'style' => true,
				'xml:lang' => true,
			),
			'aside' => array(
				'align' => true,
				'class' => true,
				'dir' => true,
				'lang' => true,
				'style' => true,
				'xml:lang' => true,
			),
			'b' => array(),
			'big' => array(),
			'blockquote' => array(
				'id' => true,
				'cite' => true,
				'class' => true,
				'lang' => true,
				'xml:lang' => true,
			),
			'br' => array (
				'class' => true,
			),
			'button' => array(
				'disabled' => true,
				'name' => true,
				'type' => true,
				'value' => true,
			),
			'caption' => array(
				'align' => true,
				'class' => true,
			),
			'cite' => array (
				'class' => true,
				'dir' => true,
				'lang' => true,
				'title' => true,
			),
			'code' => array (
				'style' => true,
			),
			'col' => array(
				'align' => true,
				'char' => true,
				'charoff' => true,
				'span' => true,
				'dir' => true,
				'style' => true,
				'valign' => true,
				'width' => true,
			),
			'del' => array(
				'datetime' => true,
			),
			'dd' => array(),
			'details' => array(
				'align' => true,
				'class' => true,
				'dir' => true,
				'lang' => true,
				'open' => true,
				'style' => true,
				'xml:lang' => true,
			),
			'div' => array(
				'align' => true,
				'class' => true,
				'dir' => true,
				'lang' => true,
				'style' => true,
				'xml:lang' => true,
			),
			'dl' => array(),
			'dt' => array(),
			'em' => array(),
			'fieldset' => array(),
			'figure' => array(
				'align' => true,
				'class' => true,
				'dir' => true,
				'lang' => true,
				'style' => true,
				'xml:lang' => true,
			),
			'figcaption' => array(
				'align' => true,
				'class' => true,
				'dir' => true,
				'lang' => true,
				'style' => true,
				'xml:lang' => true,
			),
			'font' => array(
				'color' => true,
				'face' => true,
				'size' => true,
			),
			'footer' => array(
				'align' => true,
				'class' => true,
				'dir' => true,
				'lang' => true,
				'style' => true,
				'xml:lang' => true,
			),
			'form' => array(
				'action' => true,
				'accept' => true,
				'accept-charset' => true,
				'enctype' => true,
				'method' => true,
				'name' => true,
				'target' => true,
			),
			'h1' => array(
				'align' => true,
				'class' => true,
				'id'    => true,
				'style' => true,
			),
			'h2' => array (
				'align' => true,
				'class' => true,
				'id'    => true,
				'style' => true,
			),
			'h3' => array (
				'align' => true,
				'class' => true,
				'id'    => true,
				'style' => true,
			),
			'h4' => array (
				'align' => true,
				'class' => true,
				'id'    => true,
				'style' => true,
			),
			'h5' => array (
				'align' => true,
				'class' => true,
				'id'    => true,
				'style' => true,
			),
			'h6' => array (
				'align' => true,
				'class' => true,
				'id'    => true,
				'style' => true,
			),
			'header' => array(
				'align' => true,
				'class' => true,
				'dir' => true,
				'lang' => true,
				'style' => true,
				'xml:lang' => true,
			),
			'hgroup' => array(
				'align' => true,
				'class' => true,
				'dir' => true,
				'lang' => true,
				'style' => true,
				'xml:lang' => true,
			),
			'hr' => array (
				'align' => true,
				'class' => true,
				'noshade' => true,
				'size' => true,
				'width' => true,
			),
			'i' => array(),
			'img' => array(
				'alt' => true,
				'align' => true,
				'border' => true,
				'class' => true,
				'height' => true,
				'hspace' => true,
				'longdesc' => true,
				'vspace' => true,
				'src' => true,
				'style' => true,
				'width' => true,
			),
			'ins' => array(
				'datetime' => true,
				'cite' => true,
			),
			'kbd' => array(),
			'label' => array(
				'for' => true,
			),
			'legend' => array(
				'align' => true,
			),
			'li' => array (
				'align' => true,
				'class' => true,
			),
			'menu' => array (
				'class' => true,
				'style' => true,
				'type' => true,
			),
			'nav' => array(
				'align' => true,
				'class' => true,
				'dir' => true,
				'lang' => true,
				'style' => true,
				'xml:lang' => true,
			),
			'p' => array(
				'class' => true,
				'align' => true,
				'dir' => true,
				'lang' => true,
				'style' => true,
				'xml:lang' => true,
			),
			'pre' => array(
				'style' => true,
				'width' => true,
			),
			'q' => array(
				'cite' => true,
			),
			's' => array(),
			'span' => array (
				'class' => true,
				'dir' => true,
				'align' => true,
				'lang' => true,
				'style' => true,
				'title' => true,
				'xml:lang' => true,
			),
			'section' => array(
				'align' => true,
				'class' => true,
				'dir' => true,
				'lang' => true,
				'style' => true,
				'xml:lang' => true,
			),
			'strike' => array(),
			'strong' => array(),
			'sub' => array(),
			'summary' => array(
				'align' => true,
				'class' => true,
				'dir' => true,
				'lang' => true,
				'style' => true,
				'xml:lang' => true,
			),
			'sup' => array(),
			'table' => array(
				'align' => true,
				'bgcolor' => true,
				'border' => true,
				'cellpadding' => true,
				'cellspacing' => true,
				'class' => true,
				'dir' => true,
				'id' => true,
				'rules' => true,
				'style' => true,
				'summary' => true,
				'width' => true,
			),
			'tbody' => array(
				'align' => true,
				'char' => true,
				'charoff' => true,
				'valign' => true,
			),
			'td' => array(
				'abbr' => true,
				'align' => true,
				'axis' => true,
				'bgcolor' => true,
				'char' => true,
				'charoff' => true,
				'class' => true,
				'colspan' => true,
				'dir' => true,
				'headers' => true,
				'height' => true,
				'nowrap' => true,
				'rowspan' => true,
				'scope' => true,
				'style' => true,
				'valign' => true,
				'width' => true,
			),
			'textarea' => array(
				'cols' => true,
				'rows' => true,
				'disabled' => true,
				'name' => true,
				'readonly' => true,
			),
			'tfoot' => array(
				'align' => true,
				'char' => true,
				'class' => true,
				'charoff' => true,
				'valign' => true,
			),
			'th' => array(
				'abbr' => true,
				'align' => true,
				'axis' => true,
				'bgcolor' => true,
				'char' => true,
				'charoff' => true,
				'class' => true,
				'colspan' => true,
				'headers' => true,
				'height' => true,
				'nowrap' => true,
				'rowspan' => true,
				'scope' => true,
				'valign' => true,
				'width' => true,
			),
			'thead' => array(
				'align' => true,
				'char' => true,
				'charoff' => true,
				'class' => true,
				'valign' => true,
			),
			'title' => array(),
			'tr' => array(
				'align' => true,
				'bgcolor' => true,
				'char' => true,
				'charoff' => true,
				'class' => true,
				'style' => true,
				'valign' => true,
			),
			'tt' => array(),
			'u' => array(),
			'ul' => array (
				'class' => true,
				'style' => true,
				'type' => true,
			),
			'ol' => array (
				'class' => true,
				'start' => true,
				'style' => true,
				'type' => true,
			),
			'var' => array(),
		);
	
		include_once __DIR__ . '/kses.php';
		
		return kses($string, $allowPostTags);		
	}
}