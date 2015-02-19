<?php /* @var $this \mpf\widgets\datatable\Table */ ?>
<span><?= $this->translate('Page'); ?></span>
<?= $this->getPageLink(1, 'first'); ?>
<?= $this->getPageLink(1 == $this->dataProvider->getCurrentPage() ? 1 : $this->dataProvider->getCurrentPage() - 1, 'previous'); ?>
<input type="text" url-template="<?= $this->dataProvider->getURLForPage(999999); ?>" name="page"
       class="mtable-page-input" value="<?= $this->dataProvider->getCurrentPage(); ?>"/>
<?= $this->getPageLink($this->dataProvider->getPagesNumber() == $this->dataProvider->getCurrentPage() ? $this->dataProvider->getPagesNumber() : $this->dataProvider->getCurrentPage() + 1, 'next'); ?>
<?= $this->getPageLink($this->dataProvider->getPagesNumber(), 'last'); ?>
<span><?= $this->translate('out of'); ?> <b><?= $this->dataProvider->getPagesNumber(); ?></b></span>
<span>&nbsp;</span>
<select name="<?= $this->dataProvider->perPageChangeKey; ?>" class="mtable-per-page-select">
    <?php foreach ($this->dataProvider->optionsPerPage as $number) { ?>
        <option <?= ($number == $this->dataProvider->perPage) ? 'selected="selected"' : ''; ?>
            value="<?= $number; ?>"><?= $number; ?></option>
    <?php } ?>
</select>
<span><?= $this->translate('per page out of'). ' <b>' . $this->dataProvider->getResultsNumber().'</b>'; ?></span>