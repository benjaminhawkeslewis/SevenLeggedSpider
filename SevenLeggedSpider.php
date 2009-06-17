<?php
// Requires PECL HTTP.


// Require PEAR Net::URL2
require_once( 'Net/URL2.php' );

class SevenLeggedSpider {

    public static function slurpXml( $xml ) {
        $dom = new DomDocument();
        $dom->strictErrorChecking = false;
        $dom->recover             = true;
        // TODO: Any way around error suppression?
        @$dom->loadXML( $xml );
        return $dom;
    }

    public static function slurpHtml( $html ) {
        $dom = new DomDocument();
        $dom->strictErrorChecking = false;
        $dom->recover             = true;
        // TODO: Any way around error suppression?
        @$dom->loadHTML( $html );
        return $dom;
    }

    public static function getResponseBody( $response ) {
        return $response->body;
    }
    
    public static function getNodesByXpath( $dom, $query ) {
        $xpath = new DOMXPath( $dom );
        $nodes = $xpath->query( $query );
        return $nodes;       
    }
    
    public function get( $url, $data = null ) {
        $response = http_get( $url, array(), $info );
        $parsed   = http_parse_message( $response );
        return $parsed;
    }
    
    public function post( $url, $data = array() ) {
        $response = http_post( $url, $data );
        $parsed   = http_parse_message( $response );
        return $parsed;        
    }
    
    public static function getBaseUrl( $dom, $document_url ) {
        $base_url = $document_url;
        
        $nodes = self::getNodesByXpath( $dom, '//base/@href' );

        if ( $nodes && $nodes->item(0) ) {
            $base_url_override = $nodes->item(0)->value;
        }

        $parsed = parse_url( $base_url );

        if ( !empty( $base_url_override ) ) {
            $parsed = parse_url( $base_url_override );
        }

        $scheme = !empty( $parsed['scheme'] ) ? $parsed['scheme'] : 'http';
        $host   = $parsed['host'];
        $port   = !empty( $parsed['port'] ) ? ':'.$parsed['port'] : '';
        $path   = dirname( $parsed['path'] );
        $path   = preg_replace( '/(.*[^\/])$/', '$1/', $path );

        $base_url = "${scheme}://${host}${port}/${path}";

        return $base_url;
    }

    public static function resolveUrl( $relative_url, $base_url ) {
        $base_url = new Net_URL2( $base_url );
        return $base_url->resolve( $relative_url )->getURL();
    }
                    
        
}

?>
