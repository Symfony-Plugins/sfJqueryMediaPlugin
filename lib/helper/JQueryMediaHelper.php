<?php

/*
 *      This program is free software; you can redistribute it and/or modify
 *      it under the terms of the GNU General Public License as published by
 *      the Free Software Foundation; either version 2 of the License, or
 *      (at your option) any later version.
 *
 *      This program is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *      GNU General Public License for more details.
 *
 *      You should have received a copy of the GNU General Public License
 *      along with this program; if not, write to the Free Software
 *      Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 *      MA 02110-1301, USA.
 */



/**
 * The jQuery Media Plugin supports unobtrusive conversion of standard markup into rich media content.
 * It can be used to embed virtually any media type, including Flash, Quicktime, Windows Media Player, Real Player,
 * MP3, Silverlight, PDF and more, into a web page. The plugin converts an element (usually an <a>)
 * into a <div> which holds the object, embed or iframe tags neccessary to render the media content.
 *
 * @author Yoda-BZH, yodabzh@gmail.com
 * @param $pId the id of the link
 * @param $pOptions an array of options
 * @param $pHtmlAttributes unused
 * @return javascript code
 */
function jq_media($pId, array $pOptions = array(), array $pHtmlAttributes = array()) {
    /**
     * adding javascript files to the headers
     */
    $response = sfContext::getInstance()->getResponse();
    $response->addJavascript('/sfJqueryMediaPlugin/js/jquery.media.js');
    /**
     * apparently jquery media doesn't support on-the-fly by-option player change ...
     */
    $preferences = '';
    foreach(array('mp3Player', 'flvPlayer') as $player) {
        if(array_key_exists($player, $pOptions)) {
            $preferences .= '    $.fn.media.defaults.'.$player.' = "'.$pOptions[$player].'"'."\n";
            unset($pOptions[$player]);
        }
    }
    /**
     * sort the options by *key* name
     */
    ksort($pOptions);
    $opts = jq_media_compute_options($pOptions);
    $code = '
jQuery(document).ready(function() {
'.$preferences.'
    jQuery("#'.$pId.'").media({
        '.$opts.'
    });
});
';
    return javascript_tag($code);
}

/**
 * return a string for a well formated json array
 * @param $pOptions
 * @return a string with the options
 * @author Yoda-BZH, yodabzh@gmail.com
 */
function jq_media_compute_options($pOptions) {
    /**
     * compiling js options
     */
    $opts = array();
    foreach($pOptions as $k => $v) {
        switch(gettype($v)) {
            case 'int' :
            case 'integer' :
            case 'float' :
            case 'numeric' :
                $opts[] = $k.': '.$v;
                break;
            case 'string' :
                $opts[] = $k.': "'.$v.'"';
                break;
            case 'bool' :
            case 'boolean' :
                $opts[] = $k.': '.($v ? 'true' : 'false');
                break;
            case 'NULL' :
                echo 'skipping '.$v;
                // skip it
                break;
            case 'array' :
                $opts[] = $k.': {
            '.jq_media_compute_options($v).'
        }';
                break;
            default :
                echo 'cannot handle '.gettype($v);
                break; // don't handle objects, resources, ...
        }
    }
    return implode(",\n        ", $opts);
}
