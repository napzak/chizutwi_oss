<?php
/**
 * ちずツイ
 *
 * @copyright Copyright (c) 2021 NAPZAK Inc. All Rights Reserved.
 * @author Takashi Ohta, NAPZAK Inc.
 */

namespace App\Controller\Component;

use Abraham\TwitterOAuth\TwitterOAuth;
use Cake\Controller\Component;
use Cake\Http\Client;
use Cake\Log\Log;

/**
 * Twitterコンポーネント
 */
class TwitterComponent extends Component {

	/**
	 * コネクションを取得
	 */
	private function _getConnection() {
		return new TwitterOAuth(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_KEY_SERCRET, TWITTER_ACCESS_TOKEN, TWITTER_ACCESS_TOKEN_SECRET);
	}

	/**
	 * 検索
	 */
	public function search($option = []) {
		return $this->_getConnection()->get('search/tweets', $option);
	}
}