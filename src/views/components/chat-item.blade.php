<div class="chat-item chat-item-{{ $item->id }}{{ isset($highlight) && $highlight ? ' highlight' : '' }}"
     onclick="$.fetch('/chat/{{ $item->id }}')">
  <table cellspacing="3">
    <tr>
      <td valign="top">
        <span class="img unloaded" style="width:28px;height:28px" data-src="{{ $item->image_url }}"></span>
      </td>
      <td valign="top" style="width:100%;text-overflow: ellipsis;overflow:hidden">
        <strong>{{ $item->title }}</strong>
        @if(isset($item->latest_message->id))
          <p class="vmart-0 less">{{ substr($item->latest_message->message, 0, 30) }}</p>
        @endif
      </td>
      <td valign="top" style="white-space: nowrap">
        @if(isset($item->latest_message->id))
          <span class="" style="width:.5em;height:.5em;border-radius:1em;background:green"></span>
          <small>{{ $item->latest_message->created_at->diffForHumans() }}</small>
        @endif
      </td>
    </tr>
  </table>
</div>