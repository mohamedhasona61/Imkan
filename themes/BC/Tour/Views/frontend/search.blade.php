@extends('layouts.app')
@push('css')
    <link href="{{ asset('dist/frontend/module/tour/css/tour.css?_ver=' . config('app.asset_version')) }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('libs/ion_rangeslider/css/ion.rangeSlider.min.css') }}" />
@endpush
@section('content')
    <div class="bravo_search_tour">
        <div class="bravo_banner" style="padding: 0px !important;background-color: white !important;">
            @php
                $bg = setting_item('tour_page_search_banner');
                $bgids = explode(',', $bg);
            @endphp
            <div class="bravo-form-search-slider d-none d-lg-block">
                <div class="effect">
                    <div class="owl-carousel" dir="ltr">
                        @foreach ($bgids as $id)
                            @if ($id != '')
                                <div class="item"> <img src="{{ get_file_url($id, 'full') }}" alt=""> </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            @php
                $bgMobile = setting_item('tour_page_search_banner_mobile');
                $bgmobileids = explode(',', $bgMobile);
            @endphp
            <div class="bravo-form-search-slider d-block d-lg-none ">
                <div class="effect">
                    <div class="owl-carousel" dir="ltr">
                        @foreach ($bgmobileids as $id)
                            @if ($id != '')
                                <div class="item"> <img src="{{ get_file_url($id, 'full') }}" alt=""> </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="bravo_form_search">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        @include('Tour::frontend.layouts.search.form-search') 
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            @include('Tour::frontend.layouts.search.list-item')
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript" src="{{ asset('libs/ion_rangeslider/js/ion.rangeSlider.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('module/tour/js/tour.js?_ver=' . config('app.asset_version')) }}"></script>
@endpush
