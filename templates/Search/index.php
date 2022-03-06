<?php
$km_options = [
  '0'     => '指定なし',
  '0.1'   => '100m',
  '0.5'   => '500m',
  '1'     => '1km',
  '5'     => '5km',
  '10'    => '10km',
  '20'    => '20km',
  '30'    => '30km',
  '100'   => '100km',
  '250'   => '250km',
  '300'   => '300km',
  '350'   => '350km',
  '500'   => '500km',
  '750'   => '750km',
  '1000'  => '1000km',
  '1500'  => '1500km',
];

?>
<html lang="ja" class="h-100">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
<meta http-equiv="content-language" content="ja">
<title>位置情報からツイッターのツイートを検索するツール</title>
<script type="text/javascript" src="<?php echo $this->Url->build('/js/jquery-3.6.0.min.js') ?>"></script>
<script type="text/javascript" src="<?php echo $this->Url->build('/bootstrap-4.6.0-dist/js/bootstrap.min.js') ?>"></script>
<script type="text/javascript" src="<?php echo $this->Url->build('/fontawesome-free-5.15.3-web/js/all.js') ?>"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLEMAP_APIKEY; ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->Url->build('/bootstrap-4.6.0-dist/css/bootstrap.css') ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->Url->build('/fontawesome-free-5.15.3-web/css/all.css') ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->Url->build('/css/style.css?1') ?>"/>
<script type="text/javascript">
$(document).ready(function(){
  var map,
      circle,
      lat,
      lng,
      zoom,
      map_type_id;

  var popSession = function(key, default_value) {
    var value = sessionStorage.getItem(key);
    if (value == null) {
      value = default_value;
    }

    return value;
  }

  var pushSession = function(key, value) {
    if (value == null) {
      sessionStorage.removeItem(key);
    }
    else {
      sessionStorage.setItem(key, value);
    }
  }

  var update_circle = function(){
    var km = $('[name="km"]').val(),
        latlng = map.getCenter();

    if (km == 0) {
      circle.setVisible(false);
      $('[name="lat"]').val('');
      $('[name="lng"]').val('');
    }
    else {
      circle.setVisible(true);
      circle.setCenter(latlng);
      circle.setRadius(km * 1000);
      $('[name="lat"]').val(Math.round(latlng.lat() * 100000) / 100000);
      $('[name="lng"]').val(Math.round(latlng.lng() * 100000) / 100000);
    }

  };

  lat = <?php if ($lat != ''): ?><?php echo h($lat); ?><?php else: ?>parseFloat(popSession('lat', 35.71025))<?php endif; ?>,
  lng = <?php if ($lng != ''): ?><?php echo h($lng); ?><?php else: ?>parseFloat(popSession('lng', 139.81055))<?php endif; ?>,
  zoom = parseInt(popSession('zoom', 16)),
  map_type_id = popSession('map_type_id', 'roadmap');

  map = new google.maps.Map(
    $('#map').get(0),
    {
      center: new google.maps.LatLng(lat, lng),
      zoom: zoom,
      mapTypeId: map_type_id,
      fullscreenControl: false,
      controlSize: 32
    }
  );

  map.controls[google.maps.ControlPosition.TOP_LEFT].push($('<input id="address" class="form-control w-auto" style="margin-top:7px;" placeholder="地名で移動">').get(0));

  circle = new google.maps.Circle({
    "map": map,
    "clickable": false,
    "fillColor": "#FF0000",
    "fillOpacity": 0.05,
    "strokeColor": "#FF0000",
    "strokeOpacity": 0.8,
    "strokeWeight": 2,
    "zIndex": 1
  });

  google.maps.event.addListener(map, 'center_changed', function(){
    var latlng = map.getCenter();
    pushSession('lat', latlng.lat());
    pushSession('lng', latlng.lng());

    update_circle();
  });

  google.maps.event.addListener(map, 'zoom_changed', function(){
    pushSession('zoom', map.getZoom());
  });

  google.maps.event.addListener(map, 'maptypeid_changed', function(){
    pushSession('map_type_id', map.getMapTypeId());
  });

  $('[name="km"]').change(function(){
    update_circle();
  });

  $('#map').on('change', '#address', function(){
    var geocoder = new google.maps.Geocoder();

    geocoder.geocode({'address': $(this).val()}, function(results, status){
      if (status != google.maps.GeocoderStatus.OK) return;
      map.setCenter(results[0].geometry.location);
    });
  });

  $('#hint_q').change(function(){
    $('[name="q"]').val($(this).val());
  });

  $('#sidebar form').submit(function(){
    var params = [
      ['lat', ''],
      ['lng', ''],
      ['km', '0'],
      ['q', ''],
    ];

    $.each(params, function(i, v){
      var $input = $('[name="' + v[0] + '"]');
      if ($input.val() == v[1]) {
        $input.prop('disabled', true);
      }
    });
  });

  $('#arrow').click(function(){
    $('#sidebar_inner').animate({
      scrollTop: 0
    }, 200);
  });

  $('#sidebar .list-group-item').each(function(){
    var $item = $(this),
        $pin = $('.pin', $item),
        marker,
        latlng;

    if ($pin.length > 0) {

      latlng = new google.maps.LatLng($pin.data('lat'), $pin.data('lng'));

      marker = new google.maps.Marker({
        position: latlng,
        map: map,
        icon: new google.maps.MarkerImage(
          $('.user img', $item).attr('src'),
          new google.maps.Size(24, 24),
          new google.maps.Point(0, 0),
          new google.maps.Point(12, 32),
          new google.maps.Size(24, 24)
        )
      });

      google.maps.event.addListener(marker, 'click', function(){
        var pos = $item.position().top + $('#sidebar #sidebar_inner').scrollTop() - $('#sidebar form').height();
        $('#sidebar_inner').animate({scrollTop: pos}, 'slow');
      });

      $pin.click(function(){
        map.panTo(latlng);
      });
    }
  });

  google.maps.event.addListener(map, 'center_changed', function(){
    update_circle();
  });

  update_circle();

});
</script>
<style type="text/css">
#sidebar_inner {
    flex-grow: 1;
    overflow-y: scroll;
    overflow-x: hidden;
    flex-basis: 0;
    width: 320px;
    padding-top: 6rem;
}
#sidebar form {
    position: absolute;
    width: 300px;
    background-color: #FFF;
    z-index: 1;
    top: 0;
}
.gm-style-mtc button,
.gm-style-mtc li {
    font-size: 1rem !important;
}
</style>
</head>
<body class="d-flex flex-column h-100">
<nav id="header" class="navbar navbar-dark">
  <a href="<?php echo $this->Url->build('/') ?>" class="navbar-brand">位置情報からツイッターのツイートを検索するツール</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#sidebar_frame">
    <i class="fas fa-chevron-right"></i>
    <i class="fas fa-chevron-left"></i>
  </button>
</nav>
<div class="flex-fill d-flex">
  <div id="map" class="flex-fill h-100"></div>
  <div id="sidebar_frame" class="collapse show">
    <div id="sidebar">
      <div id="sidebar_inner">
        <form action="<?php echo $this->Url->build('/') ?>">
          <input type="hidden" name="lat" value="<?php echo h($lat); ?>">
          <input type="hidden" name="lng" value="<?php echo h($lng); ?>">
          <div class="d-flex p-1">
            <select name="km" class="form-control mr-2">
<?php foreach ($km_options as $value => $label): ?>
              <option value="<?php echo h($value); ?>" <?php if ($km == $value): ?>selected<?php endif; ?>><?php echo h($label); ?></option>
<?php endforeach; ?>
            </select>
            <div class="form-check form-check-inline">
              <label class="form-check-label text-nowrap">
                <input class="form-check-input" type="checkbox" name="nort" value="1" <?php if ($nort != ''): ?>checked<?php endif; ?>>RTなし
              </label>
            </div>
            <div class="form-check form-check-inline">
              <label class="form-check-label text-nowrap">
                <input class="form-check-input" type="checkbox" name="media" value="1" <?php if ($media != ''): ?>checked<?php endif; ?>>メディアのみ
              </label>
            </div>
<?php /*
            <select id="hint_q" class="form-control w-auto">
              <option value="watching now">部分一致</option>
              <option value="<?php echo h('“happy hour”'); ?>">完全一致</option>
              <option value="love OR hate">OR検索</option>
              <option value="beer -root">一部除く</option>
              <option value="#haiku">ハッシュタグ</option>
              <option value="from:interior">アカウント</option>
              <option value="@NASA">メンション</option>
            </select>
*/ ?>
          </div>
          <div class="d-flex p-1">
            <input type="text" name="q" class="form-control" value="<?php echo h($q); ?>" placeholder="検索キーワード">
            <button type="submit" class="btn btn-danger text-nowrap mx-1"><i class="fas fa-search"></i> 検索</button>
            <a href="https://developer.twitter.com/en/docs/twitter-api/v1/tweets/search/guides/standard-operators" target="_blank" title="検索キーワードの解説（ツイッター公式）" class="form-control-plaintext w-auto"><i class="far fa-question-circle"></i></a>
          </div>
        </form>
<?php if ($tweets != ''): ?>
<?php echo $this->element('pagination_search'); ?>
        <div id="tweets">
          <ul class="list-group">
<?php foreach ($tweets->statuses as $status): ?>
<?php echo $this->element('tweet', ['status' => json_decode(json_encode($status), true)]); ?>
<?php endforeach; ?>
          </ul>
        </div>
<?php echo $this->element('pagination_search'); ?>
<?php endif; ?>
      </div>
    </div>
    <div id="arrow"><i class="fas fa-arrow-circle-up"></i></div>
  </div>
</div>
<div id="loading" class="hide">
  <div class="circle"></div>
  <div class="circle1"></div>
</div>
</body>
</html>
