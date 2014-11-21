<?php
/*
Plugin Name: Pirates Ahoy!
Plugin URI: http://urbangiraffe.com/plugins/pirates-ahoy/
Description: Speak like a pirate, you land lubber!
Version: 0.1
Author: John Godley
Author URI: http://urbangiraffe.com
============================================================================================================
Based upon PigLatin plugin by Nikolay Bachiysk
============================================================================================================
This software is provided "as is" and any express or implied warranties, including, but not limited to, the
implied warranties of merchantibility and fitness for a particular purpose are disclaimed. In no event shall
the copyright owner or contributors be liable for any direct, indirect, incidental, special, exemplary, or
consequential damages(including, but not limited to, procurement of substitute goods or services; loss of
use, data, or profits; or business interruption) however caused and on any theory of liability, whether in
contract, strict liability, or tort(including negligence or otherwise) arising in any way out of the use of
this software, even if advised of the possibility of such damage.

For full license details see license.txt
============================================================================================================
*/

class Pirates_Ahoy {
	function word2pirate( $match ) {
		$text = $match[0];

		$convert = array(
			'left'       => 'port',
			'hello'      => array( 'ahoy', 'avast' ),
			'comes'      => 'hails',
			'welcome'    => 'ahoy',
			'stop'       => 'avast',
			'yes'        => array( 'aye', 'yarr' ),
			'treasure'   => 'booty',
			'haha'       => 'hoho',
			'there'      => 'thar',
			'you'        => 'ye',
			'my'         => 'me',
			'is'         => 'be',
			'yes'        => 'yarr',
			'to'         => "t'",
			'back'       => 'aft',
			'your'       => 'yer',
			'yourself'   => 'yerself',
			'there'      => 'thar',
			'are'        => 'be',
			'for'        => 'fer',
			'of'         => "o'",
			'dinner'     => 'grub',
			'girlfriend' => 'strumpet',
			'girl'       => 'lass',
			"don't"      => 'dunno',
			'dont'       => 'dunno',
			'friend'     => 'matey',
			'what'       => "wha'",
			"you're"     => "ye're",
			"youre"      => "ye're",
			'dollars'    => 'doubloons',
			'person'     => 'landlubber',
			'beer'       => 'grog',
			'dollar'     => 'doubloon',
			'profile'    => 'stuff',
			'drink'      => 'grog',
			'woman'      => 'lass',
			'the'        => array( "t'", 'thar', 'ye', 'yonder' ),
			"it's"       => "'tis",
			'together'   => "t'gether",
			'one'        => "'un",
			'just'       => "jus'",
			'and'        => "an'",
			'with'       => "wi'",
			'perhaps'    => "p'raps",
			'big'        => 'grand',
			'via'        => "wit'",
			'user'       => 'sea dog',
			'admin'      => 'captain'
		);

		$endings = array(
			'ing'  => "in'",
			'ings' => "in's"
		);

		$middles = array(
			'v' => "'"
		);

		$lower = strtolower( $text );
		if ( isset( $convert[$lower] ) ) {
			if ( is_array( $convert[$lower] ) )
				$pirate = $convert[$lower][array_rand( $convert[$lower] )];
			else
				$pirate = $convert[$lower];

			// Check case
			if ( strtoupper( $text[0] ) == $text[0] )
				$pirate[0] = strtoupper( $pirate[0] );

			return $pirate;
		}

		// Endings
		foreach ( $endings AS $ending => $new_ending ) {
			if ( substr( $text, -strlen( $ending) ) == $ending )
				$text = str_replace( $ending, $new_ending, $text );
		}

		// Middles
		if ( strlen( $text ) > 4 ) {
			foreach ( $middles AS $middle => $new_ending ) {
				$text = preg_replace( '@^(.+?)'.$middle.'(.+)$@i', '$1'.$new_ending.'$2', $text );
			}
		}

		return $text;
	}

	function translation2pirate( $string ) {
		if ( strlen( $string ) < 3 )
    	return $string;

		$delimiters = array(
			'<.*?>',
			'\&#\d+;',
			'\&[a-z]+;',
			'%\d+\$[sd]',
			'%[sd]',
			'\s+',
		);

		$parts = preg_split( '/('.implode( '|', $delimiters ).')/i', $string, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );
		$cnt   = count( $parts );

		for ( $i = 0; $i < $cnt; ++$i) {
		    $isdelim = false;
		    foreach ( $delimiters as $delim ) {
		        if ( preg_match( "/^$delim$/", $parts[$i] ) ) {
	            $isdelim = true;
	            break;
		        }
		    }

		    if ( $isdelim )
					continue;

		    $parts[$i] = preg_replace_callback( '/[a-z]+/i', array('Pirates_Ahoy', 'word2pirate'),$parts[$i] );
		}

		// Combine
		$combine = array(
			"o'",
			" t'",
		);

		$string = implode( '', $parts );
		foreach ( $combine AS $comb ) {
			$string = str_replace( $comb.' ', $comb, $string );
		}

		$string = str_replace( "t't", "t'", $string );
		return str_replace( "''", "'", $string );
	}

	function gettext( $translated, $original ) {
		$ignore = array(
			'Jan_January_abbreviation'.
			'Feb_February_abbreviation',
			'Mar_March_abbreviation',
			'Apr_April_abbreviation',
			'May_May_abbreviation',
			'Jun_June_abbreviation',
			'Jul_July_abbreviation',
			'Aug_August_abbreviation',
			'Sep_September_abbreviation',
			'Oct_October_abbreviation',
			'Nov_November_abbreviation',
			'Dec_December_abbreviation'
		);

		if ( in_array( $original, $ignore ) )
			return $original;
	  return Pirates_Ahoy::translation2pirate( $original );
	}

	function ngettext( $translated, $single, $plural, $number ) {
		return Pirates_Ahoy::translation2pirate( $number == 1 ? $single : $plural );
	}
};

// Hook into localisation functions
add_filter( 'gettext', array( 'Pirates_Ahoy', 'gettext' ), 10, 2 );
add_filter( 'gettext_with_context', array('Pirates_Ahoy', 'gettext' ), 10, 2 );
add_filter( 'ngettext', array( 'Pirates_Ahoy', 'ngettext' ), 10, 4 );
add_filter( 'ngettext_with_context', array( 'Pirates_Ahoy', 'ngettext' ), 10, 4 );

// Hook into post, excerpt, and comment functions
add_filter( 'the_title', array( 'Pirates_Ahoy', 'translation2pirate' ) );
add_filter( 'the_content', array( 'Pirates_Ahoy', 'translation2pirate' ) );
add_filter( 'the_excerpt', array( 'Pirates_Ahoy', 'translation2pirate' ) );
add_filter( 'get_comment_text', array( 'Pirates_Ahoy', 'translation2pirate' ) );
