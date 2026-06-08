document.addEventListener('DOMContentLoaded', () => {

    if (typeof flatpickr === 'undefined') {
        return;
    }

    flatpickr('.sr-date', {
        dateFormat: 'd.m.Y',
        allowInput: true
    });

    flatpickr('.sr-datetime', {
        enableTime: true,
        time_24hr: true,
        dateFormat: 'd.m.Y H:i',
        allowInput: true
    });

});

document
    .querySelectorAll('.datetimepicker')
    .forEach(el => {

        flatpickr(el, {

            locale: 'sr',

            enableTime: true,

            time_24hr: true,

            dateFormat: 'Y-m-d H:i',

            altInput: true,

            altFormat: 'd.m.Y H:i'
        });

    });