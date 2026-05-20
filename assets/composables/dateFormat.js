export function dateFormatDay(date, locale = 'fr-FR') {
    return new Date(date)
        .toLocaleDateString(locale, {day: 'numeric'} );
}

export function dateFormatMonth(date, locale = 'fr-FR') {
    return new Date(date)
        .toLocaleDateString(locale, {month: 'short'} );
}

export function dateFormatYear(date) {
    return new Date(date)
        .getFullYear();
}

export function formatDate(date, locale = 'fr-FR') {
    return new Date(date)
        .toLocaleDateString(locale, {day: 'numeric', month: 'short', year: 'numeric'} );
}
