function sortBooks(criteria) {
    let rows = document.querySelectorAll('table tbody tr');
    let sortedRows = Array.from(rows).sort((a, b) => {
        let valA = a.querySelector(`td:nth-child(${criteria})`).innerText;
        let valB = b.querySelector(`td:nth-child(${criteria})`).innerText;
        return valA.localeCompare(valB);
    });
    let table = document.querySelector('table tbody');
    sortedRows.forEach(row => table.appendChild(row));
}
