<?php
/**
 * The buildlist view file of file module of RanZhi.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     file 
 * @version     $Id: buildlist.html.php 3138 2015-11-09 07:32:18Z chujilu $
 * @link        http://www.ranzhico.com
 */
?>
<table class='table-1 f-12px' align='center'>
  <caption><?php echo $lang->file->browse;?></caption>
  <tr>
    <th><?php echo $lang->file->id;?></th>
    <th><?php echo $lang->file->title;?></th>
    <th><?php echo $lang->file->extension;?></th>
    <th><?php echo $lang->file->size;?></th>
    <th><?php echo $lang->file->createdDate;?></th>
    <th><?php echo $lang->file->public;?></th>
    <th><?php echo $lang->file->downloads;?></th>
    <th><?php echo $lang->file->download;?></th>
  </tr>
  <?php $i = 1;?>
  <?php foreach($files as $file):?>
  <tr class='text-center'>
    <td><?php echo $i ++;?></td>
    <th class='text-left'><?php echo html::a($this->createLink('file', 'download', "id=$file->id"), $file->title, $file->isImage ? "target='_blank'" : '');?></th>
    <td><?php echo $file->extension;?></td>
    <td><?php echo $file->size;?></td>
    <td><?php echo $file->createdDate;?></td>
    <td><?php $file->public or (!$file->public and $app->user->account != 'guest') ? print($lang->file->publics[$file->public]) : print(html::a($this->createLink('user', 'login'), $lang->file->publics[$file->public]));?></td>
    <td><?php echo $file->downloads;?></td>
    <td><?php echo html::a($this->createLink('file', 'download', "id=$file->id"), $lang->file->download, $file->isImage ? "target='_blank' class='red'" : "class='red'");?></td>
  </tr>
  <?php endforeach;?>
</table>

