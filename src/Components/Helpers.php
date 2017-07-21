<?php
    namespace Soda\Analytics\Components;

    class Helpers {

        /**
         * @param $url
         * @param $other_query_string
         *
         * @return string URL
         *
         * Combine query strings
         * http://stackoverflow.com/a/3002497
         *
         * e.g.
         * merge_get_url('http://google.com','a=true') => http://google.com?a=true
         * merge_get_url('http://google.com?b=false','a=true') => http://google.com?b=false&a=true
         */
        public static function merge_get_url($url, $other_query_strings, $exclude_query_strings=[]) {
            // Parse the URL into components
            $url_parsed = parse_url($url);
            $new_qs_parsed = [];
            // Grab our first query string
            if ( isset($url_parsed['query']) ) {
                parse_str($url_parsed['query'], $new_qs_parsed);
            }

            // Here's the other query strings
            $other_qs_parsed = [];
            if ( is_string($other_query_strings) ) $other_query_strings = [$other_query_strings];
            foreach($other_query_strings as $other_query_string){
                $parsed = [];
                parse_str($other_query_string, $parsed);
                $other_qs_parsed = array_merge($parsed, $other_qs_parsed);
            }
            // Stitch the two query strings together
            $final_query_string_array = array_merge($new_qs_parsed, $other_qs_parsed);

            // remove existing
            if( is_string($exclude_query_strings) ) $exclude_query_strings = [$exclude_query_strings];
            $final_query_string_array = array_except($final_query_string_array, $exclude_query_strings);

            $final_query_string = http_build_query($final_query_string_array);
            // Now, our final URL:
            $new_url = $url_parsed['scheme']
                . '://'
                . $url_parsed['host']
                . @$url_parsed['path']
                . '?'
                . $final_query_string;

            return $new_url;
        }
    }
