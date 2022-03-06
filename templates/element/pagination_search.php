<?php
/**
 * ちずツイ
 *
 * @copyright Copyright (c) 2021 NAPZAK Inc. All Rights Reserved.
 * @author Takashi Ohta, NAPZAK Inc.
 */
?>
<ul class="list-inline d-flex justify-content-around my-3">
<?php if ($next_url != ''): ?>
  <li>
    <a href="<?php echo $next_url ?>">次へ <i class="fas fa-arrow-right"></i></a>
  </li>
<?php endif; ?>
</ul>
