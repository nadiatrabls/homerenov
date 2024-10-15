// document.addEventListener('DOMContentLoaded', function() {
//     const calendarBody = document.getElementById('calendar-body');
//     const currentMonthDisplay = document.getElementById('currentMonth');
//     const prevMonthBtn = document.getElementById('prevMonthBtn');
//     const nextMonthBtn = document.getElementById('nextMonthBtn');
//     const selectedDateInput = document.getElementById('selectedDate');
    
//     let currentDate = new Date();

//     function generateCalendar(date) {
//         calendarBody.innerHTML = '';  // Réinitialise le calendrier
//         const year = date.getFullYear();
//         const month = date.getMonth();

//         // Affiche le mois actuel
//         const options = { year: 'numeric', month: 'long' };
//         currentMonthDisplay.textContent = date.toLocaleDateString('fr-FR', options);

//         // Obtenez le premier jour du mois
//         const firstDay = new Date(year, month, 1).getDay();
//         const lastDate = new Date(year, month + 1, 0).getDate(); // Dernier jour du mois

//         let row = document.createElement('tr');
//         let dayCount = 0;

//         // Remplir les jours du mois
//         for (let i = 0; i < firstDay; i++) {
//             row.appendChild(document.createElement('td')); // Cases vides avant le 1er du mois
//             dayCount++;
//         }

//         for (let day = 1; day <= lastDate; day++) {
//             const cell = document.createElement('td');
//             cell.textContent = day;
//             cell.addEventListener('click', function() {
//                 selectedDateInput.value = `${day}/${month + 1}/${year}`;
//             });
//             row.appendChild(cell);
//             dayCount++;

//             if (dayCount % 7 === 0) {
//                 calendarBody.appendChild(row);
//                 row = document.createElement('tr');
//             }
//         }

//         // Ajouter la dernière ligne
//         if (dayCount % 7 !== 0) {
//             calendarBody.appendChild(row);
//         }
//     }

//     // Gestion des boutons de navigation des mois
//     prevMonthBtn.addEventListener('click', function() {
//         currentDate.setMonth(currentDate.getMonth() - 1);
//         generateCalendar(currentDate);
//     });

//     nextMonthBtn.addEventListener('click', function() {
//         currentDate.setMonth(currentDate.getMonth() + 1);
//         generateCalendar(currentDate);
//     });

//     // Initialisation du calendrier
//     generateCalendar(currentDate);

//     // Gestion de l'ajout d'un rendez-vous
//     document.getElementById('rendezvous-form').addEventListener('submit', function(e) {
//         e.preventDefault();

//         const date = selectedDateInput.value;
//         const heureDebut = document.getElementById('heureDebut').value;
//         const heureFin = document.getElementById('heureFin').value;

//         if (date && heureDebut && heureFin) {
//             // Vous pouvez ici envoyer ces données à votre serveur ou base de données via une requête AJAX
//             alert(`Rendez-vous créé pour le ${date}, de ${heureDebut} à ${heureFin}`);
//         } else {
//             alert('Veuillez sélectionner une date et des horaires.');
//         }
//     });
// });
// document.addEventListener('DOMContentLoaded', function() {
//     let currentDate = new Date();

//     const renderCalendar = (month, year) => {
//         const calendarBody = document.getElementById('calendar-body');
//         const currentMonth = document.getElementById('currentMonth');
//         calendarBody.innerHTML = ''; // Clear the previous calendar

//         const firstDay = new Date(year, month, 1).getDay();
//         const daysInMonth = new Date(year, month + 1, 0).getDate();

//         // Generate blank cells for days before the first of the month
//         let row = document.createElement('tr');
//         for (let i = 0; i < firstDay; i++) {
//             const cell = document.createElement('td');
//             row.appendChild(cell);
//         }

//         // Fetch the available rendez-vous from the backend
//         fetch(`/admin/rendezvous`)
//             .then(response => response.json())
//             .then(rendezvousList => {
//                 for (let day = 1; day <= daysInMonth; day++) {
//                     const cell = document.createElement('td');
//                     const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
//                     cell.textContent = day;

//                     // Check if there's an available rendezvous for this date
//                     const rendezvous = rendezvousList.find(rdv => rdv.date === dateStr);

//                     if (rendezvous) {
//                         cell.style.backgroundColor = 'lightgreen'; // Highlight available days in green
//                         cell.title = `Disponible de ${rendezvous.heureDebut} à ${rendezvous.heureFin}`;
//                     }

//                     row.appendChild(cell);

//                     // Start a new row when the week is complete
//                     if ((day + firstDay) % 7 === 0) {
//                         calendarBody.appendChild(row);
//                         row = document.createElement('tr');
//                     }
//                 }

//                 // Append the last row
//                 if (row.children.length > 0) {
//                     calendarBody.appendChild(row);
//                 }
//             });

//         currentMonth.textContent = `${month + 1}/${year}`;
//     };

//     document.getElementById('prevMonthBtn').addEventListener('click', function() {
//         currentDate.setMonth(currentDate.getMonth() - 1);
//         renderCalendar(currentDate.getMonth(), currentDate.getFullYear());
//     });

//     document.getElementById('nextMonthBtn').addEventListener('click', function() {
//         currentDate.setMonth(currentDate.getMonth() + 1);
//         renderCalendar(currentDate.getMonth(), currentDate.getFullYear());
//     });

//     renderCalendar(currentDate.getMonth(), currentDate.getFullYear());
// });
document.addEventListener('DOMContentLoaded', function() {
    // Récupérer les disponibilités depuis le backend
    fetch('/admin/dashboard/disponibilites')
        .then(response => response.json())
        .then(disponibilites => {
            disponibilites.forEach(rdv => {
                const date = new Date(rdv.date);
                const dayCell = document.querySelector(`#calendar-table td[data-date="${rdv.date}"]`);
                if (rdv.disponible) {
                    dayCell.style.backgroundColor = 'lightgreen'; // Colorer les jours disponibles en vert
                }
            });
        });
});
