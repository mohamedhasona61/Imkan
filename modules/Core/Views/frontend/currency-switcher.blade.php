@php
    $actives = \App\Currency::getActiveCurrency();
    $current = \App\Currency::getCurrent('');
@endphp
{{--Multi Language--}}
@if(!empty($actives) and count($actives) > 1)
    <li class="dropdown">
        @foreach($actives as $currency)
            @if($current == $currency['currency_main'])
                <a href="#" data-toggle="dropdown" class="is_login">
                    {{strtoupper($currency['currency_main'])}}
                    <i class="fa fa-angle-down"></i>
                </a>
            @endif
        @endforeach
        <ul class="dropdown-menu text-left width-auto">
            @foreach($actives as $currency)
                @if($current != $currency[''])
                    <li>
                        <a href="{{get_currency_switcher_url($currency['currency_main'])}}" class="is_login">
                            {{strtoupper($currency['currency_main'])}}
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    </li>
@endif
{{--End Multi language--}}