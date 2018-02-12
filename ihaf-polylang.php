<?php

/*
Plugin Name: Insert Headers and Footers - Polylang integration
Plugin URI: 
Description: 
Version: 1.0.0
Author: marale
Author URI: mailto://marek@e-kreatywnie.pl
License: GPLv2
*/

/* 
Copyright (C) 2018 marale

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/

defined( 'ABSPATH' ) or exit; // don't access directly

add_action( 'plugins_loaded', 
    function() {
    
        global $ihaf;

        if ( defined( 'POLYLANG_BASENAME' ) && is_a( $ihaf, 'InsertHeadersAndFooters' ) ) {
            $class = apply_filters( 'marale_ihaf_poly_class', MaraleIhafPoly::class );
            $class::add_hooks();
        }    
}, 11 );

abstract class MaraleIhafPoly {
    
    private static $instance;
    private static $curlang  = false;
    
    public static function add_hooks() {
        
        static $lock = false;
        
        if ( !$lock ) {
            
            add_filter( 'pre_option_ihaf_insert_header', 
                [ MaraleIhafPoly::class, 'pre_option_ihaf_insert_header' ], 
                    10, 3 );
            
            add_filter( 'pre_option_ihaf_insert_footer', 
                [ MaraleIhafPoly::class, 'pre_option_ihaf_insert_footer' ], 
                    10, 3 );
            
            add_filter( 'pre_update_option_ihaf_insert_header', 
                [ MaraleIhafPoly::class, 'pre_update_option_ihaf_insert_header' ], 
                    10, 3 );
            
            add_filter( 'pre_update_option_ihaf_insert_footer', 
                [ MaraleIhafPoly::class, 'pre_update_option_ihaf_insert_footer' ], 
                    10, 3 );
            
            //Backend init
            add_action( 'pll_language_defined', [ MaraleIhafPoly::class, 'init' ] );
            //Fronend init
            add_action(    'template_redirect', [ MaraleIhafPoly::class, 'init' ] );
            
            $lock = true;
        }
    }
    
    private static function add_option( $lang ) {
        
        $_lang = trim( $lang );
        
        if ( !$_lang ) {
            return;
        }
        
        add_option( 'ihaf_insert_header' . '_marale_' . $_lang );
        add_option( 'ihaf_insert_footer' . '_marale_' . $_lang );
    }
    
    private static function get_current_language() {
        return self::$curlang;
    }
    
    private static function set_current_language() {
        self::$curlang = function_exists( 'pll_current_language' ) ?
                pll_current_language() : false;
    }
    
    public static function init() { 
        
        self::set_current_language();   
        
        if ( self::get_current_language() ) {
            self::add_option( self::get_current_language() );
        }
    }
    
    public static function pre_option_ihaf_insert_header( $check, $option, $default ) {
        
        if ( current_filter() !== 'pre_option_ihaf_insert_header' || $check !== false ) {
            return $check;
        }
        
        $current_language = self::get_current_language();
        
        if ( !$current_language ) {
            return $check;
        }
        
        return get_option( $option . '_marale_' . $current_language, $default );
    }
    
    public static function pre_option_ihaf_insert_footer( $check, $option, $default ) {

        if ( current_filter() !== 'pre_option_ihaf_insert_footer' || $check !== false ) {
            return $check;
        }
        
        $current_language = self::get_current_language();
        
        if ( !$current_language ) {
            return $check;
        }
        return get_option( $option . '_marale_' . $current_language, $default );
    }
    
    public static function pre_update_option_ihaf_insert_header( $value, $old_value, $option ) {

        if ( current_filter() !== 'pre_update_option_ihaf_insert_header' || $value === $old_value ) {
            return $value;
        }
        
        $current_language = self::get_current_language();
        
        if ( !$current_language ) {
            return $value;
        }
        
        if ( update_option( $option . '_marale_' . $current_language, $value ) ) {
            return $old_value;
        }
        
        return $value;
    }
    
    public static function pre_update_option_ihaf_insert_footer( $value, $old_value, $option ) {

        if ( current_filter() !== 'pre_update_option_ihaf_insert_footer' || $value === $old_value ) {
            return $value;
        }
        
        $current_language = self::get_current_language();

        if ( !$current_language ) {
            return $value;
        }
        
        if ( update_option( $option . '_marale_' . $current_language, $value ) ) {
            return $old_value;
        }
        
        return $value;
    }
}