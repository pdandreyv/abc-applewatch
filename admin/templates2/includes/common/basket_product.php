<tr data-i="<?=$q['i']?>">
	<td><input class="form-control" style="width:40px; text-align: right" name="basket[products][<?=$q['i']?>][id]" value="<?=@$q['id']?>" /></td>
	<td style="width: 90%"><input class="form-control" style="min-width:200px;" name="basket[products][<?=$q['i']?>][name]" value="<?=@$q['name']?>" /></td>
	<td><input class="form-control" style="width:80px; text-align: right" type="number" name="basket[products][<?=$q['i']?>][count]" value="<?=@$q['count']?>" /></td>
	<td><input class="form-control" style="width:100px; text-align: right" name="basket[products][<?=$q['i']?>][price]" value="<?=@$q['price']?>" /></td>
	<td><a href="#" class="js_table_del"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-square"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="9" x2="15" y2="15"></line><line x1="15" y1="9" x2="9" y2="15"></line></svg></a></td>
</tr>