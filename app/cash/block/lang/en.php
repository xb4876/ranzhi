<?php
/**
 * The en file of block module of RanZhi.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Tingting Dai <daitingting@xirangit.com>
 * @package     block 
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
$lang->block->depositor = 'Payment Depositor';
$lang->block->lblBlock  = 'Block';
$lang->block->admin     = 'Manage Block';
$lang->block->num       = 'Number';
$lang->block->orderBy   = 'Order';

$lang->block->availableBlocks = new stdclass();
$lang->block->availableBlocks->depositor = 'Payment Depositor';
$lang->block->availableBlocks->trade     = 'Trade';
$lang->block->availableBlocks->provider  = 'Provider';
$lang->block->availableBlocks->report    = 'Report';

$this->lang->block->orderByList->trade['id_asc']  = 'ID ASC';
$this->lang->block->orderByList->trade['id_desc'] = 'ID DESC';

$this->lang->block->typeList->trade['all']  = 'All';
$this->lang->block->typeList->trade['in']   = 'Income';
$this->lang->block->typeList->trade['out']  = 'Expense';

$this->lang->block->groupByList = new stdclass();
$this->lang->block->groupByList->trade['category'] = 'Group by category';
$this->lang->block->groupByList->trade['dept']     = 'Group by deptment';

$this->lang->block->orderByList->provider['id_asc']  = 'ID ASC';
$this->lang->block->orderByList->provider['id_desc'] = 'ID DESC';
