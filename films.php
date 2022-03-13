<?php
// Set up CORS for localhost:8080 access
header( 'Content-Type: application/json; charset=utf-8' );
header( "Access-Control-Allow-Origin: http://localhost:8080" );
// Use * for any domain access
// header( "Access-Control-Allow-Origin: *" );
header( 'Access-Control-Allow-Methods: POST, GET, DELETE, PUT, OPTIONS' );
header( 'Access-Control-Allow-Headers: token, Content-Type' );
// LOAD Database Settings
$hostname = "xxxx";
$username = "xxxx";
$password = "xxxx";
$database = "xxxx";
$mysqli = new mysqli($hostname, $username, $password, $database);
//check for querystring
$getFilmID = $_GET[ 'filmID' ] ?? null;
// GET all
if ( $_SERVER[ 'REQUEST_METHOD' ] === 'GET' && $getFilmID == null ) {
  $queryFilms = "SELECT * FROM Films";
  $resultFilms = $mysqli->query( $queryFilms );
  while ( $row = $resultFilms->fetch_array( MYSQLI_ASSOC ) ) {
    $myArray[] = $row;
  }
  echo json_encode( $myArray );
}
// GET BY filmID
if ( $_SERVER[ 'REQUEST_METHOD' ] === 'GET' && $getFilmID !== null ) {
  $stmt = $mysqli->prepare( "SELECT * FROM Films WHERE filmID = ?" );
  $stmt->bind_param( 'i', $getFilmID );
  $stmt->execute();
  $result = $stmt->get_result();
  while ( $data = $result->fetch_assoc() ) {
    $retvar[] = $data;
  }
  echo json_encode( $retvar[ 0 ] );
}
// POST
if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
  // POST INSERT
  $json = file_get_contents( 'php://input' );
  // Converts it into a PHP object
  $data = json_decode( $json );
  // INSERT SQL
  $stmt = $mysqli->prepare( "INSERT INTO Films(
  filmTitle, 
  filmCertificate, 
  filmDescription,
  filmImage,
  filmPrice,
  stars,
  releaseDate) VALUES (?, ?, ?, ?, ?, ?, ?)" );
  $stmt->bind_param( 'ssssdis', $data->filmTitle,
    $data->filmCertificate,
    $data->filmDescription,
    $data->filmImage,
    $data->filmPrice,
    $data->stars,
    $data->releaseDate );
  $stmt->execute();
  $newId = $stmt->insert_id;
  $stmt->close();
  $returnAr = array( "filmID" => $newId );
  echo json_encode( $returnAr );
}
// PUT
if ( $_SERVER[ 'REQUEST_METHOD' ] === 'PUT' ) {
  // PUT UPDATE
  $json = file_get_contents( 'php://input' );
  // Converts it into a PHP object
  $data = json_decode( $json );
  $stmt = $mysqli->prepare( "UPDATE Films SET
  filmTitle = ?, 
  filmCertificate = ?, 
   filmDescription = ?, 
   filmImage = ?,  
   filmPrice = ?,  
   stars = ?,
   releaseDate = ?
   WHERE filmID = ?" );
  $stmt->bind_param( 'ssssdisi', $data->filmTitle,
    $data->filmCertificate,
    $data->filmDescription,
    $data->filmImage,
    $data->filmPrice,
    $data->stars,
    $data->releaseDate,
    $data->filmID );
  $stmt->execute();
  $stmt->close();
  $returnAr = array( "success" => true );
  echo json_encode( $returnAr );
}
// DELETE
if ( $_SERVER[ 'REQUEST_METHOD' ] === 'DELETE' && $getFilmID !== null ) {
  // DELETE 	
  $stmt = $mysqli->prepare( "DELETE FROM Films WHERE filmID = ?" );
  $stmt->bind_param( 'i', $getFilmID );
  $stmt->execute();
  $stmt->close();
  $returnAr = array( "success" => true );
  echo json_encode( $returnAr );
}
