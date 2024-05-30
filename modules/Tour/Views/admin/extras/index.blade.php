@extends('admin.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb20">
            <h1 class="title-bar">{{__("Menu Extras")}}</h1>
        </div>
        @include('admin.message')
        <div class="row">
            <div class="col-md-4 mb40">
                <div class="panel">
                    <div class="panel-title">{{__("Add Extra")}}</div>
                    <div class="panel-body">
                        <form action="{{route('tour.admin.extras.store',['id'=>($row->id) ? $row->id : '-1','lang'=>request()->query('lang')])}}" method="post">
                            @csrf
                            @include('Tour::admin/extras/form',['parents'=>$rows])
                            <div class="">
                                <button class="btn btn-primary" type="submit">{{__("Add new")}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="filter-div d-flex justify-content-between ">
                    <div class="col-left">
                       
                    </div>
                    <div class="col-left">
                        <form method="get" action="{{route('tour.admin.extras.index')}} " class="filter-form filter-form-right d-flex justify-content-end" role="search">
                            <input type="text" name="s" value="{{ Request()->s }}" class="form-control" placeholder="{{__("Search by name")}}">
                            <button class="btn-info btn btn-icon btn_search" id="search-submit" type="submit">{{__('Search')}}</button>
                        </form>
                    </div>
                </div>
                <div class="panel">
                    <div class="panel-title">{{__("All Extras")}}</div>
                    <div class="panel-body">
                        <form class="bravo-form-item">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>{{__("Name")}}</th>
                                    <th class="">{{__("Actions")}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(count($rows) > 0)
                                    @foreach($rows as $row)
                                        <tr>
                                            <td class="title">
                                                <a href="{{route('tour.admin.extras.edit', ['id' => $row->id]) }}">{{$row->name}}</a>
                                            </td>
                                   
                                            <td>
                                                <a href="{{route('tour.admin.extras.edit', ['id' => $row->id]) }}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> {{__('Edit')}}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3">{{__("No data")}}</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
