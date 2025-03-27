const months = [
    ['leden', 'ledna'], ['únor', 'února'], ['březen', 'března'],
    ['duben', 'dubna'], ['květen', 'května'], ['červen', 'června'],
    ['červenec', 'července'], ['srpen', 'srpna'], ['září', 'září'],
    ['říjen', 'října'], ['listopad', 'listopadu'], ['prosinec', 'prosince']
]

export function formatCzechMonth(dateString) {
    const date = new Date(dateString)
    const month = months[date.getMonth()][1]

    return `${month}`
}
export function formatCzechDate(dateString) {
    const date = new Date(dateString)
    const day = date.getDate()
    const month = months[date.getMonth()][1]

    return `${day}. ${month}`
}

export function formatCzechDateTime(dateString) {
    const date = new Date(dateString)
    const day = date.getDate()
    const month = months[date.getMonth()][1]
    const year = date.getFullYear()
    const hours = date.getHours()
    const minutes = date.getMinutes()

    return `${day}. ${month} ${year} v ${hours}:${minutes < 10 ? '0' + minutes : minutes}`
}