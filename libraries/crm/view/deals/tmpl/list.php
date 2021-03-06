<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); 
$app = JFactory::getApplication();
?>
<thead>
    <tr>
        <th class="checkbox_column"><input rel="tooltip" title="<?php echo CRMText::_('COBALT_CHECK_ALL_ITEMS'); ?>" data-placement="bottom" type="checkbox" onclick="selectAll(this);" /></th>
        <th class="name" ><div class="sort_order"><a href="javascript:void(0);" class="d.name" onclick="sortTable('d.name',this)"><?php echo ucwords(CRMText::_('COBALT_DEALS_NAME')); ?></a></div></th>
        <th class="company"><div class="sort_order"><a href="javascript:void(0);" class="c.name" onclick="sortTable('c.name',this)"><?php echo ucwords(CRMText::_('COBALT_DEALS_COMPANY')); ?></a></div></th>
        <th class="amount" ><div class="sort_order"><a href="javascript:void(0);" class="d.amount" onclick="sortTable('d.amount',this)"><?php echo ucwords(CRMText::_('COBALT_DEALS_AMOUNT')); ?></a></div></th>
        <th class="status" ><div class="sort_order"><a href="javascript:void(0);" class="d.status_id" onclick="sortTable('d.status_id',this)"><?php echo ucwords(CRMText::_('COBALT_DEALS_STATUS')); ?></a></div></th>
        <th class="stage" ><div class="sort_order"><a href="javascript:void(0);" class="d.stage_id" onclick="sortTable('d.stage_id',this)"><?php echo ucwords(CRMText::_('COBALT_DEALS_STAGE')); ?></a></div></th>
        <th class="source" ><div class="sort_order"><a href="javascript:void(0);" class="d.source_id" onclick="sortTable('d.source_id',this)"><?php echo ucwords(CRMText::_('COBALT_DEAL_SOURCE')); ?></a></div></th>
        <th class="expected_close" ><div class="sort_order"><a href="javascript:void(0);" class="d.expected_close" onclick="sortTable('d.expected_close',this)"><?php echo ucwords(CRMText::_('COBALT_DEALS_EXPECTED_CLOSE')); ?></a></div></th>
        <th class="actual_close" ><div class="sort_order"><a href="javascript:void(0);" class="d.actual_close" onclick="sortTable('d.actual_close',this)"><?php echo ucwords(CRMText::_('COBALT_DEALS_ACTUAL_CLOSE')); ?></a></div></th>
        <th class="contacts" >&nbsp;</th>
    </tr>
</thead>
<tbody id="list">
<?php
    $stages = CobaltHelperDeal::getStages(null,TRUE,FALSE);
    $statuses = CobaltHelperDeal::getStatuses(null,true);
    $sources = CobaltHelperDeal::getSources(null);
    $users = CobaltHelperUsers::getUsers(null,TRUE);
	$n = count($this->dealList);
	$k = 0;
		for($i=0;$i<$n;$i++) {
			$deal = $this->dealList[$i];
			$k = $i%2;
            $entryView = CobaltHelperView::getView('deals','entry','phtml');
            $entryView->deal = $deal;
            $entryView->stages = $stages;
            $entryView->statuses = $statuses;
            $entryView->sources = $sources;
            $entryView->users = $users;
            $entryView->k = $k;
            echo $entryView->render();
        }
?>
</tbody>
<tfoot>
    <tr>
       <td colspan="20"><?php echo $this->pagination->getListFooter(); ?></td>
    </tr>
</tfoot>
<script type="text/javascript">
    var total = <?php echo $this->total; ?>;
    jQuery("#deals_matched").html(total);
    window.top.window.assignFilterOrder();
</script>