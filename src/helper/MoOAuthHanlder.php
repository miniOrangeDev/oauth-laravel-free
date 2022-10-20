<?php
namespace MiniOrange\Helper;

class Mo_OAuth_Hanlder {

	function getAccessTokenCurl($tokenendpoint, $grant_type, $clientid, $clientsecret, $code, $redirect_url, $send_headers, $send_body){

		$ch = curl_init($tokenendpoint);
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_ENCODING, "" );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
		curl_setopt( $ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Authorization: Basic '.base64_encode($clientid.":".$clientsecret),
			'Accept: application/json'
		));

		curl_setopt( $ch, CURLOPT_POSTFIELDS, 'redirect_uri='.urlencode($redirect_url).'&grant_type='.$grant_type.'&client_id='.$clientid.'&client_secret='.$clientsecret.'&code='.$code);
		$content = curl_exec($ch);

		if(curl_error($ch)){
			echo "<b>Response : </b><br>";print_r($content);echo "<br><br>";		
			exit( curl_error($ch) );
		}

		if(!is_array(json_decode($content, true))){
			echo "<b>Response : </b><br>";print_r($content);echo "<br><br>";
			exit("Invalid response received.");
		}

		$content = json_decode($content,true);
		if(isset($content["error_description"])){
			exit($content["error_description"]);
		} else if(isset($content["error"])){
			exit($content["error"]);
		} else if(isset($content["access_token"])) {
			$access_token = $content["access_token"];
		} else {
			echo "<b>Response : </b><br>";print_r($content);echo "<br><br>";
			exit('Invalid response received from OAuth Provider. Contact your administrator for more details.');
		}

		return $access_token;
	}


	function getAccessToken($tokenendpoint, $grant_type, $clientid, $clientsecret, $code, $redirect_url, $send_headers, $send_body){

		$response = $this->getToken ($tokenendpoint, $grant_type, $clientid, $clientsecret, $code, $redirect_url, $send_headers, $send_body);
		$content = json_decode($response,true);

		if(isset($content["access_token"])) {
			return $content["access_token"];
			exit;
		} else {
			echo 'Invalid response received from OAuth Provider. Contact your administrator for more details.<br><br><b>Response : </b><br>'.esc_html($response);
			exit;
		}
	}

	function getToken($tokenendpoint, $grant_type, $clientid, $clientsecret, $code, $redirect_url, $send_headers, $send_body){

		$clientsecret = html_entity_decode( $clientsecret );
		$body = array(
				'grant_type'    => $grant_type,
				'code'          => $code,
				'client_id'     => $clientid,
				'client_secret' => $clientsecret,
				'redirect_uri'  => $redirect_url,
			);
		$headers = array(
				"Accept: application/json",
				"charset: UTF - 8",
				"Authorization: Basic ". base64_encode( $clientid . ":" . $clientsecret ),
				"Content-Type: application/x-www-form-urlencoded",
		);
		if($send_headers && !$send_body){
				unset( $body['client_id'] );
				unset( $body['client_secret'] );
		}else if(!$send_headers && $send_body){
				unset( $headers['Authorization'] );
		}

		$ch = curl_init($tokenendpoint);
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_ENCODING, "" );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_MAXREDIRS, 5 );
		curl_setopt( $ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query($body));
		$response = curl_exec($ch);

		if(curl_error($ch)){
			echo "<b>Response : </b><br>";print_r($response);echo "<br><br>";		
			exit( curl_error($ch) );
		}

		if(!is_array(json_decode($response, true))){
			echo "<b>Response : </b><br>";print_r($response);echo "<br><br>";
			exit("Invalid response received.");
		}
		// $response   = wp_remote_post( $tokenendpoint, array(
		// 	'method'      => 'POST',
		// 	'timeout'     => 45,
		// 	'redirection' => 5,
		// 	'httpversion' => '1.0',
		// 	'blocking'    => true,
		// 	'headers'     => $headers,
		// 	'body'        => $body,
		// 	'cookies'     => array(),
		// 	'sslverify'   => false
		// ) );
		// if ( is_wp_error( $response ) ) {
		// 	wp_die( $response );
		// }
		if(!is_array(json_decode($response, true))){
			echo "<b>Response : </b><br>";print_r($response);echo "<br><br>";
			if(isset($response['body']))
			exit("Invalid response received.");
		}
		
		$content = json_decode($response,true);
		if(isset($content["error_description"])){
			exit($content["error_description"]);
		} else if(isset($content["error"])){
			exit($content["error"]);
		}
		
		return $response;
	}
	
	function getIdToken($tokenendpoint, $grant_type, $clientid, $clientsecret, $code, $redirect_url, $send_headers, $send_body){
		$response = $this->getToken ($tokenendpoint, $grant_type, $clientid, $clientsecret, $code, $redirect_url, $send_headers, $send_body);
		$content = json_decode($response,true);
		if(isset($content["id_token"]) || isset($content["access_token"])) {
			return $content;
			exit;
		} else {
			echo 'Invalid response received from OpenId Provider. Contact your administrator for more details.<br><br><b>Response : </b><br>'.esc_html($response);
			exit;
		}
	}

	function getResourceOwnerFromIdToken($id_token){
		$id_array = explode(".", $id_token);
		if(isset($id_array[1])) {
			$id_body = base64_decode( str_pad( strtr( $id_array[1], '-_', '+/' ), strlen( $id_array[1] ) % 4, '=', STR_PAD_RIGHT ) );
			if(is_array(json_decode($id_body, true))){
				return json_decode($id_body,true);
			}
		}
		echo 'Invalid response received.<br><b>Id_token : </b>'.esc_html($id_token);
		exit;
	}
	
	function getResourceOwner($resourceownerdetailsurl, $access_token){
		$headers = array();
		$headers['Authorization'] = 'Bearer '.$access_token;

		// $response   = wp_remote_post( $resourceownerdetailsurl, array(
		// 	'method'      => 'GET',
		// 	'timeout'     => 45,
		// 	'redirection' => 5,
		// 	'httpversion' => '1.0',
		// 	'blocking'    => true,
		// 	'headers'     => $headers,
		// 	'cookies'     => array(),
		// 	'sslverify'   => false
		// ) );

		// if ( is_wp_error( $response ) ) {
		// 	wp_die( $response );
		// }

		$ch = curl_init($resourceownerdetailsurl);
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_ENCODING, "" );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_MAXREDIRS, 15 );
		curl_setopt( $ch, CURLOPT_HTTPGET, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'charset: UTF - 8',
			'Authorization: ' . $headers['Authorization']
        ));
		
		// curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query($body));
		$response = curl_exec($ch);
		if(curl_error($ch)){
			echo "<b>Response : </b><br>";print_r($response);echo "<br><br>";		
			exit( curl_error($ch) );
		}
		
		if(!is_array(json_decode($response, true))){
			$response = addcslashes($response, '\\');
			if(!is_array(json_decode($response, true))){
				echo "<b>Response : </b><br>";print_r($response);echo "<br><br>";
				exit("Invalid response received.");
			}
		}
		
		$content = json_decode($response,true);
		if(isset($content["error_description"])){
			exit($content["error_description"]);
		} else if(isset($content["error"])){
			exit($content["error"]);
		}
		return $content;
	}
	
}

?>
