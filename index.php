<?php

$url = "https://www.kan.org.il/page.aspx?landingpageid=1009";



$israel_tz = new DateTimeZone( 'Asia/Jerusalem' );

$time = new DateTime( 'now', $israel_tz );

$hour = (int) $time->format('H');
$minutes = (int) $time->format('i');
$date = $time->format('ymd');

if ( $minutes < 7 ) {
    $hour--;
}



$hour_formatted = sprintf( "%02d", $hour ) ;
$episode_name = "$date-$hour_formatted";

$episode_time = DateTime::createFromFormat( 'ymd-H', $episode_name, $israel_tz );
$cache_file = '/tmp/landing-page-cache-' . $episode_time->format( 'YmdH00' );

if (file_exists($cache_file) && (filemtime($cache_file) > (time() - 60 ))) {
    // Cache file is less than five minutes old. 
    // Don't bother refreshing, just use the file as-is.
    $content = file_get_contents( $cache_file );
 } else {
    // Our cache is out-of-date, so load the data from our remote server,
    // and also save it over our cache for next time.
    $content = file_get_contents( $url );
    file_put_contents($cache_file, $content, LOCK_EX);
 }

 preg_match( "/Hourly_News_Player\.loadMedia\(\{ entryId: '(.*)' }\)/", $content, $matches );
 if ( count( $matches ) > 1 ) {
     $entry_id = $matches[1];
 } else {
     error_log( "Cannot find entryId" );
     unlink( $cache_file );
     die();
}

$stream_url = "https://cdnapisec.kaltura.com/p/2717431/sp/271743100/playManifest/entryId/$entry_id/protocol/https/format/applehttp/flavorIds/1_q3ohbrbm,1_a816qlvk/a.m3u8?uiConfId=47265863&clientTag=html5:v1.6.1";


$update_time = clone $episode_time;
$update_time->setTimeZone( new DateTimeZone( 'UTC' ) );

$feed = [
    'uid' => 'kan-latest-news-' . $episode_name,
    'updateDate'=> $update_time->format( 'Y-m-d\TH:i:s\Z' ),
    'titleText' => 'KAN Reshet Bet news briefing for ' . $episode_time->format( 'l, F j, Y \a\t G:i' ),
    'streamUrl' =>  $stream_url,
    'redirectionUrl' => 'https://kan.org.il/',
    'mainText' => '',
];

header( 'Content-Type: application/json' );
echo json_encode( $feed );
