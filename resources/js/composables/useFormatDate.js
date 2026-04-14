export function useFormatDate() {
    function formatDate(date, withTime = false) {
        if (!date) return '—'
        const options = { day: 'numeric', month: 'short', year: 'numeric' }
        if (withTime) {
            options.hour = '2-digit'
            options.minute = '2-digit'
        }
        return new Date(date).toLocaleDateString('en-GB', options)
    }

    return { formatDate }
}
