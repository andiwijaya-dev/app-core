@extends('andiwijaya::cms-list')

@section('detail')
  @if(isset($item))
    @component('andiwijaya::components.log-modal', [ 'item'=>$item ])@endcomponent
  @endif
@endsection