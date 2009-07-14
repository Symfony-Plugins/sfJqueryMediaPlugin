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




function jq_media($pId, array $pOptions = array(), array $pHtmlAttributes = array()) {
    $response = sfContext::getInstance()->getResponse();
    $response->addJavascript('/sfJqueryMediaPlugin/js/jquery.media.js');
    $opts = jq_media_compute_options($pOptions);
    $code = '
jQuery(document).ready(function() {
    jQuery("#'.$pId.'").media({
        '.$opts.'
    });
});
';
    return javascript_tag($code);
}


function jq_media_compute_options($pOptions) {
    /**
     * compiling js options
     */
    $opts = array();
    foreach($pOptions as $k => $v) {
        //if($k == 'height' || $k == 'width') {
        //    $v .= 'px';
        //}
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
