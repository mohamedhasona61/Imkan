<style>
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type=number] {
        -moz-appearance: textfield;
    }

    input:focus {
        outline: none;
    }
</style>
<div class="bravo_single_book_wrap <?php if(setting_item('tour_enable_inbox')): ?> has-vendor-box <?php endif; ?>">
    <div class="bravo_single_book">
        <div id="bravo_tour_book_app" v-cloak>
            <?php if($row->discount_percent): ?>
                <div class="tour-sale-box">
                    <span class="sale_class box_sale sale_small"><?php echo e($row->discount_percent); ?></span>
                </div>
            <?php endif; ?>
            <div class="form-head">
                <div class="price">
                    <span class="label">
                        <?php echo e(__('from')); ?>

                    </span>
                    <span class="value">
                        <span class="onsale"><?php echo e($row->display_sale_price); ?></span>
                        <span class="text-lg"><?php echo e($row->display_price); ?></span>
                    </span>
                </div>
            </div>
            <div class="nav-enquiry" v-if="is_form_enquiry_and_book">
                <div class="enquiry-item active">
                    <span><?php echo e(__('Book')); ?></span>
                </div>
                <div class="enquiry-item" data-toggle="modal" data-target="#enquiry_form_modal">
                    <span><?php echo e(__('Enquiry')); ?></span>
                </div>
            </div>
            <div class="form-book" :class="{ 'd-none': enquiry_type != 'book' }">
                <div class="form-content">
                    <div class="form-group form-date-field form-date-search clearfix "
                        data-format="<?php echo e(get_moment_date_format()); ?>">
                        <div class="d-flex p-2  flex-wrap clearfix text-center" v-if="is_fixed_date">
                            <div class="w-50 py-3 flex-grow-1">
                                <div class="font-weight-bold"><?php echo e(__('Tour Start Date')); ?></div>
                                <span>{{ start_date_html }}</span>
                            </div>
                            <div class="w-50 py-3 flex-grow-1 border-left">
                                <div class="font-weight-bold"><?php echo e(__('Tour End Date')); ?></div>
                                <span>{{ end_date_html }}</span>
                            </div>
                            <div class="w-100 py-3 flex-grow-1 border-top">
                                <div class="font-weight-bold"><?php echo e(__('Last Booking Date')); ?></div>
                                <span>{{ last_booking_date_html }}</span>
                            </div>
                        </div>
                        <div class="date-wrapper clearfix" @click="openStartDate" v-else>
                            <div class="check-in-wrapper">
                                <label><?php echo e(__('Start Date')); ?></label>
                                <div class="render check-in-render" id="start_date">{{ start_date_html }}</div>
                            </div>
                            <i class="fa fa-angle-down arrow"></i>
                        </div>
                        <input type="text" class="start_date" ref="start_date"
                            style="height: 1px; visibility: hidden">
                    </div>
                    

                    <div class="form-section-group form-group" v-if="timeSlots.length && start_date">
                        <h4 class="form-section-title"><?php echo e(__('Choose Your Time Slot:')); ?></h4>
                        <div class="form-group" v-for="(slot, index) in timeSlots" :key="index"
                            ref="timeSlotDiv">
                            <div class="extra-price-wrap d-flex justify-content-between">
                                <div class="flex-grow-1">
                                    <label>
                                        <input type="checkbox" true-value="1" false-value="0"
                                            @change="handleCheckboxChange(slot.id,slot.enable)" v-model="slot.enable">
                                        {{ slot.start_at }} - {{ slot.end_at }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!--
                    <div class="" v-if="person_types">
                        <div class="form-group form-guest-search" v-for="(type,index) in person_types">
                            <div class="guest-wrapper d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <label>{{ type.name }}</label>
                                    <div class="render check-in-render">{{ type.desc }}</div>
                                    <div class="render check-in-render">{{ type.display_price }} <?php echo e(__('per person')); ?></div>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="input-number-group">
                                        <i class="icon ion-ios-remove-circle-outline" @click="minusPersonType(type)"></i>
                                        <span class="input"><input type="number" v-model="type.number" min="1" @change="changePersonType(type)" /></span>
                                        <i class="icon ion-ios-add-circle-outline" @click="addPersonType(type)"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->
                    
                    <div v-if="person_types&&start_date && (timeSlots[1]['enable'] || timeSlots[0]['enable'] )">
                        <div class="form-group form-guest-search" style="display: grid;">

                            <div class="guest-wrapper pb-2 pt-2 border-bottom"
                                v-for="(type, index) in person_types_temp" :key="type.name"
                                v-if="person_types_length===1">
                                <div class="flex-grow-1 w-100 ">
                                    <input type="radio" :id="'personType' + index" :value="type"
                                        v-model="selectedPersonType" />
                                    <label class="d-inline-block"
                                        :for="'personType' + index">{{ type.name }}</label>
                                    <div class="d-inline-block" class="render check-in-render">{{ type.desc }}
                                    </div>
                                    <div class=" w-100 text-left font-weight-light" class="render check-in-render">
                                        {{ type.display_price }} <?php echo e(__('per person')); ?>

                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="input-number-group">
                                        <i class="icon ion-ios-remove-circle-outline"
                                            @click="minusPersonType(type)"></i>
                                        <span class="input">
                                            <input type="number" class="w-100" v-model="type.number" min="1"
                                                @change="changePersonType(type)" />
                                        </span>
                                        <i class="icon ion-ios-add-circle-outline" @click="addPersonType(type)"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="guest-wrapper pb-2 pt-2 border-bottom" v-for="(type, index) in person_types"
                                :key="type.name" v-if="person_types_length!==1">
                                <div class="flex-grow-1 w-100 ">
                                    <input type="radio" :id="'personType' + index" :value="type"
                                        v-model="selectedPersonType" />
                                    <label class="d-inline-block"
                                        :for="'personType' + index">{{ type.name }}</label>
                                    <div class="d-inline-block" class="render check-in-render">{{ type.desc }}
                                    </div>
                                    <div class=" w-100 text-left font-weight-light" class="render check-in-render">
                                        {{ type.display_price }} <?php echo e(__('per person')); ?>

                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="input-number-group">
                                        <i class="icon ion-ios-remove-circle-outline"
                                            @click="minusPersonType(type)"></i>
                                        <span class="input">
                                            <input type="number" class="w-100" v-model="type.number"
                                                min="1" @change="changePersonType(type)" />
                                        </span>
                                        <i class="icon ion-ios-add-circle-outline" @click="addPersonType(type)"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    

                    <div class="form-section-group form-group " v-if="menus.length&&selectedPersonType">
                        <h4 class="form-section-title"><?php echo e(__('Menus:')); ?></h4>
                        <div class="form-group" v-for="(menu, index) in menus" :key="menu.id">
                            <div class="extra-price-wrap d-flex justify-content-between">
                                <div class="flex-grow-1">
                                    <?php if(app()->getLocale() === 'ar'): ?>
                                        <h5>{{ menu.name }}</h5>
                                    <?php else: ?>
                                        <h5>{{ menu.name_en }}</h5>
                                    <?php endif; ?>
                                    <div class="submenu-wrap" v-for="(submenu, subIndex) in menu.menus"
                                        :key="submenu.id">
                                        <div class="menu-counter input-number-group" v-if="submenu.terms.length==0">
                                            <i class="icon ion-ios-remove-circle-outline"
                                                @click="decreaseCount(menu,submenu)"></i>
                                            <span class="input">
                                                <input type="number" v-model="submenu.count" min="0"
                                                    style="width: 150px;" @change="changeMenuCount(menu,submenu)" />
                                                {{ NewTermCountTemp }}

                                            </span>
                                            <i class="icon ion-ios-add-circle-outline"
                                                @click="increaseCount(menu,submenu)"></i>
                                        </div>
                                        <?php if(app()->getLocale() === 'ar'): ?>
                                            <div class="menu-name">{{ submenu.name }}</div>
                                            <div class="menu-description font-weight-light pr-2 pl-2 mb-4">
                                                {{ submenu.description }}</div>
                                        <?php else: ?>
                                            <div class="menu-name">{{ submenu.name_en }}</div>
                                            <div class="menu-description font-weight-light pr-2 pl-2 mb-4">
                                                {{ submenu.description_en }}</div>
                                        <?php endif; ?>
                                        <ul style="list-style: none" class="w-100">
                                            <li v-for="(term, termIndex) in submenu.terms" :key="term.id"
                                                class="pb-2 mb-2 border-bottom">
                                                <div class="menu-counter d-flex ">
                                                    <span> <img :src="getImageURL(term.image_path)" class="rounded"
                                                            alt="Term Image" style="width:50px;height:50px" />
                                                    </span>
                                                    <span class=" mt-2 mr-2 ">
                                                        <?php if(app()->getLocale() === 'ar'): ?>
                                                            {{ term.name }}
                                                        <?php else: ?>
                                                            {{ term.name_en }}
                                                        <?php endif; ?>
                                                        <span class="d-block font-weight-light" v-if="term.price > 0">
                                                            (<?php echo e(__('Price')); ?>: {{ term.price }}
                                                            <?php echo e(__('ج.م')); ?>)
                                                        </span>
                                                    </span>

                                                </div>

                                                <div class=" input-number-group menu-counter text-center mt-3">
                                                    <i class="icon ion-ios-remove-circle-outline"
                                                        @click="decreaseCountTerms(menu,submenu,term)"></i>
                                                    <span class="input">
                                                        <input class="text-center input" type="number"
                                                            v-model="term.count" min="0"
                                                            style="width: 150px; border: none;"
                                                            @change="changeMenuCount(menu,submenu)" />
                                                        {{ NewTermCountTemp }}
                                                    </span>
                                                    <i class="icon ion-ios-add-circle-outline"
                                                        @click="increaseCountTerms(menu,submenu,term)"></i>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--
                    <div class="form-section-group form-group" v-if="extra_menus.length">
                        <h4 class="form-section-title"><?php echo e(__('Extra prices:')); ?></h4>
                        <div class="form-group" v-for="(type,index) in extra_menus">
                            <div class="extra-price-wrap d-flex justify-content-between">
                                <div class="flex-grow-1">
                                    <label><input type="checkbox" true-value="1" false-value="0" v-model="type.enable"> {{ type.name }}</label>
                                </div>
                                <div class="menu-counter">
                                    <i class="icon ion-ios-remove-circle-outline" @click="decreaseExtra(extra)"></i>
                                    <span class="input">
                                        <input type="number" v-model="extra.name" min="0" style="width: 150px;" @change="changeExtraCount(extra)" />
                                    </span>
                                    <i class="icon ion-ios-add-circle-outline" @click="increaseExtra(extra)"></i>
                                </div>
                            </div>
                        </div>
                    </div> -->
                    <div class="form-section-group form-group" v-if="extra_menus.length&&EnableItalyExrtas"
                        v-if="start_date" v-if="selectedPersonType">
                        <h4 class="form-section-title"><?php echo e(__('Extra prices:')); ?></h4>
                        <div class="form-group" v-for="(extra, index) in extra_menus">
                            <div class="extra-price-wrap">
                                <div class="flex-grow-1">
                                    <label>{{ extra.name }}</label>
                                </div>
                                <div class="menu-counter input-number-group">
                                    <i class="icon ion-ios-remove-circle-outline" @click="decreaseExtra(extra)"></i>
                                    <span class="input">
                                        <input type="number" v-model="extra.count" min="0"
                                            style="width: 150px;" @change="changeExtraCount(extra)" />
                                    </span>
                                    <i class="icon ion-ios-add-circle-outline" @click="increaseExtra(extra)"></i>
                                    
                                </div>
                            </div>
                        </div>
                    </div>



                    <div class="form-section-group form-group"
                        v-if="extra_price.length && person_types&&start_date && (timeSlots[1]['enable'] || timeSlots[0]['enable'] ) ">
                        <h4 class="form-section-title"><?php echo e(__('Extra prices:')); ?></h4>
                        <div class="form-group" v-for="(type,index) in extra_price">
                            <div class="extra-price-wrap d-flex justify-content-between">
                                <div class="flex-grow-1">
                                    <div><input type="checkbox" true-value="1" false-value="0"
                                            v-model="type.enable"> {{ type.name }}</div>
                                    <div class="render" v-if="type.price_type">({{ type.price_type }})</div>
                                </div>
                                <div class="flex-shrink-0">{{ type.price_html }}</div>
                            </div>
                        </div>
                    </div>


                    <div class="form-section-group form-group-padding" v-if="buyer_fees.length">
                        <div class="extra-price-wrap d-flex justify-content-between"
                            v-for="(type,index) in buyer_fees">
                            <div class="flex-grow-1">
                                <label>{{ type.type_name }}
                                    <i class="icofont-info-circle" v-if="type.desc" data-toggle="tooltip"
                                        data-placement="top" :title="type.type_desc"></i>
                                </label>
                                <div class="render" v-if="type.price_type">({{ type.price_type }})</div>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="unit" v-if='type.unit == "percent"'>
                                    {{ type.price }}%
                                </div>
                                <div class="unit" v-else>
                                    {{ formatMoney(type.price) }}
                                </div>
                            </div>
                        </div>
                    </div>


                </div>

                <ul class="form-section-total list-unstyled" v-if="total_price > 0">
                    <li>
                        <label><?php echo e(__('Total')); ?></label>
                        <span class="price">{{ total_price_html }}</span>
                    </li>
                    <li v-if="is_deposit_ready">
                        <label for=""><?php echo e(__('Pay now')); ?></label>
                        <span class="price">{{ pay_now_price_html }}</span>
                    </li>
                </ul>


                <div v-html="html"></div>
                <div class="submit-group">
                    <a class="btn btn-large" @click="doSubmit($event)"
                        :class="{ 'disabled': onSubmit, 'btn-success': (step == 2), 'btn-primary': step == 1 }"
                        name="submit">
                        <span><?php echo e(__('BOOK NOW')); ?></span>
                        <i v-show="onSubmit" class="fa fa-spinner fa-spin"></i>
                    </a>
                    <div class="alert-text mt10" v-show="message.content" v-html="message.content"
                        :class="{ 'danger': !message.type, 'success': message.type }"></div>
                </div>
            </div>
            <div class="form-send-enquiry" v-show="enquiry_type=='enquiry'">
                <button class="btn btn-primary" data-toggle="modal" data-target="#enquiry_form_modal">
                    <?php echo e(__('Contact Now')); ?>

                </button>
            </div>
        </div>
    </div>
</div>
<?php echo $__env->make('Booking::frontend.global.enquiry-form', ['service_type' => 'tour'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php /**PATH C:\mamp\htdocs\flouka\Imkan\Imkan8-6\v1\themes/BC/Tour/Views/frontend/layouts/details/tour-form-book.blade.php ENDPATH**/ ?>