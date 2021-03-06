<?php
namespace Destiny;
use Destiny\Commerce\SubscriptionStatus;
use Destiny\Common\Utils\Date;
use Destiny\Common\Utils\Tpl;
use Destiny\Common\Config;
?>
<!DOCTYPE html>
<html>
<head>
    <title><?=Tpl::title($this->title)?></title>
    <?php include 'seg/meta.php' ?>
    <link href="<?=Config::cdnv()?>/web.css" rel="stylesheet" media="screen">
</head>
<body id="gifts" class="no-contain">
    <div id="page-wrap">

        <div id="alerts-container"></div>
        <?php include 'seg/nav.php' ?>
        <?php include 'seg/alerts.php' ?>
        <?php include 'menu.php' ?>
        <?php include 'profile/userinfo.php' ?>

        <section class="container">

            <h3 data-toggle="collapse" data-target="#gifts-content">Gifts</h3>
            <div id="gifts-content" class="content collapse in">
                <div class="content-dark clearfix">

                    <?php if(!empty($this->gifts)): ?>
                        <table class="grid">
                            <thead>
                            <tr>
                                <td>Subscription</td>
                                <td>To</td>
                                <td>Status</td>
                                <td>Created</td>
                                <td>Ending</td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($this->gifts as $subscription): ?>
                                <tr>
                                    <td>
                                        Tier <?=Tpl::out($subscription['subscriptionTier'])?>
                                        <?php if($subscription['recurring'] == '1'): ?>(Recurring)<?php endif ?>
                                    </td>
                                    <td><?=Tpl::out($subscription['username'])?></td>
                                    <td>
                                        <?php if(strcasecmp($subscription['status'], SubscriptionStatus::ACTIVE) === 0): ?>
                                            <span class="badge badge-success"><?=Tpl::out($subscription['status'])?></span>
                                        <?php else: ?>
                                            <span><?=Tpl::out($subscription['status'])?></span>
                                        <?php endif ?>
                                    </td>
                                    <td><?=Tpl::moment(Date::getDateTime($subscription['createdDate']), Date::STRING_FORMAT_YEAR)?></td>
                                    <td><?=Tpl::moment(Date::getDateTime($subscription['endDate']), Date::STRING_FORMAT_YEAR)?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="ds-block">
                            <p>No subscriptions</p>
                        </div>
                    <?php endif ?>

                </div>
            </div>

        </section>
    </div>

    <?php include 'seg/foot.php' ?>
    <?php include 'seg/tracker.php' ?>
    <script src="<?=Config::cdnv()?>/web.js"></script>

</body>
</html>
