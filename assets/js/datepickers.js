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