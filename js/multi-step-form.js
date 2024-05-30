(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", onReady);

  function onReady() {
    initApp();
  }

  function getOrdinalSuffix(day) {
    if (day > 3 && day < 21) return "th";
    switch (day % 10) {
      case 1:
        return "st";
      case 2:
        return "nd";
      case 3:
        return "rd";
      default:
        return "th";
    }
  }

  function formatDateString(date) {
    const months = [
      "January",
      "February",
      "March",
      "April",
      "May",
      "June",
      "July",
      "August",
      "September",
      "October",
      "November",
      "December",
    ];
    const day = date.getDate();
    const month = months[date.getMonth()];
    const year = date.getFullYear();
    const ordinal = getOrdinalSuffix(day);
    return `${day} ${month} ${year}`;
  }

  function initApp() {
    const { ref, onMounted, computed, watch } = Vue;
    const App = {
      template: "#app-vue-temp",

      setup() {
        const StepOne = ref({
            isComplete: false,
            isOpen: true,
        });
        const StepTwo = ref({
            isComplete: false,
            isOpen: false,
        });
        const StepThree = ref({
            isComplete: false,
            isOpen: false,
        });
        const datePickerInput = ref(null);
        const datePicker = ref(null);
        const ShowDateForm = ref(false);
        const selectedDatesText = ref("");
        const isShowTravelersFrom = ref(false);
       const enableNext=ref(false)

        const fromStep = ref({
          toggleDrop: false,
          selectedStr: "",
        });
        const toStep = ref({
          toggleDrop: false,
          selectedStr: "",
        });
        const selectedCity = ref();
        const autocompletePlace = ref([]);
        const autocompleteCity = ref([]);

        const numAdults = ref(0);
        const numChildren = ref(0);
        const selectedPrices = ref([]);



        const increment = (type) => {
          if (type === "adults") {
            numAdults.value++;
          } else if (type === "children") {
            numChildren.value++;
          }
        };

        const decrement = (type) => {
          if (type === "adults" && numAdults.value > 0) {
            numAdults.value--;
          } else if (type === "children" && numChildren.value > 0) {
            numChildren.value--;
          }
        };
        const travelersText = computed(() => {
          return `${numAdults.value} adult${
            numAdults.value > 1 ? "s" : ""
          } and ${numChildren.value} child${
            numChildren.value > 1 ? "ren" : ""
          }`;
        });
        onMounted(() => {
          const picker = new Litepicker({
            element: datePickerInput.value,
            parentEl: datePicker.value,
            singleMode: false,
            numberOfMonths: 2,
            numberOfColumns: 2,
            format: "YYYY-MM-DD",
            setup: (picker) => {
              picker.on("show", () => {
                const litepickerContainer =
                  document.querySelector(".litepicker");
                const dayitem = document.querySelector(".day-item");
                if (litepickerContainer) {
                  litepickerContainer.style.display = "inline-block";
                  litepickerContainer.style.position = "absolute";
                  litepickerContainer.style.width = "100%";
                  litepickerContainer.style.height = "100%";
                  litepickerContainer.style.transform = "scale(1)";
                  litepickerContainer.style.zIndex = "9999";
                  litepickerContainer.style.boxShadow = "none";
                }
                if (dayitem) {
                  dayitem.style.border = "none !important";
                  dayitem.style.outline = "none !important";
                }
              });

              picker.on("selected", (date1, date2) => {
                ShowDateForm.value = true;
                const formattedDate1 = formatDateString(date1.dateInstance);
                const formattedDate2 = formatDateString(date2.dateInstance);
                selectedDatesText.value = `From the ${formattedDate1} to the ${formattedDate2}`;
                setTimeout(() => {
                  datePickerInput.value.value = `${formattedDate1} - ${formattedDate2}`;
                }, 0);

                // console.log(datePickerInput.value.value);
              });
            },
          });

          // datePickerInput.value.addEventListener('focus', () => {
          //     const whenFrom = document.querySelector('.when-form');
          //     if (whenFrom) {
          //         whenFrom.style.display = '';
          //     }
          //     picker.show();
          // });

          // datePickerInput.value.addEventListener('blur', () => {

          //     setTimeout(() => {
          //         const whenFrom = document.querySelector('.when-form');
          //         if (whenFrom) {
          //             whenFrom.style.display = 'none';
          //         }
          //     }, 20);
          // });
        });

        const toggleDropdown = () => {
          fromStep.value.toggleDrop = false;
        };

        const toggleSecondDrop = () => {
          toStep.value.toggleDrop = false;
        };

        const editCity = (city) => {
          selectedCity.value = {};
          fromStep.value.selectedStr = city;
        };

        const searchPlace = (event) => {
          ShowDateForm.value = false;
          isShowTravelersFrom.value = false;

          if (event.type == "click") {
            toStep.value.toggleDrop = false;
            fromStep.value.toggleDrop = true;
            return;
          }
          const input = event.target.value;
          const autocompleteService =
            new google.maps.places.AutocompleteService();
          const placesService = new google.maps.places.PlacesService(
            document.createElement("div")
          );

          if (input.length > 2) {
            autocompleteService.getPlacePredictions(
              {
                input: input,
                types: ["(cities)"],
              },
              (results, status) => {
                if (status === google.maps.places.PlacesServiceStatus.OK) {
                  const newResults = results.map((result) => {
                    return {
                      name: result.description,
                      placeId: result.place_id,
                    };
                  });
                  autocompletePlace.value = [];
                  newResults.forEach((result) => {
                    placesService.getDetails(
                      {
                        placeId: result.placeId,
                      },
                      (place, status) => {
                        if (
                          status === google.maps.places.PlacesServiceStatus.OK
                        ) {
                          const cityDetails = {
                            longName: place.address_components[0]?.long_name,
                            shortName: place.address_components[0]?.short_name,
                            country: place.address_components.find(
                              (component) => component.types.includes("country")
                            )?.long_name,
                            id: place.place_id,
                            formattedAddress: place.formatted_address,
                            lat: place.geometry.location.lat(),
                            lng: place.geometry.location.lng(),
                            icon: place.icon,
                            name: place.address_components[0]?.long_name,
                            type: place.address_components[0]?.types[0],
                            region: place.address_components.find((component) =>
                              component.types.includes(
                                "administrative_area_level_1"
                              )
                            )?.long_name,
                          };
                          autocompletePlace.value.push(cityDetails);
                        }
                      }
                    );
                  });
                  fromStep.value.toggleDrop = true;
                } else {
                  autocompletePlace.value = [];
                }
              }
            );
          } else {
            autocompletePlace.value = [];
            fromStep.value.toggleDrop = false;
          }
        };

        const searchCity = (event) => {
          ShowDateForm.value = false;
          isShowTravelersFrom.value = false;

          if (event.type == "click") {
            fromStep.value.toggleDrop = false;
            toStep.value.toggleDrop = true;
            return;
          }
          const input = event.target.value;
          const autocompleteService =
            new google.maps.places.AutocompleteService();
          const placesService = new google.maps.places.PlacesService(
            document.createElement("div")
          );

          if (input.length > 2) {
            autocompleteService.getPlacePredictions(
              {
                input: input,
                componentRestrictions: {
                  country: "it",
                },
                types: ["(cities)"],
              },
              (results, status) => {
                if (status === google.maps.places.PlacesServiceStatus.OK) {
                  const newResults = results.map((result) => {
                    return {
                      name: result.description,
                      placeId: result.place_id,
                    };
                  });
                  autocompleteCity.value = [];
                  newResults.forEach((result) => {
                    placesService.getDetails(
                      {
                        placeId: result.placeId,
                      },
                      (place, status) => {
                        if (
                          status === google.maps.places.PlacesServiceStatus.OK
                        ) {
                          const cityDetails = {
                            longName: place.address_components[0]?.long_name,
                            shortName: place.address_components[0]?.short_name,
                            country: place.address_components.find(
                              (component) => component.types.includes("country")
                            )?.long_name,
                            id: place.place_id,
                            formattedAddress: place.formatted_address,
                            lat: place.geometry.location.lat(),
                            lng: place.geometry.location.lng(),
                            icon: place.icon,
                            name: place.address_components[0]?.long_name,
                            type: place.address_components[0]?.types[0],
                            region: place.address_components.find((component) =>
                              component.types.includes(
                                "administrative_area_level_1"
                              )
                            )?.long_name,
                          };
                          autocompleteCity.value.push(cityDetails);
                        }
                      }
                    );
                  });
                  toStep.value.toggleDrop = true;
                } else {
                  autocompleteCity.value = [];
                }
              }
            );
          } else {
            autocompleteCity.value = [];
          }
        };

        const selectPlace = (place) => {
          fromStep.value.selectedCity = place;
          selectedCity.value = place;
          fromStep.value.selectedStr = place.shortName + ", " + place.country;
          autocompletePlace.value = [];
        };

        const selectCity = (place) => {
          toStep.value.selectedCity = place;
          selectedCity.value = place;
          toStep.value.selectedStr = place.shortName + ", " + place.country;
          autocompleteCity.value = [];
        };

        const showDatePicker = () => {
          ShowDateForm.value = true;
          fromStep.value.toggleDrop = false;
          toStep.value.toggleDrop = false;
          isShowTravelersFrom.value = false;

          // const whenFrom = document.querySelector('.when-form');
          // if (whenFrom) {
          //     whenFrom.style.display = '';
          // }
        };
        const hideDatePicker = () => {
          ShowDateForm.value = false;

          // const whenFrom = document.querySelector('.when-form');
          // if (whenFrom) {
          //     whenFrom.style.display = 'none';
          // }
        };
        const ShowTravelersFrom = () => {
          isShowTravelersFrom.value = true;
          fromStep.value.toggleDrop = false;
          toStep.value.toggleDrop = false;
          ShowDateForm.value = false;
        };
        const hideTravelerForm = () => {
          isShowTravelersFrom.value = false;
        };

        watch([fromStep, toStep, datePickerInput, travelersText], () => {
          if (
            fromStep.value.selectedStr &&
            toStep.value.selectedStr &&
            datePickerInput.value &&
            travelersText.value &&
            numAdults.value > 0 
          ) {
            StepOne.value.isComplete = true;
            enableNext.value = true
          }
        });
        watch([selectedPrices], () => {
            if (selectedPrices.value.length > 0) {
                StepTwo.value.isComplete = true;
                enableNext.value = true
                console.log(selectedPrices.value.length);
                console.log(selectedPrices.value);
            } else {
                StepTwo.value.isComplete = false;
                enableNext.value = false
            }
        });

        const nextStep = ()=>{
            if ( StepOne.value.isOpen == true && StepOne.value.isComplete == true) {
                StepOne.value.isOpen = false ;
                enableNext.value = false;
                StepTwo.value.isOpen =true

            }else if  (StepTwo.value.isOpen == true && StepTwo.value.isComplete == true) {
                StepTwo.value.isOpen = false
                enableNext.value = false;
            }else{
                console.log('3');
            }
        }

        return {
          fromStep,
          toStep,
          selectedCity,
          editCity,
          toggleDropdown,
          selectPlace,
          selectCity,
          toggleSecondDrop,
          autocompletePlace,
          autocompleteCity,
          searchPlace,
          searchCity,
          datePickerInput,
          datePicker,
          showDatePicker,
          ShowDateForm,
          hideDatePicker,
          selectedDatesText,
          isShowTravelersFrom,
          ShowTravelersFrom,
          hideTravelerForm,
          numAdults,
          numChildren,
          increment,
          decrement,
          travelersText,
          StepOne,
          StepTwo,
          StepThree,
          nextStep,
          enableNext,
          selectedPrices
        };
      },
    };

    const vueApp = Vue.createApp(App);

    vueApp.directive("away", {
      beforeMount(el, binding) {
        el.clickOutsideEvent = function (event) {
          if (
            !(
              el == event.target ||
              el.contains(event.target) ||
              event.target.className == "text-wrapper-2" ||
              event.target.className == "text-wrapper-3" ||
              event.target.className == "month-item" ||
              event.target.className == "datePickerInput" ||
              event.target.closest(".when-form") ||
              event.target.closest(".month-item") ||
              event.target.closest(".travelers-form") ||
              event.target.closest(".text-wrapper-8")
            )
          ) {
            binding.value();
          }
        };
        document.body.addEventListener("click", el.clickOutsideEvent);
      },
      unmounted(el) {
        document.body.removeEventListener("click", el.clickOutsideEvent);
      },
    });

    vueApp.mount("#app-vue");
  }
})();
