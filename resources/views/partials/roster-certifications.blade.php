<!--AAS/RAAS-->
@if ($member->fss == "0")
    <td align="center"><i class="fa fa-times-circle icon-no-cert"></i></td>
@elseif ($member->fss == "1")
    <td align="center"><i class="fa fa-user-circle icon-mentor"></i></td>
@elseif ($member->fss == "2")
    <td align="center"><i class="fa fa-plus-circle icon-solo"></i></td>
@elseif ($member->fss == "3")
    <td align="center"><i class="fa fa-check-circle icon-certified"></i></td>
@else
    <td align="center"><i class="fa fa-exclamation-triangle icon-error"></i></td>
@endif

<!--Delivery/Ground-->
<td align="center">
    @if ($member->delgnd == "0")
        <i class="fa fa-times-circle icon-no-cert"></i>
    @elseif ($member->delgnd == "1")
        <i class="far fa-user-circle icon-mentor"></i>
    @elseif ($member->delgnd == "3")
        @if ($member->delgnd_t2 != "3")
            <i class="far fa-check-circle icon-certified"></i>
        @endif
    @endif

    @if ($member->delgnd_t2 == "1")
        <i class="fa fa-user-circle icon-mentor"></i>
    @elseif ($member->delgnd_t2 == "3")
        <i class="fa fa-check-circle icon-certified"></i>
    @endif
</td>

<!--Tower-->
<td align="center">
    @if ($member->twr == "0")
        <i class="fa fa-times-circle icon-no-cert"></i>
    @elseif ($member->twr == "1")
        <i class="far fa-user-circle icon-mentor"></i>
    @elseif ($member->twr == "2")
        <i class="fa fa-minus-circle icon-solo"></i>
    @elseif ($member->twr == "3")
        @if ($member->twr_t2 != "3")
            <i class="far fa-check-circle icon-certified"></i>
        @endif
    @endif

    @if ($member->twr_t2 == "1")
        <i class="fa fa-user-circle icon-mentor"></i>
    @elseif ($member->twr_t2 == "2")
        <i class="fa fa-plus-circle icon-solo"></i>
    @elseif ($member->twr_t2 == "3")
        <i class="fa fa-check-circle icon-certified"></i>
    @endif
</td>

<!--Departure-->
<td align="center">
    @if ($member->dep == "0")
        <i class="fa fa-times-circle icon-no-cert"></i>
    @elseif ($member->dep == "1")
        <i class="fa fa-user-circle icon-mentor"></i>
    @elseif ($member->dep == "2")
        <i class="fa fa-plus-circle icon-solo"></i>
    @elseif ($member->dep == "3")
        <i class="fa fa-check-circle icon-certified"></i>
    @endif
</td>

<!--Approach-->
<td align="center">
    @if ($member->app == "0")
        <i class="fa fa-times-circle icon-no-cert"></i>
    @elseif ($member->app == "1")
        <i class="far fa-user-circle icon-mentor"></i>
    @elseif ($member->app == "2")
        <i class="fa fa-minus-circle icon-solo"></i>
    @elseif ($member->app == "3")
        @if ($member->app_t2 != "3")
            <i class="far fa-check-circle icon-certified"></i>
        @endif
    @endif

    @if ($member->app_t2 == "1")
        <i class="fa fa-user-circle icon-mentor"></i>
    @elseif ($member->app_t2 == "2")
        <i class="fa fa-plus-circle icon-solo"></i>
    @elseif ($member->app_t2 == "3")
        <i class="fa fa-check-circle icon-certified"></i>
    @endif
</td>

<!--Centre-->
@if ($member->ctr == "0")
    <td align="center"><i class="fa fa-times-circle icon-no-cert"></i></td>
@elseif ($member->ctr == "1")
    <td align="center"><i class="fa fa-user-circle icon-mentor"></i></td>
@elseif ($member->ctr == "2")
    <td align="center"><i class="fa fa-plus-circle icon-solo"></i></td>
@elseif ($member->ctr == "3")
    <td align="center"><i class="fa fa-check-circle icon-certified"></i></td>
@else
    <td align="center"><i class="fa fa-exclamation-triangle icon-error"></i></td>
@endif
