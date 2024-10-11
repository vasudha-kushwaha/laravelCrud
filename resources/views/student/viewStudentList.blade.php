@php
    include public_path('functions.php');
@endphp

@extends('main')

@section('content')
    @if ($action == 'list')
        
        @php
            $arrayObjectsCount = count($arrayObjects); 
            //echo $action;
            //print_r($arrayObjects); 
            //exit;
            $r1 = ''; $r2 = ''; $r3 = ''; $r4 = ''; $all = ''; $deactivated = ''; $deleted = '';
        @endphp

        @switch($range)
            @case('r1')
                @php $r1 = "active"; @endphp
                @break
        
            @case('r2')
                @php $r2 = "active"; @endphp
                @break

            @case('all')
                @php $all = "active"; @endphp
                @break

            @case('deleted')
                @php $deleted = "active"; @endphp
                @break
        @endswitch        
      
        @if (($sUserType != 'guest') && ($action != 'search'))
<div class="container">
    <div class="text-center">
        <div class="">
        <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link mt-1 {{$r1}}" href="{{route('listStudent', ['listView' => $listView, 'range' => 'r1'])}}">Recent Updated</a>
        </li>
        <li class="nav-item">
            <a class="nav-link mt-1 {{$r2}}" href="{{route('listStudent', ['listView' => $listView, 'range' => 'r2'])}}">Recent Updated</a>
        </li>
        <li class="nav-item">
            <a class="nav-link mt-1 {{$all}}" href="{{route('listStudent', ['listView' => $listView, 'range' => 'all'])}}">All</a>
        </li>
        <li class="nav-item">
            <a class="nav-link mt-1 {{$deleted}}" href="{{route('listStudent', ['listView' => $listView, 'range' => 'deleted'])}}">Deleted</a>
        </li>
        <li class="nav-item float-right">
            <span class="nav-link mt-1 ">
                <a class="mx-1" href="{{route('listStudent', ['listView' => 'card', 'range' => $range ])}}"><span class="fa fa-th-large fa-lg"></span> card</a>
                <a class="mx-1" href="{{route('listStudent', ['listView' => 'table', 'range' => $range ])}}"><span class="fa fa-table fa-lg"></span> table</a>
            </span>
        </li>
        </ul>
        </div>
    </div>
    <div class="row p-1 bg-white justify-content-between">
        <div class="col-auto">
            <span class="bigger">Students</span>&nbsp;<span class="badge badge-primary badge-counter float-right small ">{{$arrayObjectsCount}}</span>
        </div>
        @if ($isAdd)
        <div class="col-auto">
            <a class="btn btn-sm btn-add" href="{{route('addStudent')}}"><span class="fa fa-plus fa-lg"></span> New</a>
        </div>
        @endif
    </div>
    @if ($arrayObjectsCount > 0)

        <!-- Before showing entries, show table header if table view -->
        @if ( $listView == 'table') 
        <!-- table view -->
    <div style="overflow-x:auto;">
        <table class="table table-responsive-sm table-sm table-bordered  alternate datatablejs">
            <thead><tr class="thead-light"><th>Act</th><th>ID- Name</th><th>Status</th><th>Type</th><th>JD</th></tr></thead>
            <tbody>
            @php 
                //$sDataTableColumnDefs = "columnDefs: [{orderable: false, targets: 0}, {orderable: false, targets: 1}, {orderable: false, targets: 2}, {orderable: false, targets: 3}, {orderable: false, targets: 4}, ],"; 
            @endphp
        @endif
        <!-- Start loop to show cards/lines/rows -->
        @php
            $maxCardsInRow = 3;
            $cardWidthInGrid = intval(12/$maxCardsInRow);
        @endphp

        @for ($i=0, $cardsInRow=0; $i < $arrayObjectsCount; $i++)
            @php
                $id = $arrayObjects[$i]->id;
                $sTokenDetails = $arrayObjects[$i]->sTokenDetails;
                $sTokenEdit = $arrayObjects[$i]->sTokenEdit;

                $sTitle = $arrayObjects[$i]->sName;
                $edStatus = $listEnumValues['student']['edStatus'][$arrayObjects[$i]->edStatus];
                $erType = $listEnumValues['student']['erType'][$arrayObjects[$i]->erType];
                $id_jd = $arrayObjects[$i]->id_jd;
                $sButtons = "";
            @endphp

            @if ($listView == 'card')
                <!-- Start row at every first card -->
                @if ($cardsInRow == 0)
        <div class="card-deck">
                @endif
                
            <div class="card m-1 shadow bg-white rounded-lg col-md-{$cardWidthInGrid}">
                <div class="card-body">
                    <div class="card-title">
                        <span class="fas fa-bookmark fa-lg">&emsp;</span><b>{{$sTitle}}</b>
                        
                        <span class="float-right text-nowrap">

                        @if ($isDetail == true)
                            @php 
                                $sTokenDetailAndId = $sTokenDetails.$id;
                                $id = $id;
                            @endphp
                            <a class='mr-1' href="{{route('detailStudent', ['id' => $sTokenDetailAndId])}}"><span class='fa fa-external-link'></span> Detail</a>
                        @endif

                        @if ($isEdit == true)
                            @php  
                                $sTokenEditAndId = $sTokenEdit.$id;
                                $id = $id;
                            @endphp
                            <a class='mr-1' href="{{route('editStudent', ['id' => $sTokenEditAndId])}}"><span class='fa fa-pencil'></span> Edit</a>
                        @endif

                        </span>
                    </div>
                    <div class="card-text">
                        <div class="row my-2"><div class="col-3 small-heading">Status</div><div class="col-9" style="white-space: pre-wrap">{{$edStatus}}</div></div>
                        <div class="row my-2"><div class="col-3 small-heading">Type</div><div class="col-9" style="white-space: pre-wrap">{{$erType}}</div></div>
                        <div class="row my-2"><div class="col-3 small-heading">JD</div><div class="col-9" style="white-space: pre-wrap">{{$id_jd}}</div></div>
                    </div>
                </div>
            </div>
                <!-- //card -->
                <!-- Close row at every 3rd card or last card in list -->
                @php $cardsInRow++; @endphp
                @if (($cardsInRow == $maxCardsInRow) || (($i+1) == $arrayObjectsCount))
                    @php $cardsInRow = 0; @endphp            
        </div>
            @endif

            @elseif ($listView == 'table') 
                <!-- table view -->
                @php $idx = $i+1; @endphp
        <tr>
            <td>
                <span class="text-nowrap">
                    @if ($isDetail == true)
                        @php 
                            $sTokenDetailAndId = $sTokenDetails.$id;
                            $id = $id;
                        @endphp
                        <a class='mr-1' href="{{route('detailStudent', ['id' => $sTokenDetailAndId])}}"><span class='fa fa-external-link'></span> Detail</a>
                    @endif

                    @if ($isEdit == true)
                        @php  
                            $sTokenEditAndId = $sTokenEdit.$id;
                            $id = $id;
                        @endphp
                        <a class='mr-1' href="{{route('editStudent', ['id' => $sTokenEditAndId])}}"><span class='fa fa-pencil'></span> Edit</a>
                    @endif           
                </span>
            </td>
        <td>{{$arrayObjects[$i]->id}}- <b>{{$sTitle}}</b></td><td>{{$edStatus}}</td><td>{{$erType}}</td><td>{{$id_jd}}</td></tr>

            @else 
            <!-- list view -->   
        <div class="row border-top small">
            <div class="col-md-2">{{$arrayObjects[$i]->id}}- <b>{{$sTitle}}</b><span class="float-right text-nowrap">{!! $sButtons !!}</span></div>
            <div class="col-md-2">{{$edStatus}}</div>
            <div class="col-md-2">{{$erType}}</div>
            <div class="col-md-2">{{$id_jd}}</div>
        </div>
            @endif
            <!-- view -->
        @endfor

        <!-- Close table if table view -->
        @if ($listView == 'table') 
            <!-- table view -->
            </tbody>
        </table>
   </div>
        @endif
    @else             
    <div class="col-md-12 text-center">
        <strong>No entries found !</strong>
    </div>
    @endif
</div>
        @endif
    @endif
@endsection
