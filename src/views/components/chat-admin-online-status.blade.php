@if($customer_is_online ?? 0)
  <a href="?action=check-online&discussion_id={{ $discussion->id }}" class="async hmarl-05" data-push-state="0">
    <span class="fa fa-circle cl-green"></span>
    <span class="hidden-sm">Online</span>
  </a>
@else
  <a href="?action=check-online&discussion_id={{ $discussion->id }}" class="async hmarl-05" data-push-state="0">
    <span class="fa fa-circle"></span>
    <span class="hidden-sm">Offline</span>
  </a>
@endif