<div id="app-vue">
</div>

<template id="app-vue-temp">
    <div class="step-start">
        <div class="div">
                    <div class="accordion">
                        <div class="navigation">
                            <div class="container">
                                <img class="left-column" src="https://c.animaapp.com/Hv9qWqhI/img/left-column.svg" />
                                <div class="right-column"><img class="close" src="https://c.animaapp.com/Hv9qWqhI/img/close@2x.png" /></div>
                            </div>
                        </div>
                        <div class="progressbar"><span></span></div>
                        <div class="step" v-if="StepOne.isOpen">
                            <div  class="top">
                                <header class="header">
                                    <p class="p">Where in Italy do you want to go?</p>
                                </header>
                                <div class="search">
                                    <div class="overlap-group-2">
                                        <div class="lines">
                                            <img class="line" src="https://c.animaapp.com/Hv9qWqhI/img/line-2.svg" />
                                            <img class="img" src="https://c.animaapp.com/Hv9qWqhI/img/line-2.svg" />
                                            <img class="line-2" src="https://c.animaapp.com/Hv9qWqhI/img/line-2.svg" />
                                        </div>

                                        <div class="placeholders">
                                            <div class="placeholders-2">
                                                <input type="text" class="text-wrapper-2" v-model="fromStep.selectedStr" type="text" @click="searchPlace" @input="searchPlace" placeholder="City, town or airport">
                                                <!-- @input="searchPlace" -->
                                                <input type="text" class="text-wrapper-3" v-model="toStep.selectedStr" type="text" @click="searchCity" @input="searchCity" placeholder="Region, city or town">
                                                <div class="text-wrapper-4">
                                                     <input class="datePickerInput"  ref="datePickerInput" placeholder="Select Date Range" @focus="showDatePicker" />
                                                </div>
                                                <input type="text" class="text-wrapper-8"  placeholder="Travelers" @focus="ShowTravelersFrom" :value="travelersText" >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="labels">
                                        <div class="text-wrapper-5">From</div>
                                        <div class="text-wrapper-6">To</div>
                                        <div class="text-wrapper-7">When</div>
                                        <div class="text-wrapper-9">Travelers</div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        <div class="complete" v-else>
                            <p class="text">
                                <span class="icon"><i class="fas fa-check"></i></span>
                                <span style="font-weight: bold;">Trip details:</span>
                                From {{ fromStep.selectedStr }} to {{ toStep.selectedStr }}, for a weekend in January 2024. {{ numAdults }} Adults, {{ numChildren }} children
                            </p>
                            <button class="edit-btn"><i class="fas fa-pencil-alt"></i></button>
                        </div>
                     
                        <div class="step-2" >
                            <div v-if="StepTwo.isOpen" class="open">
                                <div class="text">What is your budget?</div>
                                <!-- <div class="tags">
                                    <label class="tag">
                                        <input type="checkbox"  v-model="selectedPrices">
                                        €0 - €500 a day
                                    </label>
                                    <label class="tag">
                                        <input type="checkbox"  v-model="selectedPrices">
                                        €500 - €2500 a day
                                    </label>
                                    <label class="tag">
                                        <input type="checkbox"  v-model="selectedPrices">
                                        €2500+ a day
                                    </label>
                                </div> -->
                                <div class="tags">
                                    <?php
                                    $args = array(
                                        'post_type' => 'price_range', 
                                        'posts_per_page' => -1, 
                                    );

                                    $custom_query = new WP_Query($args);

                                    if ($custom_query->have_posts()) {
                                        $price_ranges = array(); 

                                        while ($custom_query->have_posts()) {
                                            $custom_query->the_post();
                                            $start_price = get_post_meta(get_the_ID(), 'start_price', true);
                                            $end_price = get_post_meta(get_the_ID(), 'end_price', true);
                                            $last_price = get_post_meta(get_the_ID(), 'last_price', true);

                                            if ($start_price || $end_price) {
                                                $price_range = '€' . esc_html($start_price) . ' – €' . esc_html($end_price) . ' a day';
                                            } elseif ($last_price) {
                                                $price_range = '€' . esc_html($last_price) . ' a day';
                                            }

                                            $price_ranges[] = $price_range; 
                                        }

                                        foreach (array_reverse($price_ranges) as $price) {
                                            echo '<label class="tag">' . $price . '<input type="checkbox" value="' . esc_attr(get_the_ID()) . '" v-model="selectedPrices"></label>';
                                        }

                                        wp_reset_postdata();
                                    } else {
                                        echo 'No price ranges found.'; 
                                    }
                                    ?>
                                </div>
                            </div>
                            <div v-if="!StepTwo.isComplete && !StepTwo.isOpen"class="close">
                                <div class="text">What is your budget?</div> 
                                <img class="secure" src="https://c.animaapp.com/Hv9qWqhI/img/secure-1@2x.png" />
                            </div>
                        </div>
                        <div class="complete" v-if="!StepTwo.isOpen && StepTwo.isComplete">
                            <p class="text">
                                <span class="icon"><i class="fas fa-check"></i></span>
                                <span style="font-weight: bold;">Budget:</span>
                                €500 – €2500 a day
                            </p>
                            <button class="edit-btn"><i class="fas fa-pencil-alt"></i></button>
                        </div>

                        <div class="step-2">
                            <div v-if="StepThree.isOpen" class="open">
                            <p class="text-wrapper-11">What activities are you interested in?</p>
                                </div>
                            <div v-else class="close">
                                <p class="text">What activities are you interested in? {{StepTwo.isComplete}}</p>

                                <img class="secure" src="https://c.animaapp.com/Hv9qWqhI/img/secure-1@2x.png" />
                            </div>
                        </div>
                        

                        <div class="destinations" v-away="toggleSecondDrop" v-if="toStep.toggleDrop && autocompleteCity.length">
                            <div class="overlap-3">
                                <div class="from-locations-toStep">
                                    <div class="frame" v-for="city in  autocompleteCity" @click="selectCity(city)">
                                        <div class="frame-wrapper">
                                            <img class="frame-2" src="https://c.animaapp.com/cHpn0Iyn/img/frame-39942-14.svg" />
                                            <div class="frame-3">
                                                <div class="text-wrapper-12">{{ city.name }}</div>
                                                <div class="text-wrapper-13">{{ city.country }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="from-locations-2">
                                    <div class="placeholders-wrapper">
                                        <div class="placeholders-3">
                                            <div class="frame-3">
                                                <div class="text-wrapper-12">Discover Italy by regions</div>
                                                <p class="text-wrapper-13">Choose an area to explore</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="frame-7">
                                        <div class="frame-8">
                                            <img class="frame-2" src="https://c.animaapp.com/cHpn0Iyn/img/frame-39942-14.svg" />
                                            <div class="frame-9">
                                                <div class="text-wrapper-14">Abruzzo</div>
                                                <div class="text-wrapper-15">South</div>
                                            </div>
                                        </div>
                                        <div class="frame-8">
                                            <img class="frame-2" src="https://c.animaapp.com/cHpn0Iyn/img/frame-39942-14.svg" />
                                            <div class="frame-9">
                                                <div class="text-wrapper-14">Basilicata</div>
                                                <div class="text-wrapper-15">South</div>
                                            </div>
                                        </div>
                                        <div class="frame-8">
                                            <img class="frame-2" src="https://c.animaapp.com/cHpn0Iyn/img/frame-39942-14.svg" />
                                            <div class="frame-9">
                                                <div class="text-wrapper-14">Calabria</div>
                                                <div class="text-wrapper-15">South</div>
                                            </div>
                                        </div>
                                        <div class="frame-8">
                                            <img class="frame-2" src="https://c.animaapp.com/cHpn0Iyn/img/frame-39942-14.svg" />
                                            <div class="frame-9">
                                                <div class="text-wrapper-14">Campania</div>
                                                <div class="text-wrapper-15">South</div>
                                            </div>
                                        </div>
                                        <div class="frame-8">
                                            <img class="frame-2" src="https://c.animaapp.com/cHpn0Iyn/img/frame-39942-14.svg" />
                                            <div class="frame-9">
                                                <div class="text-wrapper-14">Emilia Romagna</div>
                                                <div class="text-wrapper-15">North-East</div>
                                            </div>
                                        </div>
                                        <div class="frame-8">
                                            <img class="frame-2" src="https://c.animaapp.com/cHpn0Iyn/img/frame-39942-14.svg" />
                                            <div class="frame-9">
                                                <div class="text-wrapper-14">Friuli-Venezia Giulia</div>
                                                <div class="text-wrapper-15">North-East</div>
                                            </div>
                                        </div>
                                        <div class="frame-8">
                                            <img class="frame-2" src="https://c.animaapp.com/cHpn0Iyn/img/frame-39942-14.svg" />
                                            <div class="frame-9">
                                                <div class="text-wrapper-14">Lazio</div>
                                                <div class="text-wrapper-15">Centre</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <img class="line-3" src="https://c.animaapp.com/cHpn0Iyn/img/line-12.svg" />
                            </div>
                            <div class="frame-10">
                                <div class="text-wrapper-16">Select a popular destination</div>
                                <div class="text-wrapper-13">Must-see places to visit</div>
                            </div>
                            <img class="line-4" src="https://c.animaapp.com/cHpn0Iyn/img/line-12.svg" />
                            <div class="frame-11">
                                <div class="div-2">
                                    <div class="group"></div>
                                    <div class="frame-12">
                                        <div class="frame-13">
                                            <img class="rectangle" src="https://c.animaapp.com/cHpn0Iyn/img/rectangle-36.svg" />
                                            <div class="group-2">
                                                <div class="text-wrapper-17">Venice</div>
                                                <div class="text-wrapper-18">North-East</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="div-2">
                                    <div class="group"></div>
                                    <div class="frame-12">
                                        <div class="frame-13">
                                            <img class="rectangle" src="https://c.animaapp.com/cHpn0Iyn/img/rectangle-36-1.svg" />
                                            <div class="group-2">
                                                <div class="text-wrapper-17">Capri</div>
                                                <div class="text-wrapper-18">South</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="div-2">
                                    <div class="group"></div>
                                    <div class="frame-12">
                                        <div class="frame-13">
                                            <img class="rectangle" src="https://c.animaapp.com/cHpn0Iyn/img/rectangle-36-2.svg" />
                                            <div class="group-2">
                                                <div class="text-wrapper-17">Rome</div>
                                                <div class="text-wrapper-18">Centre</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="div-2">
                                    <div class="group"></div>
                                    <div class="frame-12">
                                        <div class="frame-13">
                                            <img class="rectangle" src="https://c.animaapp.com/cHpn0Iyn/img/rectangle-36-3.svg" />
                                            <div class="group-2">
                                                <div class="text-wrapper-17">Cinque Terre</div>
                                                <div class="text-wrapper-18">North-West</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="div-2">
                                    <div class="group"></div>
                                    <div class="frame-12">
                                        <div class="frame-13">
                                            <img class="rectangle" src="https://c.animaapp.com/cHpn0Iyn/img/rectangle-36-4.svg" />
                                            <div class="group-2">
                                                <div class="text-wrapper-17">Milan</div>
                                                <div class="text-wrapper-18">North</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="fromStep.toggleDrop && autocompletePlace.length" class="open-field" v-away="toggleDropdown">
                            <div class="from-locations">
                                <div class="frame" v-for="place in autocompletePlace" @click="selectPlace(place)">

                                    <div class="frame-wrapper">
                                        <img class="frame-2" src="https://c.animaapp.com/Hv9qWqhI/img/frame-39942-1.svg" />
                                        <div class="frame-3">
                                            <div class="text-wrapper-12">{{place.name}}, {{place.country}}
                                                <!-- <span class="text-wrapper-13">{{place.longName}},
                                                        {{place.shortName}}</span> -->
                                            </div>
                                            <span class="text-wrapper-13" v-if="place.type === 'locality' && place.isCapital">Capital of
                                                {{place.country}}</span>
                                            <span class="text-wrapper-13" v-else-if="place.type === 'locality'">
                                                {{place.type.charAt(0).toUpperCase() +
                                                        place.type.slice(1)}} in {{place.region}}</span>
                                            <span class="text-wrapper-13" v-else-if="place.type === 'country'">
                                                {{place.type.charAt(0).toUpperCase() +
                                                        place.type.slice(1)}}</span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div v-show="ShowDateForm"  class="when-form" v-away="hideDatePicker">
                            <div  class="when-top">
                                <button>Calender</button>
                            </div>
                            <div ref="datePicker" class="date-range"></div>
                            <div class="when-bottom" >
                                <span v-text="selectedDatesText"></span>
                                <Button @click="hideDatePicker">Done</Button>
                            </div>
                        </div>
                        <div v-show="isShowTravelersFrom"  class="travelers-form" v-away="hideTravelerForm">
                            <div class="adults">
                                    <div>
                                        <span>Adults</span>
                                        <span>Ages 18 or above</span>
                                    </div>
                                    <div>
                                    <button class="btn" @click="decrement('adults')">-</button>
                                <span>{{ numAdults }}</span>
                                <button class="btn" @click="increment('adults')">+</button>
                                    </div>
                            </div>
                            <div class="children">
                                    <div>
                                        <span>Children</span>
                                        <span>Ages 0-17</span>
                                    </div>
                                    <div>
                                    <button class="btn" @click="decrement('children')">-</button>
                                    <span>{{ numChildren }}</span>
                                    <button class="btn" @click="increment('children')">+</button>
                                    </div>
                            </div>
                        </div>
                        <div class="next-step">
                            <button :class="enableNext ? 'btn-opacity' : ''" @click="nextStep" :disabled="!enableNext">Next</button>
                        </div>
                    </div>
        </div>
    </div>
</template>
