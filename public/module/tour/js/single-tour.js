(function ($) {
    new Vue({
        el: '#bravo_tour_book_app',
        data: {
            id: '',
            extra_price: [],
            timeSlots: [],
            person_types: null,
            // start of my new updates
            person_types_length: 0,
            person_types_temp: null,
            // end of my new updates
            selectedPersonType: null,
            message: {
                content: '',
                type: false
            },
            
            selectedExtraPrices:[],
            selectedItems: [],
            selectedMenus: [],
            menuCounts: [],

            menu_price: 0,
            extra_menus: [],

            menus: [],
            html: '',
            onSubmit: false,
            start_date: '',
            time_slot: '',
            start_date_html: '',
            step: 1,
            guests: 1,
            tourPrice: 1,
            price: 0,
            total_price_before_fee: 0,
            total_price_fee: 0,
            max_guests: 1,
            start_date_obj: '',
            duration: 0,
            allEvents: [],
            buyer_fees: [],

            is_form_enquiry_and_book: false,
            enquiry_type: 'book',
            enquiry_is_submit: false,
            enquiry_name: "",
            enquiry_email: "",
            enquiry_phone: "",
            enquiry_note: "",
            ItalyExrtasID: '',
            EnableItalyExrtas: false,
            NewTermCountTemp: '',
        },
        watch: {
            extra_price: {
                handler: function f() {
                    this.step = 1;
                },
                deep: true
            },
            start_date() {
                this.step = 1;
            },
            guests() {
                this.step = 1;
            },
            person_types: {
                handler: function f() {
                    this.step = 1;
                },
                deep: true
            },
            start_date() {
                this.step = 1;
                var me = this;
                var startDate = new Date(me.start_date).getTime();
                for (var ix in me.allEvents) {
                    var item = me.allEvents[ix];
                    var cur_date = new Date(item.start).getTime();
                    if (cur_date === startDate) {
                        if (item.person_types != null) {
                            this.person_types = {};
                            for (let i = 0; i < item.person_types.length; i++) {
                                let type = item.person_types[i];
                                this.person_types[type.name] = type;
                            }
                            this.selectedPersonType = Object.values(this.person_types)[0];

                        } else {
                            this.person_types = null;
                            this.selectedPersonType = null;
                        }
                        me.max_guests = parseInt(item.max_guests);
                        me.price = parseFloat(item.price);
                    }
                }

            },

        },
        computed: {
            total_price: function () {
                var me = this;
                if (me.start_date !== "") {
                    var total = 0;
                    var total_guests = 0;
                    var startDate = new Date(me.start_date).getTime();

                    var startDay = new Date(me.start_date);
                    var dayOfWeek = startDay.getDay();
                    var dayName = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

                    // for person types
                    if (me.person_types != null) {
                        for (var ix in me.person_types) {
                            var person_type = me.person_types[ix];
                            if (dayOfWeek === 4 || dayOfWeek === 5) {
                                total += parseFloat(person_type.special_price) * parseInt(person_type.number);
                                total_guests += parseInt(person_type.number);
                                me.guests = total_guests;
                                person_type.display_price = person_type.special_price;

                            } else {

                                total += parseFloat(person_type.price) * parseInt(person_type.number);
                                total_guests += parseInt(person_type.number);
                                me.guests = total_guests;
                                person_type.display_price = person_type.price;

                            }
                        }
                    } else {
                        // for default
                        total_guests = me.guests;
                        total += me.guests * me.price;
                    }

                    for (var ix in me.extra_price) {
                        var item = me.extra_price[ix];
                        if (!item.price) continue;
                        var type_total = 0;
                        if (item.enable == 1) {
                            switch (item.type) {
                                case "one_time":
                                    type_total += parseFloat(item.price);
                                    break;
                                case "per_hour":
                                    if (me.duration > 0) {
                                        type_total += parseFloat(item.price);
                                    }
                                    break;
                                case "per_day":
                                    if (me.duration > 0) {
                                        type_total += parseFloat(item.price) * Math.ceil(parseFloat(me.duration) / 24);
                                    }
                                    break;
                            }
                            if (typeof item.per_person !== "undefined") {
                                type_total = type_total * total_guests;
                            }
                            total += type_total;
                        }
                    }



                    this.total_price_before_fee = total;


                    return total;
                }
                return 0;
            },
            total_price_html: function () {
                if (!this.total_price) return '';
                return window.bravo_format_money(Number(this.total_price) + Number(this.tourPrice) + Number(this.menu_price));
            },

            getImageURL() {
                return function (imagePath) {
                    return "https://imkanboat.com/" + imagePath;
                };
            },


            daysOfWeekDisabled() {
                var res = [];

                for (var k in this.open_hours) {
                    if (typeof this.open_hours[k].enable == 'undefined' || this.open_hours[k].enable != 1) {

                        if (k == 7) {
                            res.push(0);
                        } else {
                            res.push(k);
                        }
                    }
                }
                return res;
            },
            pay_now_price: function () {
                if (this.is_deposit_ready) {
                    var total_price_depossit = 0;
                    var tmp_total_price = Number(this.total_price) + Number(this.tourPrice) + Number(this.menu_price);
                    var deposit_fomular = this.deposit_fomular;
                    if (deposit_fomular === "deposit_and_fee") {
                        tmp_total_price = this.total_price_before_fee;
                    }
                    switch (this.deposit_type) {
                        case "percent":
                            total_price_depossit = tmp_total_price * this.deposit_amount / 100;
                            break;
                        default:
                            total_price_depossit = this.deposit_amount;
                    }
                    if (deposit_fomular === "deposit_and_fee") {
                        total_price_depossit = total_price_depossit + this.total_price_fee;
                    }

                    return total_price_depossit
                }
                return this.total_price;
            },
            pay_now_price_html: function () {
                return window.bravo_format_money(this.pay_now_price);
            },
            is_deposit_ready: function () {
                if (this.deposit && this.deposit_amount) return true;
                return false;
            }
        },
        created: function () {
            for (var k in bravo_booking_data) {
                this[k] = bravo_booking_data[k];
            }
        },
        mounted() {
            var me = this;
            var options = {
                singleDatePicker: true,
                showCalendar: false,
                sameDate: true,
                autoApply: true,
                disabledPast: true,
                dateFormat: bookingCore.date_format,
                enableLoading: true,
                showEventTooltip: true,
                classNotAvailable: ['disabled', 'off'],
                disableHightLight: true,
                minDate: this.minDate,
                opens: bookingCore.rtl ? 'right' : 'left',
                locale: {
                    direction: bookingCore.rtl ? 'rtl' : 'ltr',
                    firstDay: daterangepickerLocale.first_day_of_week
                },
                isInvalidDate: function (date) {
                    for (var k = 0; k < me.allEvents.length; k++) {
                        var item = me.allEvents[k];
                        if (item.start == date.format('YYYY-MM-DD')) {
                            return item.active ? false : true;
                        }
                    }
                    return false;
                },
                addClassCustom: function (date) {
                    for (var k = 0; k < me.allEvents.length; k++) {
                        var item = me.allEvents[k];
                        if (item.start == date.format('YYYY-MM-DD') && item.classNames !== undefined) {
                            var class_names = "";
                            for (var i = 0; i < item.classNames.length; i++) {
                                var classItem = item.classNames[i];
                                class_names += " " + classItem;
                            }
                            return class_names;
                        }
                    }
                    return "";
                }
            };

            if (typeof daterangepickerLocale == 'object') {
                options.locale = _.merge(daterangepickerLocale, options.locale);
            }
            this.$nextTick(function () {
                $(this.$refs.start_date).daterangepicker(options).on('apply.daterangepicker',
                    function (ev, picker) {
                        me.start_date = picker.startDate.format('YYYY-MM-DD');

                        me.start_date_html = picker.startDate.format(bookingCore.date_format);
                    })
                    .on('update-calendar', function (e, obj) {
                        me.fetchEvents(obj.leftCalendar.calendar[0][0], obj.leftCalendar.calendar[5][6])
                    });
            });

            this.fetchMenus();
        },
        watch: {
            start_date_html: function (newValue, oldValue) {

                var formattedDate = moment(newValue, 'MM/DD/YYYY').format('YYYY-MM-DD');

                var tourId = this.id;

                var self = this;
                $.ajax({
                    url: '/time_slot/' + formattedDate + '/' + tourId,
                    method: 'GET',
                    success: function (response) {
                        self.timeSlots = response.time_slot;
                    },
                    error: function (error) {
                    }
                });
            },

            selectedPersonType(newValue) {
                this.selectedItems = [];
                for (let index = 0; index < this.menus.length; index++) {
                    this.menus[index]['count'] = 0;
                    for (let i = 0; i < this.menus[index]['menus'].length; i++) {
                        this.menus[index]['menus'][i]['count'] = 0;
                    }
                } if (this.person_types_length !== 1) {
                    this.person_types_temp = this.person_types;
                }
                if (newValue != null) {
                    this.person_types = [newValue];
                } else {
                    this.person_types = null;
                }
                this.person_types_length = this.person_types.length;
            },
            selectedItems: function () {
                this.selectedItems.forEach(element => {
                    if (element['submenuId'] == this.ItalyExrtasID) {
                        this.EnableItalyExrtas = true
                    } else {
                        this.EnableItalyExrtas = false
                    }
                });
            },
            NewTermCountTemp: function () {
                this.NewTermCountTemp = ''
            },
        },
        methods: {
            fetchMenus() {
                $.ajax({
                    url: `/all_menus/${this.id}`,
                    method: 'GET',
                    dataType: 'json',
                    success: (response) => {
                        this.menus = response.all_menus;
                        this.extra_menus = response.extra_terms;
                        this.initializeCountProperties();
                        // my update
                        this.ItalyExrtasID = this.extra_menus[0]['menu_id'];
                    },
                    error: (error) => {
                    }
                });
            },
            initializeCountProperties() {
                this.menus.forEach(menu => {
                    menu.count = 0;
                    menu.menus.forEach(submenu => {
                        submenu.count = 0;
                        submenu.terms.forEach(term => {
                            term.count = 0;
                        });
                    });
                });
            },
            handleTotalPrice: function () {
            },
            fetchEvents(start, end) {
                var me = this;
                var data = {
                    start: start.format('YYYY-MM-DD'),
                    end: end.format('YYYY-MM-DD'),
                    id: bravo_booking_data.id,
                    for_single: 1
                };

                $.ajax({
                    url: bravo_booking_i18n.load_dates_url,
                    dataType: "json",
                    type: 'get',
                    data: data,
                    beforeSend: function () {
                        $('.daterangepicker').addClass("loading");
                    },
                    success: function (json) {
                        me.allEvents = json;
                        var drp = $(me.$refs.start_date).data('daterangepicker');
                        drp.allEvents = json;
                        var tourId = data.id;
                        this.tourPrice = json[0].price;
                        drp.renderCalendar('left', tourId);
                        if (!drp.singleDatePicker) {
                            drp.renderCalendar('right', tourId);
                        }
                        $('.daterangepicker').removeClass("loading");

                        window.tourId = tourId;
                    }.bind(this),
                    error: function (e) {
                    }
                });
            },
            formatMoney: function (m) {
                return window.bravo_format_money(m);
            },
            validate() {
                const checkedTimeSlots = this.timeSlots.filter(slot => slot.enable);
                const selectedTimes = checkedTimeSlots.map(slot => `${slot.start_at} - ${slot.end_at}`);
                this.time_slot = selectedTimes;
                const menusWithMaximum = this.menus.filter(menu => menu.check_maximum === 1);

                if (!this.start_date) {
                    this.message.status = false;
                    this.message.content = bravo_booking_i18n.no_date_select;
                    return false;
                }
                if (this.time_slot.length === 0) {
                    this.message.status = false;
                    this.message.content = bravo_booking_i18n.no_time_slot_select;
                    return false;
                }

                for (const menu of menusWithMaximum) {
                    if (menu.count < this.guests) {
                        this.message.status = false;
                        this.message.content = `Please choose ${this.guests} from the ${menu.name}`;

                        return false;
                    }
                    if (menu.count > this.guests) {
                        this.message.status = false;
                        this.message.content = `Please choose ${this.guests} from the ${menu.name}`;

                        return false;
                    }
                }


                return true;
            },

            addPersonType(type) {
                type.number = parseInt(type.number);
                if (type.number < parseInt(type.max) || !type.max) type.number += 1;
            },
            minusPersonType(type) {
                type.number = parseInt(type.number);
                if (type.number > type.min) type.number -= 1;
            },
            changePersonType(type) {
                type.number = parseInt(type.number);
                if (type.number > parseInt(type.max)) {
                    type.number = type.max;
                }
                if (type.number < type.min) {
                    type.number = type.min
                }
            },
            addGuestsType() {
                var me = this;
                if (me.guests < parseInt(me.max_guests) || !me.max_guests) me.guests += 1;
            },
            minusGuestsType() {
                var me = this;
                if (me.guests > 1) me.guests -= 1;
            },
            increaseCount(menu, submenu) {
                if (menu.check_maximum === 1 && menu.count >= this.guests) {
                    return ;
                }
                submenu.count++;
                menu.count++;

                this.menu_price = Number(this.menu_price) + Number(submenu.price);


                var existingItem = this.selectedItems.find(item => item.submenuId === submenu.id);
                if (existingItem) {
                    existingItem.count = submenu.count;
                } else {
                    var data = {
                        menuId: menu.id,
                        submenuId: submenu.id,
                        itemId: submenu.id,
                        count: submenu.count
                    };
                    this.selectedItems.push(data);
                }
                this.NewTermCountTemp = {
                    menuId: menu.id,
                    submenuId: submenu.id,
                    itemId: submenu.id,
                    count: submenu.count
                }
            },
            decreaseCount(menu, submenu) {
                if (submenu.count == 0) {
                   return;
                }
                submenu.count--;
                menu.count--;
                this.menu_price = Number(this.menu_price) - Number(submenu.price);

                var existingItem = this.selectedItems.find(item => item.submenuId === submenu.id);

                if (existingItem) {
                    existingItem.count = submenu.count;
                } else {
                    var data = {
                        menuId: menu.id,
                        submenuId: menu.id,
                        itemId: submenu.id,
                        count: submenu.count
                    };
                    this.selectedItems.push(data);
                }
                this.NewTermCountTemp = {
                    menuId: menu.id,
                    submenuId: menu.id,
                    itemId: submenu.id,
                    count: submenu.count
                }

            },

            increaseCountTerms(menu, submenu, term) {
                if (menu.check_maximum === 1 && menu.count >= this.guests) {
                    return;
                }


                menu.count++;
                submenu.count++;
                term.count++;
                this.menu_price = Number(this.menu_price) + Number(term.price);
                var existingItem = this.selectedItems.find(item => item.itemId === term.id);
                if (existingItem) {
                    existingItem.count = submenu.count;
                } else {
                    var data = {
                        menuId: menu.id,
                        submenuId: submenu.id,
                        itemId: term.id,
                        count: term.count
                    };
                    this.selectedItems.push(data);
                }
                this.NewTermCountTemp = {
                    menuId: menu.id,
                    submenuId: submenu.id,
                    itemId: term.id,
                    count: term.count
                }


            },

            decreaseCountTerms(menu, submenu, term) {
                if (term.count == 0) {
                   return;
                }
                menu.count--;
                submenu.count--;
                term.count--;

                this.menu_price = Number(this.menu_price) - Number(term.price);
                var existingItem = this.selectedItems.find(item => item.itemId === term.id);

                if (existingItem) {
                    existingItem.count = submenu.count;
                } else {
                    var data = {
                        menuId: menu.id,
                        submenuId: submenu.id,
                        itemId: term.id,
                        count: term.count
                    };
                }
                this.NewTermCountTemp = {
                    menuId: menu.id,
                    submenuId: submenu.id,
                    itemId: term.id,
                    count: term.count
                }

            },

            changeMenuCount(menu, submenu) {
                if (menu.count > this.guests) {
                    menu.count = this.guests;
                }

                if (menu.count < this.guests) {
                    submenu.count = submenu.count;
                    menu.count = menu.count;
                }
            },
           decreaseExtra(extra, index) {
                if (this.extra_menus[index].count > 0) {
                    this.extra_menus[index].count--;
                } else {
                    return;
                }
                if (this.selectedExtraPrices.length > 0) {
                    for (let i = 0; i < this.selectedExtraPrices.length; i++) {
                        if (this.selectedExtraPrices[i].id === this.extra_menus[index].id && this.selectedExtraPrices[i].menu_id === this.extra_menus[index].menu_id) {
                            this.selectedExtraPrices[i].count = this.extra_menus[index].count;
                        }
                    }
                }
                for (let x = 0; x < this.selectedExtraPrices.length; x++) {
                    if (this.selectedExtraPrices[x].count === 0) {
                        this.selectedExtraPrices.splice(x, 1)
                    }
                }
                this.NewTermCountTemp = extra;
            },


            increaseExtra(extra, index) {
                var max = this.menus[0]['menus'][0].count * 2;
                var counts = 0;
                for (let i = 0; i < this.extra_menus.length; i++) {
                    if (this.extra_menus[i].count > 0) {
                        counts = counts + this.extra_menus[i].count
                    }
                }
                if (max === counts) {
                    return;
                }
                if (this.extra_menus[index].count) {
                    this.extra_menus[index].count++;
                } else {
                    this.extra_menus[index].count = 1;
                }
                if (this.selectedExtraPrices.length > 0) {
                    var searchResult = this.selectedExtraPrices.find(obj => obj.id === this.extra_menus[index].id);
                    if (searchResult) {
                        this.selectedExtraPrices.find(obj => obj.id === this.extra_menus[index].id).count = this.extra_menus[index].count;
                    } else {
                        this.selectedExtraPrices.push(this.extra_menus[index]);
                    }
                } else {
                    this.selectedExtraPrices.push(this.extra_menus[index]);
                }
                this.NewTermCountTemp = extra;

            },

            changeExtraCount(extra) {

            },

            doSubmit: function (e) {
                e.preventDefault();
                if (this.onSubmit) return false;

                if (!this.validate()) return false;

                this.onSubmit = true;
                var me = this;
                this.message.content = '';
                if (this.step == 1) {
                    this.html = '';
                }

                var requestData = {
                    service_id: this.id,
                    service_type: 'tour',
                    start_date: this.start_date,
                    time_slot: this.time_slot,
                    person_types: this.person_types,
                    extra_price: this.extra_price,
                    guests: this.guests
                };
                const menusWithMaximum = this.menus.filter(menu => menu.check_maximum === 1);
                $.ajax({
                    url: bookingCore.url + '/booking/addToCart',
                    data: {
                        service_id: this.id,
                        service_type: 'tour',
                        start_date: this.start_date,
                        time_slot: this.time_slot,
                        person_types: this.person_types,
                        extra_price: this.extra_price,
                        guests: this.guests,
                        selectedItems: this.selectedItems,
                        menu_price: this.menu_price,
                    },
                    dataType: 'json',
                    type: 'post',

                    success: function (res) {


                        if (!res.status) {
                            me.onSubmit = false;
                        }
                        if (res.message) {
                            me.message.content = res.message;
                            me.message.type = res.status;
                        }

                        if (res.step) {
                            me.step = res.step;
                        }
                        if (res.html) {
                            me.html = res.html
                        }

                        if (res.url) {
                            window.location.href = res.url
                        }

                        if (res.errors && typeof res.errors == 'object') {
                            var html = '';
                            for (var i in res.errors) {
                                html += res.errors[i] + '<br>';
                            }
                            me.message.content = html;
                        }
                    },
                    error: function (e) {

                        me.onSubmit = false;
                        bravo_handle_error_response(e);

                        if (e.status == 401) {
                            $('.bravo_single_book_wrap').modal('hide');
                        }

                        if (e.status != 401 && e.responseJSON) {
                            me.message.content = e.responseJSON.message ? e.responseJSON.message : 'Can not booking';
                            me.message.type = false;

                        }
                    }
                });

            },
            doEnquirySubmit: function (e) {
                e.preventDefault();
                if (this.onSubmit) return false;
                if (!this.validateenquiry()) return false;
                this.onSubmit = true;
                var me = this;
                this.message.content = '';

                $.ajax({
                    url: bookingCore.url + '/booking/addEnquiry',
                    data: {
                        service_id: this.id,
                        service_type: 'tour',
                        name: this.enquiry_name,
                        email: this.enquiry_email,
                        phone: this.enquiry_phone,
                        note: this.enquiry_note,
                    },
                    dataType: 'json',
                    type: 'post',
                    success: function (res) {
                        if (res.message) {
                            me.message.content = res.message;
                            me.message.type = res.status;
                        }
                        if (res.errors && typeof res.errors == 'object') {
                            var html = '';
                            for (var i in res.errors) {
                                html += res.errors[i] + '<br>';
                            }
                            me.message.content = html;
                        }
                        if (res.status) {
                            me.enquiry_is_submit = true;
                            me.enquiry_name = "";
                            me.enquiry_email = "";
                            me.enquiry_phone = "";
                            me.enquiry_note = "";
                        }
                        me.onSubmit = false;

                    },
                    error: function (e) {
                        me.onSubmit = false;
                        bravo_handle_error_response(e);
                        if (e.status == 401) {
                            $('.bravo_single_book_wrap').modal('hide');
                        }
                        if (e.status != 401 && e.responseJSON) {
                            me.message.content = e.responseJSON.message ? e.responseJSON.message : 'Can not booking';
                            me.message.type = false;
                        }
                    }
                })
            },
            validateenquiry() {
                if (!this.enquiry_name) {
                    this.message.status = false;
                    this.message.content = bravo_booking_i18n.name_required;
                    return false;
                }
                if (!this.enquiry_email) {
                    this.message.status = false;
                    this.message.content = bravo_booking_i18n.email_required;
                    return false;
                }
                return true;
            },
            openStartDate() {
                $(this.$refs.start_date).trigger('click');
            },
            handleCheckboxChange(slotId, enable) {
                for (let i = 0; i < this.timeSlots.length; i++) {
                    if (this.timeSlots[i].id != slotId) {
                        this.timeSlots[i].enable = 0;
                    }
                }
            }
        }

    });
    $(window).on("load", function () {
        var urlHash = window.location.href.split("#")[1];
        if (urlHash && $('.' + urlHash).length) {
            var offset_other = 70
            if (urlHash === "review-list") {
                offset_other = 330;
            }
            $('html,body').animate({
                scrollTop: $('.' + urlHash).offset().top - offset_other
            }, 1000);
        }
    });

    $(".bravo-button-book-mobile").click(function () {
        $('.bravo_single_book_wrap').modal('show');
    });

    $(".bravo_detail_tour .g-faq .item .header").click(function () {
        $(this).parent().toggleClass("active");
    });

    $(".bravo_detail_tour .g-itinerary").each(function () {
        $(this).find(".owl-carousel").owlCarousel({
            items: 3,
            loop: false,
            margin: 15,
            nav: false,
            responsive: {
                0: {
                    items: 1
                },
                768: {
                    items: 2
                },
                1000: {
                    items: 3
                }
            }
        })
    });
})(jQuery);
