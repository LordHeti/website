<?php
namespace Destiny;
use Destiny\Commerce\PaymentStatus;
use Destiny\Common\User\UserFeature;
use Destiny\Common\Utils\Tpl;
use Destiny\Common\Utils\Country;
use Destiny\Common\Utils\Date;
use Destiny\Common\Config;
use Destiny\Commerce\SubscriptionStatus;
?>
<!DOCTYPE html>
<html>
<head>
    <title><?=Tpl::title($this->title)?></title>
    <?php include 'seg/meta.php' ?>
    <link href="<?=Config::cdnv()?>/web.css" rel="stylesheet" media="screen">
</head>
<body id="account" class="no-contain">
  <div id="page-wrap">

      <?php include 'seg/nav.php' ?>
      <?php include 'seg/alerts.php' ?>
      <?php include 'profile/menu.php' ?>
      <?php include 'profile/userinfo.php' ?>

      <?php if(!empty($this->ban)): ?>
          <section class="container">
              <h3 class="collapsed" data-toggle="collapse" data-target="#ban-content">Bans</h3>
              <div id="ban-content" class="content collapse">
                  <div class="content-dark clearfix">
                      <div class="ds-block">
                          <dl class="dl-horizontal">
                              <dt>Banned user</dt>
                              <dd><?=Tpl::out($this->user['username'])?></dd>
                              <dt>Time of ban</dt>
                              <dd><?=Tpl::moment(Date::getDateTime($this->ban['starttimestamp']), Date::STRING_FORMAT)?></dd>
                              <?php if($this->ban['endtimestamp']): ?>
                                  <dt>Ending on</dt>
                                  <dd><?=Tpl::moment(Date::getDateTime($this->ban['endtimestamp']), Date::STRING_FORMAT)?></dd>
                              <?php else: ?>
                                  <dt>Ending</dt>
                                  <dd>Never</dd>
                              <?php endif; ?>
                              <dt>Ban reason</dt>
                              <dd>
                                  <blockquote>
                                      <p style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?=Tpl::out($this->ban['reason'])?></p>
                                      <small><?=Tpl::out((!empty($this->ban['username'])) ? $this->ban['username']:'System')?></small>
                                  </blockquote>
                              </dd>
                          </dl>
                          <hr/>
                          <p>
                              Any non-permanent bans are removed when subscribing as well
                              as any mutes (there are no permanent mutes, maximum 6 days long).<br/>
                              This is not meant to be a cash grab, rather a tool for those who would
                              not like to wait for a manual unban or for the ban to naturally expire
                              and are willing to pay for it.<br />
                              Feel free to evade the ban if you have da skillz.
                          </p>
                      </div>
                  </div>
              </div>
          </section>
      <?php endif ?>

      <?php if(!empty($this->subscriptions)): ?>
          <section class="container">
              <h3 class="collapsed" data-toggle="collapse" data-target="#subscription-content">Subscription</h3>
              <div id="subscription-content" class="content collapse">

                  <?php if($this->user['istwitchsubscriber'] == 1): ?>
                  <div class="content-dark clearfix" style="margin-bottom:10px;">
                      <div class="ds-block">
                          <span>You have an active Twitch subscription</span> <i class="icon-twitch"></i>
                      </div>
                  </div>
                  <?php endif; ?>

                  <?php foreach($this->subscriptions as $subscription): ?>
                      <div class="content-dark clearfix" style="margin-bottom:10px;">
                          <div class="ds-block">
                              <div class="subscription" style="width: auto;">
                                  <h3><?=$subscription['type']['tierLabel']?></h3>
                                  <p>
                                      <span class="sub-amount">$<?=$subscription['type']['amount']?></span>
                                      (<?=$subscription['type']['billingFrequency']?> <?=strtolower($subscription['type']['billingPeriod'])?>
                                      <?php if($subscription['recurring'] == 1): ?><strong>Recurring</strong><?php endif ?>)
                                  </p>
                                  <dl>
                                      <dt>Remaining time</dt>
                                      <dd><?=Date::getRemainingTime(Date::getDateTime($subscription['endDate']))?></dd>
                                  </dl>
                                  <?php if(strcasecmp($subscription['paymentStatus'], PaymentStatus::ACTIVE)===0 && $subscription['recurring'] == 1): ?>
                                      <?php
                                      $billingNextDate = Date::getDateTime($subscription['billingNextDate']);
                                      $billingStartDate = Date::getDateTime($subscription['billingStartDate']);
                                      ?>
                                      <dl>
                                          <dt>Next billing date</dt>
                                          <?php if($billingNextDate > $billingStartDate): ?>
                                              <dd><?=Tpl::moment($billingNextDate, Date::STRING_FORMAT_YEAR)?></dd>
                                          <?php else: ?>
                                              <dd><?=Tpl::moment($billingStartDate, Date::STRING_FORMAT_YEAR)?></dd>
                                          <?php endif ?>
                                      </dl>
                                  <?php endif ?>
                                  <?php if(strcasecmp($subscription['status'], SubscriptionStatus::PENDING)===0): ?>
                                      <dl>
                                          <dt>This subscription is currently</dt>
                                          <dd><span class="label label-warning"><?=Tpl::out(strtoupper($subscription['status']))?></span></dd>
                                      </dl>
                                  <?php endif ?>
                                  <?php if(!empty($subscription['gifterUsername'])): ?>
                                      <p>
                                          <span class="fa fa-gift"></span> This subscription was gifted by <span class="label label-success"><?=Tpl::out($subscription['gifterUsername'])?></span>
                                      </p>
                                  <?php endif ?>
                                  <div style="margin-top:20px;">
                                      <a class="btn btn-warning" href="/subscription/<?=$subscription['subscriptionId']?>/cancel">Change</a>
                                  </div>
                              </div>
                          </div>
                      </div>
                  <?php endforeach; ?>
              </div>
          </section>
      <?php endif ?>

      <?php if(!empty($this->gifts)): ?>
          <section class="container">
              <h3 class="collapsed" data-toggle="collapse" data-target="#gift-content">Gifts</h3>
              <div id="gift-content" class="content collapse">

                  <div class="content-dark clearfix">
                      <div class="ds-block">
                            <?php foreach ($this->gifts as $gift): ?>
                            <div class="gift-sub">
                                <div class="gift-sub-info">
                                    <div>
                                        <span class="sub-label"><?= Tpl::out( $gift['type']['tierLabel'] ) ?></span>
                                        <span class="sub-amount">$<?=$gift['type']['amount']?></span>
                                        <span class="sub-billing">(<?=$gift['type']['billingFrequency']?> <?=strtolower($gift['type']['billingPeriod'])?><?php if($gift['recurring'] == 1): ?> recurring<?php endif ?>)</span>
                                    </div>
                                    <div>
                                        <span class="sub-gifted">Gifted to <span class="gift-giftee"><?= $gift['username'] ?></span> on <?=Tpl::moment(Date::getDateTime($gift['createdDate']), Date::FORMAT)?></span>
                                    </div>
                                </div>
                                <?php if($gift['recurring'] == 1): ?>
                                <div class="gift-sub-change">
                                    <a class="btn btn-sm btn-warning cancel-gift" href="/subscription/gift/<?= $gift['subscriptionId'] ?>/cancel">Change</a>
                                </div>
                                <?php endif ?>
                            </div>
                            <?php endforeach; ?>
                      </div>
                  </div>

              </div>
          </section>
      <?php endif ?>

      <section class="container">
          <h3 class="collapsed" data-toggle="collapse" data-target="#account-content">Account</h3>
          <div id="account-content" class="content content-dark clearfix collapse">

              <form id="profileSaveForm" action="/profile/update" method="post" role="form">

                  <div class="ds-block">
                      <?php if($this->user['nameChangedCount'] > 0): ?>
                          <div class="form-group">
                              <label>Username:
                                  <br><small>(You have <?=Tpl::number($this->user['nameChangedCount'])?> name changes left)</small>
                              </label>
                              <input class="form-control" type="text" name="username" value="<?=Tpl::out($this->user['username'])?>" placeholder="Username" />
                              <span class="help-block">A-z 0-9 and underscores. Must contain at least 3 and at most 20 characters</span>
                          </div>
                      <?php endif ?>

                      <?php if($this->user['nameChangedCount'] <= 0): ?>
                          <div class="form-group">
                              <label>Username:
                                  <br><small>(You have no more name changes available)</small>
                              </label>
                              <input class="form-control" type="text" disabled="disabled" name="username" value="<?=Tpl::out($this->user['username'])?>" placeholder="Username" />
                          </div>
                      <?php endif ?>

                      <div class="form-group">
                          <label>Email:
                              <br><small>Be it valid or not, it will be safe with us.</small>
                          </label>
                          <input class="form-control" type="text" name="email" value="<?=Tpl::out($this->user['email'])?>" placeholder="Email" />
                      </div>

                      <div class="form-group">
                          <label for="country">Nationality:
                              <br><small>The country you identify with</small>
                          </label>
                          <select class="form-control" name="country" id="country">
                              <option value="">Select your country</option>
                              <?$countries = Country::getCountries();?>
                              <option value="">&nbsp;</option>
                              <option value="US" <?php if($this->user['country'] == 'US'):?>
                                  selected="selected" <?php endif ?>>United States</option>
                              <option value="GB" <?php if($this->user['country'] == 'GB'):?>
                                  selected="selected" <?php endif ?>>United Kingdom</option>
                              <option value="">&nbsp;</option>
                              <?php foreach($countries as $country):?>
                                  <option value="<?=$country['alpha-2']?>" <?php if($this->user['country'] != 'US' && $this->user['country'] != 'GB' && $this->user['country'] == $country['alpha-2']):?>selected="selected"<?php endif;?>><?=Tpl::out($country['name'])?></option>
                              <?php endforeach; ?>
                          </select>
                      </div>

                      <div class="form-group">
                          <label for="allowGifting">Accept Gifts:
                              <br><small>Whether or not you would like the ability to receive gifts (subscriptions) from other people.</small>
                          </label>
                          <select class="form-control" name="allowGifting" id="allowGifting">
                              <option value="1"<?php if($this->user['allowGifting'] == 1):?> selected="selected"<?php endif ?>>Yes, I accept gifts</option>
                              <option value="0"<?php if($this->user['allowGifting'] == 0):?> selected="selected"<?php endif ?>>No, I do not accept gifts</option>
                          </select>
                      </div>

                  </div>

                  <div class="form-actions block-foot">
                      <button class="btn btn-lg btn-primary" type="submit">Save details</button>
                  </div>

              </form>
          </div>
      </section>

      <!--
      <section class="container">
          <h3 class="collapsed" data-toggle="collapse" data-target="#address-content">Address <small>(optional)</small></h3>
          <div id="address-content" class="content content-dark clearfix collapse">
              <div class="ds-block">
                  <small>Fields marked with <span class="icon-required">*</span> are required.</small>
              </div>
              <form id="addressSaveForm" action="/profile/address/update" method="post" class="validate">
                  <div class="ds-block">
                      <div class="form-group">
                          <label>Full Name <span class="icon-required">*</span>
                              <br><small>The name of the person for this address</small>
                          </label>
                          <input class="form-control" type="text" name="fullName" value="<?/*=Tpl::out($this->address['fullName'])*/?>" placeholder="Full Name" required />
                      </div>
                      <div class="form-group">
                          <label>Address Line 1 <span class="icon-required">*</span>
                              <br><small>Street address, P.O box, company name, c/o</small>
                          </label>
                          <input class="form-control" type="text" name="line1" value="<?/*=Tpl::out($this->address['line1'])*/?>" placeholder="Address Line 1" required />
                      </div>
                      <div class="form-group">
                          <label>Address Line 2
                              <br><small>Apartment, Suite, Building, Unit, Floor etc.</small>
                          </label>
                          <input class="form-control" type="text" name="line2" value="<?/*=Tpl::out($this->address['line2'])*/?>" placeholder="Address Line 2" />
                      </div>

                      <div class="form-group">
                          <label>City <span class="icon-required">*</span></label>
                          <input class="form-control" type="text" name="city" value="<?/*=Tpl::out($this->address['city'])*/?>" placeholder="City" required />
                      </div>
                      <div class="form-group">
                          <label>State/Province/Region <span class="icon-required">*</span></label>
                          <input class="form-control" type="text" name="region" value="<?/*=Tpl::out($this->address['region'])*/?>" placeholder="Region" required />
                      </div>
                      <div class="form-group">
                          <label>ZIP/Postal Code <span class="icon-required">*</span></label>
                          <input class="form-control" type="text" name="zip" value="<?/*=Tpl::out($this->address['zip'])*/?>" placeholder="Zip/Postal Code" required />
                      </div>
                      <div class="form-group">
                          <label for="country">Country <span class="icon-required">*</span></label>
                          <select class="form-control" name="country" id="country" required>
                              <option value="">Select your country</option>
                              <?/*$countries = Country::getCountries();*/?>
                              <option value="">&nbsp;</option>
                              <option value="US" <?php /*if($this->address['country'] == 'US'): */?>
                                  selected="selected" <?php /*endif */?>>United States</option>
                              <option value="GB" <?php /*if($this->address['country'] == 'GB'): */?>
                                  selected="selected" <?php /*endif */?>>United Kingdom</option>
                              <option value="">&nbsp;</option>
                              <?php /*foreach($countries as $country): */?>
                                  <option value="<?/*=$country['alpha-2']*/?>" <?php /*if($this->address['country'] != 'US' && $this->address['country'] != 'GB' && $this->address['country'] == $country['alpha-2']):*/?>selected="selected"<?php /*endif;*/?>><?/*=Tpl::out($country['name'])*/?></option>
                              <?php /*endforeach; */?>
                          </select>
                      </div>
                  </div>

                  <div class="form-actions block-foot">
                      <button class="btn btn-lg btn-primary" type="submit">Save address</button>
                  </div>

              </form>
          </div>
      </section>
      -->

      <section class="container">
          <h3 class="collapsed" data-toggle="collapse" data-target="#discord-content">Discord</h3>
          <div id="discord-content" class="content content-dark clearfix collapse">
              <?php if(empty($this->discordAuthProfile)): ?>
              <form id="discordSaveForm" action="/profile/discord/update" method="post" role="form">
                  <div class="ds-block">
                      <div class="form-group">
                          <label>Discord username:
                              <br><small>For the discord server details, ask in chat. Remember to add your username and id. e.g. Jimmy#999</small>
                          </label>
                          <input class="form-control" type="text" name="discordname" value="<?=Tpl::out($this->user['discordname'])?>" placeholder="Discord username and id. e.g. Jimmy#999" />
                      </div>
                      <div>Or <a href="/profile/connect/discord">connect to discord</a> directly!</div>
                  </div>
                  <div class="form-actions block-foot">
                      <button class="btn btn-primary" type="submit">Save details</button>
                  </div>
              </form>
              <?php else: ?>
              <div class="ds-block">
                  <div class="form-group">
                      <label>Discord username <span class="icon-required">read only</span>
                          <br><small>Using discord authentication details.</small>
                      </label>
                      <input class="form-control" readonly="readonly" type="text" value="<?=Tpl::out($this->discordAuthProfile['authDetail'])?>" />
                  </div>
              </div>
              <?php endif; ?>
          </div>
      </section>

  </div>

  <?php include 'seg/foot.php' ?>
  <?php include 'seg/tracker.php' ?>
  <script src="<?=Config::cdnv()?>/web.js"></script>
  
</body>
</html>