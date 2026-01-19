@if($status == 'completed')
    <span class="badge-completed">Completed</span>
@elseif($status == 'pending')
    <span class="badge-pending">Pending</span>
@elseif($status == 'canceled')
    <span class="badge-canceled">Canceled</span>
@elseif($status == 'exchanged')
    <span class="badge-exchanged">Exchanged</span>
@elseif($status == 'refunded')
    <span class="badge-refunded">Refunded</span>
@elseif($status == 'partially_refunded')
    <span class="badge-partially-refunded">Partially Refunded</span>
@endif