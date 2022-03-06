<?php
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\Log\Log;
use Cake\Routing\Router;
use Cake\View\Exception\MissingTemplateException;

/**
 * 検索コントローラー
 */
class SearchController extends AppController {

	/**
	 * initialize
	 */
    public function initialize(): void {
		parent::initialize();

		// コンポーネントのインスタンス化
		$this->loadComponent('Twitter');
	}

	/**
	 * 検索画面
	 */
	public function index() {
		$tweets		= '';
		$next_url	= '';

		$q		= $this->getParam('q');
		$lat 	= $this->getParam('lat');
		$lng 	= $this->getParam('lng');
		$km 	= $this->getParam('km', 0);
		$nort 	= $this->getParam('nort');
		$media 	= $this->getParam('media');

		$q_ext = $q;
		$ext = [];

		if ($nort != '') {
			$q_ext .= ' -filter:retweets';
			$ext[] = 'nort=1';
		}

		if ($media != '') {
			$q_ext .= ' filter:media';
			$ext[] = 'media=1';
		}

		$option = [
			'q'				=> $q_ext,
			'result_type '	=> 'recent',
			'count'			=> 30,
			'max_id'		=> $this->getParam('max_id', -1),
		];

		if ($lat != '' && $lng != '' && $km != 0) {

			$geocode = "{$lat},{$lng},{$km}km";
			$option['geocode'] = $geocode;
		}

		$tweets = $this->Twitter->search($option);

		if (isset($tweets->statuses) == false) {
			$tweets = '';
		}
		else if (count($tweets->statuses) == 30 && isset($tweets->search_metadata->next_results) == true) {
			if (preg_match('/max_id=(\d*)/i', $tweets->search_metadata->next_results, $matched) == true) {

				$url = '/search/?';

				if ($lat != '' && $lng != '' && $km != 0) {
					$url .= "lat={$lat}&lng={$lng}&km={$km}&";
				}

				if (count($ext) > 0) {
					$url .= implode('&', $ext) . '&';
				}

				if ($q != '') {
					$url .= "q={$q}&";
				}

				$url .= "max_id={$matched[1]}";

				$next_url = Router::url($url);
			}
		}

		$this->set([
			'q'				=> $q,
			'lat'			=> $lat,
			'lng'			=> $lng,
			'km'			=> $km,
			'nort'			=> $nort,
			'media'			=> $media,
			'tweets'    	=> $tweets,
			'next_url'		=> $next_url,
		]);
	}
}
