export function dateFormatDay(date) {
    return new Date(date)
        .toLocaleDateString('fr-FR', {day: 'numeric'} );
}

export function dateFormatMonth(date) {
    return new Date(date)
        .toLocaleDateString('fr-FR', {month: 'short'} );
}
