var map = L.map('map').setView([45.0433, 41.9691], 12); // Ставрополь
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

var markers = [];
var markerByEventId = {}; // Связь: id события -> маркер

function loadEvents(category = '', city = '') {
    // Удаляем старые маркеры
    markers.forEach(m => map.removeLayer(m));
    markers = [];
    markerByEventId = {};

    let url = '../backend/api-events.php?category=' + encodeURIComponent(category) + '&city=' + encodeURIComponent(city);

    fetch(url)
        .then(response => response.json())   // Была опечатка responpse.json()
        .then(events => {
            // Обновляем карту и создаём маркеры
            events.forEach(event => {
                if (event.latitude && event.longitude) {
                    var marker = L.marker([event.latitude, event.longitude]).addTo(map);
                    marker.bindPopup("<b>" + event.title + "</b><br>" + event.description + "<br>Рейтинг: " + event.avg_rating);
                    markers.push(marker);
                    markerByEventId[event.id] = marker;

                    // При клике по маркеру — подсвечиваем событие в списке
                    marker.on('click', function () {
                        highlightEventItem(event.id);
                    });
                }
            });

            // Обновляем список мероприятий
            let list = document.getElementById('event-list');
            list.innerHTML = '';

            if (events.length === 0) {
                list.innerHTML = '<p>Мероприятия не найдены.</p>';
            } else {
                events.forEach(event => {
                    let item = document.createElement('div');
                    item.classList.add('event-item');
                    item.setAttribute('data-event-id', event.id);
                    item.innerHTML = '<div class="event-title">' + event.title + ' (Рейтинг: ' + event.avg_rating + ')</div>' +
                        '<div class="event-description">' + event.description + '</div>';
                    list.appendChild(item);

                    // При клике по событию — открываем popup на маркере
                    item.addEventListener('click', function () {
                        var marker = markerByEventId[event.id];
                        if (marker) {
                            map.setView(marker.getLatLng(), 13);
                            marker.openPopup();
                        }
                        highlightEventItem(event.id);
                        // Переход на страницу мероприятия
                        window.location.href = 'event.php?id=' + event.id;
                    });
                });
            }
        })
        .catch(error => console.error('Ошибка загрузки событий:', error));
}

// Подсветка элемента списка
function highlightEventItem(eventId) {
    document.querySelectorAll('.event-item').forEach(item => {
        item.style.backgroundColor = '';
    });

    let item = document.querySelector('.event-item[data-event-id="' + eventId + '"]');
    if (item) {
        item.style.backgroundColor = '#e0f7fa';
        item.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

// Загружаем все события при первой загрузке
loadEvents();

// Фильтрация при отправке формы
document.getElementById('filter-form').addEventListener('submit', function(e) {
    e.preventDefault();
    var category = document.getElementById('filter-category').value;
    var city = document.getElementById('filter-city').value;
    loadEvents(category, city);
});
