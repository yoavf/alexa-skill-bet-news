<?php

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

$update_time = clone $episode_time;
$update_time->setTimeZone( new DateTimeZone( 'UTC' ) );

$first_url = 'https://api.bynetcdn.com/Redirector/ipbc/manifest/news_he_' . $episode_time->format( 'YmdH00' ) . '/HLS/21/playlist.m3u8';

$feed = [
    'uid' => 'kan-latest-news-' . $episode_name,
    'updateDate'=> $update_time->format( 'Y-m-d\TH:i:s\Z' ),
    'titleText' => 'KAN Reshet Bet news briefing for ' . $episode_time->format( 'l, F j, Y \a\t G:i' ),
    'streamUrl' =>  $first_url,
    'redirectionUrl' => 'https://kan.org.il/',
    'mainText' => '',
];

header( 'Content-Type: application/json' );
echo json_encode( $feed );
