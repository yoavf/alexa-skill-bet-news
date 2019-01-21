<?php

$israel_tz = new DateTimeZone( 'Asia/Jerusalem' );

$time = new DateTime( 'now', $israel_tz );

$hour = (int) $time->format('H');
$minutes = (int) $time->format('i');
$date = $time->format('ymd');

if ( $minutes < 7 ) {
    $hour--;
}

$hour_formatted = sprintf("%02d", $hour ) ;

$episode_name = "$date-$hour_formatted";

$episode_time = DateTime::createFromFormat( 'ymd-H', $episode_name, $israel_tz );
$update_time = clone $episode_time->setTimeZone( new DateTimeZone( 'UTC' ) );

$feed = [
    'uid' => 'glz-latest-news-' . $episode,
    'updateDate'=> $update_time->format( 'Y-m-d\TH:i:s\Z' ),
    'titleText' => 'GLZ news breifing for ' . $episode_time->format( 'l, F j, Y \a\t G:i' ),
    'streamUrl' => "https://api.bynetcdn.com/Redirector/glz/{$episode_name}_NEWS/PD?awCollectionId=1111&ExternalId={$episode_name}_NEWS",
    'redirectionUrl' => 'https://glz.co.il/',
    'mainText' => '',
];

header( 'Content-Type: application/json' );
echo json_encode( $feed );
