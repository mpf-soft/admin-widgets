<?php /* @var $this mWidgets\datatable\Table */ ?>
<?php foreach ($this->multiSelectActions as $name => $details) { ?>
    <?php $details = is_array($details) ? $details : array('label' => $details); ?>
    <?php $htmlOptions = isset($details['htmlOptions']) ? $details['htmlOptions'] : array(); ?>
    <?php $htmlOptions['data-action'] = $name; ?>
    <?php foreach (array('url', 'js', 'confirmation', 'shortcut') as $key) { ?>
        <?php if (isset($details[$key])) { ?>
            <?php $htmlOptions['data-' . $key] = $details[$key]; ?>
        <?php } ?>
    <?php } ?>

    <?= \mpf\web\helpers\Html::get()->link('#', (isset($details['icon']) ? \mpf\web\helpers\Html::get()->image($details['icon'], $details['label']) : '') . '<span>' . $details['label'] . '</span>', $htmlOptions); ?>
<?php } ?>