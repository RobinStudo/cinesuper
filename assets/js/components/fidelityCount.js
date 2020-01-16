import noUiSlider from "nouislider";

let slider = document.getElementById('slider');

noUiSlider.create(slider, {
    start: [0],
    step: 1,
    range: {
        'min': [0],
        'max': [300]
    }
});

let sliderValueElement = document.getElementById('sliderValue');

slider.noUiSlider.on('update', function (values, handle) {
    let content = pluralOrSingular(values[handle]);
    sliderValueElement.innerHTML = Math.round(values[handle]) + content;
    document.getElementById("ticketNumber").value = Math.round(values[handle]);
});

function pluralOrSingular(value) {
    if (value < 2) {
        return " place achetée";
    }
    else {
        return " places achetées"
    }
}