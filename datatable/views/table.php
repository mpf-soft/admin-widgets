<?php /* @var $this \mWidgets\datatable\Table */ ?>
<?php /* @var $column \mWidgets\datatable\columns\Basic */ ?>
<div multi-actions-key="<?= $this->dataProvider->filtersKey; ?>"
     data-token-key="<?= \mpf\web\request\HTML::get()->getCsrfKey(); ?>"
     data-token-value="<?= \mpf\web\request\HTML::get()->getCsrfValue(); ?>"
     class="m-datatable m-datatable-<?= $this->theme; ?> <?= ($this->multiSelect && count($this->multiSelectActions)) ? 'm-datatable-multiselect' : ''; ?>">
    <?php if ($this->multiSelect && count($this->multiSelectActions)) { ?>
        <div class="m-datatable-multiactions">
            <?php $this->renderPage('multiactions'); ?>
        </div>
    <?php } ?>
    <div class="m-datatable-view-options">
        <?php $this->renderPage('pages'); ?>
    </div>
    <table multiselect="<?= ($this->multiSelect && count($this->multiSelectActions)) ? 'on' : 'off'; ?>">
        <tr class="m-datatable-header">
            <?php if ($this->multiSelect && count($this->multiSelectActions)) { ?>
                <th><input type="checkbox" name="check-all"/></th>
            <?php } ?>
            <?php foreach ($this->getColumns() as $column) { ?>
                <?php if ($column->isVisible()) { ?>
                    <th <?= $column->getHeaderHtmlOptions(); ?>><?php echo $column->getHeaderCode($this); ?></th>
                <?php } ?>
            <?php } ?>
        </tr>
        <?php $visibleColumns = 0; ?>
        <tr class="m-datatable-filters">
            <?php if ($this->multiSelect && count($this->multiSelectActions)) { ?>
                <td>&nbsp;</td><?php $visibleColumns++; ?>
            <?php } ?>
            <?= \mpf\web\helpers\Form::get()->openForm(array('method' => 'GET', 'style' => 'width:auto;height:auto;min-height:0;margin:0;padding:0;border:none;')); ?>
            <?php foreach ($this->columnObjects as $name => $column) { ?>
                <?php if ($column->isVisible()) { ?> <?php $visibleColumns++; ?>
                    <td <?= $column->getFilterHtmlOptions(); ?>><?= $column->getFilter(); ?></td>
                <?php } ?>
            <?php } ?>
            <?= \mpf\web\helpers\Form::get()->closeForm(); ?>
        </tr>
        <?php if (count($this->dataProvider->getData())) { ?>
            <?php foreach ($this->dataProvider->getData() as $row) { ?>
                <tr class="m-datatable-row">
                    <?php if ($this->multiSelect && count($this->multiSelectActions)) { ?>
                        <td><input type="checkbox" name="ids[]"
                                   value="<?= $row->{$this->dataProvider->getPkKey()}; ?>"/></td>
                    <?php } ?>
                    <?php foreach ($this->columnObjects as $name => $column) { ?>
                        <?php if ($column->isVisible()) { ?>
                            <td <?= $column->getHtmlOptions(); ?>><?= $column->getValue($row, $this); ?></td>
                        <?php } ?>
                    <?php } ?>
                </tr>
            <?php } ?>
            <tr class="m-datatable-header">
                <?php if ($this->multiSelect && count($this->multiSelectActions)) { ?>
                    <th><input type="checkbox" name="check-all"/></th>
                <?php } ?>
                <?php foreach ($this->getColumns() as $column) { ?>
                    <?php if ($column->isVisible()) { ?>
                        <th <?= $column->getHeaderHtmlOptions(); ?>><?php echo $column->getHeaderCode($this); ?></th>
                    <?php } ?>
                <?php } ?>
            </tr>
        <?php } else { ?>
            <tr class="m-datatable-noitems">
                <td colspan="<?= $visibleColumns; ?>">- no items found -</td>
            </tr>
        <?php } ?>
    </table>
    <?php if ($this->multiSelect && count($this->multiSelectActions)) { ?>
        <div class="m-datatable-multiactions">
            <?php $this->renderPage('multiactions'); ?>
        </div>
    <?php } ?>
    <div class="m-datatable-view-options">
        <?php $this->renderPage('pages'); ?>
    </div>
</div>