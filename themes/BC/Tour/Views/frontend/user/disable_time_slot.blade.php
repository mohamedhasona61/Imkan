@extends('layouts.user')

@section('content')
    <h2 class="title-bar no-border-bottom">
        {{ __('Availability Tours') }}
    </h2>
    <div class="language-navigation">
        <div class="panel-body">
            <div class="filter-div d-flex justify-content-between ">
                <div class="col-left">
                    <form method="get" action="" class="filter-form filter-form-left d-flex flex-column flex-sm-row"
                        role="search">
                        <input type="text" name="s" value="{{ Request()->s }}"
                            placeholder="{{ __('Search by name') }}" class="form-control">&nbsp;&nbsp;
                        <button class="btn-info btn btn-icon btn_search btn-sm" type="submit">{{ __('Search') }}</button>
                    </form>
                </div>
                <div class="col-right">
                    @if ($rows->total() > 0)
                        <span
                            class="count-string">{{ __('Showing :from - :to of :total tours', ['from' => $rows->firstItem(), 'to' => $rows->lastItem(), 'total' => $rows->total()]) }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @if (count($rows))
        <div class="user-panel">
            <div class="panel-title"><strong>{{ __('Availability') }}</strong></div>
            <div class="panel-body no-padding" style="background: #f4f6f8;padding: 0px 15px;">
                <div class="row">
                    <div class="col-md-3" style="border-right: 1px solid #dee2e6;">
                        <ul class="nav nav-tabs  flex-column vertical-nav" id="items_tab" role="tablist">
                            @foreach ($rows as $k => $item)
                                <li class="nav-item event-name ">
                                   <a class="nav-link" data-id="{{ $item->id }}" data-toggle="tab"
                                        href="#calendar-{{ $item->id }}" title="{{ $item->title }}">{{ $item->title }}
                                        ({{ $item->category_tour->name }} - {{ $item->location->name }}) </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-md-9" style="background: white;padding: 15px;">
                        <div id="dates-calendar" class="dates-calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-warning">{{ __('No tours found') }}</div>
    @endif
    <div class="d-flex justify-content-center">
        {{ $rows->appends($request->query())->links() }}
    </div>
    <div id="bravo_modal_calendar" class="modal fade">
        <div class="modal-dialog modal-lg  modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Date Information') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="row form_modal_calendar form-horizontal" novalidate onsubmit="return false">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>{{ __('Date Ranges') }}</label>
                                <select class="form-control" v-model="form.date_range" id="EnabledTimeSlot">
                                    <option value="">{{ __('Select Time Slot') }}</option>
                                </select>
                            </div>
                    </form>
                    <div v-if="lastResponse.message">
                        <br>
                        <div class="alert" :class="!lastResponse.status ? 'alert-danger' : 'alert-success'">
                            @{{ lastResponse.message }}</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" class="btn btn-primary" @click="saveForm">{{ __('Save changes') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('libs/fullcalendar-4.2.0/core/main.css') }}">
    <link rel="stylesheet" href="{{ asset('libs/fullcalendar-4.2.0/daygrid/main.css') }}">
    <link rel="stylesheet" href="{{ asset('libs/daterange/daterangepicker.css') }}">

    <style>
        .event-name {
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
        }

        #dates-calendar .loading {}
    </style>
@endpush

@push('js')
    <script src="{{ asset('libs/daterange/moment.min.js') }}"></script>
    <script src="{{ asset('libs/daterange/daterangepicker.min.js?_ver=' . config('app.asset_version')) }}"></script>
    <script src="{{ asset('libs/fullcalendar-4.2.0/core/main.js') }}"></script>
    <script src="{{ asset('libs/fullcalendar-4.2.0/interaction/main.js') }}"></script>
    <script src="{{ asset('libs/fullcalendar-4.2.0/daygrid/main.js') }}"></script>

    <script>
        function getTimeSlotsAvailable(form) {
            form.target_id = lastId;
            console.log(form);
            $.ajax({
                url: '{{ route('vendor.getEnabledTimeSlots') }}',
                data: form,
                dataType: 'json',
                method: 'post',
                success: function(json) {
                    var selectElement = $('#EnabledTimeSlot');
                    selectElement.empty();
                    selectElement.append($('<option>', {
                        value: '',
                        text: '{{ __('Select Time Slot') }}'
                    }));
                    $.each(json.time_slot, function(index, timeslot) {
                        selectElement.append($('<option>', {
                            value: timeslot.id,
                            text: timeslot.start_at + ' - ' + timeslot.end_at
                        }));
                    });
                },
                error: function(e) {
                    console.log(e);
                }
            });

        }




        var calendarEl, calendar, lastId, formModal;
        $('#items_tab').on('show.bs.tab', function(e) {
            calendarEl = document.getElementById('dates-calendar');
            lastId = $(e.target).data('id');
            if (calendar) {
                calendar.destroy();
            }
            calendar = new FullCalendar.Calendar(calendarEl, {
                buttonText: {
                    today: '{{ __('Today') }}',
                },

                plugins: ['dayGrid', 'interaction'],
                header: {},
                selectable: true,
                selectMirror: false,
                allDay: false,
                editable: false,
                eventLimit: true,
                defaultView: 'dayGridMonth',
                firstDay: daterangepickerLocale.first_day_of_week,
                events: {
                    url: "{{ route('tour.vendor.availability.loadDates') }}",
                    extraParams: {
                        id: lastId,
                    }
                },
                loading: function(isLoading) {
                    if (!isLoading) {
                        $(calendarEl).removeClass('loading');
                    } else {
                        $(calendarEl).addClass('loading');
                    }
                },
                select: function(arg) {
                    formModal.show({
                        start_date: moment(arg.start).format('YYYY-MM-DD'),
                        end_date: moment(arg.end).format('YYYY-MM-DD'),
                    });
                },
                eventClick: function(info) {
                    var form = Object.assign({}, info.event.extendedProps);
                    form.start_date = moment(info.event.start).format('YYYY-MM-DD');
                    form.end_date = moment(info.event.start).format('YYYY-MM-DD');
                    formModal.show(form);
                    getTimeSlotsAvailable(form);


                },
                eventRender: function(info) {
                    $(info.el).find('.fc-title').html(info.event.title);
                    $(info.el).find('.fc-content').attr("data-html", "true").attr("title", info.event
                        .title).tooltip({
                        boundary: 'window'
                    })
                }
            });
            calendar.render();
        });

        $('.event-name:first-child a').trigger('click');

        formModal = new Vue({
            el: '#bravo_modal_calendar',
            data: {
                lastResponse: {
                    status: null,
                    message: ''
                },
                form: {
                    id: '',
                },
                formDefault: {
                    id: '',
                },


                onSubmit: false
            },
            methods: {
                show: function(form) {
                    $(this.$el).modal('show');
                    this.lastResponse.message = '';
                    this.onSubmit = false;




                    if (typeof form != 'undefined') {
                        this.form = Object.assign({}, form);


                    }
                },
                hide: function() {
                    $(this.$el).modal('hide');
                    this.form = Object.assign({}, this.formDefault);
                },
                saveForm: function() {
                    this.form.target_id = lastId;

                    var me = this;
                    var customData = {
                        target_id: this.form.target_id,
                        day: this.form.start_date,
                        time_slot_id: this.form.date_range,
                    };
                    me.lastResponse.message = '';
                    if (this.onSubmit) return;
                    this.onSubmit = true;
                    $.ajax({
                        url: '{{ route('vendor.Store_disable_time_slot') }}',
                        data: customData,
                        dataType: 'json',
                        method: 'post',
                        success: function(json) {
                            if (json.status) {
                                if (calendar)
                                    calendar.refetchEvents();
                                me.hide();
                            }
                            me.lastResponse = json;
                            me.onSubmit = false;
                        },
                        error: function(e) {
                            me.onSubmit = false;
                        }
                    });
                },

            },
            created: function() {
                var me = this;
                this.$nextTick(function() {
                    $(me.$el).on('hide.bs.modal', function() {

                        this.form = Object.assign({}, this.formDefault);
                    });

                })
            },
            mounted: function() {}
        });
    </script>
@endpush
