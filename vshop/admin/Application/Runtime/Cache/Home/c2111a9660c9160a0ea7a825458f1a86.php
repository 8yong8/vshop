<?php if (!defined('THINK_PATH')) exit();?>					<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr id="row_<?php echo ($vo["id"]); ?>" class="datagrid-row <?php if(($mod) == "1"): ?>datagrid-row-alt<?php endif; ?> treegrid-tr-tree_<?php echo ($pid); ?>" mod="2">
						  <td>
						   <div style="text-align:left;height:auto;" class="datagrid-cell datagrid-cell-c1-area_name">
							<span class="tree-indent"></span>
							<span class="tree-indent"></span>
							<span class="tree-icon tree-file"></span>
							<span class="tree-title"><?php echo ($vo["area_name"]); ?></span>
						   </div></td>
						  <td><?php echo ($vo["sort"]); ?></td>
						  <td>
						   <div style="text-align:left;height:auto;padding-left:8px;" class="datagrid-cell datagrid-cell-c1-none">
							  <a href="javascript:void (0)" onclick="ajax_edit(<?php echo ($vo["id"]); ?>)">编辑</a>&nbsp;&nbsp;&nbsp;&nbsp;
							  <a href="javascript:void (0)" onclick="ajax_del(<?php echo ($vo["id"]); ?>)">删除</a>
						   </div></td>
						 </tr><?php endforeach; endif; else: echo "" ;endif; ?>