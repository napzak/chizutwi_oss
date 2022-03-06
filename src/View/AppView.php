<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     3.0.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\View;

use Cake\View\View;

/**
 * Application View
 *
 * Your application's default view class
 *
 * @link https://book.cakephp.org/4/en/views.html#the-app-view
 */
class AppView extends View
{
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading helpers.
     *
     * e.g. `$this->loadHelper('Html');`
     *
     * @return void
     */
    public function initialize(): void
    {
    }

    /**
     * プレーンテキストに含まれるURLをAタグに変換する
     */
    public function url2anchor($str) {
        return preg_replace('/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/', '<a href="$0" target="_blank">$0</a>', $str);
    }

    /**
     * ツイートの本文に関するHTMLを返す
     */
    public function htmlTweetText($status) {

        $text = _p($status, 'text');
        $html = htmlspecialchars($text);
        $html = $this->url2anchor($html);

        $hash_tags = $this->getHashtags($html);
        $mention_tags = $this->getMentiontags($html);

        foreach ($hash_tags as $v) {
            $html = str_replace($v, '<a href="https://twitter.com/search/?q=' . urlencode($v) . '" class="font-weight-bold" target="_blank">' . $v . '</a>', $html);
        }

        foreach ($mention_tags as $v) {
            $html = str_replace($v, '<a href="https://twitter.com/' . str_replace("@", "", $v) . '" class="font-weight-bold" target="_blank">' . $v . '</a>', $html);
        }

        $html = nl2br($html);

        return $html;
    }

    /**
     * ツイートの位置情報に関するHTMLを返す
     */
    public function htmlTweetGeo($status) {

        $latlng = _p($status, 'geo.coordinates');
        if ($latlng != false && is_array($latlng) == true && count($latlng) == 2) {

            return "<button type=\"button\" class=\"btn btn-link pin\" data-lat=\"{$latlng[0]}\" data-lng=\"{$latlng[1]}\"><i class=\"fas fa-map-marker-alt\"></i> ({$latlng[0]}, {$latlng[1]})</button>";
        }

        $coordinates = _p($status, 'place.bounding_box.coordinates');
        if ($coordinates != false && is_array($coordinates) == true && count($coordinates) > 0) {

            $lat_min = 999;
            $lat_max = 0;
            $lng_min = 999;
            $lng_max = 0;
            foreach ($coordinates[0] as $latlng) {
                $lat_min = min($lat_min, $latlng[1]);
                $lat_max = max($lat_max, $latlng[1]);
                $lng_min = min($lng_min, $latlng[0]);
                $lng_max = max($lng_max, $latlng[0]);
            }

            $lat = $lat_min + ($lat_max - $lat_min) / 2;
            $lng = $lng_min + ($lng_max - $lng_min) / 2;

            $full_name = _p($status, 'place.full_name');

            return "<button type=\"button\" class=\"btn btn-link pin\" data-lat=\"{$lat}\" data-lng=\"{$lng}\"><i class=\"fas fa-map-marker-alt\"></i> {$full_name}</button>";
        }

        return '';
    }

    /**
     * ツイート本文からハッシュタグを抽出する
     */
    public function getHashtags($tweet) {
        $matches = [];
        preg_match_all('/#(w*[一-龠_ぁ-ん_ァ-ヴーａ-ｚＡ-Ｚa-zA-Z0-9]+|[a-zA-Z0-9_]+|[a-zA-Z0-9_]w*)/', $tweet, $matches);

        return $matches[0];
    }

    /**
     * ツイート本文からメンションタグを抽出する
     */
    public function getMentiontags($tweet) {
        $matches = [];
        preg_match_all('/@(w*[一-龠_ぁ-ん_ァ-ヴーａ-ｚＡ-Ｚa-zA-Z0-9]+|[a-zA-Z0-9_]+|[a-zA-Z0-9_]w*)/', $tweet, $matches);

        return $matches[0];
    }
}
