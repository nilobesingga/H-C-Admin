// document.addEventListener("DOMContentLoaded", () => {
//     const noDataRows = document.querySelectorAll("tr.table-no-data-available");
//
//     noDataRows.forEach(row => {
//         const table = row.closest("table");
//         if (table) {
//             const headerRow = table.querySelector("thead tr");
//             const columnCount = headerRow ? headerRow.children.length : 1; // Default to 1 if no header is found
//             const noDataCell = row.querySelector("td");
//
//             if (noDataCell) {
//                 noDataCell.setAttribute("colspan", columnCount);
//             }
//         }
//     });
// });
