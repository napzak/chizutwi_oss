<?php
/**
 * ちずツイ
 *
 * @copyright Copyright (c) 2021 NAPZAK Inc. All Rights Reserved.
 * @author Takashi Ohta, NAPZAK Inc.
 */
?>
<li class="list-group-item">
  <a href="https://twitter.com/<?php echo _p($status, 'user.screen_name'); ?>" class="user d-flex" target="_blank">
    <img src="<?php echo _p($status, 'user.profile_image_url_https'); ?>">
    <div class="ml-2">
      <div class="user_name font-weight-bold"><?php echo h(_p($status, 'user.name')); ?></div>
      <div class="text-secondary">@<?php echo h(_p($status, 'user.screen_name')); ?></div>
    </div>
  </a>
  <div class="text my-2"><?php echo $this->htmlTweetText($status); ?></div>
  <div class="d-flex justify-content-between">
    <div class="text-secondary"><?php echo date('Y年n月j日 G:i', strtotime(_p($status, 'created_at'))); ?></div>
    <a href="https://twitter.com/<?php echo h(_p($status, 'user.screen_name')); ?>/status/<?php echo h(_p($status, 'id_str')); ?>" class="external_link" title="元ツイート" target="_blank"><i class="fas fa-external-link-alt"></i></a>
  </div>
  <div><?php echo $this->htmlTweetGeo($status); ?></div>
<?php if (isset($status['entities']['media']) == true): ?>
<?php foreach ($status['entities']['media'] as $media): ?>
    <img src="<?php echo _p($media, 'media_url_https'); ?>" class="img-fluid mt-2">
<?php endforeach; ?>
<?php endif; ?>
</li>
