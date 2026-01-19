@if($status == 'completed')
    <span class="badge-status status-completed">Completed</span>
@elseif($status == 'pending')
    <span class="badge-status status-pending">Pending</span>
@elseif($status == 'canceled')
    <span class="badge-status status-canceled">Canceled</span>
@elseif($status == 'exchanged')
    <span class="badge-status status-exchanged">Exchanged</span>
@elseif($status == 'refunded')
    <span class="badge-status status-refunded">Refunded</span>
@elseif($status == 'partially_refunded')
    <span class="badge-status status-partially">Partially Refunded</span>
@endif