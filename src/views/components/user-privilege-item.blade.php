<tr>
  <td>
    <label onclick="pv_toggle_mod($(this).closest('tr'))">{{ $item->name ?? '' }}</label>
    <input type="hidden" name="privileges[{{ $idx ?? '' }}][module_id]" value="{{ $item->id ?? '' }}" />
    <input type="hidden" name="privileges[{{ $idx ?? '' }}][name]" value="{{ $item->name ?? '' }}" />
  </td>
  <td>
    <div class="align-center">
      <input type="hidden" name="privileges[{{ $idx ?? '' }}][list]" value="0" />
      <input type="checkbox" class="privilege-list" name="privileges[{{ $idx ?? '' }}][list]" value="1"
        {{ isset($user->id) && $user->getPrivilege($item->id, 'list') ? ' checked' : '' }}/>
    </div>
  </td>
  <td>
    <div class="align-center">
      <input type="hidden" name="privileges[{{ $idx ?? '' }}][create]" value="0" />
      <input type="checkbox" class="privilege-create" name="privileges[{{ $idx ?? '' }}][create]" value="1"
        {{ isset($user->id) && $user->getPrivilege($item->id, 'create') ? ' checked' : '' }}/>
    </div>
  </td>
  <td>
    <div class="align-center">
      <input type="hidden" name="privileges[{{ $idx ?? '' }}][update]" value="0" />
      <input type="checkbox" class="privilege-update" name="privileges[{{ $idx ?? '' }}][update]" value="1"
        {{ isset($user->id) && $user->getPrivilege($item->id, 'update') ? ' checked' : '' }}/>
    </div>
  </td>
  <td>
    <div class="align-center">
      <input type="hidden" name="privileges[{{ $idx ?? '' }}][delete]" value="0" />
      <input type="checkbox" class="privilege-delete" name="privileges[{{ $idx ?? '' }}][delete]" value="1"
        {{ isset($user->id) && $user->getPrivilege($item->id, 'delete') ? ' checked' : '' }}/>
    </div>
  </td>
  <td>
    <div class="align-center">
      <input type="hidden" name="privileges[{{ $idx ?? '' }}][import]" value="0" />
      <input type="checkbox" class="privilege-import" name="privileges[{{ $idx ?? '' }}][import]" value="1"
        {{ isset($user->id) && $user->getPrivilege($item->id, 'import') ? ' checked' : '' }}/>
    </div>
  </td>
  <td>
    <div class="align-center">
      <input type="hidden" name="privileges[{{ $idx ?? '' }}][export]" value="0" />
      <input type="checkbox" class="privilege-export" name="privileges[{{ $idx ?? '' }}][export]" value="1"
        {{ isset($user->id) && $user->getPrivilege($item->id, 'export') ? ' checked' : '' }}/>
    </div>
  </td>
  <td></td>
</tr>