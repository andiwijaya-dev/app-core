@if($customer_is_online ?? 0)
  <span class="fa fa-circle cl-green"></span>
  <span>Online</span>
  <a href="?action=check-online&discussion_id={{ $discussion->id }}" class="async hmarl-05" data-push-state="0">
    <span class="fa fa-sync small selectable"></span>
  </a>
@else
  <span class="fa fa-circle"></span>
  <span>Offline</span>
  <a href="?action=check-online&discussion_id={{ $discussion->id }}" class="async hmarl-05" data-push-state="0">
    <span class="fa fa-sync small selectable"></span>
  </a>
@endif