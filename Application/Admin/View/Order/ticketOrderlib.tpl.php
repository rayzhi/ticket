	<empty name="list">
	  	<tr class="odd"><td valign="top" colspan="12" class="dataTables_empty">表格是空的</td></tr>
	<else />
		<volist name="list" id="vo">
			<tr id="div_{$vo.id}">
				<td class="center">
					<label>
						<input type="checkbox" class="ace" name="{$vo.id}" />
						<span class="lbl"></span>
					</label>
				</td>
				<td>{$vo.id}</td>
				<td>{$vo.nickname}</td>
				<td>{$vo.third_party_pay}</td>
				<td>{$vo.add_time}</td>
				<td>
					<div class="visible-md visible-lg hidden-sm hidden-xs action-buttons">
						<a class="green" href="<?php echo UC('Admin/Course/edit_level')?>?id={$vo.id}" title="编辑">
							<i class="icon-pencil bigger-130"></i>
						</a>							
						<a class="red" href="javascript:;" onclick="remove_vote({$vo.id}, '您确认要删除这条数据吗?')" title="删除">
							<i class="icon-trash bigger-130"></i>
						</a>
					</div>
				</td>
			</tr>
		</volist>
	</empty>